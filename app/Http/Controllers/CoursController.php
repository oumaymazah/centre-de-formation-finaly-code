<?php
namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\Reservation;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class PanierController extends Controller   {

public function debugFormationReservations($formationId)
{
    $formation = Training::find($formationId);
    if (!$formation) {
        return response()->json(['error' => 'Formation non trouvée']);
    }

    // Récupérer toutes les réservations qui contiennent cette formation
    $reservations = DB::table('reservations')
        ->where('training_data', 'like', '%' . $formationId . '%')
        ->get();

    $debug = [
        'formation_id' => $formationId,
        'total_seats' => $formation->total_seats,
        'total_reservations' => $reservations->count(),
        'reservations_details' => []
    ];

    $confirmedCount = 0;
    $pendingCount = 0;

    foreach ($reservations as $reservation) {
        $trainingData = json_decode($reservation->training_data, true);

        // Compter les occurrences de cette formation dans training_data
        $formationCount = 0;
        if (is_array($trainingData)) {
            $formationCount = count(array_filter($trainingData, function($id) use ($formationId) {
                return (string)$id === (string)$formationId;
            }));
        }

        $debug['reservations_details'][] = [
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'status' => $reservation->status,
            'training_data' => $reservation->training_data,
            'formation_occurrences' => $formationCount,
            'created_at' => $reservation->created_at
        ];

        if ($reservation->status == 1) {
            $confirmedCount += $formationCount;
        } else {
            $pendingCount += $formationCount;
        }
    }

    $debug['summary'] = [
        'confirmed_reservations' => $confirmedCount,
        'pending_reservations' => $pendingCount,
        'remaining_seats' => max(0, $formation->total_seats - $confirmedCount),
        'is_complete' => ($formation->total_seats - $confirmedCount) <= 0 && $formation->total_seats > 0
    ];

    return response()->json($debug);
}


public function getRemainingSeats($formationId)
{
    try {
        $formation = Training::findOrFail($formationId);
        $totalSeats = (int)($formation->total_seats ?? 0);

        // CORRECTION: Compter TOUTES les réservations validées (status = 1)
        // sans exception - y compris celles de l'utilisateur actuel
        $confirmedReservations = DB::table('reservations')
            ->where('status', 1) // Seules les réservations payées/validées comptent
            ->whereRaw("JSON_SEARCH(training_data, 'one', ?) IS NOT NULL", [$formationId])
            ->get();

        $confirmedReservationsCount = 0;

        // Compter manuellement pour être sûr
        foreach ($confirmedReservations as $reservation) {
            $trainingData = json_decode($reservation->training_data, true);
            if (is_array($trainingData)) {
                // Si training_data est un tableau d'IDs
                $confirmedReservationsCount += count(array_filter($trainingData, function($id) use ($formationId) {
                    return (string)$id === (string)$formationId;
                }));
            } else {
                // Si training_data contient des objets avec des IDs
                if (is_string($reservation->training_data)) {
                    $confirmedReservationsCount += substr_count($reservation->training_data, '"id":"' . $formationId . '"');
                    $confirmedReservationsCount += substr_count($reservation->training_data, '"id":' . $formationId);
                }
            }
        }

        $remainingSeats = max(0, $totalSeats - $confirmedReservationsCount);
        $isComplete = $remainingSeats === 0 && $totalSeats > 0;

        // Logging pour debug
        Log::info("Formation {$formationId}: Total={$totalSeats}, Confirmées={$confirmedReservationsCount}, Restantes={$remainingSeats}, Complète={$isComplete}");

        return response()->json([
            'success' => true,
            'total_seats' => $totalSeats,
            'confirmed_reservations' => $confirmedReservationsCount,
            'remaining_seats' => $remainingSeats,
            'is_complete' => $isComplete
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur getRemainingSeats pour formation {$formationId}: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Formation non trouvée'
        ], 404);
    }
}

public function index()
{
    // Vérifier si l'utilisateur est authentifié
    if (!auth()->check()) {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'authenticated' => false,
                'message' => 'Connexion requise pour accéder au panier',
                'redirect_url' => route('login')
            ], 401);
        }
        return view('admin.apps.formation.panier', [
            'authenticated' => false,
            'panierItems' => collect(),
            'totalPrice' => 0,
            'cartCount' => 0
        ]);
    }
    // Vérifier si l'utilisateur a le rôle 'etudiant'
    if (!auth()->user()->hasRole('etudiant')) {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'authenticated' => true,
                'authorized' => false,
                'message' => 'Accès restreint aux étudiants uniquement'
            ], 403);
        }

        return view('admin.apps.formation.panier', [
            'authenticated' => true,
            'authorized' => false,
            'panierItems' => collect(),
            'totalPrice' => 0,
            'cartCount' => 0
        ]);
    }

    // Logique pour les étudiants authentifiés
    $userId = Auth::id();
    $carts = Cart::where('user_id', $userId)->get();
    $panierItems = collect();
    $totalItemsCount = 0;
    $completeFormations = [];
    $expiredFormations = [];
    $currentDate = \Carbon\Carbon::now();
    foreach ($carts as $cart) {
        $trainingIds = $cart->training_ids ?: [];
        $totalItemsCount += count($trainingIds);
        $trainings = Training::whereIn('id', $trainingIds)->get();
        foreach ($trainings as $training) {
            $item = new \stdClass();
            $item->cart_id = $cart->id;
            $item->Training = $training;
            $panierItems->push($item);
            // Vérifier si la formation est complète
            $totalSeats = (int)($training->total_seats ?? 0);
            $reservations = Reservation::where('status', 1)
                ->whereNotNull('training_data')
                ->where('training_data', '!=', '')
                ->where('training_data', '!=', '[]')
                ->get(['training_data']);
            $confirmedReservations = 0;
            foreach ($reservations as $reservation) {
                $trainingData = is_string($reservation->training_data)
                    ? json_decode($reservation->training_data, true)
                    : $reservation->training_data;

                if (is_array($trainingData) && !empty($trainingData)) {
                    foreach ($trainingData as $trainingItem) {
                        $itemId = is_array($trainingItem) && isset($trainingItem['id'])
                            ? $trainingItem['id']
                            : $trainingItem;
                        if ($itemId == $training->id || $itemId == (string)$training->id) {
                            $confirmedReservations++;
                            break;
                        }
                    }
                }
            }
            $remainingSeats = max(0, $totalSeats - $confirmedReservations);
            $isComplete = ($remainingSeats === 0 && $totalSeats > 0);
            if ($isComplete) {
                $completeFormations[] = $training->id;
            }
            $item->Training->is_complete = $isComplete;
            $item->Training->remaining_seats = $remainingSeats;
            // Vérifier si la date de début est dépassée
            $isExpired = false;
            if ($training->start_date) {
                $startDate = \Carbon\Carbon::parse($training->start_date);
                $isExpired = $startDate->toDateString() < $currentDate->toDateString();                if ($isExpired) {
                    $expiredFormations[] = $training->id;
                }
            }
            $item->Training->is_expired = $isExpired; // Ajouter la propriété is_expired
        }
    }
    $totalPrice = 0;
    $totalWithoutDiscount = 0;
    $discountedItemsOriginalPrice = 0;
    $discountedItemsFinalPrice = 0;
    $hasDiscount = false;
    $debugItems = [];
    foreach ($panierItems as $item) {
        if ($item->Training && $item->Training->price !== null) {
            $originalPrice = (float)$item->Training->price;
            $trainingId = $item->Training->id;
            $discountPercent = $item->Training->discount;
            $totalWithoutDiscount += $originalPrice;
            $debugInfo = [
                'id' => $trainingId,
                'title' => $item->Training->title ?? 'Sans titre',
                'originalPrice' => $originalPrice,
                'discount' => $discountPercent,
                'is_complete' => $item->Training->is_complete,
                'remaining_seats' => $item->Training->remaining_seats,
                'is_expired' => $item->Training->is_expired, // Ajout dans le debug
            ];
            if ($discountPercent > 0) {
                $hasDiscount = true;
                $discountMultiplier = (100 - $discountPercent) / 100;
                $discountedPrice = $originalPrice * $discountMultiplier;
                $discountAmount = ($originalPrice * $discountPercent) / 100;
                $verifiedPrice = $originalPrice - $discountAmount;

                $debugInfo['calculMethod1'] = "$originalPrice * (1 - $discountPercent/100) = $discountedPrice";
                $debugInfo['calculMethod2'] = "$originalPrice - ($originalPrice * $discountPercent/100) = $verifiedPrice";

                $discountedItemsOriginalPrice += $originalPrice;
                $discountedItemsFinalPrice += $discountedPrice;
                $totalPrice += $discountedPrice;
                $item->Training->final_price = $discountedPrice;
            } else {
                $totalPrice += $originalPrice;
                $item->Training->final_price = $originalPrice;
                $debugInfo['finalPrice'] = $originalPrice;
            }
            $debugItems[] = $debugInfo;
        }
        if ($item->Training) {
            $item->Training->total_feedbacks = $item->Training->feedbacks ? $item->Training->feedbacks->count() : 0;
            $item->Training->average_rating = $item->Training->total_feedbacks > 0
                ? round($item->Training->feedbacks->sum('rating_count') / $item->Training->total_feedbacks, 1)
                : 0;
        }
    }
    $reservationData = $this->getReservationData($userId);
    $discountPercentage = $totalWithoutDiscount > 0 && $hasDiscount
        ? round(100 - ($totalPrice / $totalWithoutDiscount * 100))
        : 0;
    if (request()->ajax() || request()->wantsJson()) {
        return response()->json([
            'authenticated' => true,
            'authorized' => true,
            'panierItems' => $panierItems,
            'totalPrice' => $totalPrice,
            'totalWithoutDiscount' => $totalWithoutDiscount,
            'discountedItemsOriginalPrice' => $discountedItemsOriginalPrice,
            'discountedItemsFinalPrice' => $discountedItemsFinalPrice,
            'discountPercentage' => $discountPercentage,
            'hasDiscount' => $hasDiscount,
            'cartCount' => $totalItemsCount,
            'completeFormations' => $completeFormations,
            'expiredFormations' => $expiredFormations, // Ajouter à la réponse JSON
            'reservationData' => $reservationData,
            'debug' => $debugItems
        ]);
    }
    return view('admin.apps.formation.panier', [
        'authenticated' => true,
        'authorized' => true,
        'panierItems' => $panierItems,
        'totalPrice' => $totalPrice,
        'totalWithoutDiscount' => $totalWithoutDiscount,
        'discountedItemsOriginalPrice' => $discountedItemsOriginalPrice,
        'discountedItemsFinalPrice' => $discountedItemsFinalPrice,
        'discountPercentage' => $discountPercentage,
        'hasDiscount' => $hasDiscount,
        'cartCount' => $totalItemsCount,
        'completeFormations' => $completeFormations,
        'expiredFormations' => $expiredFormations, // Passer à la vue
        'reservationData' => $reservationData,
        'debug' => $debugItems
    ]);
}
private function getReservationData($userId)
{
    // Récupérer le panier actuel
    $cart = Cart::where('user_id', $userId)->first();
    $hasItemsInCart = $cart && !empty($cart->training_ids);

    // Vérifier s'il y a une réservation en attente (status = 0)
    $pendingReservation = Reservation::where('user_id', $userId)
        ->where('status', 0)
        ->orderBy('created_at', 'desc')
        ->first();

    // Vérifier s'il y a une réservation confirmée (status = 1)
    $confirmedReservation = Reservation::where('user_id', $userId)
        ->where('status', 1)
        ->orderBy('created_at', 'desc')
        ->first();

    $response = [
        'hasReservation' => false,
        'reservation_id' => null,
        'status' => 0,
        'buttonState' => 'reserve'
    ];

    // Si nous avons une réservation en attente
    if ($pendingReservation) {
        $response['hasReservation'] = true;
        $response['reservation_id'] = $pendingReservation->id;
        $response['status'] = 0;
        $response['buttonState'] = 'viewReservations';
    }
    // Si nous avons une réservation confirmée mais pas d'articles dans le panier
    // elseif ($confirmedReservation && !$hasItemsInCart) {
    //     $response['hasReservation'] = true;
    //     $response['reservation_id'] = $confirmedReservation->id;
    //     $response['status'] = 1;
    //     $response['buttonState'] = 'viewReservationsOnly'; // Juste voir, pas d'annulation
    // }

    return $response;
}


