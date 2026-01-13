<?php

namespace App\Http\Controllers;

use App\Models\CalendrierEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CalendrierController extends Controller
{
    /**
     * Afficher la page du calendrier
     */
    public function index()
    {
        // Récupérer tous les utilisateurs pour le champ Agent responsable
        $agents = \App\Models\User::orderBy('name', 'asc')
                                  ->get(['id', 'name', 'email', 'matricule'])
                                  ->map(function($user) {
                                      return [
                                          'id' => $user->id,
                                          'name' => $user->name,
                                          'email' => $user->email,
                                          'matricule' => $user->matricule ?? ''
                                      ];
                                  });

        return view('calendrier.Calendrier', compact('agents'));
    }

    /**
     * Récupérer tous les événements
     */
    public function getEvents(Request $request)
    {
        try {
            $query = CalendrierEvent::query();

            // Filtrer par date si spécifié
            if ($request->has('date')) {
                $query->byDate($request->date);
            }

            // Filtrer par agent si spécifié
            if ($request->has('agent')) {
                $query->byAgent($request->agent);
            }

            // Filtrer par type si spécifié
            if ($request->has('eventType')) {
                $query->where('eventType', $request->eventType);
            }

            // Filtrer par recherche
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('candidateName', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $events = $query->orderBy('date', 'desc')
                           ->orderBy('time', 'desc')
                           ->get();

            return response()->json([
                'success' => true,
                'events' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des événements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouvel événement
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'candidateName' => 'required|string|max:255',
                'candidateContact' => 'nullable|string|max:255',
                'date' => 'required|date',
                'time' => 'required',
                'eventType' => 'required|string',
                'priority' => 'required|in:low,medium,high',
                'agent' => 'required|string',
                'description' => 'nullable|string',
                'alarm' => 'boolean',
                'alarmDate' => 'nullable|date',
                'alarmTime' => 'nullable',
                'alarmFrequency' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $eventData = $request->all();
            $eventData['user_id'] = Auth::id();

            $event = CalendrierEvent::create($eventData);

            return response()->json([
                'success' => true,
                'message' => 'Événement créé avec succès',
                'event' => $event
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'événement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un événement spécifique
     */
    public function show($id)
    {
        try {
            $event = CalendrierEvent::findOrFail($id);

            return response()->json([
                'success' => true,
                'event' => $event
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Événement non trouvé'
            ], 404);
        }
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, $id)
    {
        try {
            $event = CalendrierEvent::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'candidateName' => 'sometimes|required|string|max:255',
                'candidateContact' => 'nullable|string|max:255',
                'date' => 'sometimes|required|date',
                'time' => 'sometimes|required',
                'eventType' => 'sometimes|required|string',
                'priority' => 'sometimes|required|in:low,medium,high',
                'agent' => 'sometimes|required|string',
                'description' => 'nullable|string',
                'alarm' => 'boolean',
                'alarmDate' => 'nullable|date',
                'alarmTime' => 'nullable',
                'alarmFrequency' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $event->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Événement mis à jour avec succès',
                'event' => $event
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un événement
     */
    public function destroy($id)
    {
        try {
            $event = CalendrierEvent::findOrFail($id);
            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Événement supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques du calendrier
     */
    public function getStats()
    {
        try {
            $today = now()->toDateString();
            $nextWeek = now()->addDays(7)->toDateString();

            $stats = [
                'todayInterviews' => CalendrierEvent::byDate($today)->count(),
                'weekInterviews' => CalendrierEvent::whereBetween('date', [$today, $nextWeek])->count(),
                'pendingDecisions' => CalendrierEvent::where('date', '<', $today)
                                                    ->where('decision', false)
                                                    ->count(),
                'totalEvents' => CalendrierEvent::count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les événements à venir (7 prochains jours)
     */
    public function getUpcoming()
    {
        try {
            $events = CalendrierEvent::upcoming()
                                    ->where('date', '<=', now()->addDays(7)->toDateString())
                                    ->get();

            return response()->json([
                'success' => true,
                'events' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des événements à venir: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les événements avec alarme pour aujourd'hui
     */
    public function getTodayAlarms()
    {
        try {
            $today = now()->toDateString();

            $events = CalendrierEvent::withAlarm()
                                    ->where('alarmDate', $today)
                                    ->orderBy('alarmTime', 'asc')
                                    ->get();

            return response()->json([
                'success' => true,
                'events' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des alarmes: ' . $e->getMessage()
            ], 500);
        }
    }
}
