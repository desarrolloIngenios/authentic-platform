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
        Schema::create('hv_can_telefono', function (Blueprint $table) {
            $table->bigInteger('idhvcan_telefono', true);
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('numero_telefono')->nullable();
            $table->string('otro_numero_telefono')->nullable();
            $table->tinyInteger('principal');
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fk54fo5woha0ocunf99gbplv0ry');
            $table->bigInteger('id_estado')->index('fk5kd2tisvm2pfm6ircany367vh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_telefono');
    }
};
