// ///////////////////////version3///////////////////////

// console.log('training-detail.js chargé avec succès');

// // Variables globales
// let currentLessonId = null;
// let isLoading = false;
// let isInitialized = false;

// // Fonction pour basculer l'affichage des cours
// function toggleCourse(courseId) {
//     console.log(`toggleCourse appelé pour courseId: ${courseId}`);
//     const content = document.getElementById(`course-${courseId}`);
//     const icon = document.getElementById(`course-icon-${courseId}`);

//     if (!content || !icon) {
//         console.error(`Éléments pour course-${courseId} non trouvés`);
//         return;
//     }

//     // Vérifier le contenu pour débogage
//     console.log(`Contenu de course-${courseId}:`, content.innerHTML);

//     // Utiliser getComputedStyle pour vérifier l'état d'affichage
//     const isVisible = window.getComputedStyle(content).display !== 'none';
//     content.style.display = isVisible ? 'none' : 'block';
//     icon.classList.toggle('rotated', !isVisible);

//     if (!isVisible) {
//         animateExpand(content);
//     } else {
//         animateCollapse(content);
//     }
// }

// // Fonction pour basculer l'affichage des chapitres
// function toggleChapter(chapterId) {
//     console.log(`toggleChapter appelé pour chapterId: ${chapterId}`);
//     const content = document.getElementById(`chapter-${chapterId}`);
//     const icon = document.getElementById(`chapter-icon-${chapterId}`);

//     if (!content || !icon) {
//         console.error(`Éléments pour chapter-${chapterId} non trouvés`);
//         return;
//     }

//     // Vérifier le contenu pour débogage
//     console.log(`Contenu de chapter-${chapterId}:`, content.innerHTML);

//     const isVisible = window.getComputedStyle(content).display !== 'none';
//     content.style.display = isVisible ? 'none' : 'block';
//     icon.classList.toggle('rotated', !isVisible);

//     if (!isVisible) {
//         animateExpand(content);
//     } else {
//         animateCollapse(content);
//     }
// }

// // Animations
// function animateExpand(element) {
//     console.log('Animation d’expansion pour', element.id);
//     const scrollHeight = element.scrollHeight;
//     console.log(`scrollHeight pour ${element.id}: ${scrollHeight}px`);

//     if (scrollHeight === 0) {
//         console.warn(`Hauteur nulle pour ${element.id}. Utilisation d'une hauteur par défaut.`);
//         element.style.minHeight = '100px'; // Hauteur par défaut pour contenu vide
//     }

//     element.style.overflow = 'hidden';
//     element.style.maxHeight = '0';
//     element.style.transition = 'max-height 0.3s ease-out';

//     setTimeout(() => {
//         element.style.maxHeight = `${scrollHeight || 100}px`;
//     }, 10);

//     setTimeout(() => {
//         element.style.maxHeight = '';
//         element.style.overflow = '';
//         element.style.transition = '';
//         element.style.minHeight = '';
//     }, 300);
// }

// function animateCollapse(element) {
//     console.log('Animation de collapse pour', element.id);
//     element.style.overflow = 'hidden';
//     element.style.maxHeight = `${element.scrollHeight}px`;
//     element.style.transition = 'max-height 0.3s ease-out';

//     setTimeout(() => {
//         element.style.maxHeight = '0';
//     }, 10);

//     setTimeout(() => {
//         element.style.display = 'none';
//     }, 300);
// }

// // Fonction pour charger le contenu d'une leçon
// async function loadLesson(lessonId) {
//     console.log(`loadLesson appelé pour lessonId: ${lessonId}`);
//     if (isLoading) {
//         console.log('Chargement en cours, ignoré');
//         return;
//     }

//     isLoading = true;
//     updateActiveLesson(lessonId);
//     currentLessonId = lessonId;

//     const contentArea = document.getElementById('lesson-content');
//     if (!contentArea) {
//         console.error('Élément lesson-content non trouvé');
//         isLoading = false;
//         return;
//     }

//     showLoadingState();

//     try {
//         const response = await fetch(`/api/lesson/${lessonId}/content`, {
//             method: 'GET',
//             headers: {
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
//                 'Accept': 'application/json',
//                 'Content-Type': 'application/json'
//             }
//         });

//         if (!response.ok) {
//             throw new Error(`Erreur HTTP: ${response.status}`);
//         }

//         const data = await response.json();

//         if (data.success) {
//             displayLessonContent(data.lesson);
//             trackLessonView(lessonId);
//         } else {
//             displayBlockedContent(data.message, data.requires_auth, data.requires_payment);
//         }
//     } catch (error) {
//         console.error('Erreur lors du chargement de la leçon:', error);
//         displayError(`Impossible de charger le contenu: ${error.message}`);
//     } finally {
//         isLoading = false;
//     }
// }

// // Mettre à jour l'état actif de la leçon
// function updateActiveLesson(lessonId) {
//     console.log(`Mise à jour de la leçon active: ${lessonId}`);
//     document.querySelectorAll('.lesson-item').forEach(item => {
//         item.classList.remove('active');
//     });

//     const currentLesson = document.querySelector(`.lesson-item[data-lesson-id="${lessonId}"]`);
//     if (currentLesson) {
//         currentLesson.classList.add('active');
//     } else {
//         console.warn(`Leçon avec ID ${lessonId} non trouvée dans le DOM`);
//     }
// }

// // Afficher l'état de chargement
// function showLoadingState() {
//     const contentArea = document.getElementById('lesson-content');
//     contentArea.innerHTML = `
//         <div class="text-center py-5">
//             <div class="spinner-border text-primary mb-3" role="status">
//                 <span class="visually-hidden">Chargement...</span>
//             </div>
//             <h5 class="text-muted">Chargement du contenu...</h5>
//             <p class="text-muted">Veuillez patienter pendant que nous préparons votre leçon.</p>
//         </div>
//     `;
// }

// // Afficher le contenu de la leçon
// function displayLessonContent(lesson) {
//     let contentHtml = generateLessonHeader(lesson);

//     if (lesson.content) {
//         contentHtml += generateTextContent(lesson.content);
//     }

//     if (lesson.files) {
//         contentHtml += generateFilesContent(lesson.files);
//     }

//     if (!lesson.content && (!lesson.files || isFilesEmpty(lesson.files))) {
//         contentHtml += generateEmptyContent();
//     }

//     const contentArea = document.getElementById('lesson-content');
//     contentArea.innerHTML = contentHtml;
//     initializeMediaPlayers();
// }

