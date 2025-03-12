<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collectes', function (Blueprint $table) {
            $table->json('actual_waste_types')->nullable();
            $table->decimal('actual_volume', 10, 2)->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->text('completion_notes')->nullable();
            $table->json('attendance_data')->nullable();
            $table->boolean('report_generated')->default(false);
            $table->string('report_path')->nullable();
        });

        Schema::table('collecte_contributor', function (Blueprint $table) {
            $table->boolean('attended')->nullable();
            $table->text('attendance_notes')->nullable();
        });
    }

    public function down()
    {
        Schema::table('collectes', function (Blueprint $table) {
            $table->dropColumn([
                'actual_waste_types',
                'actual_volume',
                'completion_date',
                'completion_notes',
                'attendance_data',
                'report_generated',
                'report_path'
            ]);
        });

        Schema::table('collecte_contributor', function (Blueprint $table) {
            $table->dropColumn(['attended', 'attendance_notes']);
        });
    }
}; 