public function ajouter(Request $request)
{
    try {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
        ]);
        $userId = Auth::id() ?? session()->getId();
        $formationId = $request->training_id;

        // Récupérer le panier de l'utilisateur ou en créer un nouveau
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $userId;
            $cart->training_ids = [$formationId]; // Le cast s'occupera de la conversion en JSON
            $cart->save();
        } else {
            // Récupérer les formations déjà dans le panier (déjà converti en array par le cast)
            $trainingIds = $cart->training_ids ?: [];
            // Vérifier si la formation existe déjà dans le panier
            if (in_array($formationId, $trainingIds)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cette formation est déjà dans votre panier',
                    'cartCount' => count($trainingIds)
                ]);
            }
            // Ajouter la nouvelle formation
            $trainingIds[] = $formationId;
            $cart->training_ids = $trainingIds; // Le cast s'occupera de la conversion en JSON
            $cart->save();
        }
        // NOUVEAU CODE: Synchroniser avec les réservations en attente
        if (Auth::check()) {
            $pendingReservations = Reservation::where('user_id', $userId)
                ->where('status', 0) // Réservations en attente uniquement
                ->get();

            if ($pendingReservations->isNotEmpty()) {
                // Récupérer les informations de la formation à ajouter
                $training = Training::find($formationId);

                if ($training) {
                    // Ajouter simplement l'ID de la formation comme string
                    $trainingData = (string)$training->id;

                    foreach ($pendingReservations as $reservation) {
                        // S'assurer que training_data est toujours un tableau
                        $existingTrainingData = [];

                        // Vérifier si training_data est déjà défini
                        if ($reservation->training_data !== null) {
                            if (is_array($reservation->training_data)) {
                                $existingTrainingData = $reservation->training_data;
                            } elseif (is_string($reservation->training_data)) {
                                // Si c'est une chaîne JSON, essayer de la décoder
                                $decoded = json_decode($reservation->training_data, true);
                                if (is_array($decoded)) {
                                    $existingTrainingData = $decoded;
                                }
                            }
                        }
                        // Vérifier si l'ID de formation existe déjà dans les données
                        if (!in_array((string)$formationId, $existingTrainingData)) {
                            $existingTrainingData[] = $trainingData;  // Ajouter seulement l'ID
                            $reservation->training_data = $existingTrainingData;
                            $reservation->save();

                            Log::info("ID de Formation {$formationId} ajouté à la réservation {$reservation->id}");
                        }
                    }
                }
            }
        }
        // Compter le nombre de formations dans le panier
        $trainingIds = $cart->training_ids ?: [];
        $cartCount = count($trainingIds);

        return response()->json([
            'success' => true,
            'message' => 'Formation ajoutée au panier avec succès',
            'cartCount' => $cartCount
        ]);
    } catch (\Exception $e) {
        Log::error('Erreur lors de l\'ajout au panier: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur: ' . $e->getMessage()
        ], 500);
    }
}