// // Générer l'en-tête de la leçon
// function generateLessonHeader(lesson) {
//     return `
//         <div class="lesson-header mb-4">
//             <div class="d-flex justify-content-between align-items-start">
//                 <div>
//                     <h2 class="mb-2">${lesson.title}</h2>
//                     ${lesson.description ? `<p class="text-muted lead">${lesson.description}</p>` : ''}
//                 </div>
//                 <div class="lesson-actions">
//                     <button class="btn btn-outline-primary btn-sm" onclick="printLesson()">
//                         <i class="fas fa-print me-1"></i>Imprimer
//                     </button>
//                 </div>
//             </div>
//         </div>
//     `;
// }

// // Générer le contenu textuel
// function generateTextContent(content) {
//     return `
//         <div class="lesson-text mb-4">
//             <div class="card">
//                 <div class="card-body">${content}</div>
//             </div>
//         </div>
//     `;
// }

// // Générer le contenu des fichiers
// function generateFilesContent(files) {
//     let contentHtml = '';

//     if (files.videos && files.videos.length > 0) {
//         contentHtml += `
//             <div class="files-section mb-4">
//                 <h4 class="section-title">
//                     <i class="fas fa-video me-2 text-danger"></i>
//                     Contenus Vidéo
//                 </h4>
//                 <div class="videos-grid">
//                     ${files.videos.map(video => createVideoPlayer(video)).join('')}
//                 </div>
//             </div>
//         `;
//     }

//     if (files.documents && files.documents.length > 0) {
//         contentHtml += `
//             <div class="files-section mb-4">
//                 <h4 class="section-title">
//                     <i class="fas fa-file-alt me-2 text-primary"></i>
//                     Documents
//                 </h4>
//                 <div class="documents-grid">
//                     ${files.documents.map(doc => createFileItem(doc, 'document')).join('')}
//                 </div>
//             </div>
//         `;
//     }

//     if (files.images && files.images.length > 0) {
//         contentHtml += `
//             <div class="files-section mb-4">
//                 <h4 class="section-title">
//                     <i class="fas fa-image me-2 text-success"></i>
//                     Images
//                 </h4>
//                 <div class="row images-gallery">
//                     ${files.images.map(image => createImageItem(image)).join('')}
//                 </div>
//             </div>
//         `;
//     }

//     if (files.others && files.others.length > 0) {
//         contentHtml += `
//             <div class="files-section mb-4">
//                 <h4 class="section-title">
//                     <i class="fas fa-file me-2 text-warning"></i>
//                     Autres Fichiers
//                 </h4>
//                 <div class="others-grid">
//                     ${files.others.map(file => createFileItem(file, 'other')).join('')}
//                 </div>
//             </div>
//         `;
//     }

//     return contentHtml;
// }

// // Créer un lecteur vidéo
// function createVideoPlayer(video) {
//     return `
//         <div class="video-container mb-4">
//             <div class="video-header d-flex justify-content-between align-items-center mb-2">
//                 <h6 class="mb-0">${video.name}</h6>
//                 <small class="text-muted">${video.formatted_size}</small>
//             </div>
//             <div class="video-wrapper">
//                 <video class="video-player" controls preload="metadata">
//                     <source src="/storage/${video.file_path}" type="video/${video.file_type}">
//                     Votre navigateur ne supporte pas la lecture vidéo.
//                 </video>
//             </div>
//             <div class="video-controls mt-2">
//                 <button class="btn btn-sm btn-outline-primary" onclick="downloadFile('${video.file_path}', '${video.name}')">
//                     <i class="fas fa-download me-1"></i>Télécharger
//                 </button>
//                 <button class="btn btn-sm btn-outline-secondary" onclick="toggleFullscreen(event)">
//                     <i class="fas fa-expand me-1"></i>Plein écran
//                 </button>
//             </div>
//         </div>
//     `;
// }

// // Créer un élément de fichier
// function createFileItem(file, type) {
//     const iconClass = getFileIcon(file.file_type);
//     const colorClass = getFileColorClass(file.file_type);

//     return `
//         <div class="file-item">
//             <div class="file-icon-wrapper">
//                 <i class="fas ${iconClass} file-icon ${colorClass}"></i>
//             </div>
//             <div class="file-info flex-grow-1">
//                 <strong class="file-name">${file.name}</strong>
//                 <div class="file-meta">
//                     <small class="text-muted">${file.formatted_size} • ${file.file_type.toUpperCase()}</small>
//                 </div>
//             </div>
//             <div class="file-actions">
//                 <button class="btn btn-sm btn-outline-primary" onclick="downloadFile('${file.file_path}', '${file.name}')">
//                     <i class="fas fa-download"></i>
//                 </button>
//                 ${canPreviewFile(file.file_type) ? `
//                     <button class="btn btn-sm btn-outline-secondary ms-1" onclick="previewFile('${file.file_path}', '${file.name}', '${file.file_type}')">
//                         <i class="fas fa-eye"></i>
//                     </button>
//                 ` : ''}
//             </div>
//         </div>
//     `;
// }

// // Créer un élément d'image
// function createImageItem(image) {
//     return `
//         <div class="col-md-4 col-sm-6 mb-3">
//             <div class="image-card card h-100">
//                 <div class="image-wrapper">
//                     <img src="/storage/${image.file_path}"
//                          class="card-img-top"
//                          alt="${image.name}"
//                          onclick="openImageModal('${image.file_path}', '${image.name}')"
//                          style="height: 200px; object-fit: cover; cursor: pointer;">
//                     <div class="image-overlay">
//                         <button class="btn btn-light btn-sm" onclick="openImageModal('${image.file_path}', '${image.name}')">
//                             <i class="fas fa-search-plus"></i>
//                         </button>
//                     </div>
//                 </div>
//                 <div class="card-body p-3">
//                     <h6 class="card-title text-truncate">${image.name}</h6>
//                     <div class="d-flex justify-content-between align-items-center">
//                         <small class="text-muted">${image.formatted_size}</small>
//                         <button class="btn btn-sm btn-outline-primary" onclick="downloadFile('${image.file_path}', '${image.name}')">
//                             <i class="fas fa-download"></i>
//                         </button>
//                     </div>
//                 </div>
//             </div>
//         </div>
//     `;
// }

// // Générer un contenu vide
// function generateEmptyContent() {
//     return `
//         <div class="empty-content text-center py-5">
//             <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
//             <h5 class="text-muted">Aucun contenu disponible</h5>
//             <p class="text-muted">Cette leçon ne contient pas encore de contenu ou de fichiers.</p>
//         </div>
//     `;
// }

// // Afficher un contenu bloqué
// function displayBlockedContent(message, requiresAuth, requiresPayment) {
//     let actionButton = '';
//     let title = 'Contenu Verrouillé';

