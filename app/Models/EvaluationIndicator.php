<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationIndicator extends Model
{
    protected $fillable = [
        'parameter_id',
        'code',
        'name',
        'description',
        'scoring_type',
        'scoring_config',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'scoring_config' => 'array',
            'is_active'      => 'boolean',
        ];
    }

    public function parameter(): BelongsTo
    {
        return $this->belongsTo(EvaluationParameter::class, 'parameter_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
