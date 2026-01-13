/**
 * ✅ SCRIPT JAVASCRIPT CORRIGÉ POUR GESTION DES PERMISSIONS PAR RÔLE
 * Fichier: public/js/get-role.js
 * 
 * Ce script gère l'affichage dynamique des permissions 
 * lors du changement de rôle dans les formulaires
 */

$(document).ready(function() {
    console.log('🔧 Initialisation du gestionnaire de permissions par rôle');
    
    // Initialiser les gestionnaires d'événements
    initRolePermissionHandlers();
    
    // Charger les permissions au démarrage si un rôle est sélectionné
    loadInitialPermissions();
});

/**
 * ✅ INITIALISER LES GESTIONNAIRES D'ÉVÉNEMENTS
 */
function initRolePermissionHandlers() {
    // Gestionnaire principal pour le changement de rôle
    $(document).on('change', '#role', function() {
        const roleId = $(this).val();
        console.log('🔄 Changement de rôle détecté:', roleId);
        
        if (roleId && roleId !== '') {
            loadRolePermissions(roleId);
        } else {
            resetPermissionsDisplay();
        }
    });
    
    // Gestionnaire pour les formulaires d'édition
    $(document).on('change', 'select[name="role"]', function() {
        const roleId = $(this).val();
        console.log('🔄 Changement de rôle (édition):', roleId);
        
        if (roleId && roleId !== '') {
            loadRolePermissions(roleId);
        } else {
            resetPermissionsDisplay();
        }
    });
    
    // Gestionnaire pour les autres sélecteurs de rôle
    $(document).on('change', '.role-selector', function() {
        const roleId = $(this).val();
        if (roleId && roleId !== '') {
            loadRolePermissions(roleId);
        } else {
            resetPermissionsDisplay();
        }
    });
}

/**
 * ✅ CHARGER LES PERMISSIONS INITIALES
 */
function loadInitialPermissions() {
    // Vérifier s'il y a un rôle pré-sélectionné
    const initialRoleId = $('#role').val() || $('select[name="role"]').val();
    
    if (initialRoleId && initialRoleId !== '') {
        console.log('🎯 Chargement initial des permissions pour rôle:', initialRoleId);
        setTimeout(() => {
            loadRolePermissions(initialRoleId);
        }, 500); // Petit délai pour s'assurer que le DOM est prêt
    }
}

/**
 * ✅ FONCTION PRINCIPALE : CHARGER LES PERMISSIONS D'UN RÔLE
 */
function loadRolePermissions(roleId) {
    console.log('📋 Chargement des permissions pour le rôle:', roleId);
    
    if (!roleId || roleId === '') {
        resetPermissionsDisplay();
        return;
    }
    
    // Trouver le conteneur des permissions
    const permissionContainer = findPermissionContainer();
    if (!permissionContainer || permissionContainer.length === 0) {
        console.error('❌ Container de permissions non trouvé');
        return;
    }
    
    // Afficher un indicateur de chargement
    showLoadingIndicator(permissionContainer);
    
    // Obtenir le token CSRF
    const token = getCSRFToken();
    
    // Essayer plusieurs URLs pour la compatibilité
    const urls = [
        `/get-role-permissions-badge?role_id=${roleId}`,
        `/get-role-permissions?role_id=${roleId}`,
        `/role/${roleId}/permissions`,
        `/api/roles/${roleId}/permissions`,
        `/permissions/role/${roleId}`
    ];
    
    tryPermissionRequest(urls, 0, roleId, token, permissionContainer);
}

/**
 * ✅ ESSAYER LES REQUÊTES DE PERMISSIONS AVEC FALLBACK
 */
function tryPermissionRequest(urls, index, roleId, token, container) {
    if (index >= urls.length) {
        console.log('⚠️ Toutes les URLs ont échoué, affichage des permissions par défaut');
        showDefaultPermissions(roleId, container);
        return;
    }
    
    const url = urls[index];
    console.log(`🔄 Tentative ${index + 1}/${urls.length}: ${url}`);
    
    $.ajax({
        url: url,
        type: 'GET',
        data: { 
            role_id: roleId,
            id: roleId,
            roleId: roleId,
            _token: token 
        },
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        timeout: 10000,
        success: function(response) {
            console.log('✅ Succès avec URL:', url);
            console.log('📦 Réponse:', response);
            handlePermissionResponse(response, roleId, container);
        },
        error: function(xhr, status, error) {
            console.warn(`⚠️ Échec URL ${url}:`, {
                status: xhr.status,
                statusText: xhr.statusText,
                error: error
            });
            
            // Essayer la prochaine URL après un délai
            setTimeout(() => {
                tryPermissionRequest(urls, index + 1, roleId, token, container);
            }, 500);
        }
    });
}

