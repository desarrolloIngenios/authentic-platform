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
        Schema::create('hv_can_form_ad', function (Blueprint $table) {
            $table->bigInteger('idhvcan_form_ad', true);
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('institucion')->nullable();
            $table->string('titulo')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fk5iviufjoh2qvj378fb9eoid2j');
            $table->bigInteger('id_estado')->index('fks6n30gdownqu6itec9mw23ykc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_form_ad');
    }
};
