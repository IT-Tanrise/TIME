<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class SoilApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'soil_id',
        'requested_by',
        'approved_by',
        'status',
        'reason',
        'old_data',
        'new_data',
        'change_type',
        'approved_at'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function soil()
    {
        return $this->belongsTo(Soil::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' (GMT+7)' : null;
    }

    public function getFormattedApprovedAtAttribute()
    {
        return $this->approved_at ? $this->approved_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' (GMT+7)' : null;
    }

    // Helper methods
    public function getChangeSummary()
    {
        if ($this->change_type === 'details') {
            return $this->getDetailChangeSummary();
        } elseif ($this->change_type === 'costs') {
            return $this->getCostChangeSummary();
        }
        
        return 'Unknown change type';
    }

    private function getDetailChangeSummary()
    {
        $changes = [];
        $oldData = $this->old_data;
        $newData = $this->new_data;

        $fieldLabels = [
            'nama_penjual' => 'Seller Name',
            'alamat_penjual' => 'Seller Address',
            'nomor_ppjb' => 'PPJB Number',
            'tanggal_ppjb' => 'PPJB Date',
            'letak_tanah' => 'Land Location',
            'luas' => 'Area',
            'harga' => 'Price',
            'bukti_kepemilikan' => 'Ownership Proof',
            'bukti_kepemilikan_details' => 'Ownership Details',
            'atas_nama' => 'Owner Name',
            'nop_pbb' => 'PBB Number',
            'nama_notaris_ppat' => 'Notary Name',
            'keterangan' => 'Notes'
        ];

        foreach ($newData as $field => $newValue) {
            if (isset($oldData[$field]) && $oldData[$field] != $newValue) {
                $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                
                if (in_array($field, ['luas', 'harga'])) {
                    $oldFormatted = number_format($oldData[$field], 0, ',', '.');
                    $newFormatted = number_format($newValue, 0, ',', '.');
                    $changes[] = "{$label}: {$oldFormatted} → {$newFormatted}";
                } else {
                    $changes[] = "{$label}: {$oldData[$field]} → {$newValue}";
                }
            }
        }

        return implode(', ', $changes);
    }

    private function getCostChangeSummary()
    {
        return 'Additional costs modification requested';
    }

    // Actions
    public function approve($reason = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'reason' => $reason
        ]);

        // Apply the changes to the soil record
        if ($this->change_type === 'details') {
            $this->applyDetailChanges();
        } elseif ($this->change_type === 'costs') {
            $this->applyCostChanges();
        }
    }

    public function reject($reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'reason' => $reason
        ]);
    }

    private function applyDetailChanges()
    {
        $soil = $this->soil;
        if ($soil) {
            // Prevent creating history during approval application
            $soil->historyLogging = true;
            
            $soil->update($this->new_data);
            
            // Log approval history
            $soil->logHistory('approved_update', $this->new_data);
            
            $soil->historyLogging = false;
        }
    }

    private function applyCostChanges()
    {
        $soil = $this->soil;
        if (!$soil) return;
        
        // Set history logging flag to prevent automatic history creation
        $soil->historyLogging = true;
        
        DB::transaction(function () use ($soil) {
            // Delete all existing costs
            $soil->biayaTambahanSoils()->delete();
            
            // Create new costs from approved data
            foreach ($this->new_data as $costData) {
                if (!empty($costData['description_id']) && !empty($costData['harga'])) {
                    BiayaTambahanSoil::create([
                        'soil_id' => $soil->id,
                        'description_id' => $costData['description_id'],
                        'harga' => $costData['harga'],
                        'cost_type' => $costData['cost_type'],
                        'date_cost' => $costData['date_cost'],
                    ]);
                }
            }
        });
        
        // Log approval history
        $soil->logAdditionalCostHistory('approved', $this->new_data);
        
        // Reset history logging flag
        $soil->historyLogging = false;
    }
}