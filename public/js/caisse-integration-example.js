/**
 * EXEMPLE DE CODE MODIFIÉ POUR CAISSE.BLADE.PHP
 *
 * Ce fichier montre comment modifier les fonctions saveEntree et saveSortie
 * pour utiliser l'API backend au lieu de localStorage uniquement.
 *
 * COPIEZ ET REMPLACEZ les fonctions correspondantes dans caisse.blade.php
 */

// ==================== FONCTION SAVEENTREE MODIFIÉE ====================
// Remplacer la fonction saveEntree (ligne ~2137) par cette version :

async function saveEntree(event) {
    event.preventDefault();

    if (!ensureAuth() || !hasPermission('entries')) return;

    const id = document.getElementById('entreeId').value || generateUID();
    const date = document.getElementById('entreeDate').value;
    let ref = document.getElementById('entreeRef').value.trim();
    const nom = document.getElementById('entreeNom').value.trim();
    const prenoms = document.getElementById('entreePrenoms').value.trim();
    const categorie = document.getElementById('entreeCategorie').value;
    let nature = categorie;
    const montant = parseFloat(document.getElementById('entreeMontant').value);
    const modePaiement = document.getElementById('entreeMode').value;

    // Gérer la catégorie "Autre"
    if (categorie === 'Autre') {
        nature = document.getElementById('entreeAutreNature').value.trim();
        if (!nature) {
            showToast('Veuillez préciser la nature', 'error');
            return;
        }
    }

    // Validation de base
    if (!date || !nom || !prenoms || !categorie || montant <= 0 || !modePaiement) {
        showToast('Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }

    // Récupérer le détail des prestations
    const detailPrestations = {};
    const prestationInputs = document.querySelectorAll('.prestation-montant');
    prestationInputs.forEach(input => {
        const prestation = input.dataset.prestation;
        const montantPrestation = parseFloat(input.value) || 0;
        if (montantPrestation > 0) {
            detailPrestations[prestation] = montantPrestation;
        }
    });

    let tiersNom = document.getElementById('entreeTiersNom').value.trim();
    let montantVerseTiers = parseFloat(document.getElementById('entreeMontantVerseTiers').value) || 0;

    const isDocument = categorie === 'Documents de Voyage';

    if (isDocument && (!tiersNom || montantVerseTiers < 0 || montantVerseTiers > montant)) {
        showToast('Détails du document incomplets', 'error');
        currentEntreeMontant = montant;
        openDocumentModal(montant);
        return;
    }

    if (!isDocument) {
        tiersNom = '';
        montantVerseTiers = 0;
    }

    const marge = categorie === 'Frais de Cabinet' ? montant : (montant - montantVerseTiers);

    const entrees = getEntrees();
    const isUpdate = entrees.some(e => e.id === id);
    const existing = entrees.find(e => e.id === id);

    if (currentUser.role === 'agent' && isUpdate && existing.createdBy !== currentUser.username) {
        showToast('Vous ne pouvez modifier que vos propres entrées', 'error');
        return;
    }

    // Ne pas vérifier la référence si elle sera générée par le backend
    if (ref && entrees.some(e => e.ref === ref && e.id !== id)) {
        showToast('Cette référence existe déjà', 'error');
        return;
    }

    const entree = {
        id: id,
        date: date,
        ref: ref, // Sera écrasée par la ref générée par le backend
        nom: nom,
        prenoms: prenoms,
        categorie: categorie,
        nature: nature,
        montant: montant,
        modePaiement: modePaiement,
        isDocument: isDocument,
        tiersNom: tiersNom || null,
        montantVerseTiers: montantVerseTiers,
        marge: marge,
        detailPrestations: Object.keys(detailPrestations).length > 0 ? detailPrestations : null,
        createdBy: isUpdate ? existing.createdBy : currentUser.username,
        createdAt: isUpdate ? existing.createdAt : new Date().toISOString(),
        updatedAt: new Date().toISOString()
    };

    // ========== NOUVELLE PARTIE : SAUVEGARDER DANS LA BDD ==========
    try {
        const savedEntree = await saveEntreeToDB(entree);

        // Mettre à jour localStorage pour compatibilité avec le code existant
        if (isUpdate) {
            const index = entrees.findIndex(e => e.id === id);
            // Mettre à jour avec les données de la BDD
            entrees[index] = {
                ...entree,
                id: savedEntree.uuid,
                ref: savedEntree.ref
            };
            logAudit('UPDATE_ENTREE', `Entrée ${savedEntree.ref} modifiée`);
            showToast('Entrée modifiée avec succès', 'success');
        } else {
            // Ajouter avec les données de la BDD (ref générée, uuid, etc.)
            entrees.push({
                ...entree,
                id: savedEntree.uuid,
                ref: savedEntree.ref
            });
            logAudit('ADD_ENTREE', `Entrée ${savedEntree.ref} ajoutée: ${categorie} - ${nature}`);
            showToast(`Entrée ${savedEntree.ref} ajoutée avec succès`, 'success');
        }

        saveEntrees(entrees.sort((a, b) => new Date(b.date) - new Date(a.date)));
        renderEntreesTable();

        if (hasPermission('dashboard')) {
            refreshDashboard();
        }

        cancelEntree();
    } catch (error) {
        console.error('Erreur sauvegarde entrée:', error);
        showToast('Erreur lors de l\'enregistrement: ' + error.message, 'error');
    }
    // ========== FIN DE LA NOUVELLE PARTIE ==========
}

// ==================== FONCTION DELETEENTREE MODIFIÉE ====================
// Remplacer la fonction deleteEntree (ligne ~2328) par cette version :

async function deleteEntree(id) {
    if (!ensureAuth() || !hasPermission('entries')) return;

    if (!confirm('Supprimer cette entrée ?')) return;

    let entrees = getEntrees();
    const entree = entrees.find(e => e.id === id);

    if (currentUser.role === 'agent' && entree.createdBy !== currentUser.username) {
        showToast('Vous ne pouvez supprimer que vos propres entrées', 'error');
        return;
    }

    // ========== NOUVELLE PARTIE : SUPPRIMER DE LA BDD ==========
    try {
        // Supprimer de la BDD
        await deleteEntreeFromDB(id);

        // Supprimer de localStorage
        entrees = entrees.filter(e => e.id !== id);
        saveEntrees(entrees);
        renderEntreesTable();

        if (hasPermission('dashboard')) {
            refreshDashboard();
        }

        logAudit('DELETE_ENTREE', `Entrée ${entree.ref} supprimée`);
        showToast('Entrée supprimée avec succès', 'success');
    } catch (error) {
        console.error('Erreur suppression entrée:', error);
        showToast('Erreur lors de la suppression: ' + error.message, 'error');
    }
    // ========== FIN DE LA NOUVELLE PARTIE ==========
}

// ==================== FONCTION SAVESORTIE MODIFIÉE ====================
// Remplacer la fonction saveSortie par cette version :

async function saveSortie(event) {
    event.preventDefault();

    if (!ensureAuth() || !hasPermission('exits')) return;

    const id = document.getElementById('sortieId').value || generateUID();
    const date = document.getElementById('sortieDate').value;
    let ref = document.getElementById('sortieRef').value.trim();
    const beneficiaire = document.getElementById('sortieBeneficiaire').value.trim();
    const motif = document.getElementById('sortieMotif').value.trim();
    const montant = parseFloat(document.getElementById('sortieMontant').value);
    const modePaiement = document.getElementById('sortieMode').value;
    const remarques = document.getElementById('sortieRemarques').value.trim();

    // Validation
    if (!date || !beneficiaire || !motif || montant <= 0 || !modePaiement) {
        showToast('Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }

    const sorties = getSorties();
    const isUpdate = sorties.some(s => s.id === id);
    const existing = sorties.find(s => s.id === id);

    if (currentUser.role === 'agent' && isUpdate && existing.createdBy !== currentUser.username) {
        showToast('Vous ne pouvez modifier que vos propres sorties', 'error');
        return;
    }

    // Ne pas vérifier la référence si elle sera générée par le backend
    if (ref && sorties.some(s => s.ref === ref && s.id !== id)) {
        showToast('Cette référence existe déjà', 'error');
        return;
    }

    const sortie = {
        id: id,
        date: date,
        ref: ref,
        beneficiaire: beneficiaire,
        motif: motif,
        montant: montant,
        modePaiement: modePaiement,
        remarques: remarques,
        createdBy: isUpdate ? existing.createdBy : currentUser.username,
        createdAt: isUpdate ? existing.createdAt : new Date().toISOString(),
        updatedAt: new Date().toISOString()
    };

    // ========== NOUVELLE PARTIE : SAUVEGARDER DANS LA BDD ==========
    try {
        const savedSortie = await saveSortieToDB(sortie);

        // Mettre à jour localStorage
        if (isUpdate) {
            const index = sorties.findIndex(s => s.id === id);
            sorties[index] = {
                ...sortie,
                id: savedSortie.uuid,
                ref: savedSortie.ref
            };
            logAudit('UPDATE_SORTIE', `Sortie ${savedSortie.ref} modifiée`);
            showToast('Sortie modifiée avec succès', 'success');
        } else {
            sorties.push({
                ...sortie,
                id: savedSortie.uuid,
                ref: savedSortie.ref
            });
            logAudit('ADD_SORTIE', `Sortie ${savedSortie.ref} ajoutée`);
            showToast(`Sortie ${savedSortie.ref} ajoutée avec succès`, 'success');
        }

        saveSorties(sorties.sort((a, b) => new Date(b.date) - new Date(a.date)));
        renderSortiesTable();

        if (hasPermission('dashboard')) {
            refreshDashboard();
        }

        cancelSortie();
    } catch (error) {
        console.error('Erreur sauvegarde sortie:', error);
        showToast('Erreur lors de l\'enregistrement: ' + error.message, 'error');
    }
    // ========== FIN DE LA NOUVELLE PARTIE ==========
}

// ==================== FONCTION DELETESORTIE MODIFIÉE ====================
// Remplacer la fonction deleteSortie par cette version :

async function deleteSortie(id) {
    if (!ensureAuth() || !hasPermission('exits')) return;

    if (!confirm('Supprimer cette sortie ?')) return;

    let sorties = getSorties();
    const sortie = sorties.find(s => s.id === id);

    if (currentUser.role === 'agent' && sortie.createdBy !== currentUser.username) {
        showToast('Vous ne pouvez supprimer que vos propres sorties', 'error');
        return;
    }

    // ========== NOUVELLE PARTIE : SUPPRIMER DE LA BDD ==========
    try {
        // Supprimer de la BDD
        await deleteSortieFromDB(id);

        // Supprimer de localStorage
        sorties = sorties.filter(s => s.id !== id);
        saveSorties(sorties);
        renderSortiesTable();

        if (hasPermission('dashboard')) {
            refreshDashboard();
        }

        logAudit('DELETE_SORTIE', `Sortie ${sortie.ref} supprimée`);
        showToast('Sortie supprimée avec succès', 'success');
    } catch (error) {
        console.error('Erreur suppression sortie:', error);
        showToast('Erreur lors de la suppression: ' + error.message, 'error');
    }
    // ========== FIN DE LA NOUVELLE PARTIE ==========
}

// ==================== CHARGEMENT INITIAL DES DONNÉES ====================
// Ajouter ce code dans la section d'initialisation (après DOMContentLoaded) :

// Charger les données depuis la BDD au démarrage
(async function initCaisseFromDB() {
    try {
        console.log('🔄 Chargement des données depuis la base de données...');

        await loadDataFromDB();

        // Rafraîchir l'affichage
        if (typeof renderEntreesTable === 'function') {
            renderEntreesTable();
        }
        if (typeof renderSortiesTable === 'function') {
            renderSortiesTable();
        }
        if (typeof refreshDashboard === 'function') {
            refreshDashboard();
        }

        console.log('✅ Données chargées avec succès depuis la base de données');
    } catch (error) {
        console.error('❌ Erreur lors du chargement initial:', error);
        showToast('Erreur de chargement des données', 'warning');
    }
})();
