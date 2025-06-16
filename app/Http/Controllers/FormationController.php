<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Feedback;
use App\Models\Reservation;
use App\Models\Training;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class FormationController extends Controller
{

   public function show($id)
    {
        $formation = Training::with([
            'user',
            'category',
            'feedbacks',
            'courses',
            'quizzes' => function($query) {
                $query->where('is_published', true);
            }
        ])->findOrFail($id);

        $formation->total_feedbacks = $formation->feedbacks->count();
        $formation->average_rating = $formation->feedbacks->avg('rating_count');
        $formation->sum_ratings = $formation->feedbacks->sum('rating_count');

        return view('admin.apps.formation.formationshow', compact('formation'));
    }


public function create()
{
    $professeurs = User::whereHas('roles', function($query) {
        $query->where('name', 'professeur');
    })
    ->where('status', 'active') // Ajouter cette condition pour filtrer par statut actif
    ->get(['id', 'name', 'lastname']);

    $categories = Category::all();

    return view('admin.apps.formation.formationcreate', compact('professeurs', 'categories'));
}

public function edit($id)
{
    $formation = Training::findOrFail($id);

    $professeurs = User::whereHas('roles', function($query) {
        $query->where('name', 'professeur');
    })
    ->where('status', 'active') // Ajouter cette condition pour filtrer par statut actif
    ->get(['id', 'name', 'lastname']);

    $categories = Category::all();

    return view('admin.apps.formation.formationedit', compact('formation', 'professeurs', 'categories'));
}


// public function store(Request $request)
// {
//     // Convertir les dates du format DD/MM/YYYY au format YYYY-MM-DD
//     if ($request->has('start_date')) {
//         $request->merge(['start_date' => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d')]);
//     }

//     if ($request->has('end_date')) {
//         $request->merge(['end_date' => Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d')]);
//     }

//     // Convertir également la date de publication si elle existe
//     if ($request->has('publish_date') && $request->publish_date) {
//         try {
//             $request->merge(['publish_date' => Carbon::createFromFormat('d/m/Y', $request->publish_date)->format('Y-m-d')]);
//         } catch (\Exception $e) {
//             return back()->withErrors(['publish_date' => 'Format de date invalide. Utilisez le format JJ/MM/AAAA.'])->withInput();
//         }
//     }

//     try {
//         // Définir les règles de validation
//         $rules = [
//             'title' => 'required|string|max:255',
//             'description' => 'required|string',
//             'type' => 'required|in:payante,gratuite',
//             'category_id' => 'required|exists:categories,id',
//             'user_id' => 'required|exists:users,id',
//             'start_date' => 'required|date',
//             'end_date' => 'required|date|after_or_equal:start_date',
//             'publication_type' => 'required|in:now,later',
//             'total_seats' => 'required|integer|min:1',
//         ];

//         // Ajouter validation conditionnelle pour publish_date
//         if ($request->publication_type === 'later') {
//             $rules['publish_date'] = 'required|date';
//         } else {
//             $rules['publish_date'] = 'nullable|date';
//         }

//         // Modification de la règle d'image pour prendre en compte l'option "keep_image"
//         if ($request->has('keep_image') && $request->has('current_image')) {
//             $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048';
//         } else {
//             $rules['image'] = 'required|image|mimes:jpg,jpeg,png,gif|max:2048';
//         }

//         // Ajout conditionnel de règles pour le prix
//         if ($request->type === 'payante') {
//             $rules['price'] = 'required|numeric|min:0';
//         }

//         // Valider les données
//         $validator = Validator::make($request->all(), $rules);

//         if ($validator->fails()) {
//             return back()->withErrors($validator)->withInput();
//         }

//         $validated = $validator->validated();

//         // Gestion de l'image
//         if ($request->hasFile('image')) {
//             // Assurez-vous que le répertoire existe
//             if (!Storage::disk('public')->exists('formations')) {
//                 Storage::disk('public')->makeDirectory('formations');
//             }

//             $file = $request->file('image');
//             $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
//             $imagePath = $file->storeAs('formations', $fileName, 'public');

//             // Vérifier si l'image a été correctement enregistrée
//             if (!Storage::disk('public')->exists($imagePath)) {
//                 throw new \Exception('Échec de l\'enregistrement de l\'image');
//             }
//         } elseif ($request->has('keep_image') && $request->has('current_image')) {
//             // Utiliser l'image existante
//             $imagePath = $request->current_image;
//         } else {
//             throw new \Exception('Image requise');
//         }

//         // Préparation des données pour la formation
//         $formationData = [
//             'title' => $validated['title'],
//             'description' => $validated['description'],
//             'type' => $validated['type'],
//             'category_id' => $validated['category_id'],
//             'user_id' => $validated['user_id'],
//             'image' => $imagePath,
//             'start_date' => $validated['start_date'],
//             'end_date' => $validated['end_date'],
//             'is_bestseller' => $request->has('is_bestseller') ? 1 : 0,
//             'total_seats' => $validated['total_seats'],
//         ];

//         // Gestion du prix selon le type
//         $formationData['price'] = ($validated['type'] === 'payante') ? $validated['price'] : 0;
//         $formationData['discount'] = $request->has('discount') ? $request->discount : 0;
//         $formationData['final_price'] = ($validated['type'] === 'payante')
//             ? ($formationData['price'] * (1 - $formationData['discount'] / 100))
//             : 0;

//         // Gestion de la publication
//         if ($validated['publication_type'] === 'later') {
//             if (!$request->has('publish_date') || empty($request->publish_date)) {
//                 return back()->withErrors(['publish_date' => 'La date de publication est requise pour une publication ultérieure.'])->withInput();
//             }

//             try {
//                 $publishDate = Carbon::parse($validated['publish_date'])->startOfDay();

//                 // Vérifier si la date est égale ou postérieure à aujourd'hui
//                 if ($publishDate->greaterThanOrEqualTo(Carbon::today())) {
//                     $formationData['publish_date'] = $publishDate->format('Y-m-d');
//                     $formationData['status'] = 0; // Non publiée
//                 } else {
//                     return back()->withErrors(['publish_date' => 'La date de publication doit être égale ou postérieure à aujourd\'hui.'])->withInput();
//                 }
//             } catch (\Exception $e) {
//                 Log::error('Erreur de conversion de date de publication', [
//                     'date' => $request->publish_date,
//                     'error' => $e->getMessage()
//                 ]);
//                 return back()->withErrors(['publish_date' => 'Format de date invalide. Utilisez le format JJ/MM/AAAA.'])->withInput();
//             }
//         } else {
//             $formationData['status'] = 1; // Publiée immédiatement
//             $formationData['publish_date'] = null;
//         }

//         // Log pour débogage
//         Log::info('Données formation avant création', $formationData);

//         DB::beginTransaction();

//         // Vérifiez que le modèle Training inclut ces champs dans $fillable
//         $formation = Training::create($formationData);

//         if (!$formation || !$formation->exists) {
//             throw new \Exception('La création de la formation a échoué');
//         }

//         DB::commit();

//         Log::info('Formation créée avec succès', [
//             'formation_id' => $formation->id,
//             'title' => $formation->title,
//             'user_id' => Auth::id()
//         ]);

//         // Utiliser la même clé 'formation_id' partout pour la cohérence
//         session()->flash('formation_id', $formation->id);
//         session()->flash('from_formation', true);

//         // Vérification si c'est une requête AJAX ou JSON
//         if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
//             return response()->json([
//                 'success' => true,
//                 'message' => 'Formation créée avec succès',
//                 'formation_id' => $formation->id
//             ]);
//         }

//         // Flasher les données pour SweetAlert2 et pour conserver les données du formulaire
//         return redirect()->route('formationcreate')
//             ->with('success', 'Formation créée avec succès')
//             ->with('formation_id', $formation->id)
//             ->with('form_data', $request->except(['image']))
//             ->with('old_data', $formationData);   // Conserver également les données formatées

//     } catch (\Exception $e) {
//         // En cas d'erreur, annuler la transaction
//         if (isset($formation) && DB::transactionLevel() > 0) {
//             DB::rollBack();
//         }

//         Log::error('Erreur lors de la création de la formation', [
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString(),
//             'user_id' => Auth::id(),
//             'request_data' => $request->all()
//         ]);

//         // Supprimer l'image si elle a été uploadée en cas d'échec
//         if (isset($imagePath) && $imagePath && !$request->has('current_image')) {
//             Storage::disk('public')->delete($imagePath);
//         }

//         // Vérification si c'est une requête AJAX ou JSON pour l'erreur aussi
//         if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Erreur lors de la création de la formation: ' . $e->getMessage()
//             ], 500);
//         }

//         return back()->withErrors('Erreur lors de la création de la formation: ' . $e->getMessage())->withInput();
//     }
// }
public function store(Request $request)
{
    try {
        // dd($request);


        // if ($request->has('publish_date') && $request->publish_date) {
        //     try {
        //         $request->merge(['publish_date' => Carbon::createFromFormat('d/m/Y', $request->publish_date)->format('Y-m-d')]);
        //     } catch (\Exception $e) {
        //         return back()->withErrors(['publish_date' => 'Format de date invalide. Utilisez le format JJ/MM/AAAA.'])->withInput();
        //     }
        // }

        // ✅ Validation des données
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:payante,gratuite',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'publication_type' => 'required|in:now,later',
            'total_seats' => 'required|integer|min:1',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];

        if ($request->type === 'payante') {
            $rules['price'] = 'required|numeric|min:0';
        }

        if ($request->publication_type === 'later') {
            $rules['publish_date'] = 'required|date';
        } else {
            $rules['publish_date'] = 'nullable|date';
        }

        if ($request->has('discount')) {
            $rules['discount'] = 'nullable|numeric|min:0|max:100';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // ✅ Gestion de l'image
        if ($request->hasFile('image')) {
            if (!Storage::disk('public')->exists('formations')) {
                Storage::disk('public')->makeDirectory('formations');
            }

            $file = $request->file('image');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('formations', $fileName, 'public');

            if (!Storage::disk('public')->exists($imagePath)) {
                throw new \Exception('Échec de l\'enregistrement de l\'image');
            }
        } else {
            throw new \Exception('Image requise');
        }

        // ✅ Préparer les données
        $formationData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'category_id' => $validated['category_id'],
            'user_id' => $validated['user_id'],
            'image' => $imagePath,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_bestseller' => $request->has('is_bestseller') ? 1 : 0,
            'total_seats' => $validated['total_seats'],
            'price' => $validated['type'] === 'payante' ? $validated['price'] : 0,
            'discount' => $request->discount ?? 0,
        ];

        $formationData['final_price'] = $formationData['type'] === 'payante'
            ? $formationData['price'] * (1 - $formationData['discount'] / 100)
            : 0;

        // ✅ Publication
        if ($validated['publication_type'] === 'later') {
            $publishDate = Carbon::parse($validated['publish_date'])->startOfDay();

            if ($publishDate->greaterThanOrEqualTo(Carbon::today())) {
                $formationData['publish_date'] = $publishDate->format('Y-m-d');
                $formationData['status'] = 0;
            } else {
                return back()->withErrors(['publish_date' => 'La date de publication doit être égale ou postérieure à aujourd\'hui.'])->withInput();
            }
        } else {
            $formationData['status'] = 1;
            $formationData['publish_date'] = null;
        }

        // ✅ Créer la formation
        DB::beginTransaction();
        $formation = Training::create($formationData);

        if (!$formation || !$formation->exists) {
            throw new \Exception('La création de la formation a échoué');
        }

        DB::commit();

        Log::info('Formation créée avec succès', [
            'formation_id' => $formation->id,
            'title' => $formation->title,
            'user_id' => Auth::id()
        ]);

        session()->flash('formation_id', $formation->id);
        session()->flash('from_formation', true);

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Formation créée avec succès',
                'formation_id' => $formation->id
            ]);
        }

        return redirect()->route('formationcreate')
            ->with('success', 'Formation créée avec succès')
            ->with('formation_id', $formation->id)
            ->with('form_data', $request->except(['image']));

    } catch (\Exception $e) {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        Log::error('Erreur lors de la création de la formation', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        if (isset($imagePath) && $imagePath && !$request->has('current_image')) {
            Storage::disk('public')->delete($imagePath);
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la formation: ' . $e->getMessage()
            ], 500);
        }

        return back()->withErrors('Erreur lors de la création de la formation: ' . $e->getMessage())->withInput();
    }
}

