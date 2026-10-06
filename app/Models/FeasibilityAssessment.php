<?php

namespace App\Models;

use App\Traits\ScopedByBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeasibilityAssessment extends Model
{
    use HasFactory, SoftDeletes, ScopedByBranch;

    protected $fillable = [
        'member_id',
        'branch_id',
        'user_id',
        'assessment_number',
        'assessment_date',
        // A. Data Anggota
        'nik',
        'birth_place_date',
        'marital_status',
        'education',
        'rt_rw',
        'village',
        'district',
        // B. Data Pasangan
        'spouse_name',
        'spouse_nik',
        'spouse_occupation',
        'spouse_income',
        'spouse_phone',
        // C. Data Keluarga & Rumah
        'dependents_count',
        'schooling_children_count',
        'home_ownership_status',
        'wall_type',
        'floor_type',
        'roof_type',
        'water_source',
        'electricity_power',
        // D. Aset Rumah Tangga
        'land_home_assets',
        'vehicle_assets',
        'electronic_assets',
        'savings_gold_assets',
        // E. Karakter & Sosial
        'community_relation',
        'rembug_pusat_activity',
        'reputation_character',
        // F. Kesimpulan
        'result',
        'notes',
        'status',
        'validated_by',
        'validated_at',
        'validator_notes',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date'          => 'date',
            'validated_at'             => 'datetime',
            'spouse_income'            => 'integer',
            'dependents_count'         => 'integer',
            'schooling_children_count' => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DocumentHistory::class, 'reference_id')
            ->where('document_type', 'feasibility_assessment');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function resultLabel(): string
    {
        return match ($this->result) {
            'memenuhi_syarat'        => 'Memenuhi Syarat (Layak)',
            'perlu_pertimbangan'     => 'Perlu Pertimbangan',
            'tidak_memenuhi_syarat'  => 'Tidak Memenuhi Syarat',
            default                  => ucfirst(str_replace('_', ' ', $this->result ?? '-')),
        };
    }

    public function resultBadgeColor(): string
    {
        return match ($this->result) {
            'memenuhi_syarat'        => 'emerald',
            'perlu_pertimbangan'     => 'amber',
            'tidak_memenuhi_syarat'  => 'rose',
            default                  => 'gray',
        };
    }
}
