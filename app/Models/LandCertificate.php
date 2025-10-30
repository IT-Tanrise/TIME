<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class LandCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'land_id',
        'certificate_type',
        'certificate_number',
        'issued_date',
        'expired_date',
        'issued_by',
        'notes',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expired_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REVOKED = 'revoked';
    const STATUS_PENDING = 'pending';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (Auth::check()) {
                $certificate->created_by = Auth::id();
                $certificate->updated_by = Auth::id();
            }
        });

        static::updating(function ($certificate) {
            if (Auth::check()) {
                $certificate->updated_by = Auth::id();
            }
        });
    }

    // RELATIONSHIPS

    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    public function soils(): BelongsToMany
    {
        return $this->belongsToMany(Soil::class, 'certificate_soil', 'land_certificate_id', 'soil_id')
                    ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ACCESSORS

    public function getFormattedIssuedDateAttribute()
    {
        return $this->issued_date ? $this->issued_date->format('d-m-Y') : null;
    }

    public function getFormattedExpiredDateAttribute()
    {
        return $this->expired_date ? $this->expired_date->format('d-m-Y') : null;
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->expired_date) return false;
        return $this->expired_date->isPast();
    }

    public function getDaysUntilExpirationAttribute()
    {
        if (!$this->expired_date) return null;
        return (int)now()->diffInDays($this->expired_date, false);
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-800',
            self::STATUS_EXPIRED => 'bg-red-100 text-red-800',
            self::STATUS_REVOKED => 'bg-gray-100 text-gray-800',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getFormattedStatusAttribute()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? ucfirst($this->status);
    }

    public function getTotalSoilAreaAttribute()
    {
        return $this->soils()->sum('luas');
    }

    public function getFormattedTotalSoilAreaAttribute()
    {
        $total = $this->total_soil_area;
        if ($total > 0) {
            return number_format($total, 0, ',', '.') . ' m²';
        }
        return '0 m²';
    }

    public function getCreatedAtGmt7Attribute()
    {
        return $this->created_at ? $this->created_at->setTimezone('Asia/Jakarta') : null;
    }

    public function getUpdatedAtGmt7Attribute()
    {
        return $this->updated_at ? $this->updated_at->setTimezone('Asia/Jakarta') : null;
    }

    public function getFormattedCreatedAtAttribute()
    {
        if (!$this->created_at) return null;
        
        return $this->created_at_gmt7->format('d/m/Y H:i') . ' (GMT+7)' . 
               ($this->createdBy ? ' - ' . $this->createdBy->name : '');
    }

    public function getFormattedUpdatedAtAttribute()
    {
        if (!$this->updated_at) return null;
        
        return $this->updated_at_gmt7->format('d/m/Y H:i') . ' (GMT+7)' . 
               ($this->updatedBy ? ' - ' . $this->updatedBy->name : '');
    }

    // STATIC METHODS

    public static function getCertificateTypeOptions()
    {
        return [
            'SHM' => 'Sertifikat Hak Milik (SHM)',
            'SHGB' => 'Sertifikat Hak Guna Bangunan (SHGB)',
            'SHGU' => 'Sertifikat Hak Guna Usaha (SHGU)',
            'SHP' => 'Sertifikat Hak Pakai (SHP)',
            'Girik' => 'Girik/Letter C',
            'AJB' => 'Akta Jual Beli (AJB)',
            'Petok D' => 'Petok D',
            'Lainnya' => 'Lainnya'
        ];
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_REVOKED => 'Revoked',
            self::STATUS_PENDING => 'Pending',
        ];
    }

    // SCOPES

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeExpiringSoon($query, $days = 90)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->whereNotNull('expired_date')
                     ->whereBetween('expired_date', [now(), now()->addDays($days)]);
    }
}