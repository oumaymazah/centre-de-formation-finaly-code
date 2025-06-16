<?php $__env->startSection('title'); ?>
Détails de la Formation <?php echo e($title); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?php echo e(asset('assets/css/MonCss/formation/training-detail.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Assurez-vous que le CSRF token est inclus -->
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<div class="container-fluid training-detail-container <?php if(!auth()->check()): ?> not-logged-in <?php endif; ?>">

    <div class="formation-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <!-- Bloc Titre -->
                <div class="title-block mb-3">
                    <h2 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-chalkboard me-2 text-white"></i>
                        <?php echo e($formation->title); ?>

                    </h2>
                </div>

                <!-- Bloc Informations (Formateur + Description) -->
                <div class="info-block mb-3">
                    <p class="mb-2 d-flex align-items-center">
                        <i class="fas fa-user-tie me-2 text-white"></i>
                        <strong>Formateur :</strong> <?php echo e($formation->user->name); ?> <?php echo e($formation->user->lastname); ?>

                    </p>
                    <p class="mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-white"></i>
                        <strong>Description :</strong>
                        <span><?php echo $formation->description; ?></span>
                    </p>
                </div>

                <!-- Bloc Feedbacks -->
                <div class="feedback-block">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rating-stars me-2">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= round($averageRating)): ?>
                                    <i class="fa fa-star text-warning"></i>
                                <?php else: ?>
                                    <i class="fa fa-star-o text-muted"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <span><?php echo e(number_format($averageRating, 1)); ?> (<?php echo e($totalFeedbacks); ?> avis)</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="formation-stats">
                    <div class="stat-item">
                        <strong><?php echo e($formation->courses->count()); ?></strong><br>
                        <small>Cours</small>
                    </div>
                    <div class="stat-item">
                        <strong><?php echo e($formation->duration ?? '0'); ?></strong><br>
                        <small>Heures</small>
                    </div>
                    <div class="stat-item">
                        <strong><?php echo e($formation->type == 'gratuite' ? 'Gratuite' : $formation->price . ' Dt'); ?></strong><br>
                        <small>Prix</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Zone de contenu principal -->
        <div class="col-lg-8">
            <div class="content-area" id="lesson-content">
                <div class="text-center py-5">
                    <i class="fa fa-book fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Sélectionnez une leçon pour commencer</h4>
                    <p class="text-muted">Choisissez une leçon dans la structure du cours à droite pour afficher son contenu ici.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar avec structure -->
        <div class="col-lg-4">
            <div class="structure-sidebar">
                <h5 class="mb-3">
                    <i class="fa fa-list-alt me-2"></i>
                    Structure du cours
                </h5>

                <!-- Courses et chapitres -->
                <?php $__currentLoopData = $courseStructure; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="course-item">
                        <div class="course-header" data-course-id="<?php echo e($course['id']); ?>">
                            <span>
                                <i class="fa fa-chevron-right toggle-icon" id="course-icon-<?php echo e($course['id']); ?>"></i>
                                <?php echo e($course['title']); ?>

                            </span>
                            <span class="badge bg-light text-dark"><?php echo e(count($course['chapters'])); ?></span>
                        </div>
                        <div class="course-content" id="course-<?php echo e($course['id']); ?>" style="display: none;">
                            <?php $__currentLoopData = $course['chapters']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="chapter-item">
                                    <div class="chapter-header" data-chapter-id="<?php echo e($chapter['id']); ?>">
                                        <span>
                                            <i class="fa fa-chevron-right toggle-icon" id="chapter-icon-<?php echo e($chapter['id']); ?>"></i>
                                            <?php echo e($chapter['title']); ?>

                                        </span>
                                        <span class="badge bg-secondary"><?php echo e(count($chapter['lessons'])); ?></span>
                                    </div>
                                    <div class="chapter-content" id="chapter-<?php echo e($chapter['id']); ?>" style="display: none;">
                                        <?php $__currentLoopData = $chapter['lessons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="lesson-item" data-lesson-id="<?php echo e($lesson['id']); ?>">
                                                <i class="fa <?php echo e($lesson['has_files'] ? 'fa-file-text' : 'fa-book'); ?> lesson-icon"></i>
                                                <span><?php echo e($lesson['title']); ?></span>
                                                <?php if($lesson['has_files']): ?>
                                                    <i class="fa fa-paperclip ms-auto"></i>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Section Quiz -->
                <?php if($formation->quizzes->count() > 0): ?>
                    <div class="quiz-section">
                        <h6><i class="fa fa-question-circle me-2"></i>Quiz disponibles</h6>
                        <?php $__currentLoopData = $formation->quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="quiz-item">
                                <div>
                                    <strong><?php echo e($quiz->title); ?></strong><br>
                                    <small class="text-muted">
                                        Type: <?php echo e($quiz->type == 'final' ? 'Quiz final' : 'Test de niveau'); ?>

                                    </small>
                                </div>

                                <?php if($canTakeQuiz): ?>
                                    <?php if($quiz->type == 'placement' ): ?>

                                        <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin') || auth()->id() == $formation->user_id): ?>
                                            <form action="<?php echo e(route('admin.quizzes.show', $quiz->id)); ?>" method="GET" style="display: inline;">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-eye"></i> Voir détails
                                                </button>
                                            </form>
                                        <?php elseif(auth()->user()->hasRole('etudiant')): ?>
                                            <form action="<?php echo e(route('quizzes.start', $quiz->id)); ?>" method="POST" style="display: inline;">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa fa-play me-1"></i> Commencer
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                    <?php elseif($quiz->type == 'final'): ?>
                                        <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin') || auth()->id() == $formation->user_id): ?>

                                            <form action="<?php echo e(route('admin.quizzes.show', $quiz->id)); ?>" method="GET" style="display: inline;">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-eye"></i> Voir détails
                                                </button>
                                            </form>
                                        <?php elseif(auth()->user()->hasRole('etudiant') ): ?>
                                            <?php if($hasPaidAccess): ?>
                                                <form action="<?php echo e(route('quizzes.start', $quiz->id)); ?>" method="POST" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fa fa-play me-1"></i>Commencer
                                                    </button>
                                                </form>
                                            <?php else: ?>

                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fa fa-lock me-1"></i> Verrouillé
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="fa fa-lock me-1"></i>Verrouillé
                                    </button>
                                <?php endif; ?>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php if(!auth()->check()): ?>
    <?php ($hideAdminFooter = true); ?>
    <style>
    .footer .container {
        padding-left: 50rem !important; /* Ajustez cette valeur selon vos besoins */
        padding-right: -100rem !important;
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
        padding: 0 10rem !important; /* ⇨ pousse un peu à droite */
    }
    </style>

    <style>
        .formations-container {
            margin-bottom: 0.6rem;
            padding-bottom: 0.6rem;
        }
    </style>

    <style>
        .footer {
            background: #1f2937;
            color: white;
            padding: 2rem 0 2rem;
            width: 100vw !important;
            margin: 0 !important;
            margin-left: -290px !important;
            margin-right: 5px !important;
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
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('assets/js/MonJs/formations/training-detail.js')); ?>" defer></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\apprendre laravel\Centre_Formation-main\resources\views/admin/apps/formation/detail.blade.php ENDPATH**/ ?>