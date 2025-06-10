
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
        // Mettre à jour tous les badges existants
        const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
        badges.forEach(badge => {
            badge.textContent = count.toString();
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
        
        // Mettre à jour le badge fixe si présent
        const fixedBadge = document.getElementById('fixed-cart-badge');
        if (fixedBadge) {
            fixedBadge.textContent = count.toString();
            fixedBadge.style.display = count > 0 ? 'flex' : 'none';
        }
        
        // Créer des badges s'il n'y en a pas et si le compteur > 0
        if (count > 0 && badges.length === 0) {
            window.initializeCartBadges();
        }
    })
    .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
};

window.initializeCartBadges = function() {
    // Récupérer la valeur du panier depuis localStorage
    const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
    
    // Ne rien faire si le compteur est 0
    if (cartCount <= 0) return;
    
    // Injecter le style du badge si nécessaire
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
    
    // Sélecteurs pour trouver les icônes de panier
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
            // Créer le badge
            const badge = document.createElement('span');
            badge.className = 'cart-badge custom-violet-badge';
            badge.textContent = cartCount.toString();
            
            // S'assurer que le conteneur est en position relative
            if (getComputedStyle(container).position === 'static') {
                container.style.position = 'relative';
            }
            
            container.appendChild(badge);
        }
    });
};

window.updateCartBadgeCount = function(count) {
    localStorage.setItem('cartCount', count.toString());
    
    // Mettre à jour tous les badges existants
    const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
    badges.forEach(badge => {
        badge.textContent = count.toString();
        badge.style.display = count > 0 ? 'flex' : 'none';
    });
    
    // Mettre à jour le badge fixe si présent
    const fixedBadge = document.getElementById('fixed-cart-badge');
    if (fixedBadge) {
        fixedBadge.textContent = count.toString();
        fixedBadge.style.display = count > 0 ? 'flex' : 'none';
    }
    
    // Créer des badges s'il n'y en a pas et si le compteur > 0
    if (count > 0 && badges.length === 0) {
        window.initializeCartBadges();
    }
};

// Fonction d'initialisation principale
function initCartSystem() {
    // Initialiser les badges immédiatement avec la valeur en cache
    window.initializeCartBadges();
    
    // Puis synchroniser avec le serveur
    setTimeout(window.synchronizeWithServer, 100);
    
    // Observer le DOM pour les nouvelles icônes de panier
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.addedNodes.length) {
                // Vérifier si de nouvelles icônes ont été ajoutées
                window.initializeCartBadges();
            }
        });
    });
    
    // Observer le document entier
    observer.observe(document.documentElement, {
        childList: true,
        subtree: true
    });
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
    // Initialiser tous les dropdowns d'alerte de paiement
    initPaymentAlertDropdowns();
});

