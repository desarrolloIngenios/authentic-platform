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
        Schema::create('hv_can_per_sector', function (Blueprint $table) {
            $table->bigInteger('idhvcan_per_sector', true);
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fk30ngomy55exw52yhhteqaai1s');
            $table->bigInteger('id_estado')->index('fkqm5nj5co6dfv5jj2u4ymqkmg1');
            $table->bigInteger('id_sector')->index('fk293dsf1h23n9f06sw02dl40pc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_per_sector');
    }
};
