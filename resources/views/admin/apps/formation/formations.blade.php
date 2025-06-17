@extends('layouts.admin.master')

@section('title')
Formations | ELS-Centre de Formation en Ligne
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/select2.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/owlcarousel.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/range-slider.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<style>
  .page-wrapper.compact-wrapper .page-body-wrapper .page-body {
    padding-top: 30px;
    margin-left: 290px;
   }

    /* Card styling */
    .card {
      margin-bottom: 30px;
      border: 1px solid #ADD8E633;
      -webkit-transition: all 0.3s ease;
      transition: all 0.3s ease;
      letter-spacing: 0.5px;
      border-radius: 0;
      width: 90% !important;
      /* height: 90% !important; */
      background-color: #fff;
    }

    /* Rating and filter styles */
    .rating-label {
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }
    .rating-label .fa-star,
    .rating-label .fa-star-half-alt,
    .rating-label .far.fa-star {
        font-size: 12px;
        margin-right: 1px;
        color: #FFC107;
    }
    .rating-label .far.fa-star,
    .rating-label .fa-star-half-alt {
        color: #FFC107;
    }
    .product-filter .checkbox-animated label {
        cursor: pointer;
        padding: 6px 0;
        border-bottom: 1px solid #f0f0f0;
        align-items: center;
        font-size: 14px;
    }
    .product-filter .checkbox-animated label:last-child {
        border-bottom: none;
    }
    .product-filter .checkbox-animated label:hover {
        background-color: #f8f9fa;
        padding-left: 5px;
        transition: all 0.2s ease;
    }
    .categories-toggle, .ratings-toggle {
        cursor: pointer;
        padding: 10px;
        margin-bottom: 0;
    }
    .categories-content, .ratings-content {
        transition: all 0.3s ease;
    }
    .search-indicator {
        font-size: 0.8em;
        color: #0d6efd;
        margin-left: 10px;
    }
    .search-clear-icon {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .product-search .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .product-search .form-group {
        position: relative;
    }

    /* Ensure page content starts below the header */
    .product-wrapper {
        margin-top: 80px;
    }


</style>
@endpush

<script>
    let userRoles = [];
    @if(auth()->check())
        try {
            userRoles = @json(auth()->user()->roles->pluck('name')->toArray());
            console.log("Rôles chargés:", userRoles);
        } catch(e) {
            console.error("Erreur lors du chargement des rôles:", e);
        }
    @else
        userRoles = ['guest'];
        console.log("Utilisateur non connecté, rôle défini comme 'guest'");
    @endif
</script>

@section('content')
<div class="container-fluid product-wrapper">
    <div class="product-grid">
        <div class="feature-products">
            <div class="row m-b-10">
                <div class="col-md-3 col-sm-4">
                    <div class="d-none-productlist filter-toggle">
                        <h6 class="mb-0">
                            Filtres<span class="ms-2"><i class="toggle-data" data-feather="chevron-down"></i></span>
                        </h6>
                    </div>
                </div>
                <div class="col-md-9 col-sm-8 text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('professeur')))
                        <div class="select2-drpdwn-product select-options me-3" style="margin-top: 10px;">
                            <select class="form-control btn-square status-filter" name="status">
                                <option value="">Tous</option>
                                <option value="1" {{ request()->status == '1' ? 'selected' : '' }}>Publiée</option>
                                <option value="0" {{ request()->status == '0' ? 'selected' : '' }}>Non publiée</option>
                            </select>
                        </div>
                        @endif
                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')))
                        <div class="btn-group">
                            <a href="{{ route('formationcreate') }}" class="btn btn-primary btn-sm d-flex align-items-center">
                                <i data-feather="plus-square" class="me-2"></i> Nouvelle Formation
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="pro-filter-sec">
                        <div class="product-sidebar">
                            <div class="filter-section">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0 f-w-600">
                                            Filtres<span class="pull-right"><i class="fa fa-chevron-down toggle-data"></i></span>
                                        </h6>
                                    </div>
                                    <div class="left-filter">
                                        <div class="card-body filter-cards-view animate-chk">
                                            <!-- Filtrage par Catégories -->
                                            <div class="product-filter">
                                                <h6 class="f-w-600 categories-toggle">Catégories
                                                    <span class="pull-right"><i class="fa fa-chevron-down"></i></span>
                                                </h6>
                                                <div class="checkbox-animated mt-0 categories-content" style="display: none;">
                                                    <label class="d-flex align-items-center" for="category-all">
                                                        <input class="radio_animated me-2"
                                                               id="category-all"
                                                               type="radio"
                                                               name="category_filter"
                                                               value=""
                                                               {{ (!request()->has('category_id') && !request()->has('category_title')) ||
                                                                  request()->category_id === null ||
                                                                  request()->category_id === '' ? 'checked' : '' }}/>
                                                        Toutes les catégories
                                                    </label>
                                                    @foreach($categories as $category)
                                                        <label class="d-flex align-items-center" for="category-{{ $category->id }}">
                                                            <input class="radio_animated me-2"
                                                                   id="category-{{ $category->id }}"
                                                                   type="radio"
                                                                   name="category_filter"
                                                                   value="{{ $category->id }}"
                                                                   data-category-title="{{ $category->title }}"
                                                                   {{ (request()->category_id == $category->id) ||
                                                                      (request()->has('category_title') &&
                                                                       strtolower(trim(request()->category_title)) === strtolower(trim($category->title)))
                                                                      ? 'checked' : '' }}/>
                                                            {{ $category->title }} ({{ $category->trainings_count }})
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <script>
                                            // Rendre les catégories disponibles globalement pour JavaScript
                                            window.categories = @json($categories->map(function($category) {
                                                return [
                                                    'id' => $category->id,
                                                    'title' => $category->title,
                                                    'trainings_count' => $category->trainings_count
                                                ];
                                            }));
                                            </script>

                                            <!-- Filtrage par Évaluations -->
                                            <div class="product-filter">
                                                <h6 class="f-w-600 ratings-toggle">Évaluations <span class="pull-right"><i class="fa fa-chevron-down"></i></span></h6>
                                                <div class="checkbox-animated mt-0 ratings-content" style="display: none;">
                                                    <label class="d-flex align-items-center" for="rating-all">
                                                        <input class="radio_animated me-2" id="rating-all" type="radio" name="rating_filter" value="" {{ !request()->has('rating') || request()->rating === null || request()->rating === '' ? 'checked' : '' }}/>
                                                        Toutes les évaluations
                                                    </label>
                                                    @foreach([1, 2, 2.5, 3, 3.5, 4, 4.5, 5] as $rating)
                                                        <label class="d-flex align-items-center" for="rating-{{ $rating }}">
                                                            <input class="radio_animated me-2" id="rating-{{ $rating }}" type="radio" name="rating_filter" value="{{ $rating }}" {{ request()->rating == $rating ? 'checked' : '' }}/>
                                                            <span class="rating-label">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= floor($rating))
                                                                        <i class="fa fa-star"></i>
                                                                    @elseif($i == ceil($rating) && $rating != floor($rating))
                                                                        <i class="fa fa-star-half-alt"></i>
                                                                    @else
                                                                        <i class="far fa-star"></i>
                                                                    @endif
                                                                @endfor
                                                                {{ $rating == 5 ? '5' : 'À partir de ' . $rating }}
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="product-search">
                            <form onsubmit="return false;" autocomplete="off">
                                <div class="form-group m-0">
                                    <input class="form-control" type="search"
                                           placeholder="Rechercher..."
                                           data-original-title=""
                                           title=""
                                           id="search-formations"
                                           autocomplete="off" />
                                    <i class="fa fa-search search-clear-icon"></i>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="product-wrapper-grid">
            <div class="row formations-container">
                @forelse($formations as $formation)
                    <div class="col-xl-3 col-sm-6 xl-4 formation-item">
                        <div class="card h-100">
                            <div class="product-box d-flex flex-column h-100">
                                <div class="product-img" style="height: 200px; overflow: hidden; position: relative;">
                                    <img class="img-fluid" src="{{ asset('storage/' . $formation->image) }}" alt="{{ $formation->title }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                    <div class="product-hover">
                                        <ul>
                                            <li>
                                                <a href="javascript:void(0)" onclick="showFormationDetails({{ $formation->id }})" data-bs-toggle="modal" data-bs-target="#formation-modal-{{ $formation->id }}">
                                                    <i class="icon-eye"></i>
                                                </a>
                                            </li>
                                            @if($userIsEtudiant || $userIsGuest)
                                                <li>
                                                    <a href="javascript:void(0)" class="add-to-cart" data-formation-id="{{ $formation->id }}" >
                                                        <i class="icon-shopping-cart"></i>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin') ))
                                                <li>
                                                    <a href="{{ route('formationedit', $formation->id) }}"><i class="icon-pencil"></i></a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" class="delete-formation" data-id="{{ $formation->id }}">
                                                        <i class="icon-trash"></i>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                @include('admin.apps.formation.formation-modal', ['formation' => $formation])
                                <div class="product-details flex-grow-1 d-flex flex-column p-3">
                                    <div class="card-content flex-grow-1">
                                        <a href="{{ route('formationshow', $formation->id) }}">
                                            <h4 class="formation-title" title="{{ $formation->title }}">{{ $formation->title }}</h4>
                                        </a>
                                        <p class="mb-1">Par {{ $formation->user->name }} {{ $formation->user->lastname }}</p>
                                        <div class="rating-wrapper mb-2">
                                            @if($formation->average_rating)
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($formation->average_rating))
                                                        <i class="fa fa-star text-warning"></i>
                                                    @elseif($i == ceil($formation->average_rating) && $formation->average_rating != floor($formation->average_rating))
                                                        <i class="fa fa-star-half-alt text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                                <span>({{ $formation->total_feedbacks }})</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="product-price-container">
                                        <div class="product-price mb-0">
                                            @if($formation->type == 'payante')
                                                @if($formation->discount > 0)
                                                    {{ number_format($formation->final_price, 2) }} Dt
                                                    <del>{{ number_format($formation->price, 2) }} Dt</del>
                                                @else
                                                    {{ number_format($formation->price, 2) }} Dt
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- <div class="col-12">
                        <div class="alert alert-info">
                            Aucune formation disponible.
                        </div>
                    </div> --}}
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
{{-- @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('professeur')))
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette formation ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <form id="deleteFormationForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif --}}


