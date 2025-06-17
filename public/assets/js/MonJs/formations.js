
// 4. AJOUT d'une fonction pour forcer la synchronisation des boutons radio
function syncCategoryRadioButtons(categoryTitle) {
    const categoryId = getCategoryIdByTitle(categoryTitle);

    // Décocher tous les boutons radio
    $('input[name="category_filter"]').prop('checked', false);

    if (categoryId) {
        // Cocher le bouton radio correspondant
        const radioButton = $(`input[name="category_filter"][value="${categoryId}"]`);
        if (radioButton.length > 0) {
            radioButton.prop('checked', true);
            console.log('Successfully checked radio button for category:', categoryTitle, 'ID:', categoryId);
        } else {
            console.warn('Radio button not found for category ID:', categoryId);
            $('#category-all').prop('checked', true);
        }
    } else {
        $('#category-all').prop('checked', true);
        console.log('No category ID found, checking "All categories"');
    }
}
// 1. MODIFICATION de la fonction applyUrlFilters pour une meilleure synchronisation
function applyUrlFilters() {
    const urlParams = new URLSearchParams(window.location.search);

    // Réinitialiser tous les boutons radio
    $('input[name="category_filter"]').prop('checked', false);

    if (urlParams.has('category_title')) {
        const categoryTitle = decodeURIComponent(urlParams.get('category_title'));
        const categoryId = getCategoryIdByTitle(categoryTitle);

        console.log('Category title from URL:', categoryTitle);
        console.log('Found category ID:', categoryId);

        if (categoryId) {
            $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
            console.log('Radio button selected for category ID:', categoryId);
        } else {
            $('#category-all').prop('checked', true);
        }
    } else {
        $('#category-all').prop('checked', true);
    }

    // Reste du code pour les autres filtres...
    if (urlParams.has('status')) {
        const status = urlParams.get('status');
        statusFilter.val(status);
    } else {
        if (!(userIsAdmin || userIsProf)) {
            statusFilter.val('1');
        } else {
            statusFilter.val('');
        }
    }

    if (urlParams.has('rating')) {
        const rating = urlParams.get('rating');
        $(`input[name="rating_filter"][value="${rating}"]`).prop('checked', true);
    } else {
        $('#rating-all').prop('checked', true);
    }

    if (urlParams.has('search')) {
        searchInput.val(urlParams.get('search'));
        if ((searchInput.val() || '').trim() !== '') {
            $('.product-search i').removeClass('fa-search').addClass('fa-times');
        }
    }

    if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
        statusFilter.trigger('change.select2');
    }
}

// 2. AMÉLIORATION de la fonction getCategoryIdByTitle pour plus de robustesse
function getCategoryIdByTitle(categoryTitle) {
    if (!categoryTitle || categoryTitle.trim() === '') {
        return null;
    }

    const categories = window.categories || [];
    console.log('Searching for category:', categoryTitle);
    console.log('Available categories:', categories);

    const category = categories.find(cat => {
        const catTitleLower = cat.title.toLowerCase().trim();
        const searchTitleLower = categoryTitle.toLowerCase().trim();
        return catTitleLower === searchTitleLower;
    });

    console.log('Found category object:', category);
    return category ? category.id : null;
}

// 3. AJOUT d'une fonction pour forcer la synchronisation des boutons radio
function syncCategoryRadioButtons(categoryTitle) {
    const categoryId = getCategoryIdByTitle(categoryTitle);

    // Décocher tous les boutons radio
    $('input[name="category_filter"]').prop('checked', false);

    if (categoryId) {
        // Cocher le bouton radio correspondant
        const radioButton = $(`input[name="category_filter"][value="${categoryId}"]`);
        if (radioButton.length > 0) {
            radioButton.prop('checked', true);
            console.log('Successfully checked radio button for category:', categoryTitle, 'ID:', categoryId);
        } else {
            console.warn('Radio button not found for category ID:', categoryId);
            $('#category-all').prop('checked', true);
        }
    } else {
        $('#category-all').prop('checked', true);
        console.log('No category ID found, checking "All categories"');
    }
}