public function supprimer(Request $request)
{
    Log::info('Received request to supprimer', ['formation_id' => $request->formation_id]);
    $userId = Auth::id() ?? session()->getId();
    $formationId = $request->formation_id;

    $cart = Cart::where('user_id', $userId)->first();

    if (!$cart) {
        return response()->json([
            'success' => false,
            'message' => 'Panier introuvable'
        ], 404);
    }

    $trainingIds = $cart->training_ids ?: [];

    // Vérifier si la formation est dans le panier
    $key = array_search($formationId, $trainingIds);
    if ($key === false) {
        return response()->json([
            'success' => false,
            'message' => 'Formation introuvable dans votre panier'
        ], 404);
    }

    // Supprimer la formation du panier
    array_splice($trainingIds, $key, 1);
    $cart->training_ids = array_values($trainingIds); // Réindexer le tableau
    $cart->save();

    // Synchroniser avec les réservations en attente (status=0)
    if (Auth::check()) {
        // Convertir l'ID de formation en entier et en chaîne pour les comparaisons
        $formationIdInt = (int)$formationId;
        $formationIdStr = (string)$formationId;

        Log::info("Suppression en cours pour formation: ID int={$formationIdInt}, ID string={$formationIdStr}");

        $pendingReservations = Reservation::where('user_id', $userId)
            ->where('status', 0) // Réservations en attente uniquement
            ->get();

        foreach ($pendingReservations as $reservation) {
            Log::info("Traitement de la réservation: " . $reservation->id, [
                'training_data_type' => gettype($reservation->training_data),
                'training_data' => $reservation->training_data
            ]);

            // Assurons-nous que training_data est bien un tableau
            $trainingData = $reservation->training_data;
            if (is_string($trainingData)) {
                $trainingData = json_decode($trainingData, true) ?: [];
            }

            if (empty($trainingData)) {
                continue; // Passer à la réservation suivante si pas de données
            }

            // Cas 1: Si training_data est un simple tableau d'IDs (comme [4,8,6])
            if (isset($trainingData[0]) && !is_array($trainingData[0])) {
                Log::info("Format détecté: Tableau simple d'IDs");
                $keyToRemove = array_search($formationIdInt, $trainingData);
                if ($keyToRemove === false) {
                    $keyToRemove = array_search($formationIdStr, $trainingData);
                }

                if ($keyToRemove !== false) {
                    array_splice($trainingData, $keyToRemove, 1);
                    $reservation->training_data = array_values($trainingData);
                    $reservation->save();
                    Log::info("Formation {$formationId} supprimée de la réservation {$reservation->id} (tableau simple)");
                }
            }
            // Cas 2: Si training_data est un tableau d'objets (comme [{id: 4, ...}, {id: 8, ...}])
            else {
                Log::info("Format détecté: Tableau d'objets");
                $updatedTrainingData = [];
                $removed = false;

                foreach ($trainingData as $item) {
                    // Récupérer l'ID de l'élément de formation
                    $itemId = null;
                    if (is_array($item) && isset($item['id'])) {
                        $itemId = $item['id'];
                    } elseif (is_object($item) && isset($item->id)) {
                        $itemId = $item->id;
                    }

                    // Comparer avec les deux formats (entier et chaîne)
                    if ($itemId !== $formationIdInt && $itemId !== $formationIdStr) {
                        $updatedTrainingData[] = $item;
                    } else {
                        $removed = true;
                        Log::info("Formation trouvée et supprimée du training_data: ID={$itemId}");
                    }
                }

                if ($removed) {
                    $reservation->training_data = $updatedTrainingData;
                    $reservation->save();
                    Log::info("Formation {$formationId} supprimée de la réservation {$reservation->id} (tableau d'objets)", [
                        'ancien_count' => count($trainingData),
                        'nouveau_count' => count($updatedTrainingData)
                    ]);
                }
            }
        }
    }

    // Ajoutez une log pour déboguer
    Log::debug("Formation supprimée du panier: ID={$formationId}, Panier après suppression:", [
        'training_ids' => $cart->training_ids,
        'count' => count($cart->training_ids)
    ]);

    // Recalculer les totaux
    $trainings = Training::whereIn('id', $trainingIds)->get();

    $totalPrice = 0;
    $totalWithoutDiscount = 0;
    $discountedItemsOriginalPrice = 0;
    $discountedItemsFinalPrice = 0;
    $hasDiscount = false;

    foreach ($trainings as $training) {
        if ($training && $training->price) {
            $originalPrice = $training->price;
            $totalWithoutDiscount += $originalPrice;

            if ($training->discount > 0) {
                $hasDiscount = true;
                $discountedPrice = $originalPrice * (1 - $training->discount / 100);

                $discountedItemsOriginalPrice += $originalPrice;
                $discountedItemsFinalPrice += $discountedPrice;
                $totalPrice += $discountedPrice;
            } else {
                $totalPrice += $originalPrice;
            }
        }
    }

    $globalDiscountPercentage = 0;
    if ($totalWithoutDiscount > 0 && $totalPrice < $totalWithoutDiscount) {
        $globalDiscountPercentage = round(100 - ($totalPrice / $totalWithoutDiscount * 100));
    }

    $discountPercentage = 0;
    if ($discountedItemsOriginalPrice > 0 && $hasDiscount) {
        $discountPercentage = round(100 - ($discountedItemsFinalPrice / $discountedItemsOriginalPrice * 100));
    }

    $formattedTotalPrice = number_format($totalPrice, 3);
    $formattedTotalWithoutDiscount = number_format($totalWithoutDiscount, 3);
    $formattedDiscountedItemsOriginalPrice = number_format($discountedItemsOriginalPrice, 3);
    return response()->json([
        'success' => true,
        'message' => 'Formation supprimée du panier',
        'cartCount' => count($trainingIds),
        'totalPrice' => $formattedTotalPrice,
        'totalWithoutDiscount' => $formattedTotalWithoutDiscount,
        'discountedItemsOriginalPrice' => $formattedDiscountedItemsOriginalPrice,
        'discountPercentage' => $globalDiscountPercentage,
        'individualDiscountPercentage' => $discountPercentage,
        'hasDiscount' => $hasDiscount
    ]);
}

