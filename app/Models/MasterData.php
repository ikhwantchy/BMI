<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterData extends Model
{
    protected $table = 'master_data';

    protected $fillable = [
        'category',
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function categoryLabel(): string
    {
        return match($this->category) {
            'business_type'     => 'Jenis Usaha',
            'obstacle_category' => 'Kategori Kendala',
            'coaching_type'     => 'Jenis Pembinaan',
            default             => $this->category,
        };
    }
}
