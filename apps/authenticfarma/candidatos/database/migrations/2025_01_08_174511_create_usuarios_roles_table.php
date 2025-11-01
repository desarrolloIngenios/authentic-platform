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
        Schema::create('usuarios_roles', function (Blueprint $table) {
            $table->bigInteger('idusuario');
            $table->bigInteger('idrole')->index('fkkrak6ikaey23jphvrmmgm189x');

            $table->unique(['idusuario', 'idrole'], 'uk4yyqbn0q98p3swt68g53y3yf4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_roles');
    }
};
