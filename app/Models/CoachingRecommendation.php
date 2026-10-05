<?php

namespace App\Models;

use App\Enums\CoachingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopedByBranch;

class CoachingRecommendation extends Model
{
    use ScopedByBranch, SoftDeletes;
    protected $fillable = [
        'evaluation_id',
        'category',
        'title',
        'description',
        'reason',
        'status',
        'created_by',
        'validated_by',
        'validated_at',
        'branch_id',
    ];

    protected function casts(): array
    {
        return [
            'status'       => CoachingStatus::class,
            'validated_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function followups(): HasMany
    {
        return $this->hasMany(CoachingFollowup::class, 'recommendation_id');
    }
}
