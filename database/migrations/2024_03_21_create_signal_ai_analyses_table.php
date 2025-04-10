<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('signal_ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signal_id')->constrained()->onDelete('cascade');
            $table->boolean('debris_detected')->default(false);
            $table->float('confidence_score')->nullable();
            $table->json('detected_waste_types')->nullable(); // Store detected waste types with confidence scores
            $table->json('media_analysis_results')->nullable(); // Store detailed analysis results for each media
            $table->boolean('matches_reporter_selection')->default(false); // Whether AI detection matches reporter's selection
            $table->text('analysis_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('signal_ai_analyses');
    }
}; 