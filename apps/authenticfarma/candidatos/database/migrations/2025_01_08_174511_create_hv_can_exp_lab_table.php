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
        Schema::create('hv_can_exp_lab', function (Blueprint $table) {
            $table->bigInteger('idhvcan_exp_laboral', true);
            $table->string('descripcion_cargo', 1000);
            $table->string('empresa', 200);
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('nombre_cargo', 200);
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_area')->index('fkf509is3xnouk3134ws1lrfp9l');
            $table->bigInteger('id_candidato')->index('fkpk239x9k4ne6i4696vuet1425');
            $table->bigInteger('id_estado')->index('fkgu6bpwk24pnfyym51hqqga2am');
            $table->bigInteger('id_pais')->index('fkelqvcf7gevuh1hp6swve6d0s9');
            $table->bigInteger('id_sector')->index('fkrfsdbn55vtkmwkdb4l5dp5nce');
            $table->bigInteger('id_tipo_cargo')->index('fk1tuskv9gqr06k3l2isjdci5ws');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_exp_lab');
    }
};
