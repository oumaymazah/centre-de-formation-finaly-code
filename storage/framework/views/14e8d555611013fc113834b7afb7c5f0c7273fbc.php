<?php $__env->startSection('title'); ?> Modifier un Cours <?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/dropzone.css')); ?>">
    <link href="<?php echo e(asset('assets/css/MonCss/custom-style.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/MonCss/SweatAlert2.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/MonCss/custom-calendar.css')); ?>">

<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Modifier un cours</h5>
                    </div>

                    <div class="card-body">
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if(session('success')): ?>
                            <div class="alert alert-success">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>

                        <div class="form theme-form">
                            <form action="<?php echo e(route('coursupdate', $cours->id)); ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                <!-- Titre -->
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Titre <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-tag"></i></span>
                                            <input class="form-control" type="text" id="title" name="title" placeholder="Titre" value="<?php echo e(old('title', $cours->title)); ?>" required />
                                        </div>
                                        <div class="invalid-feedback">Veuillez entrer un titre valide.</div>
                                    </div>
                                </div>

                                <!-- Description -->

                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Description</label>
                                    <div class="col-sm-10">
                                        <div class="input-group" style="flex-wrap: nowrap;">
                                            <div class="input-group-text d-flex align-items-stretch" style="height: auto;">
                                                <i class="fa fa-align-left align-self-center"></i>
                                            </div>
                                            <textarea class="form-control" id="description" name="description" placeholder="Description" ><?php echo e(old('description',$cours->description)); ?></textarea>
                                        </div>
                                        <div class="invalid-feedback">Veuillez entrer une description valide.</div>
                                    </div>
                                </div>


                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Périodes <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    <input class="form-control datepicker" type="text" id="start_date" name="start_date"
                                                        value="<?php echo e(old('start_date', \Carbon\Carbon::parse($cours->start_date)->format('Y-m-d'))); ?>" required />
                                                </div>
                                                <small class="text-muted">Date de début</small>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    <input class="form-control datepicker" type="text" id="end_date" name="end_date"
                                                        value="<?php echo e(old('end_date', \Carbon\Carbon::parse($cours->end_date)->format('Y-m-d'))); ?>" required />
                                                </div>
                                                <small class="text-muted">Date de fin</small>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback">Veuillez entrer des dates valides (la date de fin doit être après la date de début).</div>
                                    </div>
                                </div>

                                <!-- Formation -->
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Formation <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-auto">
                                                <span class="input-group-text"><i class="fa fa-book"></i></span>
                                            </div>
                                            <div class="col">
                                                <select id="formation_id" class="form-select select2-formation" name="training_id" required>
                                                    <option value="" disabled selected>Sélectionner une formation</option>
                                                    <?php $__currentLoopData = $formations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($formation->id); ?>" <?php echo e(old('training_id', $cours->training_id) == $formation->id ? 'selected' : ''); ?>>
                                                            <?php echo e($formation->title); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback">Veuillez sélectionner une formation valide.</div>
                                    </div>
                                </div>



                                <!-- Boutons de soumission -->
                                <div class="row">
                                    <div class="col">
                                        <div class="text-end mt-4">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-save"></i> Mettre à jour
                                            </button>
                                            <button class="btn btn-danger" type="button" onclick="window.location.href='<?php echo e(route('cours')); ?>'">
                                                <i class="fa fa-times"></i> Annuler
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/dropzone/dropzone.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/dropzone/dropzone-script.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="<?php echo e(asset('assets/js/MonJs/select2-init/single-select.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/MonJs/form-validation/form-validation.js')); ?>"></script>
        <script src="https://cdn.tiny.cloud/1/kekmlqdijg5r326hw82c8zalt4qp1hl0ui3v3tim9vh1xpzv/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo e(asset('assets/js/MonJs/description/description.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/MonJs/calendar/edit-calendar.js')); ?>"></script>


<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\apprendre laravel\Centre_Formation-main\resources\views/admin/apps/cours/coursedit.blade.php ENDPATH**/ ?>