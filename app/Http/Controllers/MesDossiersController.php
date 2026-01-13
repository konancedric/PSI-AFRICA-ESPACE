<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\DossierClient;

class MesDossiersController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la page d'upload de dossiers
     */
    public function index()
    {
        $user = Auth::user();

        // Récupérer tous les dossiers du client depuis la base de données
        // (envoyés par le client ET reçus de l'admin)
        $dossiers = DossierClient::forUser($user->id)
            ->with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();

        // Préparer les fichiers pour la vue
        $files = $dossiers->map(function($dossier) {
            return [
                'id' => $dossier->id,
                'name' => $dossier->original_name,
                'path' => $dossier->file_path,
                'size' => $dossier->file_size,
                'formatted_size' => $dossier->formatted_size,
                'date' => $dossier->created_at->format('d/m/Y H:i'),
                'type' => $dossier->type,
                'status' => $dossier->status,
                'status_badge' => $dossier->status_badge,
                'file_icon' => $dossier->file_icon,
                'uploader' => $dossier->uploader->name,
                'description' => $dossier->description,
                'is_from_admin' => $dossier->isFromAdmin(),
                'is_from_client' => $dossier->isFromClient(),
            ];
        });

        return view('clients.mes-dossiers', compact('files'));
    }

    /**
     * Upload un nouveau dossier
     */
    public function upload(Request $request)
    {
        $request->validate([
            'fichiers' => 'required',
            'fichiers.*' => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar', // Max 10MB
            'description' => 'nullable|string|max:500'
        ], [
            'fichiers.required' => 'Veuillez sélectionner au moins un fichier',
            'fichiers.*.max' => 'Chaque fichier ne doit pas dépasser 10 MB',
            'fichiers.*.mimes' => 'Types de fichiers autorisés: PDF, Word, Images (JPG, PNG), ZIP, RAR'
        ]);

        $user = Auth::user();
        $uploadPath = 'uploads/clients/' . $user->id . '/dossiers';

        $uploadedFiles = [];

        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

                // Stocker le fichier
                $filePath = $file->storeAs($uploadPath, $fileName, 'public');

                // Enregistrer dans la base de données
                DossierClient::create([
                    'user_id' => $user->id,
                    'uploader_id' => $user->id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'original_name' => $originalName,
                    'file_size' => $file->getSize(),
                    'file_type' => $extension,
                    'type' => 'client_to_admin',
                    'status' => 'pending',
                    'description' => $request->description
                ]);

                $uploadedFiles[] = $fileName;
            }
        }

        return redirect()->route('mes-dossiers')
            ->with('success', count($uploadedFiles) . ' fichier(s) uploadé(s) avec succès!');
    }

    /**
     * Télécharger un fichier
     */
    public function download($id)
    {
        $user = Auth::user();

        // Récupérer le dossier depuis la base de données
        $dossier = DossierClient::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Marquer comme vu si c'est un document de l'admin et qu'il est en attente
        if ($dossier->isFromAdmin() && $dossier->status === 'pending') {
            $dossier->markAsViewed();
        }

        // Vérifier que le fichier existe
        if (!Storage::disk('public')->exists($dossier->file_path)) {
            return redirect()->route('mes-dossiers')
                ->with('error', 'Fichier introuvable');
        }

        return Storage::disk('public')->download($dossier->file_path, $dossier->original_name);
    }

    /**
     * Supprimer un fichier
     */
    public function delete($id)
    {
        $user = Auth::user();

        // Récupérer le dossier depuis la base de données
        $dossier = DossierClient::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Vérifier que c'est bien un document envoyé par le client
        // Les clients ne peuvent pas supprimer les documents envoyés par l'admin
        if ($dossier->isFromAdmin()) {
            return redirect()->route('mes-dossiers')
                ->with('error', 'Vous ne pouvez pas supprimer les documents envoyés par l\'administration');
        }

        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($dossier->file_path)) {
            Storage::disk('public')->delete($dossier->file_path);
        }

        // Supprimer l'enregistrement de la base de données
        $dossier->delete();

        return redirect()->route('mes-dossiers')
            ->with('success', 'Fichier supprimé avec succès');
    }
}