// Dans votre PanierController.php

// public function getFormationsAvailability(Request $request)
// {
//     try {
//         // Récupérer les IDs depuis la requête
//         $formationIds = $request->input('formation_ids', []);

//         // Validation
//         if (empty($formationIds) || !is_array($formationIds)) {
//             return response()->json([
//                 'success' => true,
//                 'remaining_seats' => [],
//                 'total_seats' => [],
//                 'complete_formations' => [],
//             ]);
//         }

//         // Convertir tous les IDs en entiers
//         $formationIds = array_map('intval', $formationIds);
//         $formationIds = array_filter($formationIds, function($id) {
//             return $id > 0;
//         });

//         if (empty($formationIds)) {
//             return response()->json([
//                 'success' => true,
//                 'remaining_seats' => [],
//                 'total_seats' => [],
//                 'complete_formations' => [],
//             ]);
//         }

//         // Récupérer toutes les formations concernées en une seule requête
//         $formations = Training::whereIn('id', $formationIds)
//             ->get(['id', 'total_seats', 'start_date']);

//         // Initialiser les résultats
//         $results = [
//             'success' => true,
//             'remaining_seats' => [],
//             'total_seats' => [],
//             'complete_formations' => [],
//         ];

//         // Si aucune formation trouvée, retourner des tableaux vides
//         if ($formations->isEmpty()) {
//             return response()->json($results);
//         }

