<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Carbon\Carbon;

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

    public function certificates(): HasMany
    {
        return $this->hasMany(LandCertificate::class);
    }

    public function activeCertificates(): HasMany
    {
        return $this->certificates()->where('status', 'active');
    }

    /**
     * Calculate interest costs for a specific month and year
     * Based on all soils' additional costs in this land
     * Updated with custom rounding rules and first cost handling
     */
    public function calculateInterestForMonth($month, $year)
    {
        // Get all soils for this land with their costs
        $soils = $this->soils()->with(['biayaTambahanSoils' => function($query) use ($month, $year) {
            $query->whereYear('date_cost', $year)
                ->whereMonth('date_cost', $month)
                ->orderBy('date_cost', 'asc');
        }])->get();

        // Get interest rate for this month
        $interestRate = \App\Models\LandInterestRate::where('land_id', $this->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
        
        $rate = $interestRate ? $interestRate->rate : 0;

        // Start and end dates for the month
        $monthStart = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();

        // SIMPLE FIX: Just get the end_value from previous month
        $carryForward = $this->getCarryForwardValue($month, $year);

        // Get the very first cost date across all soils
        $firstCostDate = $this->first_cost_date;

        // Collect all costs in this month from all soils
        $allCosts = collect();
        foreach ($soils as $soil) {
            foreach ($soil->biayaTambahanSoils as $cost) {
                $allCosts->push([
                    'soil_id' => $soil->id,
                    'soil_name' => $soil->nama_penjual,
                    'description' => $cost->description->description ?? 'Unknown',
                    'date' => $cost->date_cost,
                    'amount' => $cost->harga,
                    'cost_type' => $cost->cost_type,
                ]);
            }
        }

        // Sort all costs by date
        $allCosts = $allCosts->sortBy('date')->values();

        // Build calculation entries
        $calculations = collect();
        $currentValue = $carryForward;

        // Handle interest from previous month to first cost in this month
        // ONLY if there's a carry forward AND costs in this month
        if ($carryForward > 0 && $allCosts->isNotEmpty()) {
            $firstCostInMonth = $allCosts->first();
            $firstCostInMonthDate = \Carbon\Carbon::parse($firstCostInMonth['date'])->startOfDay();
            
            // Calculate days from start of month to first cost
            $days = (int) floor($monthStart->diffInDays($firstCostInMonthDate, false));
            
            if ($days > 0) {
                $interest = $this->calculateInterest($carryForward, $rate, $days);
                $valueAfterInterest = $carryForward + $interest;
                
                $calculations->push([
                    'description' => 'Interest from previous month',
                    'start_date' => $monthStart->format('Y-m-d'),
                    'end_date' => $firstCostInMonthDate->format('Y-m-d'),
                    'days' => $days,
                    'harga_perolehan' => 0,
                    'nilai_sebelum' => $carryForward,
                    'rate' => $rate,
                    'interest' => $interest,
                    'nilai_setelah' => $valueAfterInterest,
                    'soil_name' => null,
                    'cost_type' => null,
                ]);
                
                $currentValue = $valueAfterInterest;
            }
        }

        // Process each cost
        foreach ($allCosts as $index => $cost) {
            $costDate = \Carbon\Carbon::parse($cost['date'])->startOfDay();
            
            $isVeryFirstCost = $firstCostDate && $costDate->equalTo($firstCostDate);
            
            $nextCost = $allCosts->get($index + 1);
            $nextDate = $nextCost ? \Carbon\Carbon::parse($nextCost['date'])->startOfDay() : $monthEnd->endOfDay();
            
            $days = (int) floor($costDate->diffInDays($nextDate, false));
            
            if ($isVeryFirstCost) {
                $interest = $this->calculateInterest($cost['amount'], $rate, $days);
                $valueAfterCost = $cost['amount'] + $interest;
                
                $calculations->push([
                    'description' => $cost['description'] . ' (First Cost)',
                    'start_date' => $costDate->format('Y-m-d'),
                    'end_date' => $nextDate->format('Y-m-d'),
                    'days' => $days,
                    'harga_perolehan' => $cost['amount'],
                    'nilai_sebelum' => $cost['amount'],
                    'rate' => $rate,
                    'interest' => $interest,
                    'nilai_setelah' => $valueAfterCost,
                    'soil_name' => $cost['soil_name'],
                    'cost_type' => $cost['cost_type'],
                ]);
                
                $currentValue = $valueAfterCost;
            } else {
                $nilaiSebelum = $currentValue;
                $interest = $this->calculateInterest($currentValue, $rate, $days);
                $valueAfterCost = $currentValue + $cost['amount'] + $interest;
                
                $calculations->push([
                    'description' => $cost['description'],
                    'start_date' => $costDate->format('Y-m-d'),
                    'end_date' => $nextDate->format('Y-m-d'),
                    'days' => $days,
                    'harga_perolehan' => $cost['amount'],
                    'nilai_sebelum' => $nilaiSebelum,
                    'rate' => $rate,
                    'interest' => $interest,
                    'nilai_setelah' => $valueAfterCost,
                    'soil_name' => $cost['soil_name'],
                    'cost_type' => $cost['cost_type'],
                ]);
                
                $currentValue = $valueAfterCost;
            }
        }

        // If no costs in the month, calculate interest for entire month on carry forward
        if ($allCosts->isEmpty() && $carryForward > 0) {
            $days = (int) floor($monthStart->diffInDays($monthEnd, false));
            $interest = $this->calculateInterest($carryForward, $rate, $days);
            $valueAfterInterest = $carryForward + $interest;
            
            $calculations->push([
                'description' => 'Interest from previous month',
                'start_date' => $monthStart->format('Y-m-d'),
                'end_date' => $monthEnd->format('Y-m-d'),
                'days' => $days,
                'harga_perolehan' => 0,
                'nilai_sebelum' => $carryForward,
                'rate' => $rate,
                'interest' => $interest,
                'nilai_setelah' => $valueAfterInterest,
                'soil_name' => null,
                'cost_type' => null,
            ]);
            
            $currentValue = $valueAfterInterest;
        }

        return [
            'calculations' => $calculations,
            'start_value' => $carryForward,
            'end_value' => $currentValue,
            'total_costs' => $allCosts->sum('amount'),
            'total_interest' => $calculations->sum('interest'),
            'interest_rate' => $rate,
        ];
    }

    /**
     * Calculate interest amount with custom rounding rules
     * Rule: <= 0.4 rounds down, > 0.4 rounds up
     */
    private function calculateInterest($baseValue, $rate, $days)
    {
        if ($rate <= 0 || $days <= 0) {
            return 0;
        }
        
        $daysInYear = 365;
        $interest = ($baseValue * ($rate / 100) / $daysInYear * $days);
        
        // Custom rounding: <= 0.4 rounds down, > 0.4 rounds up
        $decimal = $interest - floor($interest);
        if ($decimal <= 0.4) {
            return floor($interest);
        } else {
            return ceil($interest);
        }
    }

    /**
     * Get carry forward value from previous months
     * Updated to handle THE VERY FIRST COST correctly across all soils
     * Uses SAME logic as calculateInterestForMonth to ensure consistency
     */
    private function getCarryForwardValue($month, $year)
    {
        // Get first cost date to know when calculations started
        $firstCost = \App\Models\BiayaTambahanSoil::whereHas('soil', function($query) {
                $query->where('land_id', $this->id);
            })
            ->orderBy('date_cost', 'asc')
            ->first();
        
        if (!$firstCost) {
            return 0;
        }
        
        $firstCostDate = \Carbon\Carbon::parse($firstCost->date_cost);
        $requestedMonth = \Carbon\Carbon::create($year, $month, 1);
        
        // If requested month is before or same as first cost month, no carry forward
        if ($requestedMonth->lessThanOrEqualTo($firstCostDate->startOfMonth())) {
            return 0;
        }
        
        // Get previous month
        $previousMonth = $requestedMonth->copy()->subMonth();
        
        // Simply calculate the previous month and return its end_value
        $previousMonthData = $this->calculateInterestForMonth($previousMonth->month, $previousMonth->year);
        
        return $previousMonthData['end_value'];
    }

    /**
     * Get available months with costs
     */
    public function getAvailableMonthsWithCosts()
    {
        $costs = \App\Models\BiayaTambahanSoil::whereHas('soil', function($query) {
                $query->where('land_id', $this->id);
            })
            ->selectRaw('YEAR(date_cost) as year, MONTH(date_cost) as month')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return $costs->map(function($cost) {
            return [
                'year' => $cost->year,
                'month' => $cost->month,
                'label' => \Carbon\Carbon::create($cost->year, $cost->month, 1)->format('F Y')
            ];
        });
    }

    /**
     * Get total land interest from first cost date until current month
     */
    public function getTotalLandInterest()
    {
        // Get the first cost date
        $firstCost = \App\Models\BiayaTambahanSoil::whereHas('soil', function($query) {
                $query->where('land_id', $this->id);
            })
            ->orderBy('date_cost', 'asc')
            ->first();
        
        if (!$firstCost) {
            return 0;
        }
        
        $startDate = \Carbon\Carbon::parse($firstCost->date_cost);
        $startMonth = $startDate->month;
        $startYear = $startDate->year;
        
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        $totalInterest = 0;
        
        // Loop through all months from start to current
        $currentDate = \Carbon\Carbon::create($startYear, $startMonth, 1);
        $endDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
        
        while ($currentDate <= $endDate) {
            $interestData = $this->calculateInterestForMonth($currentDate->month, $currentDate->year);
            $totalInterest += $interestData['total_interest'];
            $currentDate->addMonth();
        }
        
        return $totalInterest;
    }

    /**
     * Get formatted total land interest
     */
    public function getFormattedTotalLandInterestAttribute()
    {
        $total = $this->getTotalLandInterest();
        return 'Rp ' . number_format($total, 0, ',', '.');
    }

    /**
     * Get first cost date
     */
    public function getFirstCostDateAttribute()
    {
        $firstCost = \App\Models\BiayaTambahanSoil::whereHas('soil', function($query) {
                $query->where('land_id', $this->id);
            })
            ->orderBy('date_cost', 'asc')
            ->first();
        
        return $firstCost ? \Carbon\Carbon::parse($firstCost->date_cost) : null;
    }

    /**
     * Get interest period description
     */
    public function getInterestPeriodAttribute()
    {
        $firstDate = $this->first_cost_date;
        
        if (!$firstDate) {
            return 'No costs recorded';
        }
        
        return $firstDate->format('M Y') . ' - ' . date('M Y');
    }
}