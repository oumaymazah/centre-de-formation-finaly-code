@extends('layouts.admin.master')

@section('title')
Formations
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/select2.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/owlcarousel.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/range-slider.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
@include('admin.apps.formation.partials.styles')
@include('admin.apps.formation.partials.user-roles-script')
@endpush

@section('content')
<div class="container-fluid product-wrapper">
    <div class="product-grid">
        <div class="feature-products">
            {{-- Header avec filtres de statut et bouton nouvelle formation --}}
            @include('admin.apps.formation.partials.header-controls')

            <div class="row">
                <div class="col-md-12">
                    <div class="pro-filter-sec">
                        {{-- Section filtres --}}
                        @include('admin.apps.formation.partials.filters')

                        {{-- Barre de recherche --}}
                        @include('admin.apps.formation.partials.search')
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenu principal selon le statut d'authentification --}}
        <div class="product-wrapper-grid">
            @auth
                @if(auth()->user()->hasRole('etudiant'))
                    @include('admin.apps.formation.partials.authenticated-student', ['formations' => $formations])
                @else
                    {{-- Vue pour admin/professeur --}}
                    @include('admin.apps.formation.partials.admin-view', ['formations' => $formations])
                @endif
            @else
                @include('admin.apps.formation.partials.guest-student', ['formations' => $formations])
            @endauth
        </div>
    </div>
</div>

{{-- Modales communes --}}
@include('admin.apps.formation.partials.modals')

@push('scripts')
@include('admin.apps.formation.partials.scripts')
@endpush
@endsection
