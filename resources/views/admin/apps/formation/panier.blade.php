@extends('layouts.admin.master')
@section('content')
@if(auth()->user()->hasRole('etudiant'))
<!DOCTYPE html>
<html lang="fr">
<head>
    <script>
        window.initialReservationData = {!! json_encode($reservationData ?? ['hasReservation' => false, 'reservation_id' => null, 'status' => 0, 'buttonState' => 'reserve']) !!};
        window.cartCount = {{ $cartCount }};
        window.completeFormations = {!! json_encode($completeFormations ?? []) !!};
        window.expiredFormations = {!! json_encode($expiredFormations ?? []) !!};
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="formations-url" content="{{ route('formations') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <title>Votre Panier</title>
    <link rel="stylesheet" href="{{ asset('assets/css/MonCss/panier.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/MonCss/reservation.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
  .complete-formations-wrapper {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.complete-formations-warning {
    width: 75vw; /* Largeur de toute la fenêtre */
    padding: 1rem;
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center; /* ← LIGNE AJOUTÉE pour centrer */
}
/* ALERTE DATE DÉPASSÉE - MÊME STYLE QUE EXPIRED */
.expired-formations-warning {
    width: 75vw; /* Largeur de toute la fenêtre */
    padding: 1rem;
    background-color: #d1d5db;;
    color: #495057;
    border: 1px solid #d0d9e0;  /* ← GRIS FONCÉ */
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center; /* ← LIGNE AJOUTÉE pour centrer */
}

.complete-formations-warning i,
.expired-formations-warning i {
    margin-right: 0.5rem;
}

.formation-status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 0.25rem;
    margin-left: 0.5rem;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

/* BADGE DATE DÉPASSÉE - MÊME COULEUR QUE EXPIRED */
.badge-expired {
    background-color: #d1d5db;;
    color: #374151;
}

/* BADGE POUR DATE DÉPASSÉE (nouveau) */
.badge-date-depassee {
    background-color: #e9ecef;  /* MÊME GRIS QUE EXPIRED */
    color:495057;
}

.formation-full {
    background-color: #fff8f8;
    border-left: 4px solid #f8d7da;
}

.formation-expired {
    background-color: #F5F5F5;  /* GRIS CLAIR */
    border-left: 4px solid #6c757d;  /* GRIS FONCÉ */
}

/* FORMATION DATE DÉPASSÉE - MÊME STYLE QUE EXPIRED */
.formation-date-depassee {
    background-color: #e9ecef;  /* MÊME GRIS CLAIR QUE EXPIRED */
    border-left: 4px solid #6c757d;  /* MÊME GRIS FONCÉ QUE EXPIRED */
}

.complete-formations-warning,
.expired-formations-warning {
    animation: none !important;
    transition: none !important;
    opacity: 1 !important;
    transform: none !important;
}
</style>
</head>
<body>
    <div class="container" style="background-color: white !important;">
        <div class="panier-header">
            @if($cartCount > 0)
                <h1>Panier d'achat</h1>
            @endif
            <div class="complete-formations-wrapper" id="alertsContainer"></div>
            <div class="panier-count">{{ $cartCount }} formation(s)</div>
        </div>
        <div id="app" data-formations-url="{{ route('formations') }}">

        @if($cartCount > 0)
            <div class="panier-content">
                <div class="formations-list">
                    @foreach($panierItems as $item)
                        <div class="formation-item {{ $item->Training->is_complete ? 'formation-full' : '' }} {{ $item->Training->is_expired ? 'formation-expired' : '' }}" data-formation-id="{{ $item->Training->id }}">
                            <div class="formation-image">
                                @if($item->Training->image)
                                    <img src="{{ asset('storage/' . $item->Training->image) }}" alt="{{ $item->Training->title }}">
                                @else
                                    <div class="placeholder-image"></div>
                                @endif
                            </div>

                            <div class="formation-details">
                                <h3 class="formation-title">
                                    {{ $item->Training->title }}
                                    @if($item->Training->is_complete)
                                        <span class="formation-status-badge badge-danger">Complète</span>
                                    @endif
                                    @if($item->Training->is_expired)
                                        <span class="formation-status-badge badge-expired">Date dépassée</span>
                                    @endif
                                </h3>
                                <div class="formation-instructor">
                                    @if($item->Training->user)
                                        {{ $item->Training->user->name }} {{ $item->Training->user->lastname ?? '' }}
                                        @if($item->Training->user->role)
                                            | {{ $item->Training->user->role }}
                                        @endif
                                    @endif
                                </div>
                                <div class="formation-date">
                                    @if($item->Training->start_date)
                                        Date de début: <strong>{{ \Carbon\Carbon::parse($item->Training->start_date)->format('d/m/Y') }}</strong>
                                    @else
                                        <i class="far fa-calendar-alt"></i> Date non définie
                                    @endif
                                </div>
                                @if(isset($item->Training->average_rating) && $item->Training->average_rating > 0)
                                    <div class="formation-rating">
                                        <div class="rating-stars">
                                            @php
                                                $rating = $item->Training->average_rating;
                                                $fullStars = floor($rating);
                                                $hasHalfStar = ($rating - $fullStars) >= 0.25;
                                            @endphp
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $fullStars)
                                                    <i class="fas fa-star"></i>
                                                @elseif($i == $fullStars + 1 && $hasHalfStar)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                            <span class="rating-value">{{ number_format($rating, 1) }}</span>
                                        </div>
                                        <span class="rating-count">({{ $item->Training->total_feedbacks ?? 0 }})</span>
                                    </div>
                                @endif

                                <div class="formation-meta">
                                    @if($item->Training->duration && $item->Training->duration != '00:00' && !empty($item->Training->formatted_duration))
                                        <span><strong>{{ $item->Training->formatted_duration }}</strong> au Total</span>
                                        @if(isset($item->Training->courses) && count($item->Training->courses) > 0)
                                            <span class="dot-separator">•</span>
                                            <span><strong>{{ count($item->Training->courses) }}</strong> cours</span>
                                        @endif
                                    @elseif(isset($item->Training->courses) && count($item->Training->courses) > 0)
                                        <span><strong>{{ count($item->Training->courses) }}</strong> cours</span>
                                    @endif
                                </div>
                            </div>

                            <div class="formation-actions">
                                <div class="action-links">
                                    <a href="javascript:void(0);" class="remove-link" data-formation-id="{{ $item->Training->id }}">Supprimer</a>
                                </div>
                                <div class="formation-price">
                                    @if($item->Training->type == 'gratuite' || $item->Training->price == 0)
                                        <div class="final-price">Gratuite</div>
                                    @elseif($item->Training->discount > 0)
                                        <div class="price-with-discount">
                                            <span class="original-price">{{ number_format($item->Training->price, 3) }} DT</span>
                                            <span class="discount-badge">
                                                -{{ $item->Training->discount }}%
                                            </span>
                                        </div>
                                        <div class="final-price">{{ number_format($item->Training->final_price, 3) }} DT</div>
                                    @else
                                        <div class="final-price">{{ number_format($item->Training->price, 3) }} DT</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="panier-summary usd-style">
                    <div class="summary-title">Total:</div>
                    <div class="total-price">{{ number_format($totalPrice, 2) }} Dt</div>
                    @if($hasDiscount)
                        <div class="original-price">{{ number_format($totalWithoutDiscount, 2) }} Dt</div>
                        <div class="discount-percentage"> -{{ $discountPercentage }}%</div>
                    @endif

                    <div class="reservation-buttons-container" id="reservationButtons">
                        @if($reservationData['hasReservation'])
                            @if($reservationData['buttonState'] === 'viewReservations')
                                <button class="voir-reservations-button" data-action="view-reservations">
                                    Voir mes réservations
                                    <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                @if($reservationData['status'] !== 1)
                                    <button class="annuler-button" data-action="cancel-reservation" data-reservation-id="{{ $reservationData['reservation_id'] }}">
                                        Annuler la réservation
                                    </button>
                                @endif
                            @elseif($reservationData['buttonState'] === 'viewReservationsOnly')
                                <button class="voir-reservations-button" data-action="view-reservations">
                                    Voir mes réservations
                                    <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            @endif
                        @else
                            <button class="reserver-button {{ !empty($completeFormations) || !empty($expiredFormations) ? 'disabled' : '' }}" data-action="create-reservation" id="reserveButton" {{ !empty($completeFormations) || !empty($expiredFormations) ? 'disabled title="Une ou plusieurs formations sont complètes ou ont une date dépassée"' : '' }}>
                                Réserver
                                <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="empty-cart-placeholder"></div>
        @endif
        </div>
    </div>
