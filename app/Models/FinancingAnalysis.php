<?php

namespace App\Models;

use App\Traits\ScopedByBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancingAnalysis extends Model
{
    use HasFactory, SoftDeletes, ScopedByBranch;

    protected $fillable = [
        'member_id',
        'business_id',
        'branch_id',
        'user_id',
        'analysis_number',
        'analysis_date',
        // A. Data Anggota
        'spouse_name',
        'rembug_pusat',
        'registration_year',
        // B. Pembiayaan
        'proposed_amount',
        'financing_purpose',
        'last_financing_amount',
        'approved_ceiling',
        'investment_ceiling',
        'financing_other_institution',
        // C. Kapasitas Usaha
        'business_type',
        'business_start_year',
        'monthly_turnover',
        'daily_turnover',
        'workforce_count',
        'net_business_income',
        // D. Aset dan Simpanan
        'business_assets_estimate',
        'savings_amount',
        'electronic_assets_estimate',
        'vehicle_assets_estimate',
        'total_assets_estimate',
        // E. Keuangan
        'business_income',
        'spouse_income',
        'total_income',
        'per_capita_income',
        'household_expenses',
        'business_expenses',
        'other_installments',
        'total_expenses',
        // F. Kemampuan Mengangsur
        'saving_capacity',
        'installment_capacity',
        'conclusion',
        'notes',
        // Validasi
        'status',
        'validated_by',
        'validated_at',
        'validator_notes',
    ];

    protected function casts(): array
    {
        return [
            'analysis_date'               => 'date',
            'validated_at'                => 'datetime',
            'proposed_amount'             => 'integer',
            'last_financing_amount'       => 'integer',
            'approved_ceiling'            => 'integer',
            'investment_ceiling'          => 'integer',
            'financing_other_institution' => 'integer',
            'monthly_turnover'            => 'integer',
            'daily_turnover'              => 'integer',
            'net_business_income'         => 'integer',
            'business_assets_estimate'    => 'integer',
            'savings_amount'              => 'integer',
            'electronic_assets_estimate'  => 'integer',
            'vehicle_assets_estimate'     => 'integer',
            'total_assets_estimate'       => 'integer',
            'business_income'             => 'integer',
            'spouse_income'               => 'integer',
            'total_income'                => 'integer',
            'per_capita_income'           => 'integer',
            'household_expenses'          => 'integer',
            'business_expenses'           => 'integer',
            'other_installments'          => 'integer',
            'total_expenses'              => 'integer',
            'saving_capacity'             => 'integer',
            'installment_capacity'        => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function analyst(): BelongsTo
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
            ->where('document_type', 'financing_analysis');
    }

    // ─── Calculated Fields Helper ─────────────────────────────────────────────

    public function calculateFinancials(): void
    {
        $this->total_income = (int) $this->business_income + (int) $this->spouse_income;

        $familyCount = ($this->member?->dependents_count ?? 0) + 1;
        $this->per_capita_income = $familyCount > 0 ? (int) round($this->total_income / $familyCount) : $this->total_income;

        $this->total_expenses = (int) $this->household_expenses + (int) $this->business_expenses + (int) $this->other_installments;

        $this->saving_capacity = $this->total_income - $this->total_expenses;

        // Kemampuan mengangsur standar aman perbankan mikro syariah (75% dari saving capacity)
        $this->installment_capacity = $this->saving_capacity > 0 ? (int) round($this->saving_capacity * 0.75) : 0;

        $this->total_assets_estimate = (int) $this->business_assets_estimate
            + (int) $this->savings_amount
            + (int) $this->electronic_assets_estimate
            + (int) $this->vehicle_assets_estimate;
    }

    public function conclusionLabel(): string
    {
        return match ($this->conclusion) {
            'layak'           => 'Layak',
            'layak_bersyarat' => 'Layak Bersyarat',
            'tidak_layak'     => 'Tidak Layak',
            default           => ucfirst(str_replace('_', ' ', $this->conclusion ?? '-')),
        };
    }

    public function conclusionBadgeColor(): string
    {
        return match ($this->conclusion) {
            'layak'           => 'emerald',
            'layak_bersyarat' => 'amber',
            'tidak_layak'     => 'rose',
            default           => 'gray',
        };
    }
}
