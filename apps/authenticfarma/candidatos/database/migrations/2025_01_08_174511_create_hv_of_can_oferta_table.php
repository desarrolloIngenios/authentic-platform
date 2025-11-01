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
        Schema::create('hv_of_can_oferta', function (Blueprint $table) {
            $table->bigInteger('idhvofcan_oferta')->primary();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_estado')->index('fkertbwfpfgahlbrxfo6v3u45jk');
            $table->bigInteger('id_hoja_vida')->index('fk92f7y2hjw34kpy97sa1rmi96v');
            $table->bigInteger('idofoferta_laboral')->index('fkrqecualkugtkdgl9fraluwtnr');
            $table->mediumText('ai')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_of_can_oferta');
    }
};
