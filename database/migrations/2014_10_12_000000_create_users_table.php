<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Shared attributes
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('verified')->default(0)->nullable();
            $table->string('verification_token')->nullable();
            $table->string('profile_picture')->nullable();
            $table->enum('role', ['admin', 'contributor', 'supervisor'])->default('contributor')->index();
            $table->rememberToken();
            $table->timestamps();

            // Supervisor-specific attributes
            $table->enum('account_status', ['active', 'inactive', 'under_review'])->nullable();
            $table->string('CNI')->unique()->nullable();
            $table->string('city')->nullable();
            $table->enum('organisation', [
                'WWF (World Wide Fund for Nature)',
                'Greenpeace',
                'The Ocean Cleanup',
                'Associations locales pour la protection des plages et de l’environnement',
                'Programme des Nations Unies pour l’Environnement (PNUE)'
            ])->nullable();
            $table->enum('region', [
                'Tanger-Tétouan-Al Hoceïma',
                'Oriental',
                'Fès-Meknès',
                'Rabat-Salé-Kénitra',
                'Béni Mellal-Khénifra',
                'Casablanca-Settat',
                'Marrakech-Safi',
                'Drâa-Tafilalet',
                'Souss-Massa',
                'Guelmim-Oued Noun',
                'Laâyoune-Sakia El Hamra',
                'Dakhla-Oued Ed-Dahab'
            ])->nullable();
            $table->string('organisation_id_card_recto')->nullable();
            $table->string('organisation_id_card_verso')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('cascade');

            // Contributor-specific attributes
            $table->string('phone_number')->nullable();
            $table->string('username')->unique()->nullable();
            $table->decimal('credibility_score', 5, 2)->default(0.00)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};