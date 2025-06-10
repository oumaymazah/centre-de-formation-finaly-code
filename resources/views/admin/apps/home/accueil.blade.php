@extends('layouts.admin.master')

@php
    $hideAdminFooter = true;
@endphp

@section('content')
<div class="fullwidth-container">
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>els - Plateforme de Formation</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
            /* Ajoutez cette règle CSS dans votre section <style> de la page "À propos" */

/* Décalage du contenu du footer vers la droite */
.footer .container {
    padding-left: 6% !important; /* Ajustez cette valeur selon vos besoins */
    padding-right: -20rem !important;
}

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
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                overflow-x: hidden;
            }

            /* Hero Section */
            .hero {
                height: 100vh;
                background: linear-gradient(135deg, #ede9fe, #8b82b6);
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                color: white;
                position: relative;
                overflow: hidden;
                width: 100%;
            }

            .hero::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,0 1000,300 1000,1000 0,700"/></svg>');
                background-size: cover;
            }

            .hero-content {
                max-width: 800px;
                padding: 0 2rem;
                position: relative;
                z-index: 2;
            }

            .hero h1 {
                font-size: 3.5rem;
                font-weight: 700;
                margin-bottom: 1.5rem;
                opacity: 0;
                animation: fadeInUp 1s ease 0.5s forwards;
            }

            .hero p {
                font-size: 1.3rem;
                margin-bottom: 2rem;
                opacity: 0;
                animation: fadeInUp 1s ease 0.7s forwards;
            }

            .hero-buttons {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
                opacity: 0;
                animation: fadeInUp 1s ease 0.9s forwards;
            }

            .btn-hero {
                padding: 1rem 2rem;
                font-size: 1.1rem;
                border: 2px solid white;
                color: white;
                text-decoration: none;
                border-radius: 50px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .btn-hero::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: white;
                transition: left 0.3s ease;
                z-index: -1;
            }

            .btn-hero:hover::before {
                left: 0;
            }

            .btn-hero:hover {
                color: #60a5fa;
                transform: translateY(-3px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            }

            /* Features Section */
            .features {
                padding: 5rem 0;
                background: #f8fafc;
                width: 100%;
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
                padding: 0 2rem;
            }


            /* .container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 2rem;
            } */

            .section-title {
                text-align: center;
                margin-bottom: 3rem;
            }

            .section-title h2 {
                font-size: 2.5rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 1rem;
            }

            .section-title p {
                font-size: 1.2rem;
                color: #6b7280;
                max-width: 600px;
                margin: 0 auto;
            }

            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 2rem;
                margin-top: 3rem;
            }

            .feature-card {
                background: white;
                padding: 2.5rem;
                border-radius: 20px;
                text-align: center;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .feature-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #2563EB, #a78bfa);
                transform: scaleX(0);
                transition: transform 0.3s ease;
            }

            .feature-card:hover::before {
                transform: scaleX(1);
            }

            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }

            .feature-icon {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #2563EB, #a78bfa);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.5rem;
                color: white;
                font-size: 2rem;
            }

            .feature-card h3 {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 1rem;
                color: #1f2937;
            }

            .feature-card p {
                color: #6b7280;
                line-height: 1.6;
            }

            /* CTA Section */
            .cta {
                padding: 5rem 0;
                /* background: linear-gradient(135deg, #ede9fe, #8b82b6); */

                /* background: linear-gradient(135deg, #c7d7f5, #a3baf2); */
                color: white;
                text-align: center;
                width: 100%;
            }

            .cta h2 {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
            }

            .cta p {
                font-size: 1.2rem;
                margin-bottom: 2rem;
                opacity: 0.9;
            }

            .btn-cta {
                background: white;
                color: #2563EB;
                padding: 1rem 2rem;
                border-radius: 50px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1.1rem;
                transition: all 0.3s ease;
                display: inline-block;
            }

            .btn-cta:hover {
                /* transform: translateY(-3px) scale(1.05);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2); */
            }

            /* Animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(50px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-on-scroll {
                opacity: 0;
                transform: translateY(50px);
                transition: all 0.6s ease;
            }

            .animate-on-scroll.animate {
                opacity: 1;
                transform: translateY(0);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .hero h1 {
                    font-size: 2.5rem;
                }

                .hero p {
                    font-size: 1.1rem;
                }

                .hero-buttons {
                    flex-direction: column;
                    align-items: center;
                }

                .section-title h2 {
                    font-size: 2rem;
                }

                .features-grid {
                    grid-template-columns: 1fr;
                }

                .cta h2 {
                    font-size: 2rem;
                }
            }

            /* Floating Animation */
            .floating {
                animation: floating 3s ease-in-out infinite;
            }

            @keyframes floating {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }

            /* Gradient Text */
            .gradient-text {
                background: linear-gradient(135deg, #2563EB, #a78bfa);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    </head>
    <body>
        <!-- Hero Section -->
        <section id="accueil" class="hero">
            <div class="hero-content">
                <h1 class="gradient-text">Transformez votre avenir avec nos formations</h1>
                <p>Découvrez une nouvelle façon d'apprendre avec des formations de qualité, adaptées à vos besoins et à votre rythme.</p>
                <div class="hero-buttons">
                    <a href="formations" class="btn-hero">Voir nos formations</a>
                    <a href="ÀPropos" class="btn-hero">En savoir plus</a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="container">
                <div class="section-title animate-on-scroll">
                    <h2>Pourquoi choisir <span class="gradient-text">ELS</span> ?</h2>
                    <p>Rejoignez une communauté d'apprenants ambitieux et accédez à des formations de pointe conçues pour votre réussite professionnelle.</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card animate-on-scroll floating">
                        <div class="feature-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Formations Certifiantes</h3>
                        <p>Obtenez des certifications reconnues par l'industrie et valorisez vos compétences sur le marché du travail.</p>
                    </div>
                    <div class="feature-card animate-on-scroll floating" style="animation-delay: 0.5s;">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Experts Qualifiés</h3>
                        <p>Apprenez auprès de professionnels expérimentés qui partagent leur expertise et leur passion.</p>
                    </div>
                    <div class="feature-card animate-on-scroll floating" style="animation-delay: 1s;">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Flexibilité Totale</h3>
                        <p>Étudiez à votre rythme, où que vous soyez, avec un accès 24/7 à nos contenus pédagogiques.</p>
                    </div>
                    <div class="feature-card animate-on-scroll floating" style="animation-delay: 2s;">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Support Personnalisé</h3>
                        <p>Bénéficiez d'un accompagnement individuel et d'un support technique réactif tout au long de votre parcours.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <h2  class="gradient-text">Prêt à commencer votre parcours ?</h2>
<p style="color: black;">Rejoignez des milliers d'apprenants qui ont déjà transformé leur carrière grâce à nos formations</p>
                <a href="register" class="btn-cta">Commencer maintenant</a>
            </div>
        </section>

        <!-- Include Footer Component -->
        @include('components.footer')

        <script>
            // Smooth scrolling for navigation links
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

            // Animate elements on scroll
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

            // Initialize animations
            window.addEventListener('scroll', animateOnScroll);
            window.addEventListener('load', () => {
                animateOnScroll();
            });
        </script>
    </body>
    </html>
</div>
@endsection
