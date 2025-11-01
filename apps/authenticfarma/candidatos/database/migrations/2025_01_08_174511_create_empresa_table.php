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
        Schema::create('empresa', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->boolean('confidencial')->nullable();
            $table->string('descripcion')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->string('logourl')->nullable();
            $table->string('nit', 200)->nullable()->unique('uk_a344uhvvn5iuti0u46a2e8no9');
            $table->string('nombre')->nullable();
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_modificacion')->nullable();
            $table->bigInteger('id_estado')->index('fkn3i1a9gdcuxo3wuof7rq5e0eb');
            $table->bigInteger('id_pais')->index('fk9r1vbqlu5j4vkid6aya04shmt');
            $table->bigInteger('id_sector')->index('fk1pj5j171535tj5oyd3id2ft27');
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
