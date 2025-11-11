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
    Schema::create('sanciones_federales', function (Blueprint $table) {
        $table->id();
        $table->string('dependencia')->nullable();
        $table->string('rfc')->nullable();
        $table->string('homo')->nullable();
        $table->string('apellido_paterno')->nullable();
        $table->string('apellido_materno')->nullable();
        $table->string('nombre')->nullable();
        $table->string('autoridad_sancionadora')->nullable();
        $table->string('puesto')->nullable();
        $table->integer('periodo')->nullable(); // años
        $table->date('fecha_resolucion')->nullable();
        $table->date('fecha_notificacion')->nullable();
        $table->date('fecha_inicio')->nullable();
        $table->date('fecha_fin')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanciones_federales');
    }
};
