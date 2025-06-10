
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/select2.css')); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/owlcarousel.css')); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/range-slider.css')); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
    .rating-label {
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }
    .rating-label .fa-star,
    .rating-label .fa-star-half-alt,
    .rating-label .far.fa-star {
        font-size: 12px;
        margin-right: 1px;
        color: #d2691e;
    }
    .rating-label .far.fa-star,
    .rating-label .fa-star-half-alt {
        color: #c66a00;
    }
    .product-filter .checkbox-animated label {
        cursor: pointer;
        padding: 6px 0;
        border-bottom: 1px solid #f0f0f0;
        align-items: center;
        font-size: 14px;
    }
    .product-filter .checkbox-animated label:last-child {
        border-bottom: none;
    }
    .product-filter .checkbox-animated label:hover {
        background-color: #f8f9fa;
        padding-left: 5px;
        transition: all 0.2s ease;
    }
    .categories-toggle, .ratings-toggle {
        cursor: pointer;
        padding: 10px;
        margin-bottom: 0;
    }
    .categories-content, .ratings-content {
        transition: all 0.3s ease;
    }
    .search-indicator {
        font-size: 0.8em;
        color: #0d6efd;
        margin-left: 10px;
    }
    .search-clear-icon {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .product-search .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .product-search .form-group {
        position: relative;
    }
</style>
<script>
    let userRoles = [];
    <?php if(auth()->check()): ?>
        try {
            userRoles = <?php echo json_encode(auth()->user()->roles->pluck('name')->toArray(), 15, 512) ?>;
            console.log("Rôles chargés:", userRoles);
        } catch(e) {
            console.error("Erreur lors du chargement des rôles:", e);
        }
    <?php else: ?>
        userRoles = ['guest'];
        console.log("Utilisateur non connecté, rôle défini comme 'guest'");
    <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="<?php echo e(asset('assets/js/range-slider/ion.rangeSlider.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/range-slider/rangeslider-script.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/touchspin/vendors.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/touchspin/touchspin.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/touchspin/input-groups.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/owlcarousel/owl.carousel.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/select2/select2.full.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/select2/select2-custom.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tooltip-init.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/product-tab.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/formations/feedback.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/formations/formations-cards.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/formations/formation-button-layouts.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/formations.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/toast/toast.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/formations/panier.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/formations/reservation.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/cart.js')); ?>"></script>
<?php $__env->stopPush(); ?>


<div class="row">
    <div class="col-md-12">
        <div class="pro-filter-sec">
            <div class="product-sidebar">
                <div class="filter-section">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0 f-w-600">
                                Filtres<span class="pull-right"><i class="fa fa-chevron-down toggle-data"></i></span>
                            </h6>
                        </div>
                        <div class="left-filter">
                            <div class="card-body filter-cards-view animate-chk">
                                <!-- Filtrage par Catégories -->
                                <div class="product-filter">
                                    <h6 class="f-w-600 categories-toggle">Catégories <span class="pull-right"><i class="fa fa-chevron-down"></i></span></h6>
                                    <div class="checkbox-animated mt-0 categories-content" style="display: none;">
                                        <label class="d-flex align-items-center" for="category-all">
                                            <input class="radio_animated me-2" id="category-all" type="radio" name="category_filter" value="" <?php echo e(!request()->has('category_id') || request()->category_id === null || request()->category_id === '' ? 'checked' : ''); ?>/>
                                            Toutes les catégories
                                        </label>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="d-flex align-items-center" for="category-<?php echo e($category->id); ?>">
                                                <input class="radio_animated me-2" id="category-<?php echo e($category->id); ?>" type="radio" name="category_filter" value="<?php echo e($category->id); ?>" <?php echo e(request()->category_id == $category->id ? 'checked' : ''); ?>/>
                                                <?php echo e($category->title); ?> (<?php echo e($category->trainings_count); ?>)
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <!-- Filtrage par Évaluations -->
                                <div class="product-filter">
                                    <h6 class="f-w-600 ratings-toggle">Évaluations <span class="pull-right"><i class="fa fa-chevron-down"></i></span></h6>
                                    <div class="checkbox-animated mt-0 ratings-content" style="display: none;">
                                        <label class="d-flex align-items-center" for="rating-all">
                                            <input class="radio_animated me-2" id="rating-all" type="radio" name="rating_filter" value="" <?php echo e(!request()->has('rating') || request()->rating === null || request()->rating === '' ? 'checked' : ''); ?>/>
                                            Toutes les évaluations
                                        </label>
                                        <?php $__currentLoopData = [1, 2, 2.5, 3, 3.5, 4, 4.5, 5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="d-flex align-items-center" for="rating-<?php echo e($rating); ?>">
                                                <input class="radio_animated me-2" id="rating-<?php echo e($rating); ?>" type="radio" name="rating_filter" value="<?php echo e($rating); ?>" <?php echo e(request()->rating == $rating ? 'checked' : ''); ?>/>
                                                <span class="rating-label">
                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                        <?php if($i <= floor($rating)): ?>
                                                            <i class="fa fa-star"></i>
                                                        <?php elseif($i == ceil($rating) && $rating != floor($rating)): ?>
                                                            <i class="fa fa-star-half-alt"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-star"></i>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                    <?php echo e($rating == 5 ? '5' : 'À partir de ' . $rating); ?>

                                                </span>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="product-search">
                <form onsubmit="return false;" autocomplete="off">
                    <div class="form-group m-0">
                        <input class="form-control" type="search"
                            placeholder="Rechercher..."
                            data-original-title=""
                            title=""
                            id="search-formations"
                            autocomplete="off" />
                        <i class="fa fa-search search-clear-icon"></i>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\hibah\P_Plateforme_ELS\resources\views/admin/apps/formation/common-styles-scripts.blade.php ENDPATH**/ ?>