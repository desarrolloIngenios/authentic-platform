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
        Schema::create('of_ubicacion', function (Blueprint $table) {
            $table->bigInteger('idofoferta_ubicacion')->primary();
            $table->string('direccion')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_ciudad')->index('fkj207cg53c01gqiuw8d1exrf9b');
            $table->bigInteger('id_departamento')->index('fka2qcwxqqkafaxuoqt3pe9t3de');
            $table->bigInteger('id_estado')->index('fk684ptyrgu8obnbydxaequu0vy');
            $table->bigInteger('idofoferta_laboral')->index('fkfvo2rc4qjuna69mtivcj84lnq');
            $table->bigInteger('id_pais')->index('fk2boge4biisud8x9pwfwpigi3i');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('of_ubicacion');
    }
};
