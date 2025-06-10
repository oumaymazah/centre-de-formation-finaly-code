// // cart-sync.js - Script de synchronisation du panier entre toutes les pages
// (function() {
//     'use strict';
    
//     // Configuration
//     const STORAGE_EVENTS = {
//         CART_UPDATED: 'cartUpdated',
//         CART_ITEM_ADDED: 'cartItemAdded',
//         CART_ITEM_REMOVED: 'cartItemRemoved'
//     };
    
//     const SYNC_INTERVAL = 30000; // 30 secondes
//     let isCurrentlyUpdating = false;
//     let lastKnownCount = parseInt(localStorage.getItem('cartCount') || '0');
    
//     // Gestionnaire centralisé pour tous les événements du panier
//     class CartSyncManager {
//         constructor() {
//             this.initEventListeners();
//             this.startPeriodicSync();
//             this.interceptNetworkRequests();
//         }
        
//         // Initialiser les écouteurs d'événements
//         initEventListeners() {
//             // Écouter les changements dans localStorage depuis d'autres onglets
//             window.addEventListener('storage', (e) => {
//                 if (e.key === 'cartCount' && e.newValue !== e.oldValue) {
//                     this.handleCartCountChange(e.newValue);
//                 }
                
//                 if (e.key === 'cartFormations' && e.newValue !== e.oldValue) {
//                     this.handleCartFormationsChange(e.newValue);
//                 }
//             });
            
//             // Écouter les événements personnalisés du panier
//             window.addEventListener(STORAGE_EVENTS.CART_UPDATED, (e) => {
//                 this.syncFromEvent(e.detail);
//             });
            
//             window.addEventListener(STORAGE_EVENTS.CART_ITEM_ADDED, (e) => {
//                 this.handleItemAdded(e.detail);
//             });
            
//             window.addEventListener(STORAGE_EVENTS.CART_ITEM_REMOVED, (e) => {
//                 this.handleItemRemoved(e.detail);
//             });
            
//             // Écouter les changements de visibilité de la page
//             document.addEventListener('visibilitychange', () => {
//                 if (!document.hidden) {
//                     this.forceSyncFromServer();
//                 }
//             });
            
//             // Écouter les événements de changement de focus
//             window.addEventListener('focus', () => {
//                 this.forceSyncFromServer();
//             });
//         }
        
//         // Gérer les changements de compteur depuis d'autres onglets
//         handleCartCountChange(newValue) {
//             if (isCurrentlyUpdating) return;
            
//             const newCount = parseInt(newValue || '0');
//             if (newCount !== lastKnownCount) {
//                 console.log(`[CartSync] Synchronisation du compteur: ${lastKnownCount} → ${newCount}`);
//                 lastKnownCount = newCount;
                
//                 // Utiliser la fonction existante updateCartBadges si disponible
//                 if (typeof window.updateCartBadges === 'function') {
//                     window.updateCartBadges(newCount);
//                 } else if (typeof updateCartBadges === 'function') {
//                     updateCartBadges(newCount);
//                 }
                
//                 // Vérifier si on doit afficher le message de panier vide
//                 if (newCount === 0 && window.location.pathname.includes('/panier')) {
//                     if (typeof window.showEmptyCartMessage === 'function') {
//                         window.showEmptyCartMessage();
//                     } else if (typeof showEmptyCartMessage === 'function') {
//                         showEmptyCartMessage();
//                     }
//                 }
//             }
//         }
        
//         // Gérer les changements de formations depuis d'autres onglets
//         handleCartFormationsChange(newValue) {
//             if (isCurrentlyUpdating) return;
            
//             try {
//                 const formations = JSON.parse(newValue || '[]');
//                 console.log('[CartSync] Synchronisation des formations:', formations);
                
//                 // Mettre à jour l'affichage des boutons
//                 if (typeof window.checkFormationsInCart === 'function') {
//                     window.checkFormationsInCart();
//                 } else if (typeof checkFormationsInCart === 'function') {
//                     checkFormationsInCart();
//                 }
//             } catch (error) {
//                 console.error('[CartSync] Erreur lors de la synchronisation des formations:', error);
//             }
//         }
        
//         // Forcer la synchronisation depuis le serveur
//         forceSyncFromServer() {
//             if (isCurrentlyUpdating) return;
            
//             console.log('[CartSync] Synchronisation forcée depuis le serveur');
            
//             // Utiliser la fonction existante syncCartCount si disponible
//             if (typeof window.syncCartCount === 'function') {
//                 window.syncCartCount();
//             } else if (typeof syncCartCount === 'function') {
//                 syncCartCount();
//             } else {
//                 // Fallback - synchronisation basique
//                 this.basicServerSync();
//             }
            
//             // Vérifier l'état des formations
//             if (typeof window.checkCartItemsStatus === 'function') {
//                 window.checkCartItemsStatus();
//             } else if (typeof checkCartItemsStatus === 'function') {
//                 checkCartItemsStatus();
//             }
//         }
        
//         // Synchronisation basique en cas d'absence de fonction existante
//         basicServerSync() {
//             const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
//             if (!csrfToken) return;
            
