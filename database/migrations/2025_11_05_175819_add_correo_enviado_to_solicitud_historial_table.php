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
        Schema::table('solicitud_historial', function (Blueprint $table) {
            $table->tinyInteger('correo_enviado')
            ->default(0)
            ->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_historial', function (Blueprint $table) {
            $table->dropColumn('correo_enviado');
        });
    }
};
