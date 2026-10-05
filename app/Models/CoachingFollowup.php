<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopedByBranch;

class CoachingFollowup extends Model
{
    use ScopedByBranch, SoftDeletes;
    protected $fillable = [
        'evaluation_id',
        'recommendation_id',
        'followup_date',
        'activity',
        'result',
        'notes',
        'officer_id',
        'branch_id',
    ];

    protected function casts(): array
    {
        return [
            'followup_date' => 'date',
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

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(CoachingRecommendation::class, 'recommendation_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}
