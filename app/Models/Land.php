<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Land extends Model
{
    use HasFactory;

    protected $fillable = [
        'lokasi_lahan',
        'tahun_perolehan',
        'business_unit_id',
        'alamat',
        'link_google_maps',
        'kota_kabupaten',
        'status',
        'keterangan',
        'njop',
        'est_harga_pasar'
    ];

    protected $casts = [
        'njop' => 'decimal:2',
        'est_harga_pasar' => 'decimal:2',
        'tahun_perolehan' => 'integer'
    ];

    protected static function booted()
    {
        static::created(function ($land) {
            if (auth()->check()) {
                $isDirect = auth()->user()->can('land-data.approval');
                
                if ($isDirect) {
                    LandHistory::recordHistory(
                        $land->id,
                        'created',
                        null,
                        $land->only($land->fillable),
                        null
                    );
                }
            }
        });

        static::updated(function ($land) {
            if (auth()->check()) {
                $isDirect = auth()->user()->can('land-data.approval');
                
                if ($isDirect) {
                    $changes = $land->getChanges();
                    unset($changes['updated_at']);
                    
                    if (!empty($changes)) {
                        LandHistory::recordHistory(
                            $land->id,
                            'updated',
                            $land->getOriginal(),
                            $changes,
                            null
                        );
                    }
                }
            }
        });

        static::deleting(function ($land) {
            if (auth()->check()) {
                $isDirect = auth()->user()->can('land-data.approval');
                
                if ($isDirect) {
                    LandHistory::recordHistory(
                        $land->id,
                        'deleted',
                        $land->toArray(),
                        null,
                        null
                    );
                }
            }
        });
    }

    // RELATIONSHIPS
    
    // Direct Business Unit relationship
    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class, 'business_unit_id');
    }

    // Soils relationship
    public function soils(): HasMany
    {
        return $this->hasMany(Soil::class);
    }

    // Projects relationship
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // Rentals relationships
    public function rentals(): HasMany
    {
        return $this->hasMany(RentLand::class);
    }

    public function activeRentals(): HasMany
    {
        return $this->rentals()->where('end_rent', '>=', now());
    }

    public function expiredRentals(): HasMany
    {
        return $this->rentals()->where('end_rent', '<', now());
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(LandApproval::class);
    }

    public function pendingApprovals(): HasMany
    {
        return $this->approvals()->where('status', 'pending');
    }

    // RENTAL ATTRIBUTES

    public function getCurrentRentalAttribute()
    {
        return $this->activeRentals()->orderBy('end_rent', 'desc')->first();
    }

    public function getIsRentedAttribute()
    {
        return $this->activeRentals()->exists();
    }

    public function getTotalRentedAreaAttribute()
    {
        return $this->activeRentals()->sum('area_m2');
    }

    // SOIL-RELATED ATTRIBUTES

    // Get total soil area
    public function getTotalSoilAreaAttribute()
    {
        return $this->soils()->sum('luas');
    }

    // Get total soil price (sum of all soil prices)
    public function getTotalSoilPriceAttribute()
    {
        return $this->soils()->sum('harga');
    }

    // Get formatted total soil price
    public function getFormattedTotalSoilPriceAttribute()
    {
        $total = $this->total_soil_price;
        if ($total > 0) {
            return 'Rp ' . number_format($total, 0, ',', '.');
        }
        return 'Rp 0';
    }

    // Get formatted total soil area
    public function getFormattedTotalSoilAreaAttribute()
    {
        $total = $this->total_soil_area;
        if ($total > 0) {
            return number_format($total, 0, ',', '.') . ' m²';
        }
        return '0 m²';
    }

    // Get average price per m² based on SOIL prices divided by SOIL area
    public function getAveragePricePerM2Attribute()
    {
        $totalArea = $this->total_soil_area;
        $totalPrice = $this->total_soil_price;
        
        if ($totalArea > 0 && $totalPrice > 0) {
            return $totalPrice / $totalArea;
        }
        return 0;
    }

    // Get formatted average price per m²
    public function getFormattedAveragePricePerM2Attribute()
    {
        $avgPrice = $this->average_price_per_m2;
        if ($avgPrice > 0) {
            return 'Rp ' . number_format($avgPrice, 0, ',', '.');
        }
        return 'Rp 0';
    }

    // FORMATTING ATTRIBUTES

    // Get formatted NJOP
    public function getFormattedNjopAttribute()
    {
        if ($this->njop) {
            return 'Rp ' . number_format($this->njop, 0, ',', '.');
        }
        return null;
    }

    // Get formatted estimated market price
    public function getFormattedEstHargaPasarAttribute()
    {
        if ($this->est_harga_pasar) {
            return 'Rp ' . number_format($this->est_harga_pasar, 0, ',', '.');
        }
        return null;
    }
}