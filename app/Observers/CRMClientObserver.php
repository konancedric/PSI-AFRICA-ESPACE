<?php

namespace App\Observers;

use App\Console\Commands\SendRelancesHebdomadaires;
use App\Models\CRMClient;
use App\Models\SmsLog;
use App\Models\SmsRelanceAuto;
use App\Services\OrangeSmsService;
use Illuminate\Support\Facades\Log;

class CRMClientObserver
{
    /**
     * Quand un client est créé avec un statut connu → SMS immédiat
     */
    public function created(CRMClient $client): void
    {
        $this->sendImmediateRelance($client);
    }

    /**
     * Quand le statut d'un client change → SMS immédiat
     */
    public function updated(CRMClient $client): void
    {
        if ($client->isDirty('statut')) {
            $this->sendImmediateRelance($client);
        }
    }

    /**
     * Envoyer le premier SMS de relance immédiatement
     */
    private function sendImmediateRelance(CRMClient $client): void
    {
        $phone = SendRelancesHebdomadaires::extractFirstPhone($client->contact ?? '');
        if (empty($phone)) return;

        $statut   = $client->statut;
        $messages = SendRelancesHebdomadaires::$messagesParStatut[$statut] ?? [];
        if (empty($messages)) return;

        try {
            $smsService = app(OrangeSmsService::class);

            // Vérifier si le numéro est compatible Orange/MTN CI
            if (!$smsService->isOrangeOrMtnNumber($phone)) return;

            $relance = SmsRelanceAuto::firstOrCreate(
                ['client_id' => $client->id, 'statut' => $statut],
                ['message_index' => 0, 'status_changed_at' => now(), 'active' => true, 'total_sent' => 0]
            );

            // Ne pas envoyer si un SMS a déjà été envoyé récemment pour ce statut
            if (!$relance->wasRecentlyCreated && !is_null($relance->last_sent_at)) {
                return;
            }

            $nom       = trim($client->nom ?? '');
            $prenom    = trim(explode(' ', trim($client->prenoms ?? ''))[0]);
            $salutation = trim('M/Mme ' . $nom . ($prenom ? ' ' . $prenom : ''));
            if (empty(trim($nom . $prenom))) $salutation = 'Client';

            $index   = $relance->message_index % count($messages);
            $message = str_replace('[Prénom]', $salutation, $messages[$index]);

            $result = $smsService->sendSms($phone, $message);

            SmsLog::create([
                'sent_by'         => null,
                'recipient_name'  => $salutation,
                'recipient_phone' => $phone,
                'message'         => $message,
                'status'          => $result['success'] ? 'sent' : 'failed',
                'error_message'   => $result['success'] ? null : ($result['message'] ?? 'Erreur inconnue'),
                'api_response'    => isset($result['data']) ? json_encode($result['data']) : null,
                'sent_at'         => $result['success'] ? now() : null,
            ]);

            if ($result['success']) {
                $relance->update([
                    'message_index' => ($index + 1) % count($messages),
                    'last_sent_at'  => now(),
                    'total_sent'    => ($relance->total_sent ?? 0) + 1,
                ]);
                Log::info("Relance immédiate envoyée ({$statut})", ['client_id' => $client->id, 'phone' => $phone]);
            } else {
                Log::warning("Relance immédiate échouée ({$statut})", ['client_id' => $client->id, 'phone' => $phone]);
            }
        } catch (\Exception $e) {
            Log::error("Erreur relance immédiate CRM", ['client_id' => $client->id, 'error' => $e->getMessage()]);
        }
    }
}
