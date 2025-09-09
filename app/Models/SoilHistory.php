<?php
// app/Models/SoilHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SoilHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'soil_id',
        'user_id',
        'action',
        'changes',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function soil()
    {
        return $this->belongsTo(Soil::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors for GMT+7 timezone
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
        
        return $this->created_at_gmt7->format('d/m/Y H:i') . ' (GMT+7)';
    }

    // Get action display name
    public function getActionDisplayAttribute()
    {
        return match($this->action) {
            'created' => 'Record Created',
            'updated' => 'Record Updated', 
            'deleted' => 'Record Deleted',
            'restored' => 'Record Restored',
            default => ucfirst($this->action)
        };
    }

    // Get user display name
    public function getUserDisplayAttribute()
    {
        return $this->user ? $this->user->name : 'System';
    }

    // Get changes summary
    public function getChangesSummaryAttribute()
    {
        if (!$this->changes || !is_array($this->changes)) {
            return 'No changes recorded';
        }

        $changesList = [];
        foreach ($this->changes as $field) {
            $changesList[] = $this->getFieldDisplayName($field);
        }

        return implode(', ', $changesList);
    }

    // Helper method to get human readable field names
    private function getFieldDisplayName($field)
    {
        $fieldMap = [
            'nama_penjual' => 'Seller Name',
            'alamat_penjual' => 'Seller Address',
            'nomor_ppjb' => 'PPJB Number',
            'tanggal_ppjb' => 'PPJB Date',
            'letak_tanah' => 'Land Location',
            'luas' => 'Area',
            'harga' => 'Price',
            'bukti_kepemilikan' => 'Ownership Proof',
            'bukti_kepemilikan_details' => 'Ownership Proof Details',
            'atas_nama' => 'Owner Name',
            'nop_pbb' => 'NOP PBB',
            'nama_notaris_ppat' => 'Notaris/PPAT Name',
            'keterangan' => 'Notes',
            'land_id' => 'Land',
            'business_unit_id' => 'Business Unit',
        ];

        return $fieldMap[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    // Scope for recent activities
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // Scope for specific actions
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }
}