public function update(Request $request, $id)
{
    $formation = Training::findOrFail($id);

    // Validation rules
    $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required',
        'start_date' => 'required|date_format:d/m/Y',
        'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
        'type' => 'required|in:payante,gratuite',
        'category_id' => 'required|exists:categories,id',
        'user_id' => 'required|exists:users,id',
        'total_seats' => 'required|integer|min:1',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    // Add conditional rules for price and discount if type is 'payante'
    if ($request->type == 'payante') {
        $rules['price'] = 'required|numeric|min:0';
        $rules['discount'] = 'nullable|numeric|min:0|max:100';
    }

    // Handle publish date validation differently based on publication type
    if ($request->publication_type == 'later') {
        $rules['publish_date'] = 'required|date_format:d/m/Y';
    }

    // Validate the request data
    $validatedData = $request->validate($rules);

    // Format dates for database
    $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $validatedData['start_date'])->format('Y-m-d');
    $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $validatedData['end_date'])->format('Y-m-d');

    // Update formation with validated data
    $formation->title = $validatedData['title'];
    $formation->description = $validatedData['description'];
    $formation->start_date = $startDate;
    $formation->end_date = $endDate;
    $formation->type = $validatedData['type'];
    $formation->category_id = $validatedData['category_id'];
    $formation->user_id = $validatedData['user_id'];
    $formation->total_seats = $validatedData['total_seats'];

    // Handle price and discount if type is 'payante'
    if ($validatedData['type'] == 'payante') {
        $formation->price = $validatedData['price'];
        $formation->discount = $validatedData['discount'] ?? 0;
        $formation->final_price = $request->final_price;
    } else {
        $formation->price = 0;
        $formation->discount = 0;
        $formation->final_price = 0;
    }

    // Handle publication status and date
    if ($request->publication_type == 'now') {
        $formation->status = true;
        $formation->publish_date = now();
    } else {
        // Use createFromFormat to properly convert the date string to a Carbon date
        $formation->status = false;
        if ($request->publish_date) {
            $formation->publish_date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->publish_date)->format('Y-m-d');
        } else {
            $formation->publish_date = null;
        }
    }

    // Handle image upload
    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($formation->image && Storage::disk('public')->exists($formation->image)) {
            Storage::disk('public')->delete($formation->image);
        }

        // Store the new image
        $imagePath = $request->file('image')->store('formations', 'public');
        $formation->image = $imagePath;
    } elseif ($request->delete_image == 1) {
        // Delete the image if requested
        if ($formation->image && Storage::disk('public')->exists($formation->image)) {
            Storage::disk('public')->delete($formation->image);
        }
        $formation->image = null;
    }

    // Save the formation
    $formation->save();

    // Redirect with success message
    return redirect()->route('formations')->with('success', 'Formation mise à jour avec succès.');
}

