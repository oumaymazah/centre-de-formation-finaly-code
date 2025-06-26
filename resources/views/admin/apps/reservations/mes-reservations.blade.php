

@extends('layouts.admin.master')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/MonCss/mes-reservations2.css') }}">

<style>
    .formation-card {
        cursor: pointer; /* Indique que la carte est cliquable */
        transition: transform 0.2s, box-shadow 0.2s; /* Animation pour un effet de survol */
    }
    .formation-card:hover {
        transform: translateY(-5px); /* Effet de survol */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Ombre au survol */
    }
</style>

<script>
    // Définir les fonctions dans l'espace global pour y accéder depuis n'importe où
    window.synchronizeWithServer = function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) return;

        fetch('/panier/data', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            const count = data.count || 0;
            localStorage.setItem('cartCount', count.toString());
            const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
            badges.forEach(badge => {
                badge.textContent = count.toString();
                badge.style.display = count > 0 ? 'flex' : 'none';
            });

            const fixedBadge = document.getElementById('fixed-cart-badge');
            if (fixedBadge) {
                fixedBadge.textContent = count.toString();
                fixedBadge.style.display = count > 0 ? 'flex' : 'none';
            }

            if (count > 0 && badges.length === 0) {
                window.initializeCartBadges();
            }
        })
        .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
    };

    window.initializeCartBadges = function() {
        const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
        if (cartCount <= 0) return;

        if (!document.getElementById('cart-badge-styles')) {
            const style = document.createElement('style');
            style.id = 'cart-badge-styles';
            style.innerHTML = `
                .cart-badge, .custom-violet-badge {
                    position: absolute;
                    top: -8px;
                    right: -8px;
                    background-color: #2B6ED4;
                    color: white;
                    border-radius: 50%;
                    width: 18px;
                    height: 18px;
                    font-size: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    z-index: 10;
                    opacity: 1 !important;
                    animation: none !important;
                    transition: none !important;
                }
            `;
            document.head.appendChild(style);
        }

        const cartSelectors = [
            '.shopping-cart-icon',
            'svg[data-icon="shopping-cart"]',
            '.cart-icon',
            'a[href*="panier"] svg',
            '.cart-container svg',
            '.cart-link',
            '.panier-icon'
        ];

        const iconSelector = cartSelectors.join(', ');
        const cartIcons = document.querySelectorAll(iconSelector);

        cartIcons.forEach(icon => {
            const container = icon.closest('a, div, button, .cart-container');
            if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
                const badge = document.createElement('span');
                badge.className = 'cart-badge custom-violet-badge';
                badge.textContent = cartCount.toString();
                if (getComputedStyle(container).position === 'static') {
                    container.style.position = 'relative';
                }
                container.appendChild(badge);
            }
        });
    };

    window.updateCartBadgeCount = function(count) {
        localStorage.setItem('cartCount', count.toString());
        const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
        badges.forEach(badge => {
            badge.textContent = count.toString();
            badge.style.display = count > 0 ? 'flex' : 'none';
        });

        const fixedBadge = document.getElementById('fixed-cart-badge');
        if (fixedBadge) {
            fixedBadge.textContent = count.toString();
            fixedBadge.style.display = count > 0 ? 'flex' : 'none';
        }

        if (count > 0 && badges.length === 0) {
            window.initializeCartBadges();
        }
    };

    // Fonction pour gérer le clic sur une carte de formation
    function redirectToTrainingDetail(trainingId) {
        if (!trainingId) {
            console.error('Training ID is undefined');
            return;
        }
        // Utiliser la route Laravel générée dynamiquement
        window.location.href = '{{ route("training.detail", ":id") }}'.replace(':id', trainingId);
    }

    // Fonction pour initialiser les événements de clic sur les cartes
    function initFormationCardClicks() {
        const formationCards = document.querySelectorAll('.formation-card');
        formationCards.forEach(card => {
            if (!card.dataset.clickInitialized) {
                card.dataset.clickInitialized = 'true';
                card.addEventListener('click', function(event) {
                    event.preventDefault(); // Empêche tout comportement par défaut
                    const trainingId = this.dataset.trainingId;
                    if (trainingId) {
                        redirectToTrainingDetail(trainingId);
                    } else {
                        console.error('No training ID found for this card');
                    }
                });
            }
        });
    }

    // Fonction d'initialisation principale
    function initCartSystem() {
        window.initializeCartBadges();
        setTimeout(window.synchronizeWithServer, 100);

        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.addedNodes.length) {
                    window.initializeCartBadges();
                    initFormationCardClicks();
                }
            });
        });

        observer.observe(document.documentElement, {
            childList: true,
            subtree: true
        });

        initFormationCardClicks();
    }

    // Exécuter au chargement de la page
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCartSystem);
    } else {
        initCartSystem();
    }

    // Recharger les badges quand on revient sur la page
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            window.initializeCartBadges();
            window.synchronizeWithServer();
        }
    });

    // Fonction pour gérer les dropdowns d'alerte de paiement
    document.addEventListener('DOMContentLoaded', function() {
        initPaymentAlertDropdowns();
    });

    function initPaymentAlertDropdowns() {
        const paymentAlertHeaders = document.querySelectorAll('.payment-alert-header');
        paymentAlertHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const content = this.nextElementSibling;
                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                    content.classList.remove('open');
                    this.querySelector('.dropdown-icon').classList.remove('rotate');
                } else {
                    content.classList.add('open');
                    content.style.maxHeight = content.scrollHeight + "px";
                    this.querySelector('.dropdown-icon').classList.add('rotate');
                }
            });
        });
    }

    function toggleFormations(reservationId, remainingCount) {
        const hiddenFormations = document.getElementById(`hidden-formations-${reservationId}`);
        const toggleBtn = document.getElementById(`btn-toggle-${reservationId}`);
        const formationsGrid = document.getElementById(`formations-grid-${reservationId}`);
        const reservationCard = formationsGrid.closest('.reservation-card');

        if (hiddenFormations.style.display === 'none' || hiddenFormations.style.display === '') {
            hiddenFormations.style.display = 'contents';
            toggleBtn.innerHTML = '<i class="fas fa-chevron-up me-2"></i>Voir moins';
            reservationCard.style.minHeight = 'auto';
            reservationCard.style.transition = 'all 0.5s ease';
            requestAnimationFrame(() => {
                const cardHeight = reservationCard.scrollHeight;
                reservationCard.style.minHeight = cardHeight + 'px';
            });
        } else {
            hiddenFormations.style.display = 'none';
            toggleBtn.innerHTML = `<i class="fas fa-chevron-down me-2"></i>Voir plus (${remainingCount} formation(s))`;
            reservationCard.style.minHeight = 'auto';
            reservationCard.style.height = 'auto';
            reservationCard.style.transition = 'all 0.5s ease';
            setTimeout(() => {
                reservationCard.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                    inline: 'nearest'
                });
            }, 250);
        }
    }
