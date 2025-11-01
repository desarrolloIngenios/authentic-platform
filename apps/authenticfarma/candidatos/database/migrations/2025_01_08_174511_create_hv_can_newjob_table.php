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
        Schema::create('hv_can_newjob', function (Blueprint $table) {
            $table->bigInteger('idhvcan_new_job', true);
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('nombre_cargo')->nullable();
            $table->boolean('pregunta1')->nullable();
            $table->boolean('pregunta2')->nullable();
            $table->boolean('pregunta3')->nullable();
            $table->string('texto1', 800)->nullable();
            $table->string('texto2', 800)->nullable();
            $table->string('texto3', 800)->nullable();
            $table->string('texto4', 800)->nullable();
            $table->string('texto5', 800)->nullable();
            $table->string('texto6', 800)->nullable();
            $table->string('texto7', 800)->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fkh4px02fuicdwqfmksx489g4nc');
            $table->bigInteger('id_estado')->index('fk36nwsbkgp8nx1rjx75fiq6hch');
            $table->bigInteger('id_rango_salario')->index('fkf1n64eyc6oj30t5qqcokoe0nb');
            $table->bigInteger('id_tipo_trabajo')->index('fk2vu8c4t3h9th2sv2kven7ucc7');
            $table->boolean('is_buscando_ofertas')->nullable()->default(true);
            $table->boolean('is_visible_reclutadores')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_newjob');
    }
};
