<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\DossierClient;
use App\Models\User;

class AdminDossiersController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // TODO: Ajouter middleware de permission pour l'admin
        // $this->middleware('permission:manage_client_documents');
    }

    /**
     * Afficher la liste de tous les dossiers clients
     */
    public function index(Request $request)
    {
        $query = DossierClient::with(['user', 'uploader']);

        // Filtres
        if ($request->has('client_id') && $request->client_id) {
            $query->where('user_id', $request->client_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Statistiques
        $statistiques = [
            'total_dossiers' => DossierClient::count(),
            'dossiers_clients' => DossierClient::fromClient()->count(),
            'dossiers_admin' => DossierClient::fromAdmin()->count(),
            'dossiers_pending' => DossierClient::pending()->count(),
        ];

        $dossiers = $query->orderBy('created_at', 'desc')->paginate(20);

        // Liste des clients pour le filtre
        $clients = User::where('type_user', 'public')
            ->orderBy('name')
            ->get();

        return view('admin.dossiers-clients.index', compact('dossiers', 'statistiques', 'clients'));
    }

    /**
     * Afficher les dossiers d'un client spécifique
     */
    public function showClient($clientId)
    {
        $client = User::where('id', $clientId)
            ->where('type_user', 'public')
            ->firstOrFail();

        $dossiers = DossierClient::forUser($clientId)
            ->with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistiques pour ce client
        $statistiques = [
            'total_dossiers' => $dossiers->count(),
            'dossiers_envoyes' => $dossiers->where('type', 'client_to_admin')->count(),
            'dossiers_recus' => $dossiers->where('type', 'admin_to_client')->count(),
            'dossiers_pending' => $dossiers->where('status', 'pending')->count(),
        ];

        return view('admin.dossiers-clients.show-client', compact('client', 'dossiers', 'statistiques'));
    }

    /**
     * Formulaire d'envoi de dossiers pour un client
     */
    public function createForm($clientId)
    {
        $client = User::where('id', $clientId)
            ->where('type_user', 'public')
            ->firstOrFail();

        return view('admin.dossiers-clients.upload-form', compact('client'));
    }

    /**
     * Enregistrer un nouveau dossier pour un client
     */
    public function store(Request $request, $clientId)
    {
        $request->validate([
            'fichiers' => 'required',
            'fichiers.*' => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar',
            'description' => 'nullable|string|max:500'
        ], [
            'fichiers.required' => 'Veuillez sélectionner au moins un fichier',
            'fichiers.*.max' => 'Chaque fichier ne doit pas dépasser 10 MB',
            'fichiers.*.mimes' => 'Types de fichiers autorisés: PDF, Word, Images (JPG, PNG), ZIP, RAR'
        ]);

        $client = User::where('id', $clientId)
            ->where('type_user', 'public')
            ->firstOrFail();

        $admin = Auth::user();
        $uploadPath = 'uploads/clients/' . $client->id . '/dossiers';

        $uploadedFiles = [];

        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_admin_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

                // Stocker le fichier
                $filePath = $file->storeAs($uploadPath, $fileName, 'public');

                // Enregistrer dans la base de données
                DossierClient::create([
                    'user_id' => $client->id,
                    'uploader_id' => $admin->id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'original_name' => $originalName,
                    'file_size' => $file->getSize(),
                    'file_type' => $extension,
                    'type' => 'admin_to_client',
                    'status' => 'pending',
                    'description' => $request->description
                ]);

                $uploadedFiles[] = $fileName;
            }
        }

        return redirect()->route('admin.dossiers.client', $clientId)
            ->with('success', count($uploadedFiles) . ' fichier(s) envoyé(s) au client avec succès!');
    }

    /**
     * Télécharger un dossier
     */
    public function download($id)
    {
        $dossier = DossierClient::with('user')->findOrFail($id);

        // Marquer comme vu si c'est un document du client et qu'il est en attente
        if ($dossier->isFromClient() && $dossier->status === 'pending') {
            $dossier->markAsViewed();
        }

        // Vérifier que le fichier existe
        if (!Storage::disk('public')->exists($dossier->file_path)) {
            return redirect()->back()
                ->with('error', 'Fichier introuvable');
        }

        return Storage::disk('public')->download($dossier->file_path, $dossier->original_name);
    }

    /**
     * Supprimer un dossier
     */
    public function destroy($id)
    {
        $dossier = DossierClient::findOrFail($id);
        $clientId = $dossier->user_id;

        // Les admins peuvent supprimer uniquement les documents qu'ils ont envoyés
        if ($dossier->isFromClient()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas supprimer les documents envoyés par le client');
        }

        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($dossier->file_path)) {
            Storage::disk('public')->delete($dossier->file_path);
        }

        // Supprimer l'enregistrement de la base de données
        $dossier->delete();

        return redirect()->route('admin.dossiers.client', $clientId)
            ->with('success', 'Fichier supprimé avec succès');
    }

    /**
     * Marquer un document comme traité
     */
    public function markAsProcessed($id)
    {
        $dossier = DossierClient::findOrFail($id);

        if ($dossier->isFromClient()) {
            $dossier->markAsProcessed();
            return redirect()->back()
                ->with('success', 'Document marqué comme traité');
        }

        return redirect()->back()
            ->with('error', 'Action non autorisée');
    }

    /**
     * Rechercher des clients
     */
    public function searchClients(Request $request)
    {
        $search = $request->get('q', '');

        $clients = User::where('type_user', 'public')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('contact', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email', 'contact']);

        return response()->json($clients);
    }
}
