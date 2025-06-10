<?php $__env->startSection('title'); ?> Liste des Chapitres
<?php echo e($title); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/prism.css')); ?>">
<style>
    .highlighted {
        background-color: #ffeb3b !important; /* Couleur de surbrillance */
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
        <h3>Liste des Chapitres</h3>
    <?php $__env->endSlot(); ?>
    <li class="breadcrumb-item">Chapitres</li>
    <li class="breadcrumb-item active">Liste des Chapitres</li>
<?php echo $__env->renderComponent(); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Chapitres Disponibles</h5>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success" id="success-message">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('delete')): ?>
                        <div class="alert alert-danger" id="delete-message">
                            <?php echo e(session('delete')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="row project-cards">
                        <div class="col-md-12 project-list">
                            <div class="card">
                                <div class="row">
                                    <div class="col-md-6 p-0"></div>
                                    <div class="col-md-6 p-0">
                                        <a class="btn btn-primary custom-btn" href="<?php echo e(route('chapitrecreate')); ?>">
                                            <i data-feather="plus-square"></i> Ajouter un Chapitre
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="display dataTable" id="chapitres-table">
                            <thead>
                                <tr>
                                    <th>title</th>
                                    <th>Description</th>
                                    <th>Durée</th>
                                    <th>Cours</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $chapitres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chapitre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($chapitre->title); ?></td>
                                        <td><?php echo $chapitre->description; ?></td>
                                        <td><?php echo e($chapitre->duration); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('cours', ['selected_cours' => $chapitre->Course->id])); ?>" class="cours-link" data-cours-id="<?php echo e($chapitre->Course->id); ?>">
                                                <?php echo e($chapitre->Course->title); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <i class="icofont icofont-edit edit-icon action-icon" data-edit-url="<?php echo e(route('chapitreedit', $chapitre->id)); ?>" style="cursor: pointer;"></i>
                                            <i class="icofont icofont-ui-delete delete-icon action-icon" data-delete-url="<?php echo e(route('chapitredestroy', $chapitre->id)); ?>" data-csrf="<?php echo e(csrf_token()); ?>" style="cursor: pointer; color: rgb(204, 28, 28);"></i>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/js/prism/prism.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/clipboard/clipboard.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/custom-card/custom-card.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/height-equal.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs//actions-icon/actions-icon.js')); ?>"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo e(asset('assets/js/MonJs/datatables/datatables.js')); ?>"></script>

<script>
    $(document).ready(function () {
        // Récupérer l'ID du cours sélectionné depuis l'URL
        let selectedCoursId = new URLSearchParams(window.location.search).get('selected_cours');

        if (selectedCoursId) {
            $('.cours-link').each(function () {
                if ($(this).data('cours-id') == selectedCoursId) {
                    $(this).addClass('highlighted');
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\hibah\P_Plateforme_ELS\resources\views/admin/apps/chapitre/chapitres.blade.php ENDPATH**/ ?>