<?php
namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\Reservation;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
class ReservationController extends Controller
{

    public function getRemainingPlaces($trainingId)
    {
        $training = Training::findOrFail($trainingId);
        $remainingSeats = $training->getRemainingSeatsAttribute();
        return response()->json([
            'remaining_places' => $remainingSeats,
            'is_full' => $remainingSeats <= 0,
            'total_places' => $training->total_seats
        ]);
    }

    public function checkCompleteFormations()
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->first();
        $hasComplete = false;

        if ($cart && !empty($cart->training_ids)) {
            foreach ($cart->training_ids as $trainingId) {
                $training = Training::find($trainingId);
                if ($training && $training->getRemainingSeatsAttribute() <= 0) {
                    $hasComplete = true;
                    break;
                }
            }
        }

        return response()->json(['has_complete_formations' => $hasComplete]);
    }
    //zedtha neww
    public function checkConfirmedReservation($formationId)
{
    try {
        // Vérifier si la formation a une réservation avec status=1
        $hasConfirmedReservation = Reservation::where('status', 1)
            ->where(function($query) use ($formationId) {
                $query->where('training_data', 'LIKE', '%"' . $formationId . '"%')
                      ->orWhere('training_data', 'LIKE', '%[' . $formationId . ']%')
                      ->orWhere('training_data', 'LIKE', '%[' . $formationId . ',%')
                      ->orWhere('training_data', 'LIKE', '%,' . $formationId . ']%')
                      ->orWhere('training_data', 'LIKE', '%,' . $formationId . ',%')
                      ->orWhere('training_data', '=', $formationId)
                      ->orWhere('training_data', '=', '"' . $formationId . '"');
            })
            ->exists();

        // Vérification supplémentaire pour les données JSON
        if (!$hasConfirmedReservation) {
            $reservations = Reservation::where('status', 1)->get();

            foreach ($reservations as $reservation) {
                if (!empty($reservation->training_data)) {
                    $trainingIds = $reservation->training_data;

                    if (is_string($trainingIds)) {
                        try {
                            $trainingIds = json_decode($trainingIds, true);
                        } catch (\Exception $e) {
                            if (is_numeric($trainingIds) && (int)$trainingIds == $formationId) {
                                $hasConfirmedReservation = true;
                                break;
                            }
                            continue;
                        }
                    }

                    if (is_array($trainingIds)) {
                        foreach ($trainingIds as $id) {
                            if (is_numeric($id) && (int)$id == $formationId) {
                                $hasConfirmedReservation = true;
                                break 2;
                            }
                        }
                    } elseif (is_numeric($trainingIds) && (int)$trainingIds == $formationId) {
                        $hasConfirmedReservation = true;
                        break;
                    }
                }
            }
        }

        return response()->json([
            'has_confirmed_reservation' => $hasConfirmedReservation,
            'formation_id' => $formationId
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur lors de la vérification des réservations confirmées: " . $e->getMessage());
        return response()->json([
            'has_confirmed_reservation' => false,
            'error' => 'Erreur lors de la vérification'
        ], 500);
    }
}
    public function cleanEmptyReservationsEndpoint(Request $request)
    {
        try {
            // Mode débogage - ne supprime pas les réservations
            $debugMode = $request->input('debug', false);

            // Vérifier si l'utilisateur est connecté
            if (!Auth::check()) {
                Log::warning('Tentative de nettoyage sans authentification');
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour nettoyer les réservations'
                ], 401);
            }

            // Rechercher toutes les réservations avec training_data null ou vide
            $query = Reservation::query();

            // Vérifier s'il y a des réservations avec training_data null
            $nullCount = (clone $query)->whereNull('training_data')->count();
            Log::info("Réservations avec training_data NULL: {$nullCount}");

            // Vérifier s'il y a des réservations avec training_data = '[]'
            $emptyArrayCount = (clone $query)->where('training_data', '[]')->count();
            Log::info("Réservations avec training_data '[]': {$emptyArrayCount}");

            // Vérifier s'il y a des réservations avec training_data = ''
            $emptyStringCount = (clone $query)->where('training_data', '')->count();
            Log::info("Réservations avec training_data '': {$emptyStringCount}");

            // Regrouper les conditions
            $emptyReservations = $query->whereNull('training_data')
                ->orWhere('training_data', '[]')
                ->orWhere('training_data', '')
                ->get();

            $count = $emptyReservations->count();

            // Afficher les détails de chaque réservation trouvée
            foreach ($emptyReservations as $reservation) {
                Log::info("Réservation trouvée: ID={$reservation->id}, training_data=" . json_encode($reservation->training_data));
            }

            if ($count > 0) {
                if (!$debugMode) {
                    // Supprimer les réservations vides
                    foreach ($emptyReservations as $reservation) {
                        $reservation->delete();
                    }

                    Log::info("{$count} réservations vides supprimées avec succès");

                    return response()->json([
                        'success' => true,
                        'message' => "{$count} réservations vides supprimées avec succès"
                    ]);
                } else {
                    // En mode débogage, renvoyer juste les informations
                    return response()->json([
                        'success' => true,
                        'message' => "{$count} réservations vides trouvées (mode débogage - pas de suppression)",
                        'reservations' => $emptyReservations->map(function($r) {
                            return [
                                'id' => $r->id,
                                'training_data' => $r->training_data,
                                'created_at' => $r->created_at
                            ];
                        })
                    ]);
                }
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Aucune réservation vide trouvée'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Exception lors du nettoyage des réservations: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du nettoyage des réservations: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer une réservation'
            ], 401);
        }
        // Récupérer le panier de l'utilisateur
        $cart = Cart::where('user_id', Auth::id())->first();
        // Vérifier si le panier existe
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun panier trouvé'
            ], 404);
        }
        // Vérifier si le panier a des formations
        if (empty($cart->training_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Votre panier est vide'
            ], 400);
        }
 // Dans la méthode create() autour de la ligne 191
       try {
        $trainingIds = [];
        if (!empty($cart->training_ids)) {
            $trainings = Training::whereIn('id', $cart->training_ids)->get();

            // Convertir explicitement chaque ID en string
            $trainingIds = $trainings->pluck('id')->map(function($id) {
                return (string)$id; // Conversion forcée en string
            })->toArray();

            Log::info('IDs convertis en strings', [
                'training_ids' => $trainingIds,
                'training_ids_count' => count($trainingIds)
            ]);
        }
            // Créer la réservation
            $reservation = new Reservation();
            $reservation->cart_id = $cart->id;
            $reservation->user_id = Auth::id();
            $reservation->training_data = $trainingIds; // Maintenant toujours des strings
            $reservation->reservation_date = $request->input('reservation_date', now()->toDateString());
            $reservation->reservation_time = $request->input('reservation_time', now()->toTimeString());
            $reservation->status = false; // Non payé par défaut
            $reservation->save();

            return response()->json([
                'success' => true,
                'message' => 'Réservation effectuée avec succès',
                'reservation_id' => $reservation->id,
                'clearCart' => true
            ]);
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Erreur lors de la création de la réservation: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la réservation: ' . $e->getMessage()
            ], 500);
        }
    }




    public function checkReservation()
{
    $userId = Auth::id();

    // Récupérer le panier actuel
    $cart = Cart::where('user_id', $userId)->first();
    $hasItemsInCart = $cart && !empty($cart->training_ids);

    // Vérifier s'il y a une réservation confirmée (status = 1)
    $confirmedReservation = Reservation::where('user_id', $userId)
                                ->where('status', 1)
                                ->orderBy('created_at', 'desc')
                                ->first();

    // Vérifier s'il y a une réservation en attente (status = 0)
    $pendingReservation = Reservation::where('user_id', $userId)
                                ->where('status', 0)
                                ->orderBy('created_at', 'desc')
                                ->first();

    // Construire une réponse complète
    $response = [
        'hasItemsInCart' => $hasItemsInCart,
        'hasConfirmedReservation' => $confirmedReservation !== null,
        'hasPendingReservation' => $pendingReservation !== null,
        'hasReservation' => false,
        'reservation_id' => null,
        'shouldCreateNewReservation' => false,
        'buttonState' => 'reserve' // Par défaut: montrer le bouton "Réserver"
    ];

    // Si nous avons une réservation en attente, l'utiliser
    if ($pendingReservation) {
        $response['hasReservation'] = true;
        $response['reservation_id'] = $pendingReservation->id;
        $response['buttonState'] = 'viewReservations'; // Montrer "Voir mes réservations"
    }

    // Si nous avons des articles dans le panier et une réservation confirmée,
    // il faut créer une nouvelle réservation
    elseif ($hasItemsInCart && $confirmedReservation) {
        $response['shouldCreateNewReservation'] = true;
        $response['buttonState'] = 'reserve'; // Montrer "Réserver"
    }

    // Si une réservation est confirmée mais pas d'articles dans le panier
    elseif ($confirmedReservation && !$hasItemsInCart) {
        $response['hasReservation'] = true;
        $response['reservation_id'] = $confirmedReservation->id;
        $response['buttonState'] = 'viewReservations'; // Montrer "Voir mes réservations"
    }

    return response()->json($response);
}
 private function synchronizeTrainingData($userId, $trainingIds)
    {
        // Récupérer toutes les réservations en attente de l'utilisateur
        $pendingReservations = Reservation::where('user_id', $userId)
            ->where('status', 0)
            ->get();

        if ($pendingReservations->isEmpty()) {
            return; // Pas de réservations à synchroniser
        }

        // Vérifier que les IDs des formations existent
        $existingTrainingIds = Training::whereIn('id', $trainingIds)
        ->pluck('id')
        ->map(function($id) {
            return (string)$id; // Conversion en string
        })->toArray();

    foreach ($pendingReservations as $reservation) {
        $reservation->training_data = $existingTrainingIds; // Stockage en strings
        $reservation->save();

        Log::info("Mise à jour des IDs (strings) dans la réservation {$reservation->id}", [
            'training_ids_count' => count($existingTrainingIds)
        ]);
    }
}

      public function updateTrainingData(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Panier non trouvé'
            ], 404);
        }
        try {
            // Synchroniser les IDs de formation avec les réservations
            $this->synchronizeTrainingData($userId, $cart->training_ids ?? []);

            return response()->json([
                'success' => true,
                'message' => 'IDs de formation mis à jour dans les réservations'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des IDs de formation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkAuth()
    {
        return response()->json([
            'authenticated' => Auth::check()
        ]);
    }

    public function getDetails()
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['trainings' => [], 'discount' => 0]);
        }

        $trainings = $cart->getFormations();
        $discount = 0; // À remplacer par votre logique de remise

        return response()->json([
            'trainings' => $trainings->map(function($training) {
                return [
                    'id' => $training->id,
                    'title' => $training->title,
                    'price' => $training->price
                ];
            }),
            'discount' => $discount
        ]);
    }



    public function listStudentsWithReservations(Request $request)
{
    // Initialiser la requête de base pour les réservations
    $query = Reservation::with(['user']);

    // Appliquer le filtre de statut si fourni
    if ($request->filled('status') && $request->status != '') {
        $query->where('status', $request->status);
    }

    if ($request->filled('search') && trim($request->search) !== '') {
        $searchTerm = trim($request->search);
        $query->where(function($q) use ($searchTerm) {
            // Recherche exacte par ID si c'est numérique
            if (is_numeric($searchTerm)) {
                $q->orWhere('reservations.id', '=', (int)$searchTerm);
            }
            $q->orWhereHas('user', function($userQuery) use ($searchTerm) {
                if (is_numeric($searchTerm)) {
                    $userQuery->where('users.phone', 'LIKE', "%{$searchTerm}")
                            ->orWhere('users.phone', '=', $searchTerm);
                } else {
                    // Si non numérique, recherche standard
                    $userQuery->where('users.phone', 'LIKE', "%{$searchTerm}%");
                }

                // Recherche normale pour nom et prénom
                $userQuery->orWhere('users.name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('users.lastname', 'LIKE', "%{$searchTerm}%");
            });
        });
    }

    // Paginer les résultats - 2 réservations par page comme dans votre code original
    $reservations = $query->paginate(10)->appends(request()->query());

    $studentsWithReservations = [];

    foreach ($reservations as $reservation) {
        $user = $reservation->user;

        // Vérifier si l'utilisateur existe
        if ($user) {
            // Initialiser les variables
            $trainings = [];
            $totalOriginal = 0;
            $totalDiscount = 0;
            $totalAfterDiscount = 0;

            // Traiter les données de formation
            if (!empty($reservation->training_data)) {
                // Convertir training_data en tableau si c'est une chaîne
                $trainingIds = is_string($reservation->training_data)
                    ? json_decode($reservation->training_data, true)
                    : $reservation->training_data;

                // Vérifier que nous avons bien un tableau avant de continuer
                if (is_array($trainingIds)) {
                    // Extraire tous les IDs de formation
                    $formationIds = [];
                    foreach ($trainingIds as $data) {
                        if (is_string($data)) {
                            $formationIds[] = $data;
                        } elseif (is_array($data) && isset($data['id'])) {
                            $formationIds[] = $data['id'];
                        }
                    }

                    // Récupérer toutes les formations correspondantes en une seule requête
                      $trainingDetails = Training::whereIn('id', $formationIds)
        ->select('id', 'title', 'price', 'discount', 'start_date', 'end_date') // Ajout des colonnes dates
        ->get()
        ->keyBy('id');

    // Dans la partie où vous créez l'objet training, ajoutez :
    $trainings = collect($trainingIds)->map(function($trainingData) use ($trainingDetails) {
        $trainingId = is_string($trainingData) ? $trainingData : ($trainingData['id'] ?? null);

        if ($trainingId && $trainingDetails->has($trainingId)) {
            $trainingInfo = $trainingDetails->get($trainingId);
            $training = new \stdClass();
            $training->id = $trainingInfo->id;
            $training->title = $trainingInfo->title;
            $training->price = $trainingInfo->price;
            $training->discount = $trainingInfo->discount;
            $training->start_date = $trainingInfo->start_date; // Nouvelle ligne
            $training->end_date = $trainingInfo->end_date;     // Nouvelle ligne

                            // Calculer le prix après remise
                            if ($training->discount > 0) {
                                $discountAmount = ($training->price * $training->discount) / 100;
                                $training->discount_amount = $discountAmount;
                                $training->price_after_discount = $training->price - $discountAmount;
                            } else {
                                $training->discount_amount = 0;
                                $training->price_after_discount = $training->price;
                            }

                            return $training;
                        } else {
                            // Formation non trouvée
                            $training = new \stdClass();
                             $training->start_date = null; // Nouvelle ligne
            $training->end_date = null;
                            $training->id = $trainingId ?? 'N/A';
                            $training->title = 'Formation #' . ($trainingId ?? 'N/A') . ' (non trouvée)';
                            $training->price = 0;
                            $training->discount = 0;
                            $training->discount_amount = 0;
                            $training->price_after_discount = 0;
                            return $training;
                        }
                    });
                }

                // Calculer les totaux
                foreach ($trainings as $training) {
                    $originalPrice = $training->price;
                    $totalOriginal += $originalPrice;

                    // Vérifier si la formation a une remise
                    if ($training->discount > 0) {
                        $discountAmount = ($originalPrice * $training->discount) / 100;
                        $priceAfterDiscount = $originalPrice - $discountAmount;

                        $totalDiscount += $discountAmount;
                        $totalAfterDiscount += $priceAfterDiscount;
                    } else {
                        $totalAfterDiscount += $originalPrice;
                    }
                }
            }

            // Formater le statut de la réservation
            $statusText = '';
            switch ($reservation->status) {
                case 0:
                    $statusText = 'En attente';
                    break;
                case 1:
                    $statusText = 'Confirmée';
                    break;
                default:
                    $statusText = 'Inconnu';
            }

            // Ajouter les informations de l'étudiant et ses réservations
            $studentInfo = [
                'id' => $user->id,
                'reservation_id' => $reservation->id,
                'nom' => $user->lastname ?? 'N/A',
                'prenom' => $user->name ?? 'N/A',
                'telephone' => $user->phone ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'reservation_date' => $reservation->reservation_date,
                'reservation_time' => $reservation->reservation_time,
                'status' => $reservation->status,
                'status_text' => $statusText,
                'formations' => $trainings->map(function($training) {
                    return [
                        'id' => $training->id,
                        'title' => $training->title,
                        'price' => $training->price,
                        'discount' => $training->discount ?? 0,
                        'start_date' => $training->start_date, // Nouvelle ligne
            'end_date' => $training->end_date,     // Nouvelle ligne
                        'discount_amount' => $training->discount_amount ?? 0,
                        'price_after_discount' => $training->price_after_discount ?? $training->price
                    ];
                }),
                'total_original' => $totalOriginal,
                'total_discount' => $totalDiscount,
                'total_after_discount' => $totalAfterDiscount,
                'payment_date' => $reservation->payment_date
            ];

            // Ajouter à la liste des étudiants avec réservations
            $studentsWithReservations[] = $studentInfo;
        }
    }

    // Retourner la vue avec les données paginées
    return view('admin.apps.reservations.reservations-list', [
        'studentsWithReservations' => $studentsWithReservations,
        'reservations' => $reservations
    ]);
}

public function showUserReservations()
{
    // Récupérer toutes les réservations (sans filtrer par utilisateur)
    $reservations = Reservation::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();

    // Pour chaque réservation, récupérer les formations à partir des IDs stockés
    foreach ($reservations as $reservation) {
        if (!empty($reservation->training_data)) {
            // Vérifier si training_data est un tableau, sinon essayer de le décoder
            $trainingIds = $reservation->training_data;

            // S'assurer que nous avons des IDs numériques valides
            if (is_string($trainingIds)) {
                try {
                    $trainingIds = json_decode($trainingIds, true);
                } catch (\Exception $e) {
                    Log::error("Erreur lors du décodage des IDs de formation: " . $e->getMessage());
                }
            }

            // Filtrer pour ne garder que les ID numériques valides
            $validIds = [];
            if (is_array($trainingIds)) {
                foreach ($trainingIds as $id) {
                    // Ne garder que les valeurs numériques
                    if (is_numeric($id)) {
                        $validIds[] = (int)$id;
                    }
                }
            } elseif (is_numeric($trainingIds)) {
                $validIds[] = (int)$trainingIds;
            }

            // Log pour débogage
            Log::info("Reservation #" . $reservation->id . " - Training IDs: " . json_encode($validIds));

            // Récupérer les formations seulement si nous avons des IDs valides
            $trainings = collect();
            if (!empty($validIds)) {
                $trainingDetails = Training::whereIn('id', $validIds)->get();

                // Préparer les objets de formation avec les données calculées
                $trainings = $trainingDetails->map(function($training) {
                    $result = new \stdClass();
                    $result->id = $training->id;
                    $result->title = $training->title;
                    $result->price = $training->price;
                    $result->discount = $training->discount ?? 0;
                    $result->image = $training->image ?? null;
                    // Ajouter les dates de début et de fin
                    $result->start_date = $training->start_date ?? null; // Adjust to your column name, e.g., date_debut
                    $result->end_date = $training->end_date ?? null;     // Adjust to your column name, e.g., date_fin
                    $result->user = $training->user;

                    // Calculer les prix avec remise
                    if ($result->discount > 0) {
                        $result->discount_amount = ($result->price * $result->discount) / 100;
                        $result->price_after_discount = $result->price - $result->discount_amount;
                    } else {
                        $result->discount_amount = 0;
                        $result->price_after_discount = $result->price;
                    }

                    return $result;
                });
            }

            // Calculer le prix total et les remises
            $totalPrice = 0;
            $totalDiscount = 0;

            foreach ($trainings as $training) {
                if ($training->discount > 0) {
                    $totalPrice += $training->price_after_discount;
                    $totalDiscount += $training->discount_amount;
                } else {
                    $totalPrice += $training->price;
                }
            }

            // Ajouter les formations et les infos de prix à la réservation
            $reservation->trainings = $trainings;
            $reservation->total_price = $totalPrice;
            $reservation->total_discount = $totalDiscount;
            $reservation->original_total = $totalPrice + $totalDiscount;
        } else {
            $reservation->trainings = collect();
            $reservation->total_price = 0;
            $reservation->total_discount = 0;
            $reservation->original_total = 0;
        }
    }

    return view('admin.apps.reservations.mes-reservations', compact('reservations'));
}
public function checkUserReservationForFormation($formationId)
{
    try {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            return response()->json(['has_reservation' => false]);
        }

        $userId = auth()->id();

        // Rechercher les réservations de l'utilisateur qui contiennent cette formation
        $hasReservation = Reservation::where('user_id', $userId)
            ->where(function($query) use ($formationId) {
                // Recherche dans training_data (JSON ou string)
                $query->where('training_data', 'LIKE', '%"' . $formationId . '"%')
                      ->orWhere('training_data', 'LIKE', '%[' . $formationId . ']%')
                      ->orWhere('training_data', 'LIKE', '%[' . $formationId . ',%')
                      ->orWhere('training_data', 'LIKE', '%,' . $formationId . ']%')
                      ->orWhere('training_data', 'LIKE', '%,' . $formationId . ',%')
                      ->orWhere('training_data', '=', $formationId)
                      ->orWhere('training_data', '=', '"' . $formationId . '"');
            })
            ->exists();

        // Vérification plus précise en décodant le JSON
        if (!$hasReservation) {
            $reservations = Reservation::where('user_id', $userId)->get();

            foreach ($reservations as $reservation) {
                if (!empty($reservation->training_data)) {
                    $trainingIds = $reservation->training_data;

                    // Si c'est une chaîne JSON, la décoder
                    if (is_string($trainingIds)) {
                        try {
                            $trainingIds = json_decode($trainingIds, true);
                        } catch (\Exception $e) {
                            // Si ce n'est pas du JSON valide, traiter comme un ID simple
                            if (is_numeric($trainingIds) && (int)$trainingIds == $formationId) {
                                $hasReservation = true;
                                break;
                            }
                            continue;
                        }
                    }

                    // Vérifier si l'ID de formation est dans le tableau
                    if (is_array($trainingIds)) {
                        foreach ($trainingIds as $id) {
                            if (is_numeric($id) && (int)$id == $formationId) {
                                $hasReservation = true;
                                break 2; // Sortir des deux boucles
                            }
                        }
                    } elseif (is_numeric($trainingIds) && (int)$trainingIds == $formationId) {
                        $hasReservation = true;
                        break;
                    }
                }
            }
        }

        return response()->json([
            'has_reservation' => $hasReservation,
            'user_id' => $userId,
            'formation_id' => $formationId
        ]);

    } catch (\Exception $e) {
        Log::error("Erreur lors de la vérification de réservation: " . $e->getMessage());
        return response()->json([
            'has_reservation' => false,
            'error' => 'Erreur lors de la vérification'
        ], 500);
    }
}


    public function cancelReservation(Request $request)
    {
        Log::info('==== DÉBUT cancelReservation ====');
        Log::info('Données de la requête: ' . print_r($request->all(), true));

        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            Log::warning('Tentative de suppression sans authentification');
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour supprimer une réservation'
            ], 401);
        }

        $reservationId = $request->input('reservation_id');

        if (empty($reservationId)) {
            Log::error('ID de réservation manquant');
            return response()->json([
                'success' => false,
                'message' => 'ID de réservation manquant'
            ], 400);
        }

        try {
            // Rechercher la réservation
            $reservation = Reservation::find($reservationId);

            if (!$reservation) {
                Log::warning("Réservation non trouvée: {$reservationId}");
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation non trouvée'
                ], 404);
            }

            // Supprimer la réservation
            $reservation->delete();

            Log::info("Réservation supprimée avec succès - ID: {$reservationId}");

            return response()->json([
                'success' => true,
                'message' => 'Réservation supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error("Exception lors de la suppression: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la réservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour le statut d'une réservation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request) {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Vous devez être connecté pour mettre à jour une réservation');
        }

        // Récupérer les données de la requête
        $reservationId = $request->input('reservation_id');
        $newStatus = $request->input('status');

        Log::info("Tentative de mise à jour du statut de la réservation ID: {$reservationId} vers statut: {$newStatus}");

        if (!$reservationId || !in_array($newStatus, [0, 1])) {
            Log::error("Données invalides: reservation_id={$reservationId}, status={$newStatus}");
            return redirect()->back()->with('error', 'Données de requête invalides');
        }

        try {
            // Trouver la réservation avec l'utilisateur associé
            $reservation = Reservation::with('user')->find($reservationId);

            if (!$reservation) {
                Log::error("Réservation non trouvée: {$reservationId}");
                return redirect()->back()->with('error', 'Réservation non trouvée');
            }

            // Vérifier si l'utilisateur associé existe
            if (!$reservation->user) {
                Log::error("Aucun utilisateur associé à la réservation ID: {$reservationId}");
                return redirect()->back()->with('error', 'Aucun utilisateur associé à cette réservation');
            }

            // Vérifier si l'email de l'utilisateur est défini
            if (empty($reservation->user->email)) {
                Log::error("Email utilisateur non défini pour la réservation ID: {$reservationId}, user ID: {$reservation->user_id}");
                return redirect()->back()->with('error', 'Email utilisateur non défini');
            }

            Log::info("Email de l'utilisateur: " . $reservation->user->email);

            // Sauvegarder l'ancien statut pour vérifier s'il y a eu un changement
            $oldStatus = $reservation->status;

            // Récupérer le panier associé à la réservation
            $cart = Cart::find($reservation->cart_id);

            // Si le statut passe à 1 (payé)
            if ($newStatus == 1 && $oldStatus != $newStatus) {
                // Définir la date de paiement
                $reservation->payment_date = now();
                Log::info("Date de paiement définie: " . $reservation->payment_date);

                // Mettre à jour le statut de la réservation
                $reservation->status = $newStatus;
                $reservation->save();

                // Vider les training_ids du panier
                if ($cart) {
                    $cart->training_ids = [];
                    $cart->save();
                    Log::info("Panier vidé (ID: {$cart->id}) après confirmation du paiement");
                }
               

                // Envoyer l'email de confirmation
                try {
                    Log::info("Tentative d'envoi d'email à: " . $reservation->user->email);

                    // Calculer le prix total à partir des IDs stockés
                    $totalPrice = 0;

                    if (!empty($reservation->training_data)) {
                        $trainingDetails = Training::whereIn('id', $reservation->training_data)->get();

                        foreach ($trainingDetails as $training) {
                            $price = $training->price;
                            $discount = $training->discount ?? 0;

                            if ($discount > 0) {
                                $totalPrice += $price - ($price * $discount / 100);
                            } else {
                                $totalPrice += $price;
                            }
                        }
                    }

                    // Envoyer l'email de confirmation avec le total
                    Mail::to($reservation->user->email)->send(new \App\Mail\ReservationConfirmationMail($reservation, $totalPrice));

                    Log::info("Email envoyé avec succès");

                    return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès et email de confirmation envoyé.');
                } catch (\Exception $emailError) {
                    // En cas d'erreur d'envoi d'email, enregistrer l'erreur mais continuer
                    Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $emailError->getMessage());
                    Log::error($emailError->getTraceAsString());
                    return redirect()->back()->with('warning', 'Statut de la réservation mis à jour avec succès, mais l\'email de confirmation n\'a pas pu être envoyé: ' . $emailError->getMessage());
                }
            } else {
                // Mettre à jour le statut de la réservation
                $reservation->status = $newStatus;

                // Réinitialiser la date de paiement si le statut n'est pas payé
                if ($newStatus != 1) {
                    $reservation->payment_date = null;
                }

                $reservation->save();

                Log::info("Réservation mise à jour avec succès, ancien statut: {$oldStatus}, nouveau statut: {$newStatus}");
                return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès');
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du statut de réservation: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }



    /**
     * Télécharger la facture de la réservation au format PDF
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice(Reservation $reservation)
    {
        // Génération du PDF
        $pdf = $reservation->generateInvoicePdf();

        // Construction du nom de fichier
        $filename = 'facture_reservation_' . $reservation->id . '_' . date('Y-m-d') . '.pdf';

        // Retourne le PDF en téléchargement
        return $pdf->download($filename);
    }



    public function checkNewCartItems()
    {
        if (!Auth::check()) {
            return response()->json([
                'hasConfirmedReservation' => false
            ]);
        }

        $userId = Auth::id();

        // Vérifier si l'utilisateur a une réservation confirmée (status = 1)
        $confirmedReservation = Reservation::where('user_id', $userId)
                                    ->where('status', 1)
                                    ->orderBy('created_at', 'desc')
                                    ->first();

        // Si l'utilisateur a une réservation confirmée
        if ($confirmedReservation) {
            // Vérifier si le panier contient des formations
            $cart = Cart::where('user_id', $userId)->first();

            if ($cart && !empty($cart->training_ids)) {
                return response()->json([
                    'hasConfirmedReservation' => true,
                    'shouldCreateNewReservation' => true
                ]);
            }
        }
        return response()->json([
            'hasConfirmedReservation' => false,
            'shouldCreateNewReservation' => false
        ]);
    }

public function checkCompleteReservationStatus()
{
    $userId = Auth::id();

    if (!$userId) {
        return response()->json([
            'hasItemsInCart' => false,
            'hasReservation' => false,
            'reservation_id' => null,
            'buttonState' => 'login',
            'hasPendingReservation' => false,
            'hasConfirmedReservation' => false,
            'shouldCreateNewReservation' => false
        ]);
    }

    // Récupérer le panier actuel
    $cart = Cart::where('user_id', $userId)->first();
    $hasItemsInCart = $cart && !empty($cart->training_ids);

    // Vérifier s'il y a une réservation confirmée (status = 1)
    $confirmedReservation = Reservation::where('user_id', $userId)
                                ->where('status', 1)
                                ->orderBy('created_at', 'desc')
                                ->first();

    // Vérifier s'il y a une réservation en attente (status = 0)
    $pendingReservation = Reservation::where('user_id', $userId)
                                ->where('status', 0)
                                ->orderBy('created_at', 'desc')
                                ->first();

    // Construire une réponse complète et déterministe
    $response = [
        'hasItemsInCart' => $hasItemsInCart,
        'hasConfirmedReservation' => $confirmedReservation !== null,
        'hasPendingReservation' => $pendingReservation !== null,
        'hasReservation' => false,
        'reservation_id' => null,
        'shouldCreateNewReservation' => false,
        'buttonState' => 'none' // none, reserve, viewReservations
    ];

    // Logique de priorité pour déterminer l'état des boutons
    if ($pendingReservation) {
        // PRIORITÉ 1: Réservation en attente - toujours afficher "Voir mes réservations"
        $response['hasReservation'] = true;
        $response['reservation_id'] = $pendingReservation->id;
        $response['buttonState'] = 'viewReservations';
    }
    elseif ($hasItemsInCart && $confirmedReservation) {
        // PRIORITÉ 2: Articles dans le panier + réservation confirmée = nouvelle réservation possible
        $response['shouldCreateNewReservation'] = true;
        $response['buttonState'] = 'reserve';
    }
    elseif ($hasItemsInCart && !$confirmedReservation) {
        // PRIORITÉ 3: Articles dans le panier mais pas de réservation = bouton réserver
        $response['buttonState'] = 'reserve';
    }
    elseif (!$hasItemsInCart && $confirmedReservation) {
        // PRIORITÉ 4: Pas d'articles mais réservation confirmée = voir réservations
        $response['hasReservation'] = true;
        $response['reservation_id'] = $confirmedReservation->id;
        $response['buttonState'] = 'viewReservations';
    }
    else {
        // PRIORITÉ 5: Pas d'articles et pas de réservation = pas de boutons
        $response['buttonState'] = 'none';
    }

    return response()->json($response);
}

}
