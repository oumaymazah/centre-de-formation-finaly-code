
<?php if(auth()->guard()->check()): ?>

<header class="main-nav">

    <div class="sidebar-user text-center">
        <a class="setting-primary" href="<?php echo e(route('profile.parametre')); ?>">
            <i data-feather="settings"></i>
        </a>
        <div class="mb-4">
            <div class="avatar-circle text-white mx-auto" style="background-color:  #2B6ED4; width: 80px; height: 80px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 28px; font-weight: bold;">
                <span><?php echo e(substr(auth()->user()->name, 0, 1)); ?><?php echo e(substr(auth()->user()->lastname, 0, 1)); ?></span>
            </div>
        </div>

        <a style="color:  #2B6ED4">
            <h6 class="mt-3 f-14 f-w-600"><?php echo e(auth()->user()->name); ?> <?php echo e(auth()->user()->lastname); ?> </h6>
        </a>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="nav-menu custom-scrollbar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>



                    <li class="sidebar-main-title">
                        <div>
                            <h6>Contenu Educatif</h6>
                        </div>
                    </li>
                    <li class="dropdown">
                          <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')): ?>

                         <a class="nav-link menu-title <?php echo e(prefixActive('/categorie')); ?>" href="javascript:void(0)"><i data-feather="box"></i><span>Categories </span></a>
                         <ul class="nav-submenu menu-content" style="display: <?php echo e(prefixBlock('/categorie')); ?>;">
                             <li><a href="<?php echo e(route('categories')); ?>" class="<?php echo e(routeActive('categories')); ?>">Liste de Categories</a></li>
                             <li><a href="<?php echo e(route('categoriecreate')); ?>" class="<?php echo e(routeActive('categoriecreate')); ?>">Nouvelle Catégorie </a></li>
                         </ul>
                        <?php endif; ?>

                         
                         <a class="nav-link menu-title <?php echo e(prefixActive('/formation')); ?>" href="javascript:void(0)"><i data-feather="box"></i><span>Formations </span></a>
                         <ul class="nav-submenu menu-content" style="display: <?php echo e(prefixBlock('/formation')); ?>;">
                             <li><a href="<?php echo e(route('formations')); ?>" class="<?php echo e(routeActive('formations')); ?>">Liste de formations</a></li>
                                <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')): ?>

                             <li><a href="<?php echo e(route('formationcreate')); ?>" class="<?php echo e(routeActive('formationcreate')); ?>">Nouvelle Formation  </a></li>
                                <?php endif; ?>
                            </ul>

                         <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')|| auth()->user()->hasRole('professeur')): ?>

                         
                         <a class="nav-link menu-title <?php echo e(prefixActive('/cours')); ?>" href="javascript:void(0)"><i data-feather="box"></i><span>Cours </span></a>
                         <ul class="nav-submenu menu-content" style="display: <?php echo e(prefixBlock('/cours')); ?>;">
                             <li><a href="<?php echo e(route('cours')); ?>" class="<?php echo e(routeActive('cours')); ?>">Liste de cours</a></li>
                             <li><a href="<?php echo e(route('courscreate')); ?>" class="<?php echo e(routeActive('courscreate')); ?>">Nouveau Cours </a></li>
                         </ul>
                         

                         <a class="nav-link menu-title <?php echo e(prefixActive('/chapitre')); ?>" href="javascript:void(0)"><i data-feather="box"></i><span>Chapitres </span></a>
                         <ul class="nav-submenu menu-content" style="display: <?php echo e(prefixBlock('/chapitre')); ?>;">
                             <li><a href="<?php echo e(route('chapitres')); ?>" class="<?php echo e(routeActive('chapitres')); ?>">Liste de chapitres</a></li>
                             <li><a href="<?php echo e(route('chapitrecreate')); ?>" class="<?php echo e(routeActive('chapitrecreate')); ?>">Nouveau chapitre </a></li>
                         </ul>

                          
                          <a class="nav-link menu-title <?php echo e(prefixActive('/lesson')); ?>" href="javascript:void(0)"><i data-feather="box"></i><span>Leçons </span></a>
                          <ul class="nav-submenu menu-content" style="display: <?php echo e(prefixBlock('/lesson')); ?>;">
                              <li><a href="<?php echo e(route('lessons')); ?>" class="<?php echo e(routeActive('lessons')); ?>">Liste de Leçons</a></li>
                              <li><a href="<?php echo e(route('lessoncreate')); ?>" class="<?php echo e(routeActive('lessoncreate')); ?>">Nouvelle leçon </a></li>
                          </ul>
                        <?php endif; ?>
                        <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|super-admin')): ?>
                                <a class="nav-link menu-title <?php echo e(prefixActive('/quizzes')); ?>" href="javascript:void(0)">
                                    <i data-feather="box"></i>
                                    <span>Quiz</span>
                                </a>
                                <ul class="nav-submenu menu-content" style="display: <?php echo e(prefixBlock('/quizzes')); ?>;">
                                    <li><a href="<?php echo e(route('admin.quizzes.index')); ?>" class="<?php echo e(routeActive('admin.quizzes.index')); ?>">Liste des Quiz</a></li>
                                    <li><a href="<?php echo e(route('admin.quizzes.create')); ?>" class="<?php echo e(routeActive('admin.quizzes.create')); ?>">Nouveau Quiz</a></li>
                                </ul>
                                <a class="nav-link menu-title <?php echo e(prefixActive('/feedbacks')); ?>" href="javascript:void(0)">
                                    <i data-feather="box"></i>
                                    <span>Avis</span>
                                </a>
                                <ul class="nav-submenu menu-content" style="display: <?php echo e(prefixBlock('/feedbacks')); ?>;">
                                    <li><a href="<?php echo e(route('feedbacks')); ?>" class="<?php echo e(routeActive('feedbacks')); ?>">Liste des Avis</a></li>

                                </ul>
                        <?php endif; ?>

                    </li>
                    <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')): ?>

                        <li class="sidebar-main-title">
                            <div>
                                <h6 style="font-size: 16px; ">Administration des comptes</h6>
                            </div>
                        </li>
                        <li class="dropdown">

                                <a class="nav-link menu-title link-nav <?php echo e(routeActive('contacts')); ?>" href="<?php echo e(route('contacts')); ?>"><i data-feather="list"></i><span>Gestion des utilisateurs</span></a>

                        </li>
                    <?php endif; ?>






                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>
<?php endif; ?>
<?php /**PATH D:\apprendre laravel\Centre_Formation-main\resources\views/layouts/admin/partials/sidebar.blade.php ENDPATH**/ ?>