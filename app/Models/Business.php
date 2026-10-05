<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'name',
        'business_type',
        'address',
        'business_age_months',
        'initial_capital',
        'products_services',
        'initial_condition',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'initial_capital'      => 'integer',
            'business_age_months'  => 'integer',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function businessAgeLabel(): string
    {
        if (! $this->business_age_months) {
            return '-';
        }

        $years  = intdiv($this->business_age_months, 12);
        $months = $this->business_age_months % 12;

        $parts = [];
        if ($years > 0) {
            $parts[] = "{$years} tahun";
        }
        if ($months > 0) {
            $parts[] = "{$months} bulan";
        }

        return implode(' ', $parts) ?: '-';
    }

    public function latestEvaluation()
    {
        return $this->evaluations()->latest()->first();
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, ?string $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('business_type', 'like', "%{$search}%");
        });
    }
}
