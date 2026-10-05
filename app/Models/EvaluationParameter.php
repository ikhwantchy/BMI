<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationParameter extends Model
{
    protected $fillable = [
        'code',
        'name',
        'weight',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'weight'    => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(EvaluationIndicator::class, 'parameter_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function weightPercentage(): string
    {
        return number_format($this->weight * 100, 0) . '%';
    }

    public function getWeightPercentAttribute(): float
    {
        return (float) ($this->weight * 100);
    }
}