@if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')))
<!-- Modal de confirmation de suppression normale -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-black">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette formation ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <form id="deleteFormationForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non, annuler</button>
                    <button type="submit" class="btn btn-danger">Oui, Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour réservations confirmées seulement -->
<div class="modal fade" id="deleteWithConfirmedReservationsModal" tabindex="-1" role="dialog" aria-labelledby="deleteWithConfirmedReservationsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-black">
                <h5 class="modal-title text-black" id="deleteWithConfirmedReservationsModalLabel">
                    Cette formation a des réservations confirmées
                </h5>
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- <div class="alert alert-danger"> --}}
                    <div class="text-center">

                    <strong>Attention !</strong><br>
                    Des utilisateurs ont déjà réservé cette formation avec un statut confirmé.</br>
                   <br> Voulez-vous supprimer la formation malgré cela ?</br>
                    </div>

                {{-- </div> --}}
            </div>
            <div class="modal-footer">
                <form id="deleteFormationWithConfirmedReservationsForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non, annuler</button>
                    <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteWithCombinedReservationsModal" tabindex="-1" role="dialog" aria-labelledby="deleteWithCombinedReservationsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-black">
                <h5 class="modal-title" id="deleteWithCombinedReservationsModalLabel">
                    Cette formation a des réservations multiples
                </h5>
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <strong>Attention !</strong>
                    <br>Cette formation contient à la fois des réservations confirmées et des réservations en attente.</br>
                    <br>Voulez-vous supprimer la formation malgré cela ?</br>
                </div>
            </div>
            <div class="modal-footer">
                <form id="deleteFormationWithCombinedReservationsForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non, annuler</button>
                    <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal pour réservations en attente seulement -->
