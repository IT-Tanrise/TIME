<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Land extends Model
{
    use HasFactory;

    protected $fillable = [
        'lokasi_lahan',
        'tahun_perolehan', 
        'nilai_perolehan',
        'alamat',
        'link_google_maps',
        'kota_kabupaten',
        'status',
        'keterangan',
        'nominal_b',
        'njop',
        'est_harga_pasar'
    ];

    protected $casts = [
        'nilai_perolehan' => 'decimal:2',
        'nominal_b' => 'decimal:2',
        'njop' => 'decimal:2',
        'est_harga_pasar' => 'decimal:2',
        'tahun_perolehan' => 'integer'
    ];

    protected static function booted()
    {
        // Track creation (only for direct creation)
        static::created(function ($land) {
            if (auth()->check()) {
                // Check if this was a direct creation or approval-based
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

        // Track updates (only for direct updates)
        static::updated(function ($land) {
            if (auth()->check()) {
                $isDirect = auth()->user()->can('land-data.approval');
                
                if ($isDirect) {
                    $changes = $land->getChanges();
                    // Remove timestamp fields
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

        // Track deletion (only for direct deletion)
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

    // Add the missing soils relationship
    public function soils(): HasMany
    {
        return $this->hasMany(Soil::class);
    }

    // Add projects relationship if it exists
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // Relationship to get business units through soils
    public function businessUnits(): BelongsToMany
    {
        return $this->belongsToMany(BusinessUnit::class, 'soils', 'land_id', 'business_unit_id')
                    ->distinct();
    }

    // Helper method to get associated business units
    public function getAssociatedBusinessUnitsAttribute()
    {
        return $this->businessUnits;
    }

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

    // Get current active rental (if any)
    public function getCurrentRentalAttribute()
    {
        return $this->activeRentals()->orderBy('end_rent', 'desc')->first();
    }

    // Check if land is currently rented
    public function getIsRentedAttribute()
    {
        return $this->activeRentals()->exists();
    }

    // Get total rented area (if partially rented)
    public function getTotalRentedAreaAttribute()
    {
        return $this->activeRentals()->sum('area_m2');
    }

    // ======= MISSING METHODS FOR BLADE TEMPLATE =======

    // Get total soil area
    public function getTotalSoilAreaAttribute()
    {
        return $this->soils()->sum('luas');
    }

    // Get formatted total soil area
    public function getFormattedTotalSoilAreaAttribute()
    {
        $total = $this->total_soil_area;
        if ($total > 0) {
            return number_format($total, 0, ',', '.') . ' mÂ²';
        }
        return '0 mÂ²';
    }

    // Get formatted acquisition value
    public function getFormattedNilaiPerolehanAttribute()
    {
        return 'Rp ' . number_format($this->nilai_perolehan, 0, ',', '.');
    }

    // Get average price per mÂ² based on acquisition value and total soil area
    public function getAveragePricePerM2Attribute()
    {
        $totalArea = $this->total_soil_area;
        if ($totalArea > 0 && $this->nilai_perolehan > 0) {
            return $this->nilai_perolehan / $totalArea;
        }
        return 0;
    }

    // Get formatted average price per mÂ²
    public function getFormattedAveragePricePerM2Attribute()
    {
        $avgPrice = $this->average_price_per_m2;
        if ($avgPrice > 0) {
            return 'Rp ' . number_format($avgPrice, 0, ',', '.');
        }
        return 'Rp 0';
    }

    // Get business units count
    public function getBusinessUnitsCountAttribute()
    {
        return $this->soils()->distinct('business_unit_id')->whereNotNull('business_unit_id')->count();
    }

    // Get business unit names (for single business unit display)
    public function getBusinessUnitNamesAttribute()
    {
        return $this->soils()
            ->with('businessUnit')
            ->get()
            ->pluck('businessUnit.name')
            ->filter()
            ->unique()
            ->implode(', ');
    }

    // Get business unit codes (for single business unit display)
    public function getBusinessUnitCodesAttribute()
    {
        return $this->soils()
            ->with('businessUnit')
            ->get()
            ->pluck('businessUnit.code')
            ->filter()
            ->unique()
            ->implode(', ');
    }

    // Get formatted nominal_b
    public function getFormattedNominalBAttribute()
    {
        if ($this->nominal_b) {
            return 'Rp ' . number_format($this->nominal_b, 0, ',', '.');
        }
        return null;
    }

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

    public function approvals(): HasMany
    {
        return $this->hasMany(LandApproval::class);
    }

    public function pendingApprovals(): HasMany
    {
        return $this->approvals()->where('status', 'pending');
    }
}