<link rel="stylesheet" href="{{ asset('assets/css/MonCss/header-styles.css') }}">

<!-- Script for cart badge initialization -->
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

<div class="page-main-header {{ auth()->check() ? 'authenticated-header' : '' }}">
  <div class="main-header-right row m-0 {{ auth()->check() ? 'authenticated-main-header' : '' }}">
    <div class="main-header-left {{ auth()->check() ? 'authenticated-left' : '' }}">
      <!-- Unified logo for both authenticated and non-authenticated users -->
      <div class="logo-wrapper"><a href="{{ route('index') }}"><img class="img-fluid {{ auth()->check() ? 'authenticated-logo' : 'nav-logo-img' }}" src="{{ asset('assets/images/logo/elsEMPO-removebg-preview.png') }}" alt="Logo"></a></div>
      <div class="dark-logo-wrapper"><a href="{{ route('index') }}"><img class="img-fluid {{ auth()->check() ? 'authenticated-logo' : 'nav-logo-img' }}" src="{{ asset('assets/images/logo/elsEMPO-removebg-preview.png') }}" alt="Dark Logo"></a></div>
      <!-- Toggle only for authenticated users -->
      @auth
      <div class="toggle-sidebar"><i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle"></i></div>
      @endauth
    </div>

    <div class="nav-right col pull-right right-menu p-0 {{ auth()->check() ? 'authenticated-nav-right' : '' }}">
      <ul class="nav-menus {{ auth()->check() ? 'authenticated-nav-menus' : '' }}">
        <!-- Navigation links for guests -->
        @guest
        <li class="nav-item">
          <a href="{{ url('accueil') }}" class="nav-links">Accueil</a>
        </li>
        <li class="nav-item">
          <a href="{{ url('ÀPropos') }}" class="nav-links">À propos</a>
        </li>
        <li class="nav-item"><a href="{{ route('formations') }}" class="nav-links">Catalogues de formations</a></li>
        <li class="nav-item"><a href="#contact" class="nav-links">Nos contacts</a></li>
        <li class="nav-item"><a href="politique" class="nav-links">Politique de réservation</a></li>
        @endguest

        @if(auth()->check() && auth()->user()->hasRole('etudiant'))
        <!-- Icône de réservation visible pour utilisateurs authentifiés avec rôle étudiant seulement -->
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
        @endif

        @if(!auth()->check() || (auth()->check() && auth()->user()->hasRole('etudiant')))
        <!-- Icône de panier visible pour non-auth et étudiants seulement -->
        <li>
          @if(!auth()->check())
            <!-- Pour les utilisateurs non authentifiés : modal au hover -->
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
          @else
            <!-- Pour les étudiants authentifiés : lien normal -->
            <a href="{{ route('panier.index') }}">
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
          @endif
        </li>
        @endif

        <!-- Boutons d'authentification après l'icône de panier (pour les utilisateurs non connectés) -->
        @guest
        <li class="auth-buttons-nav">
          <a href="{{ route('login') }}" class="btn-connect-nav">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
              <polyline points="10,17 15,12 10,7"></polyline>
              <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            Se connecter
          </a>
        </li>
        <li class="auth-buttons-nav">
          <a href="{{ route('register') }}" class="btn-register-nav">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="8.5" cy="7" r="4"></circle>
              <line x1="20" y1="8" x2="20" y2="14"></line>
              <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            S'inscrire
          </a>
        </li>
        @endguest

        @auth
        <li class="onhover-dropdown p-0">
          <a class="btn btn-primary-light" href="{{ route('logout') }}"
            onclick="event.preventDefault();
                      document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </li>
        @endauth
      </ul>
    </div>
    <div class="d-lg-none mobile-toggle pull-right w-auto"><i data-feather="more-horizontal"></i></div>
  </div>
</div>

<!-- Modal de connexion pour le panier -->
<div id="cartLoginModal" class="cart-login-modal" style="display: none;">
  <div class="modal-content">
    <!-- Bouton de fermeture -->
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
      <a href="{{ route('login') }}" class="btn-connect">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
          <polyline points="10,17 15,12 10,7"></polyline>
          <line x1="15" y1="12" x2="3" y2="12"></line>
        </svg>
        Se connecter
      </a>
      <a href="{{ route('register') }}" class="btn-register">
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
    modal.addEventListener('mouseenter', () => clearTimeout(cartModalTimeout));
    modal.addEventListener('mouseleave', hideCartLoginModal);
    modal.addEventListener('click', function(e) {
      if (e.target === modal) closeCartLoginModal();
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && modal.classList.contains('show')) closeCartLoginModal();
    });
  }

  // Restore toggle sidebar functionality
  const toggle = document.getElementById('sidebar-toggle');
  if (toggle) {
    toggle.addEventListener('click', function() {
      document.body.classList.toggle('sidebar-mini');
    });
  }
});
</script>

<script src="{{ asset('assets/js/MonJs/formations/panier.js') }}"></script>
