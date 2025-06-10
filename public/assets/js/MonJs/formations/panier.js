
const CART_ICON_SELECTORS = [
    '.shopping-cart-icon',
    'svg[data-icon="shopping-cart"]',
    '.cart-icon',
    'a[href*="panier"] svg',
    '.cart-container svg',
    '.cart-link',
    '.panier-icon'
].join(', ');
const BADGE_SELECTORS = '.cart-badge, .custom-violet-badge, #fixed-cart-badge';
const pendingRequests = {};
const processedCompleteFormationIds = new Set();
const processedExpiredFormationIds = new Set();
function handleResponse(response) {
    if (!response.ok) {
        throw new Error(`Erreur réseau: ${response.status}`);
    }
    return response.json();
}
// Mettre à jour les badges du panier
function updateCartBadges(count) {
    count = parseInt(count) || 0;
    const badges = document.querySelectorAll(BADGE_SELECTORS);
    badges.forEach(badge => {
        badge.textContent = count.toString();
        badge.style.display = count > 0 ? 'flex' : 'none';
        badge.style.visibility = count > 0 ? 'visible' : 'hidden';
        badge.style.opacity = count > 0 ? '1' : '0';
    });
    if (count > 0) {
        const cartIcons = document.querySelectorAll(CART_ICON_SELECTORS);
        cartIcons.forEach(icon => {
            const container = icon.closest('a, div, button, .cart-container');
            if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
                const badge = document.createElement('span');
                badge.className = 'cart-badge custom-violet-badge';
                badge.textContent = count.toString();
                badge.style.position = 'absolute';
                badge.style.top = '-8px';
                badge.style.right = '-8px';
                badge.style.display = 'flex';
                badge.style.visibility = 'visible';
                badge.style.opacity = '1';
                if (getComputedStyle(container).position === 'static') {
                    container.style.position = 'relative';
                }
                container.appendChild(badge);
            }
        });
    }
}
// Synchronisation du compteur du panier
function syncCartCount() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) return;

    fetch('/panier/data', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(handleResponse)
        .then(data => {
            const count = parseInt(data.count) || 0;
            const items = data.items || [];

            // MODIFICATION PRINCIPALE : Forcer la mise à jour depuis le serveur
            localStorage.setItem('cartCount', count.toString());
            localStorage.setItem('cartFormations', JSON.stringify(items.map(id => id.toString())));
            updateCartBadges(count);

            // Si on est sur la page panier et qu'elle est vide côté serveur
            if (count === 0 && window.location.pathname.includes('/panier')) {
                showEmptyCartMessage();
            }

            // AJOUT : Forcer la vérification des formations dans le panier
            if (count > 0) {
                checkFormationsInCart();
            }
        })
        .catch(error => console.error('Erreur lors de la synchronisation du compteur:', error));
}
function checkFormationsInCart() {
    const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    const lastAddedFormation = localStorage.getItem('lastAddedFormation');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        console.error('CSRF token non trouvé');
        return;
    }
    // Gérer la formation récemment ajoutée
    if (lastAddedFormation) {
        console.log('Formation précédemment ajoutée:', lastAddedFormation);
        updateAddToCartButton(lastAddedFormation, true, false);
        if (!cartFormations.includes(lastAddedFormation)) {
            cartFormations.push(lastAddedFormation);
            localStorage.setItem('cartFormations', JSON.stringify(cartFormations));
        }
        localStorage.removeItem('lastAddedFormation');
    }
    // Mettre à jour les boutons pour les formations dans le panier
    cartFormations.forEach(formationId => {
        updateAddToCartButton(formationId, true, false);
    });
    // Récupérer les données du panier depuis le serveur
    fetch('/panier/data', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(handleResponse)
        .then(data => {
            if (data.items && Array.isArray(data.items)) {
                const serverFormationIds = data.items.map(item => item.toString());
                const completeFormations = (data.completeFormations || []).map(id => id.toString());
                const expiredFormations = (data.expiredFormations || []).map(id => id.toString());
                // Mettre à jour le localStorage avec les formations du serveur
                localStorage.setItem('cartFormations', JSON.stringify(serverFormationIds));

                // CORRECTION PRINCIPALE: Récupérer les informations de disponibilité AVANT de mettre à jour l'interface
                // Utiliser la méthode du contrôleur qui calcule correctement les places
                if (serverFormationIds.length > 0) {
                    // Appeler l'API pour obtenir la disponibilité réelle des formations
                    fetch('/formations/availability', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            formation_ids: serverFormationIds.map(id => parseInt(id))
                        })
                    })
                    .then(handleResponse)
                    .then(availabilityData => {
                        // Mettre à jour l'interface avec les vraies données de disponibilité
                        updateInterfaceWithAvailability(serverFormationIds, availabilityData, completeFormations, expiredFormations, data.count);
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération de la disponibilité:', error);
                        // Fallback: utiliser les données du panier si l'API de disponibilité échoue
                        updateInterfaceWithCartData(serverFormationIds, completeFormations, expiredFormations, data.count);
                    });
                } else {
                    // Pas de formations dans le panier, nettoyer l'interface
                    updateInterfaceWithCartData([], [], [], 0);
                }
            }
        })
        .catch(error => console.error('Erreur lors de la vérification du panier:', error));
}
// NOUVELLE FONCTION: Mettre à jour l'interface avec les vraies données de disponibilité
function updateInterfaceWithAvailability(serverFormationIds, availabilityData, completeFormations, expiredFormations, cartCount) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    // Parcourir les éléments du DOM pour mettre à jour les boutons et badges
    document.querySelectorAll('.formation-item, .product-box').forEach(item => {
        const formationId = item.getAttribute('data-category-id') || item.getAttribute('data-formation-id');
        if (formationId) {
            const inCart = serverFormationIds.includes(formationId.toString());
            const isExpired = expiredFormations.includes(formationId.toString());

            // CORRECTION: Utiliser les vraies données de disponibilité, pas les données du panier
            const remainingSeats = availabilityData.remaining_seats ? availabilityData.remaining_seats[formationId] : null;
            const isReallyComplete = remainingSeats !== null && remainingSeats === 0;

            // Mettre à jour le bouton "Ajouter au panier" avec le vrai statut
            updateAddToCartButton(formationId, inCart, isReallyComplete || isExpired);

            // Mettre à jour le badge des places avec les vraies données
            const seatsInfo = item.querySelector('.badge-light-success, .badge-light-danger');
            if (seatsInfo && remainingSeats !== null) {
                // Calculer le nombre de réservations confirmées
                const totalSeats = availabilityData.total_seats ? availabilityData.total_seats[formationId] : 0;
                const confirmedReservations = totalSeats - remainingSeats;

                seatsInfo.textContent = `${confirmedReservations}/${totalSeats}`;
                // seatsInfo.classList.remove('badge-light-success', 'badge-light-danger');
                // seatsInfo.classList.add(isReallyComplete ? 'badge-light-danger' : 'badge-light-success');
            }
        }
    });

    // Mettre à jour le compteur du panier
    const serverCount = parseInt(cartCount) || 0;
    if (serverFormationIds.length !== parseInt(localStorage.getItem('cartCount') || '0')) {
        localStorage.setItem('cartCount', serverCount.toString());
        updateCartBadges(serverCount);
    }
    // Mettre à jour les ensembles avec les vraies formations complètes
    processedCompleteFormationIds.clear();
    if (availabilityData.complete_formations) {
        availabilityData.complete_formations.forEach(id => processedCompleteFormationIds.add(id.toString()));
    }
    processedExpiredFormationIds.clear();
    expiredFormations.forEach(id => processedExpiredFormationIds.add(id));

    // Mettre à jour les alertes
    updateWarnings();
}
// FONCTION DE FALLBACK: Si l'API de disponibilité échoue
function updateInterfaceWithCartData(serverFormationIds, completeFormations, expiredFormations, cartCount) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    // Parcourir les éléments du DOM pour mettre à jour les boutons et badges
    const promises = [];
    document.querySelectorAll('.formation-item, .product-box').forEach(item => {
        const formationId = item.getAttribute('data-category-id') || item.getAttribute('data-formation-id');
        if (formationId) {
            const inCart = serverFormationIds.includes(formationId.toString());
            const isComplete = completeFormations.includes(formationId.toString());
            const isExpired = expiredFormations.includes(formationId.toString());
            // Mettre à jour le bouton "Ajouter au panier"
            updateAddToCartButton(formationId, inCart, isComplete || isExpired);
            // Mettre à jour le badge des places avec une requête API individuelle (comme avant)
            const seatsInfo = item.querySelector('.badge-light-success, .badge-light-danger');
            if (seatsInfo) {
                promises.push(
                    fetch(`/get-remaining-seats/${formationId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(handleResponse)
                        .then(seatsData => {
                            if (seatsData.success) {
                                seatsInfo.textContent = `${seatsData.confirmed_reservations}/${seatsData.total_seats}`;
                                seatsInfo.classList.remove('badge-light-success', 'badge-light-danger');
                                seatsInfo.classList.add(seatsData.is_complete ? 'badge-light-danger' : 'badge-light-success');
                            }
                        })
                        .catch(error => {
                            console.error(`Erreur lors de la récupération des places pour la formation ${formationId}:`, error);
                        })
                );
            }
        }
    });
    // Attendre que toutes les requêtes de mise à jour des badges soient terminées
    Promise.all(promises).then(() => {
        // Mettre à jour le compteur du panier
        const serverCount = parseInt(cartCount) || 0;
        if (serverFormationIds.length !== parseInt(localStorage.getItem('cartCount') || '0')) {
            localStorage.setItem('cartCount', serverCount.toString());
            updateCartBadges(serverCount);
        }

        // Mettre à jour les ensembles de formations complètes et expirées
        processedCompleteFormationIds.clear();
        completeFormations.forEach(id => processedCompleteFormationIds.add(id));
        processedExpiredFormationIds.clear();
        expiredFormations.forEach(id => processedExpiredFormationIds.add(id));

        // Mettre à jour les alertes
        updateWarnings();
    });
}
function updateCartUI(data) {
    updateCartBadges(data.cartCount);

    const cartCountElements = document.querySelectorAll('.cart-count, .panier-count');
    cartCountElements.forEach(element => {
        if (data.cartCount > 0) {
            element.textContent = `${data.cartCount} formation(s)`;
            element.style.display = '';
            element.style.opacity = '1';
            element.classList.remove('empty');
        } else {
            element.style.display = 'none';
            element.classList.add('empty');
        }
    });
    const totalPriceElement = document.querySelector('.cart-total-price, .total-price');
    if (totalPriceElement) {
        totalPriceElement.textContent = data.totalPrice ? `${data.totalPrice} DT` : '0 DT';
    }

    const originalPriceElement = document.querySelector('.original-price');
    const discountElement = document.querySelector('.discount-percentage');

    if (data.hasDiscount && data.discountedItemsOriginalPrice) {
        if (originalPriceElement) {
            originalPriceElement.textContent = `${data.discountedItemsOriginalPrice} DT`;
        } else if (totalPriceElement) {
            const newOriginalPrice = document.createElement('div');
            newOriginalPrice.className = 'original-price';
            newOriginalPrice.textContent = `${data.discountedItemsOriginalPrice} DT`;
            totalPriceElement.insertAdjacentElement('afterend', newOriginalPrice);
        }

        if (discountElement) {
            discountElement.textContent = `${data.discountPercentage}%`;
        } else if (originalPriceElement) {
            const newDiscount = document.createElement('div');
            newDiscount.className = 'discount-percentage';
            newDiscount.textContent = `${data.discountPercentage}%`;
            originalPriceElement.insertAdjacentElement('afterend', newDiscount);
        }
    } else {
        if (originalPriceElement) originalPriceElement.remove();
        if (discountElement) discountElement.remove();
    }

    if (data.cartCount === 0) {
        showEmptyCartMessage();
    }
}
// Alternative: Si vous voulez maintenir la méthode innerHTML mais éviter les problèmes d'animation
function updateWarningsAlternative() {
    const alertsContainer = document.querySelector('#alertsContainer');
    if (!alertsContainer) return;
    // Conserver une référence de l'état actuel
    const currentHTML = alertsContainer.innerHTML;
    let newHTML = '';
    // Construire le nouveau HTML
    if (processedCompleteFormationIds.size > 0) {
        newHTML += `
            <div class="complete-formations-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div><strong>Attention:</strong> Vous devez supprimer les formations complètes pour poursuivre votre réservation.</div>
            </div>
        `;
    }
    if (processedExpiredFormationIds.size > 0) {
        newHTML += `
            <div class="expired-formations-warning">
                <i class="fas fa-calendar-times"></i>
                <div><strong>Attention:</strong> Votre panier contient des formations dont la date est dépassée. Veuillez les supprimer pour poursuivre votre réservation.</div>
            </div>
        `;
    }
    // Mettre à jour seulement si le contenu a changé
    if (currentHTML.trim() !== newHTML.trim()) {
        alertsContainer.innerHTML = newHTML;
    }
}
function initializeCart() {
    initializeEmptyCartDisplay();
    syncCartCount();

    const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
    updateCartBadges(cartCount);
    const completeFormations = window.completeFormations || [];
    const expiredFormations = window.expiredFormations || [];
    if (completeFormations.length > 0) {
        console.log('Formations complètes initiales:', completeFormations);
        processedCompleteFormationIds.clear();
        completeFormations.forEach(formationId => {
            processedCompleteFormationIds.add(formationId.toString());
            const formationElement = document.querySelector(
                `.formation-item[data-formation-id="${formationId}"]`
            );
            if (formationElement) {
                formationElement.classList.add('formation-full');
            }
        });
    }

    if (expiredFormations.length > 0) {
        console.log('Formations avec dates dépassées initiales:', expiredFormations);
        processedExpiredFormationIds.clear();
        expiredFormations.forEach(formationId => {
            processedExpiredFormationIds.add(formationId.toString());
            const formationElement = document.querySelector(
                `.formation-item[data-formation-id="${formationId}"]`
            );
            if (formationElement) {
                formationElement.classList.add('formation-expired');
            }
        });
    }
    // Afficher les alertes initiales
    updateWarnings();
    if (completeFormations.length === 0 && expiredFormations.length === 0) {
        const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');
        if (reserverButton) {
            reserverButton.disabled = false;
            reserverButton.classList.remove('disabled');
            reserverButton.removeAttribute('title');
        }
        window.hasCompleteFormationsInCart = false;
        window.hasExpiredFormationsInCart = false;
    } else {
        const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');
        if (reserverButton) {
            reserverButton.disabled = true;
            reserverButton.classList.add('disabled');
            reserverButton.title = 'Une ou plusieurs formations sont complètes ou ont une date dépassée';
        }
        window.hasCompleteFormationsInCart = completeFormations.length > 0;
        window.hasExpiredFormationsInCart = expiredFormations.length > 0;
    }
    checkFormationsInCart();
    checkAndShowEmptyCart();
    const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
    window.hasExistingReservation = hasExistingReservation;
    if (hasExistingReservation) {
        const reservationId = localStorage.getItem('reservationId');
        if (reservationId && typeof transformReserverButton === 'function') {
            transformReserverButton(parseInt(reservationId));
        }
    }
    document.addEventListener('click', event => {
        const addToCartBtn = event.target.closest('.addcart-btn .btn[href="/panier"]');
        const removeLink = event.target.closest('.remove-link');

        if (removeLink && !removeLink.classList.contains('processing')) {
            event.preventDefault();
            const formationId = removeLink.getAttribute('data-formation-id');
            if (formationId && !pendingRequests[formationId]) {
                removeLink.classList.add('processing');
                pendingRequests[formationId] = true;
                removeFromCart(formationId, () => {
                    removeLink.classList.remove('processing');
                    delete pendingRequests[formationId];
                    checkAndShowEmptyCart();
                });
            }
        }

        if (addToCartBtn && !addToCartBtn.classList.contains('processing') && !addToCartBtn.disabled) {
            event.preventDefault();
            if (addToCartBtn.getAttribute('data-in-cart') === 'true') {
                window.location.href = '/panier';
                return;
            }
            addToCartBtn.classList.add('processing');
            let formationId = addToCartBtn.closest('.modal-content')?.closest('.modal').id.split('-').pop() ||
                addToCartBtn.closest('.formation-item, .product-box')?.getAttribute('data-formation-id') ||
                addToCartBtn.closest('.formation-item, .product-box')?.getAttribute('data-category-id');
            if (formationId && !pendingRequests[formationId]) {
                pendingRequests[formationId] = true;
                addToCart(formationId, false, () => {
                    addToCartBtn.classList.remove('processing');
                    delete pendingRequests[formationId];
                });
            } else {
                addToCartBtn.classList.remove('processing');
            }
        }
    });

    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(mutations => {
            const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
            if (cartCount <= 0) return;
            mutations.forEach(mutation => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1) {
                            const icons = node.querySelectorAll(CART_ICON_SELECTORS);
                            icons.forEach(icon => addBadgeToIcon(icon, cartCount));
                            if (node.matches && node.matches(CART_ICON_SELECTORS)) {
                                addBadgeToIcon(node, cartCount);
                            }
                        }
                    });
                }
            });
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }

    setInterval(syncCartCount, 5000);
    setInterval(checkCartItemsStatus, 120000);

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            syncCartCount();
            checkCartItemsStatus();
            checkAndShowEmptyCart();
        }
    });
}

// 1. MODIFICATION DE LA FONCTION updateWarnings()
function updateWarnings() {
    const alertsContainer = document.querySelector('#alertsContainer');
    if (!alertsContainer) return;
    // Vérifier s'il y a des changements avant de modifier le DOM
    const hasCompleteFormations = processedCompleteFormationIds.size > 0;
    const hasExpiredFormations = processedExpiredFormationIds.size > 0;

    const existingCompleteWarning = alertsContainer.querySelector('.complete-formations-warning');
    const existingExpiredWarning = alertsContainer.querySelector('.expired-formations-warning');

    // Gérer l'alerte pour les formations complètes
    if (hasCompleteFormations && !existingCompleteWarning) {
        const completeWarning = document.createElement('div');
        completeWarning.className = 'complete-formations-warning';
        completeWarning.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <div><strong>Attention:</strong> Vous devez supprimer les formations complètes pour poursuivre votre réservation.</div>
        `;
        alertsContainer.appendChild(completeWarning);
    } else if (!hasCompleteFormations && existingCompleteWarning) {
        existingCompleteWarning.remove();
    }
    // Gérer l'alerte pour les formations expirées - CORRECTION ICI
    if (hasExpiredFormations && !existingExpiredWarning) {
        const expiredWarning = document.createElement('div');
        expiredWarning.className = 'expired-formations-warning';
        expiredWarning.innerHTML = `
            <i class="fas fa-calendar-times"></i>
            <div><strong>Attention:</strong> Votre panier contient des formations dont la date est dépassée. Veuillez les supprimer pour poursuivre votre réservation.</div>
        `;
        alertsContainer.appendChild(expiredWarning);
    } else if (!hasExpiredFormations && existingExpiredWarning) {
        existingExpiredWarning.remove();
    }
}
// 2. MODIFICATION DE LA FONCTION checkCartItemsStatus()
function checkCartItemsStatus() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token non trouvé');
        return;
    }

    fetch('/panier/data', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(handleResponse)
        .then(data => {
            if (data.items && Array.isArray(data.items)) {
                console.log('Données du panier reçues:', data);

                // CORRECTION: Maintenir les formations expirées même après la réponse du serveur
                const previousExpiredFormations = new Set(processedExpiredFormationIds);

                // Réinitialiser les sets
                processedCompleteFormationIds.clear();
                processedExpiredFormationIds.clear();

                // Traiter les formations complètes
                if (data.completeFormations && Array.isArray(data.completeFormations)) {
                    data.completeFormations.forEach(formationId => {
                        processedCompleteFormationIds.add(formationId.toString());
                        const formationElement = document.querySelector(
                            `.formation-item[data-formation-id="${formationId}"]`
                        );
                        if (formationElement) {
                            formationElement.classList.add('formation-full');
                        }
                    });
                }

                // Traiter les formations expirées - CORRECTION ICI
                if (data.expiredFormations && Array.isArray(data.expiredFormations)) {
                    data.expiredFormations.forEach(formationId => {
                        processedExpiredFormationIds.add(formationId.toString());
                        const formationElement = document.querySelector(
                            `.formation-item[data-formation-id="${formationId}"]`
                        );
                        if (formationElement) {
                            formationElement.classList.add('formation-expired');
                        }
                    });
                } else {
                    // CORRECTION: Si le serveur ne renvoie pas d'expiredFormations,
                    // mais qu'on en avait avant, les maintenir s'ils sont toujours dans le panier
                    const currentCartItems = data.items.map(item => item.toString());
                    previousExpiredFormations.forEach(formationId => {
                        if (currentCartItems.includes(formationId)) {
                            processedExpiredFormationIds.add(formationId);
                            const formationElement = document.querySelector(
                                `.formation-item[data-formation-id="${formationId}"]`
                            );
                            if (formationElement) {
                                formationElement.classList.add('formation-expired');
                            }
                        }
                    });
                }

                // Supprimer les classes des formations qui ne sont plus problématiques
                document.querySelectorAll('.formation-item').forEach(element => {
                    const formationId = element.getAttribute('data-formation-id');
                    if (formationId) {
                        // Pour les formations complètes
                        if (!data.completeFormations.includes(parseInt(formationId))) {
                            element.classList.remove('formation-full');
                        }
                        // Pour les formations expirées - CORRECTION
                        if (!processedExpiredFormationIds.has(formationId)) {
                            element.classList.remove('formation-expired');
                        }
                    }
                });

                // Mettre à jour l'état du bouton réserver
                const hasCompleteFormations = data.completeFormations.length > 0;
                const hasExpiredFormations = processedExpiredFormationIds.size > 0; // CORRECTION ICI

                const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');
                if (hasCompleteFormations || hasExpiredFormations) {
                    if (reserverButton && (!window.hasExistingReservation || localStorage.getItem('reservationStatus') !== '0')) {
                        reserverButton.disabled = true;
                        reserverButton.classList.add('disabled');
                        reserverButton.title = 'Une ou plusieurs formations sont complètes ou ont une date dépassée';
                    }
                    window.hasCompleteFormationsInCart = hasCompleteFormations;
                    window.hasExpiredFormationsInCart = hasExpiredFormations;
                } else {
                    if (reserverButton) {
                        reserverButton.disabled = false;
                        reserverButton.classList.remove('disabled');
                        reserverButton.removeAttribute('title');
                    }
                    window.hasCompleteFormationsInCart = false;
                    window.hasExpiredFormationsInCart = false;
                }

                // Mettre à jour les alertes - TOUJOURS APPELER CETTE FONCTION
                updateWarnings();

                // Mettre à jour le localStorage
                const serverFormationIds = data.items.map(item => item.toString());
                localStorage.setItem('cartFormations', JSON.stringify(serverFormationIds));
                const serverCount = parseInt(data.count) || 0;
                localStorage.setItem('cartCount', serverCount.toString());
                updateCartBadges(serverCount);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des formations du panier:', error);
        });
}


// MODIFICATION DE LA FONCTION removeFromCart()
function removeFromCart(formationId, callback = null) {
    const formationItem = document.querySelector(`.formation-item[data-formation-id="${formationId}"]`);
    if (formationItem) {
        formationItem.classList.add('removing');
        formationItem.style.opacity = '0.5';
    }
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token non trouvé');
        if (formationItem) {
            formationItem.classList.remove('removing');
            formationItem.style.opacity = '1';
        }
        if (callback) callback();
        return;
    }

    const wasFormationComplete = processedCompleteFormationIds.has(formationId.toString());
    const wasFormationExpired = processedExpiredFormationIds.has(formationId.toString());

    fetch('/panier/supprimer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ formation_id: formationId })
    })
        .then(handleResponse)
        .then(data => {
            if (data.success) {
                // Mettre à jour le localStorage
                const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
                const updatedFormations = cartFormations.filter(id => id !== formationId.toString());
                localStorage.setItem('cartFormations', JSON.stringify(updatedFormations));
                localStorage.setItem('cartCount', data.cartCount.toString());

                // Supprimer l'élément du DOM
                if (formationItem) {
                    formationItem.style.transition = 'all 0.05s ease-out';
                    formationItem.remove();
                }

                // Mettre à jour l'UI du panier
                updateCartUI(data);

                // CORRECTION: Supprimer la formation des sets IMMÉDIATEMENT
                processedCompleteFormationIds.delete(formationId.toString());
                processedExpiredFormationIds.delete(formationId.toString());

                // AJOUT: Mettre à jour les alertes IMMÉDIATEMENT après suppression des sets
                updateWarnings();

                // Vérifier s'il reste des formations problématiques
                const hasRemainingCompleteFormations = processedCompleteFormationIds.size > 0;
                const hasRemainingExpiredFormations = processedExpiredFormationIds.size > 0;

                // Mettre à jour le bouton réserver si nécessaire
                if (!hasRemainingCompleteFormations && !hasRemainingExpiredFormations) {
                    const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');
                    if (reserverButton) {
                        reserverButton.disabled = false;
                        reserverButton.classList.remove('disabled');
                        reserverButton.removeAttribute('title');
                    }
                    window.hasCompleteFormationsInCart = false;
                    window.hasExpiredFormationsInCart = false;
                }

                // Masquer l'en-tête du panier si vide
                if (data.cartCount === 0) {
                    const panierHeader = document.querySelector('.panier-header');
                    if (panierHeader) {
                        panierHeader.style.display = 'none';
                    }
                }
            } else {
                if (formationItem) {
                    formationItem.classList.remove('removing');
                    formationItem.style.opacity = '1';
                }
            }
            if (callback) callback();
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);
            if (formationItem) {
                formationItem.classList.remove('removing');
                formationItem.style.opacity = '1';
            }
            showNotification('Erreur lors de la suppression', 'error');
            if (callback) callback();
        });
}

function addEmptyCartStyles() {
    if (document.querySelector('#empty-cart-styles')) {
        return;
    }
    const style = document.createElement('style');
    style.id = 'empty-cart-styles';
    style.textContent = `
        .empty-cart-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 2rem 0;
            background-color: transparent;
            margin: 0;
            text-align: left;
        }

        .empty-cart-icon {
            margin-bottom: 1.5rem;
        }

        .empty-cart-icon i {
            font-size: 2.5rem;
            color: #374151;
        }

        .empty-cart-title {
            font-size: 2rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .empty-cart-subtitle {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
            max-width: 600px;
        }

        .discover-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: transparent;
            color: #3b82f6;
            text-decoration: none;
            border: none;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .discover-btn:hover {
            color: #2563eb;
            text-decoration: none;
        }

        .discover-btn i {
            font-size: 0.875rem;
            margin-left: 0.25rem;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .empty-cart-container {
            animation: fadeInUp 0.6s ease-out;
        }
    `;

    document.head.appendChild(style);
}
function initializeEmptyCartDisplay() {
    if (window.location.pathname.includes('/panier')) {
        const panierContent = document.querySelector('.panier-content');
        const emptyCartPlaceholder = document.querySelector('.empty-cart-placeholder');
        const oldEmptyCart = document.querySelector('.empty-cart');

        if (!panierContent || emptyCartPlaceholder || oldEmptyCart) {
            showEmptyCartMessage();
        }
    }
}
function showNotification(message, type = 'info') {
    if (typeof toast !== 'undefined' && toast.show) {
        toast.show(message, type);
    } else if (type === 'error') {
        alert('Erreur: ' + message);
    } else {
        alert(message);
    }
}
function updateAddToCartButton(formationId, inCart, isComplete = false) {
    const buttons = document.querySelectorAll(`.addcart-btn .btn[href="/panier"][data-formation-id="${formationId}"], .addcart-btn .btn[href="/panier"][data-category-id="${formationId}"]`);
    buttons.forEach(button => {
        if (inCart) {
            button.setAttribute('data-in-cart', 'true');
            button.textContent = 'Voir le panier';
            button.disabled = false;
        } else {
            button.removeAttribute('data-in-cart');
            button.textContent = 'Ajouter au panier';
            button.disabled = isComplete;
        }
    });
}
function addToCart(formationId, redirectToCart = false, callback = null) {
    const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    if (cartFormations.includes(formationId.toString())) {
        showNotification('Cette formation est déjà dans votre panier', 'info');
        if (callback) callback();
        return;
    }
    const currentCartCount = parseInt(localStorage.getItem('cartCount') || '0');
    const newCartCount = currentCartCount + 1;
    localStorage.setItem('cartCount', newCartCount.toString());
    updateCartBadges(newCartCount);
    updateAddToCartButton(formationId, true);

    fetch('/panier/ajouter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({ training_id: formationId })
    })
        .then(handleResponse)
        .then(data => {
            if (data.success) {
                localStorage.setItem('cartCount', data.cartCount.toString());
                if (!cartFormations.includes(formationId.toString())) {
                    cartFormations.push(formationId.toString());
                    localStorage.setItem('cartFormations', JSON.stringify(cartFormations));
                }
                updateCartBadges(data.cartCount);
                updateAddToCartButton(formationId, true);
                showNotification(data.message, 'success');
                if (redirectToCart) {
                    window.location.href = '/panier';
                }
            } else {
                localStorage.setItem('cartCount', currentCartCount.toString());
                updateCartBadges(currentCartCount);
                updateAddToCartButton(formationId, false);
                showNotification(data.message, 'error');
            }
            if (callback) callback();
        })
        .catch(error => {
            console.error('Erreur lors de l\'ajout au panier:', error);
            localStorage.setItem('cartCount', currentCartCount.toString());
            updateCartBadges(currentCartCount);
            updateAddToCartButton(formationId, false);
            showNotification('Erreur lors de l\'ajout au panier', 'error');
            if (callback) callback();
        });
}