public function destroy($id)
{
    try {
        // Trouver la formation
        $training = Training::findOrFail($id);

        // Supprimer l'image si elle existe et n'est pas l'image par défaut
        if ($training->image && $training->image !== 'formations/default.jpg' && Storage::disk('public')->exists($training->image)) {
            Storage::disk('public')->delete($training->image);
        }

        // Nettoyer les paniers contenant cette formation AVANT de supprimer la formation
        $this->removeFromAllCarts($id);

        // Supprimer la formation
        $training->delete();

        // Vérifier si c'est une requête AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Formation supprimée avec succès'
            ]);
        }

        return redirect()->route('formations')
            ->with('success', 'Formation supprimée avec succès.');
    } catch (\Exception $e) {
        Log::error('Erreur lors de la suppression de la formation: ' . $e->getMessage());

        // Vérifier si c'est une requête AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression de la formation.'
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'Une erreur est survenue lors de la suppression de la formation.');
    }
}

private function removeFromAllCarts($trainingId)
{
    try {
        // Convertir en string pour assurer la cohérence des types
        $trainingId = (string) $trainingId;

        // Récupérer tous les paniers
        $carts = Cart::all();

        foreach ($carts as $cart) {
            // S'assurer que training_ids est un tableau (peut être NULL)
            $trainingIds = $cart->training_ids ?: [];

            // Vérifier si cet ID de formation existe dans le panier
            if (in_array($trainingId, $trainingIds) || in_array((int)$trainingId, $trainingIds)) {
                // Filtrer l'ID de formation (en gérant à la fois string et int)
                $updatedIds = array_values(array_filter($trainingIds, function($id) use ($trainingId) {
                    return (string)$id !== (string)$trainingId;
                }));

                // Mettre à jour le panier avec les nouveaux IDs
                $cart->training_ids = $updatedIds;
                $cart->save();

                Log::info("Formation ID={$trainingId} retirée du panier ID={$cart->id}");
            }
        }
    } catch (\Exception $e) {
        Log::error("Erreur lors du nettoyage des paniers pour la formation ID={$trainingId}: " . $e->getMessage());
    }
}

    public function checkAvailableSeats(Request $request)
    {
        try {
            // Vérifier si l'utilisateur est connecté
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            $user = Auth::user();
            // Récupérer le panier de l'utilisateur
            $cart = Cart::where('user_id', $user->id)->first();
            // Récupérer les IDs de formation à vérifier
            $trainingIds = $cart ? $cart->training_ids : [];
            // Si aucun panier ou panier vide, chercher dans les réservations confirmées
            if (empty($trainingIds)) {
                // Trouver la dernière réservation confirmée
                $confirmedReservation = Reservation::where('user_id', $user->id)
                                            ->where('status', 1)
                                            ->first();

                // Si une réservation confirmée existe, utiliser ses formations
                if ($confirmedReservation && !empty($confirmedReservation->training_data)) {
                    $trainingIds = array_column($confirmedReservation->training_data, 'id');
                }
            }
            // Si toujours aucune formation à vérifier
            if (empty($trainingIds)) {
                return response()->json([
                    'success' => true,
                    'trainings' => [],
                    'message' => 'Aucune formation à vérifier'
                ]);
            }
            // Récupérer les informations sur les places pour ces formations
            $trainings = Training::whereIn('id', $trainingIds)->get();
            // Transformer les données des formations
            $formattedTrainings = $trainings->map(function ($training) use ($user) {
                // Compter les réservations confirmées pour cette formation
                $confirmedReservations = Reservation::where('status', 1)
                    ->whereJsonContains('training_data', ['id' => $training->id])
                    ->count();
                // Calculer les places restantes
                $remainingSeats = max(0, $training->total_seats - $confirmedReservations);
                return [
                    'id' => $training->id,
                    'title' => $training->title,
                    'total_seats' => (int) $training->total_seats,
                    'available_seats' => (int) $remainingSeats,
                    'occupied_seats' => (int) $confirmedReservations
                ];
            })->toArray();
            return response()->json([
                'success' => true,
                'trainings' => $formattedTrainings
            ]);
        } catch (\Exception $e) {
            // Journaliser l'erreur pour le débogage
            Log::error('Erreur lors de la vérification des places disponibles: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Retourner une réponse d'erreur plus informative
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la vérification des places disponibles.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
//originale
// public function index(Request $request) {
//     // Détermine le rôle de l'utilisateur
//     $userIsAdmin = auth()->user() && (
//         auth()->user()->hasRole('admin') ||
//         auth()->user()->hasRole('super-admin')
//     );
//     $userIsProf = auth()->user() && auth()->user()->hasRole('professeur');

//     // Récupère les catégories avec le nombre de formations approprié selon le rôle
//     if ($userIsProf) {
//         // Pour les professeurs, ne compter que leurs propres formations
//         $categories = Category::withCount(['trainings' => function ($query) {
//             $query->where('user_id', auth()->id());
//         }])->get();
//     } elseif (!$userIsAdmin) {
//         // Pour les étudiants, ne compter que les formations publiées
//         $categories = Category::withCount(['trainings' => function ($query) {
//             $query->where('status', 1); // Uniquement les formations publiées
//         }])->get();
//     } else {
//         // Pour admin, compter toutes les formations
//         $categories = Category::withCount('trainings')->get();
//     }

//     $query = Training::with(['user', 'category', 'feedbacks', 'courses']);

//     // Filtrage par catégorie
//     if ($request->has('category_id') && $request->category_id !== null && $request->category_id !== '') {
//         $query->where('category_id', $request->category_id);
//     }
//     if ($request->has('rating') && $request->rating !== null && $request->rating !== '') {
//     $minRating = (float) $request->rating;

//     // Utiliser un sous-query pour calculer la moyenne des ratings et filtrer
//     $query->whereHas('feedbacks', function($feedbackQuery) use ($minRating) {
//         // Cette sous-requête s'assure qu'il y a au moins un feedback
//     })
//     ->whereRaw('(
//         SELECT ROUND(AVG(rating_count), 1)
//         FROM feedbacks
//         WHERE feedbacks.training_id = trainings.id
//     ) >= ?', [$minRating]);
// }

//     // Filtrage par statut et rôle utilisateur
//     if ($userIsProf) {
//         $query->where('user_id', auth()->id());

//         // Appliquer également le filtre de statut si fourni
//         if (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
//             $query->where('status', $request->status);
//         }
//     }
//     elseif (!$userIsAdmin) {
//         $query->where('status', 1);
//     }
//     elseif (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
//         $query->where('status', $request->status);
//     }

//     // Recherche par terme - Optimisé pour rechercher principalement dans le titre
//     if ($request->filled('search')) {
//         $searchTerm = $request->search;
//         $query->where(function($q) use ($searchTerm) {
//             // Recherche d'abord dans le titre (priorité)
//             $q->where('title', 'LIKE', "%{$searchTerm}%")
//               // Puis dans la description (optionnel - vous pouvez retirer cette ligne si vous voulez uniquement par titre)
//               ->orWhere('description', 'LIKE', "%{$searchTerm}%");
//         });
//     }

//     // Modification: Ajout d'un indicateur de recherche spécifique
//     $searchPerformed = $request->filled('search');
//     $searchTerm = $request->search;

//     $formations = $query->get();

//     // Récupérer toutes les réservations confirmées une seule fois pour optimiser
//     $confirmedReservations = Reservation::where('status', 1)
//         ->whereNotNull('training_data')
//         ->where('training_data', '!=', '')
//         ->where('training_data', '!=', '[]')
//         ->get(['id', 'user_id', 'training_data']);

//     // Compter les places occupées par formation en analysant training_data
//     $occupiedSeatsCount = [];

//     foreach ($confirmedReservations as $reservation) {
//         $trainingData = $reservation->training_data;

//         // Si training_data est une chaîne JSON, la décoder
//         if (is_string($trainingData)) {
//             $trainingData = json_decode($trainingData, true);

//             // Vérifier si le décodage a échoué
//             if (json_last_error() !== JSON_ERROR_NONE) {
//                 continue;
//             }
//         }

//         // Vérifier que training_data est un tableau et n'est pas vide
//         if (!is_array($trainingData) || empty($trainingData)) {
//             continue;
//         }

//         // Parcourir chaque formation dans training_data
//         foreach ($trainingData as $trainingItem) {
//             $trainingId = null;

//             // Extraire l'ID de formation
//             if (!is_array($trainingItem)) {
//                 // Si c'est un entier/string direct, le traiter comme ID
//                 if (is_numeric($trainingItem)) {
//                     $trainingId = (int)$trainingItem;
//                 }
//             } else if (isset($trainingItem['id'])) {
//                 $trainingId = (int)$trainingItem['id'];
//             }

//             // Incrémenter le compteur pour cette formation
//             if ($trainingId) {
//                 if (!isset($occupiedSeatsCount[$trainingId])) {
//                     $occupiedSeatsCount[$trainingId] = 0;
//                 }
//                 $occupiedSeatsCount[$trainingId]++;
//             }
//         }
//     }

//     // Calculer les informations de places et déterminer si les formations sont complètes
//     $formations->each(function ($formation) use ($occupiedSeatsCount) {
//         $formation->final_price = $formation->discount > 0
//             ? $formation->price * (1 - $formation->discount / 100)
//             : $formation->price;
//         $formation->total_feedbacks = $formation->feedbacks->count();
//         $formation->average_rating = $formation->total_feedbacks > 0
//             ? round($formation->feedbacks->sum('rating_count') / $formation->total_feedbacks, 1)
//             : null;
//         $formation->cours_count = $formation->courses->count();

//         // Calculer les places occupées et restantes
//         $occupiedSeats = isset($occupiedSeatsCount[$formation->id]) ? $occupiedSeatsCount[$formation->id] : 0;
//         $totalSeats = (int)($formation->total_seats ?? 0);
//         $remainingSeats = max(0, $totalSeats - $occupiedSeats);

//         $formation->remaining_seats = $remainingSeats;
//         $formation->occupied_seats = $occupiedSeats;

//         // Déterminer si la formation est complète
//         $formation->is_complete = ($remainingSeats === 0 && $totalSeats > 0);
//     });

//     $totalFeedbacks = $formations->sum('total_feedbacks');

//     // Déterminer le titre à afficher (sans inclure les termes de recherche)
//     if ($request->has('category_id') && $request->category_id !== null && $request->category_id !== '') {
//         $title = Category::find($request->category_id)->title;
//     } else {
//         $title = $userIsProf ? 'Mes formations' : 'Toutes les formations';
//     }

//     // Recherche effectuée mais pas affichée dans le titre
//     $searchPerformed = $request->filled('search');

//     // Vérifier le format de réponse attendu
//     $responseFormat = 'html'; // Format par défaut

//     // Explicitement demandé HTML
//     if ($request->has('format') && $request->format === 'html') {
//         $responseFormat = 'html';
//     }
//     // Explicitement demandé JSON via l'en-tête Accept
//     else if ($request->expectsJson() || $request->wantsJson()) {
//         $responseFormat = 'json';
//     }
//     // Explicitement demandé JSON via le paramètre
//     else if ($request->has('format') && $request->format === 'json') {
//         $responseFormat = 'json';
//     }
//     // Vraie requête AJAX
//     else if ($request->ajax() && $request->header('X-Requested-With') === 'XMLHttpRequest') {
//         $responseFormat = 'json';
//     }

//     // Retourner la réponse dans le format approprié
//     if ($responseFormat === 'json') {
//         return response()->json([
//             'formations' => $formations,
//             'title' => $title,
//             'searchPerformed' => $searchPerformed,
//             'searchTerm' => $searchTerm,  // Ajouté pour meilleure analyse côté client
//             'totalFeedbacks' => $totalFeedbacks,
//             'userIsAdmin' => $userIsAdmin,
//             'userIsProf' => $userIsProf
//         ]);
//     }

//     // Si on arrive ici, c'est qu'on veut du HTML
//     return view('admin.apps.formation.formations', compact(
//         'formations',
//         'categories',
//         'title',
//         'searchPerformed',
//         'searchTerm',  // Ajouté pour faciliter l'affichage côté vue
//         'totalFeedbacks',
//         'userIsAdmin',
//         'userIsProf'
//     ));

// }
// public function index(Request $request)
// {
//     $userIsAdmin = auth()->check() && (
//         auth()->user()->hasRole('admin') ||
//         auth()->user()->hasRole('super-admin')
//     );
//     $userIsProf = auth()->check() && auth()->user()->hasRole('professeur');
//     $userIsEtudiant = auth()->check() && auth()->user()->hasRole('etudiant');
//     $userIsGuest = !auth()->check(); // Ajout pour détecter les utilisateurs non connectés

//     // Récupérer les catégories
//     if ($userIsProf) {
//         // Pour les professeurs, ne compter que leurs propres formations
//         $categories = Category::withCount(['trainings' => function ($query) {
//             $query->where('user_id', auth()->id());
//         }])->get();
//     } elseif ($userIsEtudiant || $userIsGuest) {
//         // Pour les étudiants ou utilisateurs non connectés, ne compter que les formations publiées
//         $categories = Category::withCount(['trainings' => function ($query) {
//             $query->where('status', 1); // Uniquement les formations publiées
//         }])->get();
//     } else {
//         // Pour admin, compter toutes les formations
//         $categories = Category::withCount('trainings')->get();
//     }

//     $query = Training::with(['user', 'category', 'feedbacks', 'courses']);

//     // Filtrage par catégorie
//     if ($request->has('category_id') && $request->category_id !== null && $request->category_id !== '') {
//         $query->where('category_id', $request->category_id);
//     }

//     // Filtrage par rating
//     if ($request->has('rating') && $request->rating !== null && $request->rating !== '') {
//         $minRating = (float) $request->rating;
//         $query->whereHas('feedbacks', function($feedbackQuery) use ($minRating) {
//             // Cette sous-requête s'assure qu'il y a au moins un feedback
//         })
//         ->whereRaw('(
//             SELECT ROUND(AVG(rating_count), 1)
//             FROM feedbacks
//             WHERE feedbacks.training_id = trainings.id
//         ) >= ?', [$minRating]);
//     }

//     // Filtrage par statut et rôle utilisateur
//     if ($userIsProf) {
//         $query->where('user_id', auth()->id());
//         if (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
//             $query->where('status', $request->status);
//         }
//     } elseif ($userIsEtudiant || $userIsGuest) {
//         $query->where('status', 1); // Uniquement les formations publiées
//     } elseif (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
//         $query->where('status', $request->status);
//     }

//     // Recherche par terme
//     if ($request->filled('search')) {
//         $searchTerm = $request->search;
//         $query->where(function($q) use ($searchTerm) {
//             $q->where('title', 'LIKE', "%{$searchTerm}%")
//               ->orWhere('description', 'LIKE', "%{$searchTerm}%");
//         });
//     }

//     $searchPerformed = $request->filled('search');
//     $searchTerm = $request->search;

//     $formations = $query->get();

//     // Récupérer les réservations confirmées
//     $confirmedReservations = Reservation::where('status', 1)
//         ->whereNotNull('training_data')
//         ->where('training_data', '!=', '')
//         ->where('training_data', '!=', '[]')
//         ->get(['id', 'user_id', 'training_data']);

//     // Compter les places occupées
//     $occupiedSeatsCount = [];
//     foreach ($confirmedReservations as $reservation) {
//         $trainingData = is_string($reservation->training_data) ? json_decode($reservation->training_data, true) : $reservation->training_data;
//         if (json_last_error() !== JSON_ERROR_NONE || !is_array($trainingData) || empty($trainingData)) {
//             continue;
//         }
//         foreach ($trainingData as $trainingItem) {
//             $trainingId = is_array($trainingItem) && isset($trainingItem['id']) ? (int)$trainingItem['id'] : (is_numeric($trainingItem) ? (int)$trainingItem : null);
//             if ($trainingId) {
//                 $occupiedSeatsCount[$trainingId] = ($occupiedSeatsCount[$trainingId] ?? 0) + 1;
//             }
//         }
//     }

//     // Calculer les informations de places
//     $formations->each(function ($formation) use ($occupiedSeatsCount) {
//         $formation->final_price = $formation->discount > 0
//             ? $formation->price * (1 - $formation->discount / 100)
//             : $formation->price;
//         $formation->total_feedbacks = $formation->feedbacks->count();
//         $formation->average_rating = $formation->total_feedbacks > 0
//             ? round($formation->feedbacks->sum('rating_count') / $formation->total_feedbacks, 1)
//             : null;
//         $formation->cours_count = $formation->courses->count();

//         $occupiedSeats = isset($occupiedSeatsCount[$formation->id]) ? $occupiedSeatsCount[$formation->id] : 0;
//         $totalSeats = (int)($formation->total_seats ?? 0);
//         $remainingSeats = max(0, $totalSeats - $occupiedSeats);

//         $formation->remaining_seats = $remainingSeats;
//         $formation->occupied_seats = $occupiedSeats;
//         $formation->is_complete = ($remainingSeats === 0 && $totalSeats > 0);
//     });

//     $totalFeedbacks = $formations->sum('total_feedbacks');
//     $title = $request->has('category_id') && $request->category_id !== null && $request->category_id !== ''
//         ? Category::find($request->category_id)->title
//         : ($userIsProf ? 'Mes formations' : 'Toutes les formations');

//     $responseFormat = $request->has('format') && $request->format === 'html' ? 'html' :
//         ($request->expectsJson() || $request->wantsJson() || $request->ajax() ? 'json' : 'html');

//     if ($responseFormat === 'json') {
//         return response()->json([
//             'formations' => $formations,
//             'title' => $title,
//             'searchPerformed' => $searchPerformed,
//             'searchTerm' => $searchTerm,
//             'totalFeedbacks' => $totalFeedbacks,
//             'userIsAdmin' => $userIsAdmin,
//             'userIsProf' => $userIsProf,
//             'userIsEtudiant' => $userIsEtudiant,
//             'userIsGuest' => $userIsGuest
//         ]);
//     }

//     return view('admin.apps.formation.formations', compact(
//         'formations',
//         'categories',
//         'title',
//         'searchPerformed',
//         'searchTerm',
//         'totalFeedbacks',
//         'userIsAdmin',
//         'userIsProf',
//         'userIsEtudiant',
//         'userIsGuest'
//     ));
// }

// public function index(Request $request)
// {
//     $userIsAdmin = auth()->check() && (
//         auth()->user()->hasRole('admin') ||
//         auth()->user()->hasRole('super-admin')
//     );
//     $userIsProf = auth()->check() && auth()->user()->hasRole('professeur');
//     $userIsEtudiant = auth()->check() && auth()->user()->hasRole('etudiant');
//     $userIsGuest = !auth()->check();

//     // Récupérer les catégories
//     if ($userIsProf) {
//         $categories = Category::withCount(['trainings' => function ($query) {
//             $query->where('user_id', auth()->id());
//         }])->get();
//     } elseif ($userIsEtudiant || $userIsGuest) {
//         $categories = Category::withCount(['trainings' => function ($query) {
//             $query->where('status', 1);
//         }])->get();
//     } else {
//         $categories = Category::withCount('trainings')->get();
//     }

//     $query = Training::with(['user', 'category', 'feedbacks', 'courses']);

//     // Filtrage par catégorie (via category_title)
//     if ($request->has('category_title') && $request->category_title !== null && $request->category_title !== '') {
//         $category = Category::where('title', urldecode($request->category_title))->first();
//         if ($category) {
//             $query->where('category_id', $category->id);
//         }
//     }

//     // Filtrage par rating
//     if ($request->has('rating') && $request->rating !== null && $request->rating !== '') {
//         $minRating = (float) $request->rating;
//         $query->whereHas('feedbacks', function($feedbackQuery) use ($minRating) {
//             // Cette sous-requête s'assure qu'il y a au moins un feedback
//         })
//         ->whereRaw('(
//             SELECT ROUND(AVG(rating_count), 1)
//             FROM feedbacks
//             WHERE feedbacks.training_id = trainings.id
//         ) >= ?', [$minRating]);
//     }

//     // Filtrage par statut et rôle utilisateur
//     if ($userIsProf) {
//         $query->where('user_id', auth()->id());
//         if (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
//             $query->where('status', $request->status);
//         }
//     } elseif ($userIsEtudiant || $userIsGuest) {
//         $query->where('status', 1);
//     } elseif (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
//         $query->where('status', $request->status);
//     }

//     // Recherche par terme
//     if ($request->filled('search')) {
//         $searchTerm = $request->search;
//         $query->where(function($q) use ($searchTerm) {
//             $q->where('title', 'LIKE', "%{$searchTerm}%")
//               ->orWhere('description', 'LIKE', "%{$searchTerm}%");
//         });
//     }

//     $searchPerformed = $request->filled('search');
//     $searchTerm = $request->search;

//     $formations = $query->get();

//     // Récupérer les réservations confirmées
//     $confirmedReservations = Reservation::where('status', 1)
//         ->whereNotNull('training_data')
//         ->where('training_data', '!=', '')
//         ->where('training_data', '!=', '[]')
//         ->get(['id', 'user_id', 'training_data']);

//     // Compter les places occupées
//     $occupiedSeatsCount = [];
//     foreach ($confirmedReservations as $reservation) {
//         $trainingData = is_string($reservation->training_data) ? json_decode($reservation->training_data, true) : $reservation->training_data;
//         if (json_last_error() !== JSON_ERROR_NONE || !is_array($trainingData) || empty($trainingData)) {
//             continue;
//         }
//         foreach ($trainingData as $trainingItem) {
//             $trainingId = is_array($trainingItem) && isset($trainingItem['id']) ? (int)$trainingItem['id'] : (is_numeric($trainingItem) ? (int)$trainingItem : null);
//             if ($trainingId) {
//                 $occupiedSeatsCount[$trainingId] = ($occupiedSeatsCount[$trainingId] ?? 0) + 1;
//             }
//         }
//     }

//     // Calculer les informations de places
//     $formations->each(function ($formation) use ($occupiedSeatsCount) {
//         $formation->final_price = $formation->discount > 0
//             ? $formation->price * (1 - $formation->discount / 100)
//             : $formation->price;
//         $formation->total_feedbacks = $formation->feedbacks->count();
//         $formation->average_rating = $formation->total_feedbacks > 0
//             ? round($formation->feedbacks->sum('rating_count') / $formation->total_feedbacks, 1)
//             : null;
//         $formation->cours_count = $formation->courses->count();

//         $occupiedSeats = isset($occupiedSeatsCount[$formation->id]) ? $occupiedSeatsCount[$formation->id] : 0;
//         $totalSeats = (int)($formation->total_seats ?? 0);
//         $remainingSeats = max(0, $totalSeats - $occupiedSeats);

//         $formation->remaining_seats = $remainingSeats;
//         $formation->occupied_seats = $occupiedSeats;
//         $formation->is_complete = ($remainingSeats === 0 && $totalSeats > 0);
//     });

//     $totalFeedbacks = $formations->sum('total_feedbacks');
//     $title = $request->has('category_title') && $request->category_title !== null && $request->category_title !== ''
//         ? urldecode($request->category_title)
//         : ($userIsProf ? 'Mes formations' : 'Toutes les formations');

//     $responseFormat = $request->has('format') && $request->format === 'html' ? 'html' :
//         ($request->expectsJson() || $request->wantsJson() || $request->ajax() ? 'json' : 'html');

//     if ($responseFormat === 'json') {
//         return response()->json([
//             'formations' => $formations,
//             'categories' => $categories, // Inclure les catégories dans la réponse JSON
//             'title' => $title,
//             'searchPerformed' => $searchPerformed,
//             'searchTerm' => $searchTerm,
//             'totalFeedbacks' => $totalFeedbacks,
//             'userIsAdmin' => $userIsAdmin,
//             'userIsProf' => $userIsProf,
//             'userIsEtudiant' => $userIsEtudiant,
//             'userIsGuest' => $userIsGuest
//         ]);
//     }

//     return view('admin.apps.formation.formations', compact(
//         'formations',
//         'categories',
//         'title',
//         'searchPerformed',
//         'searchTerm',
//         'totalFeedbacks',
//         'userIsAdmin',
//         'userIsProf',
//         'userIsEtudiant',
//         'userIsGuest'
//     ));
// }
public function index(Request $request)
{
    $userIsAdmin = auth()->check() && (
        auth()->user()->hasRole('admin') ||
        auth()->user()->hasRole('super-admin')
    );
    $userIsProf = auth()->check() && auth()->user()->hasRole('professeur');
    $userIsEtudiant = auth()->check() && auth()->user()->hasRole('etudiant');
    $userIsGuest = !auth()->check();

    // Récupérer les catégories
    if ($userIsProf) {
        $categories = Category::withCount(['trainings' => function ($query) {
            $query->where('user_id', auth()->id());
        }])->get();
    } elseif ($userIsEtudiant || $userIsGuest) {
        $categories = Category::withCount(['trainings' => function ($query) {
            $query->where('status', 1);
        }])->get();
    } else {
        $categories = Category::withCount('trainings')->get();
    }

    $query = Training::with(['user', 'category', 'feedbacks', 'courses']);

    // Variable pour stocker la catégorie sélectionnée
    $selectedCategory = null;

    // Filtrage par catégorie (via category_title)
    if ($request->has('category_title') && $request->category_title !== null && $request->category_title !== '') {
        // $categoryTitle = urldecode($request->category_title);
            $categoryTitle = $request->get('category_title');

        $selectedCategory = Category::where('title', $categoryTitle)->first();
        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory->id);
        }
    }

    // Filtrage par catégorie (via category_id - pour compatibilité avec l'ancien système)
    if ($request->has('category_id') && $request->category_id !== null && $request->category_id !== '') {
        $query->where('category_id', $request->category_id);
        if (!$selectedCategory) {
            $selectedCategory = Category::find($request->category_id);
        }
    }

    // Filtrage par rating
    if ($request->has('rating') && $request->rating !== null && $request->rating !== '') {
        $minRating = (float) $request->rating;
        $query->whereHas('feedbacks', function($feedbackQuery) use ($minRating) {
            // Cette sous-requête s'assure qu'il y a au moins un feedback
        })
        ->whereRaw('(
            SELECT ROUND(AVG(rating_count), 1)
            FROM feedbacks
            WHERE feedbacks.training_id = trainings.id
        ) >= ?', [$minRating]);
    }

    // Filtrage par statut et rôle utilisateur
    if ($userIsProf) {
        $query->where('user_id', auth()->id());
        if (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }
    } elseif ($userIsEtudiant || $userIsGuest) {
        $query->where('status', 1);
    } elseif (!$request->has('status_all') && $request->has('status') && $request->status !== null && $request->status !== '') {
        $query->where('status', $request->status);
    }

    // Recherche par terme
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%");
        });
    }

    $searchPerformed = $request->filled('search');
    $searchTerm = $request->search;

    $formations = $query->get();

    // Récupérer les réservations confirmées
    $confirmedReservations = Reservation::where('status', 1)
        ->whereNotNull('training_data')
        ->where('training_data', '!=', '')
        ->where('training_data', '!=', '[]')
        ->get(['id', 'user_id', 'training_data']);

    // Compter les places occupées
    $occupiedSeatsCount = [];
    foreach ($confirmedReservations as $reservation) {
        $trainingData = is_string($reservation->training_data) ? json_decode($reservation->training_data, true) : $reservation->training_data;
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($trainingData) || empty($trainingData)) {
            continue;
        }
        foreach ($trainingData as $trainingItem) {
            $trainingId = is_array($trainingItem) && isset($trainingItem['id']) ? (int)$trainingItem['id'] : (is_numeric($trainingItem) ? (int)$trainingItem : null);
            if ($trainingId) {
                $occupiedSeatsCount[$trainingId] = ($occupiedSeatsCount[$trainingId] ?? 0) + 1;
            }
        }
    }

    // Calculer les informations de places
    $formations->each(function ($formation) use ($occupiedSeatsCount) {
        $formation->final_price = $formation->discount > 0
            ? $formation->price * (1 - $formation->discount / 100)
            : $formation->price;
        $formation->total_feedbacks = $formation->feedbacks->count();
        $formation->average_rating = $formation->total_feedbacks > 0
            ? round($formation->feedbacks->sum('rating_count') / $formation->total_feedbacks, 1)
            : null;
        $formation->cours_count = $formation->courses->count();

        $occupiedSeats = isset($occupiedSeatsCount[$formation->id]) ? $occupiedSeatsCount[$formation->id] : 0;
        $totalSeats = (int)($formation->total_seats ?? 0);
        $remainingSeats = max(0, $totalSeats - $occupiedSeats);

        $formation->remaining_seats = $remainingSeats;
        $formation->occupied_seats = $occupiedSeats;
        $formation->is_complete = ($remainingSeats === 0 && $totalSeats > 0);
    });

    $totalFeedbacks = $formations->sum('total_feedbacks');

    // Déterminer le titre basé sur la catégorie sélectionnée
    if ($selectedCategory) {
        $title = $selectedCategory->title;
    } else {
        $title = $userIsProf ? 'Mes formations' : 'Toutes les formations';
    }

    $responseFormat = $request->has('format') && $request->format === 'html' ? 'html' :
        ($request->expectsJson() || $request->wantsJson() || $request->ajax() ? 'json' : 'html');

    if ($responseFormat === 'json') {
        return response()->json([
            'formations' => $formations,
            'categories' => $categories,
            'title' => $title,
            'searchPerformed' => $searchPerformed,
            'searchTerm' => $searchTerm,
            'totalFeedbacks' => $totalFeedbacks,
            'userIsAdmin' => $userIsAdmin,
            'userIsProf' => $userIsProf,
            'userIsEtudiant' => $userIsEtudiant,
            'userIsGuest' => $userIsGuest,
            'selectedCategory' => $selectedCategory
        ]);
    }

    return view('admin.apps.formation.formations', compact(
        'formations',
        'categories',
        'title',
        'searchPerformed',
        'searchTerm',
        'totalFeedbacks',
        'userIsAdmin',
        'userIsProf',
        'userIsEtudiant',
        'userIsGuest',
        'selectedCategory'
    ));
}
/**
 * Vérifie l'état d'une formation pour un utilisateur donné
 * @param Request $request
 * @param int $formationId
 * @return \Illuminate\Http\JsonResponse
 */
