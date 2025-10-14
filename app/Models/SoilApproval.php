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
            'create' => 'Create Record',
            'interest' => 'Interest Costs',
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
        } elseif ($this->change_type === 'interest') {
            return $this->getInterestChangeSummary();
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

    private function getInterestChangeSummary()
    {
        $oldData = $this->old_data ?? [];
        $newData = $this->new_data ?? [];
        
        $oldCount = count($oldData);
        $newCount = count($newData);
        
        $oldTotal = collect($oldData)->sum('harga_perolehan');
        $newTotal = collect($newData)->sum('harga_perolehan');
        
        return 'Interest changes: ' . $newCount . ' periods, total harga perolehan difference: Rp ' . 
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
            } elseif ($this->change_type === 'interest') {
                $this->applyInterestChanges();
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
        DB::transaction(function () use ($reason) {
            $this->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'reason' => $reason
            ]);

            // Log rejection in history based on change type
            if ($this->soil_id) {
                $soil = $this->soil;
                
                if ($soil) {
                    if ($this->change_type === 'details') {
                        $this->logRejectedDetailChanges($soil, $reason);
                    } elseif ($this->change_type === 'costs') {
                        $this->logRejectedCostChanges($soil, $reason);
                    } elseif ($this->change_type === 'delete') {
                        $this->logRejectedDeletion($soil, $reason);
                    }
                }
            } elseif ($this->change_type === 'create') {
                // For creation rejection, we don't have a soil record yet
                $this->logRejectedCreation($reason);
            } elseif ($this->change_type === 'interest') {
                $this->logRejectedInterestChanges($soil, $reason);
            }
        });
    }

    /**
     * Log rejected detail changes to history
     */
    private function logRejectedDetailChanges($soil, $reason)
    {
        $soil->historyLogging = true;
        
        try {
            // Determine changed fields
            $changedFields = array_keys($this->new_data);
            
            // Add rejection metadata
            $rejectionMetadata = [
                'rejected_by' => Auth::id(),
                'approval_id' => $this->id,
                'is_rejected_change' => true,
                'rejection_reason' => $reason
            ];
            
            $newValuesWithMetadata = array_merge($this->new_data, ['_rejection_metadata' => $rejectionMetadata]);

            SoilHistory::create([
                'soil_id' => $soil->id,
                'user_id' => Auth::id(),
                'action' => 'rejected_update',
                'changes' => $changedFields,
                'old_values' => $this->old_data,
                'new_values' => $newValuesWithMetadata,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create rejected detail history: ' . $e->getMessage(), [
                'soil_id' => $soil->id,
                'approval_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        } finally {
            $soil->historyLogging = false;
        }
    }

    /**
     * Log rejected cost changes to history
     */
    private function logRejectedCostChanges($soil, $reason)
    {
        $soil->historyLogging = true;
        
        try {
            // Add rejection metadata
            $rejectionMetadata = [
                'rejected_by' => Auth::id(),
                'approval_id' => $this->id,
                'is_rejected_change' => true,
                'rejection_reason' => $reason
            ];
            
            $newValuesWithMetadata = array_merge(
                ['costs' => $this->new_data], 
                ['_rejection_metadata' => $rejectionMetadata]
            );

            SoilHistory::create([
                'soil_id' => $soil->id,
                'user_id' => Auth::id(),
                'action' => 'rejected_cost_update',
                'changes' => [],
                'old_values' => ['costs' => $this->old_data],
                'new_values' => $newValuesWithMetadata,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create rejected cost history: ' . $e->getMessage(), [
                'soil_id' => $soil->id,
                'approval_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        } finally {
            $soil->historyLogging = false;
        }
    }

    private function logRejectedInterestChanges($soil, $reason)
    {
        $soil->historyLogging = true;
        
        try {
            // Add rejection metadata
            $rejectionMetadata = [
                'rejected_by' => Auth::id(),
                'approval_id' => $this->id,
                'is_rejected_change' => true,
                'rejection_reason' => $reason
            ];
            
            $newValuesWithMetadata = array_merge(
                ['interests' => $this->new_data], 
                ['_rejection_metadata' => $rejectionMetadata]
            );

            SoilHistory::create([
                'soil_id' => $soil->id,
                'user_id' => Auth::id(),
                'action' => 'rejected_interest_update',
                'changes' => [],
                'old_values' => ['interests' => $this->old_data],
                'new_values' => $newValuesWithMetadata,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create rejected interest history: ' . $e->getMessage(), [
                'soil_id' => $soil->id,
                'approval_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        } finally {
            $soil->historyLogging = false;
        }
    }

    /**
     * Log rejected deletion to history
     */
    private function logRejectedDeletion($soil, $reason)
    {
        $soil->historyLogging = true;
        
        try {
            $rejectionMetadata = [
                'rejected_by' => Auth::id(),
                'approval_id' => $this->id,
                'is_rejected_change' => true,
                'rejection_reason' => $reason
            ];
            
            $newValuesWithMetadata = [
                'deletion_reason' => $this->getDeletionReason(),
                '_rejection_metadata' => $rejectionMetadata
            ];

            SoilHistory::create([
                'soil_id' => $soil->id,
                'user_id' => Auth::id(),
                'action' => 'rejected_deletion',
                'changes' => [],
                'old_values' => $this->old_data,
                'new_values' => $newValuesWithMetadata,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create rejected deletion history: ' . $e->getMessage(), [
                'soil_id' => $soil->id,
                'approval_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        } finally {
            $soil->historyLogging = false;
        }
    }

    /**
     * Log rejected creation (no soil record exists yet)
     */
    private function logRejectedCreation($reason)
    {
        try {
            // For rejected creation, we log without a soil_id
            // This can be queried separately if needed
            $rejectionMetadata = [
                'rejected_by' => Auth::id(),
                'approval_id' => $this->id,
                'is_rejected_change' => true,
                'rejection_reason' => $reason,
                'was_creation_attempt' => true
            ];
            
            $newValuesWithMetadata = array_merge($this->new_data, ['_rejection_metadata' => $rejectionMetadata]);

            // Note: We can't create SoilHistory without soil_id
            // So we just log it for now - you might want to create a separate ApprovalHistory table
            \Log::info('Rejected creation request', [
                'approval_id' => $this->id,
                'requested_by' => $this->requested_by,
                'rejected_by' => Auth::id(),
                'reason' => $reason,
                'data' => $this->new_data
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to log rejected creation: ' . $e->getMessage(), [
                'approval_id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function applyDetailChanges()
    {
        $soil = $this->soil;
        if ($soil) {
            // Get old values BEFORE updating
            $oldValues = $soil->only(array_keys($this->new_data));
            
            // Prevent creating history during approval application
            $soil->historyLogging = true;
            
            $soil->update($this->new_data);
            
            // Reset flag BEFORE logging approval history
            $soil->historyLogging = false;
            
            // Now log approval history with both old and new values
            $soil->logApprovedHistory('approved_update', $oldValues, $this->new_data, $this->approved_by, $this->id);
        }
    }

    private function applyCostChanges()
    {
        $soil = $this->soil;
        if (!$soil) return;
        
        // Set history logging flag to prevent automatic history creation
        $soil->historyLogging = true;
        
        DB::transaction(function () use ($soil) {
            $oldCosts = $soil->biayaTambahanSoils()->with('description')->get();
            $newCostsData = $this->new_data;
            
            // Create lookup arrays
            $oldCostsById = $oldCosts->keyBy('id');
            $newCostsById = collect($newCostsData)->keyBy('id')->filter(fn($item) => !empty($item['id']));
            
            // Track all IDs
            $oldIds = $oldCostsById->keys();
            $newIds = $newCostsById->keys();
            
            // 1. IDENTIFY DELETED COSTS (in old but not in new)
            $deletedIds = $oldIds->diff($newIds);
            foreach ($deletedIds as $deletedId) {
                $cost = $oldCostsById->get($deletedId);
                $oldCostData = [
                    'description' => $cost->description->description ?? 'Unknown',
                    'harga' => $cost->harga,
                    'cost_type' => $cost->cost_type,
                    'date_cost' => $cost->date_cost,
                ];
                
                // Log as deleted with approval metadata
                $soil->logAdditionalCostHistory('deleted', [], $oldCostData, $this->approved_by, $this->id);
            }
            
            // Delete costs not in new data
            $soil->biayaTambahanSoils()->whereNotIn('id', $newIds)->delete();
            
            // 2. IDENTIFY ADDED COSTS (no ID or new ID)
            $addedCosts = collect($newCostsData)->filter(function($item) use ($oldIds) {
                return empty($item['id']) || !$oldIds->contains($item['id']);
            });
            
            foreach ($addedCosts as $costData) {
                if (!empty($costData['description_id']) && !empty($costData['harga'])) {
                    BiayaTambahanSoil::create([
                        'soil_id' => $soil->id,
                        'description_id' => $costData['description_id'],
                        'harga' => $costData['harga'],
                        'cost_type' => $costData['cost_type'],
                        'date_cost' => $costData['date_cost'],
                    ]);
                    
                    $newCostData = [
                        'description' => $costData['description'] ?? 'Unknown',
                        'harga' => $costData['harga'],
                        'cost_type' => $costData['cost_type'],
                        'date_cost' => $costData['date_cost'],
                    ];
                    
                    // Log as added with approval metadata
                    $soil->logAdditionalCostHistory('added', $newCostData, [], $this->approved_by, $this->id);
                }
            }
            
            // 3. IDENTIFY UPDATED COSTS (in both old and new, but changed)
            foreach ($newIds as $costId) {
                $oldCost = $oldCostsById->get($costId);
                $newCostData = $newCostsById->get($costId);
                
                if ($oldCost && $newCostData) {
                    // Check if anything actually changed
                    $hasChanges = (
                        $oldCost->description_id != $newCostData['description_id'] ||
                        $oldCost->harga != $newCostData['harga'] ||
                        $oldCost->cost_type != $newCostData['cost_type'] ||
                        $oldCost->date_cost != $newCostData['date_cost']
                    );
                    
                    if ($hasChanges) {
                        // Update the cost
                        BiayaTambahanSoil::where('id', $costId)->update([
                            'description_id' => $newCostData['description_id'],
                            'harga' => $newCostData['harga'],
                            'cost_type' => $newCostData['cost_type'],
                            'date_cost' => $newCostData['date_cost'],
                        ]);
                        
                        $oldCostData = [
                            'description' => $oldCost->description->description ?? 'Unknown',
                            'harga' => $oldCost->harga,
                            'cost_type' => $oldCost->cost_type,
                            'date_cost' => $oldCost->date_cost,
                        ];
                        
                        $newCostDataFormatted = [
                            'description' => $newCostData['description'] ?? 'Unknown',
                            'harga' => $newCostData['harga'],
                            'cost_type' => $newCostData['cost_type'],
                            'date_cost' => $newCostData['date_cost'],
                        ];
                        
                        // Log as updated with approval metadata
                        $soil->logAdditionalCostHistory('updated', $newCostDataFormatted, $oldCostData, $this->approved_by, $this->id);
                    }
                }
            }
        });
        
        // Reset flag
        $soil->historyLogging = false;
    }

    private function applyInterestChanges()
    {
        $soil = $this->soil;
        if (!$soil) return;
        
        // Set history logging flag to prevent automatic history creation
        $soil->historyLogging = true;
        
        DB::transaction(function () use ($soil) {
            $oldInterests = $soil->biayaTambahanInterestSoils()->get();
            $newInterestsData = $this->new_data;
            
            // Create lookup arrays
            $oldInterestsById = $oldInterests->keyBy('id');
            $newInterestsById = collect($newInterestsData)->keyBy('id')->filter(fn($item) => !empty($item['id']));
            
            // Track all IDs
            $oldIds = $oldInterestsById->keys();
            $newIds = $newInterestsById->keys();
            
            // 1. IDENTIFY DELETED INTERESTS (in old but not in new)
            $deletedIds = $oldIds->diff($newIds);
            foreach ($deletedIds as $deletedId) {
                $interest = $oldInterestsById->get($deletedId);
                $oldInterestData = [
                    'start_date' => $interest->start_date->format('Y-m-d'),
                    'end_date' => $interest->end_date->format('Y-m-d'),
                    'remarks' => $interest->remarks,
                    'harga_perolehan' => $interest->harga_perolehan,
                    'bunga' => $interest->bunga,
                ];
                
                // Log as deleted with approval metadata
                $soil->logInterestHistory('deleted', [], $oldInterestData, $this->approved_by, $this->id);
            }
            
            // Delete interests not in new data
            $soil->biayaTambahanInterestSoils()->whereNotIn('id', $newIds)->delete();
            
            // 2. IDENTIFY ADDED INTERESTS (no ID or new ID)
            $addedInterests = collect($newInterestsData)->filter(function($item) use ($oldIds) {
                return empty($item['id']) || !$oldIds->contains($item['id']);
            });
            
            foreach ($addedInterests as $interestData) {
                if (!empty($interestData['start_date']) && !empty($interestData['end_date'])) {
                    BiayaTambahanInterestSoil::create([
                        'soil_id' => $soil->id,
                        'start_date' => $interestData['start_date'],
                        'end_date' => $interestData['end_date'],
                        'remarks' => $interestData['remarks'],
                        'harga_perolehan' => $interestData['harga_perolehan'],
                        'bunga' => $interestData['bunga'],
                    ]);
                    
                    $newInterestData = [
                        'start_date' => $interestData['start_date'],
                        'end_date' => $interestData['end_date'],
                        'remarks' => $interestData['remarks'],
                        'harga_perolehan' => $interestData['harga_perolehan'],
                        'bunga' => $interestData['bunga'],
                    ];
                    
                    // Log as added with approval metadata
                    $soil->logInterestHistory('added', $newInterestData, [], $this->approved_by, $this->id);
                }
            }
            
            // 3. IDENTIFY UPDATED INTERESTS (in both old and new, but changed)
            foreach ($newIds as $interestId) {
                $oldInterest = $oldInterestsById->get($interestId);
                $newInterestData = $newInterestsById->get($interestId);
                
                if ($oldInterest && $newInterestData) {
                    // Check if anything actually changed
                    $hasChanges = (
                        $oldInterest->start_date->format('Y-m-d') != $newInterestData['start_date'] ||
                        $oldInterest->end_date->format('Y-m-d') != $newInterestData['end_date'] ||
                        $oldInterest->harga_perolehan != $newInterestData['harga_perolehan'] ||
                        $oldInterest->bunga != $newInterestData['bunga'] ||
                        $oldInterest->remarks != $newInterestData['remarks']
                    );
                    
                    if ($hasChanges) {
                        // Update the interest
                        \App\Models\BiayaTambahanInterestSoil::where('id', $interestId)->update([
                            'start_date' => $newInterestData['start_date'],
                            'end_date' => $newInterestData['end_date'],
                            'remarks' => $newInterestData['remarks'],
                            'harga_perolehan' => $newInterestData['harga_perolehan'],
                            'bunga' => $newInterestData['bunga'],
                        ]);
                        
                        $oldInterestData = [
                            'start_date' => $oldInterest->start_date->format('Y-m-d'),
                            'end_date' => $oldInterest->end_date->format('Y-m-d'),
                            'remarks' => $oldInterest->remarks,
                            'harga_perolehan' => $oldInterest->harga_perolehan,
                            'bunga' => $oldInterest->bunga,
                        ];
                        
                        $newInterestDataFormatted = [
                            'start_date' => $newInterestData['start_date'],
                            'end_date' => $newInterestData['end_date'],
                            'remarks' => $newInterestData['remarks'],
                            'harga_perolehan' => $newInterestData['harga_perolehan'],
                            'bunga' => $newInterestData['bunga'],
                        ];
                        
                        // Log as updated with approval metadata
                        $soil->logInterestHistory('updated', $newInterestDataFormatted, $oldInterestData, $this->approved_by, $this->id);
                    }
                }
            }
        });
        
        // Reset flag
        $soil->historyLogging = false;
    }

    private function applyDeletion()
    {
        $soil = $this->soil;
        if (!$soil) {
            throw new \Exception('Soil record not found for deletion approval.');
        }

        // Log deletion history FIRST (before any flags are set)
        $soil->logHistory('approved_deletion', [
            'deletion_reason' => $this->getDeletionReason(),
            'deleted_by_approval_id' => $this->id,
            'approved_by' => $this->approved_by
        ], $this->approved_by, $this->id);

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