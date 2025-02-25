<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name')->unique(); // City name
            $table->string('region')->nullable(); // Optional region (e.g., Casablanca-Settat)
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
