
// // Vérifier quelles formations expirées sont encore présentes dans le DOM
// function checkRemainingExpiredFormations() {
//     if (!isInitialized || expiredFormationIds.length === 0) {
//         return;
//     }
    
//     console.log('Vérification des formations expirées restantes...');
//     let remainingExpiredIds = [];
    
//     // Parcourir les IDs des formations expirées connues
//     expiredFormationIds.forEach(id => {
//         // Vérifier si la formation est toujours dans le DOM
//         const formationElements = document.querySelectorAll(`.formation-item[data-formation-id="${id}"]`);
//         if (formationElements.length > 0) {
//             remainingExpiredIds.push(id);
//         } else {
//             console.log(`Formation expirée ${id} n'est plus dans le DOM - probablement supprimée`);
//         }
//     });
    
//     // Mettre à jour la liste des formations expirées
//     if (expiredFormationIds.length !== remainingExpiredIds.length) {
//         console.log(`Formations expirées mises à jour: ${expiredFormationIds.length} -> ${remainingExpiredIds.length}`);
//         expiredFormationIds = remainingExpiredIds;
//         hasExpiredFormationsInCart = remainingExpiredIds.length > 0;
        
//         // Mettre à jour l'UI en fonction du nouvel état
//         if (remainingExpiredIds.length > 0) {
//             showExpiredFormationsWarning();
//         } else {
//             removeExpiredFormationsWarning();
//         }
        
//         updateReserveButtonState();
//     }
// }let expiredFormationIds = [];
// let hasExpiredFormationsInCart = false;
// let isInitialized = false;
// let badgesApplied = false;
// let observerInitialized = false;

// // Vérification des dates des formations - version garantie
// function checkFormationsDates() {
//     // Éviter les vérifications en double
//     if (isInitialized) {
//         console.log('Vérification déjà effectuée');
//         return Promise.resolve(false);
//     }

//     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
//     if (!csrfToken) {
//         console.error('CSRF token non trouvé');
//         return Promise.resolve(false);
//     }

//     console.log('Vérification initiale des dates des formations...');

//     const baseUrl = window.location.origin;
//     const url = `${baseUrl}/panier/details`;

//     return fetch(url, {
//         method: 'GET',
//         headers: {
//             'Accept': 'application/json',
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': csrfToken,
//             'X-Requested-With': 'XMLHttpRequest'
//         },
//         credentials: 'same-origin'
//     })
//     .then(async response => {
//         if (!response.ok) {
//             const errorText = await response.text();
//             console.error('Erreur de réponse:', response.status, errorText);
//             throw new Error(`Erreur de réponse du serveur: ${response.status}`);
//         }
//         return response.json();
//     })
//     .then(data => {
//         console.log('Réponse reçue pour les détails du panier:', data);

//         if (!data.success || !data.trainings || data.trainings.length === 0) {
//             console.log('Aucune formation dans le panier');
//             isInitialized = true;
//             return false;
//         }

//         let hasExpiredFormations = false;
//         const today = new Date();
//         today.setHours(0, 0, 0, 0);
//         expiredFormationIds = [];

//         // Vérifier chaque formation
//         data.trainings.forEach(formation => {
//             const startDate = new Date(formation.start_date);
//             if (startDate < today) {
//                 console.log(`Formation ${formation.id} a une date dépassée: ${formation.start_date}`);
//                 hasExpiredFormations = true;
//                 expiredFormationIds.push(formation.id);
//             }
//         });

//         hasExpiredFormationsInCart = hasExpiredFormations;
        
//         // Marquer comme initialisé et appliquer les badges
//         isInitialized = true;
//         console.log('Formations expirées identifiées:', expiredFormationIds);
        
//         // Appliquer immédiatement, puis réessayer après un court délai
//         applyExpiredStatus();
        
//         // Planifier plusieurs tentatives d'application des badges
//         const checkIntervals = [100, 300, 500, 1000, 2000, 3000];
//         checkIntervals.forEach(interval => {
//             setTimeout(applyExpiredStatus, interval);
//         });
        
//         // Initialiser l'observateur pour maintenir les badges
//         initMutationObserver();
        
