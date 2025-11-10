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
        Schema::create('ai_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('activity_type'); // cv_analysis, interview_questions, matching
            $table->unsignedBigInteger('candidate_id')->nullable();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->json('input_data')->nullable(); // Datos de entrada (sin información sensible)
            $table->json('output_data')->nullable(); // Resultado de la IA
            $table->integer('score')->nullable(); // Score de matching o análisis
            $table->string('model_used')->default('gemini-1.5-flash');
            $table->float('processing_time')->nullable(); // Tiempo en segundos
            $table->integer('tokens_used')->nullable(); // Tokens consumidos
            $table->string('status')->default('completed'); // completed, error, pending
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['user_id', 'activity_type']);
            $table->index(['candidate_id', 'activity_type']);
            $table->index(['created_at', 'activity_type']);
            $table->index('status');
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_activities');
    }
};