//         // Récupérer les réservations payées
//         $reservations = Reservation::where('status', 1) // Seules les réservations payées comptent
//             ->whereNotNull('training_data')
//             ->where('training_data', '!=', '')
//             ->where('training_data', '!=', '[]')
//             ->get(['id', 'training_data']);

//         // Compter les réservations par formation
//         $reservationCounts = array_fill_keys($formationIds, 0);

//         foreach ($reservations as $reservation) {
//             $trainingData = is_string($reservation->training_data)
//                 ? json_decode($reservation->training_data, true)
//                 : $reservation->training_data;

//             if (!is_array($trainingData) || empty($trainingData)) {
//                 continue;
//             }

//             foreach ($trainingData as $item) {
//                 $formationId = is_array($item) ? ($item['id'] ?? null) : $item;
//                 if ($formationId && in_array($formationId, $formationIds)) {
//                     $reservationCounts[$formationId]++;
//                 }
//             }
//         }

//         // Calculer les places restantes
//         foreach ($formations as $formation) {
//             $totalSeats = (int)($formation->total_seats ?? 0);
//             $confirmedReservations = $reservationCounts[$formation->id] ?? 0;
//             $remainingSeats = max(0, $totalSeats - $confirmedReservations);

//             $results['remaining_seats'][$formation->id] = $remainingSeats;
//             $results['total_seats'][$formation->id] = $totalSeats;

//             // Une formation est complète SEULEMENT si toutes les places sont occupées par des réservations payées
//             if ($remainingSeats === 0 && $totalSeats > 0) {
//                 $results['complete_formations'][] = $formation->id;
//             }
//         }

//         return response()->json($results);

//     } catch (\Exception $e) {
//         // Log l'erreur pour le débogage
//         Log::error('Erreur dans getFormationsAvailability: ' . $e->getMessage(), [
//             'trace' => $e->getTraceAsString(),
//             'request_data' => $request->all()
//         ]);

