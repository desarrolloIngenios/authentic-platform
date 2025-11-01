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
        Schema::create('hv_can_idioma', function (Blueprint $table) {
            $table->bigInteger('idhvcan_idioma', true);
            $table->boolean('certificado')->nullable();
            $table->string('detalle', 1000)->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fk6dwt37sywt2cvk8bieujj144d');
            $table->bigInteger('id_estado')->index('fk2ai6b07g86hkxwhjnu021ib65');
            $table->bigInteger('id_idioma')->index('fk873vix9ac52f214d37rmlnkxo');
            $table->bigInteger('id_nivel_idioma')->index('fkcm0vqfgiegjnyu5ig0a2x1d1x');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_idioma');
    }
};