function initPaymentAlertDropdowns() {
    const paymentAlertHeaders = document.querySelectorAll('.payment-alert-header');
    
    paymentAlertHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const content = this.nextElementSibling;
            
            // Toggle la visibilité du contenu
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
</script>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-5">
  <?php if($reservations->isEmpty()): ?>
<div class="container my-5">
    <div class="empty-reservations-container">
        <div class="empty-reservations-content">
            <div class="cart-icon-wrapper">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h2 class="empty-reservations-title">Aucune réservation trouvée</h2>
            <p class="empty-reservations-message">Vous n'avez pas encore effectué de réservations.</p>
<p class="empty-reservations-subtext">Pour effectuer une réservation, ajoutez des formations à votre panier puis validez votre commande, le paiement s'effectuera directement en centre.</p>            <div class="empty-reservations-action">
                <a href="<?php echo e(route('formations')); ?>" class="btn-discover">
                    Découvrir nos formations <i class="fas fa-arrow-right"></i>
                </a>
                <a href="<?php echo e(route('panier.index')); ?>" class="btn-cart">
                    <i class="fas fa-shopping-cart"></i> Voir panier
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .empty-reservations-container {
        padding: 0;
        background-color: transparent;
        border-radius: 0;
        min-height: auto;
        display: block;
    }
.empty-reservations-content {
    max-width: 100%;  /* Remplacer par: max-width: 100%; */
    text-align: left;
    padding: 40px;
    background-color: #f9fafb;
    border-radius: 8px;
    margin: 0;
    display: inline-block;
    width: 100%;  /* Remplacer par: width: 100%; */
}

    .cart-icon-wrapper {
        display: inline-block;
        margin-bottom: 20px;
    }

    .cart-icon-wrapper i {
        font-size: 24px;
        color: #333;
    }

    .empty-reservations-title {
        font-size: 30px;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }

    .empty-reservations-message {
        font-size: 18px;
        color: #333;
        margin-bottom: 8px;
    }

    .empty-reservations-subtext {
        font-size: 16px;
        color: #6B7280;
        margin-bottom: 30px;
        line-height: 1.5;
    }

    .empty-reservations-action {
        margin-top: 20px;
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }

    .btn-discover, .btn-cart {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 16px;
    }
    
    .btn-discover {
        color: #2B6ED4;
    }
    
    .btn-cart {
        color: #6c757d;
    }

    .btn-discover i, .btn-cart i {
        margin-left: 8px;
        transition: transform 0.3s ease;
    }
    
    .btn-cart i {
        margin-left: 0;
        margin-right: 8px;
    }

    .btn-discover:hover {
        color: #1e5bb8;
    }
    
    .btn-cart:hover {
        color: #5a6268;
    }

    .btn-discover:hover i {
        transform: translateX(4px);
    }

    @media (max-width: 768px) {
        .empty-reservations-title {
            font-size: 24px;
        }

        .empty-reservations-message,
        .empty-reservations-subtext {
            font-size: 15px;
        }
        
        .empty-reservations-content {
            padding: 30px 20px;
        }
    }
</style>
<?php else: ?>

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <?php $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card mb-4 reservation-card">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #CFE2FF;">
                    <div>
                        <h4 style="font-weight: bold; color:#2C2C3A;">Mes Réservations</h4>
                        
                        <?php if(!$reservation->status): ?>
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
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0" style="color: #1b1c1d;">Réservation #<?php echo e($reservation->id); ?></h5>
                                <small class="text-muted">Effectuée le <?php echo e(\Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y à H:i')); ?></small>
                                <!-- Bouton "Voir le reçu" placé ici, juste sous la date -->
                                <div class="mt-2">
                                    <button 
                                        class="btn view-invoice-btn btn-sm square-button" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#invoiceModal<?php echo e($reservation->id); ?>"
                                    >
                                        <i class="fas fa-file-invoice me-2"></i> Voir le reçu 
                                    </button>
                                </div>
                            </div>
                            
                            <div class="status-badge">
    <?php if($reservation->status): ?>
        <span class="badge square-badge" style="background-color: #2B6ED4; color: white;"><i class="fas fa-check-circle me-1"></i> Payée</span>
    <?php else: ?>
        <span class="badge custom-bg  small square-badge"><i class="fas fa-clock me-1"></i> En attente de paiement</span>
    <?php endif; ?>
</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    
                    
                    <?php if($reservation->trainings->isEmpty()): ?>
                        <p>Aucune formation trouvée pour cette réservation.</p>
                    <?php else: ?>
                        <div class="formations-grid">
                            <?php $__currentLoopData = $reservation->trainings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $training): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="formation-card">
                                    <div class="formation-image">
                                        <?php if($training->discount > 0): ?>
                                            <div class="ribbon ribbon-success ribbon-right"><?php echo e($training->discount); ?>%</div>
                                        <?php endif; ?>
                                        <?php if($training->image): ?>
                                            <img src="<?php echo e(asset('storage/' . $training->image)); ?>" alt="<?php echo e($training->title); ?>">
                                        <?php else: ?>
                                            <div class="no-image-placeholder">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2B6ED4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="formation-details">
                                        <h5><?php echo e($training->title); ?></h5>
                                        <p class="teacher"><?php echo e($training->user ? $training->user->lastname . ' ' . $training->user->name : 'Non assigné'); ?></p>
                                        <div class="price-container">
                                            <?php if($training->discount > 0): ?>
                                                <div class="original-price"><?php echo e(number_format($training->price, 2, ',', ' ')); ?> Dt</div>
                                            <?php endif; ?>
                                            <div class="final-price"><?php echo e(number_format($training->price_after_discount, 2, ',', ' ')); ?> Dt</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                        <div class="total-section" style="width: 300px; margin-left: 0;">
                            <?php if($reservation->total_discount > 0): ?>
                            <div class="total-row">
                                <span>Total original</span>
                                <span><?php echo e(number_format($reservation->original_total, 2, ',', ' ')); ?> Dt</span>
                            </div>
                            
                            <div class="total-row">
                                <span>Total remises</span>
                                <span>-<?php echo e(number_format($reservation->total_discount, 2, ',', ' ')); ?> Dt</span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="total-row grand-total">
                                <span>Total à payer</span>
                                <span><?php echo e(number_format($reservation->total_price, 2, ',', ' ')); ?> Dt</span>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>
                </div>
                <!-- Suppression du bouton dans le footer -->
                <div class="card-footer d-flex justify-content-end" style="background-color: #f8f9fa; border-top: 1px solid #CFE2FF;">
                    <!-- Le bouton "Voir le reçu" a été déplacé -->
                </div>
            </div>

            <!-- Inclusion du modal de facture -->
            <?php echo $__env->make('admin.apps.reservations.modal-facture', ['reservation' => $reservation], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>

<style>
    /* Styles pour les cartes de réservation */
    .reservation-card {
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #CFE2FF;
        padding: 20px;
    }
    
    .card-header h4 {
        margin-bottom: 15px;
        padding-bottom: 10px;
        position: relative;
        font-size: 1.4rem;
    }
    
    .card-header h4:after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 100px;
        background-color: #2B6ED4;
    }
    
    /* Nouveaux styles pour l'alerte dropdown */
    .payment-alert {
        border-radius: 8px;
        border-left: 4px solid #f8d7da;
        background-color: #f8d7da;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        color:#721c24;
        overflow: hidden;
    }
    
    .payment-alert-header {
        padding: 15px;
        cursor: pointer;
        user-select: none;
    }
    
    .payment-alert-content {
        padding: 0 15px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        margin: 0;
        visibility: hidden;
    }
    
    .payment-alert-content.open {
        padding: 0 15px 15px 15px;
        visibility: visible;
    }
    
    .dropdown-icon {
        transition: transform 0.3s ease;
    }
    
    .dropdown-icon.rotate {
        transform: rotate(180deg);
    }
    
    .alert-icon {
        font-size: 1.5rem;
        color: #721c24;
    }
    
    .status-badge .badge {
        font-size: 0.8rem;
        padding: 0.5rem 0.8rem;
        letter-spacing: 0.5px;
        border-radius: 0; /* Rendre les badges carrés */
    }
    
    /* Styles pour la grille de formations */
    .formations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .formation-card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background-color: white;
    }
    
    .formation-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .formation-image {
    height: 160px;
    overflow: hidden;
    position: relative;
}
    .formation-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