//         return response()->json([
//             'success' => false,
//             'error' => 'Erreur serveur',
//             'message' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
//         ], 500);
//     }
// }

    //     public function getFormationsAvailability(array $formationIds)
    // {
    //     if (empty($formationIds)) {
    //         return [
    //             'remaining_seats' => [],
    //             'complete_formations' => [],
    //         ];
    //     }

    //     // Récupérer toutes les formations concernées en une seule requête
    //     $formations = Training::whereIn('id', $formationIds)
    //         ->get(['id', 'total_seats', 'start_date']);

    //     // Initialiser les résultats
    //     $results = [
    //         'remaining_seats' => [],
    //         'complete_formations' => [],
    //     ];

    //     // Si aucune formation trouvée, retourner des tableaux vides
    //     if ($formations->isEmpty()) {
    //         return $results;
    //     }

    //     $reservations = Reservation::where('status', 1) // Seules les réservations payées comptent
    //         ->whereNotNull('training_data')
    //         ->where('training_data', '!=', '')
    //         ->where('training_data', '!=', '[]')
    //         ->get(['id', 'training_data']);

    //     // Compter les réservations par formation
    //     $reservationCounts = array_fill_keys($formationIds, 0);

    //     foreach ($reservations as $reservation) {
    //         $trainingData = is_string($reservation->training_data)
    //             ? json_decode($reservation->training_data, true)
    //             : $reservation->training_data;

    //         if (!is_array($trainingData) || empty($trainingData)) {
    //             continue;
    //         }

    //         foreach ($trainingData as $item) {
    //             $formationId = is_array($item) ? ($item['id'] ?? null) : $item;
    //             if ($formationId && in_array($formationId, $formationIds)) {
    //                 $reservationCounts[$formationId]++;
    //             }
    //         }
    //     }

    //     // Calculer les places restantes
    //     foreach ($formations as $formation) {
    //         $totalSeats = (int)($formation->total_seats ?? 0);
    //         $confirmedReservations = $reservationCounts[$formation->id] ?? 0;
    //         $remainingSeats = max(0, $totalSeats - $confirmedReservations);

    //         $results['remaining_seats'][$formation->id] = $remainingSeats;

    //         // Une formation est complète SEULEMENT si toutes les places sont occupées par des réservations payées
    //         if ($remainingSeats === 0 && $totalSeats > 0) {
    //             $results['complete_formations'][] = $formation->id;
    //         }
    //     }

    //     return $results;
    // }
    //     public function getCartData()
    //     {
    //         $userId = Auth::id();
    //         if (!$userId) {
    //             return response()->json([
    //                 'items' => [],
    //                 'count' => 0,
    //                 'completeFormations' => [],
    //                 'expiredFormations' => []
    //             ]);
    //         }
    //         $carts = Cart::where('user_id', $userId)->get();
    //         $items = [];
    //         $formationIds = [];

    //         // Récupérer tous les IDs de formations
    //         foreach ($carts as $cart) {
    //             $cartFormationIds = $cart->training_ids ?: [];
    //             $items = array_merge($items, $cartFormationIds);
    //             $formationIds = array_merge($formationIds, $cartFormationIds);
    //         }
    //         $formationIds = array_unique($formationIds);

    //         // Calculer la disponibilité
    //         $availability = $this->getFormationsAvailability($formationIds);

    //         // Vérifier les formations expirées
    //         $expiredFormations = [];
    //         // Définir la date système (28 mai 2025, 01:53 AM CET) - même logique que dans index()
    //         $currentDate = \Carbon\Carbon::create(2025, 5, 28, 1, 53, 0, 'CET');

    //         if (!empty($formationIds)) {
    //             $formations = Training::whereIn('id', $formationIds)
    //                 ->get(['id', 'start_date']);

    //             foreach ($formations as $formation) {
    //                 if ($formation->start_date) {
    //                     $startDate = \Carbon\Carbon::parse($formation->start_date);

    //                     // Comparer seulement les dates (sans l'heure) pour exclure le même jour
    //                     // La formation est expirée seulement si sa date de début est antérieure à aujourd'hui
    //                     if ($startDate->toDateString() < $currentDate->toDateString()) {
    //                         $expiredFormations[] = $formation->id;
    //                     }
    //                 }
    //             }
    //         }

    //         return response()->json([
    //             'items' => array_unique($items),
    //             'count' => count(array_unique($items)),
    //             'completeFormations' => $availability['complete_formations'],
    //             'expiredFormations' => array_unique($expiredFormations)
    //         ]);
    // }

//     public function getCartData()
// {
//     $userId = Auth::id();
//     if (!$userId) {
//         return response()->json([
//             'items' => [],
//             'count' => 0,
//             'completeFormations' => [],
//             'expiredFormations' => []
//         ]);
//     }
//     $carts = Cart::where('user_id', $userId)->get();
//     $items = [];
//     $formationIds = [];

//     // Récupérer tous les IDs de formations
//     foreach ($carts as $cart) {
//         $cartFormationIds = $cart->training_ids ?: [];
//         $items = array_merge($items, $cartFormationIds);
//         $formationIds = array_merge($formationIds, $cartFormationIds);
//     }
//     $formationIds = array_unique($formationIds);

//     // Calculer la disponibilité
//     $availability = $this->getFormationsAvailability($formationIds);

//     // Vérifier les formations expirées
//     $expiredFormations = [];
//     // Définir la date système (28 mai 2025, 01:53 AM CET)
//     $currentDate = \Carbon\Carbon::create(2025, 5, 28, 1, 53, 0, 'CET');

//     if (!empty($formationIds)) {
//         $formations = Training::whereIn('id', $formationIds)
//             ->get(['id', 'start_date']);

//         foreach ($formations as $formation) {
//             if ($formation->start_date) {
//                 $startDate = \Carbon\Carbon::parse($formation->start_date);

