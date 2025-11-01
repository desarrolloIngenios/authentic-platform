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
        Schema::create('hv_can_ubicacion', function (Blueprint $table) {
            $table->bigInteger('idhvcan_ubicacion', true);
            $table->string('direccion')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->tinyInteger('principal');
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fk3fn94l0kpwo7vdr8ihgekc1ln');
            $table->bigInteger('id_ciudad_nacimiento')->index('fkdinadx6w966j7bd5ij656u7nq');
            $table->bigInteger('id_ciudad_residencia')->index('fklxy371bi06vjugrxqhc75jxrc');
            $table->bigInteger('id_departamento_nacimiento')->nullable()->index('fkl1ey562ijsk696r35ba659mou');
            $table->bigInteger('id_departamento_residencia')->nullable()->index('fkaq5ccl8mv965vfnkipbav8dv3');
            $table->bigInteger('id_estado')->index('fkl4kofechiu9x3cq2igud4d485');
            $table->bigInteger('id_pais_nacimiento')->index('fkh81v060xge4yued37kayh3ylf');
            $table->bigInteger('id_pais_residencia')->index('fk115kuj0hj4n1vhwk0ffoqvyau');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_ubicacion');
    }
};
