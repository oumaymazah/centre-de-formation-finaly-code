 

 <?php $__env->startSection('title'); ?> 
     Modifier une Leçon 
 <?php $__env->stopSection(); ?>
 
 <?php $__env->startPush('css'); ?>
 <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/MonCss/dropzone.css')); ?>">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
 <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
 <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
 <style>
     .file-card {
         border: 1px solid #ddd;
         border-radius: 5px;
         padding: 10px;
         margin-bottom: 10px;
         background: #f9f9f9;
     }
     .file-preview {
         margin-top: 10px;
         padding: 10px;
         border-top: 1px solid #eee;
     }
     .dz-preview .dz-image img {
         width: 100%;
         height: 100%;
         object-fit: cover;
     }
     #existing-files-container {
         max-height: 300px;
         overflow-y: auto;
     }
     .link-item {
         margin-bottom: 5px;
     }
     .file-name {
         max-width: 200px;
         white-space: nowrap;
         overflow: hidden;
         text-overflow: ellipsis;
         display: inline-block;
         vertical-align: middle;
     }
     .file-name:hover {
         white-space: normal;
         overflow: visible;
         text-overflow: unset;
         background-color: #f8f9fa;
         z-index: 1000;
         position: relative;
     }
     .file-actions {
         display: flex;
         gap: 5px;
     }
 </style>
 <?php $__env->stopPush(); ?>
 
 <?php $__env->startSection('content'); ?>
 <div class="container-fluid">
     <div class="row">
         <div class="col-sm-12">
             <div class="card">
                 <div class="card-header pb-0">
                     <h5>Modifier une Leçon</h5>
                     <span>Mettez à jour les informations de la leçon</span>
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
 
                     <form action="<?php echo e(route('lessonupdate', $lesson->id)); ?>" method="POST" enctype="multipart/form-data" id="lesson-form">
                         <?php echo csrf_field(); ?>
                         <?php echo method_field('PUT'); ?>
                         <input type="hidden" id="uploadRoute" value="<?php echo e(route('upload.temp')); ?>">
                         <input type="hidden" id="deleteRoute" value="<?php echo e(route('delete.temp')); ?>">
                         <input type="hidden" name="deleted_files" id="deleted_files" value="">
 
                         <!-- Titre -->
                         <div class="mb-3 row">
                             <label class="col-sm-2 col-form-label">Titre <span class="text-danger">*</span></label>
                             <div class="col-sm-10">
                                 <input class="form-control" type="text" name="title" 
                                        value="<?php echo e(old('title', $lesson->title)); ?>" required>
                             </div>
                         </div>
 
                        <div class="mb-3 row">
                            <label class="col-sm-2 col-form-label">Description <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group" style="flex-wrap: nowrap;">
                                    <div class="input-group-text d-flex align-items-stretch" style="height: auto;">
                                        <i class="fa fa-align-left align-self-center"></i>
                                    </div>
                                    <textarea class="form-control" id="description" name="description" placeholder="Description" required><?php echo e(old('description',$lesson->description)); ?></textarea>
                                </div>
                                <div class="invalid-feedback">Veuillez entrer une description valide.</div>
                            </div>
                        </div>

                         <!-- Durée -->
                         <div class="mb-3 row">
                            <label class="col-sm-2 col-form-label">Durée (HH:mm:ss) <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input class="form-control" type="text" name="duration" 
                                       value="<?php echo e(old('duration', $lesson->duration)); ?>" 
                                       pattern="\d{2}:\d{2}:\d{2}" required>
                            </div>
                        </div>
                         <!-- Chapitre -->
                         <div class="mb-3 row">
                             <label class="col-sm-2 col-form-label">Chapitre <span class="text-danger">*</span></label>
                             <div class="col-sm-10">
                                 <select class="form-select select2-chapitre" name="chapter_id" required>
                                     <option value="">Sélectionnez un chapitre</option>
                                     <?php $__currentLoopData = $chapitres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chapitre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <option value="<?php echo e($chapitre->id); ?>" 
                                             <?php echo e(old('chapter_id', $lesson->chapter_id) == $chapitre->id ? 'selected' : ''); ?>>
                                             <?php echo e($chapitre->title); ?>

                                         </option>
                                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                 </select>
                             </div>
                         </div>
 
                         <!-- Fichiers existants -->
                         <div class="mb-3 row">
                             <label class="col-sm-2 col-form-label">Fichiers existants</label>
                             <div class="col-sm-10">
                                 <div class="existing-files" id="existing-files-container">
                                     <?php $__currentLoopData = $lesson->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <div class="file-card" id="file-<?php echo e($file->id); ?>">
                                             <div class="d-flex justify-content-between align-items-center">
                                                 <div>
                                                     <i class="fas <?php echo e(getFileIcon($file->file_type)); ?> me-2"></i>
                                                     <span class="file-name" title="<?php echo e($file->name); ?> (<?php echo e(formatFileSize($file->file_size)); ?>)">
                                                         <?php echo e($file->name); ?>

                                                     </span>
                                                     <small class="text-muted ms-2"><?php echo e(formatFileSize($file->file_size)); ?></small>
                                                 </div>
                                                 <div class="file-actions">
                                                     
                                                     <button type="button" class="btn btn-sm btn-danger delete-existing-file" 
                                                             data-file-id="<?php echo e($file->id); ?>"
                                                             title="Supprimer">
                                                         <i class="fas fa-trash"></i>
                                                     </button>
                                                 </div>
                                             </div>
                                         </div>
                                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                     <?php if($lesson->files->isEmpty()): ?>
                                         <div class="alert alert-info">Aucun fichier existant</div>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
 
                         <!-- Nouveaux fichiers -->
                         <div class="mb-3 row">
                             <label class="col-sm-2 col-form-label">Ajouter des fichiers</label>
                             <div class="col-sm-10">
                                 <div class="dropzone" id="fileUploadDropzone"></div>
                                 <input type="hidden" name="uploaded_files" id="uploaded_files" value="">
                             </div>
                         </div>

                         <!-- Conteneur pour la prévisualisation des fichiers -->
