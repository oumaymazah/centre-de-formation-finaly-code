<?php $__env->startSection('title'); ?> Politique <?php echo e($title); ?> <?php $__env->stopSection(); ?>
<?php
    $hideAdminFooter = true;
?>

<?php $__env->startSection('content'); ?>
<div class="fullwidth-container">
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Politique de Réservation</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
            /* Ajoutez cette règle CSS dans votre section <style> de la page "À propos" */

/* Décalage du contenu du footer vers la droite */
.footer .container {
    padding-left: 6rem !important; /* Ajustez cette valeur selon vos besoins */
    padding-right: -20rem !important;
}


/* Alternative avec margin si vous préférez */
/*
.footer .container {
    margin-left: 4rem !important;
    margin-right: 2rem !important;
}
*/

/* Responsive - réduire le décalage sur mobile */
@media (max-width: 768px) {
    .footer .container {
        padding-left: 2rem !important;
        padding-right: 1rem !important;
    }
}

@media (max-width: 480px) {
    .footer .container {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
}
        </style>
        <style>
            /* Reset pour forcer la pleine largeur */
           /* .fullwidth-container {
                margin: 0 !important;
                padding: 0 !important;
                width: 100vw !important;
                position: relative;
                left: 50%;
                right: 100%;
                margin-left: -60vw !important;
                margin-right: -80vw !important;
            } */
              .fullwidth-container {
                margin: 0 !important;
                padding: 0 !important;
                width: 100vw !important;
                position: relative;
                left: 40%;
                right: 100%;
                margin-left: -50vw !important;
                margin-right: -80vw !important;
            }


            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.7;
                color: #1a202c;
                background: #e8e4f0;
            }

            .politique-section {
                padding: 100px 0 80px;
                position: relative;
                overflow: hidden;
                width: 100%;
                min-height: 100vh;
                background: #e8e4f0;
            }

            .politique-section::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
                animation: rotate 30s linear infinite;
                z-index: 0;
            }

            @keyframes  rotate {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* .container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 40px;
                position: relative;
                z-index: 2;
            } */
               .container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 2rem !important;
            }

            .content-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 2rem;
            }


            .section-header {
                text-align: center;
                margin-bottom: 80px;
                animation: fadeInDown 1.2s ease-out;
            }

            .section-title {
                font-size: 4rem;
                font-weight: 800;
                color: #4c6ef5;
                margin-bottom: 30px;
                position: relative;
                display: inline-block;
            }

            .section-title::after {
                content: '';
                position: absolute;
                bottom: -15px;
                left: 50%;
                transform: translateX(-50%);
                width: 120px;
                height: 4px;
                background: #4c6ef5;
                border-radius: 2px;
                animation: expandWidth 1.5s ease-out 0.5s both;
            }

            @keyframes  expandWidth {
                from { width: 0; }
                to { width: 120px; }
            }

            .section-subtitle {
                font-size: 1.2rem;
                color: #6b7280;
                max-width: 800px;
                margin: 0 auto;
                font-weight: 400;
                opacity: 0;
                animation: fadeInUp 1s ease-out 0.3s both;
            }

            .politique-content {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                padding: 60px;
                box-shadow: 0 10px 30px rgba(76, 110, 245, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.5);
                margin-top: 40px;
                animation: fadeInUp 1s ease-out 0.5s both;
            }

            .politique-item {
                margin-bottom: 50px;
                padding-bottom: 40px;
                border-bottom: 1px solid rgba(76, 110, 245, 0.1);
            }

            .politique-item:last-child {
                margin-bottom: 0;
                border-bottom: none;
                padding-bottom: 0;
            }

            .item-title {
                font-size: 1.8rem;
                font-weight: 600;
                /* color: #4c6ef5; */
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 2px solid rgba(76, 110, 245, 0.2);
            }

            .item-description {
                font-size: 1.1rem;
                color: #4a5568;
                line-height: 1.8;
                font-weight: 400;
            }

            .highlight {
                padding: 2px 6px;
                border-radius: 4px;
                font-weight: 500;
                color: #1a202c;
            }

            .cta-section {
                text-align: center;
                margin-top: 60px;
                padding-top: 40px;
                border-top: 1px solid rgba(76, 110, 245, 0.1);
            }

            .cta-button {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                background: #4c6ef5;
                color: white;
                padding: 15px 30px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.1rem;
                transition: none;

                /* transition: all 0.3s ease; */
                /* box-shadow: 0 4px 15px rgba(76, 110, 245, 0.3); */
            }

            .cta-button:hover {
                /* transform: translateY(-2px); */
                /* box-shadow: 0 8px 25px rgba(76, 110, 245, 0.4); */
                color: white;
                text-decoration: none;
            }

            .footer-offset {
                margin-left: -200px;
                padding-left: 200px;
                background: #f3f4f6;
                border-radius: 40px 0 0 0;
                box-shadow: -10px -10px 30px rgba(76, 110, 245, 0.05);
                position: relative;
                z-index: 1;
            }

            @keyframes  fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes  fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @media (max-width: 1024px) {
                .container {
                    padding: 0 30px;
                }

                .politique-content {
                    padding: 40px;
                }

                .footer-offset {
                    margin-left: 0;
                    padding-left: 0;
                    border-radius: 0;
                }
            }

            @media (max-width: 768px) {
                .section-title {
                    font-size: 2.8rem;
                }

                .section-subtitle {
                    font-size: 1.1rem;
                }

                .container {
                    padding: 0 20px;
                }

                .politique-content {
                    padding: 30px 20px;
                }

                .item-title {
                    font-size: 1.5rem;
                }

                .item-description {
                    font-size: 1rem;
                }
            }
        </style>
    </head>
    <body>
        <section class="politique-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Politique de Réservation</h2>
                    <p class="section-subtitle">
                       - Découvrez nos conditions essentielles pour la validation et l'annulation de vos formations -.
                    </p>
                </div>

                <div class="politique-content">
                    <div class="politique-item">
                        <h3 class="item-title">Validation de Réservation</h3>
                        <div class="item-description">
                            Pour confirmer définitivement votre inscription, il est impératif de <span class="highlight">vous présenter au centre</span> et d'effectuer le règlement complet au minimum <span class="highlight">48 heures avant</span> le commencement de votre première session de formation.
                        </div>
                    </div>

                    <div class="politique-item">
                        <h3 class="item-title">Respect des Délais</h3>
                        <div class="item-description">
                            Le délai de <span class="highlight">2 jours minimum</span> constitue une exigence absolue pour sécuriser votre place dans nos programmes. Toute réservation non validée dans ce délai sera supprimée pour permettre l'inscription d'autres candidats.
                        </div>
                    </div>

                    <div class="politique-item">
                        <h3 class="item-title">Procédure d'Annulation</h3>
                        <div class="item-description">
                            En cas de désistement, nous vous demandons de <span class="highlight">nous informer dans les plus brefs délais</span> par téléphone ou en vous rendant directement à notre centre. Cette démarche nous permet d'optimiser la gestion des places disponibles .
                        </div>
                    </div>

                    <div class="cta-section">
                        <a href="#contact" class="cta-button">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Nous Contacter Immédiatement
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Include Footer Component with offset -->
        <div class="footer-offset">
            <?php echo $__env->make('components.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <script>
            // Animation au scroll simple
            const observerOptions = {
                threshold: 0.2,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        entry.target.classList.add('animate');
                    }
                });
            }, observerOptions);

            // Observer pour les éléments animés
            document.querySelectorAll('.politique-content, .section-header').forEach(item => {
                observer.observe(item);
            });

            // Smooth scroll pour le bouton contact
            document.querySelector('.cta-button').addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector('#contact');
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                } else {
                    window.scrollTo({
                        top: document.body.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            });
        </script>
    </body>
    </html>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\apprendre laravel\Centre_Formation-main\resources\views/admin/apps/home/politique.blade.php ENDPATH**/ ?>