<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RentLand extends Model
{
    use HasFactory;

    protected $fillable = [
        'land_id',
        'area_m2',
        'price',
        'nama_penyewa',
        'alamat_penyewa',
        'nomor_handphone_penyewa',
        'start_rent',
        'end_rent',
        'reminder_period',
        'reminder_sent'
    ];

    protected $casts = [
        'start_rent' => 'date',
        'end_rent' => 'date',
        'reminder_sent' => 'boolean'
    ];

    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    // FIXED: Scope for filtering by business unit through land->soils relationship
    public function scopeByBusinessUnit($query, $businessUnitId)
    {
        return $query->whereHas('land', function ($landQuery) use ($businessUnitId) {
            $landQuery->where('business_unit_id', $businessUnitId);
        });
    }

    // Scope for expired rentals
    public function scopeExpired($query)
    {
        return $query->where('end_rent', '<', now());
    }

    // Scope for rentals expiring soon based on reminder period
    public function scopeExpiringSoon($query, $period = null)
    {
        $now = now();
        
        if ($period) {
            $query->where('reminder_period', $period);
        }
        
        return $query->where(function ($query) use ($now) {
            $query->where(function ($q) use ($now) {
                $q->where('reminder_period', '1month')
                  ->where('end_rent', '<=', $now->copy()->addMonth())
                  ->where('end_rent', '>=', $now);
            })
            ->orWhere(function ($q) use ($now) {
                $q->where('reminder_period', '1week')
                  ->where('end_rent', '<=', $now->copy()->addWeek())
                  ->where('end_rent', '>=', $now);
            })
            ->orWhere(function ($q) use ($now) {
                $q->where('reminder_period', '3days')
                  ->where('end_rent', '<=', $now->copy()->addDays(3))
                  ->where('end_rent', '>=', $now);
            });
        });
    }

    // Get days until expiry
    public function getDaysUntilExpiryAttribute()
    {
        return now()->diffInDays($this->end_rent, false);
    }

    // Check if rental is expired
    public function getIsExpiredAttribute()
    {
        return $this->end_rent->isPast();
    }

    // Check if rental is expiring soon based on reminder period
    public function getIsExpiringSoonAttribute()
    {
        if (!$this->reminder_period) {
            return false;
        }

        $now = now();
        $daysUntilExpiry = $this->days_until_expiry;

        return match ($this->reminder_period) {
            '1month' => $daysUntilExpiry <= 30 && $daysUntilExpiry >= 0,
            '1week' => $daysUntilExpiry <= 7 && $daysUntilExpiry >= 0,
            '3days' => $daysUntilExpiry <= 3 && $daysUntilExpiry >= 0,
            default => false
        };
    }

    // Format price to currency
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // Get reminder period label
    public function getReminderPeriodLabelAttribute()
    {
        return match ($this->reminder_period) {
            '1month' => '1 Month Before',
            '1week' => '1 Week Before',
            '3days' => '3 Days Before',
            default => 'No Reminder'
        };
    }

    // Get status based on expiry
    public function getStatusAttribute()
    {
        if ($this->is_expired) {
            return 'expired';
        }
        
        if ($this->is_expiring_soon) {
            return 'expiring_soon';
        }
        
        return 'active';
    }

    // Get status label
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'expired' => 'Expired',
            'expiring_soon' => 'Expiring Soon',
            'active' => 'Active',
            default => 'Unknown'
        };
    }

    // Get status color for UI
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'expired' => 'red',
            'expiring_soon' => 'yellow',
            'active' => 'green',
            default => 'gray'
        };
    }
}