/**
 * ✅ GÉRER LA RÉPONSE DES PERMISSIONS
 */
function handlePermissionResponse(response, roleId, container) {
    try {
        let htmlContent = '';
        
        // Gestion de différents formats de réponse
        if (response && typeof response === 'object') {
            if (response.success === true && response.badges) {
                htmlContent = response.badges;
            } else if (response.success === true && response.permissions) {
                htmlContent = response.permissions;
            } else if (response.badges) {
                htmlContent = response.badges;
            } else if (response.permissions) {
                htmlContent = response.permissions;
            } else if (response.data) {
                htmlContent = response.data;
            }
        } else if (typeof response === 'string' && response.trim() !== '') {
            htmlContent = response;
        }
        
        // Vérifier si le contenu est valide
        if (htmlContent && htmlContent.trim() !== '' && !htmlContent.includes('Erreur') && !htmlContent.includes('error')) {
            container.html(htmlContent);
            animatePermissionBadges();
            showSuccessNotification('Permissions chargées avec succès');
        } else {
            console.log('📄 Contenu invalide ou vide, utilisation des permissions par défaut');
            showDefaultPermissions(roleId, container);
        }
        
    } catch (error) {
        console.error('❌ Erreur traitement réponse:', error);
        showDefaultPermissions(roleId, container);
    }
}

/**
 * ✅ AFFICHER LES PERMISSIONS PAR DÉFAUT
 */
function showDefaultPermissions(roleId, container) {
    console.log('📄 Affichage des permissions par défaut pour rôle:', roleId);
    
    // Récupérer le nom du rôle depuis le select
    const roleSelect = $('#role, select[name="role"]');
    const roleName = roleSelect.find(`option[value="${roleId}"]`).text().trim();
    
    console.log('🏷️ Nom du rôle:', roleName);
    
    const defaultHtml = generateDefaultPermissionsHtml(roleName, roleId);
    container.html(defaultHtml);
    
    // Animer les badges après un court délai
    setTimeout(() => {
        animatePermissionBadges();
    }, 100);
    
    showInfoNotification('Permissions par défaut chargées');
}

/**
 * ✅ GÉNÉRER LE HTML DES PERMISSIONS PAR DÉFAUT
 */
function generateDefaultPermissionsHtml(roleName, roleId) {
    // Définitions des permissions par défaut selon le rôle
    const rolePermissions = {
        'Super Admin': {
            permissions: ['Administration complète', 'Toutes les permissions', 'Accès total'],
            isSuper: true
        },
        'Admin': {
            permissions: ['Gestion utilisateurs', 'Gestion rôles', 'Dashboard admin', 'Configuration système', 'Export données'],
            isSuper: false
        },
        'Agent Comptoir': {
            permissions: ['Gestion profils visa', 'Modification statuts', 'Dashboard comptoir', 'Service client', 'Gestion rendez-vous'],
            isSuper: false
        },
        'Commercial': {
            permissions: ['Gestion clients', 'Gestion forfaits', 'Dashboard commercial', 'Suivi ventes', 'Gestion partenaires'],
            isSuper: false
        }
    };
    
    // Trouver les permissions correspondantes
    let config = { permissions: ['Accès de base', 'Consultation dashboard'], isSuper: false };
    
    for (const [role, roleConfig] of Object.entries(rolePermissions)) {
        if (roleName.includes(role) || role.toLowerCase().includes(roleName.toLowerCase())) {
            config = roleConfig;
            break;
        }
    }
    
    // Cas spécial pour Super Admin
    if (config.isSuper || roleName.toLowerCase().includes('super')) {
        return generateSuperAdminBadge();
    }
    
    // Générer les badges normaux
    return generateNormalPermissionBadges(config.permissions, roleName);
}