//     if (requiresAuth) {
//         title = 'Connexion Requise';
//         actionButton = `
//             <a href="/login" class="btn btn-primary btn-lg me-2">
//                 <i class="fas fa-sign-in-alt me-2"></i>Se connecter
//             </a>
//             <a href="/register" class="btn btn-outline-primary btn-lg">
//                 <i class="fas fa-user-plus me-2"></i>S'inscrire
//             </a>
//         `;
//     } else if (requiresPayment) {
//         title = 'Accès Payant';
//         actionButton = `
//             <a href="/panier" class="btn btn-success btn-lg me-2">
//                 <i class="fas fa-shopping-cart me-2"></i>Accéder au panier
//             </a>
//             <a href="/formations" class="btn btn-outline-success btn-lg">
//                 <i class="fas fa-search me-2"></i>Voir les formations
//             </a>
//         `;
//     }

//     const contentArea = document.getElementById('lesson-content');
//     contentArea.innerHTML = `
//         <div class="blocked-content text-center py-5">
//             <div class="blocked-icon mb-4">
//                 <i class="fas fa-lock fa-4x text-muted"></i>
//             </div>
//             <h3 class="text-muted mb-3">${title}</h3>
//             <p class="text-muted mb-4 lead">${message}</p>
//             <div class="blocked-actions">
//                 ${actionButton}
//             </div>
//         </div>
//     `;
// }

// // Afficher une erreur
// function displayError(message) {
//     const contentArea = document.getElementById('lesson-content');
//     contentArea.innerHTML = `
//         <div class="error-content text-center py-5">
//             <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
//             <h4 class="text-muted mb-3">Oups ! Une erreur est survenue</h4>
//             <p class="text-muted mb-4">${message}</p>
//             <button class="btn btn-primary" onclick="location.reload()">
//                 <i class="fas fa-sync-alt me-2"></i>Recharger la page
//             </button>
//         </div>
//     `;
// }

// // Méthodes utilitaires pour les fichiers
// function getFileIcon(fileType) {
//     const type = fileType.toLowerCase();
//     const iconMap = {
//         'pdf': 'fa-file-pdf',
//         'doc': 'fa-file-word',
//         'docx': 'fa-file-word',
//         'xls': 'fa-file-excel',
//         'xlsx': 'fa-file-excel',
//         'ppt': 'fa-file-powerpoint',
//         'pptx': 'fa-file-powerpoint',
//         'txt': 'fa-file-alt',
//         'zip': 'fa-file-archive',
//         'rar': 'fa-file-archive',
//         '7z': 'fa-file-archive'
//     };
//     return iconMap[type] || 'fa-file';
// }

// function getFileColorClass(fileType) {
//     const type = fileType.toLowerCase();
//     const colorMap = {
//         'pdf': 'text-danger',
//         'doc': 'text-primary',
//         'docx': 'text-primary',
//         'xls': 'text-success',
//         'xlsx': 'text-success',
//         'ppt': 'text-warning',
//         'pptx': 'text-warning',
//         'txt': 'text-info',
//         'zip': 'text-secondary',
//         'rar': 'text-secondary',
//         '7z': 'text-secondary'
//     };
//     return colorMap[type] || 'text-muted';
// }

// function canPreviewFile(fileType) {
//     const previewableTypes = ['pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'svg'];
//     return previewableTypes.includes(fileType.toLowerCase());
// }

// function isFilesEmpty(files) {
//     return !files || (
//         (!files.videos || files.videos.length === 0) &&
//         (!files.documents || files.documents.length === 0) &&
//         (!files.images || files.images.length === 0) &&
//         (!files.others || files.others.length === 0)
//     );
// }

// // Actions sur les fichiers
// function downloadFile(filePath, fileName) {
//     console.log(`Téléchargement du fichier: ${fileName}`);
//     const link = document.createElement('a');
//     link.href = `/storage/${filePath}`;
//     link.download = fileName;
//     link.target = '_blank';
//     document.body.appendChild(link);
//     link.click();
//     document.body.removeChild(link);
// }

// function previewFile(filePath, fileName, fileType) {
//     console.log(`Prévisualisation du fichier: ${fileName}`);
//     const url = `/storage/${filePath}`;
//     window.open(url, '_blank');
// }

// function openImageModal(imagePath, imageName) {
//     console.log(`Ouverture du modal pour l'image: ${imageName}`);
//     const modalHtml = `
//         <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
//             <div class="modal-dialog modal-lg modal-dialog-centered">
//                 <div class="modal-content">
//                     <div class="modal-header">
//                         <h5 class="modal-title" id="imageModalLabel">${imageName}</h5>
//                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
//                     </div>
//                     <div class="modal-body text-center">
//                         <img src="/storage/${imagePath}" class="img-fluid" alt="${imageName}" style="max-height: 70vh;">
//                     </div>
//                     <div class="modal-footer">
//                         <button type="button" class="btn btn-outline-primary" onclick="downloadFile('${imagePath}', '${imageName}')">
//                             <i class="fas fa-download me-1"></i>Télécharger
//                         </button>
//                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
//                     </div>
//                 </div>
//             </div>
//         </div>
//     `;

//     const existingModal = document.getElementById('imageModal');
//     if (existingModal) {
//         existingModal.remove();
//     }

//     document.body.insertAdjacentHTML('beforeend', modalHtml);
//     const modalElement = document.getElementById('imageModal');
//     const modal = new bootstrap.Modal(modalElement);
//     modal.show();

//     modalElement.addEventListener('hidden.bs.modal', function () {
//         this.remove();
//     });
// }

// function toggleFullscreen(event) {
//     console.log('Toggle plein écran');
//     const button = event.target.closest('button');
//     const videoContainer = button.closest('.video-container');
//     const video = videoContainer.querySelector('video');

//     if (!document.fullscreenElement) {
//         if (video.requestFullscreen) {
//             video.requestFullscreen();
//         } else if (video.mozRequestFullScreen) {
//             video.mozRequestFullScreen();
//         } else if (video.webkitRequestFullscreen) {
//             video.webkitRequestFullscreen();
//         } else if (video.msRequestFullscreen) {
//             video.msRequestFullscreen();
//         }
//         button.innerHTML = '<i class="fas fa-compress me-1"></i>Réduire';
//     } else {
//         if (document.exitFullscreen) {
//             document.exitFullscreen();
//         } else if (document.mozCancelFullScreen) {
//             document.mozCancelFullScreen();
//         } else if (document.webkitExitFullscreen) {
//             document.webkitExitFullscreen();
//         } else if (document.msExitFullscreen) {
//             document.msExitFullscreen();
//         }
//         button.innerHTML = '<i class="fas fa-expand me-1"></i>Plein écran';
//     }
// }

