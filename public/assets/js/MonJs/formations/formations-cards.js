
function initFooterVisibility() {
    console.log("Initialisation de la visibilité du footer");
    const footer = document.querySelector('.footer');
    if (footer) {
        footer.style.display = 'none';
        console.log("Footer masqué pendant le chargement");
    }
}
function showFooterAfterLoading() {
    console.log("Affichage du footer après chargement des formations");
    const footer = document.querySelector('.footer');
    if (footer) {
        // Petite animation pour l'apparition du footer
        footer.style.display = 'block';
        footer.style.opacity = '0';
        footer.style.transition = 'opacity 0.5s ease-in-out';
        setTimeout(() => {
            footer.style.opacity = '1';
        }, 100);

        console.log("Footer affiché avec animation");
    }
}
function isSpinnerActive() {
    const spinners = document.querySelectorAll('.spinner-border, .loading, .loader, .fa-spinner');
    for (let spinner of spinners) {
        if (spinner && (spinner.offsetParent !== null || window.getComputedStyle(spinner).display !== 'none')) {
            return true;
        }
    }
    return false;
}
function areFormationCardsLoaded() {
    const formationCards = document.querySelectorAll('.formation-item, .card, .product-box');
    console.log(`Nombre de cartes trouvées: ${formationCards.length}`);
    return formationCards.length > 0;
}
function monitorLoadingAndShowFooter() {
    console.log("Surveillance du chargement pour affichage du footer");
    let checkCount = 0;
    const maxChecks = 50; // Maximum 5 secondes (50 x 100ms)
    const checkInterval = setInterval(() => {
        checkCount++;
        const spinnerActive = isSpinnerActive();
        const cardsLoaded = areFormationCardsLoaded();
        console.log(`Check ${checkCount}: Spinner actif: ${spinnerActive}, Cartes chargées: ${cardsLoaded}`);
        // Afficher le footer si le spinner n'est plus actif ET que les cartes sont chargées
        // OU si on a atteint le maximum de vérifications
        if ((!spinnerActive && cardsLoaded) || checkCount >= maxChecks) {
            clearInterval(checkInterval);

            // Attendre un petit délai supplémentaire pour s'assurer que tout est rendu
            setTimeout(() => {
                showFooterAfterLoading();
            }, 300);

            console.log(`Footer sera affiché - Raison: ${!spinnerActive && cardsLoaded ? 'Chargement terminé' : 'Timeout atteint'}`);
        }
    }, 100);
}
const originalInitButtonLayout = window.initButtonLayout || function() {};
function initButtonLayout() {
    console.log("Initialisation de la mise en page des boutons avec gestion du footer");
    initFooterVisibility();
    originalInitButtonLayout();
    addButtonStyles();
    initUnauthenticatedLeftShift();
    monitorLoadingAndShowFooter();
    document.addEventListener('shown.bs.modal', function(event) {
        const modal = event.target;
        if (modal.id && modal.id.startsWith('formation-modal-')) {
            const formationId = modal.id.split('-').pop();
            updateFormationStatus(formationId, true);
        }
    });
}
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM chargé - Initialisation avec gestion du footer");
    initFooterVisibility();
    initUnauthenticatedLeftShift();
    setTimeout(() => {
        if (areFormationCardsLoaded() && !isSpinnerActive()) {
            showFooterAfterLoading();
        }
    }, 1000);
});

if (typeof $ !== 'undefined') {
    $(document).ajaxComplete(function() {
        console.log("Requête AJAX terminée - Vérification pour affichage du footer");
        setTimeout(() => {
            if (areFormationCardsLoaded() && !isSpinnerActive()) {
                showFooterAfterLoading();
            }
        }, 500);
    });
}
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(function(mutations) {
        let cardsAdded = false;

        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.classList && (node.classList.contains('formation-item') ||
                                              node.classList.contains('card') ||
                                              node.classList.contains('product-box'))) {
                            cardsAdded = true;
                        }
                        // Vérifier aussi les enfants du nœud ajouté
                        if (node.querySelector && node.querySelector('.formation-item, .card, .product-box')) {
                            cardsAdded = true;
                        }
                    }
                });
            }
        });

        if (cardsAdded) {
            console.log("Nouvelles cartes détectées - Vérification pour affichage du footer");
            setTimeout(() => {
                if (areFormationCardsLoaded() && !isSpinnerActive()) {
                    showFooterAfterLoading();
                }
            }, 300);
        }
    });

    // Commencer l'observation
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

function isFormationComplete(formationId, forceRefresh = true) {
    console.log(`Vérification de l'état de formation pour ID: ${formationId} (toujours depuis le serveur)`);
    const timestamp = new Date().getTime();
    const url = `/get-remaining-seats/${formationId}?_=${timestamp}`;
    console.log(`Appel API pour vérifier les places de formation #${formationId} à ${url}`);

    return fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Réponse API reçue:", JSON.stringify(data));

            // Vérifier si la réponse contient directement is_complete
            let isComplete = false;
            if (data.is_complete !== undefined) {
                // Utiliser la valeur is_complete directement si disponible
                isComplete = Boolean(data.is_complete);
                console.log(`is_complete depuis API: ${data.is_complete}, converti en: ${isComplete}`);
            } else if (data.remaining_seats !== undefined && data.total_seats !== undefined) {
                // Sinon calculer is_complete sur base des places disponibles
                isComplete = (parseInt(data.remaining_seats) === 0 && parseInt(data.total_seats) > 0);
                console.log(`Calcul is_complete: remaining_seats=${data.remaining_seats}, total_seats=${data.total_seats}, resultat=${isComplete}`);
            }

            console.log(`Formation #${formationId} est complète: ${isComplete}`);
            // CORRECTION: Ne plus mettre en cache, juste retourner le résultat
            return isComplete;
        })
        .catch(error => {
            console.error("Erreur lors de la vérification des places disponibles:", error);
            return false; // Par défaut, considérer que la formation n'est pas complète
        });
}

