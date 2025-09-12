<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Add this import

use App\Models\User;
use App\Models\SoilHistory;

class Soil extends Model
{
    use HasFactory;

    protected $fillable = [
        'land_id',
        'business_unit_id',
        'nama_penjual',
        'alamat_penjual',
        'nomor_ppjb',
        'tanggal_ppjb',
        'letak_tanah',
        'luas',
        'harga',
        'bukti_kepemilikan',
        'bukti_kepemilikan_details',
        'atas_nama',
        'nop_pbb',
        'nama_notaris_ppat',
        'keterangan',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_ppjb' => 'date',
        'luas' => 'integer',
        'harga' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Add these boot events to automatically log history
    protected static function boot()
    {
        parent::boot();

        static::created(function ($soil) {
            $soil->logHistory('created');
        });

        static::updated(function ($soil) {
            $soil->logHistory('updated');
        });

        // Set created_by and updated_by automatically
        static::creating(function ($soil) {
            if (Auth::check()) {
                $soil->created_by = Auth::id();
                $soil->updated_by = Auth::id();
            }
        });

        static::updating(function ($soil) {
            if (Auth::check()) {
                $soil->updated_by = Auth::id();
            }
        });
    }

    // Relationships
    public function land()
    {
        return $this->belongsTo(Land::class);
    }

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function biayaTambahanSoils()
    {
        return $this->hasMany(BiayaTambahanSoil::class)->orderBy('date_cost', 'asc');
    }

    // Accessors
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getFormattedLuasAttribute()
    {
        return number_format($this->luas, 0, ',', '.') . ' m²';
    }

    public function getHargaPerMeterAttribute()
    {
        if ($this->luas > 0) {
            return $this->harga / $this->luas;
        }
        return 0;
    }

    public function getFormattedHargaPerMeterAttribute()
    {
        return 'Rp ' . number_format($this->harga_per_meter, 0, ',', '.');
    }

    public function getTotalBiayaTambahanAttribute()
    {
        return $this->biayaTambahanSoils->sum('harga');
    }

    public function getFormattedTotalBiayaTambahanAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya_tambahan, 0, ',', '.');
    }

    public function getTotalBiayaKeseluruhanAttribute()
    {
        return $this->harga + $this->total_biaya_tambahan;
    }

    public function getFormattedTotalBiayaKeseluruhanAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya_keseluruhan, 0, ',', '.');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function histories()
    {
        return $this->hasMany(SoilHistory::class)->orderBy('created_at', 'desc');
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

    // Method to log history
    public function logHistory($action, $changes = null)
    {
        $oldValues = null;
        $newValues = null;
        $changedFields = [];

        if ($action === 'updated' && $this->isDirty()) {
            $changedFields = array_keys($this->getDirty());
            $oldValues = $this->getOriginal();
            $newValues = $this->getAttributes();
        }

        SoilHistory::create([
            'soil_id' => $this->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'changes' => $changedFields,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}