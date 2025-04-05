<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rapports', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->foreignId('collecte_id')->constrained()->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
            $table->text('description');
            
            // Collection Details
            $table->decimal('volume', 10, 2);  // Same precision as actual_volume in collectes
            $table->json('waste_types');       // Store the actual waste types found
            $table->json('participants');      // Store array of participant IDs who attended
            $table->integer('nbrContributors'); // Total number of contributors who attended
            
            // Location Information
            $table->string('location');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            
            // Dates
            $table->timestamp('starting_date');
            $table->timestamp('end_date');
            
            // Metadata
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rapports');
    }
};