/**
 * ✅ GÉNÉRER LE BADGE SUPER ADMIN
 */
function generateSuperAdminBadge() {
    return `
        <div class="alert alert-warning mb-0 super-admin-badge" style="border-radius: 8px; border: 2px solid #ffc107; background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); animation: subtlePulse 3s infinite;">
            <div class="d-flex align-items-center">
                <i class="fas fa-crown text-warning fa-2x me-3"></i>
                <div>
                    <h6 class="mb-1 text-warning fw-bold">
                        <i class="fas fa-infinity me-1"></i>
                        Super Administrateur
                    </h6>
                    <p class="mb-0 small text-dark">
                        Accès complet à toutes les fonctionnalités et permissions du système
                    </p>
                </div>
            </div>
        </div>
        
        <style>
            @keyframes subtlePulse {
                0%, 100% { box-shadow: 0 0 5px rgba(255, 193, 7, 0.3); }
                50% { box-shadow: 0 0 15px rgba(255, 193, 7, 0.5); }
            }
        </style>
    `;
}

/**
 * ✅ GÉNÉRER LES BADGES DE PERMISSIONS NORMAUX
 */
function generateNormalPermissionBadges(permissions, roleName) {
    let html = `<div class="default-permissions">`;
    
    if (roleName) {
        html += `<div class="mb-2"><small class="text-muted">Permissions pour <strong>${escapeHtml(roleName)}</strong>:</small></div>`;
    }
    
    html += `<div class="permissions-grid" style="display: flex; flex-wrap: wrap; gap: 5px;">`;
    
    permissions.forEach((permission, index) => {
        const color = getPermissionBadgeColor(permission);
        const animationDelay = index * 0.1;
        
        html += `
            <span class="badge bg-${color} permission-badge permission-animate" 
                  style="opacity: 0; animation-delay: ${animationDelay}s; font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 8px; transition: all 0.3s ease;"
                  title="${escapeHtml(permission)}">
                <i class="fas fa-key me-1"></i>
                ${escapeHtml(permission)}
            </span>
        `;
    });
    
    html += `</div></div>`;
    
    return html;
}

/**
 * ✅ OBTENIR LA COULEUR DU BADGE SELON LA PERMISSION
 */
function getPermissionBadgeColor(permission) {
    const normalizedPermission = permission.toLowerCase();
    
    if (normalizedPermission.includes('gestion') || normalizedPermission.includes('manage')) {
        return 'primary';
    } else if (normalizedPermission.includes('dashboard') || normalizedPermission.includes('tableau')) {
        return 'success';
    } else if (normalizedPermission.includes('admin') || normalizedPermission.includes('système')) {
        return 'danger';
    } else if (normalizedPermission.includes('service') || normalizedPermission.includes('client')) {
        return 'info';
    } else if (normalizedPermission.includes('export') || normalizedPermission.includes('ventes')) {
        return 'warning';
    } else {
        return 'secondary';
    }
}

/**
 * ✅ TROUVER LE CONTENEUR DES PERMISSIONS
 */
function findPermissionContainer() {
    // Essayer différents sélecteurs possibles
    const selectors = [
        '#permission',
        '.permission-container',
        '.permissions-display',
        '[data-permissions]',
        '.role-permissions'
    ];
    
    for (const selector of selectors) {
        const container = $(selector);
        if (container.length > 0) {
            console.log('📦 Conteneur trouvé:', selector);
            return container;
        }
    }
    
    console.error('❌ Aucun conteneur de permissions trouvé');
    return null;
}

/**
 * ✅ AFFICHER L'INDICATEUR DE CHARGEMENT
 */
function showLoadingIndicator(container) {
    container.html(`
        <div class="text-center py-3 loading-indicator">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <span class="text-muted">Chargement des permissions...</span>
        </div>
    `);
}

/**
 * ✅ RÉINITIALISER L'AFFICHAGE DES PERMISSIONS
 */
function resetPermissionsDisplay() {
    const container = findPermissionContainer();
    if (container && container.length > 0) {
        container.html(`
            <div class="permissions-placeholder text-center py-3">
                <i class="fas fa-info-circle me-1 text-muted"></i>
                <span class="text-muted">Sélectionnez un rôle pour voir les permissions</span>
            </div>
        `);
    }
}

