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
        Schema::create('pass_tem', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_user');
            $table->string('password_tem');
            $table->string('password_real');
            $table->boolean('enable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pass_tem');
    }
};
