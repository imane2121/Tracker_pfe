<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('signals', function (Blueprint $table) {
        $table->unsignedBigInteger('created_by')->nullable()->change();
    });
}

public function down()
{
    Schema::table('signals', function (Blueprint $table) {
        $table->unsignedBigInteger('created_by')->nullable(false)->change();
    });
}
};
