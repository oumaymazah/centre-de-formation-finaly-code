
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
        <title>À propos - ELS Centre de Formation</title>
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
            }

            /* Container modifié pour pleine largeur */
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

            /* About Page Styles */
            .about-hero {
                background: linear-gradient(135deg, #ede9fe, #8b82b6);
                padding: 6rem 0 4rem;
                text-align: center;
                color: white;
                width: 100%;
            }

            .about-hero h1 {
                font-size: 3rem;
                font-weight: 700;
                margin-bottom: 1rem;
            }

            .about-hero p {
                font-size: 1.2rem;
                max-width: 600px;
                margin: 0 auto;
            }

            .about-content {
                padding: 5rem 0;
                width: 100%;
                background: #ffffff;
            }

            .about-section {
                margin-bottom: 4rem;
                width: 100%;
            }

            .about-text {
                background: white;
                padding: 3rem;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                font-size: 1.1rem;
                line-height: 1.8;
                text-align: justify;
                margin: 0 auto 3rem;
                max-width: 1200px;
            }

            /* Mission Vision - Pleine largeur */
            .mission-vision {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 3rem;
                margin: 4rem 0;
                width: 100%;
                max-width: 1400px;
                margin-left: auto;
                margin-right: auto;
                padding: 0 2rem;
            }

            .mission-card, .vision-card {
                background: white;
                padding: 3rem;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                text-align: center;
                transition: transform 0.3s ease;
            }

            .mission-card:hover, .vision-card:hover {
                transform: translateY(-10px);
            }

            .mission-card {
                border-top: 4px solid #2563EB;
            }

            .vision-card {
                border-top: 4px solid #a78bfa;
            }

            .mission-card h3, .vision-card h3 {
                font-size: 1.8rem;
                font-weight: 700;
                margin-bottom: 1.5rem;
                color: #1f2937;
            }

            .mission-card .icon, .vision-card .icon {
                font-size: 3rem;
                margin-bottom: 1.5rem;
            }

            .mission-card .icon {
                color: #2563EB;
            }

            .vision-card .icon {
                color: #a78bfa;
            }

            /* Values Section - Pleine largeur */
            .values-section {
                padding: 5rem 0;
                background: #f8fafc;
                width: 100%;
            }

            .values-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 2rem;
                margin-top: 3rem;
                max-width: 1400px;
                margin-left: auto;
                margin-right: auto;
                padding: 0 2rem;
            }

            .value-card {
                background: white;
                padding: 2rem;
                border-radius: 15px;
                text-align: center;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .value-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            }

            .value-card .icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: #2563EB;
            }

            .value-card h4 {
                font-size: 1.3rem;
                font-weight: 600;
                margin-bottom: 1rem;
                color: #1f2937;
            }

            .section-title {
                text-align: center;
                margin-bottom: 3rem;
                padding: 0 2rem;
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

            /* Partnerships Section - Pleine largeur */
            .partnerships-section {
                background: #f8fafc;
                padding: 4rem 0;
                margin: 3rem 0;
                overflow: hidden;
                width: 100%;
            }

            .partnerships-title {
                text-align: center;
                margin-bottom: 3rem;
                padding: 0 2rem;
            }

            .partnerships-title h2 {
                font-size: 2.5rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 1rem;
                background: linear-gradient(135deg, #2563EB, #a78bfa);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .partnerships-title p {
                font-size: 1.1rem;
                color: #6b7280;
                max-width: 600px;
                margin: 0 auto;
            }

            /* Carrousel Container */
            .partnerships-carousel {
                position: relative;
                width: 100%;
                overflow: hidden;
                background: transparent;
                padding: 2rem 0;
            }

            /* Track du carrousel */
            .partnerships-track {
                display: flex;
                width: calc(200px * 12);
                animation: scroll 20s linear infinite;
                gap: 2rem;
            }

            /* Animation de défilement */
            @keyframes scroll {
                0% {
                    transform: translateX(0);
                }
                100% {
                    transform: translateX(calc(-200px * 6 - 2rem * 6));
                }
            }

            /* Pause au survol */
            .partnerships-carousel:hover .partnerships-track {
                animation-play-state: paused;
            }

            /* Style des logos */
            .partnership-logo {
                background: white;
                padding: 1.5rem;
                border-radius: 15px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                width: 200px;
                height: 120px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                cursor: pointer;
            }

            .partnership-logo:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            }

            .partnership-logo img {
                max-width: 100%;
                max-height: 80px;
                object-fit: contain;
                filter: grayscale(0.3);
                transition: filter 0.3s ease;
            }

            .partnership-logo:hover img {
                filter: grayscale(0);
            }

            /* Responsive Design */
            @media (max-width: 1200px) {
                .values-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .mission-vision {
                    grid-template-columns: 1fr;
                    gap: 2rem;
                }
            }

            @media (max-width: 768px) {
                .about-hero h1 {
                    font-size: 2rem;
                }

                .about-text {
                    padding: 2rem;
                }

                .values-grid {
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                }

                .mission-vision {
                    grid-template-columns: 1fr;
                    gap: 2rem;
                    padding: 0 1rem;
                }

                .section-title h2 {
                    font-size: 2rem;
                }

                .partnerships-title h2 {
                    font-size: 2rem;
                }

                .partnerships-track {
                    animation-duration: 15s;
                }

                .partnership-logo {
                    width: 160px;
                    height: 100px;
                    padding: 1rem;
                }

                .values-section {
                    padding: 3rem 0;
                }

                .content-container {
                    padding: 0 1rem;
                }
            }

            @media (max-width: 480px) {
                .partnership-logo {
                    width: 140px;
                    height: 90px;
                }

                .partnerships-track {
                    width: calc(140px * 12);
                    animation-duration: 12s;
                }

                @keyframes scroll {
                    0% {
                        transform: translateX(0);
                    }
                    100% {
                        transform: translateX(calc(-140px * 6 - 2rem * 6));
                    }
                }
            }
        </style>
    </head>
    <body>
        <!-- Hero Section -->
        <section class="about-hero">
            <div class="content-container">
                <h1>À propos d'ELS</h1>
                <p> - Empowerment Learning Success - <br> Votre partenaire pour l'excellence en formation</p>
            </div>
        </section>

        <!-- About Content -->
        <section class="about-content">
            <div class="content-container">
                <!-- About Text -->
                <div class="about-section">
                    <div class="about-text">
                        Nous sommes un centre de formation spécialisé dans l'accompagnement des individus et des entreprises pour le développement des compétences et l'atteinte de leurs objectifs. Grâce à une approche adaptée et à des formateurs expérimentés, nous proposons des programmes variés dans des domaines clés, visant à renforcer les capacités professionnelles, encourager l'innovation et favoriser une gestion efficace des défis. Notre mission est d'offrir des solutions concrètes pour permettre à chacun de se dépasser et de construire un avenir professionnel solide.
                    </div>
                </div>
            </div>

            <!-- Mission & Vision - Pleine largeur -->
            <div class="mission-vision">
                <div class="mission-card">
                    <div class="icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Notre Mission</h3>
                    <p>Accompagner les individus et les entreprises dans leur développement professionnel en offrant des formations de qualité, adaptées aux défis du marché du travail actuel. Nous nous engageons à être le pont entre les aspirations de nos apprenants et leurs objectifs professionnels.</p>
                </div>

                <div class="vision-card">
                    <div class="icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Notre Vision</h3>
                    <p>Devenir le centre de formation de référence en proposant des solutions innovantes et personnalisées qui transforment les compétences en véritables atouts professionnels. Nous aspirons à créer un écosystème d'apprentissage qui favorise l'excellence et l'épanouissement.</p>
                    </div>
            </div>
        </section>

        <!-- Values Section - Pleine largeur -->
        <div class="values-section">
            <div class="section-title">
                <h2>Nos Valeurs</h2>
                <p>Les principes qui guident notre approche pédagogique et notre engagement envers vos succès</p>
            </div>

            <div class="values-grid">
                <div class="value-card">
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>Excellence</h4>
                    <p>Nous visons l'excellence dans chaque formation proposée, avec des contenus actualisés et des méthodes pédagogiques innovantes.</p>
                </div>

                <div class="value-card">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Accompagnement</h4>
                    <p>Un suivi personnalisé et un soutien constant pour garantir la réussite de chaque apprenant dans son parcours.</p>
                </div>

                <div class="value-card">
                    <div class="icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4>Innovation</h4>
                    <p>Nous encourageons la créativité et l'innovation pour préparer nos apprenants aux défis de demain.</p>
                </div>

                <div class="value-card">
                    <div class="icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4>Partenariat</h4>
                    <p>Nous construisons des relations durables avec nos apprenants et partenaires, basées sur la confiance mutuelle.</p>
                </div>
            </div>
        </div>

        <!-- Partnerships Section - Pleine largeur -->
        <div class="partnerships-section">
            <div class="partnerships-title">
                <h2>Partenariats et Collaborations</h2>
                <p>Nous collaborons avec des organisations prestigieuses pour enrichir notre offre de formation</p>
            </div>

            <div class="partnerships-carousel">
                <div class="partnerships-track">
                    <!-- Premier set de logos -->
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/unimed.png')}}" alt="UNIMED" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/ma3an.png')}}" alt="MA3AN" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/map.png')}}" alt="MAP" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/meduse.png')}}" alt="MEDUSE" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/unsaid.png')}}" alt="USAID" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/awledona.jpg')}}" alt="Awled Ona" />
                    </div>

                    <!-- Duplication pour boucle infinie -->
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/unimed.png')}}" alt="UNIMED" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/ma3an.png')}}" alt="MA3AN" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/map.png')}}" alt="MAP" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/meduse.png')}}" alt="MEDUSE" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/unsaid.png')}}" alt="USAID" />
                    </div>
                    <div class="partnership-logo">
                        <img src="{{asset('assets/images/colab/awledona.jpg')}}" alt="Awled Ona" />
                    </div>
                </div>
            </div>
        </div>
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

        <!-- Inclusion du composant footer -->
        @include('components.footer')
    </body>
    </html>
</div>
@endsection