// // Expand/Collapse tous les éléments
// function expandAll() {
//     console.log('Expansion de tous les éléments');
//     document.querySelectorAll('[id^="course-"]').forEach(course => {
//         if (course.id.includes('icon')) return;
//         course.style.display = 'block';
//         const icon = document.getElementById(course.id.replace('course-', 'course-icon-'));
//         if (icon) icon.classList.add('rotated');
//         animateExpand(course);
//     });

//     document.querySelectorAll('[id^="chapter-"]').forEach(chapter => {
//         if (chapter.id.includes('icon')) return;
//         chapter.style.display = 'block';
//         const icon = document.getElementById(chapter.id.replace('chapter-', 'chapter-icon-'));
//         if (icon) icon.classList.add('rotated');
//         animateExpand(chapter);
//     });
// }

// function collapseAll() {
//     console.log('Collapse de tous les éléments');
//     const modals = document.querySelectorAll('.modal.show');
//     modals.forEach(modal => {
//         const modalInstance = bootstrap.Modal.getInstance(modal);
//         if (modalInstance) modalInstance.hide();
//     });

//     document.querySelectorAll('[id^="course-"]').forEach(course => {
//         if (course.id.includes('icon')) return;
//         course.style.display = 'none';
//         const icon = document.getElementById(course.id.replace('course-', 'course-icon-'));
//         if (icon) icon.classList.remove('rotated');
//         animateCollapse(course);
//     });

//     document.querySelectorAll('[id^="chapter-"]').forEach(chapter => {
//         if (chapter.id.includes('icon')) return;
//         chapter.style.display = 'none';
//         const icon = document.getElementById(chapter.id.replace('chapter-', 'chapter-icon-'));
//         if (icon) icon.classList.remove('rotated');
//         animateCollapse(chapter);
//     });
// }

// // Initialiser les lecteurs média
// function initializeMediaPlayers() {
//     console.log('Initialisation des joueurs média');
//     const videos = document.querySelectorAll('.video-player');
//     videos.forEach(video => {
//         video.addEventListener('loadedmetadata', () => {
//             console.log(`Vidéo chargée: ${video.src}`);
//         });

//         video.addEventListener('error', (e) => {
//             console.error('Erreur de lecture vidéo:', e);
//             const container = video.closest('.video-container');
//             if (container) {
//                 container.innerHTML = `
//                     <div class="alert alert-warning">
//                         <i class="fas fa-exclamation-triangle me-2"></i>
//                         Impossible de charger cette vidéo. Veuillez réessayer plus tard.
//                     </div>
//                 `;
//             }
//         });
//     });
// }

// // Imprimer la leçon
// function printLesson() {
//     console.log('Impression de la leçon');
//     const lessonContent = document.getElementById('lesson-content');
//     if (!lessonContent) return;

//     const printWindow = window.open('', '_blank');
//     printWindow.document.write(`
//         <!DOCTYPE html>
//         <html>
//         <head>
//             <meta charset="utf-8">
//             <title>Impression - Leçon</title>
//             <style>
//                 body { font-family: Arial, sans-serif; margin: 20px; }
//                 .video-container, .file-actions, .lesson-actions { display: none !important; }
//                 .card { border: 1px solid #ddd; margin-bottom: 20px; }
//                 .card-body { padding: 15px; }
//                 img { max-width: 100%; height: auto; }
//                 @media print {
//                     .no-print { display: none !important; }
//                 }
//             </style>
//         </head>
//         <body>
//             ${lessonContent.innerHTML}
//         </body>
//         </html>
//     `);

//     printWindow.document.close();
//     printWindow.focus();
//     printWindow.print();
//     printWindow.close();
// }

