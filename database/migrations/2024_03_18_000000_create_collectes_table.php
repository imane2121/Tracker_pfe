<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('collectes', function (Blueprint $table) {
            $table->id();
            $table->json('signal_ids')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('region');
            $table->string('location');
            $table->string('image')->nullable(); // Image for the collecte
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('nbrContributors')->default(0);
            $table->integer('current_contributors')->default(0); // Track current number of volunteers
            $table->enum('status', ['planned', 'in_progress', 'completed', 'validated', 'cancelled'])->default('planned');
            $table->timestamp('starting_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes for better data management
        });

        // Create pivot table for collecte-contributor relationship
        Schema::create('collecte_contributor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collecte_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collecte_contributor');
        Schema::dropIfExists('collectes');
    }
}; 