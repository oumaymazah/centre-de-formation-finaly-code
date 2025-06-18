

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="viho admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities. laravel/framework: ^8.40">
    <meta name="keywords" content="admin template, viho admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="{{asset('assets/images/favicon1.png')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset('assets/images/favicon1.png')}}" type="image/x-icon">
    <title>@yield('title')</title>
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <!-- Font Awesome-->
    @includeIf('layouts.admin.partials.css')
  </head>
  <body>
    <!-- Loader starts-->
    <div class="loader-wrapper">
      <div class="theme-loader"></div>
    </div>
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
@guest

    <div class="" id="pageWrapper">
    @endguest
    @auth

    <div class="page-wrapper compact-sidebar" id="pageWrapper">
    @endauth      <!-- Page Header Start-->
      @includeIf('layouts.admin.partials.header')


      <!-- Page Header Ends -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper sidebar-icon">
        <!-- Page Sidebar Start-->
        @includeIf('layouts.admin.partials.sidebar')
        <!-- Page Sidebar Ends-->
        <div class="page-body">
          <!-- Container-fluid starts-->
          @yield('content')
          <!-- Container-fluid Ends-->
        </div>

        <!-- Footer conditionnel -->
        @guest

<footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>EMPOWERMENT LEARNING SUCCESS</h3>
                <p>Votre partenaire pour l'excellence en formation. Nous vous accompagnons dans votre développement professionnel avec des formations de qualité.</p>
            </div>
            <div class="footer-section">
                <h3>Liens Rapides</h3>
                <p><a href="accueil">Accueil</a></p>
                <p><a href="ÀPropos">À propos</a></p>
                <p><a href="formations">Formations</a></p>
                <p><a href="politique">Politique de réservation</a></p>
            </div>
            <div class="footer-section">
                <h3>Formations</h3>
                <?php
                // Récupérer les 4 premières catégories avec au moins une formation publiée
                $categories = App\Models\Category::withCount(['trainings' => function ($query) {
                    $query->where('status', 1);
                }])
                    ->whereHas('trainings', function ($query) {
                        $query->where('status', 1);
                    })
                    ->take(4)
                    ->get();

                foreach ($categories as $category) {
                    $categoryTitle = htmlspecialchars($category->title, ENT_QUOTES, 'UTF-8');
                    $formationsUrl = url('formations') . '?category_title=' . urlencode($categoryTitle);
                    // Solution simple : utiliser seulement le lien href sans JavaScript
                    echo "<p><a href='{$formationsUrl}' class='footer-formation-link' data-category-title='{$categoryTitle}'>{$categoryTitle}</a></p>";
                }
                ?>
            </div>
            <div class="footer-section" id="contact">
                <h3>Contact</h3>
                <p>
                    <i class="fas fa-envelope" style="margin-right: 8px; color: #60a5fa;"></i>
                    <a href="mailto:els.center2022@gmail.com">els.center2022@gmail.com</a>
                </p>
                <p>
                    <i class="fas fa-phone" style="margin-right: 8px; color: #60a5fa;"></i>
                    <a href="tel:+21652450193">52450193</a> / <a href="tel:+21621272129">21272129</a>
                </p>
                <p>
                    <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #60a5fa;"></i>
                    <a href="https://www.google.com/maps/search/?api=1&query=Rue+El+Farabi+Sousse+Tunisia" target="_blank">
                        Rue farabi trocadéro, immeuble kraiem 1 étage
                    </a>
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-left">
                <span>Copyright 2025-2026 © ELS Centre de Formation en Ligne. Tous droits réservés.</span>
            </div>
            <div class="footer-bottom-right">
                <span>Conçu avec passion pour votre réussite professionnelle</span>
            </div>
        </div>
    </footer>
        @endguest
      </div>
    </div>
      <style>
        .footer {
            background: #1f2937;
            color: white;
           
            width: 100vw !important;
            margin: 0 !important;

            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.7;
            color: #1a202c;
            /* display: none;  */
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 5rem;
            margin: 0 !important;
            padding: 0 5rem !important;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #60a5fa;
        }

        .footer-section p,
        .footer-section a {
            color: #d1d5db;
            text-decoration: none;
            line-height: 1.8;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #60a5fa;
        }

        .footer-bottom {
            border-top: 1px solid #374151;
            padding: 1rem 2rem 0 2rem !important;
            text-align: right;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-bottom-left {
            flex: 1;
            text-align: right;
            min-width: 0;
            padding-right: 2rem;
        }

        .footer-bottom-right {
            flex: 1;
            padding-right: -5rem;
            margin-right: 3rem;
        }

        @media (max-width: 768px) {
            .footer-content {
                padding: 0 1rem;
            }

            .footer-bottom {
                padding: 2rem 1rem 0 1rem;
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .footer-bottom-left,
            .footer-bottom-right {
                text-align: center;
            }
        }
    </style>
    <!-- latest jquery-->
    @includeIf('layouts.admin.partials.js')
  </body>
</html>