//                 // Comparer seulement les dates (sans l'heure)
//                 if ($startDate->toDateString() < $currentDate->toDateString()) {
//                     $expiredFormations[] = $formation->id;
//                 }
//             }
//         }
//     }

//     return response()->json([
//         'items' => array_unique($items),
//         'count' => count(array_unique($items)),
//         'completeFormations' => $availability['complete_formations'],
//         'expiredFormations' => array_unique($expiredFormations)
//     ]);
// }

public function getCartData()
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json([
            'items' => [],
            'count' => 0,
            'completeFormations' => [],
            'expiredFormations' => []
        ]);
    }

    $carts = Cart::where('user_id', $userId)->get();
    $items = [];
    $formationIds = [];

    // Récupérer tous les IDs de formations
    foreach ($carts as $cart) {
        $cartFormationIds = $cart->training_ids ?: [];
        $items = array_merge($items, $cartFormationIds);
        $formationIds = array_merge($formationIds, $cartFormationIds);
    }

    $formationIds = array_unique($formationIds);

    // Calculer la disponibilité avec la méthode privée
    $availability = $this->calculateFormationsAvailability($formationIds);

    // Vérifier les formations expirées
    $expiredFormations = [];
    $currentDate = \Carbon\Carbon::create(2025, 5, 28, 1, 53, 0, 'CET');

    if (!empty($formationIds)) {
        $formations = Training::whereIn('id', $formationIds)
            ->get(['id', 'start_date']);

        foreach ($formations as $formation) {
            if ($formation->start_date) {
                $startDate = \Carbon\Carbon::parse($formation->start_date);
                if ($startDate->toDateString() < $currentDate->toDateString()) {
                    $expiredFormations[] = $formation->id;
                }
            }
        }
    }

    return response()->json([
        'items' => array_unique($items),
        'count' => count(array_unique($items)),
        'completeFormations' => $availability['complete_formations'] ?? [],
        'expiredFormations' => array_unique($expiredFormations)
    ]);
}

// Méthode privée pour calculer la disponibilité
private function calculateFormationsAvailability($formationIds)
{
    try {
        // Validation
        if (empty($formationIds) || !is_array($formationIds)) {
            return [
                'remaining_seats' => [],
                'total_seats' => [],
                'complete_formations' => [],
            ];
        }

        // Convertir tous les IDs en entiers
        $formationIds = array_map('intval', $formationIds);
        $formationIds = array_filter($formationIds, function($id) {
            return $id > 0;
        });

        if (empty($formationIds)) {
            return [
                'remaining_seats' => [],
                'total_seats' => [],
                'complete_formations' => [],
            ];
        }

        // Récupérer toutes les formations concernées
        $formations = Training::whereIn('id', $formationIds)
            ->get(['id', 'total_seats', 'start_date']);

        $results = [
            'remaining_seats' => [],
            'total_seats' => [],
            'complete_formations' => [],
        ];

        if ($formations->isEmpty()) {
            return $results;
        }

        // Récupérer les réservations payées
        $reservations = Reservation::where('status', 1)
            ->whereNotNull('training_data')
            ->where('training_data', '!=', '')
            ->where('training_data', '!=', '[]')
            ->get(['id', 'training_data']);

        // Compter les réservations par formation
        $reservationCounts = array_fill_keys($formationIds, 0);

        foreach ($reservations as $reservation) {
            $trainingData = is_string($reservation->training_data)
                ? json_decode($reservation->training_data, true)
                : $reservation->training_data;

            if (!is_array($trainingData) || empty($trainingData)) {
                continue;
            }

            foreach ($trainingData as $item) {
                $formationId = is_array($item) ? ($item['id'] ?? null) : $item;
                if ($formationId && in_array($formationId, $formationIds)) {
                    $reservationCounts[$formationId]++;
                }
            }
        }

        // Calculer les places restantes
        foreach ($formations as $formation) {
            $totalSeats = (int)($formation->total_seats ?? 0);
            $confirmedReservations = $reservationCounts[$formation->id] ?? 0;
            $remainingSeats = max(0, $totalSeats - $confirmedReservations);

            $results['remaining_seats'][$formation->id] = $remainingSeats;
            $results['total_seats'][$formation->id] = $totalSeats;

            if ($remainingSeats === 0 && $totalSeats > 0) {
                $results['complete_formations'][] = $formation->id;
            }
        }

        return $results;

    } catch (\Exception $e) {
        Log::error('Erreur dans calculateFormationsAvailability: ' . $e->getMessage());
        return [
            'remaining_seats' => [],
            'total_seats' => [],
            'complete_formations' => [],
        ];
    }
}