<div class="modal fade" id="deleteWithPendingReservationsModal" tabindex="-1" role="dialog" aria-labelledby="deleteWithPendingReservationsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-black">
                <h5 class="modal-title" id="deleteWithPendingReservationsModalLabel">
                    Cette formation a des réservations en attente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">

                {{-- <div class="alert alert-warning"> --}}
                    <strong>Attention !</strong>
                    <br>Cette formation contient des réservations pas encore validées (en attente).</br>
                    <br>Voulez-vous supprimer la formation malgré cela ?</br>
                    <div></div>
                {{-- </div> --}}
            </div>
            <div class="modal-footer">
                <form id="deleteFormationWithPendingReservationsForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non, annuler</button>
                    <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteWithCombinedReservationsModal" tabindex="-1" role="dialog" aria-labelledby="deleteWithCombinedReservationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-black" id="deleteWithCombinedReservationsModalLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    ATTENTION - Formation avec réservations multiples
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-custom">
                    <h6 class="alert-heading">
                        <i class="fa fa-warning me-2"></i>
                        <strong>SUPPRESSION CRITIQUE</strong>
                    </h6>
                    <hr>
                    <p class="mb-3">
                        Cette formation contient <strong>à la fois</strong> des réservations confirmées et des réservations en attente :
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card reservation-card mb-2">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1">
                                        <i class="fa fa-check-circle me-1"></i>
                                        Réservations confirmées
                                    </h6>
                                    <small class="text-muted">Des utilisateurs ont déjà validé leur inscription</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card reservation-card mb-2">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1">
                                        <i class="fa fa-clock me-1"></i>
                                        Réservations en attente
                                    </h6>
                                    <small class="text-muted">Des demandes d'inscription sont en cours de validation</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 p-3 impact-section rounded">
                        <strong class="text-dark">⚠️ Impact de la suppression :</strong>
                        <ul class="mt-2 mb-0 text-dark">
                            <li>Les utilisateurs avec réservations confirmées perdront leur inscription</li>
                            <li>Les demandes en attente seront automatiquement annulées</li>
                            <li>Cette action est <strong>irréversible</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <p class="confirmation-text mb-0">
                        Êtes-vous absolument certain de vouloir supprimer cette formation ?
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>
                    Non, conserver la formation
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCombinedReservations">
                    <i class="fa fa-trash me-1"></i>
                    Oui, supprimer définitivement
                </button>

                <!-- Formulaire pour la suppression avec tokens CSRF -->
                <form id="deleteFormationWithCombinedReservationsForm" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour réservations combinées (confirmées + en attente) -->
{{-- <div class="modal fade" id="deleteWithCombinedReservationsModal" tabindex="-1" role="dialog" aria-labelledby="deleteWithCombinedReservationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-black">
                <h5 class="modal-title text-black" id="deleteWithCombinedReservationsModalLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    ATTENTION - Formation avec réservations multiples
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6 class="alert-heading">
                        <i class="fa fa-warning me-2"></i>
                        <strong>SUPPRESSION CRITIQUE</strong>
                    </h6>
                    <hr>
                    <p class="mb-3">
                        Cette formation contient <strong>à la fois</strong> des réservations confirmées et des réservations en attente :
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-success mb-2">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1 text-success">
                                        <i class="fa fa-check-circle me-1"></i>
                                        Réservations confirmées
                                    </h6>
                                    <small class="text-muted">Des utilisateurs ont déjà validé leur inscription</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-warning mb-2">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-1 text-warning">
                                        <i class="fa fa-clock me-1"></i>
                                        Réservations en attente
                                    </h6>
                                    <small class="text-muted">Des demandes d'inscription sont en cours de validation</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 p-3 bg-light border-left border-danger">
                        <strong class="text-danger">⚠️ Impact de la suppression :</strong>
                        <ul class="mt-2 mb-0">
                            <li>Les utilisateurs avec réservations confirmées perdront leur inscription</li>
                            <li>Les demandes en attente seront automatiquement annulées</li>
                            <li>Cette action est <strong>irréversible</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <p class="fw-bold text-danger mb-0">
                        Êtes-vous absolument certain de vouloir supprimer cette formation ?
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>
                    Non, conserver la formation
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCombinedReservations">
                    <i class="fa fa-trash me-1"></i>
                    Oui, supprimer définitivement
                </button>

                <!-- Formulaire pour la suppression avec tokens CSRF -->
                <form id="deleteFormationWithCombinedReservationsForm" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div> --}}

