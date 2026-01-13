<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MessagerieMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class MessagerieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la page de messagerie
     */
    public function index()
    {
        $user = Auth::user();
        
        Log::info('Messagerie: Accès utilisateur', [
            'user' => $user->name,
            'user_id' => $user->id,
            'role' => $user->getRoleNames()->first()
        ]);
        
        return view('messagerie');
    }

    /**
     * Récupérer UNIQUEMENT LES AGENTS pour la messagerie
     */
    public function getUsers()
    {
        try {
            $currentUser = Auth::user();
            
            $users = User::where('etat', 1)
                ->where('id', '!=', $currentUser->id)
                ->whereHas('roles', function($query) {
                    $query->whereIn('name', [
                        'Agent Comptoir',
                        'Agent',
                        'Commercial',
                        'Manager',
                        'Admin',
                        'Super Admin'
                    ]);
                })
                ->select('id', 'name', 'email')
                ->with('roles')
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->getRoleNames()->first() ?? 'Agent',
                        'active' => true
                    ];
                });
            
            Log::info('Messagerie getUsers (Agents uniquement):', [
                'current_user' => $currentUser->name,
                'agents_count' => $users->count()
            ]);
            
            return response()->json([
                'success' => true,
                'users' => $users,
                'current_user' => [
                    'id' => $currentUser->id,
                    'name' => $currentUser->name,
                    'role' => $currentUser->getRoleNames()->first() ?? 'User'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Messagerie getUsers: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage(),
                'users' => []
            ], 500);
        }
    }

    /**
     * Récupérer les messages depuis la BDD
     */
    public function getMessages(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Récupérer les messages des dernières 24h
            $messages = MessagerieMessage::with(['sender', 'recipient'])
                ->forUser($user->id)
                ->where('created_at', '>=', now()->subDay())
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($message) use ($user) {
                    return [
                        'id' => $message->id,
                        'from' => $message->sender->name,
                        'userId' => $message->sender_id,
                        'text' => $message->text,
                        'audio' => $message->audio,
                        'audio_duration' => $message->audio_duration,
                        'time' => $message->created_at->format('H:i'),
                        'timestamp' => $message->created_at->timestamp,
                        'recipient' => $message->recipient_id ? $message->recipient_id : 'all',
                        'type' => $message->type,
                        'is_private' => $message->is_private,
                        'read' => $message->read ?? false, // ✅ Statut de lecture
                        'read_at' => $message->read_at ? $message->read_at->timestamp : null
                    ];
                });
            
            Log::info('Messagerie getMessages:', [
                'user' => $user->name,
                'messages_count' => $messages->count()
            ]);
            
            return response()->json([
                'success' => true,
                'messages' => $messages,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->getRoleNames()->first()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Messagerie getMessages: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ CORRIGÉ : Marquer les messages comme lus
     */
    public function markAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'message_ids' => 'required|array',
                'message_ids.*' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $messageIds = $request->message_ids;
            
            // ✅ Marquer comme lus UNIQUEMENT les messages PRIVÉS destinés à cet utilisateur
            $updated = MessagerieMessage::whereIn('id', $messageIds)
                ->where('recipient_id', $user->id) // Seulement messages pour cet utilisateur
                ->where('sender_id', '!=', $user->id) // Pas ses propres messages
                ->whereIn('read', [false, 0])
                ->orWhereNull('read')
                ->whereIn('id', $messageIds) // Important de répéter pour le OR
                ->where('recipient_id', $user->id)
                ->update([
                    'read' => true,
                    'read_at' => now()
                ]);
            
            Log::info('Messages marqués comme lus:', [
                'user' => $user->name,
                'count' => $updated,
                'message_ids' => $messageIds
            ]);

            return response()->json([
                'success' => true,
                'marked_count' => $updated
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur markAsRead: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ CORRIGÉ : Obtenir le nombre de messages non lus
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $user = Auth::user();
            
            // ✅ Compter UNIQUEMENT les messages PRIVÉS non lus destinés à cet utilisateur
            $unreadByUser = MessagerieMessage::where('recipient_id', $user->id)
                ->where('sender_id', '!=', $user->id)
                ->whereIn('read', [false, 0])
                ->orWhereNull('read')
                ->where('recipient_id', $user->id) // Important de répéter pour le OR
                ->where('created_at', '>=', now()->subDay())
                ->selectRaw('sender_id, COUNT(*) as count')
                ->groupBy('sender_id')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->sender_id => $item->count];
                });
            
            $totalUnread = $unreadByUser->sum();
            
            Log::info('Compteurs non lus:', [
                'user' => $user->name,
                'total' => $totalUnread,
                'by_user' => $unreadByUser->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'total_unread' => $totalUnread,
                'unread_by_user' => $unreadByUser,
                'unread_public' => 0 // On ne gère pas les messages publics pour l'instant
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur getUnreadCount: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Envoyer un message (sauvegarde en BDD)
     */
    public function sendMessage(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'text' => 'nullable|string|max:5000',
                'audio' => 'nullable|string',
                'audio_duration' => 'nullable|integer|min:1|max:300',
                'recipient' => 'required',
                'type' => 'required|in:text,voice,system'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false, 
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validation : soit text, soit audio
            if (!$request->text && !$request->audio) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Le message doit contenir du texte ou de l\'audio'
                ], 422);
            }

            // Déterminer si c'est un message privé
            $isPrivate = $request->recipient !== 'all';
            $recipientId = $isPrivate ? $request->recipient : null;

            // Créer le message en BDD
            $message = MessagerieMessage::create([
                'sender_id' => $user->id,
                'recipient_id' => $recipientId,
                'text' => $request->text,
                'audio' => $request->audio,
                'audio_duration' => $request->audio_duration,
                'type' => $request->type,
                'is_private' => $isPrivate
            ]);

            Log::info('Message envoyé:', [
                'from' => $user->name,
                'to' => $isPrivate ? "User #{$recipientId}" : 'Tous',
                'type' => $request->type,
                'text' => $request->text ? substr($request->text, 0, 50) : null,
                'audio_duration' => $request->audio_duration
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message envoyé avec succès',
                'data' => [
                    'id' => $message->id,
                    'from' => $user->name,
                    'userId' => $user->id,
                    'text' => $message->text,
                    'audio' => $message->audio,
                    'audio_duration' => $message->audio_duration,
                    'recipient' => $request->recipient,
                    'type' => $message->type,
                    'time' => $message->created_at->format('H:i'),
                    'timestamp' => $message->created_at->timestamp,
                    'is_private' => $isPrivate
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Messagerie sendMessage: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Démarrer un appel vidéo avec invitation
     */
    public function startVideoCall(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'recipient_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $recipientId = $request->recipient_id;
            
            // Vérifier que le destinataire n'est pas l'appelant
            if ($recipientId == $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vous ne pouvez pas vous appeler vous-même'
                ], 400);
            }

            // Créer une invitation d'appel vidéo unique
            $callId = uniqid('call_', true);
            
            // Stocker l'invitation en cache (expire après 2 minutes)
            $callData = [
                'call_id' => $callId,
                'caller_id' => $user->id,
                'caller_name' => $user->name,
                'recipient_id' => $recipientId,
                'status' => 'pending',
                'created_at' => now()->timestamp
            ];
            
            Cache::put("video_call_{$callId}", $callData, now()->addMinutes(2));
            Cache::put("video_call_for_user_{$recipientId}", $callId, now()->addMinutes(2));
            
            // Envoyer un message système
            MessagerieMessage::create([
                'sender_id' => $user->id,
                'recipient_id' => $recipientId,
                'text' => "📹 {$user->name} vous invite à un appel vidéo",
                'type' => 'system',
                'is_private' => true
            ]);
            
            Log::info('Appel vidéo démarré:', [
                'call_id' => $callId,
                'caller' => $user->name,
                'recipient_id' => $recipientId
            ]);

            return response()->json([
                'success' => true,
                'call_id' => $callId,
                'message' => 'Invitation envoyée'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur startVideoCall: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Vérifier les invitations d'appel vidéo en attente
     */
    public function checkVideoCallInvitations()
    {
        try {
            $user = Auth::user();
            
            // Chercher une invitation en attente pour cet utilisateur
            $callId = Cache::get("video_call_for_user_{$user->id}");
            
            if (!$callId) {
                return response()->json([
                    'success' => true,
                    'has_invitation' => false
                ]);
            }
            
            $callData = Cache::get("video_call_{$callId}");
            
            if (!$callData || $callData['status'] !== 'pending') {
                return response()->json([
                    'success' => true,
                    'has_invitation' => false
                ]);
            }
            
            return response()->json([
                'success' => true,
                'has_invitation' => true,
                'call_data' => $callData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur checkVideoCallInvitations: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Accepter une invitation d'appel vidéo
     */
    public function acceptVideoCall(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'call_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $callId = $request->call_id;
            $callData = Cache::get("video_call_{$callId}");
            
            if (!$callData) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invitation expirée'
                ], 404);
            }
            
            // Vérifier que c'est bien le destinataire
            if ($callData['recipient_id'] != $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Non autorisé'
                ], 403);
            }
            
            // Mettre à jour le statut
            $callData['status'] = 'accepted';
            $callData['accepted_at'] = now()->timestamp;
            Cache::put("video_call_{$callId}", $callData, now()->addMinutes(30));
            
            // Supprimer l'invitation en attente
            Cache::forget("video_call_for_user_{$user->id}");
            
            // Message système
            MessagerieMessage::create([
                'sender_id' => $user->id,
                'recipient_id' => $callData['caller_id'],
                'text' => "✅ {$user->name} a accepté l'appel vidéo",
                'type' => 'system',
                'is_private' => true
            ]);
            
            Log::info('Appel vidéo accepté:', [
                'call_id' => $callId,
                'accepter' => $user->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appel accepté',
                'call_data' => $callData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur acceptVideoCall: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Refuser une invitation d'appel vidéo
     */
    public function rejectVideoCall(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'call_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $callId = $request->call_id;
            $callData = Cache::get("video_call_{$callId}");
            
            if (!$callData) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invitation expirée'
                ], 404);
            }
            
            // Vérifier que c'est bien le destinataire
            if ($callData['recipient_id'] != $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Non autorisé'
                ], 403);
            }
            
            // Supprimer l'appel
            Cache::forget("video_call_{$callId}");
            Cache::forget("video_call_for_user_{$user->id}");
            
            // Message système
            MessagerieMessage::create([
                'sender_id' => $user->id,
                'recipient_id' => $callData['caller_id'],
                'text' => "❌ {$user->name} a refusé l'appel vidéo",
                'type' => 'system',
                'is_private' => true
            ]);
            
            Log::info('Appel vidéo refusé:', [
                'call_id' => $callId,
                'refuser' => $user->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appel refusé'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur rejectVideoCall: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Démarrer un appel vidéo de groupe
     */
    public function startGroupVideoCall(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'participant_ids' => 'required|array|min:1|max:10',
                'participant_ids.*' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $participantIds = $request->participant_ids;
            
            // Vérifier qu'on ne s'appelle pas soi-même
            $participantIds = array_filter($participantIds, function($id) use ($user) {
                return $id != $user->id;
            });
            
            if (empty($participantIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun participant valide sélectionné'
                ], 400);
            }

            // Créer un ID d'appel de groupe unique
            $callId = uniqid('group_call_', true);
            
            // Récupérer les infos des participants
            $participants = User::whereIn('id', $participantIds)->get(['id', 'name']);
            
            // Créer les données de l'appel de groupe
            $callData = [
                'call_id' => $callId,
                'type' => 'group',
                'initiator_id' => $user->id,
                'initiator_name' => $user->name,
                'participant_ids' => $participantIds,
                'participants' => $participants->toArray(),
                'accepted_by' => [$user->id], // L'initiateur est automatiquement dans l'appel
                'status' => 'pending',
                'created_at' => now()->timestamp,
                'max_participants' => 6
            ];
            
            // Stocker l'appel en cache (expire après 5 minutes)
            Cache::put("video_call_{$callId}", $callData, now()->addMinutes(5));
            
            // Créer une invitation pour chaque participant
            foreach ($participantIds as $participantId) {
                Cache::put("video_call_for_user_{$participantId}", $callId, now()->addMinutes(5));
            }
            
            // Envoyer un message système
            MessagerieMessage::create([
                'sender_id' => $user->id,
                'recipient_id' => null,
                'text' => "📹 {$user->name} a démarré un appel vidéo de groupe avec " . count($participantIds) . " participant(s)",
                'type' => 'system',
                'is_private' => false
            ]);
            
            Log::info('Appel vidéo de groupe démarré:', [
                'call_id' => $callId,
                'initiator' => $user->name,
                'participants_count' => count($participantIds)
            ]);

            return response()->json([
                'success' => true,
                'call_id' => $callId,
                'call_data' => $callData,
                'message' => 'Invitations envoyées'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur startGroupVideoCall: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Accepter une invitation d'appel de groupe
     */
    public function acceptGroupVideoCall(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'call_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $callId = $request->call_id;
            $callData = Cache::get("video_call_{$callId}");
            
            if (!$callData) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invitation expirée'
                ], 404);
            }
            
            // Vérifier que l'utilisateur est bien invité
            if (!in_array($user->id, $callData['participant_ids']) && $user->id != $callData['initiator_id']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Non autorisé'
                ], 403);
            }
            
            // Vérifier la limite de participants
            if (count($callData['accepted_by']) >= $callData['max_participants']) {
                return response()->json([
                    'success' => false,
                    'error' => 'L\'appel est complet (limite de ' . $callData['max_participants'] . ' participants)'
                ], 400);
            }
            
            // Ajouter l'utilisateur aux participants acceptés
            if (!in_array($user->id, $callData['accepted_by'])) {
                $callData['accepted_by'][] = $user->id;
            }
            
            $callData['status'] = 'active';
            Cache::put("video_call_{$callId}", $callData, now()->addMinutes(60));
            
            // Supprimer l'invitation en attente
            Cache::forget("video_call_for_user_{$user->id}");
            
            // Message système
            MessagerieMessage::create([
                'sender_id' => $user->id,
                'recipient_id' => null,
                'text' => "✅ {$user->name} a rejoint l'appel de groupe",
                'type' => 'system',
                'is_private' => false
            ]);
            
            Log::info('Appel de groupe accepté:', [
                'call_id' => $callId,
                'user' => $user->name,
                'participants_count' => count($callData['accepted_by'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appel accepté',
                'call_data' => $callData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur acceptGroupVideoCall: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Quitter un appel de groupe
     */
    public function leaveGroupVideoCall(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'call_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $callId = $request->call_id;
            $callData = Cache::get("video_call_{$callId}");
            
            if ($callData) {
                // Retirer l'utilisateur des participants
                $callData['accepted_by'] = array_values(array_filter($callData['accepted_by'], function($id) use ($user) {
                    return $id != $user->id;
                }));
                
                // Si plus personne dans l'appel, le supprimer
                if (empty($callData['accepted_by'])) {
                    Cache::forget("video_call_{$callId}");
                    Cache::forget("webrtc_{$callId}");
                } else {
                    Cache::put("video_call_{$callId}", $callData, now()->addMinutes(60));
                }
                
                // Message système
                MessagerieMessage::create([
                    'sender_id' => $user->id,
                    'recipient_id' => null,
                    'text' => "👋 {$user->name} a quitté l'appel de groupe",
                    'type' => 'system',
                    'is_private' => false
                ]);
            }
            
            Log::info('Utilisateur a quitté l\'appel de groupe:', [
                'call_id' => $callId,
                'user' => $user->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vous avez quitté l\'appel'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur leaveGroupVideoCall: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Obtenir les participants actifs d'un appel
     */
    public function getCallParticipants(Request $request)
    {
        try {
            $callId = $request->call_id;
            
            if (!$callId) {
                return response()->json([
                    'success' => false,
                    'error' => 'call_id requis'
                ], 400);
            }
            
            $callData = Cache::get("video_call_{$callId}");
            
            if (!$callData) {
                return response()->json([
                    'success' => true,
                    'participants' => []
                ]);
            }
            
            // Récupérer les détails des participants actifs
            $participants = User::whereIn('id', $callData['accepted_by'])
                ->get(['id', 'name', 'email'])
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ];
                });
            
            return response()->json([
                'success' => true,
                'participants' => $participants,
                'call_data' => $callData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur getCallParticipants: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * ✅ NOUVEAU : Vérifier le statut d'un appel (pour l'initiateur)
     */
    public function checkCallStatus(Request $request)
    {
        try {
            $callId = $request->call_id;
            
            if (!$callId) {
                return response()->json([
                    'success' => false,
                    'error' => 'call_id requis'
                ], 400);
            }
            
            $callData = Cache::get("video_call_{$callId}");
            
            if (!$callData) {
                return response()->json([
                    'success' => true,
                    'status' => 'expired'
                ]);
            }
            
            // Récupérer les données WebRTC
            $webrtcData = Cache::get("webrtc_{$callId}");
            
            return response()->json([
                'success' => true,
                'status' => $callData['status'],
                'call_data' => $callData,
                'webrtc_data' => $webrtcData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur checkCallStatus: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Échanger les données WebRTC (SDP Offer/Answer)
     */
    public function exchangeWebRTC(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validator = Validator::make($request->all(), [
                'call_id' => 'required|string',
                'type' => 'required|in:offer,answer,ice-candidate',
                'data' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $callId = $request->call_id;
            $type = $request->type;
            $data = $request->data;
            
            // Vérifier que l'appel existe
            $callData = Cache::get("video_call_{$callId}");
            if (!$callData) {
                return response()->json([
                    'success' => false,
                    'error' => 'Appel non trouvé'
                ], 404);
            }
            
            // Stocker les données WebRTC
            $webrtcKey = "webrtc_{$callId}_{$type}_{$user->id}";
            Cache::put($webrtcKey, [
                'user_id' => $user->id,
                'type' => $type,
                'data' => $data,
                'timestamp' => now()->timestamp
            ], now()->addMinutes(30));
            
            // Aussi stocker dans un cache global pour cet appel
            $allWebrtcData = Cache::get("webrtc_{$callId}", []);
            $allWebrtcData[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'type' => $type,
                'data' => $data,
                'timestamp' => now()->timestamp
            ];
            Cache::put("webrtc_{$callId}", $allWebrtcData, now()->addMinutes(30));
            
            Log::info('WebRTC data exchanged:', [
                'call_id' => $callId,
                'user' => $user->name,
                'type' => $type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Données WebRTC échangées'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur exchangeWebRTC: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Récupérer les données WebRTC de l'autre participant
     */
    public function getWebRTCData(Request $request)
    {
        try {
            $user = Auth::user();
            $callId = $request->call_id;
            
            if (!$callId) {
                return response()->json([
                    'success' => false,
                    'error' => 'call_id requis'
                ], 400);
            }
            
            // Récupérer toutes les données WebRTC pour cet appel
            $webrtcData = Cache::get("webrtc_{$callId}", []);
            
            // Filtrer pour obtenir seulement les données de l'autre utilisateur
            $otherUserData = array_filter($webrtcData, function($item) use ($user) {
                return $item['user_id'] !== $user->id;
            });
            
            return response()->json([
                'success' => true,
                'data' => array_values($otherUserData)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur getWebRTCData: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtenir les statistiques de la messagerie
     */
    public function getStats()
    {
        try {
            $user = Auth::user();
            
            $totalAgents = User::where('etat', 1)
                ->whereHas('roles', function($query) {
                    $query->whereIn('name', [
                        'Agent Comptoir',
                        'Agent',
                        'Commercial',
                        'Manager'
                    ]);
                })
                ->count();
            
            $todayMessages = MessagerieMessage::whereDate('created_at', today())->count();
            $totalMessages = MessagerieMessage::count();
            
            $stats = [
                'total_users' => $totalAgents,
                'active_users' => $totalAgents,
                'total_messages' => $totalMessages,
                'today_messages' => $todayMessages
            ];
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Messagerie getStats: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}