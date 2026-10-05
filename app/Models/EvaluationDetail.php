<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationDetail extends Model
{
    protected $fillable = [
        'evaluation_id',
        'parameter_id',
        'indicator_id',
        'numeric_value',
        'selected_value',
        'score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'numeric_value' => 'decimal:2',
            'score'         => 'decimal:2',
        ];
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function parameter(): BelongsTo
    {
        return $this->belongsTo(EvaluationParameter::class, 'parameter_id');
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(EvaluationIndicator::class, 'indicator_id');
    }
}
