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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('apellido')->nullable();
            $table->string('confirmation_token', 128)->nullable();
            $table->string('email', 200)->nullable()->unique('uk_kfsp0s1tflm1cwlj8idhqsad0');
            $table->boolean('enabled')->nullable();
            $table->string('nombre')->nullable();
            $table->string('password', 61)->nullable();
            $table->string('reset_token', 128)->nullable();
            $table->string('username', 200)->nullable()->unique('uk_m2dvbwfge291euvmk6vkkocao');
            $table->bigInteger('id_empresa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