function calculateSeatsInfo(formation) {
    console.log('=== DEBUG calculateSeatsInfo ===');
    console.log('Formation reçue:', formation);
    console.log('formation.total_seats:', formation.total_seats, 'type:', typeof formation.total_seats);
    console.log('formation.remaining_seats:', formation.remaining_seats, 'type:', typeof formation.remaining_seats);
    console.log('formation.is_complete:', formation.is_complete, 'type:', typeof formation.is_complete);
    const totalSeats = parseInt(formation.total_seats || 0);
    const remainingSeats = formation.remaining_seats !== undefined ? parseInt(formation.remaining_seats) : totalSeats;
    console.log('totalSeats après parseInt:', totalSeats);
    console.log('remainingSeats après parseInt:', remainingSeats);
    const isCompleteFromFlag = (formation.is_complete === true || formation.is_complete === 'true');
    const isCompleteFromSeats = (remainingSeats === 0 && totalSeats > 0);
    const isComplete = isCompleteFromFlag || isCompleteFromSeats;

    console.log('isCompleteFromFlag:', isCompleteFromFlag);
    console.log('isCompleteFromSeats:', isCompleteFromSeats);
    console.log('isComplete final:', isComplete);
    let actualRemainingSeats = isComplete ? 0 : remainingSeats;
    let occupiedSeats = isComplete ? totalSeats : Math.max(0, totalSeats - remainingSeats);
    let occupancyRate = isComplete ? 100 : (totalSeats > 0 ? Math.round((occupiedSeats / totalSeats) * 100) : 0);
    occupancyRate = Math.min(100, Math.max(0, occupancyRate));
    let progressClass = 'bg-bleu';
    if (isComplete || occupancyRate > 90) {
        progressClass = 'bg-danger';
    } else if (occupancyRate >= 50) {
        progressClass = 'bg-warning';
    }

    console.log(`calculateSeatsInfo - Formation: Total=${totalSeats}, Restant=${actualRemainingSeats}, Occupé=${occupiedSeats}, Taux=${occupancyRate}%, Complet=${isComplete}, Classe=${progressClass}`);
    console.log('=== FIN DEBUG calculateSeatsInfo ===');

    return {
        totalSeats,
        remainingSeats: actualRemainingSeats,
        occupiedSeats,
        occupancyRate,
        progressClass,
        isComplete
    };
}
function showFormationDetails(formationId) {
    console.log('Démarrage de showFormationDetails pour formation ID:', formationId);
    const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    const inCart = cartFormations.includes(formationId.toString());
    const formationElement = document.querySelector(`.formation-item[data-id="${formationId}"]`);
    let isStarted = false;

    if (formationElement) {
        const startDateStr = formationElement.getAttribute('data-start-date');
        if (startDateStr) {
            const startDate = new Date(startDateStr);
            const currentDate = new Date();
            isStarted = currentDate > startDate;
        }
    }
    const modalButtons = document.querySelectorAll(`#formation-modal-${formationId} .addcart-btn .btn[href="/panier"]`);
    modalButtons.forEach(button => {
        if (inCart) {
            button.textContent = 'Accéder au panier';
            button.setAttribute('data-in-cart', 'true');
        } else if (isStarted) {
            button.textContent = 'Ajouter au panier';
            button.classList.add('btn-sky', 'disabled');
            button.disabled = true;
        }
    });
    // Nettoyer d'abord tout backdrop existant
    const existingBackdrops = document.querySelectorAll('.modal-backdrop');
    existingBackdrops.forEach(backdrop => backdrop.remove());
    const modal = document.getElementById(`formation-modal-${formationId}`);
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');

        const bsModal = new bootstrap.Modal(modal, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        bsModal.show();
    } else {
        console.error(`Modal pour la formation #${formationId} non trouvé`);
        return;
    }

    // CORRECTION: Toujours vérifier l'état depuis le serveur
    isFormationComplete(formationId)
        .then(isComplete => {
            console.log('Formation ID:', formationId, 'inCart:', inCart, 'isComplete:', isComplete);

            // Mettre à jour le bouton avec l'état actuel
            updateAddToCartButton(formationId, inCart, isComplete);

            // Mettre à jour la barre de progression avec les données actuelles
            updateProgressBarFromServer(modal, formationId);
        })
        .catch(error => {
            console.error('Erreur lors de la vérification de l\'état de la formation:', error);
        });
}
function updateProgressBarFromServer(modal, formationId) {
    if (!modal) return;

    console.log(`updateProgressBarFromServer - Modal:`, modal, `formationId:`, formationId);

    // Récupérer les données actuelles depuis le serveur
    const timestamp = new Date().getTime();
    const url = `/get-remaining-seats/${formationId}?_=${timestamp}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Calculer les informations de places avec les données actuelles
            const seatsInfo = calculateSeatsInfo({
                total_seats: data.total_seats,
                remaining_seats: data.remaining_seats,
                is_complete: data.is_complete
            });

            // Mettre à jour le badge des places occupées
            const occupancyTexts = modal.querySelectorAll('p strong');
            let occupancyBadge = null;

            for (const textElement of occupancyTexts) {
                if (textElement.textContent.includes("Places occupées")) {
                    if (textElement.nextElementSibling && textElement.nextElementSibling.classList.contains('badge')) {
                        occupancyBadge = textElement.nextElementSibling;
                        break;
                    }
                    const parentP = textElement.closest('p');
                    if (parentP) {
                        const badgeInP = parentP.querySelector('.badge');
                        if (badgeInP) {
                            occupancyBadge = badgeInP;
                            break;
                        }
                    }
                }
            }

            if (occupancyBadge) {
                occupancyBadge.textContent = `${seatsInfo.occupiedSeats} / ${seatsInfo.totalSeats}`;
                occupancyBadge.className = `badge ${seatsInfo.isComplete ? 'badge-danger' : (seatsInfo.remainingSeats < seatsInfo.totalSeats * 0.2 ? 'badge-warning' : 'badge-bleu')} text-white`;
                console.log("Badge des places occupées mis à jour:", occupancyBadge.textContent, occupancyBadge.className);
            }

            // Mettre à jour la barre de progression
            const progressBar = modal.querySelector('.progress .progress-bar');
            if (progressBar) {
                progressBar.style.width = `${seatsInfo.occupancyRate}%`;
                progressBar.setAttribute('aria-valuenow', seatsInfo.occupancyRate);

                progressBar.classList.remove('bg-bleu', 'bg-warning', 'bg-danger');
                progressBar.classList.add(seatsInfo.progressClass);

                console.log("Barre de progression mise à jour:",
                    `largeur:${seatsInfo.occupancyRate}%`,
                    `classe:${progressBar.className}`,
                    `isComplete:${seatsInfo.isComplete}`);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des données de progression:', error);
        });
}
function updateFormationStatus(formationId, forceRefresh = true) {
    console.log(`Mise à jour du statut pour formation ID ${formationId} (toujours depuis le serveur)`);
    // Vérifier si la formation est dans le panier
    const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    const inCart = cartFormations.includes(formationId.toString());

    // Récupérer la date de début
    const formationElement = document.querySelector(`.formation-item[data-id="${formationId}"]`);
    let isStarted = false;

    if (formationElement) {
        const startDateStr = formationElement.getAttribute('data-start-date');
        if (startDateStr) {
            const startDate = new Date(startDateStr);
            const currentDate = new Date();
            isStarted = currentDate > startDate;
        }
    }

    // CORRECTION: Toujours vérifier depuis le serveur
    isFormationComplete(formationId)
        .then(isComplete => {
            // Mettre à jour les boutons
            updateAddToCartButton(formationId, inCart, isComplete);

            // Trouver l'élément de formation
            if (formationElement) {
                // Mettre à jour l'attribut data
                formationElement.setAttribute('data-is-complete', isComplete);

                // Mettre à jour le badge
                updateCompleteBadge(formationElement, isComplete);
            }
            // Mettre à jour le modal si ouvert
            const modal = document.getElementById(`formation-modal-${formationId}`);
            if (modal && modal.classList.contains('show')) {
                updateProgressBarFromServer(modal, formationId);
            }
        })
        .catch(error => {
            console.error(`Erreur lors de la mise à jour du statut pour formation ${formationId}:`, error);
        });
}
window.isFormationComplete = isFormationComplete;
window.updateAddToCartButton = updateAddToCartButton;
window.showFormationDetails = showFormationDetails;
window.updateProgressBarFromServer = updateProgressBarFromServer;
window.calculateSeatsInfo = calculateSeatsInfo;
window.createFormationCard = createFormationCard;
window.updateFormationStatus = updateFormationStatus;
/** Met à jour l'affichage des boutons d'ajout au panier
 * @param {string|number} formationId - ID de la formation
 * @param {boolean} inCart - Indique si la formation est dans le panier
 * @param {boolean} isComplete - Indique si la formation est complète*/
function updateCompleteBadge(formationElement, isComplete) {
    if (!formationElement) {
        console.error("updateCompleteBadge: formationElement est null ou undefined");
        return;
    }
    const formationId = formationElement.getAttribute('data-id');
    console.log(`updateCompleteBadge - Formation ${formationId}: isComplete=${isComplete}`);
    // CORRECTION: Utiliser directement le paramètre isComplete sans appel API
    updateBadgeDisplay(formationElement, formationId, isComplete);

    // Mettre à jour l'attribut data de l'élément
    formationElement.setAttribute('data-is-complete', isComplete);
}
function updateBadgeDisplay(formationElement, formationId, isComplete) {
    console.log(`updateBadgeDisplay - Formation ${formationId}: isComplete=${isComplete}`);

    // Mettre à jour la carte principale
    const productImgs = formationElement.querySelectorAll('.product-img');

    productImgs.forEach(productImg => {
        let ribbon = productImg.querySelector('.ribbon-danger');

        if (isComplete === true) {
            // Ajouter le badge s'il n'existe pas
            if (!ribbon) {
                ribbon = document.createElement('div');
                ribbon.className = 'ribbon ribbon-danger';
                ribbon.textContent = 'Complète';

                // Déterminer la position selon les autres badges présents
                const hasGratuite = productImg.querySelector('.ribbon-warning') !== null;
                const hasDiscount = productImg.querySelector('.ribbon-success') !== null;

                if (hasGratuite && hasDiscount) {
                    ribbon.classList.add('ribbon-bottom-right');
                } else if (hasGratuite || hasDiscount) {
                    ribbon.style.top = '15px';
                }

                if (productImg.firstChild) {
                    productImg.insertBefore(ribbon, productImg.firstChild);
                } else {
                    productImg.appendChild(ribbon);
                }

                console.log(`Badge 'Complète' ajouté pour formation ${formationId}:`, ribbon);
            }
        } else {
            // Supprimer le badge s'il existe
            if (ribbon) {
                ribbon.remove();
                console.log(`Badge 'Complète' supprimé pour formation ${formationId}`);
            }
        }
    });
    // Mettre à jour le modal s'il existe
    const modal = document.getElementById(`formation-modal-${formationId}`);
    if (modal) {
        const modalProductImg = modal.querySelector('.product-img');
        if (modalProductImg) {
            let modalRibbon = modalProductImg.querySelector('.ribbon-danger');

            if (isComplete === true) {
                if (!modalRibbon) {
                    modalRibbon = document.createElement('div');
                    modalRibbon.className = 'ribbon ribbon-danger';
                    modalRibbon.textContent = 'Complète';

                    const hasGratuite = modalProductImg.querySelector('.ribbon-warning') !== null;
                    const hasDiscount = modalProductImg.querySelector('.ribbon-success') !== null;

                    if (hasGratuite && hasDiscount) {
                        modalRibbon.classList.add('ribbon-bottom-right');
                    } else if (hasGratuite || hasDiscount) {
                        modalRibbon.style.top = '15px';
                    }

                    if (modalProductImg.firstChild) {
                        modalProductImg.insertBefore(modalRibbon, modalProductImg.firstChild);
                    } else {
                        modalProductImg.appendChild(modalRibbon);
                    }

                    console.log(`Badge 'Complète' ajouté au modal pour formation ${formationId}`);
                }
            } else if (modalRibbon) {
                modalRibbon.remove();
                console.log(`Badge 'Complète' supprimé du modal pour formation ${formationId}`);
            }
        }
    }
}

function isFormationEnded(endDate) {
    const formationEndDate = new Date(endDate);
    const currentDate = new Date();
    return currentDate > formationEndDate;
}

// Fonction pour afficher le popup d'avertissement formation terminée
function showFormationEndedPopup() {
    console.log('Affichage du popup formation terminée');
    let popup = document.getElementById('formation-ended-popup');
    if (!popup) {
        popup = document.createElement('div');
        popup.id = 'formation-ended-popup';
        popup.innerHTML = `
            <div class="modal fade" id="formationEndedModal" tabindex="-1" aria-labelledby="formationEndedModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title text-white" id="formationEndedModalLabel">
                                <i class="icon-warning"></i> Formation Terminée
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="icon-calendar" style="font-size: 48px; color: #f39c12;"></i>
                            </div>
                            <h6 class="mb-3">Cette formation est déjà terminée</h6>
                            <p class="text-muted">Vous ne pouvez pas modifier une formation dont la date de fin est dépassée.</p>
                        </div>

                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(popup);
    }
    const modal = new bootstrap.Modal(document.getElementById('formationEndedModal'));
    modal.show();
}
/** Fonction pour initialiser la mise en page des boutons*/
function initButtonLayout() {
    console.log("Initialisation de la mise en page des boutons");
    // Ajouter les styles CSS nécessaires
    addButtonStyles();
    // Ajouter des écouteurs d'événements pour les modals
    document.addEventListener('shown.bs.modal', function(event) {
        const modal = event.target;
        if (modal.id && modal.id.startsWith('formation-modal-')) {
            const formationId = modal.id.split('-').pop();

            // Mettre à jour le statut de la formation dans le modal
            updateFormationStatus(formationId, true);
        }
    });
}
/** Met à jour la mise en page des boutons pour une formation spécifique
 * @param {string|number} formationId - ID de la formation*/
