<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandInterestRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'land_id',
        'month',
        'year',
        'rate',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($rate) {
            if (auth()->check()) {
                $rate->created_by = auth()->id();
            }
        });

        static::updating(function ($rate) {
            if (auth()->check()) {
                $rate->updated_by = auth()->id();
            }
        });
    }

    // RELATIONSHIPS
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ATTRIBUTES
    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 
            4 => 'April', 5 => 'May', 6 => 'June',
            7 => 'July', 8 => 'August', 9 => 'September', 
            10 => 'October', 11 => 'November', 12 => 'December'
        ];
        return $months[$this->month] ?? '';
    }

    public function getPeriodAttribute(): string
    {
        return sprintf('%s %d', $this->month_name, $this->year);
    }

    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 2) . '%';
    }

    // SCOPES
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)
                     ->where('year', $year);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('year', 'desc')
                     ->orderBy('month', 'desc');
    }

    // STATIC METHODS
    public static function getRate(int $landId, int $month, int $year): ?float
    {
        $rate = self::where('land_id', $landId)
                    ->forMonth($month, $year)
                    ->first();
        return $rate ? $rate->rate : null;
    }

    public static function getAvailableYears(): array
    {
        return self::distinct()
                   ->pluck('year')
                   ->sort()
                   ->values()
                   ->toArray();
    }
}