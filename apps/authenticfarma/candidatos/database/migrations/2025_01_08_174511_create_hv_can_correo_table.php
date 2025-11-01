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
        Schema::create('hv_can_correo', function (Blueprint $table) {
            $table->bigInteger('idhvcan_correo', true);
            $table->string('email')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->tinyInteger('principal');
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_candidato')->index('fkodauksntgpopw1hkrrdpolen6');
            $table->bigInteger('id_estado')->index('fkqklo0lxoqt5jaixbqt8j7e2sc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_correo');
    }
};
