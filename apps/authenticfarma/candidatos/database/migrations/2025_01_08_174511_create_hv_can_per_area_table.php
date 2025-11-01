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
        Schema::create('hv_can_per_area', function (Blueprint $table) {
            $table->bigInteger('idhvcan_per_area', true);
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_area')->index('fkcgv07syaf864db6tv6ecxftqf');
            $table->bigInteger('id_candidato')->index('fk6ecaiiknypubmj5uyb18tyakj');
            $table->bigInteger('id_estado')->index('fkat9fes79263hhkn5v679ypl5w');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hv_can_per_area');
    }
};