// // Tracker la vue de la leçon
// function trackLessonView(lessonId) {
//     console.log(`Tracking de la vue de la leçon ID: ${lessonId}`);
//     fetch('/api/lesson/track-view', {
//         method: 'POST',
//         headers: {
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
//             'Accept': 'application/json',
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({
//             lesson_id: lessonId,
//             timestamp: new Date().toISOString()
//         })
//     }).catch(error => {
//         console.log('Erreur de tracking (non critique):', error);
//     });
// }

// // Configurer les raccourcis clavier
// function setupKeyboardShortcuts() {
//     console.log('Configuration des raccourcis clavier');
//     document.addEventListener('keydown', (e) => {
//         if (e.key === 'Escape') {
//             console.log('Touche Échap pressée');
//             collapseAll();
//         }

//         if (e.ctrlKey && e.key === 'e') {
//             e.preventDefault();
//             console.log('Ctrl + E pressé');
//             expandAll();
//         }
//     });
// }

// // Configurer l'auto-expansion
// function setupAutoExpand() {
//     console.log('Configuration de l\'auto-expansion');
//     const courseHeaders = document.querySelectorAll('.course-header');
//     if (courseHeaders.length === 1) {
//         const firstCourseId = parseInt(courseHeaders[0].dataset.courseId);
//         if (!isNaN(firstCourseId)) {
//             console.log(`Auto-expansion du premier cours: ${firstCourseId}`);
//             toggleCourse(firstCourseId);
//         }
//     }
// }

// // Attacher les écouteurs d'événements
// function initializeEventListeners() {
//     if (isInitialized) {
//         console.log('Écouteurs déjà initialisés, ignorer');
//         return;
//     }
//     isInitialized = true;

//     console.log('Attachement des écouteurs d’événements');

//     const courseHeaders = document.querySelectorAll('.course-header');
//     const chapterHeaders = document.querySelectorAll('.chapter-header');
//     const lessonItems = document.querySelectorAll('.lesson-item');

//     console.log(`Nombre de course-headers trouvés: ${courseHeaders.length}`);
//     console.log(`Nombre de chapter-headers trouvés: ${chapterHeaders.length}`);
//     console.log(`Nombre de lesson-items trouvés: ${lessonItems.length}`);

//     courseHeaders.forEach(header => {
//         const courseId = parseInt(header.dataset.courseId);
//         if (isNaN(courseId)) {
//             console.error(`ID de cours invalide pour l'élément:`, header);
//             return;
//         }
//         header.removeEventListener('click', handleCourseClick);
//         header.addEventListener('click', handleCourseClick);

//         function handleCourseClick() {
//             console.log(`Clic sur le cours ${courseId}`);
//             toggleCourse(courseId);
//         }
//     });

//     chapterHeaders.forEach(header => {
//         const chapterId = parseInt(header.dataset.chapterId);
//         if (isNaN(chapterId)) {
//             console.error(`ID de chapitre invalide pour l'élément:`, header);
//             return;
//         }
//         header.removeEventListener('click', handleChapterClick);
//         header.addEventListener('click', handleChapterClick);

//         function handleChapterClick() {
//             console.log(`Clic sur le chapitre ${chapterId}`);
//             toggleChapter(chapterId);
//         }
//     });

//     lessonItems.forEach(item => {
//         const lessonId = parseInt(item.dataset.lessonId);
//         if (isNaN(lessonId)) {
//             console.error(`ID de leçon invalide pour l'élément:`, item);
//             return;
//         }
//         item.removeEventListener('click', handleLessonClick);
//         item.addEventListener('click', handleLessonClick);

//         function handleLessonClick() {
//             console.log(`Clic sur la leçon ${lessonId}`);
//             loadLesson(lessonId);
//         }
//     });

//     setupKeyboardShortcuts();
//     setupAutoExpand();
// }

// // Initialisation après le chargement du DOM
// document.addEventListener('DOMContentLoaded', () => {
//     console.log('DOM chargé, lancement de l\'initialisation');
//     initializeEventListeners();
// });





























///////////////////////version3///////////////////////

console.log('training-detail.js chargé avec succès');

// Variables globales
let currentLessonId = null;
let isLoading = false;
let isInitialized = false;

// Fonction pour basculer l'affichage des cours
function toggleCourse(courseId) {
    console.log(`toggleCourse appelé pour courseId: ${courseId}`);
    const content = document.getElementById(`course-${courseId}`);
    const icon = document.getElementById(`course-icon-${courseId}`);

    if (!content || !icon) {
        console.error(`Éléments pour course-${courseId} non trouvés`);
        return;
    }

    // Vérifier le contenu pour débogage
    console.log(`Contenu de course-${courseId}:`, content.innerHTML);

    // Utiliser getComputedStyle pour vérifier l'état d'affichage
    const isVisible = window.getComputedStyle(content).display !== 'none';
    content.style.display = isVisible ? 'none' : 'block';
    icon.classList.toggle('rotated', !isVisible);

    if (!isVisible) {
        animateExpand(content);
    } else {
        animateCollapse(content);
    }
}

// Fonction pour basculer l'affichage des chapitres
function toggleChapter(chapterId) {
    console.log(`toggleChapter appelé pour chapterId: ${chapterId}`);
    const content = document.getElementById(`chapter-${chapterId}`);
    const icon = document.getElementById(`chapter-icon-${chapterId}`);

    if (!content || !icon) {
        console.error(`Éléments pour chapter-${chapterId} non trouvés`);
        return;
    }

    // Vérifier le contenu pour débogage
    console.log(`Contenu de chapter-${chapterId}:`, content.innerHTML);

    const isVisible = window.getComputedStyle(content).display !== 'none';
    content.style.display = isVisible ? 'none' : 'block';
    icon.classList.toggle('rotated', !isVisible);

    if (!isVisible) {
        animateExpand(content);
    } else {
        animateCollapse(content);
    }
}

// Animations
function animateExpand(element) {
    console.log('Animation d’expansion pour', element.id);
    const scrollHeight = element.scrollHeight;
    console.log(`scrollHeight pour ${element.id}: ${scrollHeight}px`);

    if (scrollHeight === 0) {
        console.warn(`Hauteur nulle pour ${element.id}. Utilisation d'une hauteur par défaut.`);
        element.style.minHeight = '100px'; // Hauteur par défaut pour contenu vide
    }

    element.style.overflow = 'hidden';
    element.style.maxHeight = '0';
    element.style.transition = 'max-height 0.3s ease-out';

    setTimeout(() => {
        element.style.maxHeight = `${scrollHeight || 100}px`;
    }, 10);

    setTimeout(() => {
        element.style.maxHeight = '';
        element.style.overflow = '';
        element.style.transition = '';
        element.style.minHeight = '';
    }, 300);
}

function animateCollapse(element) {
    console.log('Animation de collapse pour', element.id);
    element.style.overflow = 'hidden';
    element.style.maxHeight = `${element.scrollHeight}px`;
    element.style.transition = 'max-height 0.3s ease-out';

    setTimeout(() => {
        element.style.maxHeight = '0';
    }, 10);

    setTimeout(() => {
        element.style.display = 'none';
    }, 300);
}

// Fonction pour charger le contenu d'une leçon
async function loadLesson(lessonId) {
    console.log(`loadLesson appelé pour lessonId: ${lessonId}`);
    if (isLoading) {
        console.log('Chargement en cours, ignoré');
        return;
    }

    isLoading = true;
    updateActiveLesson(lessonId);
    currentLessonId = lessonId;

    const contentArea = document.getElementById('lesson-content');
    if (!contentArea) {
        console.error('Élément lesson-content non trouvé');
        isLoading = false;
        return;
    }

    showLoadingState();

    try {
        const response = await fetch(`/api/lesson/${lessonId}/content`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            displayLessonContent(data.lesson);
            trackLessonView(lessonId);
        } else {
            displayBlockedContent(data.message, data.requires_auth, data.requires_payment);
        }
    } catch (error) {
        console.error('Erreur lors du chargement de la leçon:', error);
        displayError(`Impossible de charger le contenu: ${error.message}`);
    } finally {
        isLoading = false;
    }
}

// Mettre à jour l'état actif de la leçon
function updateActiveLesson(lessonId) {
    console.log(`Mise à jour de la leçon active: ${lessonId}`);
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });

    const currentLesson = document.querySelector(`.lesson-item[data-lesson-id="${lessonId}"]`);
    if (currentLesson) {
        currentLesson.classList.add('active');
    } else {
        console.warn(`Leçon avec ID ${lessonId} non trouvée dans le DOM`);
    }
}