function updateButtonLayout(formationId) {
    // Vérifier si cette formation est dans le panier
    const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    const inCart = cartFormations.includes(formationId.toString());
    // Utiliser notre fonction pour vérifier si la formation est complète
    isFormationComplete(formationId, true)
        .then(isComplete => {
            // Mettre à jour les boutons
            updateAddToCartButton(formationId, inCart, isComplete);

            // Trouver l'élément de formation
            const formationElement = document.querySelector(`.formation-item[data-id="${formationId}"]`);
            if (formationElement) {
                // Mettre à jour l'attribut data
                formationElement.setAttribute('data-is-complete', isComplete);

                // Mettre à jour le badge
                updateCompleteBadge(formationElement, isComplete);
            }
        });
    }
// Initialiser au chargement du document
document.addEventListener('DOMContentLoaded', function() {
    initButtonLayout();
});
// Écouteur d'événements pour les modals
document.addEventListener('shown.bs.modal', function(event) {
    const modal = event.target;
    if (modal.id && modal.id.startsWith('formation-modal-')) {
        const formationId = modal.id.split('-').pop();
        // Vérifier si cette formation est dans le panier
        const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
        const inCart = cartFormations.includes(formationId.toString());
        // Utiliser notre fonction pour vérifier si la formation est complète via l'API
        isFormationComplete(formationId)
            .then(isComplete => {
                updateAddToCartButton(formationId, inCart, isComplete);
        });
    }
});
function createFormationCard(formation, inCart = false, skipAsyncCheck = false) {
    let initialIsComplete = false;
    if (formation.is_complete !== undefined) {
        initialIsComplete = Boolean(formation.is_complete);
    } else if (formation.remaining_seats !== undefined && formation.total_seats !== undefined) {
        const remaining = parseInt(formation.remaining_seats) || 0;
        const total = parseInt(formation.total_seats) || 0;
        initialIsComplete = (remaining === 0 && total > 0);
    }
    console.log(`createFormationCard - Formation ID ${formation.id} - isComplete initial: ${initialIsComplete}`);

    const seatsInfo = calculateSeatsInfo({
        ...formation,
        is_complete: initialIsComplete
    });
    const { totalSeats, remainingSeats, occupiedSeats, occupancyRate, progressClass, isComplete } = seatsInfo;
    console.log(`Formation ${formation.title}: Total=${totalSeats}, Restant=${remainingSeats}, Occupé=${occupiedSeats}, Taux=${occupancyRate}%, Complète=${isComplete}, Classe=${progressClass}`);

    let priceHtml = '';
    if (formation.type === 'payante') {
        if (formation.discount > 0) {
            priceHtml = `
                ${parseFloat(formation.final_price).toFixed(2)} Dt
                <del>${parseFloat(formation.price).toFixed(2)} Dt</del>
            `;
        } else {
            priceHtml = `${parseFloat(formation.price).toFixed(2)} Dt`;
        }
    }
    const detailUrl = `/training/${formation.id}`;

    const startDate = new Date(formation.start_date);
    const endDate = new Date(formation.end_date);
    const formattedStartDate = `${startDate.getDate().toString().padStart(2, '0')}/${(startDate.getMonth()+1).toString().padStart(2, '0')}/${startDate.getFullYear()}`;
    const formattedEndDate = `${endDate.getDate().toString().padStart(2, '0')}/${(endDate.getMonth()+1).toString().padStart(2, '0')}/${endDate.getFullYear()}`;
    const coursCount = formation.cours_count || (formation.courses ? formation.courses.length : 0);
    const userName = formation.user ? formation.user.name : '';
    const userLastname = formation.user ? formation.user.lastname : '';
    const totalFeedbacks = formation.total_feedbacks || 0;
    const averageRating = formation.average_rating || 0;
    const ratingStarsHtml = generateRatingStarsHtml ? generateRatingStarsHtml(averageRating, totalFeedbacks) : '';
    const isAuthenticated = typeof userRoles !== 'undefined' && userRoles.length > 0;
    const isStudent = isAuthenticated && userRoles.includes('etudiant');
    const isAdminRole = isAuthenticated && (userRoles.includes('admin') || userRoles.includes('super-admin') || userRoles.includes('professeur'));
const showCartButtons = !isAdminRole; // Ne pas afficher les boutons pour admin/super-admin/professeur
    function getCompleteRibbonClass() {
        const hasGratuite = formation.type === 'gratuite';
        const hasDiscount = formation.discount > 0;
        if (hasGratuite && hasDiscount) {
            return 'ribbon-bottom-right';
        } else if (hasGratuite || hasDiscount) {
            return '';
        } else {
            return '';
        }
    }
    const completeRibbonClass = getCompleteRibbonClass();
    const completeRibbonStyle = (formation.type === 'gratuite' || formation.discount > 0) && completeRibbonClass === ''
        ? 'style="top: 15px !important;"'
        : '';

    const dataAttributes = `
        data-category-id="${formation.category_id}"
        data-status="${formation.status}"
        data-id="${formation.id}"
        data-is-complete="${isComplete}"
        data-start-date="${formation.start_date}"

        data-description="${(formation.description || '').replace(/"/g, '&quot;')}"
    `;
  const cardHtml = `
    <div class="col-xl-3 col-sm-6 xl-4 formation-item" ${dataAttributes}>
        <div class="card h-100">
            <div class="product-box d-flex flex-column h-100">
                <div class="product-img" style="height: 200px; overflow: hidden; position: relative;">
                    ${isComplete ? `<div class="ribbon ribbon-danger ${completeRibbonClass}" ${completeRibbonStyle}>Complète</div>` : ''}
                    ${formation.type === 'gratuite' ? '<div class="ribbon ribbon-warning">Gratuite</div>' : ''}
                    ${formation.discount > 0 ? `<div class="ribbon ribbon-success ribbon-right">${formation.discount}%</div>` : ''}
                    <img class="img-fluid" src="${window.location.origin}/storage/${formation.image}" alt="${formation.title}" style="width: 100%; height: 100%; object-fit: cover;" />
                    <div class="product-hover">
                        <ul>
                            <li>
                                <a href="javascript:void(0)" onclick="showFormationDetails(${formation.id})" data-bs-toggle="modal" data-bs-target="#formation-modal-${formation.id}">
                                    <i class="icon-eye"></i>
                                </a>
                            </li>
                            ${(isAuthenticated && isStudent) ? `
                            <li>
                                <a href="/panier">
                                    <i class="icon-shopping-cart"></i>
                                </a>
                            </li>
                            ` : ''}
                          ${typeof userRoles !== 'undefined' && (userRoles.includes('admin') || userRoles.includes('super-admin') || userRoles.includes('professeur')) ? `
<li>
    <a href="javascript:void(0)" onclick="handleFormationEdit(${formation.id}, '${formation.end_date}')">
        <i class="icon-pencil"></i>
    </a>
</li>
<li>
    <a href="javascript:void(0)" class="delete-formation" data-id="${formation.id}">
        <i class="icon-trash"></i>
    </a>
</li>
` : ''}
                            </ul>
                        </div>
                    </div>
                   <div class="product-details flex-grow-1 d-flex flex-column p-3">
                    <div class="card-content flex-grow-1">
                        <a href="/admin/formation/${formation.id}">
                            <h4 class="formation-title" title="${formation.title}">${formation.title}</h4>
                        </a>
                        <p class="mb-1">Par ${userName} ${userLastname}</p>
                        <div class="rating-wrapper mb-2">
                ${ratingStarsHtml} ${totalFeedbacks > 0 ? `<span>(${totalFeedbacks})</span>` : ''}
                        </div>

                    <div class="product-price-container">
                        <div class="product-price mb-0">
                            ${formation.type === 'payante' ? priceHtml : ''}
                        </div>
                    </div>
                     </div>

                    <!-- Modal for detailed view -->
                    <div class="modal fade" id="formation-modal-${formation.id}">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">${formation.title}</h5>
                                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="product-box row">
                                        <div class="product-img col-lg-6" style="height: 300px; overflow: hidden; position: relative;">
                                            ${formation.type === 'gratuite' ? '<div class="ribbon ribbon-warning">Gratuite</div>' : ''}
                                            ${formation.discount > 0 ? `<div class="ribbon ribbon-success ribbon-right">${formation.discount}%</div>` : ''}
                                            ${isComplete ? `<div class="ribbon ribbon-danger ${completeRibbonClass}" ${completeRibbonStyle}>Complète</div>` : ''}
                                            <img class="img-fluid" src="${window.location.origin}/storage/${formation.image}" alt="${formation.title}" style="width: 100%; height: 100%; object-fit: cover;" />
                                        </div>
                                        <div class="product-details col-lg-6 text-start">
                                            <a href="/admin/formation/${formation.id}">
                                                <h4>${formation.title}</h4>
                                            </a>
                                            <div class="rating-wrapper mb-2">
                                                ${ratingStarsHtml}
                                            </div>
                                            <div class="product-price">
                                                ${formation.type === 'payante' ? priceHtml : ''}
                                            </div>
                                            <div class="product-view">
                                                <p class="mb-0">${formation.description || ''}</p>
                                                <div class="mt-3">
                                                    <p><strong>Places occupées:</strong>
                                                        <span class="badge ${isComplete ? 'badge-danger' : (remainingSeats < totalSeats * 0.2 ? 'badge-warning' : 'badge-bleu')} text-white">
                                                            ${occupiedSeats} / ${totalSeats}
                                                        </span>
                                                    </p>
                                                    <div class="progress mb-3" style="height: 4px;">
                                                        <div class="progress-bar ${progressClass}" role="progressbar"
                                                            style="width: ${occupancyRate}%"
                                                            aria-valuenow="${occupancyRate}"
                                                            aria-valuemin="0"
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <p><strong>Durée:</strong> ${formation.duration || '0 heures'}</p>
                                                    <p><strong>Date début:</strong> ${formattedStartDate}</p>
                                                    <p><strong>Date fin:</strong> ${formattedEndDate}</p>
                                                    <p><strong>Nombre de cours:</strong> ${coursCount}</p>
                                                </div>
                                            </div>
                                            <div class="addcart-btn">
                                                ${showCartButtons ? `
                                                <a class="btn ${inCart ? 'btn-primary' : (isComplete ? 'btn-secondary disabled' : 'btn-primary')}"
                                                   ${isComplete && !inCart ? 'disabled' : ''}
                                                   ${inCart ? 'data-in-cart="true"' : ''}
                                                   href="${isStudent ? '/panier' : 'javascript:void(0)'}"
                                                   ${!isStudent && !isComplete ? 'onclick="showLoginPopup()"' : ''}>
                                                    ${inCart ? 'Accéder au panier' : (isComplete ? 'Ajouter au panier' : 'Ajouter au panier')}
                                                </a>
                                                ` : ''}
                                                    <a class="btn btn-primary" href="${detailUrl}">Voir détails</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    return cardHtml;
}

// Fonction pour vérifier si l'utilisateur a une réservation pour une formation spécifique
function checkUserReservationForFormation(formationId) {
    console.log(`Vérification de réservation pour formation ${formationId}`);

    // Si l'utilisateur n'est pas authentifié, retourner false
    const isAuthenticated = typeof userRoles !== 'undefined' && userRoles.length > 0;
    if (!isAuthenticated) {
        return Promise.resolve(false);
    }

    const timestamp = new Date().getTime();
    const url = `/check-user-reservation/${formationId}?_=${timestamp}`;

    return fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log(`Réponse de vérification de réservation pour formation ${formationId}:`, data);
            return Boolean(data.has_reservation);
        })
        .catch(error => {
            console.error(`Erreur lors de la vérification de réservation pour formation ${formationId}:`, error);
            return false;
        });
}


function checkFormationHasConfirmedReservation(formationId) {
    console.log(`Vérification des réservations confirmées pour la formation ${formationId}`);

    const timestamp = new Date().getTime();
    const url = `/check-confirmed-reservation/${formationId}?_=${timestamp}`;

    return fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log(`Résultat de la vérification des réservations confirmées pour formation ${formationId}:`, data);
            return Boolean(data.has_confirmed_reservation);
        })
        .catch(error => {
            console.error(`Erreur lors de la vérification des réservations confirmées pour formation ${formationId}:`, error);
            return false;
        });
}

function handleFormationEdit(formationId, endDate) {
    console.log(`Tentative de modification de la formation ${formationId} avec date de fin: ${endDate}`);

    if (isFormationEnded(endDate)) {
        console.log('Formation terminée - affichage du popup d\'avertissement');
        showFormationEndedPopup();
        return;
    }

    // Vérifier si la formation a une réservation confirmée (status=1)
    checkFormationHasConfirmedReservation(formationId)
        .then(hasConfirmedReservation => {
            if (hasConfirmedReservation) {
                console.log('Formation avec réservation confirmée - affichage du modal de confirmation');
                showConfirmedReservationModal(formationId);
            } else {
                console.log('Aucune réservation confirmée - redirection vers la page de modification');
                window.location.href = `/formation/${formationId}/edit`;
            }
        })
        .catch(error => {
            console.error(`Erreur lors de la vérification des réservations pour formation ${formationId}:`, error);
            // En cas d'erreur, permettre la modification par défaut
            window.location.href = `/formation/${formationId}/edit`;
        });
}

window.isFormationEnded = isFormationEnded;
window.showFormationEndedPopup = showFormationEndedPopup;
window.handleFormationEdit = handleFormationEdit;
function updateAddToCartButton(formationId, inCart, isComplete) {
    console.log(`Mise à jour bouton pour formation #${formationId}: inCart=${inCart}, isComplete=${isComplete}`);
    const isAuthenticated = typeof userRoles !== 'undefined' && userRoles.length > 0;
    const isStudent = isAuthenticated && userRoles.includes('etudiant');
    const formationElement = document.querySelector(`.formation-item[data-id="${formationId}"]`);
    let isStarted = false;

    if (formationElement) {
        const startDateStr = formationElement.getAttribute('data-start-date');

        if (startDateStr) {
            const startDate = new Date(startDateStr);
            const currentDate = new Date();
            isStarted = currentDate > startDate;
            console.log(`Formation #${formationId} - Date de début: ${startDate}, Date actuelle: ${currentDate}, Commencée: ${isStarted}`);
        }
    }

    const buttons = document.querySelectorAll(`.formation-item[data-id="${formationId}"] .addcart-btn .btn[href="/panier"],
                                          .formation-item[data-id="${formationId}"] .addcart-btn .btn[href="javascript:void(0)"],
                                          .formation-item[data-formation-id="${formationId}"] .addcart-btn .btn[href="/panier"],
                                          .formation-item[data-formation-id="${formationId}"] .addcart-btn .btn[href="javascript:void(0)"],
                                          .formation-item[data-category-id="${formationId}"] .addcart-btn .btn[href="/panier"],
                                          .formation-item[data-category-id="${formationId}"] .addcart-btn .btn[href="javascript:void(0)"],
                                          #formation-modal-${formationId} .addcart-btn .btn[href="/panier"],
                                          #formation-modal-${formationId} .addcart-btn .btn[href="javascript:void(0)"]`);

    if (buttons.length === 0) {
        console.warn(`Aucun bouton trouvé pour la formation #${formationId}`);
        return;
    }

    // Vérifier si l'utilisateur a une réservation pour cette formation spécifique
    checkUserReservationForFormation(formationId).then(hasReservation => {
        console.log(`Formation #${formationId} - Utilisateur a une réservation: ${hasReservation}`);

        buttons.forEach((button, index) => {
            console.log(`Traitement du bouton ${index + 1}/${buttons.length} pour formation #${formationId}`);
            button.classList.remove('btn-secondary', 'disabled', 'btn-primary', 'btn-sky');
            button.disabled = false;
            button.removeAttribute('onclick');

            if (hasReservation) {
                // L'utilisateur a une réservation pour cette formation spécifique
                console.log(`Formation #${formationId} - Utilisateur a une réservation. Configuration du bouton en "Voir mes réservations"`);
                button.textContent = 'Voir mes réservations';
                button.classList.add('btn-primary');
                button.href = '/mes-reservations';
                button.removeAttribute('data-in-cart');
            } else if (inCart && isStudent) {
                console.log(`Formation #${formationId} dans le panier. Configuration du bouton en "Accéder au panier"`);
                button.textContent = 'Accéder au panier';
                button.setAttribute('data-in-cart', 'true');
                button.href = '/panier';
                button.classList.add('btn-primary');
            } else if (isComplete) {
                console.log(`Formation #${formationId} complète. Configuration du bouton en "Ajouter au panier"`);
                button.textContent = 'Ajouter au panier';
                button.classList.add('btn-secondary', 'disabled');
                button.disabled = true;
                button.removeAttribute('data-in-cart');
                button.href = 'javascript:void(0)';
            } else if (isStarted && !inCart) {
                console.log(`Formation #${formationId} déjà commencée. Configuration du bouton en "Ajouter au panier" (désactivé)`);
                button.textContent = 'Ajouter au panier';
                button.classList.remove('btn-primary', 'btn-secondary', 'btn-success', 'btn-danger', 'btn-warning');
                button.classList.add('btn-sky', 'disabled');
                button.disabled = true;
                button.removeAttribute('data-in-cart');
                button.href = 'javascript:void(0)';
            } else {
                console.log(`Formation #${formationId} disponible. Configuration du bouton en "Ajouter au panier"`);
                button.textContent = 'Ajouter au panier';
                button.removeAttribute('data-in-cart');
                if (isStudent) {
                    button.href = '/panier';
                    button.classList.add('btn-primary');
                } else {
                    button.href = 'javascript:void(0)';
                    button.setAttribute('onclick', 'showLoginPopup()');
                    button.classList.add('btn-primary');
                }
            }
            console.log(`État final du bouton ${index + 1}: texte="${button.textContent}", disabled=${button.disabled}, classes="${button.className}"`);
        });
    }).catch(error => {
        console.error(`Erreur lors de la vérification de réservation pour formation ${formationId}:`, error);

    });
}
function showLoginPopup() {
    console.log('Affichage du popup de connexion');
    let popup = document.getElementById('login-popup');
    if (!popup) {
        popup = document.createElement('div');
        popup.id = 'login-popup';
        popup.innerHTML = `
            <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="loginModalLabel">
                                <i class="icon-lock"></i> Connexion requise
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="icon-user" style="font-size: 48px; color: #2B6ED4;"></i>
                            </div>
                            <h6 class="mb-3">Pour ajouter des formations à votre panier</h6>
                            <p class="text-muted">Vous devez être connecté avec un compte étudiant .</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <a href="/login" class="btn btn-primary">
                                <i class="icon-sign-in"></i> Se connecter
                            </a>
                            <a href="/register" class="btn btn" style="border-radius: 4px; color: #2B6ED4;">
    <i class="icon-user-plus"></i> S'inscrire
</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(popup);
    }
    const modal = new bootstrap.Modal(document.getElementById('loginModal'));
    modal.show();
}
// function addButtonStyles() {
//     const style = document.createElement('style');
//     style.textContent = `
//    .btn-sky {
//             background-color: rgb(141, 26, 186) !important;
//             color: #ffffff !important;
//             border-color: rgb(141, 26, 186) !important;
//         }

//         .btn-sky:hover {
//             background-color:rgb(141, 26, 186) !important;
//             border-color:rgb(141, 26, 186) !important;
//         }

//         .btn-sky:disabled {
//             background-color:rgb(141, 26, 186) !important;
//             border-color:rgb(141, 26, 186) !important;
//                 color: white !important;  // ← CORRECTION : texte blanc même quand désactivé

//             opacity: 0.8 !important;
//         }
//         .addcart-btn {
//             display: flex !important;
//             gap: 10px !important;
//             width: 100% !important;
//         }
//         .addcart-btn .btn {
//             flex: 1 !important;
//             white-space: nowrap !important;
//         }
//          .badge-bleu {
//             background-color:  #2B6ED4;
//             color: #ffffff !important;
//         }
//         body {
//             padding-right: 0 !important;
//             overflow-y: scroll !important;
//         }

//         body.modal-open {
//             overflow: hidden !important;
//             padding-right: 0 !important;
//         }

//         .modal-open .modal {
//             overflow-x: hidden;
//             overflow-y: auto;
//         }

//         /* NOUVEAUX STYLES: Styles pour le popup de connexion */
//         #loginModal .modal-body i.icon-user {
//             margin-bottom: 15px;
//         }

//         #loginModal .modal-footer .btn {
//             margin: 0 5px;
//             min-width: 100px;
//         }

//         #loginModal .modal-content {
//             border-radius: 10px;
//             border: none;
//             box-shadow: 0 10px 30px rgba(0,0,0,0.2);
//         }

//         #loginModal .modal-header {
//             background: linear-gradient(135deg, #2B6ED4 0%, #1e5bb8 100%);
//             color: white;
//             border-bottom: none;
//             border-radius: 10px 10px 0 0;
//         }

//         #loginModal .modal-header .btn-close {
//             filter: invert(1);
//         }

//         /* Styles améliorés pour tous les rubans */
//         .ribbon {
//             width: 110px !important;
//             min-width: 110px !important;
//             max-width: 110px !important;
//             text-align: center !important;
//             padding: 3px 10px !important;
//             box-sizing: border-box !important;
//             height: 27px !important;
//             line-height: 20px !important;
//             overflow: hidden !important;
//             white-space: nowrap !important;
//             text-overflow: ellipsis !important;
//         }

//         .ribbon-danger {
//             top: 15px !important;
//             right: 0 !important;
//             left: auto !important;
//         }

//         .ribbon-warning {
//             top: 50px !important;
//             right: 0 !important;
//             left: auto !important;
//         }

//         .ribbon-success {
//             top: 50px !important;
//             right: 0 !important;
//             left: auto !important;
//         }

//         .progress {
//             height: 4px !important;
//         }
//     `;
//     document.head.appendChild(style);
// }
function addButtonStyles() {
    const style = document.createElement('style');
    style.textContent = `
        /* Styles existants... */

        /* Ajout de styles pour les conteneurs principaux */
        body {
            opacity: 1 !important;
            background-color: #ffffff !important;
            transition: none !important;
        }

        .container-fluid,
        .formations-container,
        .formation-item,
        .card,
        .product-box {
            opacity: 1 !important;
            background-color: #ffffff !important;
            transition: none !important;
        }

        .formation-item img,
        .card img,
        .product-box img {
            opacity: 1 !important;
            transition: none !important;
        }
    `;
    document.head.appendChild(style);
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        calculateSeatsInfo,
        isFormationComplete,
        updateAddToCartButton,
        showFormationDetails,
        createFormationCard,
        updateButtonLayout,
        addButtonStyles,
        initButtonLayout,
        showLoginPopup

    };
}
function applyLeftShiftForUnauthenticated() {
    console.log("Vérification du statut d'authentification pour décalage");

    // Vérifier si l'utilisateur est authentifié
    const isAuthenticated = typeof userRoles !== 'undefined' && userRoles.length > 0 && !userRoles.includes('guest');

    console.log("Utilisateur authentifié:", isAuthenticated);
    console.log("Rôles utilisateur:", userRoles);

    if (!isAuthenticated) {
        console.log("Utilisateur non authentifié - Application du décalage à gauche");
        applyLeftShiftStyles();
    } else {
        console.log("Utilisateur authentifié - Aucun décalage appliqué");
    }
}
function applyLeftShiftStyles() {
    let shiftStyle = document.getElementById('unauthenticated-left-shift-styles');
    if (!shiftStyle) {
        shiftStyle = document.createElement('style');
        shiftStyle.id = 'unauthenticated-left-shift-styles';
        document.head.appendChild(shiftStyle);
    }
    shiftStyle.textContent = `
        /* Décalage à gauche pour utilisateurs non authentifiés */
        body:not(.authenticated-user) {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }

        body:not(.authenticated-user) .container-fluid {
            padding-left: 50px !important;
            margin-left: -200px !important;
        }

        body:not(.authenticated-user) .product-wrapper {
            margin-left: -30px !important;
            padding-left: 25px !important;
        }

        body:not(.authenticated-user) .product-grid {
            margin-left: -30px !important;
        }

        body:not(.authenticated-user) .feature-products {
            margin-left: -40px !important;
        }

        body:not(.authenticated-user) .pro-filter-sec {
            margin-left: -30px !important;
        }

        body:not(.authenticated-user) .product-sidebar {
            margin-left: -20px !important;
        }

        body:not(.authenticated-user) .filter-section {
            margin-left: -20px !important;
        }

        body:not(.authenticated-user) .product-search {
            margin-left: -20px !important;
        }

        body:not(.authenticated-user) .product-wrapper-grid {
            margin-left: -25px !important;
        }

        body:not(.authenticated-user) .formations-container {
            margin-left: -25px !important;
        }

        body:not(.authenticated-user) .formation-item {
            margin-left: -10px !important;
        }

        body:not(.authenticated-user) .card {
            margin-left: -10px !important;
        }

        /* Décalage plus prononcé pour les éléments principaux */
        body:not(.authenticated-user) .row {
            margin-left: -25px !important;
        }

        body:not(.authenticated-user) .col-md-12,
        body:not(.authenticated-user) .col-xl-3,
        body:not(.authenticated-user) .col-sm-6,
        body:not(.authenticated-user) .xl-4 {
            padding-left: 10px !important;
            margin-left: -10px !important;
        }

        /* Ajustements pour les modaux (pas de décalage) */
        body:not(.authenticated-user) .modal {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }

        body:not(.authenticated-user) .modal-dialog {
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Ajustements pour les alertes et messages */
        body:not(.authenticated-user) .alert {
            margin-left: -20px !important;
        }

        /* Responsivité - ajustements pour mobile */
        @media (max-width: 768px) {
            body:not(.authenticated-user) .container-fluid {
                margin-left: -20px !important;
                padding-left: 20px !important;
            }

            body:not(.authenticated-user) .row {
                margin-left: -20px !important;
            }

            body:not(.authenticated-user) .formation-item {
                margin-left: -3px !important;
            }
        }

        /* Version alternative plus subtile - décommentez si le décalage ci-dessus est trop prononcé */
        /*
        body:not(.authenticated-user) .container-fluid {
            transform: translateX(-30px);
        }

        body:not(.authenticated-user) .product-wrapper {
            transform: translateX(-25px);
        }

        body:not(.authenticated-user) .formations-container {
            transform: translateX(-20px);
        }
        */
    `;

    console.log("Styles de décalage à gauche appliqués");
}
function setAuthenticationBodyClass() {
    const isAuthenticated = typeof userRoles !== 'undefined' && userRoles.length > 0 && !userRoles.includes('guest');

    if (isAuthenticated) {
        document.body.classList.add('authenticated-user');
        console.log("Classe 'authenticated-user' ajoutée au body");
    } else {
        document.body.classList.remove('authenticated-user');
        console.log("Classe 'authenticated-user' supprimée du body");
    }
}
function initUnauthenticatedLeftShift() {
    console.log("Initialisation du système de décalage pour utilisateurs non authentifiés");
    if (typeof userRoles === 'undefined') {
        console.log("userRoles non défini - traitement comme utilisateur non authentifié");
        document.body.classList.remove('authenticated-user');
        applyLeftShiftStyles();
        return;
    }
    setAuthenticationBodyClass();
    applyLeftShiftForUnauthenticated();
}
function initUnauthenticatedLeftShiftWithRetry() {
    console.log("Initialisation du système de décalage pour utilisateurs non authentifiés");
    let retryCount = 0;
    const maxRetries = 5;
    function tryInit() {
        if (typeof userRoles === 'undefined' && retryCount < maxRetries) {
            console.log(`userRoles non encore défini, tentative ${retryCount + 1}/${maxRetries}`);
            retryCount++;
            setTimeout(tryInit, 200); // Augmenter le délai à 200ms
            return;
        }

        if (typeof userRoles === 'undefined') {
            console.log("userRoles toujours non défini après les essais - traitement par défaut");
            // Traiter comme utilisateur non authentifié
            document.body.classList.remove('authenticated-user');
            applyLeftShiftStyles();
            return;
        }

        setAuthenticationBodyClass();
        applyLeftShiftForUnauthenticated();
    }

    tryInit();
}

function showConfirmedReservationModal(formationId) {
    console.log('Affichage du modal pour réservation confirmée');
    let popup = document.getElementById('confirmed-reservation-popup');
    if (!popup) {
        popup = document.createElement('div');
        popup.id = 'confirmed-reservation-popup';
        popup.innerHTML = `
            <div class="modal fade" id="confirmedReservationModal" tabindex="-1" aria-labelledby="confirmedReservationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title text-white" id="confirmedReservationModalLabel">
                                <i class="icon-warning"></i> Réservation Confirmée
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="icon-calendar" style="font-size: 48px; color: #f39c12;"></i>
                            </div>
                            <h6 class="mb-3">Cette formation a des réservations confirmées</h6>
                            <p class="text-muted">Des utilisateurs ont déjà réservé cette formation avec un statut confirmé. Voulez-vous modifier la formation malgré cela ?</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" onclick="window.location.href='/formation/${formationId}/edit'">Oui, modifier quand même</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(popup);
    }
    const modal = new bootstrap.Modal(document.getElementById('confirmedReservationModal'));
    modal.show();
}
function removeLeftShiftStyles() {
    const shiftStyle = document.getElementById('unauthenticated-left-shift-styles');
    if (shiftStyle) {
        shiftStyle.remove();
        console.log("Styles de décalage supprimés");
    }
}
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM chargé - Initialisation du décalage pour utilisateurs non authentifiés");
    initUnauthenticatedLeftShift();
});
if (typeof userRoles !== 'undefined') {
    const originalUserRoles = userRoles;
    Object.defineProperty(window, 'userRoles', {
        get: function() {
            return originalUserRoles;
        },
        set: function(newValue) {
            console.log("Changement de userRoles détecté:", newValue);
            originalUserRoles = newValue;

            // Réappliquer le décalage avec les nouveaux rôles
            setTimeout(() => {
                setAuthenticationBodyClass();
                applyLeftShiftForUnauthenticated();
            }, 100);
        }
    });
}
window.applyLeftShiftForUnauthenticated = applyLeftShiftForUnauthenticated;
window.applyLeftShiftStyles = applyLeftShiftStyles;
window.setAuthenticationBodyClass = setAuthenticationBodyClass;
window.initUnauthenticatedLeftShift = initUnauthenticatedLeftShift;
window.removeLeftShiftStyles = removeLeftShiftStyles;
window.initButtonLayout = initButtonLayout;
window.isFormationComplete = isFormationComplete;
window.updateAddToCartButton = updateAddToCartButton;
window.showFormationDetails = showFormationDetails;
window.addButtonStyles = addButtonStyles;
window.calculateSeatsInfo = calculateSeatsInfo;
window.createFormationCard = createFormationCard;
window.updateButtonLayout = updateButtonLayout;
window.showLoginPopup = showLoginPopup;
window.initFooterVisibility = initFooterVisibility;
window.showFooterAfterLoading = showFooterAfterLoading;
window.monitorLoadingAndShowFooter = monitorLoadingAndShowFooter;
window.isSpinnerActive = isSpinnerActive;
window.areFormationCardsLoaded = areFormationCardsLoaded;
