<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use App\Channels\FcmChannel;
use App\Models\CRMClient;
use App\Models\ProfilVisa;
use App\Observers\CRMClientObserver;
use App\Observers\ProfilVisaObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register FCM notification channel
        Notification::extend('fcm', function ($app) {
            return new FcmChannel($app->make(\App\Services\FcmService::class));
        });

        // ✅ Enregistrer l'Observer pour envoyer automatiquement un SMS quand le profil visa est terminé (étape 6)
        ProfilVisa::observe(ProfilVisaObserver::class);

        // ✅ SMS immédiat quand un client est créé/change vers statut Lead ou Prospect
        CRMClient::observe(CRMClientObserver::class);
    }
}