// Afficher l'état de chargement
function showLoadingState() {
    const contentArea = document.getElementById('lesson-content');
    contentArea.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <h5 class="text-muted">Chargement du contenu...</h5>
            <p class="text-muted">Veuillez patienter pendant que nous préparons votre leçon.</p>
        </div>
    `;
}


// function displayLessonContent(lesson) {
//     let contentHtml = generateLessonHeader(lesson);

//     if (lesson.content) {
//         contentHtml += generateTextContent(lesson.content);
//     }

//     if (lesson.links && lesson.links.length > 0) { // Vérifie le tableau links
//         contentHtml += generateLinkContent(lesson.links);
//     }

//     if (lesson.files) {
//         contentHtml += generateFilesContent(lesson.files);
//     }

//     if (!lesson.content && (!lesson.links || lesson.links.length === 0) && (!lesson.files || isFilesEmpty(lesson.files))) {
//         contentHtml += generateEmptyContent();
//     }

//     const contentArea = document.getElementById('lesson-content');
//     contentArea.innerHTML = contentHtml;
//     initializeMediaPlayers();
// }

function displayLessonContent(lesson) {
    let contentHtml = generateLessonHeader(lesson);

    if (lesson.content) {
        contentHtml += generateTextContent(lesson.content);
    }

    if (lesson.links && lesson.links.length > 0) {
        contentHtml += generateLinkContent(lesson.links);
    }

    if (lesson.files) {
        contentHtml += generateFilesContent(lesson.files);
    }

    // Afficher "Aucun contenu disponible" uniquement si aucun contenu, lien ou fichier n'est présent
    if (!lesson.content && (!lesson.links || lesson.links.length === 0) && (!lesson.files || isFilesEmpty(lesson.files))) {
        contentHtml += generateEmptyContent();
    }

    const contentArea = document.getElementById('lesson-content');
    contentArea.innerHTML = contentHtml;
    initializeMediaPlayers();
}
function generateLinkContent(links) {
    console.log(`Traitement des liens: ${JSON.stringify(links)}`);
    if (!Array.isArray(links) || links.length === 0) {
        console.warn(`Aucun lien valide détecté: ${JSON.stringify(links)}`);
        return '';
    }

    let contentHtml = '';

    links.forEach(link => {
        const isValidUrl = /^https?:\/\/[^\s$.?#].[^\s]*$/.test(link);
        if (!isValidUrl) {
            console.warn(`Lien invalide détecté: ${link}`);
            return;
        }

        let linkType = 'Lien Externe';
        let bgClass = 'bg-light';
        if (link.includes('meet.google.com')) {
            linkType = 'Google Meet';
            bgClass = 'bg-success-subtle';
        } else if (link.includes('youtube.com')) {
            linkType = 'YouTube';
            bgClass = 'bg-danger-subtle';
        }

        contentHtml += `
            <div class="files-section mb-4">
                <h4 class="section-title">
                    ${linkType}
                </h4>
                <div class="link-container ${bgClass} p-3 rounded mb-2">
                    <a href="${link}" target="_blank" rel="noopener noreferrer" class="link-text">
                        ${link}
                    </a>
                </div>
            </div>
        `;
    });

    return contentHtml;
}




// function generateLessonHeader(lesson) {
//     return `
//         <div class="lesson-header mb-4">
//             <div class="d-flex justify-content-between align-items-start">
//                 <div>
//                     <h2 class="mb-2">${lesson.title}</h2>
//                     ${lesson.duration ? `
//                         <div class="text-muted mb-2">
//                             <i class="fas fa-clock me-1"></i>
//                             <span>${lesson.duration}</span>
//                         </div>
//                     ` : ''}
//                     ${lesson.description ? `<p class="text-muted lead">${lesson.description}</p>` : ''}
//                 </div>
//             </div>
//         </div>
//     `;
// }
function generateLessonHeader(lesson) {
    return `
        <div class="lesson-header mb-4">
            <div class="lesson-header-content">
                <div class="lesson-info">
                    <div class="lesson-title-section">
                        <div class="lesson-title-group">
                            <div class="lesson-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h2 class="lesson-title">${lesson.title}</h2>
                        </div>
                        ${lesson.duration ? `
                            <div class="lesson-duration-badge">
                                <i class="fas fa-clock"></i>
                                <span>${lesson.duration}</span>
                            </div>
                        ` : ''}
                    </div>



                    ${lesson.description ? `
                        <p class="lesson-description">${lesson.description}</p>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}
// Générer le contenu textuel
function generateTextContent(content) {
    return `
        <div class="lesson-text mb-4">
            <div class="card">
                <div class="card-body">${content}</div>
            </div>
        </div>
    `;
}

// Générer le contenu des fichiers
function generateFilesContent(files) {
    let contentHtml = '';

    if (files.videos && files.videos.length > 0) {
        contentHtml += `
            <div class="files-section mb-4">
                <h4 class="section-title">
                    <i class="fas fa-video me-2 text-danger"></i>
                    Contenus Vidéo
                </h4>
                <div class="videos-grid">
                    ${files.videos.map(video => createVideoPlayer(video)).join('')}
                </div>
            </div>
        `;
    }

    if (files.documents && files.documents.length > 0) {
        contentHtml += `
            <div class="files-section mb-4">
                <h4 class="section-title">
                    <i class="fas fa-file-alt me-2 text-primary"></i>
                    Documents
                </h4>
                <div class="documents-grid">
                    ${files.documents.map(doc => createFileItem(doc, 'document')).join('')}
                </div>
            </div>
        `;
    }

    if (files.images && files.images.length > 0) {
        contentHtml += `
            <div class="files-section mb-4">
                <h4 class="section-title">
                    <i class="fas fa-image me-2 text-success"></i>
                    Images
                </h4>
                <div class="row images-gallery">
                    ${files.images.map(image => createImageItem(image)).join('')}
                </div>
            </div>
        `;
    }

    if (files.others && files.others.length > 0) {
        contentHtml += `
            <div class="files-section mb-4">
                <h4 class="section-title">
                    <i class="fas fa-file me-2 text-warning"></i>
                    Autres Fichiers
                </h4>
                <div class="others-grid">
                    ${files.others.map(file => createFileItem(file, 'other')).join('')}
                </div>
            </div>
        `;
    }

    return contentHtml;
}

// Créer un lecteur vidéo
function createVideoPlayer(video) {
    return `
        <div class="video-container mb-4">
            <div class="video-header d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">${video.name}</h6>
                <small class="text-muted">${video.formatted_size}</small>
            </div>
            <div class="video-wrapper">
                <video class="video-player" controls preload="metadata">
                    <source src="/storage/${video.file_path}" type="video/${video.file_type}">
                    Votre navigateur ne supporte pas la lecture vidéo.
                </video>
            </div>

        </div>
    `;
}

// Créer un élément de fichier
// function createFileItem(file, type) {
//     const iconClass = getFileIcon(file.file_type);
//     const colorClass = getFileColorClass(file.file_type);

//     return `
//         <div class="file-item">
//             <div class="file-icon-wrapper">
//                 <i class="fas ${iconClass} file-icon ${colorClass}"></i>
//             </div>
//             <div class="file-info flex-grow-1">
//                 <strong class="file-name">${file.name}</strong>
//                 <div class="file-meta">
//                     <small class="text-muted">${file.formatted_size} • ${file.file_type.toUpperCase()}</small>
//                 </div>
//             </div>
//             <div class="file-actions">
//                 <button class="btn btn-sm btn-outline-primary" onclick="downloadFile('${file.file_path}', '${file.name}')">
//                     <i class="fas fa-download"></i>
//                 </button>
//                 ${canPreviewFile(file.file_type) ? `
//                     <button class="btn btn-sm btn-outline-secondary ms-1" onclick="previewFile('${file.file_path}', '${file.name}', '${file.file_type}')">
//                         <i class="fas fa-eye"></i>
//                     </button>
//                 ` : ''}
//             </div>
//         </div>
//     `;
// }

// Créer un élément de fichier avec icônes cliquables
function createFileItem(file, type) {
    const iconClass = getFileIcon(file.file_type);
    const colorClass = getFileColorClass(file.file_type);

    return `
        <div class="file-item">
            <div class="file-icon-wrapper">
                <i class="fas ${iconClass} file-icon ${colorClass}"></i>
            </div>
            <div class="file-info flex-grow-1">
                <strong class="file-name">${file.name}</strong>
                <div class="file-meta">
                    <small class="text-muted">${file.formatted_size} • ${file.file_type.toUpperCase()}</small>
                </div>
            </div>
            <div class="file-actions">
                <!-- Icône de téléchargement -->
                <div class="action-icon download-icon"
                     onclick="downloadFile('${file.file_path}', '${file.name}')"
                     title="Télécharger le fichier">
                    <i class="fas fa-download"></i>
                </div>

                <!-- Icône de prévisualisation si le fichier peut être prévisualisé -->
                ${canPreviewFile(file.file_type) ? `
                    <div class="action-icon preview-icon"
                         onclick="previewFile('${file.file_path}', '${file.name}', '${file.file_type}')"
                         title="Prévisualiser le fichier">
                        <i class="fas fa-eye"></i>
                    </div>
                ` : ''}


            </div>
        </div>
    `;
}
// Créer un élément d'image
function createImageItem(image) {
    return `
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="image-card card h-100">
                <div class="image-wrapper">
                    <img src="/storage/${image.file_path}"
                         class="card-img-top"
                         alt="${image.name}"
                         onclick="openImageModal('${image.file_path}', '${image.name}')"
                         style="height: 200px; object-fit: cover; cursor: pointer;">
                    <div class="image-overlay">
                        <button class="btn btn-light btn-sm" onclick="openImageModal('${image.file_path}', '${image.name}')">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <h6 class="card-title text-truncate">${image.name}</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">${image.formatted_size}</small>
                        <div class="action-icon download-icon"
                            onclick="downloadFile('${image.file_path}', '${image.name}')"
                            title="Télécharger l'image">
                            <i class="fas fa-download"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    `;
}

// Générer un contenu vide
function generateEmptyContent() {
    return `
        <div class="empty-content text-center py-5">
            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun contenu disponible</h5>
            <p class="text-muted">Cette leçon ne contient pas encore de contenu ou de fichiers.</p>
        </div>
    `;
}

// Afficher un contenu bloqué
function displayBlockedContent(message, requiresAuth, requiresPayment) {
    let actionButton = '';
    let title = 'Contenu Verrouillé';

    if (requiresAuth) {
        title = 'Connexion Requise';
        actionButton = `
            <a href="/login" class="btn btn-connect-nav btn-lg me-2">
                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
            </a>
            <a href="/sign-up" class="btn btn-register-nav btn-lg">
                <i class="fas fa-user-plus me-2"></i>S'inscrire
            </a>
        `;
    } else if (requiresPayment) {
        title = 'Accès Payant';
        actionButton = `
            <a href="/panier" class="btn btn-success btn-lg me-2">
                <i class="fas fa-shopping-cart me-2"></i>Accéder au panier
            </a>
            <a href="/formations" class="btn btn-outline-success btn-lg">
                <i class="fas fa-search me-2"></i>Voir les formations
            </a>
        `;
    }

    const contentArea = document.getElementById('lesson-content');
    contentArea.innerHTML = `
        <div class="blocked-content text-center py-5">
            <div class="blocked-icon mb-4">
                <i class="fas fa-lock fa-4x text-muted"></i>
            </div>
            <h3 class="text-muted mb-3">${title}</h3>
            <p class="text-muted mb-4 lead">${message}</p>
            <div class="blocked-actions">
                ${actionButton}
            </div>
        </div>
    `;
}

// Afficher une erreur
function displayError(message) {
    const contentArea = document.getElementById('lesson-content');
    contentArea.innerHTML = `
        <div class="error-content text-center py-5">
            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
            <h4 class="text-muted mb-3">Oups ! Une erreur est survenue</h4>
            <p class="text-muted mb-4">${message}</p>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-2"></i>Recharger la page
            </button>
        </div>
    `;
}

// Méthodes utilitaires pour les fichiers
function getFileIcon(fileType) {
    const type = fileType.toLowerCase();
    const iconMap = {
        'pdf': 'fa-file-pdf',
        'doc': 'fa-file-word',
        'docx': 'fa-file-word',
        'xls': 'fa-file-excel',
        'xlsx': 'fa-file-excel',
        'ppt': 'fa-file-powerpoint',
        'pptx': 'fa-file-powerpoint',
        'txt': 'fa-file-alt',
        'zip': 'fa-file-archive',
        'rar': 'fa-file-archive',
        '7z': 'fa-file-archive'
    };
    return iconMap[type] || 'fa-file';
}

function getFileColorClass(fileType) {
    const type = fileType.toLowerCase();
    const colorMap = {
        'pdf': 'text-danger',
        'doc': 'text-primary',
        'docx': 'text-primary',
        'xls': 'text-success',
        'xlsx': 'text-success',
        'ppt': 'text-warning',
        'pptx': 'text-warning',
        'txt': 'text-info',
        'zip': 'text-secondary',
        'rar': 'text-secondary',
        '7z': 'text-secondary'
    };
    return colorMap[type] || 'text-muted';
}

function canPreviewFile(fileType) {
    const previewableTypes = ['pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'svg'];
    return previewableTypes.includes(fileType.toLowerCase());
}

function isFilesEmpty(files) {
    return !files || (
        (!files.videos || files.videos.length === 0) &&
        (!files.documents || files.documents.length === 0) &&
        (!files.images || files.images.length === 0) &&
        (!files.others || files.others.length === 0)
    );
}

// Actions sur les fichiers
function downloadFile(filePath, fileName) {
    console.log(`Téléchargement du fichier: ${fileName}`);
    const link = document.createElement('a');
    link.href = `/storage/${filePath}`;
    link.download = fileName;
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function previewFile(filePath, fileName, fileType) {
    console.log(`Prévisualisation du fichier: ${fileName}`);
    const url = `/storage/${filePath}`;
    window.open(url, '_blank');
}

function openImageModal(imagePath, imageName) {
    console.log(`Ouverture du modal pour l'image: ${imageName}`);
    const modalHtml = `
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">${imageName}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="/storage/${imagePath}" class="img-fluid" alt="${imageName}" style="max-height: 70vh;">
                    </div>

                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('imageModal');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modalElement = document.getElementById('imageModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();

    modalElement.addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
}

function toggleFullscreen(event) {
    console.log('Toggle plein écran');
    const button = event.target.closest('button');
    const videoContainer = button.closest('.video-container');
    const video = videoContainer.querySelector('video');

    if (!document.fullscreenElement) {
        if (video.requestFullscreen) {
            video.requestFullscreen();
        } else if (video.mozRequestFullScreen) {
            video.mozRequestFullScreen();
        } else if (video.webkitRequestFullscreen) {
            video.webkitRequestFullscreen();
        } else if (video.msRequestFullscreen) {
            video.msRequestFullscreen();
        }
        button.innerHTML = '<i class="fas fa-compress me-1"></i>Réduire';
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        button.innerHTML = '<i class="fas fa-expand me-1"></i>Plein écran';
    }
}

// Expand/Collapse tous les éléments
function expandAll() {
    console.log('Expansion de tous les éléments');
    document.querySelectorAll('[id^="course-"]').forEach(course => {
        if (course.id.includes('icon')) return;
        course.style.display = 'block';
        const icon = document.getElementById(course.id.replace('course-', 'course-icon-'));
        if (icon) icon.classList.add('rotated');
        animateExpand(course);
    });

    document.querySelectorAll('[id^="chapter-"]').forEach(chapter => {
        if (chapter.id.includes('icon')) return;
        chapter.style.display = 'block';
        const icon = document.getElementById(chapter.id.replace('chapter-', 'chapter-icon-'));
        if (icon) icon.classList.add('rotated');
        animateExpand(chapter);
    });
}

function collapseAll() {
    console.log('Collapse de tous les éléments');
    const modals = document.querySelectorAll('.modal.show');
    modals.forEach(modal => {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) modalInstance.hide();
    });

    document.querySelectorAll('[id^="course-"]').forEach(course => {
        if (course.id.includes('icon')) return;
        course.style.display = 'none';
        const icon = document.getElementById(course.id.replace('course-', 'course-icon-'));
        if (icon) icon.classList.remove('rotated');
        animateCollapse(course);
    });

    document.querySelectorAll('[id^="chapter-"]').forEach(chapter => {
        if (chapter.id.includes('icon')) return;
        chapter.style.display = 'none';
        const icon = document.getElementById(chapter.id.replace('chapter-', 'chapter-icon-'));
        if (icon) icon.classList.remove('rotated');
        animateCollapse(chapter);
    });
}

// Initialiser les lecteurs média
function initializeMediaPlayers() {
    console.log('Initialisation des joueurs média');
    const videos = document.querySelectorAll('.video-player');
    videos.forEach(video => {
        video.addEventListener('loadedmetadata', () => {
            console.log(`Vidéo chargée: ${video.src}`);
        });

        video.addEventListener('error', (e) => {
            console.error('Erreur de lecture vidéo:', e);
            const container = video.closest('.video-container');
            if (container) {
                container.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Impossible de charger cette vidéo. Veuillez réessayer plus tard.
                    </div>
                `;
            }
        });
    });
}

