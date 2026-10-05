<?php

namespace App\Models;

use App\Enums\VisitStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'officer_id',
        'visit_date',
        'evaluation_period',
        'status',
        'field_notes',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'status'     => VisitStatus::class,
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VisitDocument::class);
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeForOfficer($query, int $officerId)
    {
        return $query->where('officer_id', $officerId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', today());
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [VisitStatus::Scheduled, VisitStatus::InProgress]);
    }
}
