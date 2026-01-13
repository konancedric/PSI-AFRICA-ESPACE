/**
 * Caisse API Synchronization Module
 *
 * Ce module remplace les fonctions localStorage par des appels API vers la base de données
 * pour assurer la persistance des données de caisse.
 */

(function() {
    'use strict';

    // Configuration
    const API_BASE = '/caisse/api';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Helper pour les requêtes API
    async function apiRequest(endpoint, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        };

        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(`${API_BASE}${endpoint}`, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `Erreur HTTP ${response.status}`);
            }

            return result;
        } catch (error) {
            console.error('Erreur API:', error);
            throw error;
        }
    }

    // ==================== FONCTIONS POUR LES ENTRÉES ====================

    /**
     * Récupérer toutes les entrées depuis la BDD
     */
    window.getEntreesFromDB = async function() {
        try {
            const result = await apiRequest('/entrees');
            return result.success ? result.data : [];
        } catch (error) {
            console.error('Erreur chargement entrées:', error);
            // Fallback sur localStorage si l'API échoue
            return JSON.parse(localStorage.getItem('ec_entries') || '[]');
        }
    };

    /**
     * Sauvegarder une entrée dans la BDD
     */
    window.saveEntreeToDB = async function(entreeData) {
        try {
            // Préparer les données pour l'API
            const apiData = {
                date: entreeData.date,
                nom: entreeData.nom,
                prenoms: entreeData.prenoms,
                categorie: entreeData.categorie,
                nature: entreeData.nature,
                montant: entreeData.montant,
                mode_paiement: entreeData.modePaiement,
                detail_prestations: entreeData.detailPrestations || null,
                tiers_nom: entreeData.tiersNom || null,
                montant_verse_tiers: entreeData.montantVerseTiers || 0,
                created_by_username: entreeData.createdBy || window.currentUser?.username || '',
                // Informations du payeur
                type_payeur: entreeData.type_payeur || 'lui_meme',
                payeur_nom_prenom: entreeData.payeur_nom_prenom || null,
                payeur_telephone: entreeData.payeur_telephone || null,
                payeur_relation: entreeData.payeur_relation || null,
                payeur_reference_dossier: entreeData.payeur_reference_dossier || null
            };

            // Si c'est une modification (a un uuid valide et existe dans la BDD)
            // Un UUID valide contient 4 tirets (format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)
            const isValidUUID = entreeData.id &&
                                typeof entreeData.id === 'string' &&
                                entreeData.id.split('-').length === 5 &&
                                entreeData.id.length === 36;

            if (isValidUUID && entreeData.isUpdate) {
                // Mise à jour d'une entrée existante
                const result = await apiRequest(`/entrees/${entreeData.id}`, 'PUT', apiData);
                return result.data;
            } else {
                // Nouvelle entrée (pas d'UUID ou UUID invalide)
                const result = await apiRequest('/entrees', 'POST', apiData);
                return result.data;
            }
        } catch (error) {
            console.error('Erreur sauvegarde entrée:', error);

            // Afficher l'erreur à l'utilisateur
            if (window.showToast) {
                window.showToast('Erreur lors de l\'enregistrement: ' + error.message, 'error');
            }

            throw error;
        }
    };

    /**
     * Supprimer une entrée de la BDD
     */
    window.deleteEntreeFromDB = async function(uuid) {
        try {
            const result = await apiRequest(`/entrees/${uuid}`, 'DELETE');
            return result.success;
        } catch (error) {
            console.error('Erreur suppression entrée:', error);
            throw error;
        }
    };

    // ==================== FONCTIONS POUR LES SORTIES ====================

    /**
     * Récupérer toutes les sorties depuis la BDD
     */
    window.getSortiesFromDB = async function() {
        try {
            const result = await apiRequest('/sorties');
            return result.success ? result.data : [];
        } catch (error) {
            console.error('Erreur chargement sorties:', error);
            // Fallback sur localStorage si l'API échoue
            return JSON.parse(localStorage.getItem('ec_exits') || '[]');
        }
    };

    /**
     * Sauvegarder une sortie dans la BDD
     */
    window.saveSortieToDB = async function(sortieData) {
        try {
            // Préparer les données pour l'API
            const apiData = {
                date: sortieData.date,
                beneficiaire: sortieData.beneficiaire,
                motif: sortieData.motif,
                montant: sortieData.montant,
                mode_paiement: sortieData.modePaiement,
                remarques: sortieData.remarques || null,
                created_by_username: sortieData.createdBy || window.currentUser?.username || ''
            };

            // Si c'est une modification (a un uuid valide et existe dans la BDD)
            // Un UUID valide contient 4 tirets (format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)
            const isValidUUID = sortieData.id &&
                                typeof sortieData.id === 'string' &&
                                sortieData.id.split('-').length === 5 &&
                                sortieData.id.length === 36;

            if (isValidUUID && sortieData.isUpdate) {
                // Mise à jour d'une sortie existante
                const result = await apiRequest(`/sorties/${sortieData.id}`, 'PUT', apiData);
                return result.data;
            } else {
                // Nouvelle sortie (pas d'UUID ou UUID invalide)
                const result = await apiRequest('/sorties', 'POST', apiData);
                return result.data;
            }
        } catch (error) {
            console.error('Erreur sauvegarde sortie:', error);

            // Afficher l'erreur à l'utilisateur
            if (window.showToast) {
                window.showToast('Erreur lors de l\'enregistrement: ' + error.message, 'error');
            }

            throw error;
        }
    };

    /**
     * Supprimer une sortie de la BDD
     */
    window.deleteSortieFromDB = async function(uuid) {
        try {
            const result = await apiRequest(`/sorties/${uuid}`, 'DELETE');
            return result.success;
        } catch (error) {
            console.error('Erreur suppression sortie:', error);
            throw error;
        }
    };

    // ==================== FONCTIONS DE SYNCHRONISATION ====================

    /**
     * Synchroniser les données localStorage vers la BDD
     */
    window.syncToDB = async function() {
        try {
            const entrees = JSON.parse(localStorage.getItem('ec_entries') || '[]');
            const sorties = JSON.parse(localStorage.getItem('ec_exits') || '[]');

            console.log('Synchronisation des données vers la BDD...');
            console.log(`${entrees.length} entrées et ${sorties.length} sorties à synchroniser`);

            // Synchroniser les entrées
            for (const entree of entrees) {
                try {
                    await saveEntreeToDB(entree);
                } catch (error) {
                    console.error('Erreur sync entrée:', entree.ref, error);
                }
            }

            // Synchroniser les sorties
            for (const sortie of sorties) {
                try {
                    await saveSortieToDB(sortie);
                } catch (error) {
                    console.error('Erreur sync sortie:', sortie.ref, error);
                }
            }

            console.log('Synchronisation terminée');

            if (window.showToast) {
                window.showToast('Données synchronisées avec succès', 'success');
            }

            return true;
        } catch (error) {
            console.error('Erreur synchronisation:', error);
            return false;
        }
    };

    /**
     * Charger les données depuis la BDD au démarrage
     */
    window.loadDataFromDB = async function() {
        try {
            console.log('Chargement des données depuis la BDD...');

            const [entrees, sorties] = await Promise.all([
                getEntreesFromDB(),
                getSortiesFromDB()
            ]);

            console.log(`${entrees.length} entrées et ${sorties.length} sorties chargées`);

            // Mettre à jour localStorage avec les données de la BDD
            // (pour compatibilité avec le code existant)
            if (entrees.length > 0) {
                const formattedEntrees = entrees.map(e => ({
                    id: e.uuid,
                    date: e.date,
                    ref: e.ref,
                    nom: e.nom,
                    prenoms: e.prenoms,
                    categorie: e.categorie,
                    nature: e.nature,
                    montant: parseFloat(e.montant),
                    modePaiement: e.mode_paiement,
                    isDocument: e.tiers_nom ? true : false,
                    tiersNom: e.tiers_nom,
                    montantVerseTiers: parseFloat(e.montant_verse_tiers || 0),
                    marge: parseFloat(e.montant) - parseFloat(e.montant_verse_tiers || 0),
                    detailPrestations: e.detail_prestations,
                    createdBy: e.created_by_username,
                    createdByName: e.creator?.name || e.created_by_username || 'N/A',
                    createdAt: e.created_at,
                    updatedAt: e.updated_at,
                    // Informations du payeur
                    type_payeur: e.type_payeur || 'lui_meme',
                    payeur_nom_prenom: e.payeur_nom_prenom,
                    payeur_telephone: e.payeur_telephone,
                    payeur_relation: e.payeur_relation,
                    payeur_reference_dossier: e.payeur_reference_dossier
                }));

                localStorage.setItem('ec_entries', JSON.stringify(formattedEntrees));
            }

            if (sorties.length > 0) {
                const formattedSorties = sorties.map(s => ({
                    id: s.uuid,
                    date: s.date,
                    ref: s.ref,
                    beneficiaire: s.beneficiaire,
                    motif: s.motif,
                    montant: parseFloat(s.montant),
                    modePaiement: s.mode_paiement,
                    remarques: s.remarques,
                    createdBy: s.created_by_username,
                    createdByName: s.creator?.name || s.created_by_username || 'N/A',
                    createdAt: s.created_at,
                    updatedAt: s.updated_at
                }));

                localStorage.setItem('ec_exits', JSON.stringify(formattedSorties));
            }

            return { entrees, sorties };
        } catch (error) {
            console.error('Erreur chargement depuis BDD:', error);
            return { entrees: [], sorties: [] };
        }
    };

    console.log('✅ Module de synchronisation API Caisse chargé');
})();