/**
 * ✅ ANIMER LES BADGES DE PERMISSIONS
 */
function animatePermissionBadges() {
    $('.permission-animate').each(function(index) {
        const $badge = $(this);
        setTimeout(() => {
            $badge.animate({ opacity: 1 }, 300);
        }, index * 100);
    });
    
    // Ajouter des effets hover
    $('.permission-badge').hover(
        function() { $(this).css('transform', 'translateY(-2px)'); },
        function() { $(this).css('transform', 'translateY(0)'); }
    );
}

/**
 * ✅ OBTENIR LE TOKEN CSRF
 */
function getCSRFToken() {
    // Essayer différentes méthodes pour récupérer le token
    let token = $('input[name="_token"]').val() ||
                $('meta[name="csrf-token"]').attr('content') ||
                $('#token').val() ||
                window.Laravel?.csrfToken;
    
    if (!token) {
        console.warn('⚠️ Token CSRF non trouvé, certaines requêtes peuvent échouer');
        // Essayer de récupérer depuis les cookies si disponible
        token = getCsrfFromCookie();
    }
    
    return token;
}

/**
 * ✅ RÉCUPÉRER LE CSRF DEPUIS LES COOKIES (FALLBACK)
 */
function getCsrfFromCookie() {
    try {
        const name = 'XSRF-TOKEN=';
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookieArray = decodedCookie.split(';');
        
        for(let cookie of cookieArray) {
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(name) === 0) {
                return cookie.substring(name.length, cookie.length);
            }
        }
    } catch (error) {
        console.warn('Erreur récupération CSRF depuis cookies:', error);
    }
    return null;
}

/**
 * ✅ ÉCHAPPER LE HTML POUR ÉVITER LES XSS
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

/**
 * ✅ NOTIFICATIONS
 */
function showSuccessNotification(message) {
    showNotification(message, 'success');
}

function showInfoNotification(message) {
    showNotification(message, 'info');
}

function showErrorNotification(message) {
    showNotification(message, 'error');
}

function showNotification(message, type = 'info') {
    // Ne pas spam les notifications
    if (window.lastNotificationTime && (Date.now() - window.lastNotificationTime) < 1000) {
        return;
    }
    window.lastNotificationTime = Date.now();
    
    console.log(`📢 ${type.toUpperCase()}: ${message}`);
    
    // Si la fonction showNotification globale existe, l'utiliser
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
        return;
    }
    
    // Sinon, utiliser une notification simple
    const colors = {
        success: '#28a745',
        info: '#17a2b8', 
        error: '#dc3545',
        warning: '#ffc107'
    };
    
    const notification = $(`
        <div class="permission-notification" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${colors[type] || colors.info};
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999;
            max-width: 300px;
            font-size: 14px;
            animation: slideInFromRight 0.3s ease;
        ">
            <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>
            ${message}
        </div>
        
        <style>
            @keyframes slideInFromRight {
                from { opacity: 0; transform: translateX(100%); }
                to { opacity: 1; transform: translateX(0); }
            }
        </style>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

/**
 * ✅ FONCTIONS EXPOSÉES GLOBALEMENT POUR COMPATIBILITÉ
 */
window.loadRolePermissions = loadRolePermissions;
window.resetPermissionsDisplay = resetPermissionsDisplay;

// Fonction legacy pour compatibilité
window.getRolePermissions = function(roleId) {
    console.log('📢 Appel de la fonction legacy getRolePermissions, redirection vers loadRolePermissions');
    loadRolePermissions(roleId);
};

/**
 * ✅ DEBUG MODE
 */
if (window.location.search.includes('debug=permissions')) {
    window.debugPermissions = {
        loadRolePermissions: loadRolePermissions,
        resetPermissionsDisplay: resetPermissionsDisplay,
        showDefaultPermissions: showDefaultPermissions,
        findPermissionContainer: findPermissionContainer,
        getCSRFToken: getCSRFToken
    };
    console.log('🔍 Mode debug activé pour les permissions. Utilisez window.debugPermissions');
}