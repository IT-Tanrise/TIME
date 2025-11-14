<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\Soil;
use App\Models\PreSoilBuyApproval;
use App\Models\PreSoilBuyCashOut;

class PreSoilBuy extends Model
{
    use HasFactory;

    protected $table = 'pre_soil_buy';

    protected $fillable = [
        'nomor_memo',
        'tanggal',
        'dari',
        'kepada',
        'cc',
        'subject_perihal',
        'subject_penjual',
        'luas',
        'objek_jual_beli',
        'kesepakatan_harga_jual_beli',
        'harga_per_meter',
        'upload_file_im',
        'created_by',
        'updated_by',
        'soil_id',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'kesepakatan_harga_jual_beli' => 'integer',
        'harga_per_meter' => 'integer',
        'luas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function soils()
    {
        return $this->belongsTo(Soil::class, 'soil_id');
    }

    public function approvals()
    {
        return $this->hasMany(PreSoilBuyApproval::class);
    }

    public function pendingApprovals()
    {
        return $this->hasMany(PreSoilBuyApproval::class)->where('status', 'pending');
    }

    // Check if soil has any pending approvals
    public function hasPendingApprovals()
    {
        return $this->pendingApprovals()->exists();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cashOuts()
    {
        return $this->hasMany(PreSoilBuyCashOut::class);
    }


    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors for formatted values
    public function getFormattedLuasAttribute()
    {
        return number_format($this->luas, 0, ',', '.') . ' m²';
    }

    public function getFormattedHargaPerMeterAttribute()
    {
        return 'Rp ' . number_format($this->harga_per_meter, 0, ',', '.');
    }

    public function getFormattedKesespakatanHargaAttribute()
    {
        return 'Rp ' . number_format($this->kesepakatan_harga_jual_beli, 0, ',', '.');
    }

    // Boot method to auto-calculate
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto-calculate harga_per_meter if not set
            if ($model->luas > 0 && $model->kesepakatan_harga_jual_beli > 0 && !$model->harga_per_meter) {
                $model->harga_per_meter = round($model->kesepakatan_harga_jual_beli / $model->luas);
            }
        });
    }















    // GMT+7 timezone accessors
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

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_SOLD = 'sold';
    const STATUS_RESERVED = 'reserved';
    const STATUS_PENDING = 'pending';
    const STATUS_INACTIVE = 'inactive';

    // Get available status options
    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_SOLD => 'Sold',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    // Get status badge color
    public function getStatusBadgeColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-800',
            self::STATUS_SOLD => 'bg-gray-100 text-gray-800',
            self::STATUS_RESERVED => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PENDING => 'bg-blue-100 text-blue-800',
            self::STATUS_INACTIVE => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