//         return hasExpiredFormations;
//     })
//     .catch(error => {
//         console.error('Erreur lors de la vérification des dates des formations:', error);
//         return false;
//     });
// }

// // Initialiser un observateur de mutations pour maintenir les badges
// function initMutationObserver() {
//     if (observerInitialized) return;
    
//     console.log('Initialisation de l\'observateur de mutations');
    
//     // Créer et configurer l'observateur
//     const observer = new MutationObserver((mutations) => {
//         let shouldReapply = false;
//         let possibleFormationRemoval = false;
        
//         mutations.forEach(mutation => {
//             // Vérifier si des éléments pertinents ont été modifiés
//             if (mutation.type === 'childList') {
//                 // Détecter les suppressions possibles de formation
//                 if (mutation.removedNodes && mutation.removedNodes.length > 0) {
//                     for (let i = 0; i < mutation.removedNodes.length; i++) {
//                         const node = mutation.removedNodes[i];
//                         if (node.nodeType === Node.ELEMENT_NODE && 
//                             (node.classList?.contains('formation-item') || 
//                              node.querySelector?.('.formation-item'))) {
//                             console.log('Détection possible de suppression de formation');
//                             possibleFormationRemoval = true;
//                             break;
//                         }
//                     }
//                 }
//                 shouldReapply = true;
//             } else if (mutation.type === 'attributes' && 
//                 (mutation.target.classList?.contains('formation-item') || 
//                  mutation.target.closest?.('.formation-item'))) {
//                 shouldReapply = true;
//             }
//         });
        
//         // Si une formation a potentiellement été supprimée, vérifier les formations restantes
//         if (possibleFormationRemoval) {
//             console.log('Vérification des formations restantes après possible suppression');
//             checkRemainingExpiredFormations();
//         }
        
//         if (shouldReapply && isInitialized && expiredFormationIds.length > 0) {
//             console.log('Mutations détectées, réapplication des badges');
//             applyExpiredStatus();
//         }
//     });
    
//     // Options de configuration de l'observateur
//     const config = { 
//         childList: true,
//         subtree: true,
//         attributes: true,
//         attributeFilter: ['class', 'style']
//     };
    
//     // Observer le conteneur principal ou tout le corps du document
//     const container = document.querySelector('.panier-content') || 
//                       document.querySelector('.container') || 
//                       document.body;
    
//     observer.observe(container, config);
//     observerInitialized = true;
// }

// // Appliquer les badges et avertissements pour les formations expirées
// function applyExpiredStatus() {
//     if (!isInitialized || expiredFormationIds.length === 0) {
//         // S'il n'y a plus de formations expirées, supprimer l'avertissement
//         removeExpiredFormationsWarning();
//         updateReserveButtonState();
//         return;
//     }
    
//     console.log('Application des badges pour formations expirées:', expiredFormationIds);
//     let badgesAppliedThisRun = 0;
    
//     // Ajouter les badges uniquement pour les formations expirées
//     expiredFormationIds.forEach(id => {
//         // Sélectionner tous les éléments possibles pour cette formation
//         const formationElements = document.querySelectorAll(`.formation-item[data-formation-id="${id}"]`);
        
//         if (formationElements.length === 0) {
//             console.log(`Aucun élément trouvé pour la formation ${id}`);
//             return;
//         }
        
//         formationElements.forEach(formationElement => {
//             console.log(`Traitement de la formation expirée ID: ${id}`);
            
//             // Vérifier si le badge existe déjà
//             const existingBadge = formationElement.querySelector('.formation-expired-badge');
//             if (existingBadge) {
//                 console.log(`Badge déjà présent pour formation ${id}`);
//                 badgesAppliedThisRun++;
//                 return; // Badge déjà présent, ne rien faire
//             }
            
//             // Appliquer la classe pour le style de formation expirée
//             formationElement.classList.add('formation-expired');
            
//             // Trouver le bon emplacement pour le badge
//             const formationTitle = formationElement.querySelector('.formation-title') ||
//                                   formationElement.querySelector('h4') ||
//                                   formationElement.querySelector('h3');
            