/* .ribbon {
    position: absolute;
    top: 15px;
    z-index: 10;
    padding: 4px 8px;
    font-size: 0.85rem;
    font-weight: 600;
    color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.ribbon-success {
    background-color: #1e6356;
}

.ribbon-right {
    right: 0;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
} */
    
    .no-image-placeholder {
        width: 100%;
        height: 100%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .formation-details {
        padding: 15px;
    }
    
    .formation-details h5 {
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 1rem;
        line-height: 1.3;
        height: 2.6rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .teacher {
        color: #666;
        font-size: 0.85rem;
        margin-bottom: 12px;
    }
    

    
.discount-badge {
    background-color: #1e6356;
    color: white;
    padding: 6px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
}
 .ribbon-success {
            top: 50px !important;  /* Ajusté pour maintenir l'espace sous le "Complet" */
            right: 0 !important;
            left: auto !important;
        }
    
.price-container {
    display: flex;
    align-items: center;
    gap: 20px; /* Espace réduit entre les deux prix */
    justify-content: flex-start; /* Aligner à gauche */
}

.final-price {
    font-weight: 700;
    color: #333;
    font-size: 1rem;
    order: 1; /* Prix final en premier */
}

.original-price {
    text-decoration: line-through;
    color: #888;
    font-size: 0.85rem;
    order: 2; /* Prix original en second */
    margin-left: 0; /* Supprimer le margin-left: auto */
}

    
    /* Styles pour la section de total */
   .total-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px 20px;
    margin-top: 50px;
    /* Ajoutez ces propriétés pour assurer que la section reste alignée à gauche */
    align-self: flex-start;
    box-shadow: 0 3px 6px rgba(0,0,0,0.08);
}
    
    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 0.95rem;
        color: #6c757d;  /* Changé de rgba(108, 117, 125, 0.3) à un gris solide */    
    }
    
    .grand-total {
        border-top: 1px solid #CFE2FF;
        margin-top: 8px;
        padding-top: 12px;
        padding: 6px 10px; /* Réduction du padding pour rendre la zone plus petite */   
        font-weight: 200;
        font-size: 1rem;
        background-color:   #6c757d !important;  /* Changé de rgba(108, 117, 125, 0.3) à un gris solide */    
        color: #6c757d;  /* Changé de rgba(108, 117, 125, 0.3) à un gris solide */
    }
    
    /* Style pour le bouton Voir le reçu */
    .view-invoice-btn {
        color: white;
        background-color: #6c757d;  /* Changé de #2B6ED4 à #6c757d (gris) */
        transition: all 0.3s ease;
        border-radius: 0; /* Rendre le bouton carré */
        padding: 6px 14px;  /* Plus petit pour être proportionné à btn-sm */
        font-weight: 600;
        font-size: 0.85rem;  /* Taille de police plus petite pour btn-sm */
    }
    
    .square-button {
        border-radius: 0 !important; /* Assurer que le bouton est parfaitement carré */
    }
    
    /* Style pour les badges carrés */
    .square-badge {
        border-radius: 0 !important; /* Rendre les badges carrés */
        padding: 0.5rem 0.8rem;
        letter-spacing: 0.5px;
    }

    .view-invoice-btn:hover {
        background-color: #5a6268;
        color: white;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .custom-bg{
                background-color: #f8d7da;
                        color:#721c24;


    }
    .card-footer {
        padding: 15px 20px;
    }
</style>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Gestion du téléchargement de facture via l'API
    document.querySelectorAll('.download-button').forEach(button => {
        button.addEventListener('click', function() {
            const reservationId = this.getAttribute('data-reservation-id');
            // Redirection vers l'endpoint de téléchargement de facture
            window.location.href = `/api/reservations/${reservationId}/invoice`;
        });
    });
    
    // Initialiser les dropdowns d'alerte de paiement après chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        initPaymentAlertDropdowns();
    });
</script>

<script>
    <script src="<?php echo e(asset('assets/js/MonJs/formations/panier.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/MonJs/cart.js')); ?>"></script>
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\hibah\P_Plateforme_ELS\resources\views/admin/apps/reservations/mes-reservations.blade.php ENDPATH**/ ?>