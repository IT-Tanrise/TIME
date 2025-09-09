<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryBiayaTambahanSoil extends Model
{
    use HasFactory;

    protected $table = 'category_biaya_tambahan_soils';

    protected $fillable = [
        'category'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship to BiayaTambahanSoil
    public function biayaTambahanSoils()
    {
        return $this->hasMany(BiayaTambahanSoil::class, 'category_id');
    }

    // Relationship to DescriptionBiayaTambahanSoil
    public function descriptions()
    {
        return $this->hasMany(DescriptionBiayaTambahanSoil::class, 'category_id');
    }

    // Scope for search
    public function scopeSearch($query, $term)
    {
        return $query->where('category', 'like', '%' . $term . '%');
    }
}