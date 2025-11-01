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
        Schema::create('of_requerimiento', function (Blueprint $table) {
            $table->bigInteger('idofoferta_requerimiento')->primary();
            $table->integer('cantidad_vacantes')->nullable();
            $table->integer('experienciaanios')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_cargo')->index('fkqha8tu372yqc4v1a70eydhmsq');
            $table->bigInteger('id_ciudad')->index('fkthwjkcrkkn2og4atpgjgvyns3');
            $table->bigInteger('id_departamento')->index('fkc35e3svlm91jr1x6jssd2dwhw');
            $table->bigInteger('id_estado')->index('fki27kih5ju6jwcpy1a7c81rgkr');
            $table->bigInteger('id_horario_tipo_constrato')->index('fke4e9y8m1dx287k4rrxkr6sngi');
            $table->bigInteger('id_nivel_educacion')->index('fkdwo1xiqt2ak70htr2n5l56fe');
            $table->bigInteger('idofoferta_laboral')->index('fk3urxofwhb52relxtdsairbowh');
            $table->bigInteger('id_pais')->index('fkm1byu2h4sux1jsbhydih8qfw5');
            $table->bigInteger('id_rango_salario')->index('fkfyhmw362qrf24kfrr1gts7wy7');
            $table->bigInteger('id_sector')->index('fkolo940k3f8o43wwlq9ka6raos');
            $table->bigInteger('id_tipo_trabajo')->index('fketwiwh1shf5msc88s1jm46tw2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('of_requerimiento');
    }
};
