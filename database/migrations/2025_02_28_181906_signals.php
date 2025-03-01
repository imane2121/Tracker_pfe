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
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('volume')->default(0);
            $table->json('wasteTypes');
            $table->string('location');
            $table->string('customType');
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->boolean('anomalyFlag')->default(false);
            $table->timestamp('signalDate')->useCurrent();
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
           // $table->string('manual_location')->nullable();
            $table->timestamps();
        });
        Schema::create('signal_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signal_id')->constrained('signals')->onDelete('cascade');
            $table->string('media_type'); // photo/video
            $table->string('file_path'); // file location on the server
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signal_media');
        Schema::dropIfExists('signals');
    }
};
