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
        Schema::create('waste_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Name of the waste type (e.g., Plastic, Glass)
            $table->enum('type', ['general', 'specific']);  // Whether it's a general or specific waste type
            $table->foreignId('parent_id')->nullable()->constrained('waste_types');  // Self-referencing for hierarchy
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_types');
    }
};
