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

        Schema::table('sancionados', function (Blueprint $table) {
            $table->tinyInteger('tipo_origen')->default(1)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sancionados', function (Blueprint $table) {
            $table->tinyInteger('tipo_origen')->default(1)->nullable(false)->change();
        });
    }
};
