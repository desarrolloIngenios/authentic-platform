<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIActivity extends Model
{
    use HasFactory;

    protected $table = 'ai_activities';

    protected $fillable = [
        'user_id',
        'activity_type',
        'candidate_id',
        'job_id',
        'input_data',
        'output_data',
        'score',
        'model_used',
        'processing_time',
        'tokens_used',
        'status',
        'error_message'
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'processing_time' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el candidato (si aplica)
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'candidate_id');
    }

    /**
     * Scopes para consultas frecuentes
     */
    public function scopeCVAnalysis($query)
    {
        return $query->where('activity_type', 'cv_analysis');
    }

    public function scopeMatching($query)
    {
        return $query->where('activity_type', 'matching');
    }

    public function scopeInterviewQuestions($query)
    {
        return $query->where('activity_type', 'interview_questions');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Métodos auxiliares
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasError(): bool
    {
        return $this->status === 'error';
    }

    public function getActivityTypeLabel(): string
    {
        $labels = [
            'cv_analysis' => 'Análisis de CV',
            'matching' => 'Matching Candidato-Puesto',
            'interview_questions' => 'Generación de Preguntas',
        ];

        return $labels[$this->activity_type] ?? $this->activity_type;
    }
}