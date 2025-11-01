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
        Schema::create('hv_can_form_ac', function (Blueprint $table) {
            $table->bigInteger('idhvcan_form_ac', true);
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('institucion')->nullable();
            $table->string('titulo')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fk4cx1mh5j3fxsdwthphgq3livr');
            $table->bigInteger('id_estado')->index('fkc2ps4k4u64ew5lfh2vqsuueqr');
            $table->bigInteger('id_nivel_educacion')->index('fksf0ijo0ntxf2ulco6a5k512a2');
            $table->bigInteger('id_pais')->index('fkcgemwuvp083e5x7hn6rfu69xq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_form_ac');
    }
};