<div id="filePreviewContainer" style="display:none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.9); z-index: 9999; padding: 20px; overflow: auto;">
    <div class="card" style="max-width: 90%; margin: 20px auto; max-height: 90vh; overflow: hidden;">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="mb-0" id="filePreviewTitle">Aperçu du fichier</h5>
            <button type="button" class="btn-close btn-close-white close-preview" aria-label="Close"></button>
        </div>
        <div class="card-body" id="filePreviewContent" style="max-height: calc(90vh - 60px); overflow: auto; background: #f8f9fa;">
            <!-- Le contenu sera inséré ici dynamiquement -->
        </div>
    </div>
</div>
 
                         <!-- Liens -->
                         <div class="mb-3 row">
                             <label class="col-sm-2 col-form-label">Liens <span class="text-danger">*</span></label>
                             <div class="col-sm-10">
                                 <div id="links-container">
                                     <?php
                                         $links = json_decode($lesson->link) ?? [];
                                         if (json_last_error() !== JSON_ERROR_NONE) {
                                             $links = [$lesson->link];
                                         }
                                     ?>
                                     <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <div class="link-item input-group mb-2">
                                             <input type="url" name="links[]" class="form-control" 
                                                    value="<?php echo e(old('links.'.$index, $link)); ?>" 
                                                    placeholder="https://example.com">
                                             <?php if($index > 0): ?>
                                                 <button type="button" class="btn btn-danger remove-link">
                                                     <i class="fas fa-times"></i>
                                                 </button>
                                             <?php endif; ?>
                                         </div>
                                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                 </div>
                                 <button type="button" id="add-link" class="btn btn-sm btn-primary mt-2">
                                     <i class="fas fa-plus"></i> Ajouter un lien
                                 </button>
                             </div>
                         </div>
 
                         <!-- Boutons -->
                         <div class="row mt-4">
                             <div class="col text-end">
                                 <button type="submit" class="btn btn-primary">
                                     <i class="fas fa-save"></i> Enregistrer
                                 </button>
                                 <a href="<?php echo e(route('lessons')); ?>" class="btn btn-danger">
                                     <i class="fas fa-times"></i> Annuler
                                 </a>
                             </div>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
 </div>
 <?php $__env->stopSection(); ?>
 
 <?php $__env->startPush('scripts'); ?>
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
 <script src="<?php echo e(asset('assets/js/MonJs/lecons/lecon-edit.js')); ?>"></script>
 <script src="<?php echo e(asset('assets/js/MonJs/select2-init/single-select.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/description/description.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce/js/tinymce/tinymce.min.js')); ?>"></script>
    <script src="https://cdn.tiny.cloud/1/ivqx4rg9mkp3j7b0kjhnttlk4jwpkp1ay6dw3twe5jjabyss/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>


 <?php $__env->stopPush(); ?>
 
 <?php
 function getFileIcon($type) {
     $icons = [
         'pdf' => 'fa-file-pdf',
         'doc' => 'fa-file-word',
         'docx' => 'fa-file-word',
         'xls' => 'fa-file-excel',
         'xlsx' => 'fa-file-excel',
         'ppt' => 'fa-file-powerpoint',
         'pptx' => 'fa-file-powerpoint',
         'zip' => 'fa-file-archive',
         'mp3' => 'fa-file-audio',
         'mp4' => 'fa-file-video',
         'jpg' => 'fa-file-image',
         'jpeg' => 'fa-file-image',
         'png' => 'fa-file-image',
     ];
     return $icons[strtolower($type)] ?? 'fa-file';
 }
 
 function formatFileSize($bytes) {
     if ($bytes >= 1073741824) {
         return number_format($bytes / 1073741824, 2) . ' GB';
     } elseif ($bytes >= 1048576) {
         return number_format($bytes / 1048576, 2) . ' MB';
     } elseif ($bytes >= 1024) {
         return number_format($bytes / 1024, 2) . ' KB';
     } elseif ($bytes > 1) {
         return $bytes . ' bytes';
     } elseif ($bytes == 1) {
         return '1 byte';
     } else {
         return '0 bytes';
     }
 }
 ?> 

















<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\hibah\P_Plateforme_ELS\resources\views/admin/apps/lesson/lessonedit.blade.php ENDPATH**/ ?>