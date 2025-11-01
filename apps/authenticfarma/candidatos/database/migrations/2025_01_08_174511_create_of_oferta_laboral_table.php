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
        Schema::create('of_oferta_laboral', function (Blueprint $table) {
            $table->bigInteger('idofoferta_laboral')->primary();
            $table->mediumText('descripcion')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('titulo')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_area')->nullable()->index('fkr8iny23psb14lo60meae3ic50');
            $table->bigInteger('id_empresa')->index('fklt0x50hgsxhp1ytmq5awdxr89');
            $table->bigInteger('id_estado')->index('fk9w0m0uh3olo95mt04ch6cnrn2');
            $table->boolean('is_confidencial')->default(false);
            $table->integer('numero_vacantes')->default(1);
            $table->integer('id_cargo')->nullable();
            $table->integer('id_idioma')->nullable();
            $table->integer('id_nivel_idioma')->nullable();
            $table->integer('id_sector')->nullable();
            $table->integer('id_nivel_educacion')->nullable();
            $table->integer('id_ciudad')->nullable();
            $table->integer('id_tiempo_experiencia')->nullable();
            $table->integer('sector_porcentaje')->nullable()->default(0);
            $table->integer('area_porcentaje')->nullable()->default(0);
            $table->integer('cargo_porcentaje')->nullable()->default(0);
            $table->integer('idioma_porcentaje')->nullable()->default(0);
            $table->integer('experiencia_porcentaje')->nullable()->default(0);
            $table->integer('educacion_porcentaje')->nullable()->default(0);
            $table->unsignedBigInteger('id_rango_salario')->nullable();
            $table->timestamp('fecha_cierre_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('of_oferta_laboral');
    }
};