//             // Créer le badge avec une classe unique pour l'identifier
//             const statusBadge = document.createElement('span');
//             statusBadge.className = 'formation-status-badge formation-expired-badge ml-2 badge badge-secondary';
//             statusBadge.setAttribute('data-formation-id', id);
//             statusBadge.textContent = 'Date dépassée';
//             statusBadge.style.fontWeight = 'bold';
//             statusBadge.style.fontSize = '0.8rem';
//             statusBadge.style.padding = '0.3rem 0.6rem';
//             statusBadge.style.display = 'inline-flex';
//             statusBadge.style.color = '#495057';
//             statusBadge.style.backgroundColor = '#e9ecef';
            
//             // Ajouter le badge à l'élément approprié
//             if (formationTitle) {
//                 console.log(`Ajout du badge à l'élément titre pour formation ${id}`);
//                 formationTitle.appendChild(statusBadge);
//             } else {
//                 console.log(`Ajout du badge au début de la formation ${id}`);
//                 formationElement.insertAdjacentElement('afterbegin', statusBadge);
//             }
            
//             badgesAppliedThisRun++;
//         });
//     });
    
//     // Mettre à jour le statut global des badges
//     badgesApplied = badgesApplied || (badgesAppliedThisRun > 0);
    
//     // Afficher ou supprimer l'avertissement global
//     if (hasExpiredFormationsInCart && expiredFormationIds.length > 0) {
//         showExpiredFormationsWarning();
//     } else {
//         removeExpiredFormationsWarning();
//     }
    
//     updateReserveButtonState();
// }

// // Mettre à jour l'état du bouton de réservation
// function updateReserveButtonState() {
//     const localHasExpiredFormations = expiredFormationIds.length > 0;
//     const completeFormationElements = document.querySelectorAll('.formation-full');
//     const localHasCompleteFormations = completeFormationElements.length > 0;

//     hasExpiredFormationsInCart = localHasExpiredFormations;
//     window.hasCompleteFormationsInCart = localHasCompleteFormations;

//     const reserverButtons = document.querySelectorAll('.reserver-button');
//     if (reserverButtons.length > 0 && !window.hasExistingReservation) {
//         reserverButtons.forEach(reserverButton => {
//             if (localHasExpiredFormations) {
//                 reserverButton.disabled = true;
//                 reserverButton.classList.add('disabled');
//                 reserverButton.title = 'Votre panier contient des formations dont la date est dépassée';
//             } else if (localHasCompleteFormations) {
//                 reserverButton.disabled = true;
//                 reserverButton.classList.add('disabled');
//                 reserverButton.title = 'Une ou plusieurs formations sont complètes';
//             } else {
//                 reserverButton.disabled = false;
//                 reserverButton.classList.remove('disabled');
//                 reserverButton.removeAttribute('title');
//             }
//         });
//     }
// }

// // Afficher l'avertissement pour les formations expirées
// function showExpiredFormationsWarning() {
//     let existingWarning = document.querySelector('.expired-formations-warning');
//     if (existingWarning) return;

//     const warningContainer = document.createElement('div');
//     warningContainer.className = 'expired-formations-warning';
//     warningContainer.innerHTML = `
//         <i class="fas fa-calendar-times mr-2"></i>
//         <strong>Attention:</strong> Votre panier contient des formations dont la date est dépassée. Veuillez les supprimer pour poursuivre votre réservation.
//     `;
//     // Appliquer des styles pour garantir la visibilité
//     warningContainer.style.width = '100%';
//     warningContainer.style.marginBottom = '1rem';
//     warningContainer.style.display = 'flex';
//     warningContainer.style.alignItems = 'center';
//     warningContainer.style.textAlign = 'center';
//     warningContainer.style.justifyContent = 'center';
//     warningContainer.style.padding = '1rem';
//     warningContainer.style.backgroundColor = '#e9ecef';
//     warningContainer.style.color = '#495057';
//     warningContainer.style.border = '1px solid rgb(84, 88, 92)';
//     warningContainer.style.borderRadius = '4px';
//     warningContainer.style.zIndex = '100';
//     const completeWarning = document.querySelector('.complete-formations-warning');
//     const greenHeader = document.querySelector('.panier-header');
//     const panierContent = document.querySelector('.panier-content');
//     const container = document.querySelector('.container');
//     if (completeWarning) {
//         completeWarning.parentNode.insertBefore(warningContainer, completeWarning.nextSibling);
//     } else if (greenHeader) {
//         greenHeader.parentNode.insertBefore(warningContainer, greenHeader);
//     } else if (panierContent) {
//         panierContent.insertBefore(warningContainer, panierContent.firstChild);
//     } else if (container) {
//         container.insertBefore(warningContainer, container.firstChild);
//     }