window.showFormationsForCurrentCategory = function(categoryTitle) {
    window.isShowingLimitedResults = false;
    window.currentCategoryData = null;
    $('#search-formations').val('');
    $('.product-search i').removeClass('fa-times').addClass('fa-search');
    $('#rating-all').prop('checked', true);

    // Masquer le footer avant l'appel AJAX
    $('.footer').css('display', 'none');

    $('.formations-container').html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden"></span></div></div>');

    // Récupérer l'ID de la catégorie à partir du titre
    const categoryId = getCategoryIdByTitle(categoryTitle);

    // Sélectionner le bon bouton radio AVANT l'appel AJAX
    if (categoryId && categoryId !== '') {
        $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
        $('#category-all').prop('checked', false);
    } else {
        $('#category-all').prop('checked', true);
        $('input[name="category_filter"]:not(#category-all)').prop('checked', false);
    }

    const ajaxParams = { format: 'json' };
    if (categoryId && categoryId !== '') {
        ajaxParams.category_id = categoryId;
    }

    const currentStatus = $('.status-filter').val();
    if (currentStatus !== '') {
        ajaxParams.status = currentStatus;
    }

    const url = new URL(window.location);
    if (categoryTitle && categoryTitle !== '') {
        url.searchParams.set('category_title', categoryTitle);
    } else {
        url.searchParams.delete('category_title');
    }
    url.searchParams.delete('category_id');
    url.searchParams.delete('search');
    url.searchParams.delete('rating');
    window.history.pushState({}, '', url);

    $.ajax({
        url: window.location.pathname,
        type: 'GET',
        data: ajaxParams,
        dataType: 'json',
        success: function(response) {
            console.log("Formations chargées pour la catégorie:", response);
            if (typeof window.updateFormationsDisplay === 'function') {
                window.updateFormationsDisplay(response);
            } else {
                window.location.reload();
            }

            $('html, body').animate({
                scrollTop: $('.formations-container').offset().top - 100
            }, 500);
        },
        error: function(xhr, status, error) {
            console.error("Erreur lors du chargement des formations:", error);
            $('.formations-container').html(`
                <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <div class="alert alert-danger text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <i class="fa fa-exclamation-triangle fa-2x mb-3"></i>
                        <h5>Erreur</h5>
                        <p class="mb-0">Une erreur s'est produite lors du chargement des formations.</p>
                    </div>
                </div>
            `);
            // Afficher le footer même en cas d'erreur
            $('.footer').css('display', 'block');
        }
    });
};
// 5. MODIFICATION du script du footer pour une meilleure synchronisation
document.addEventListener('DOMContentLoaded', function() {
    // Gérer les clics sur les liens de formation dans le footer
    document.querySelectorAll('.footer-formation-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            const categoryTitle = this.getAttribute('data-category-title');

            // Si on est déjà sur la page formations
            if (window.location.pathname === '/formations') {
                e.preventDefault();

                // Synchroniser immédiatement les boutons radio
                syncCategoryRadioButtons(categoryTitle);

                // Puis charger les formations
                if (typeof window.showFormationsForCurrentCategory === 'function') {
                    window.showFormationsForCurrentCategory(categoryTitle);
                }
                return false;
            }
            // Sinon, laisser le lien normal fonctionner (redirection vers formations avec paramètre)
        });
    });
});


$(document).ready(function() {
    // Vérifier si on est sur la page formations au chargement
    if (window.location.pathname === '/formations') {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('category_title')) {
            const categoryTitle = decodeURIComponent(urlParams.get('category_title'));

            // Attendre que les catégories soient chargées avant d'appliquer les filtres
            setTimeout(() => {
                syncCategoryRadioButtons(categoryTitle);
                window.showFormationsForCurrentCategory(categoryTitle);
            }, 200);
        } else {
            // Charger les formations par défaut et afficher le footer après
            loadFilteredFormations();
        }
    }
});

