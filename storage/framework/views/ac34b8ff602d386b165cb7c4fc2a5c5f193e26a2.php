 

 <?php $__env->startSection('title'); ?> Liste des Cours
 <?php echo e($title); ?>

 <?php $__env->stopSection(); ?>
 
 <?php $__env->startPush('css'); ?>
 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">
 <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/prism.css')); ?>">
 <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/MonCss/table.css')); ?>">
 <?php $__env->stopPush(); ?>
 
 <?php $__env->startSection('content'); ?>
 <?php $__env->startComponent('components.breadcrumb'); ?>
     <?php $__env->slot('breadcrumb_title'); ?>
         <h3>Liste des Cours</h3>
     <?php $__env->endSlot(); ?>
     <li class="breadcrumb-item">Apps</li>
     <li class="breadcrumb-item active">Liste des Cours</li>
 <?php echo $__env->renderComponent(); ?>
 
 <div class="container-fluid">
     <div class="row">
         <div class="col-md-12">
             <div class="card">
                 <div class="card-header pb-0">
                     <div class="card-header">
                         <h5>Cours Disponibles</h5>
                         <span>Ce tableau affiche la liste des Cours disponibles. Vous pouvez rechercher, trier et paginer les données.</span>
                     </div>
                 </div>
                 <div class="card-body">
                     <!-- Affichage des messages de succès et de suppression avec animation -->
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
                                     <div class="col-md-6 p-0">
                                     </div>
                                     <div class="col-md-6 p-0 text-end">
                                         <a class="btn btn-primary custom-btn" href="<?php echo e(route('courscreate')); ?>">
                                             <i data-feather="plus-square"></i> Ajouter un Cours
                                         </a>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
 
                     <!-- Table pour afficher la liste des cours -->
                     <div class="table-responsive">
                         <table class="display dataTable" id="cours-table">
                             <thead>
                                 <tr>
                                     <th>Title</th>
                                     <th>Description</th>
                                     <th>Date début</th>
                                     <th>Date fin</th>
                                     <th>Formation</th>
                                     <th>Actions</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php $__currentLoopData = $cours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                     <tr>
                                         <td><?php echo e($cour->title); ?></td>
                                         <!-- Afficher la description avec HTML -->
                                         <td><?php echo $cour->description; ?></td>
                                         <td><?php echo e($cour->start_date); ?></td>
                                         <td><?php echo e($cour->end_date); ?></td>
                                         <td>
                                             <?php if($cour->training): ?>
                                                 <a href="<?php echo e(route('formationshow', $cour->training_id)); ?>" class="text-primary">
                                                     <?php echo e($cour->training->title); ?>

                                                 </a>
                                             <?php else: ?>
                                                 Aucune formation
                                             <?php endif; ?>
                                         </td>
                                         <td>
                                             <i class="icofont icofont-edit edit-icon action-icon" data-edit-url="<?php echo e(route('coursedit', $cour->id)); ?>" style="cursor: pointer;"></i>
                                             <i class="icofont icofont-ui-delete delete-icon action-icon" data-delete-url="<?php echo e(route('coursdestroy', $cour->id)); ?>" data-csrf="<?php echo e(csrf_token()); ?>" style="cursor: pointer; color: rgb(204, 28, 28);"></i>
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
 <script src="<?php echo e(asset('assets/js/MonJs/actions-icon/actions-icon.js')); ?>"></script>
 
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <!-- Inclure les scripts de DataTables -->
 <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
 
 <!-- Inclure le fichier JavaScript pour l'initialisation -->
 <script src="<?php echo e(asset('assets/js/MonJs/datatables/datatables.js')); ?>"></script>
 
 <?php $__env->stopPush(); ?>
 <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\apprendre laravel\Centre_Formation-main\resources\views/admin/apps/cours/cours.blade.php ENDPATH**/ ?>