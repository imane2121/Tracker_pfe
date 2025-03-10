<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('collecte_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collecte_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('media_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('collecte_media');
    }
}; 