</body>
</html>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/MonJs/formations/reservation.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/formations/reservation-validation.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/formations/expired-formations-checker.js')}}"></script>
    <script src="{{ asset('assets/js/MonJs/formations/panier.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/cart.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/toast/toast.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/formations/formation-button-layouts.js')}}"></script>
    <script src="{{ asset('assets/js/MonJs/formations.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/formations/formations-cards.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/formations/cart-sync.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@endpush

@php
function formatDuration($duration) {
    if (empty($duration) || $duration === '00:00:00' || $duration === '0:0:0') {
        return '';
    }

    $parts = explode(':', $duration);

    $hours = (int)($parts[0] ?? 0);
    $minutes = (int)($parts[1] ?? 0);
    $seconds = (int)($parts[2] ?? 0);

    $result = [];
    if ($hours > 0) $result[] = $hours . ' h';
    if ($minutes > 0) $result[] = $minutes . ' min';
    if ($seconds > 0) $result[] = $seconds . ' s';

return !empty($result) ? implode(' ', $result) : '';
}
@endphp

</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Récupérer le compteur depuis les données de la vue
        const cartCount = {{ $cartCount ?? 0 }};

        // Mettre à jour immédiatement le localStorage et le badge
        localStorage.setItem('cartCount', cartCount.toString());

        // S'assurer que la fonction updateCartBadge existe
        if (typeof updateCartBadge === 'function') {
            updateCartBadge(cartCount);
        } else {
            // Fallback si la fonction n'est pas définie
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                cartBadge.textContent = cartCount;
                cartBadge.style.display = cartCount > 0 ? 'block' : 'none';
            }
        }
    });
</script>

@endif
