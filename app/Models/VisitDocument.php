<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitDocument extends Model
{
    protected $fillable = [
        'visit_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'caption',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function fileSizeLabel(): string
    {
        $size = $this->file_size;

        if ($size < 1024) {
            return "{$size} B";
        } elseif ($size < 1048576) {
            return round($size / 1024, 1) . ' KB';
        }

        return round($size / 1048576, 1) . ' MB';
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}
