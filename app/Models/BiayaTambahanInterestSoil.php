<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BiayaTambahanInterestSoil extends Model
{
    use HasFactory;

    protected $fillable = [
        'soil_id',
        'start_date',
        'end_date',
        'remarks',
        'harga_perolehan',
        'bunga'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'harga_perolehan' => 'integer',
        'bunga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to Soil
     */
    public function soil()
    {
        return $this->belongsTo(Soil::class);
    }

    /**
     * Calculate number of days between start and end date
     */
    public function getHariAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get the previous accumulated value (nilai tanah before)
     * This calculates the sum of all previous periods up to this one
     */
    public function getNilaiTanahSebelumAttribute()
    {
        // Get all previous records up to this one, ordered by start_date
        $previousRecords = static::where('soil_id', $this->soil_id)
            ->where('start_date', '<', $this->start_date)
            ->orderBy('start_date', 'asc')
            ->get();

        $total = 0;
        foreach ($previousRecords as $record) {
            $total += $record->harga_perolehan + $record->bunga_calculation;
        }

        return $total;
    }

    /**
     * Calculate interest amount using compound interest formula
     * Formula: (baseValue * bunga% / 365 * days)
     * where baseValue = previous accumulated total + current harga_perolehan
     */
    public function getBungaCalculationAttribute()
    {
        $daysInYear = 365;
        $hari = $this->hari;
        
        if (!$this->bunga || $this->bunga <= 0 || $hari <= 0) {
            return 0;
        }
        
        // Get the base value (previous nilai tanah + current harga perolehan)
        $baseValue = $this->nilai_tanah_sebelum + $this->harga_perolehan;
        
        // Calculate: (baseValue * bunga% / daysInYear * hari)
        $interest = ($baseValue * ($this->bunga / 100) / $daysInYear * $hari);
        
        return round($interest, 0); // Round to nearest integer
    }

    /**
     * Calculate final nilai tanah (accumulated value after this period)
     * Formula: previous total + current acquisition cost + interest
     */
    public function getNilaiTanahAttribute()
    {
        return $this->nilai_tanah_sebelum + $this->harga_perolehan + $this->bunga_calculation;
    }

    /**
     * Format harga perolehan for display
     */
    public function getFormattedHargaPerolehanAttribute()
    {
        return 'Rp ' . number_format($this->harga_perolehan, 0, ',', '.');
    }

    /**
     * Format bunga calculation for display
     */
    public function getFormattedBungaCalculationAttribute()
    {
        return 'Rp ' . number_format($this->bunga_calculation, 0, ',', '.');
    }

    /**
     * Format nilai tanah for display
     */
    public function getFormattedNilaiTanahAttribute()
    {
        return 'Rp ' . number_format($this->nilai_tanah, 0, ',', '.');
    }

    /**
     * Format nilai tanah sebelum for display
     */
    public function getFormattedNilaiTanahSebelumAttribute()
    {
        return 'Rp ' . number_format($this->nilai_tanah_sebelum, 0, ',', '.');
    }

    /**
     * Get formatted date range
     */
    public function getFormattedDateRangeAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return '-';
        }
        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }

    /**
     * Scope to get records ordered by date
     */
    public function scopeOrderedByDate($query)
    {
        return $query->orderBy('start_date', 'asc');
    }

    /**
     * Scope to get records for a specific soil
     */
    public function scopeForSoil($query, $soilId)
    {
        return $query->where('soil_id', $soilId);
    }

    /**
     * Scope to get records within a date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    /**
     * Calculate total harga perolehan for a soil
     */
    public static function getTotalHargaPerolehan($soilId)
    {
        return static::where('soil_id', $soilId)->sum('harga_perolehan');
    }

    /**
     * Calculate total interest for a soil
     */
    public static function getTotalInterest($soilId)
    {
        $records = static::where('soil_id', $soilId)
            ->orderBy('start_date', 'asc')
            ->get();
        
        $totalInterest = 0;
        foreach ($records as $record) {
            $totalInterest += $record->bunga_calculation;
        }
        
        return $totalInterest;
    }

    /**
     * Get the final accumulated land value for a soil
     */
    public static function getFinalNilaiTanah($soilId)
    {
        $lastRecord = static::where('soil_id', $soilId)
            ->orderBy('start_date', 'desc')
            ->first();
        
        return $lastRecord ? $lastRecord->nilai_tanah : 0;
    }
}