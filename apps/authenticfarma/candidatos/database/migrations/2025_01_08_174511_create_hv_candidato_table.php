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
        Schema::create('hv_candidato', function (Blueprint $table) {
            $table->bigInteger('id_candidato', true);
            $table->string('apellidos', 200)->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->dateTime('fecha_nacimiento', 6)->nullable();
            $table->string('nombres', 200)->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_estado')->index('fktc1u68cd6v33pp6mia4x3qmrh');
            $table->bigInteger('id_genero')->index('fkljc1xiruisoryu0uv5lgswotk');
            $table->bigInteger('id_tipo_documento')->index('fk8wgfkc2xpon5m9ihilgxwlshc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_candidato');
    }
};