//             fetch('/panier/count', {
//                 method: 'GET',
//                 headers: {
//                     'Accept': 'application/json',
//                     'X-CSRF-TOKEN': csrfToken,
//                     'X-Requested-With': 'XMLHttpRequest'
//                 }
//             })
//             .then(response => response.json())
//             .then(data => {
//                 const serverCount = parseInt(data.count || 0);
//                 const localCount = parseInt(localStorage.getItem('cartCount') || '0');
                
//                 if (serverCount !== localCount) {
//                     isCurrentlyUpdating = true;
//                     localStorage.setItem('cartCount', serverCount.toString());
//                     lastKnownCount = serverCount;
                    
//                     // Émettre un événement pour notifier les autres composants
//                     this.emitCartEvent(STORAGE_EVENTS.CART_UPDATED, {
//                         count: serverCount,
//                         source: 'server-sync'
//                     });
                    
//                     setTimeout(() => {
//                         isCurrentlyUpdating = false;
//                     }, 100);
//                 }
//             })
//             .catch(error => {
//                 console.error('[CartSync] Erreur lors de la synchronisation:', error);
//             });
//         }
        
//         // Démarrer la synchronisation périodique
//         startPeriodicSync() {
//             setInterval(() => {
//                 if (!document.hidden) {
//                     this.forceSyncFromServer();
//                 }
//             }, SYNC_INTERVAL);
//         }
        
//         // Intercepter les requêtes réseau pour détecter les modifications du panier
//         interceptNetworkRequests() {
//             // Intercepter fetch
//             const originalFetch = window.fetch;
//             window.fetch = async (...args) => {
//                 const response = await originalFetch(...args);
                
//                 // Vérifier si c'est une requête liée au panier
//                 const url = args[0];
//                 if (typeof url === 'string' && this.isCartRelatedUrl(url)) {
//                     // Attendre un peu puis synchroniser
//                     setTimeout(() => {
//                         this.forceSyncFromServer();
//                     }, 500);
//                 }
                
//                 return response;
//             };
            
//             // Intercepter XMLHttpRequest
//             const originalXHROpen = XMLHttpRequest.prototype.open;
//             XMLHttpRequest.prototype.open = function(method, url, ...args) {
//                 this.addEventListener('loadend', () => {
//                     if (this.status >= 200 && this.status < 300) {
//                         if (cartSyncManager.isCartRelatedUrl(url)) {
//                             setTimeout(() => {
//                                 cartSyncManager.forceSyncFromServer();
//                             }, 500);
//                         }
//                     }
//                 });
                
//                 return originalXHROpen.call(this, method, url, ...args);
//             };
//         }
        
//         // Vérifier si l'URL est liée au panier
//         isCartRelatedUrl(url) {
//             const cartUrls = [
//                 '/panier/ajouter',
//                 '/panier/supprimer',
//                 '/panier/count',
//                 '/panier/items',
//                 '/panier/check-availability'
//             ];
            
//             return cartUrls.some(cartUrl => url.includes(cartUrl));
//         }
        
//         // Émettre un événement personnalisé
//         emitCartEvent(eventType, detail) {
//             const event = new CustomEvent(eventType, { detail });
//             window.dispatchEvent(event);
//         }
        
//         // Fonction publique pour déclencher manuellement la synchronisation
//         manualSync() {
//             console.log('[CartSync] Synchronisation manuelle déclenchée');
//             this.forceSyncFromServer();
//         }
        
//         // Fonction publique pour notifier un changement local
//         notifyLocalChange(type, data) {
//             isCurrentlyUpdating = true;
            
//             // Mettre à jour localStorage
//             if (data.count !== undefined) {
//                 localStorage.setItem('cartCount', data.count.toString());
//                 lastKnownCount = data.count;
//             }
            
//             if (data.formations !== undefined) {
//                 localStorage.setItem('cartFormations', JSON.stringify(data.formations));
//             }
            
//             // Émettre l'événement
//             this.emitCartEvent(type, data);
            
//             // Déclencher la synchronisation après un délai
//             setTimeout(() => {
//                 isCurrentlyUpdating = false;
//                 this.forceSyncFromServer();
//             }, 1000);
//         }
//     }
    
//     // Initialiser le gestionnaire de synchronisation
//     const cartSyncManager = new CartSyncManager();
    
//     // Exposer l'API publique
//     window.CartSync = {
//         // Forcer la synchronisation
//         sync: () => cartSyncManager.manualSync(),
        
//         // Notifier un changement local
//         notify: (type, data) => cartSyncManager.notifyLocalChange(type, data),
        
//         // Événements disponibles
//         EVENTS: STORAGE_EVENTS,
        
//         // Émettre un événement personnalisé
//         emit: (eventType, detail) => cartSyncManager.emitCartEvent(eventType, detail)
//     };
    
//     // Méthodes utilitaires globales
//     window.syncCartNow = () => cartSyncManager.manualSync();
    
//     // Log d'initialisation
//     console.log('[CartSync] Gestionnaire de synchronisation du panier initialisé');
    
//     // Synchronisation initiale après un court délai
//     setTimeout(() => {
//         cartSyncManager.forceSyncFromServer();
//     }, 1000);
// })();