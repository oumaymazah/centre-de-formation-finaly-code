<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>elsEMPO - Plateforme de Formation</title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/MonCss/header-styles.css')); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/homepage.css')); ?>">
</head>
<body>
    <!-- Header -->
<?php echo $__env->make('admin.apps.home.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- Content Container -->
    <main id="content-container">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Scripts -->
    <script src="<?php echo e(asset('assets/js/MonJs/formations/panier.js')); ?>"></script>
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

        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

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

        (function() {
            const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
            const badge = document.getElementById('fixed-cart-badge');
            if (badge) {
                badge.textContent = cartCount.toString();
                badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
                badge.style.opacity = cartCount > 0 ? '1' : '0';
            }
        })();

        // Dynamic content loading for Accueil
        function loadHomeContent(event) {
            event.preventDefault();
            const contentContainer = document.getElementById('content-container');
            fetch('<?php echo e(route("home.content")); ?>')
                .then(response => response.text())
                .then(html => {
                    contentContainer.innerHTML = html;
                    initializeAnimations();
                })
                .catch(error => console.error('Error loading content:', error));
        }

        function initializeAnimations() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            const animateOnScroll = () => {
                const elements = document.querySelectorAll('.animate-on-scroll');
                elements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 150;
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.classList.add('animate');
                    }
                });
            };

            const animateCounters = () => {
                const counters = document.querySelectorAll('.stat-number');
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'));
                    const count = parseInt(counter.innerText) || 0;
                    const increment = target / 100;
                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        setTimeout(() => animateCounters(), 50);
                    } else {
                        counter.innerText = target;
                    }
                });
            };

            window.addEventListener('scroll', animateOnScroll);
            animateOnScroll();
            const statsSection = document.querySelector('.stats');
            if (statsSection) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCounters();
                            observer.unobserve(entry.target);
                        }
                    });
                });
                observer.observe(statsSection);
            }
        }
    </script>
</body>
</html><?php /**PATH C:\Users\hibah\P_Plateforme_ELS\resources\views/admin/apps/home/main.blade.php ENDPATH**/ ?>