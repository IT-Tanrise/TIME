<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescriptionBiayaTambahanSoil extends Model
{
    use HasFactory;

    protected $table = 'description_biaya_tambahan_soils';

    protected $fillable = [
        'category_id',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship to Category
    public function category()
    {
        return $this->belongsTo(CategoryBiayaTambahanSoil::class, 'category_id');
    }

    // Relationship to BiayaTambahanSoil
    public function biayaTambahanSoils()
    {
        return $this->hasMany(BiayaTambahanSoil::class, 'description_id');
    }

    // Scope for search
    public function scopeSearch($query, $term)
    {
        return $query->where('description', 'like', '%' . $term . '%');
    }

    // Scope to filter by category
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}