<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Table des clients CRM
        Schema::create('crm_clients', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('nom');
            $table->string('prenoms')->nullable();
            $table->string('contact');
            $table->string('email')->nullable();
            $table->string('media')->nullable();
            $table->string('prestation');
            $table->decimal('budget', 15, 2)->default(0);
            $table->enum('statut', ['Lead', 'Prospect', 'Opportunité', 'Négociation', 'Converti', 'Perdu'])->default('Lead');
            $table->string('agent');
            $table->date('date_creation');
            $table->text('commentaire')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['statut', 'agent']);
            $table->index('date_creation');
        });

        // Table des factures CRM
        Schema::create('crm_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('cascade');
            $table->string('client_name');
            $table->string('service');
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('agent');
            $table->text('notes')->nullable();
            $table->integer('reminders_count')->default(0);
            $table->timestamp('last_reminder_at')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'due_date']);
            $table->index('client_id');
        });

        // Table des paiements
        Schema::create('crm_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('crm_invoices')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Table des activités
        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->text('details');
            $table->string('user_name');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('created_at');
        });

        // Table des logs de rappels
        Schema::create('crm_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('crm_invoices')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('crm_clients')->onDelete('cascade');
            $table->enum('channel', ['whatsapp', 'email', 'sms']);
            $table->string('tone');
            $table->string('scenario');
            $table->text('message_rendered');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Table des templates de rappels
        Schema::create('crm_reminder_templates', function (Blueprint $table) {
            $table->id();
            $table->string('channel');
            $table->string('tone');
            $table->string('scenario');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('crm_reminder_logs');
        Schema::dropIfExists('crm_reminder_templates');
        Schema::dropIfExists('crm_payments');
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_invoices');
        Schema::dropIfExists('crm_clients');
    }
};