function addBadgeToIcon(icon, cartCount) {
    const container = icon.closest('a, div, button, .cart-container');
    if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
        const badge = document.createElement('span');
        badge.className = 'cart-badge custom-violet-badge';
        badge.textContent = cartCount.toString();
        badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
        badge.style.opacity = cartCount > 0 ? '1' : '0';

        if (getComputedStyle(container).position === 'static') {
            container.style.position = 'relative';
        }
        container.appendChild(badge);
    }
}
function checkAndShowEmptyCart() {
    const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
    const isOnCartPage = window.location.pathname.includes('/panier');

    if (cartCount === 0 && isOnCartPage) {
        showEmptyCartMessage();
    }
}
function showEmptyCartMessage() {
    console.log('Affichage du message de panier vide amélioré');

    const authRequiredContainer = document.querySelector('.auth-required-container');
    if (authRequiredContainer) {
        console.log('Utilisateur non connecté - Message "Connexion Requise" déjà affiché');
        return;
    }

    const panierHeader = document.querySelector('.panier-header');
    if (!panierHeader) {
        console.log('En-tête panier non trouvé - L\'utilisateur n\'est peut-être pas connecté');
        return;
    }

    const existingEmptyCart = document.querySelector('.empty-cart-container');
    if (existingEmptyCart) {
        console.log('Message "panier vide" déjà présent');
        return;
    }

    const panierContent = document.querySelector('.panier-content');
    if (panierContent) {
        panierContent.remove();
        console.log('Contenu du panier supprimé');
    }

    const oldEmptyCart = document.querySelector('.empty-cart');
    const emptyCartPlaceholder = document.querySelector('.empty-cart-placeholder');

    if (oldEmptyCart) oldEmptyCart.remove();
    if (emptyCartPlaceholder) emptyCartPlaceholder.remove();

    const emptyCartHTML = `
        <div class="empty-cart-container">
            <div class="empty-cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2 class="empty-cart-title">Votre panier est vide</h2>
            <p class="empty-cart-subtitle">
                Découvrez nos formations exceptionnelles et commencez votre parcours d'apprentissage dès aujourd'hui
            </p>
            <a href="formations" class="discover-btn">
                Découvrir nos formations
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    `;

    panierHeader.insertAdjacentHTML('afterend', emptyCartHTML);
    console.log('Message "panier vide" ajouté après l\'en-tête');

    const panierCount = document.querySelector('.panier-count');
    if (panierCount) {
        panierCount.style.display = 'none';
    }

    const emptyCartElement = document.querySelector('.empty-cart-container');
    if (emptyCartElement) {
        emptyCartElement.style.opacity = '0';
        emptyCartElement.style.transform = 'translateY(30px)';

        requestAnimationFrame(() => {
            emptyCartElement.style.transition = 'all 0.6s ease-out';
            emptyCartElement.style.opacity = '1';
            emptyCartElement.style.transform = 'translateY(0)';
        });
    }

    if (typeof addEmptyCartStyles === 'function') {
        addEmptyCartStyles();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCart);
} else {
    initializeCart();
}

window.removeFromCart = removeFromCart;
window.updateCartCount = (count) => {
    localStorage.setItem('cartCount', count.toString());
    updateCartBadges(count);
};
window.checkCartItemsStatus = checkCartItemsStatus;
window.addToCart = addToCart;
window.showNotification = showNotification;
window.checkFormationsInCart = checkFormationsInCart;