// Garder la méthode publique pour l'API
public function getFormationsAvailability(Request $request)
{
    $formationIds = $request->input('formation_ids', []);
    $results = $this->calculateFormationsAvailability($formationIds);

    return response()->json(array_merge(['success' => true], $results));
}
public function checkInCart($formationId)
        {
            $userId = Auth::id() ?? session()->getId();
            $cart = Cart::where('user_id', $userId)->first();

            $inCart = false;
            if ($cart && is_array($cart->training_ids)) {
                // Log for debugging
                Log::debug("Checking if formation {$formationId} is in cart: ", [
                    'training_ids' => $cart->training_ids,
                    'formationId' => $formationId
                ]);

                $inCart = in_array($formationId, $cart->training_ids);
            }

            return response()->json(['in_cart' => $inCart, 'cart_items' => $cart ? $cart->training_ids : []]);
}

 public function getCartDetails(Request $request)
    {
        $userId = Auth::id() ?? session()->getId();
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart || empty($cart->training_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Panier vide',
                'trainings' => [],
                'totalPrice' => 0
            ]);
        }
        // Récupérer toutes les formations dans le panier
        $trainings = Training::whereIn('id', $cart->training_ids)->get();
        // Calculer le prix total
        $totalPrice = $trainings->sum(function($training) {
            return $training->final_price ? $training->final_price : $training->price;
        });
        // Formater les données pour le front-end
        $formattedTrainings = $trainings->map(function($training) {
            return [
                'id' => $training->id,
                'title' => $training->title,
                'start_date' => $training->start_date,
                'end_date' => $training->end_date,
                'price' => (float) $training->price,
                'discount' => (float) $training->discount,
                'final_price' => (float) ($training->final_price ? $training->final_price : $training->price),
                'duration' => $training->formatted_duration
            ];
        });

        return response()->json([
            'success' => true,
            'trainings' => $formattedTrainings,
            'totalPrice' => $totalPrice
        ]);
    }
public function verifyCartItemsExistence(Request $request){
    $userId = Auth::id() ?? session()->getId();
    $cart = Cart::where('user_id', $userId)->first();
    if (!$cart || empty($cart->training_ids)) {
        return response()->json([
            'success' => true,
            'cartCount' => 0,
            'removed_items' => []
        ]);
    }
    $cartItems = $cart->training_ids;
    $existingTrainings = Training::whereIn('id', $cartItems)->pluck('id')->toArray();
    // Convertir en chaînes pour une comparaison cohérente
    $existingTrainings = array_map('strval', $existingTrainings);
    $cartItems = array_map('strval', $cartItems);
    // Trouver les articles qui n'existent plus
    $removedItems = array_diff($cartItems, $existingTrainings);
    // Si des articles ont été supprimés, mettre à jour le panier
    if (!empty($removedItems)) {
        $updatedItems = array_diff($cartItems, $removedItems);
        $cart->training_ids = array_values($updatedItems);
        $cart->save();
        // Recalculer les totaux
        $trainings = Training::whereIn('id', $updatedItems)->get();
        $totalPrice = $this->calculateCartTotals($trainings);
        return response()->json([
            'success' => true,
            'cartCount' => count($updatedItems),
            'removed_items' => array_values($removedItems),
            'totalPrice' => number_format($totalPrice['totalPrice'], 3),
            'discountedItemsOriginalPrice' => number_format($totalPrice['discountedItemsOriginalPrice'], 3),
            'discountPercentage' => $totalPrice['discountPercentage'],
            'hasDiscount' => $totalPrice['hasDiscount']
        ]);
    }
    return response()->json([
        'success' => true,
        'cartCount' => count($cartItems),
        'removed_items' => []
    ]);
}
private function calculateCartTotals($trainings)
{
    $totalPrice = 0;
    $totalWithoutDiscount = 0;
    $discountedItemsOriginalPrice = 0;
    $discountedItemsFinalPrice = 0;
    $hasDiscount = false;
    foreach ($trainings as $training) {
        if ($training && $training->price) {
            $originalPrice = $training->price;
            $totalWithoutDiscount += $originalPrice;

            if ($training->discount > 0) {
                $hasDiscount = true;
                $discountedPrice = $originalPrice * (1 - $training->discount / 100);

                $discountedItemsOriginalPrice += $originalPrice;
                $discountedItemsFinalPrice += $discountedPrice;
                $totalPrice += $discountedPrice;
            } else {
                $totalPrice += $originalPrice;
            }
        }
    }
    $globalDiscountPercentage = 0;
    if ($totalWithoutDiscount > 0 && $totalPrice < $totalWithoutDiscount) {
        $globalDiscountPercentage = round(100 - ($totalPrice / $totalWithoutDiscount * 100));
    }
    $discountPercentage = 0;
    if ($discountedItemsOriginalPrice > 0 && $hasDiscount) {
        $discountPercentage = round(100 - ($discountedItemsFinalPrice / $discountedItemsOriginalPrice * 100));
    }
    return [
        'totalPrice' => $totalPrice,
        'totalWithoutDiscount' => $totalWithoutDiscount,
        'discountedItemsOriginalPrice' => $discountedItemsOriginalPrice,
        'discountPercentage' => $globalDiscountPercentage,
        'individualDiscountPercentage' => $discountPercentage,
        'hasDiscount' => $hasDiscount
    ];
}
/** Méthode pour obtenir le nombre d'articles dans le panier*/
public function getCount()
{
    $userId = Auth::id() ?? session()->getId();
    $cart = Cart::where('user_id', $userId)->first();
    $count = 0;
    if ($cart && is_array($cart->training_ids)) {
        $count = count($cart->training_ids);
    }
    return response()->json(['count' => $count]);
}
}
