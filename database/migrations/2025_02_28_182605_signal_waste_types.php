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
        Schema::create('signal_waste_types', function (Blueprint $table) {
            $table->unsignedBigInteger('signal_id');
            $table->unsignedBigInteger('waste_type_id');
            
            // Primary key
            $table->primary(['signal_id', 'waste_type_id']);
            
            // Foreign keys
            $table->foreign('signal_id')->references('id')->on('signals')->onDelete('cascade');
            $table->foreign('waste_type_id')->references('id')->on('waste_types')->onDelete('cascade');
        });
            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signal_waste_types');
    }
};
