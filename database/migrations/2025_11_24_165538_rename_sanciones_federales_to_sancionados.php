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
            Schema::rename('sanciones_federales', 'sancionados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sancionados', function (Blueprint $table) {
            Schema::rename('sancionados', 'sanciones_federales');
        });
    }
};