public function checkFormationStatus(Request $request, $formationId)
{
    try {
        // Trouver la formation
        $formation = Training::findOrFail($formationId);

        // Vérifier si l'utilisateur est connecté
        $user = Auth::user();
        $isAuthenticated = Auth::check();
        $isStudent = $isAuthenticated && $user->hasRole('etudiant');

        // 1. Vérifier si la formation est dans le panier
        $inCart = false;
        if ($isAuthenticated) {
            $cart = Cart::where('user_id', $user->id)->first();
            $inCart = $cart && in_array((string)$formationId, $cart->training_ids ?? []);
        }

        // 2. Vérifier si la formation est complète
        $confirmedReservations = Reservation::where('status', 1)
            ->whereJsonContains('training_data', ['id' => (int)$formationId])
            ->count();
        $remainingSeats = max(0, $formation->total_seats - $confirmedReservations);
        $isComplete = ($remainingSeats === 0 && $formation->total_seats > 0);

        // 3. Vérifier si la formation est réservée avec status=1
        $isReserved = false;
        if ($isAuthenticated) {
            $isReserved = Reservation::where('user_id', $user->id)
                ->where('status', 1)
                ->whereJsonContains('training_data', ['id' => (int)$formationId])
                ->exists();
        }

        // 4. Vérifier si la date de fin est dépassée
        $isDateExpired = Carbon::parse($formation->end_date)->isPast();

        // Construire la réponse
        return response()->json([
            'success' => true,
            'formation_id' => $formationId,
            'in_cart' => $inCart,
            'is_complete' => $isComplete,
            'is_reserved' => $isReserved,
            'is_date_expired' => $isDateExpired,
            'remaining_seats' => $remainingSeats,
            'total_seats' => (int)$formation->total_seats,
        ]);
    } catch (\Exception $e) {
        Log::error('Erreur lors de la vérification de l\'état de la formation: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la vérification de l\'état de la formation.',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
}
