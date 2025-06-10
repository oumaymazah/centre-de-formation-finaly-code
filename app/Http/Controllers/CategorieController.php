<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Training;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function getCategoriesCount()
{
    $count = Category::count();
    return response()->json(['count' => $count]);
}
    public function index()
    {
        $categories = Category::all();
        return view('admin.apps.categorie.categories', compact('categories'));
    }

    public function create()
    {
        return view('admin.apps.categorie.categoriecreate');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Category::create($request->all());
        return redirect()->route('formations');


        // return redirect()->route('categories')->with('success', 'Catégorie ajoutée avec succès.');
    }

    public function show($id)
    {
        $categorie = Category::findOrFail($id);
        return view('admin.apps.categorie.categorieshow', compact('categorie'));
    }

    public function edit($id)
    {
        $categorie = Category::findOrFail($id);
        $formations = Training::all(); // Ajouter la récupération des formations


        return view('admin.apps.categorie.categorieedit', compact('categorie','formations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $categorie = Category::findOrFail($id);
        $categorie->update($request->all());

        return redirect()->route('categories')->with('success', 'Catégorie mise à jour avec succès.');
    }



    public function destroy($id)
    {
        try {
            // Trouver la catégorie par son ID ou lever une exception si elle n'existe pas
            $categorie = Category::findOrFail($id);

            // Récupérer le nom de la catégorie
            $categorieName = $categorie->title;

            // Supprimer la catégorie
            $categorie->delete();

            // Retourner un message de succès
            return response()->json(['successMessage' => "La catégorie '{$categorieName}' a été supprimée avec succès!"]);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un message d'erreur
            return response()->json(['errorMessage' => 'Une erreur est survenue.']);
        }
    }


    // Dans votre controller ou dans un nouveau controller API
public function getCategories()
{
    $userIsAdmin = auth()->check() && (
        auth()->user()->hasRole('admin') ||
        auth()->user()->hasRole('super-admin')
    );
    $userIsProf = auth()->check() && auth()->user()->hasRole('professeur');
    $userIsEtudiant = auth()->check() && auth()->user()->hasRole('etudiant');
    $userIsGuest = !auth()->check();

    // Récupérer les catégories avec le même logic que dans votre index
    if ($userIsProf) {
        $categories = Category::withCount(['trainings' => function ($query) {
            $query->where('user_id', auth()->id());
        }])
        ->whereHas('trainings', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->orderBy('name')
        ->get();
    } elseif ($userIsEtudiant || $userIsGuest) {
        $categories = Category::withCount(['trainings' => function ($query) {
            $query->where('status', 1);
        }])
        ->whereHas('trainings', function ($query) {
            $query->where('status', 1);
        })
        ->orderBy('name')
        ->get();
    } else {
        $categories = Category::withCount('trainings')
            ->whereHas('trainings')
            ->orderBy('name')
            ->get();
    }

    // Retourner seulement les champs nécessaires pour optimiser
    return response()->json($categories->map(function ($category) {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'trainings_count' => $category->trainings_count
        ];
    }));
}

}
