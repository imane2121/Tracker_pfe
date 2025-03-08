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
            $table->unsignedBigInteger('signal_id');
            $table->unsignedBigInteger('user_id'); // Creator (admin or supervisor)
            $table->string('region');
            $table->string('location');
            $table->string('image')->nullable(); // Image for the collecte
            $table->string('description')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('nbrContributors')->default(0);
            $table->integer('current_contributors')->default(0); // Track current number of volunteers
            $table->enum('status', ['planned', 'in_progress', 'completed', 'validated', 'cancelled'])->default('planned');
            $table->timestamp('starting_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes for better data management
        
            // Foreign keys
            $table->foreign('signal_id')->references('id')->on('signals')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create pivot table for collecte-contributor relationship
        Schema::create('collecte_contributor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collecte_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('joined_at')->useCurrent();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            
            $table->foreign('collecte_id')->references('id')->on('collectes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Ensure a user can't join the same collecte multiple times
            $table->unique(['collecte_id', 'user_id']);
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