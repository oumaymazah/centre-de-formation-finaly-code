<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Empowerment Learning'); ?></title>
    
    <!-- Votre CSS existant -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/MonCss/header-styles.css')); ?>">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <script>
    (function() {
        var cartCount = localStorage.getItem('cartCount');
        cartCount = cartCount ? parseInt(cartCount) : 0;
        
        if (cartCount > 0) {
            document.write(`
                <style>
                    #fixed-cart-badge {
                        display: flex !important;
                        content: "${cartCount}";
                    }
                </style>
            `);
        }
    })();
    </script>
</head>
<body>
    <div class="app-container">
        <!-- VOTRE HEADER EXISTANT - EXACTEMENT LE MÊME -->
        <div class="page-main-header">
          <div class="main-header-right row m-0">
            <?php if(auth()->guard()->check()): ?>
            <div class="main-header-left">
              <div class="logo-wrapper"><a href="<?php echo e(route('index')); ?>"><img class="img-fluid logo-img" src="<?php echo e(asset('assets/images/logo/elsEMPO-removebg-preview.png')); ?>" alt="Logo"></a></div>
              <div class="dark-logo-wrapper"><a href="<?php echo e(route('index')); ?>"><img class="img-fluid logo-img" src="<?php echo e(asset('assets/images/logo/elsEMPO-removebg-preview.png')); ?>" alt="Dark Logo"></a></div>
              <div class="toggle-sidebar"><i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle"></i></div>
            </div>
            <?php endif; ?>
            
            <div class="nav-right col pull-right right-menu p-0">
              <ul class="nav-menus">
                <?php if(auth()->guard()->guest()): ?>
                <li class="nav-logo">
                  <a href="#" onclick="loadPage('accueil')">
                    <img src="<?php echo e(asset('assets/images/logo/elsEMPO-removebg-preview.png')); ?>" alt="Logo" class="nav-logo-img">
                  </a>
                </li>
                <?php endif; ?>

                <?php if(auth()->guard()->guest()): ?>
                <li class="nav-item">
                    <a href="#" class="nav-link active" onclick="loadPage('accueil')" data-page="accueil">Accueil</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="loadPage('apropos')" data-page="apropos">À propos</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="loadPage('formations')" data-page="formations">Catalogues de formations</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="loadPage('contact')" data-page="contact">Nos contacts</a>
                </li>
                <?php endif; ?>

                <li>
                  <div class="mode"><i class="fa fa-moon-o"></i></div>
                </li>

                <?php if(auth()->check() && auth()->user()->hasRole('etudiant')): ?>
                <li>
                  <a href="/mes-reservations" class="position-relative" data-bs-toggle="tooltip" data-bs-placement="right" title="Mes réservations">
                    <div class="position-relative">
                      <i data-feather="calendar" class="feather-icon text-dark"></i>
                      <span id="reservation-indicator" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.5rem; transform: translate(-50%, -50%);">
                        <span class="visually-hidden">réservations actives</span>
                      </span>
                    </div>
                  </a>
                </li>
                <?php endif; ?>

                <?php if(!auth()->check() || (auth()->check() && auth()->user()->hasRole('etudiant'))): ?>
                <li>
                  <?php if(!auth()->check()): ?>
                    <div class="cart-container" style="position: relative; cursor: pointer;" 
                        onmouseenter="showCartLoginModal()" 
                        onmouseleave="hideCartLoginModal()">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                      </svg>
                      <span id="fixed-cart-badge" class="custom-violet-badge"
                            style="position: absolute; top: -8px; right: -8px; background-color: #2563EB; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold; z-index: 10; visibility: hidden; opacity: 0;"></span>
                    </div>
                  <?php else: ?>
                    <a href="<?php echo e(route('panier.index')); ?>">
                      <div class="cart-container" style="position: relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
                          <circle cx="9" cy="21" r="1"></circle>
                          <circle cx="20" cy="21" r="1"></circle>
                          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span id="fixed-cart-badge" class="custom-violet-badge"
                              style="position: absolute; top: -8px; right: -8px; background-color: #2563EB; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold; z-index: 10; visibility: hidden; opacity: 0;"></span>
                      </div>
                    </a>
                  <?php endif; ?>
                </li>
                <?php endif; ?>

                <?php if(auth()->guard()->guest()): ?>
                <li class="auth-buttons-nav">
                  <a href="<?php echo e(route('login')); ?>" class="btn-connect-nav">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                      <polyline points="10,17 15,12 10,7"></polyline>
                      <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    Se connecter
                  </a>
                </li>
                <li class="auth-buttons-nav">
                  <a href="<?php echo e(route('register')); ?>" class="btn-register-nav">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                      <circle cx="8.5" cy="7" r="4"></circle>
                      <line x1="20" y1="8" x2="20" y2="14"></line>
                      <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    S'inscrire
                  </a>
                </li>
                <?php endif; ?>
              
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                      return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                  });
                </script>

                <?php if(auth()->guard()->check()): ?>
                <li class="onhover-dropdown p-0">
                  <a class="btn btn-primary-light" href="<?php echo e(route('logout')); ?>"
                    onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();">
                    <?php echo e(__('Logout')); ?>

                  </a>
                  <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                    <?php echo csrf_field(); ?>
                  </form>
                </li>
                <?php endif; ?>
              </ul>
            </div>
            <div class="d-lg-none mobile-toggle pull-right w-auto"><i data-feather="more-horizontal"></i></div>
          </div>
        </div>

        <!-- Modal de connexion pour le panier -->
        <div id="cartLoginModal" class="cart-login-modal" style="display: none;">
          <div class="modal-content">
            <button class="close-modal-btn" onclick="closeCartLoginModal()" type="button">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
            
            <div class="modal-icon">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <circle cx="12" cy="16" r="1"></circle>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </div>
            <h3>Connexion Requise</h3>
            <p>Vous devez vous connecter tout d'abord pour accéder à votre panier et gérer vos formations.</p>
            <div class="modal-buttons">
              <a href="<?php echo e(route('login')); ?>" class="btn-connect">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                  <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                  <polyline points="10,17 15,12 10,7"></polyline>
                  <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                Se connecter
              </a>
              <a href="<?php echo e(route('register')); ?>" class="btn-register">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="8.5" cy="7" r="4"></circle>
                  <line x1="20" y1="8" x2="20" y2="14"></line>
                  <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                S'inscrire
              </a>
            </div>
          </div>
        </div>

        <!-- Conteneur pour le contenu dynamique -->
        <div id="main-content" style="flex: 1; padding: 20px;">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <!-- Tous vos scripts existants -->
    <script>
    let cartModalTimeout;

    function showCartLoginModal() {
      clearTimeout(cartModalTimeout);
      const modal = document.getElementById('cartLoginModal');
      if (modal) {
        modal.style.display = 'flex';
        setTimeout(() => {
          modal.classList.add('show');
        }, 10);
      }
    }

    function hideCartLoginModal() {
      cartModalTimeout = setTimeout(() => {
        const modal = document.getElementById('cartLoginModal');
        if (modal) {
          modal.classList.remove('show');
          setTimeout(() => {
            modal.style.display = 'none';
          }, 300);
        }
      }, 100);
    }

    function closeCartLoginModal() {
      clearTimeout(cartModalTimeout);
      const modal = document.getElementById('cartLoginModal');
      if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
          modal.style.display = 'none';
        }, 300);
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('cartLoginModal');
      if (modal) {
        modal.addEventListener('mouseenter', () => {
          clearTimeout(cartModalTimeout);
        });
        
        modal.addEventListener('mouseleave', hideCartLoginModal);
        
        modal.addEventListener('click', function(e) {
          if (e.target === modal) {
            closeCartLoginModal();
          }
        });
        
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeCartLoginModal();
          }
        });
      }
    });
    </script>

    <script>
    (function() {
      const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
      const badge = document.getElementById('fixed-cart-badge');
      if (badge) {
        badge.textContent = cartCount.toString();
        badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
        badge.style.opacity = cartCount > 0 ? '1' : '0';
      }
    })();
    </script>

    <!-- Script pour la navigation SPA -->
    <script>
    // Configuration des pages
    const pages = {
        accueil: { title: 'Accueil - Empowerment Learning', url: '/api/accueil' },
        apropos: { title: 'À propos - Empowerment Learning', url: '/api/apropos' },
        formations: { title: 'Formations - Empowerment Learning', url: '/api/formations' },
        contact: { title: 'Contact - Empowerment Learning', url: '/api/contact' }
    };

    function loadPage(pageName) {
        const contentContainer = document.getElementById('main-content');
        const page = pages[pageName];
        
        if (!page) return;

        // Afficher loading
        contentContainer.innerHTML = '<div style="text-align: center; padding: 50px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #2563EB; border-radius: 50%; animation: spin 1s linear infinite;"></div><p>Chargement...</p></div>';
        
        // Charger le contenu via AJAX
        fetch(page.url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.text())
        .then(html => {
            contentContainer.innerHTML = html;
            document.title = page.title;
            updateActiveLink(pageName);
            
            // Mettre à jour l'URL sans recharger
            const newUrl = `/${pageName}`;
            history.pushState({ page: pageName }, page.title, newUrl);
        })
        .catch(error => {
            contentContainer.innerHTML = '<div style="text-align: center; padding: 50px; color: red;">Erreur de chargement</div>';
        });
    }

    function updateActiveLink(activePage) {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-page') === activePage) {
                link.classList.add('active');
            }
        });
    }

    // Gestion du bouton retour
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.page) {
            loadPage(event.state.page);
        }
    });

    // Empêcher le comportement par défaut des liens
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('nav-link') && e.target.getAttribute('onclick')) {
            e.preventDefault();
        }
    });
    </script>

    <script src="<?php echo e(asset('assets/js/MonJs/formations/panier.js')); ?>"></script>
    
    <style>
    @keyframes  spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .app-container {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    </style>
</body>
</html><?php /**PATH C:\Users\hibah\P_Plateforme_ELS\resources\views/layouts/main.blade.php ENDPATH**/ ?>