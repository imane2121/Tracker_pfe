<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->boolean('viewed')->default(false);
        });
    }

    public function down()
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->dropColumn('viewed');
        });
    }
}; 