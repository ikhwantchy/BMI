<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use App\Enums\RecommendationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'business_id',
        'status',
        'total_score',
        'recommendation',
        'recommendation_reason',
        'submitted_by',
        'validated_by',
        'submitted_at',
        'validated_at',
        'validator_notes',
    ];

    protected function casts(): array
    {
        return [
            'status'         => EvaluationStatus::class,
            'recommendation' => RecommendationStatus::class,
            'total_score'    => 'decimal:2',
            'submitted_at'   => 'datetime',
            'validated_at'   => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(EvaluationDetail::class);
    }

    public function coachingRecommendations(): HasMany
    {
        return $this->hasMany(CoachingRecommendation::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(CoachingFollowup::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeWaitingValidation($query)
    {
        return $query->where('status', EvaluationStatus::WaitingValidation);
    }

    public function scopeValidated($query)
    {
        return $query->where('status', EvaluationStatus::Validated);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isDraft(): bool
    {
        return $this->status === EvaluationStatus::Draft;
    }

    public function isValidated(): bool
    {
        return $this->status === EvaluationStatus::Validated;
    }

    public function getFinalScoreAttribute(): float
    {
        return (float) ($this->total_score ?? 0);
    }
}
