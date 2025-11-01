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
        Schema::create('hv_hoja_vida', function (Blueprint $table) {
            $table->bigInteger('id_hoja_vida', true);
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->longText('foto')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->nullable()->index('fkduty2q37jq402qkj2erjokvq0');
            $table->bigInteger('id_estado')->index('fk928au7idepghmh1yxdylqu8by');
            $table->bigInteger('id_usuario')->index('fk885qrif6uv8bvh6lcwm11mku4');
            $table->bigInteger('id_empresa')->nullable();
            $table->mediumText('json_skills');
            $table->mediumText('json_profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_hoja_vida');
    }
};
