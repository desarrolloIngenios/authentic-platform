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
        Schema::create('hv_can_perfil', function (Blueprint $table) {
            $table->bigInteger('idhvcan_perfil', true);
            $table->string('descripcion_perfil', 800)->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fk5fge72yupo3upn1qe46r3f9av');
            $table->bigInteger('id_estado')->index('fkipsf9y3vi1uiyeepr0npw38ru');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_perfil');
    }
};
