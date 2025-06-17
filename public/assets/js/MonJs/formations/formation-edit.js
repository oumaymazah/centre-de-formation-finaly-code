// document.addEventListener('DOMContentLoaded', function() {
//     // Gestion de l'image
//     const deleteImageBtn = document.getElementById('deleteImage');
//     const restoreImageBtn = document.getElementById('restoreImage');
//     const imageUpload = document.getElementById('imageUpload');
//     const currentImageContainer = document.getElementById('currentImageContainer');
//     const newImagePreview = document.getElementById('newImagePreview');
//     const deleteImageInput = document.getElementById('deleteImageInput');
//     const imageIcon = document.getElementById('imageIcon');

//     if (deleteImageBtn) {
//         deleteImageBtn.addEventListener('click', function() {
//             currentImageContainer.style.display = 'none';
//             imageUpload.style.display = 'block';
//             imageIcon.style.display = 'flex';
//             deleteImageInput.value = '1';
//             restoreImageBtn.style.display = 'block';
//         });
//     }

//     if (restoreImageBtn) {
//         restoreImageBtn.addEventListener('click', function() {
//             currentImageContainer.style.display = 'block';
//             imageUpload.style.display = 'none';
//             imageIcon.style.display = 'none';
//             deleteImageInput.value = '0';
//             restoreImageBtn.style.display = 'none';
//             newImagePreview.style.display = 'none';
//             imageUpload.value = '';
//         });
//     }

//     if (imageUpload) {
//         imageUpload.addEventListener('change', function(e) {
//             if (e.target.files && e.target.files[0]) {
//                 const reader = new FileReader();
//                 reader.onload = function(event) {
//                     newImagePreview.style.display = 'block';
//                     document.getElementById('previewImage').src = event.target.result;
//                 };
//                 reader.readAsDataURL(e.target.files[0]);
//             }
//         });
//     }

//     // Validation des dates
//     function validateDates() {
//         const startDate = new Date(document.getElementById('start_date').value);
//         const endDate = new Date(document.getElementById('end_date').value);

//         if (startDate && endDate && startDate > endDate) {
//             document.getElementById('end_date').setCustomValidity('La date de fin doit être après la date de début');
//         } else {
//             document.getElementById('end_date').setCustomValidity('');
//         }
//     }

//     document.getElementById('start_date').addEventListener('change', validateDates);
//     document.getElementById('end_date').addEventListener('change', validateDates);
// });

document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'image
    const deleteImageBtn = document.getElementById('deleteImage');
    const restoreImageBtn = document.getElementById('restoreImage');
    const imageUpload = document.getElementById('imageUpload');
    const currentImageContainer = document.getElementById('currentImageContainer');
    const newImagePreview = document.getElementById('newImagePreview');
    const deleteImageInput = document.getElementById('deleteImageInput');
    const imageIcon = document.getElementById('imageIcon');

    if (deleteImageBtn) {
        deleteImageBtn.addEventListener('click', function() {
            currentImageContainer.style.display = 'none';
            imageUpload.style.display = 'block';
            imageIcon.style.display = 'flex';
            deleteImageInput.value = '1';
            restoreImageBtn.style.display = 'block';
        });
    }

    if (restoreImageBtn) {
        restoreImageBtn.addEventListener('click', function() {
            currentImageContainer.style.display = 'block';
            imageUpload.style.display = 'none';
            imageIcon.style.display = 'none';
            deleteImageInput.value = '0';
            restoreImageBtn.style.display = 'none';
            newImagePreview.style.display = 'none';
            imageUpload.value = '';
        });
    }

    if (imageUpload) {
        imageUpload.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    newImagePreview.style.display = 'block';
                    document.getElementById('previewImage').src = event.target.result;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }

    // Gestion des radio buttons de publication
    const publishNowRadio = document.getElementById('publishNow');
    const publishLaterRadio = document.getElementById('publishLater');
    const publishDateContainer = document.getElementById('publishDateContainer');

    function togglePublishDateContainer() {
        if (publishLaterRadio && publishLaterRadio.checked) {
            publishDateContainer.style.display = 'block';
        } else {
            publishDateContainer.style.display = 'none';
        }
    }

    // Ajouter les event listeners aux radio buttons
    if (publishNowRadio) {
        publishNowRadio.addEventListener('change', togglePublishDateContainer);
    }

    if (publishLaterRadio) {
        publishLaterRadio.addEventListener('change', togglePublishDateContainer);
    }

    // Initialiser l'état au chargement de la page
    togglePublishDateContainer();

    // Validation des dates
    function validateDates() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);

        if (startDate && endDate && startDate > endDate) {
            document.getElementById('end_date').setCustomValidity('La date de fin doit être après la date de début');
        } else {
            document.getElementById('end_date').setCustomValidity('');
        }
    }

    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput) {
        startDateInput.addEventListener('change', validateDates);
    }

    if (endDateInput) {
        endDateInput.addEventListener('change', validateDates);
    }
});
