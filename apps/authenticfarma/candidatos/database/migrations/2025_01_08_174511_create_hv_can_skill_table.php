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
        Schema::create('hv_can_skill', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('descripcion_skill')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fkjynase9b6hvka53hm8rtms474');
            $table->bigInteger('id_estado')->index('fk6mwik4epll4vgoynw7ywbput');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_skill');
    }
};