@endif

 {{-- <style>
    /* Modifier le header du modal pour texte et croix en blanc */
#deleteWithCombinedReservationsModal .modal-header {
    /* background-color: #dc3545; */
    background-color: white!important;

    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

#deleteWithCombinedReservationsModal .modal-title {
    /* color: white !important; */

}

#deleteWithCombinedReservationsModal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
    opacity: 0.8;
}

#deleteWithCombinedReservationsModal .btn-close:hover {
    opacity: 1;
}
        .modal-content {
            border: 1px solid #676c71;
        }
        .modal-header {
            background-color: white;
            border-bottom: 1px solid #dee2e6;
        }
        .alert-custom {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
        }
        .reservation-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
        }
        .impact-section {
            background-color: #f8f9fa;
            border-left: 3px solid #6c757d;
        }
        .confirmation-text {
            color: #495057;
            font-weight: 600;
        }
    </style> --}}
<!-- Footer conditionnel selon l'authentification -->
@if(!auth()->check())
    @php($hideAdminFooter = true)
    <style>
    .footer .container {
        padding-left: 50rem !important; /* Ajustez cette valeur selon vos besoins */
        /* padding-right: -100rem !important; */
    }

    .container {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 2rem !important;
    }

    .content-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 10rem !important; /* ⇨ pousse un peu à droite */
    }
    </style>

    <style>
        .formations-container {
            margin-bottom: 0.6rem;
            padding-bottom: 0.6rem;
        }
    </style>

    <style>
        .footer {
            background: #1f2937;
            color: white;
            padding: 2rem 0 2rem;
            width: 100vw !important;
            margin: 0 !important;
            /* margin-left: -290px !important;
            margin-right: 5px !important; */
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.7;
            color: #1a202c;
            display: none; /* Masquer le footer par défaut */
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 5rem;
            margin: 0 !important;
            padding: 0 5rem !important;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #60a5fa;
        }

        .footer-section p,
        .footer-section a {
            color: #d1d5db;
            text-decoration: none;
            line-height: 1.8;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #60a5fa;
        }

        .footer-bottom {
            border-top: 1px solid #374151;
            padding: 1rem 2rem 0 2rem !important;
            text-align: right;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-bottom-left {
            flex: 1;
            text-align: right;
            min-width: 0;
            padding-right: 2rem;
        }

        .footer-bottom-right {
            flex: 1;
            padding-right: -5rem;
            margin-right: 3rem;
        }

        @media (max-width: 768px) {
            .footer-content {
                padding: 0 1rem;
            }

            .footer-bottom {
                padding: 2rem 1rem 0 1rem;
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .footer-bottom-left,
            .footer-bottom-right {
                text-align: center;
            }
        }
    </style>

@endif

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="{{asset('assets/js/range-slider/ion.rangeSlider.min.js')}}"></script>
<script src="{{asset('assets/js/range-slider/rangeslider-script.js')}}"></script>
<script src="{{asset('assets/js/touchspin/vendors.min.js')}}"></script>
<script src="{{asset('assets/js/touchspin/touchspin.js')}}"></script>
<script src="{{asset('assets/js/touchspin/input-groups.min.js')}}"></script>
<script src="{{asset('assets/js/owlcarousel/owl.carousel.js')}}"></script>
<script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
<script src="{{asset('assets/js/tooltip-init.js')}}"></script>
<script src="{{asset('assets/js/product-tab.js')}}"></script>
<script src="{{asset('assets/js/MonJs/formations/feedback.js')}}"></script>
<script src="{{asset('assets/js/MonJs/formations/formations-cards.js')}}"></script>
<script src="{{asset('assets/js/MonJs/formations/formation-button-layouts.js')}}"></script>
<script src="{{asset('assets/js/MonJs/formations.js')}}"></script>
<script src="{{ asset('assets/js/MonJs/toast/toast.js') }}"></script>
<script src="{{ asset('assets/js/MonJs/formations/panier.js') }}"></script>
<script src="{{ asset('assets/js/MonJs/formations/reservation.js') }}"></script>
<script src="{{ asset('assets/js/MonJs/cart.js') }}"></script>
@endpush
@endsection