$(document).ready(function() {
    window.isShowingLimitedResults = false;
    window.currentCategoryData = null;
    window.updateFormationsDisplay = updateFormationsDisplay;

    $(document).on('click', '[data-action="show-all-formations"]', function() {
        const categoryId = $(this).data('category-id') || '';
        showFormationsForCurrentCategory('');
    });

    const ratingFilter = $('input[name="rating_filter"]');
    ratingFilter.on('change', function() {
        isShowingLimitedResults = false;
        currentCategoryData = null;
        loadFilteredFormations();
    });

    const buttonStyles = `
        <style id="custom-button-styles">
        .show-more-btn, .show-more-all-btn, .show-less-btn, .show-less-all-btn {
            border: 2px solid #9b59b6 !important;
            color: #9b59b6 !important;
            background: transparent !important;
            padding: 8px 16px;
            border-radius: 8px !important;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: none;
            position: relative;
            overflow: hidden;
            font-size: 14px;
            width: auto !important;
            max-width: none !important;
            margin: 0 !important;
            display: inline-block !important;
            white-space: nowrap !important;
            text-align: center;
            min-width: 150px;
        }

        .btn-outline-primary.no-hover-effect:hover {
    background-color: #2B6ED4 !important;
    border-color: #2B6ED4 !important;
    color: white !important;
}

        .show-more-btn:hover, .show-more-all-btn:hover,
        .show-less-btn:hover, .show-less-all-btn:hover {
            background: #EDE7F6 !important;
            border-color: #9b59b6 !important;
            color: #9b59b6 !important;
            transform: none;
            box-shadow: none;
            background-clip: padding-box !important;
        }

        .show-more-btn:focus, .show-more-all-btn:focus,
        .show-less-btn:focus, .show-less-all-btn:focus {
            box-shadow: none;
            outline: none;
            border-color: #9b59b6 !important;
            background: #EDE7F6 !important;
            color: #9b59b6 !important;
            background-clip: padding-box !important;
        }

        .show-more-btn:disabled, .show-more-all-btn:disabled,
        .show-less-btn:disabled, .show-less-all-btn:disabled {
            opacity: 0.7;
            transform: none;
            cursor: not-allowed;
            color: #9b59b6 !important;
            background: transparent !important;
        }

        .show-more-section, .show-less-section {
            text-align: left !important;
        }

        .show-more-section .d-flex, .show-less-section .d-flex {
            justify-content: flex-start !important;
        }

        .show-more-section p, .show-less-section p {
            text-align: left !important;
            margin-left: 0 !important;
        }

        .button-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(155, 89, 182, 0.3);
            border-radius: 50%;
            border-top-color: #9b59b6;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        </style>
    `;

    if (!document.getElementById('custom-button-styles')) {
        $('head').append(buttonStyles);
    }

    // Sélecteurs
    const formationsContainer = $('.formations-container');
    const searchInput = $('#search-formations');
    const categoryFilter = $('input[name="category_filter"]');
    const statusFilter = $('.status-filter');
    let isShowingLimitedResults = false;
    let currentCategoryData = null;

    const userIsAdmin = $('body').data('user-is-admin');
    const userIsProf = $('body').data('user-is-prof');

    console.log("L'utilisateur est admin:", userIsAdmin);
    console.log("L'utilisateur est professeur:", userIsProf);
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);

        $('.notifications-container').html(notification);
        setTimeout(function() {
            notification.alert('close');
        }, 3000);
    }
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }
    $('.product-search i').on('click', function() {
        const searchInput = $('#search-formations');

        if ($(this).hasClass('fa-times')) {
            searchInput.val('');
            $(this).removeClass('fa-times').addClass('fa-search');
            loadFilteredFormations();
        } else if ((searchInput.val() || '').trim() !== '') {
            loadFilteredFormations();
        }
    });
    categoryFilter.on('change', function() {
        isShowingLimitedResults = false;
        currentCategoryData = null;
        loadFilteredFormations();
    });

    statusFilter.on('change', loadFilteredFormations);

    searchInput.on('keyup', function(e) {
        if (e.key === 'Escape') {
            $(this).val('');
            $('.product-search i').removeClass('fa-times').addClass('fa-search');
            loadFilteredFormations();
            return;
        }
        debounce(loadFilteredFormations, 500)();
    });

    $('.categories-toggle, .ratings-toggle').on('click', function() {
        const $content = $(this).next('.checkbox-animated');
        const $icon = $(this).find('i');

        if ($content.is(':visible')) {
            $content.slideUp(200);
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            $('.checkbox-animated').slideUp();
            $('.toggle-data').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $content.slideDown(200);
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });

    function loadFilteredFormations() {
    const categoryId = $('input[name="category_filter"]:checked').val();
    const status = statusFilter.val();
    const rating = $('input[name="rating_filter"]:checked').val();
    const searchTerm = (searchInput.val() || '').trim();

    console.log("Filtrage:", { categoryId, status, rating, searchTerm });

    const searchIcon = $('.product-search i');
    if (searchTerm !== '') {
        searchIcon.removeClass('fa-search').addClass('fa-times');
    } else {
        searchIcon.removeClass('fa-times').addClass('fa-search');
    }

    const url = new URL(window.location);

    if (categoryId) {
        const category = window.categories.find(cat => cat.id == categoryId);
        if (category) {
            url.searchParams.set('category_title', category.title);
        } else {
            url.searchParams.delete('category_title');
        }
        url.searchParams.delete('category_id');
    } else {
        url.searchParams.delete('category_title');
        url.searchParams.delete('category_id');
    }

    if (status !== '' && (userIsAdmin || userIsProf)) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }

    url.searchParams.delete('rating');

    url.searchParams.delete('search');

    window.history.pushState({}, '', url);

    const ajaxParams = { format: 'json' };

    if (categoryId) ajaxParams.category_id = categoryId;
    if (status !== '') ajaxParams.status = status;
    if (rating && rating !== '') ajaxParams.rating = rating;
    if (searchTerm) ajaxParams.search = searchTerm;

    // Masquer le footer avant l'appel AJAX
    $('.footer').css('display', 'none');

    formationsContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden"></span></div></div>');

    $.ajax({
        url: window.location.pathname,
        type: 'GET',
        data: ajaxParams,
        dataType: 'json',
        success: function(response) {
            console.log("Réponse:", response);
            if (response.categories) {
                window.categories = response.categories;
            }
            updateFormationsDisplay(response);
        },
        error: function(xhr, status, error) {
            console.error("Erreur:", error);
            formationsContainer.html(`
                <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <div class="alert alert-danger text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <i class="fa fa-exclamation-triangle fa-2x mb-3"></i>
                        <h5>Erreur</h5>
                        <p class="mb-0">Une erreur s'est produite lors du chargement des formations.</p>
                    </div>
                </div>
            `);
            // Afficher le footer même en cas d'erreur
            $('.footer').css('display', 'block');
        }
    });
}

