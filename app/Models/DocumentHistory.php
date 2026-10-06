<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'document_number',
        'reference_id',
        'member_id',
        'business_id',
        'generated_by',
        'file_path',
        'snapshot_data',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_data' => 'array',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function documentTypeLabel(): string
    {
        return match ($this->document_type) {
            'financing_analysis'     => 'Analisis Pembiayaan',
            'feasibility_assessment' => 'Uji Kelayakan',
            'business_evaluation'    => 'Evaluasi Usaha',
            default                  => ucfirst(str_replace('_', ' ', $this->document_type)),
        };
    }
}