// Imprimer la leçon
function printLesson() {
    console.log('Impression de la leçon');
    const lessonContent = document.getElementById('lesson-content');
    if (!lessonContent) return;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Impression - Leçon</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .video-container, .file-actions, .lesson-actions { display: none !important; }
                .card { border: 1px solid #ddd; margin-bottom: 20px; }
                .card-body { padding: 15px; }
                img { max-width: 100%; height: auto; }
                @media print {
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            ${lessonContent.innerHTML}
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

// Tracker la vue de la leçon
function trackLessonView(lessonId) {
    console.log(`Tracking de la vue de la leçon ID: ${lessonId}`);
    fetch('/api/lesson/track-view', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            lesson_id: lessonId,
            timestamp: new Date().toISOString()
        })
    }).catch(error => {
        console.log('Erreur de tracking (non critique):', error);
    });
}

// Configurer les raccourcis clavier
function setupKeyboardShortcuts() {
    console.log('Configuration des raccourcis clavier');
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            console.log('Touche Échap pressée');
            collapseAll();
        }

        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            console.log('Ctrl + E pressé');
            expandAll();
        }
    });
}

// Configurer l'auto-expansion
function setupAutoExpand() {
    console.log('Configuration de l\'auto-expansion');
    const courseHeaders = document.querySelectorAll('.course-header');
    if (courseHeaders.length === 1) {
        const firstCourseId = parseInt(courseHeaders[0].dataset.courseId);
        if (!isNaN(firstCourseId)) {
            console.log(`Auto-expansion du premier cours: ${firstCourseId}`);
            toggleCourse(firstCourseId);
        }
    }
}