function updateFormationsDisplay(data) {
    formationsContainer.empty();

    const searchIcon = $('.product-search i');
    if ((searchInput.val() || '').trim() !== '') {
        searchIcon.removeClass('fa-search').addClass('fa-times');
    } else {
        searchIcon.removeClass('fa-times').addClass('fa-search');
    }

    // Masquer le footer avant de commencer le rendu
    $('.footer').css('display', 'none');

    if (!data.formations || data.formations.length === 0) {
        const currentRating = $('input[name="rating_filter"]:checked').val();
        const currentCategory = $('input[name="category_filter"]:checked').val();
        const categoryName = data.title || 'cette catégorie';

        const isRatingFilter = currentRating && currentRating !== '' && currentRating !== 'all';
        const isCategoryFilter = currentCategory && currentCategory !== '';
        if (data.searchPerformed && data.searchTerm) {
            formationsContainer.html(`
                <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 400px;">
                    <div class="text-center p-5" style="max-width: 600px;">
                        <div class="mb-4">
                            <div class="search-icon-container" style="display: inline-block; width: 80px; height: 80px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 50%; position: relative; box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                                <i class="fa fa-search" style="font-size: 32px; color: #6c757d; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                            </div>
                        </div>
                        <h4 class="mb-3" style="color: #495057; font-weight: 600;">Aucun résultat pour "${data.searchTerm}"</h4>
                        <p class="text-muted mb-4" style="font-size: 16px; line-height: 1.6;">
                            Aucune formation ne correspond à votre recherche.<br>
                            Essayez d'autres termes ou modifiez vos filtres.
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                           <button class="btn btn-outline-primary px-4 py-2 no-hover-effect" onclick="$('#search-formations').val('').trigger('keyup');" style="transition: none !important;">
                                <i class="fa fa-refresh me-2"></i>Effacer la recherche
                            </button>

                            <button class="btn btn-outline-secondary px-4 py-2" onclick="showFormationsForCurrentCategory('');">
                                <i class="fa fa-list me-2"></i>Voir toutes les formations
                            </button>
                        </div>
                    </div>
                </style>
            `);
        } else if (isRatingFilter) {
            const ratingText = getRatingText(currentRating);
            const ratingStars = getRatingStars(currentRating);

            const messageTitle = isCategoryFilter ?
                `Aucune formation ${ratingText} en ${categoryName}` :
                `Aucune formation `;

            const messageContent = isCategoryFilter ?
                `Il n'y a actuellement aucune formation avec une évaluation ${ratingText} dans la catégorie "${categoryName}".` :
                `Il n'y a actuellement aucune formation avec une évaluation ${ratingText}.`;

            formationsContainer.html(`
                <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 400px;">
                    <div class="text-center p-5" style="max-width: 700px;">
                        <div class="mb-4">
                        </div>
                        <h4 class="mb-3" style="color: #495057; font-weight: 600;">${messageTitle}</h4>
                        <p class="text-muted mb-4" style="font-size: 16px; line-height: 1.6;">
                            ${messageContent};
                            Essayez de modifier votre filtre ou explorez d'autres catégories.
                        </p>
                    </div>
                </div>
            `);
        } else {
            formationsContainer.html(`
                <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 400px;">
                    <div class="text-center p-5" style="max-width: 600px;">
                        <div class="mb-4">
                            <div class="info-icon-container" style="display: inline-block; width: 80px; height: 80px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 50%; position: relative; box-shadow: 0 8px 25px rgba(33,150,243,0.2);">
                                <i class="fa fa-info-circle" style="font-size: 32px; color: #2196f3; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                            </div>
                        </div>
                        <h6>Aucune formation disponible</h5>
                        <p class="text-muted mb-4" style="font-size: 16px; line-height: 1.6;">
                            Il n'y a actuellement aucune formation disponible dans cette catégorie.
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                            ${(userIsAdmin || userIsProf) ? `
                            <button class="btn btn-outline-secondary px-4 py-2" onclick="$('.status-filter').val('').trigger('change');">
                                <i class="fa fa-eye me-2"></i>Voir tous les statuts
                            </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `);
        }
        // Afficher le footer après le rendu des messages d'absence de formations
        $('.footer').css('display', 'block');
        return;
    }

    if (data.title) {
        $('.breadcrumb_title h3').text('Formations: ' + data.title);

        if (data.searchPerformed && data.searchTerm) {
            $('.breadcrumb_title h3').text('Recherche: ' + data.searchTerm);
        }
    }

    const categoryId = $('input[name="category_filter"]:checked').val();
    const searchTerm = searchInput.val().trim();
    const isAllCategories = !categoryId || categoryId === '';

    const limitForDisplay = isAllCategories ? 6 : 3;
    const shouldLimitDisplay = !searchTerm && !isShowingLimitedResults;

    if (shouldLimitDisplay || isShowingLimitedResults) {
        currentCategoryData = data;
    }

    let formationsToShow = data.formations;
    let showMoreButton = false;
    let showLessButton = false;

    if (shouldLimitDisplay && data.formations.length > limitForDisplay) {
        formationsToShow = data.formations.slice(0, limitForDisplay);
        showMoreButton = true;
    } else if (isShowingLimitedResults && data.formations.length > limitForDisplay) {
        formationsToShow = data.formations;
        showLessButton = true;
    }

    const coursesPerRow = 3;

    formationsToShow.forEach((formation, index) => {
        const formationHtml = createFormationCard(formation);
        formationsContainer.append(formationHtml);

        if ((index + 1) % coursesPerRow === 0 && index < formationsToShow.length - 1) {
            formationsContainer.append('<div class="w-100 mb-4"></div>');
        }
    });

    if (showMoreButton) {
        const remainingCount = data.formations.length - limitForDisplay;

        if (isAllCategories) {
            const showMoreButtonHtml = createShowMoreAllButton(remainingCount);
            formationsContainer.append(showMoreButtonHtml);
        } else {
            const categoryName = data.title || 'cette catégorie';
            const showMoreButtonHtml = createShowMoreButton(categoryName, remainingCount);
            formationsContainer.append(showMoreButtonHtml);
        }
    }

    if (showLessButton) {
        if (isAllCategories) {
            const showLessButtonHtml = createShowLessAllButton();
            formationsContainer.append(showLessButtonHtml);
        } else {
            const categoryName = data.title || 'cette catégorie';
            const showLessButtonHtml = createShowLessButton(categoryName);
            formationsContainer.append(showLessButtonHtml);
        }
    }

    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    if ($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Afficher le footer après le rendu des cards
    $('.footer').css('display', 'block');
}

    function createShowMoreButton(categoryName, remainingCount) {
        return `
            <div class="col-12 mt-4 mb-4">
                <div class="show-more-section">
                    <hr class="my-4">
                    <div class="d-flex justify-content-start">
                        <button class="btn btn-lg show-more-btn" data-category="${categoryName}">
                            <div class="button-spinner"></div>
                            <span class="button-text">Afficher plus de formations en ${categoryName}</span>
                        </button>
                    </div>
                    <p class="text-muted mt-2 small ">
                        ${remainingCount} formation(s) supplémentaire(s) disponible(s) dans cette catégorie
                    </p>
                </div>
            </div>
        `;
    }

    function createShowMoreAllButton(remainingCount) {
        return `
            <div class="col-12 mt-4 mb-4">
                <div class="show-more-section">
                    <hr class="my-4">
                    <div class="d-flex justify-content-start">
                        <button class="btn btn-lg show-more-all-btn">
                            <div class="button-spinner"></div>
                            <span class="button-text">Voir toutes les formations</span>
                        </button>
                    </div>
                    <p class="text-muted mt-2 small">
                        ${remainingCount} formation(s) supplémentaire(s) disponible(s)
                    </p>
                </div>
            </div>
        `;
    }

    function createShowLessButton(categoryName) {
        return `
            <div class="col-12 mt-4 mb-4">
                <div class="show-less-section">
                    <hr class="my-4">
                    <div class="d-flex justify-content-start">
                        <button class="btn btn-lg show-less-btn" data-category="${categoryName}">
                            <div class="button-spinner"></div>
                            <span class="button-text">Afficher moins de formations en ${categoryName}</span>
                        </button>
                    </div>
                    <p class="text-muted mt-2 small">
                        Afficher seulement les 3 premières formations de cette catégorie
                    </p>
                </div>
            </div>
        `;
    }

    function createShowLessAllButton() {
        return `
            <div class="col-12 mt-4 mb-4">
                <div class="show-less-section">
                    <hr class="my-4">
                    <div class="d-flex justify-content-start">
                        <button class="btn btn-lg show-less-all-btn">
                            <div class="button-spinner"></div>
                            <span class="button-text">Afficher moins de formations</span>
                        </button>
                    </div>
                    <p class="text-muted mt-2 small">
                        Afficher seulement les 6 premières formations
                    </p>
                </div>
            </div>
        `;
    }


    function getRatingText(ratingValue) {
        const ratingMap = {
            '5': '5 étoiles',
            '4': '4 étoiles et plus',
            '3': '3 étoiles et plus',
            '2': '2 étoiles et plus',
            '1': '1 étoile et plus'
        };
        return ratingMap[ratingValue] || 'avec cette évaluation';
    }

    function getRatingStars(ratingValue) {
        const numStars = parseInt(ratingValue) || 0;
        let stars = '';

        for (let i = 1; i <= 5; i++) {
            if (i <= numStars) {
                stars += '<i class="fa fa-star"></i>';
            } else {
                stars += '<i class="fa fa-star-o"></i>';
            }
        }

        return stars;
    }

    $(document).on('click', '.show-more-btn', function() {
        const button = $(this);
        const categoryName = button.data('category');

        button.find('.button-text').hide();
        button.find('.button-spinner').show();
        button.prop('disabled', true);

        isShowingLimitedResults = true;

        setTimeout(() => {
            if (currentCategoryData) {
                updateFormationsDisplay(currentCategoryData);
                $('html, body').animate({
                    scrollTop: formationsContainer.offset().top - 100
                }, 500);
                $('.breadcrumb_title h3').text(`Toutes les formations: ${categoryName}`);
            }
        }, 500);
    });

    $(document).on('click', '.show-more-all-btn', function() {
        const button = $(this);

        button.find('.button-text').hide();
        button.find('.button-spinner').show();
        button.prop('disabled', true);

        isShowingLimitedResults = true;

        setTimeout(() => {
            if (currentCategoryData) {
                updateFormationsDisplay(currentCategoryData);
                $('html, body').animate({
                    scrollTop: formationsContainer.offset().top - 100
                }, 500);
                $('.breadcrumb_title h3').text('Toutes les formations disponibles');
            }
        }, 500);
    });

    $(document).on('click', '.show-less-btn', function() {
        const button = $(this);
        const categoryName = button.data('category');

        button.find('.button-text').hide();
        button.find('.button-spinner').show();
        button.prop('disabled', true);

        isShowingLimitedResults = false;

        setTimeout(() => {
            if (currentCategoryData) {
                updateFormationsDisplay(currentCategoryData);
                $('html, body').animate({
                    scrollTop: formationsContainer.offset().top - 100
                }, 500);
                $('.breadcrumb_title h3').text(`Formations: ${categoryName}`);
            }
        }, 500);
    });

    $(document).on('click', '.show-less-all-btn', function() {
        const button = $(this);

        button.find('.button-text').hide();
        button.find('.button-spinner').show();
        button.prop('disabled', true);

        isShowingLimitedResults = false;

        setTimeout(() => {
            if (currentCategoryData) {
                updateFormationsDisplay(currentCategoryData);
                $('html, body').animate({
                    scrollTop: formationsContainer.offset().top - 100
                }, 500);
                $('.breadcrumb_title h3').text('Toutes les catégories');
            }
        }, 500);
    });

    $(document).on('click', '.delete-formation', function() {
    const formationId = $(this).data('id');

    console.log("ID de formation à supprimer:", formationId);

    if (!formationId) {
        console.error("Erreur: data-id manquant sur le bouton de suppression");
        return;
    }

    window.formationIdToDelete = formationId;

    // Vérifier le statut des réservations
    $.ajax({
        url: `/check-reservation-status/${formationId}`,
        type: 'GET',
        beforeSend: function() {
            console.log("Vérification du statut des réservations...");
        },
        success: function(response) {
            console.log("Réponse de vérification:", response);
            console.log("Type de réservation détecté:", response.reservation_type);

            const hasConfirmed = response.has_confirmed_reservation;
            const hasPending = response.has_pending_reservation;
            const reservationType = response.reservation_type;

            // Déterminer quel modal afficher selon les cas
            if (hasConfirmed && hasPending) {
                // CAS SPÉCIAL: Réservations confirmées ET en attente - MODAL COMBINÉ
                console.log("🚨 Formation avec réservations CONFIRMÉES ET EN ATTENTE - Modal combiné");
                $('#deleteWithCombinedReservationsModal').modal('show');
                setupDeleteForm('#deleteFormationWithCombinedReservationsForm', formationId);

            } else if (hasConfirmed && !hasPending) {
                // Cas 2: Seulement des réservations confirmées
                console.log("Formation avec réservations confirmées uniquement");
                $('#deleteWithConfirmedReservationsModal').modal('show');
                setupDeleteForm('#deleteFormationWithConfirmedReservationsForm', formationId);

            } else if (!hasConfirmed && hasPending) {
                // Cas 3: Seulement des réservations en attente
                console.log("Formation avec réservations en attente uniquement");
                $('#deleteWithPendingReservationsModal').modal('show');
                setupDeleteForm('#deleteFormationWithPendingReservationsForm', formationId);

            } else {
                // Cas 4: Aucune réservation
                console.log("Formation sans réservations");
                $('#deleteConfirmationModal').modal('show');
                setupDeleteForm('#deleteFormationForm', formationId);
            }
        },
        error: function(xhr, status, error) {
            console.error("Erreur lors de la vérification des réservations:", error);
            console.error("Détails de l'erreur:", xhr.responseText);

            // En cas d'erreur, afficher le modal normal par défaut
            if (typeof showNotification === 'function') {
                showNotification('warning', 'Impossible de vérifier les réservations. Procédure de suppression normale.');
            }
            $('#deleteConfirmationModal').modal('show');
            setupDeleteForm('#deleteFormationForm', formationId);
        }
    });
});

// NOUVEAU: Gestionnaire pour le bouton du modal combiné
$(document).on('click', '#confirmDeleteCombinedReservations', function() {
    console.log("Confirmation de suppression avec réservations combinées");

    // Récupérer l'ID de la formation stocké
    const formationId = window.formationIdToDelete;

    if (!formationId) {
        console.error("Erreur: ID de formation manquant");
        return;
    }

    // Configurer et soumettre le formulaire
    setupDeleteForm('#deleteFormationWithCombinedReservationsForm', formationId);

    // Soumettre le formulaire
    $('#deleteFormationWithCombinedReservationsForm').submit();

    // Fermer le modal
    $('#deleteWithCombinedReservationsModal').modal('hide');
});

// Fonction helper pour configurer le formulaire de suppression
function setupDeleteForm(formSelector, formationId) {
    const deleteUrl = `/formation/${formationId}`;
    $(formSelector).attr('action', deleteUrl);

    console.log(`Formulaire ${formSelector} configuré avec l'URL: ${deleteUrl}`);
}

// Debug: Fonction pour tester manuellement les modals
function testModals() {
    console.log("Test des modals disponibles:");
    console.log("- deleteConfirmationModal:", $('#deleteConfirmationModal').length > 0);
    console.log("- deleteWithConfirmedReservationsModal:", $('#deleteWithConfirmedReservationsModal').length > 0);
    console.log("- deleteWithPendingReservationsModal:", $('#deleteWithPendingReservationsModal').length > 0);
    console.log("- deleteWithCombinedReservationsModal:", $('#deleteWithCombinedReservationsModal').length > 0);
}
//     $(document).on('click', '.delete-formation', function() {
//     const formationId = $(this).data('id');

//     console.log("ID de formation à supprimer:", formationId);

//     if (!formationId) {
//         console.error("Erreur: data-id manquant sur le bouton de suppression");
//         return;
//     }

//     window.formationIdToDelete = formationId;

//     // Vérifier le statut des réservations
//     $.ajax({
//         url: `/check-reservation-status/${formationId}`,
//         type: 'GET',
//         beforeSend: function() {
//             console.log("Vérification du statut des réservations...");
//         },
//         success: function(response) {
//             console.log("Réponse de vérification:", response);
//             console.log("Type de réservation détecté:", response.reservation_type);

//             const hasConfirmed = response.has_confirmed_reservation;
//             const hasPending = response.has_pending_reservation;
//             const reservationType = response.reservation_type;

//             // Déterminer quel modal afficher selon les cas
//             if (hasConfirmed && hasPending) {
//                 // CAS SPÉCIAL: Réservations confirmées ET en attente - MODAL COMBINÉ
//                 console.log("🚨 Formation avec réservations CONFIRMÉES ET EN ATTENTE - Modal combiné");
//                 $('#deleteWithCombinedReservationsModal').modal('show');
//                 setupDeleteForm('#deleteFormationWithCombinedReservationsForm', formationId);

//             } else if (hasConfirmed && !hasPending) {
//                 // Cas 2: Seulement des réservations confirmées
//                 console.log("Formation avec réservations confirmées uniquement");
//                 $('#deleteWithConfirmedReservationsModal').modal('show');
//                 setupDeleteForm('#deleteFormationWithConfirmedReservationsForm', formationId);

//             } else if (!hasConfirmed && hasPending) {
//                 // Cas 3: Seulement des réservations en attente
//                 console.log("Formation avec réservations en attente uniquement");
//                 $('#deleteWithPendingReservationsModal').modal('show');
//                 setupDeleteForm('#deleteFormationWithPendingReservationsForm', formationId);

//             } else {
//                 // Cas 4: Aucune réservation
//                 console.log("Formation sans réservations");
//                 $('#deleteConfirmationModal').modal('show');
//                 setupDeleteForm('#deleteFormationForm', formationId);
//             }
//         },
//         error: function(xhr, status, error) {
//             console.error("Erreur lors de la vérification des réservations:", error);
//             console.error("Détails de l'erreur:", xhr.responseText);

//             // En cas d'erreur, afficher le modal normal par défaut
//             if (typeof showNotification === 'function') {
//                 showNotification('warning', 'Impossible de vérifier les réservations. Procédure de suppression normale.');
//             }
//             $('#deleteConfirmationModal').modal('show');
//             setupDeleteForm('#deleteFormationForm', formationId);
//         }
//     });
// });

// // Fonction helper pour configurer le formulaire de suppression
// function setupDeleteForm(formSelector, formationId) {
//     const deleteUrl = `/formation/${formationId}`;
//     $(formSelector).attr('action', deleteUrl);

//     console.log(`Formulaire ${formSelector} configuré avec l'URL: ${deleteUrl}`);
// }

// Debug: Fonction pour tester manuellement les modals
// function testModals() {
//     console.log("Test des modals disponibles:");
//     console.log("- deleteConfirmationModal:", $('#deleteConfirmationModal').length > 0);
//     console.log("- deleteWithConfirmedReservationsModal:", $('#deleteWithConfirmedReservationsModal').length > 0);
//     console.log("- deleteWithPendingReservationsModal:", $('#deleteWithPendingReservationsModal').length > 0);
//     console.log("- deleteWithCombinedReservationsModal:", $('#deleteWithCombinedReservationsModal').length > 0);
// }

// $(document).on('click', '.delete-formation', function() {
//     const formationId = $(this).data('id');

//     console.log("ID de formation à supprimer:", formationId);

//     if (!formationId) {
//         console.error("Erreur: data-id manquant sur le bouton de suppression");
//         return;
//     }

//     window.formationIdToDelete = formationId;

//     // Vérifier le statut des réservations
//     $.ajax({
//         url: `/check-reservation-status/${formationId}`,
//         type: 'GET',
//         beforeSend: function() {
//             console.log("Vérification du statut des réservations...");
//         },
//         success: function(response) {
//             console.log("Réponse de vérification:", response);

//             const hasConfirmed = response.has_confirmed_reservation;
//             const hasPending = response.has_pending_reservation;

//             // Déterminer quel modal afficher selon les cas
//             if (hasConfirmed && hasPending) {
//                 // Cas 1: Réservations confirmées ET en attente
//                 console.log("Formation avec réservations confirmées et en attente");
//                 $('#deleteWithMixedReservationsModal').modal('show');
//                 setupDeleteForm('#deleteFormationWithMixedReservationsForm', formationId);

//             } else if (hasConfirmed && !hasPending) {
//                 // Cas 2: Seulement des réservations confirmées
//                 console.log("Formation avec réservations confirmées uniquement");
//                 $('#deleteWithConfirmedReservationsModal').modal('show');
//                 setupDeleteForm('#deleteFormationWithConfirmedReservationsForm', formationId);

//             } else if (!hasConfirmed && hasPending) {
//                 // Cas 3: Seulement des réservations en attente
//                 console.log("Formation avec réservations en attente uniquement");
//                 $('#deleteWithPendingReservationsModal').modal('show');
//                 setupDeleteForm('#deleteFormationWithPendingReservationsForm', formationId);

//             } else {
//                 // Cas 4: Aucune réservation
//                 console.log("Formation sans réservations");
//                 $('#deleteConfirmationModal').modal('show');
//                 setupDeleteForm('#deleteFormationForm', formationId);
//             }
//         },
//         error: function(xhr, status, error) {
//             console.error("Erreur lors de la vérification des réservations:", error);

//             // En cas d'erreur, afficher le modal normal par défaut
//             showNotification('warning', 'Impossible de vérifier les réservations. Procédure de suppression normale.');
//             $('#deleteConfirmationModal').modal('show');
//             setupDeleteForm('#deleteFormationForm', formationId);
//         }
//     });
// });

// // Fonction helper pour configurer le formulaire de suppression
// function setupDeleteForm(formSelector, formationId) {
//     const deleteUrl = `/formations/${formationId}`;
//     $(formSelector).attr('action', deleteUrl);

//     console.log(`Formulaire ${formSelector} configuré avec l'URL: ${deleteUrl}`);
// }

// Optionnel: Ajouter des logs pour le debugging
$(document).ready(function() {
    // Debug: Vérifier que tous les modals sont présents
    const modals = [
        '#deleteConfirmationModal',
        '#deleteWithConfirmedReservationsModal',
        '#deleteWithPendingReservationsModal',
        // '#deleteWithMixedReservationsModal'
                '#deleteWithCombinedReservationsModal'

    ];

    modals.forEach(function(modalId) {
        if ($(modalId).length === 0) {
            console.warn(`Modal ${modalId} non trouvé dans le DOM`);
        }
    });
});
    function applyUrlFilters() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('category_title')) {
            const categoryTitle = decodeURIComponent(urlParams.get('category_title'));
            const categoryId = getCategoryIdByTitle(categoryTitle);
            if (categoryId) {
                $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
            } else {
                $('#category-all').prop('checked', true);
            }
        } else {
            $('#category-all').prop('checked', true);
        }

        if (urlParams.has('status')) {
            const status = urlParams.get('status');
            statusFilter.val(status);
        } else {
            if (!(userIsAdmin || userIsProf)) {
                statusFilter.val('1');
            } else {
                statusFilter.val('');
            }
        }

        if (urlParams.has('rating')) {
            const rating = urlParams.get('rating');
            $(`input[name="rating_filter"][value="${rating}"]`).prop('checked', true);
        } else {
            $('#rating-all').prop('checked', true);
        }

        if (urlParams.has('search')) {
            searchInput.val(urlParams.get('search'));
            if ((searchInput.val() || '').trim() !== '') {
                $('.product-search i').removeClass('fa-search').addClass('fa-times');
            }
        }

        if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
            statusFilter.trigger('change.select2');
        }
    }

    if ($.fn.select2) {
        $('.select2').select2();
    }

    applyUrlFilters();
    loadFilteredFormations();
});
