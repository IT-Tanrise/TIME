<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescriptionBiayaTambahanSoil extends Model
{
    use HasFactory;

    protected $table = 'description_biaya_tambahan_soils';

    protected $fillable = [
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
}