// Attacher les écouteurs d'événements
function initializeEventListeners() {
    if (isInitialized) {
        console.log('Écouteurs déjà initialisés, ignorer');
        return;
    }
    isInitialized = true;

    console.log('Attachement des écouteurs d’événements');

    const courseHeaders = document.querySelectorAll('.course-header');
    const chapterHeaders = document.querySelectorAll('.chapter-header');
    const lessonItems = document.querySelectorAll('.lesson-item');

    console.log(`Nombre de course-headers trouvés: ${courseHeaders.length}`);
    console.log(`Nombre de chapter-headers trouvés: ${chapterHeaders.length}`);
    console.log(`Nombre de lesson-items trouvés: ${lessonItems.length}`);

    courseHeaders.forEach(header => {
        const courseId = parseInt(header.dataset.courseId);
        if (isNaN(courseId)) {
            console.error(`ID de cours invalide pour l'élément:`, header);
            return;
        }
        header.removeEventListener('click', handleCourseClick);
        header.addEventListener('click', handleCourseClick);

        function handleCourseClick() {
            console.log(`Clic sur le cours ${courseId}`);
            toggleCourse(courseId);
        }
    });

    chapterHeaders.forEach(header => {
        const chapterId = parseInt(header.dataset.chapterId);
        if (isNaN(chapterId)) {
            console.error(`ID de chapitre invalide pour l'élément:`, header);
            return;
        }
        header.removeEventListener('click', handleChapterClick);
        header.addEventListener('click', handleChapterClick);

        function handleChapterClick() {
            console.log(`Clic sur le chapitre ${chapterId}`);
            toggleChapter(chapterId);
        }
    });

    lessonItems.forEach(item => {
        const lessonId = parseInt(item.dataset.lessonId);
        if (isNaN(lessonId)) {
            console.error(`ID de leçon invalide pour l'élément:`, item);
            return;
        }
        item.removeEventListener('click', handleLessonClick);
        item.addEventListener('click', handleLessonClick);

        function handleLessonClick() {
            console.log(`Clic sur la leçon ${lessonId}`);
            loadLesson(lessonId);
        }
    });

    setupKeyboardShortcuts();
    setupAutoExpand();
}

// Initialisation après le chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM chargé, lancement de l\'initialisation');
    initializeEventListeners();
});