</script>

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
@endpush

@section('content')
<div class="container my-5">
  @if($reservations->isEmpty())
    <div class="container my-5">
        <div class="empty-reservations-container">
            <div class="empty-reservations-content">
                <div class="cart-icon-wrapper">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h2 class="empty-reservations-title">Aucune réservation trouvée</h2>
                <p class="empty-reservations-message">Vous n'avez pas encore effectué de réservations.</p>
                <p class="empty-reservations-subtext">Pour effectuer une réservation, ajoutez des formations à votre panier puis validez votre commande, le paiement s'effectuera directement en centre.</p>
                <div class="empty-reservations-action">
                    <a href="{{ route('formations') }}" class="btn-discover">
                        Découvrir nos formations <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('panier.index') }}" class="btn-cart">
                        <i class="fas fa-shopping-cart"></i> Voir panier
                    </a>
                </div>
            </div>
        </div>
    </div>
  @else
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @foreach($reservations as $reservation)
        <div class="card mb-4 reservation-card">
            <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #CFE2FF;">
                <div>
                    <h4 style="font-weight: bold; color:#2C2C3A;">Mes Réservations</h4>

                    @if(!$reservation->status)
                    <div class="payment-alert">
                        <div class="payment-alert-header">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-3 alert-icon"></i>
                                <div>
                                    <h6 class="mb-1">Paiement requis</h6>
                                </div>
                                <i class="fas fa-chevron-down ms-auto dropdown-icon"></i>
                            </div>
                        </div>
                        <div class="payment-alert-content">
                            <p class="mb-0">Afin de confirmer votre réservation, merci d’effectuer le paiement au centre au minimum <strong>2 jours </strong> avant votre première formation, en présentant le reçu ci-dessous. Si cela n’est pas possible, vous pourrez régler exceptionnellement le jour même de la formation.</p>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0" style="color: #1b1c1d;">Réservation #{{ $reservation->id }}</h5>
                            <small class="text-muted">Effectuée le {{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y à H:i') }}</small>
                            <div class="mt-2">
                                <button
                                    class="btn view-invoice-btn btn-sm square-button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#invoiceModal{{ $reservation->id}}"
                                >
                                    <i class="fas fa-file-invoice me-2"></i> Voir le reçu
                                </button>
                            </div>
                        </div>
                        <div class="status-badge">
                            @if($reservation->status)
                                <span class="badge square-badge" style="background-color: #2B6ED4; color: white;"><i class="fas fa-check-circle me-1"></i> Payée</span>
                            @else
                                <span class="badge custom-bg small square-badge"><i class="fas fa-clock me-1"></i> En attente de paiement</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($reservation->trainings->isEmpty())
                    <p>Aucune formation trouvée pour cette réservation.</p>
                @else
                    <div class="formations-container {{ $reservation->trainings->count() <= 3 ? 'formations-few' : '' }}" id="formations-container-{{ $reservation->id }}">
                        <div class="formations-grid" id="formations-grid-{{ $reservation->id }}">
                            @foreach($reservation->trainings->take(3) as $training)
                                <div class="formation-card" data-training-id="{{ $training->id }}">
                                    <div class="formation-image">
                                        @if($training->discount > 0)
                                            <div class="ribbon ribbon-success ribbon-right">{{ $training->discount }}%</div>
                                        @endif
                                        @if($training->image)
                                            <img src="{{ asset('storage/' . $training->image) }}" alt="{{ $training->title }}">
                                        @else
                                            <div class="no-image-placeholder">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2B6ED4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="formation-details">
                                        <h5>{{ $training->title }}</h5>
                                        <p class="teacher">{{ $training->user ? $training->user->lastname . ' ' . $training->user->name : 'Non assigné' }}</p>
                                        <div class="price-container">
                                            @if($training->discount > 0)
                                                <div class="original-price">{{ number_format($training->price, 2, ',', ' ') }} Dt</div>
                                            @endif
                                            <div class="final-price">{{ number_format($training->price_after_discount, 2, ',', ' ') }} Dt</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($reservation->trainings->count() > 3)
                                <div class="hidden-formations" id="hidden-formations-{{ $reservation->id }}" style="display: none;">
                                    @foreach($reservation->trainings->skip(3) as $training)
                                        <div class="formation-card" data-training-id="{{ $training->id }}">
                                            <div class="formation-image">
                                                @if($training->discount > 0)
                                                    <div class="ribbon ribbon-success ribbon-right">{{ $training->discount }}%</div>
                                                @endif
                                                @if($training->image)
                                                    <img src="{{ asset('storage/' . $training->image) }}" alt="{{ $training->title }}">
                                                @else
                                                    <div class="no-image-placeholder">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2B6ED4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="formation-details">
                                                <h5>{{ $training->title }}</h5>
                                                <p class="teacher">{{ $training->user ? $training->user->lastname . ' ' . $training->user->name : 'Non assigné' }}</p>
                                                <div class="price-container">
                                                    @if($training->discount > 0)
                                                        <div class="original-price">{{ number_format($training->price, 2, ',', ' ') }} Dt</div>
                                                    @endif
                                                    <div class="final-price">{{ number_format($training->price_after_discount, 2, ',', ' ') }} Dt</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if($reservation->trainings->count() > 3)
                            <div class="text-center mt-3 mb-4">
                                <button class="btn-voir-plus" onclick="toggleFormations({{ $reservation->id }}, {{ $reservation->trainings->count() - 3 }})" id="btn-toggle-{{ $reservation->id }}">
                                    <i class="fas fa-chevron-down me-2"></i>Voir plus ({{ $reservation->trainings->count() - 3 }} formations)
                                </button>
                            </div>
                        @endif

                        <div class="total-container">
                            <div class="total-section">
                                @if($reservation->total_discount > 0)
                                    <div class="total-row">
                                        <span>Total original</span>
                                        <span>{{ number_format($reservation->original_total, 2, ',', ' ') }} Dt</span>
                                    </div>
                                    <div class="total-row">
                                        <span>Total remises</span>
                                        <span>-{{ number_format($reservation->total_discount, 2, ',', ' ') }} Dt</span>
                                    </div>
                                @endif
                                <div class="total-row grand-total">
                                    <span>Total à payer</span>
                                    <span>{{ number_format($reservation->total_price, 2, ',', ' ') }} Dt</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-end" style="background-color: #f8f9fa; border-top: 1px solid #CFE2FF;"></div>
        </div>

        @include('admin.apps.reservations.modal-facture', ['reservation' => $reservation])
    @endforeach
  @endif
</div>
@endsection
