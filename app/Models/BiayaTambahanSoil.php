<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaTambahanSoil extends Model
{
    use HasFactory;

    protected $fillable = [
        'soil_id',
        'description_id',
        'harga',
        'cost_type',
        'date_cost'  // Add this
    ];

    protected $casts = [
        'harga' => 'integer',
        'date_cost' => 'date',  // Add this
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship to Soil
    public function soil()
    {
        return $this->belongsTo(Soil::class);
    }

    // Relationship to Description
    public function description()
    {
        return $this->belongsTo(DescriptionBiayaTambahanSoil::class, 'description_id');
    }

    // Relationship to Category (through Description)
    public function category()
    {
        return $this->hasOneThrough(
            CategoryBiayaTambahanSoil::class,
            DescriptionBiayaTambahanSoil::class,
            'id', // Foreign key on descriptions table
            'id', // Foreign key on categories table
            'description_id', // Local key on biaya_tambahan_soils table
            'category_id' // Local key on descriptions table
        );
    }

    // Format harga untuk display
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    // Get cost type options
    public static function getCostTypeOptions()
    {
        return [
            'standard' => 'Standard',
            'non_standard' => 'Non Standard'
        ];
    }

    // Format harga for input (with thousand separator)
    public function getFormattedInputHargaAttribute()
    {
        return number_format($this->harga, 0, ',', '.');
    }
}