

<?php $__env->startSection('title'); ?>
Formations 
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.apps.formation.common-styles-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid product-wrapper">
    <div class="product-grid">
        <div class="feature-products">
            <div class="row m-b-10">
                <div class="col-md-3 col-sm-4">
                    <div class="d-none-productlist filter-toggle">
                        <h6 class="mb-0">
                            Filtres<span class="ms-2"><i class="toggle-data" data-feather="chevron-down"></i></span>
                        </h6>
                    </div>
                </div>
                <div class="col-md-9 col-sm-8 text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <!-- Aucun bouton d'administration pour les invités -->
                    </div>
                </div>  
            </div>
            
            
        </div>
        
        <div class="product-wrapper-grid">
            <div class="row formations-container">
                <?php $__empty_1 = true; $__currentLoopData = $formations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-xl-3 col-sm-6 xl-4 formation-item">
                        <div class="card h-100">
                            <div class="product-box d-flex flex-column h-100">
                                <div class="product-img" style="height: 200px; overflow: hidden; position: relative;">
                                    <?php if($formation->is_complete): ?>
                                        <div class="ribbon ribbon-danger">Complète</div>
                                    <?php endif; ?>
                                    <?php if($formation->type == 'gratuite'): ?>
                                        <div class="ribbon ribbon-warning">Gratuite</div>
                                    <?php endif; ?>
                                    <?php if($formation->discount > 0): ?>
                                        <div class="ribbon ribbon-success ribbon-right"><?php echo e($formation->discount); ?>%</div>
                                    <?php endif; ?>
                                    <img class="img-fluid" src="<?php echo e(asset('storage/' . $formation->image)); ?>" alt="<?php echo e($formation->title); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                                    <div class="product-hover">
                                        <ul>
                                            <li>
                                                <a href="javascript:void(0)" onclick="showFormationDetails(<?php echo e($formation->id); ?>)" data-bs-toggle="modal" data-bs-target="#formation-modal-<?php echo e($formation->id); ?>">
                                                    <i class="icon-eye"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="add-to-cart" data-formation-id="<?php echo e($formation->id); ?>" >
                                                    <i class="icon-shopping-cart"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <?php echo $__env->make('admin.apps.formation.formation-modal', ['formation' => $formation], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <div class="product-details flex-grow-1 d-flex flex-column p-3">
                                    <div class="card-content flex-grow-1">
                                        <a href="<?php echo e(route('formationshow', $formation->id)); ?>"> 
                                            <h4 class="formation-title" title="<?php echo e($formation->title); ?>"><?php echo e($formation->title); ?></h4>
                                        </a>
                                        <p class="mb-1">Par <?php echo e($formation->user->name); ?> <?php echo e($formation->user->lastname); ?></p>
                                        <div class="rating-wrapper mb-2">
                                            <?php if($formation->average_rating): ?>
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <?php if($i <= floor($formation->average_rating)): ?>
                                                        <i class="fa fa-star text-warning"></i>
                                                    <?php elseif($i == ceil($formation->average_rating) && $formation->average_rating != floor($formation->average_rating)): ?>
                                                        <i class="fa fa-star-half-alt text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star text-muted"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                                <span>(<?php echo e($formation->total_feedbacks); ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="product-price-container">
                                        <div class="product-price mb-0">
                                            <?php if($formation->type == 'payante'): ?>
                                                <?php if($formation->discount > 0): ?>
                                                    <?php echo e(number_format($formation->final_price, 2)); ?> Dt
                                                    <del><?php echo e(number_format($formation->price, 2)); ?> Dt</del>
                                                <?php else: ?>
                                                    <?php echo e(number_format($formation->price, 2)); ?> Dt
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            Aucune formation disponible.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour connexion/inscription -->
<div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authModalLabel">Connexion ou Inscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Vous devez être connecté pour ajouter une formation au panier ou accéder au panier.</p>
                <p>Avez-vous un compte ?</p>
                <div class="d-flex justify-content-around">
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-primary">Se connecter</a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-secondary">S'inscrire</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\hibah\P_Plateforme_ELS\resources\views/admin/apps/formation/formations-guest.blade.php ENDPATH**/ ?>