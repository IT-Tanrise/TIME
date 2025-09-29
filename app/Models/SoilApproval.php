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
        'change_type', // 'details', 'costs', 'delete'
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
        return $this->belongsTo(Soil::class)->withTrashed(); // In case the soil record gets soft deleted
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
        return $query->where('soil_approvals.status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('soil_approvals.status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('soil_approvals.status', 'rejected');
    }

    public function scopeDetails($query)
    {
        return $query->where('change_type', 'details');
    }

    public function scopeCosts($query)
    {
        return $query->where('change_type', 'costs');
    }

    public function scopeDeletes($query)
    {
        return $query->where('change_type', 'delete');
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
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isDetailsChange()
    {
        return $this->change_type === 'details';
    }

    public function isCostsChange()
    {
        return $this->change_type === 'costs';
    }

    public function isDeleteRequest()
    {
        return $this->change_type === 'delete';
    }

    // Get deletion reason if this is a delete request
    public function getDeletionReason()
    {
        if ($this->isDeleteRequest() && is_array($this->new_data)) {
            return $this->new_data['deletion_reason'] ?? null;
        }
        return null;
    }

    // Get formatted change type
    public function getFormattedChangeType()
    {
        return match($this->change_type) {
            'details' => 'Soil Details',
            'costs' => 'Additional Costs',
            'delete' => 'Delete Record',
            'create' => 'Create Record', // Add this line
            default => ucfirst($this->change_type)
        };
    }

    public function getChangeSummary()
    {
        if ($this->change_type === 'details') {
            return $this->getDetailChangeSummary();
        } elseif ($this->change_type === 'costs') {
            return $this->getCostChangeSummary();
        } elseif ($this->change_type === 'delete') {
            return $this->getDeleteChangeSummary();
        } elseif ($this->change_type === 'create') {
            return $this->getCreateChangeSummary();
        }
        
        return 'Unknown change type';
    }

    private function getCreateChangeSummary()
    {
        if (!$this->new_data) {
            return 'New soil record creation request';
        }
        
        $sellerName = $this->new_data['nama_penjual'] ?? 'Unknown Seller';
        $location = $this->new_data['letak_tanah'] ?? 'Unknown Location';
        $ppjb = $this->new_data['nomor_ppjb'] ?? 'N/A';
        
        return "Create new record: {$sellerName} - {$location} (PPJB: {$ppjb})";
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
            'letak_tanah' => 'Soil Location',
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
        $oldData = $this->old_data ?? [];
        $newData = $this->new_data ?? [];
        
        $oldTotal = collect($oldData)->sum('harga');
        $newTotal = collect($newData)->sum('harga');
        
        return 'Cost changes: ' . count($newData) . ' items, total difference: Rp ' . 
               number_format($newTotal - $oldTotal, 0, ',', '.');
    }

    private function getDeleteChangeSummary()
    {
        $reason = $this->getDeletionReason();
        $summary = 'Request to delete soil record';
        
        if ($reason) {
            $summary .= ' - Reason: ' . substr($reason, 0, 50) . (strlen($reason) > 50 ? '...' : '');
        }
        
        return $summary;
    }

    // Actions
    public function approve($reason = null)
    {
        DB::transaction(function () use ($reason) {
            $this->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'reason' => $reason
            ]);

            // Apply the changes to the soil record based on change type
            if ($this->change_type === 'details') {
                $this->applyDetailChanges();
            } elseif ($this->change_type === 'costs') {
                $this->applyCostChanges();
            } elseif ($this->change_type === 'delete') {
                $this->applyDeletion();
            } elseif ($this->change_type === 'create') {
                $this->applyCreation();
            }
        });
    }

    private function applyCreation()
    {
        if (!$this->new_data) {
            throw new \Exception('No data provided for soil record creation.');
        }
        
        // Create the new soil record
        $soil = Soil::create($this->new_data);
        
        // Update this approval record with the newly created soil_id
        $this->update(['soil_id' => $soil->id]);
        
        // Log creation history with approval info
        $soil->logHistory('approved_creation', $this->new_data, $this->approved_by, $this->id);
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

    private function applyDeletion()
    {
        $soil = $this->soil;
        if (!$soil) {
            throw new \Exception('Soil record not found for deletion approval.');
        }

        // Log deletion history before actually deleting
        $soil->logHistory('approved_deletion', [
            'deletion_reason' => $this->getDeletionReason(),
            'deleted_by_approval_id' => $this->id,
            'approved_by' => $this->approved_by
        ]);

        // Delete related BiayaTambahanSoil records first
        $soil->biayaTambahanSoils()->delete();
        
        // Delete the soil record
        $soil->delete();
    }

    public function scopeCreates($query)
    {
        return $query->where('change_type', 'create');
    }

    public function isCreateRequest()
    {
        return $this->change_type === 'create' && is_null($this->soil_id);
    }
}