//     const rect = warningContainer.getBoundingClientRect();
//     if (rect.top < 0 || rect.bottom > window.innerHeight) {
//         warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
//     }
// }
// // Supprimer l'avertissement
// function removeExpiredFormationsWarning() {
//     const warning = document.querySelector('.expired-formations-warning');
//     if (warning) warning.remove();
// }
// // Appliquer les styles CSS
// function applyStyles() {
//     // Supprimer les styles existants s'ils existent déjà
//     const existingStyles = document.querySelector('#expired-formations-styles');
//     if (existingStyles) existingStyles.remove();
    
//     const expiredStyleElement = document.createElement('style');
//     expiredStyleElement.id = 'expired-formations-styles';
//     expiredStyleElement.textContent = `
//         .expired-formations-warning {
//             width: 100%;
//             margin-bottom: 1rem;
//             display: flex;
//             align-items: center;
//             text-align: center;
//             justify-content: center;
//             padding: 1rem;
//             background-color: #e9ecef;
//             color: #495057;
//             border: 1px solid rgb(84, 88, 92);
//             border-radius: 4px;
//             z-index: 100;
//         }
//         .badge-secondary {
//             background-color: #6c757d !important;
//             color: white !important;
//         }
//         .formation-expired {
//             background-color: #f8f9fa !important;
//             opacity: 0.8 !important;
//             border-left: 4px solid #495057 !important;
//         }
//         .formation-expired-badge {
//             font-weight: bold !important;
//             font-size: 0.8rem !important;
//             padding: 0.3rem 0.6rem !important;
//             display: inline-flex !important;
//             color: #495057 !important;
//             background-color: #e9ecef !important;
//             margin-left: 0.5rem !important;
//         }
//     `;
//     document.head.appendChild(expiredStyleElement);
// }
// // Vérifier périodiquement si les badges doivent être réappliqués
// function setupPeriodicChecks() {
//     // Vérifier toutes les 2 secondes si les badges sont toujours présents
//     setInterval(() => {
//         if (isInitialized && expiredFormationIds.length > 0) {
//             let allBadgesPresent = true;
            
//             // Vérifier si tous les badges sont présents
//             expiredFormationIds.forEach(id => {
//                 const formationElements = document.querySelectorAll(`.formation-item[data-formation-id="${id}"]`);
//                 formationElements.forEach(formationElement => {
//                     const hasBadge = formationElement.querySelector('.formation-expired-badge');
//                     if (!hasBadge) {
//                         allBadgesPresent = false;
//                     }
//                 });
//             });
            
//             // Si des badges manquent, les réappliquer
//             if (!allBadgesPresent) {
//                 console.log('Certains badges manquent, réapplication...');
//                 applyExpiredStatus();
//             }
//         }
//     }, 2000);
// }
// // Initialiser
// (function init() {
//     // Appliquer les styles immédiatement
//     applyStyles();
//     // Lancer la vérification des dates
//     checkFormationsDates().then(() => {
//         // Mettre en place les vérifications périodiques
//         setupPeriodicChecks();
//     });
//     // Ajouter également un écouteur pour l'événement DOMContentLoaded
//     document.addEventListener('DOMContentLoaded', function() {
//         console.log('DOM chargé, application des badges...');
//         // Réappliquer les styles pour s'assurer qu'ils ne sont pas écrasés
//         applyStyles();
//         // Réappliquer les badges
//         setTimeout(applyExpiredStatus, 100);
//         setTimeout(applyExpiredStatus, 500);
//         // Initialiser l'observateur s'il ne l'a pas déjà été
//         if (!observerInitialized && isInitialized) {
//             initMutationObserver();
//         }
//     });
// })();