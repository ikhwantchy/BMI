<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopedByBranch;

class Member extends Model
{
    use HasFactory, SoftDeletes, ScopedByBranch;

    protected $fillable = [
        'member_number',
        'full_name',
        'address',
        'phone',
        'membership_status',
        'notes',
        'branch_id',
    ];

    protected function casts(): array
    {
        return [
            'membership_status' => MembershipStatus::class,
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('membership_status', MembershipStatus::Active);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', "%{$search}%")
              ->orWhere('member_number', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
