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

    public function getFormattedUpdatedAtAttribute()
    {
        if (!$this->updated_at) return null;
        
        return $this->updated_at_gmt7->format('d/m/Y H:i') . ' (GMT+7)';
    }

    // COMPLETE: Get action display name for ALL scenarios
    public function getActionDisplayAttribute()
    {
        $isApproved = $this->isApprovedChange();
        $isRejected = $this->isRejectedChange();
        
        return match($this->action) {
            // Creation actions
            'created' => 'Record Created',
            'approved_creation' => 'Record Created (Approved)',
            'rejected_creation' => 'Record Creation (Rejected)',
            
            // Update actions
            'updated' => 'Record Updated', 
            'approved_update' => 'Record Updated (Approved)',
            'rejected_update' => 'Record Update (Rejected)',
            
            // Deletion actions
            'deleted' => 'Record Deleted',
            'approved_deletion' => 'Record Deleted (Approved)',
            'rejected_deletion' => 'Record Deletion (Rejected)',
            
            // Restoration
            'restored' => 'Record Restored',
            
            // Additional cost actions - Added
            'additional_cost_added' => 'Additional Cost Added' . ($isApproved ? ' (Approved)' : ($isRejected ? ' (Rejected)' : '')),
            
            // Additional cost actions - Updated
            'additional_cost_updated' => 'Additional Cost Updated' . ($isApproved ? ' (Approved)' : ($isRejected ? ' (Rejected)' : '')),
            
            // Additional cost actions - Deleted
            'additional_cost_deleted' => 'Additional Cost Deleted' . ($isApproved ? ' (Approved)' : ($isRejected ? ' (Rejected)' : '')),
            
            // Rejected cost update (standalone)
            'rejected_cost_update' => 'Additional Costs Update (Rejected)',

            // Interest cost actions
            'interest_cost_added' => 'Interest Cost Added' . ($isApproved ? ' (Approved)' : ($isRejected ? ' (Rejected)' : '')),
            'interest_cost_updated' => 'Interest Cost Updated' . ($isApproved ? ' (Approved)' : ($isRejected ? ' (Rejected)' : '')),
            'interest_cost_deleted' => 'Interest Cost Deleted' . ($isApproved ? ' (Approved)' : ($isRejected ? ' (Rejected)' : '')),
            'approved_interest_update' => 'Interest Costs Updated (Approved)',
            'rejected_interest_update' => 'Interest Costs Update (Rejected)',
            
            default => ucfirst(str_replace('_', ' ', $this->action))
        };
    }

    // Get user display name
    public function getUserDisplayAttribute()
    {
        return $this->user ? $this->user->name : 'System';
    }

    // COMPLETE: Get changes summary
    public function getChangesSummaryAttribute()
    {
        // Handle additional cost actions specially
        if (str_contains($this->action, 'additional_cost') || $this->action === 'rejected_cost_update') {
            return $this->getAdditionalCostChangesSummary();
        }

        // Handle interest cost actions specially
        if (str_contains($this->action, 'interest_cost') || $this->action === 'rejected_interest_update' || $this->action === 'approved_interest_update') {
            return $this->getInterestCostChangesSummary();
        }

        // Handle rejected actions
        if ($this->isRejectedChange()) {
            return $this->getRejectedChangesSummary();
        }

        // Check if changes exist, is array, and is not empty
        if (!$this->changes || !is_array($this->changes) || empty($this->changes)) {
            // Try to determine changes from old_values and new_values if available
            if ($this->old_values && $this->new_values && is_array($this->old_values) && is_array($this->new_values)) {
                $actualChanges = [];
                
                // Compare old and new values to find actual changes
                foreach ($this->new_values as $field => $newValue) {
                    // Skip metadata fields
                    if (str_starts_with($field, '_')) continue;
                    
                    $oldValue = $this->old_values[$field] ?? null;
                    
                    // Handle different value types
                    if ($this->valuesAreDifferent($oldValue, $newValue)) {
                        $actualChanges[] = $this->getFieldDisplayName($field);
                    }
                }
                
                if (!empty($actualChanges)) {
                    return implode(', ', $actualChanges);
                }
            }
            
            return 'No specific field changes detected';
        }

        $changesList = [];
        foreach ($this->changes as $field) {
            $changesList[] = $this->getFieldDisplayName($field);
        }

        return implode(', ', $changesList);
    }

    // COMPLETE: Handle additional cost changes summary
    private function getAdditionalCostChangesSummary()
    {
        if ($this->action === 'additional_cost_added') {
            $description = $this->new_values['description'] ?? 
                          $this->new_values['additional_cost_description'] ?? 
                          'Cost item';
            return "Added: {$description}";
        }

        if ($this->action === 'additional_cost_deleted') {
            $description = $this->old_values['description'] ?? 
                          $this->old_values['additional_cost_description'] ?? 
                          'Cost item';
            return "Deleted: {$description}";
        }

        if ($this->action === 'additional_cost_updated') {
            $description = $this->new_values['description'] ?? 
                          $this->new_values['additional_cost_description'] ?? 
                          $this->old_values['description'] ?? 
                          $this->old_values['additional_cost_description'] ?? 
                          'Cost item';
            return "Updated: {$description}";
        }

        if ($this->action === 'rejected_cost_update') {
            $costs = $this->new_values['costs'] ?? [];
            $count = is_array($costs) ? count($costs) : 0;
            return "Rejected changes to {$count} cost item(s)";
        }

        return 'Additional cost modified';
    }

    private function getInterestCostChangesSummary()
    {
        if ($this->action === 'interest_cost_added') {
            $startDate = isset($this->new_values['start_date']) ? \Carbon\Carbon::parse($this->new_values['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($this->new_values['end_date']) ? \Carbon\Carbon::parse($this->new_values['end_date'])->format('d/m/Y') : 'N/A';
            return "Added interest period: {$startDate} to {$endDate}";
        }

        if ($this->action === 'interest_cost_deleted') {
            $startDate = isset($this->old_values['start_date']) ? \Carbon\Carbon::parse($this->old_values['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($this->old_values['end_date']) ? \Carbon\Carbon::parse($this->old_values['end_date'])->format('d/m/Y') : 'N/A';
            return "Deleted interest period: {$startDate} to {$endDate}";
        }

        if ($this->action === 'interest_cost_updated') {
            $startDate = isset($this->new_values['start_date']) ? \Carbon\Carbon::parse($this->new_values['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($this->new_values['end_date']) ? \Carbon\Carbon::parse($this->new_values['end_date'])->format('d/m/Y') : 'N/A';
            return "Updated interest period: {$startDate} to {$endDate}";
        }

        if (in_array($this->action, ['rejected_interest_update', 'approved_interest_update'])) {
            $interests = $this->new_values['interests'] ?? [];
            $count = is_array($interests) ? count($interests) : 0;
            $status = $this->action === 'rejected_interest_update' ? 'Rejected' : 'Approved';
            return "{$status} changes to {$count} interest period(s)";
        }

        return 'Interest cost modified';
    }

    // NEW: Handle rejected changes summary
    private function getRejectedChangesSummary()
    {
        if ($this->action === 'rejected_creation') {
            $sellerName = $this->new_values['nama_penjual'] ?? 'Unknown Seller';
            return "Rejected creation request for: {$sellerName}";
        }

        if ($this->action === 'rejected_deletion') {
            return "Rejected deletion request";
        }

        if ($this->action === 'rejected_update') {
            // Get changed fields from new_values
            $newValues = $this->new_values;
            if (isset($newValues['_rejection_metadata'])) {
                unset($newValues['_rejection_metadata']);
            }
            
            if (!empty($this->changes)) {
                $fieldsList = array_map([$this, 'getFieldDisplayName'], $this->changes);
                return "Rejected changes to: " . implode(', ', $fieldsList);
            }
            
            if (!empty($newValues) && !empty($this->old_values)) {
                $changedFields = [];
                foreach ($newValues as $field => $newValue) {
                    if (str_starts_with($field, '_')) continue;
                    $oldValue = $this->old_values[$field] ?? null;
                    if ($this->valuesAreDifferent($oldValue, $newValue)) {
                        $changedFields[] = $this->getFieldDisplayName($field);
                    }
                }
                if (!empty($changedFields)) {
                    return "Rejected changes to: " . implode(', ', $changedFields);
                }
            }
            
            return "Rejected update request";
        }

        if ($this->action === 'rejected_cost_update') {
            $costs = $this->new_values['costs'] ?? [];
            $count = is_array($costs) ? count($costs) : 0;
            return "Rejected cost changes ({$count} items)";
        }

        return 'Rejected change';
    }

    // Helper method to compare values properly
    private function valuesAreDifferent($oldValue, $newValue)
    {
        // Handle null values
        if (is_null($oldValue) && is_null($newValue)) {
            return false;
        }
        
        if (is_null($oldValue) || is_null($newValue)) {
            return true;
        }
        
        // Handle empty strings
        if ($oldValue === '' && $newValue === '') {
            return false;
        }
        
        // Convert to string for comparison to handle different data types
        return (string) $oldValue !== (string) $newValue;
    }

    // Helper method to get human readable field names
    private function getFieldDisplayName($field)
    {
        $fieldMap = [
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
            'nama_notaris_ppat' => 'Notaris/PPAT Name',
            'keterangan' => 'Notes',
            'land_id' => 'Land',
            'business_unit_id' => 'Business Unit',
            'description' => 'Description',
            'cost_type' => 'Cost Type',
            'date_cost' => 'Cost Date',
            'costs' => 'Additional Costs',
            'deletion_reason' => 'Deletion Reason',
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

    // COMPLETE: Check if this is an approved change
    public function isApprovedChange()
    {
        // Check action name first
        if (in_array($this->action, [
            'approved_update', 
            'approved_deletion', 
            'approved_creation',
            'additional_cost_approved'
        ])) {
            return true;
        }
        
        // Check metadata in new_values
        if ($this->new_values && isset($this->new_values['_approval_metadata'])) {
            return true;
        }
        
        return false;
    }

    // NEW: Check if this is a rejected change
    public function isRejectedChange()
    {
        // Check action name
        if (in_array($this->action, [
            'rejected_update', 
            'rejected_deletion', 
            'rejected_creation',
            'rejected_cost_update'
        ])) {
            return true;
        }
        
        // Check metadata in new_values
        if ($this->new_values && isset($this->new_values['_rejection_metadata'])) {
            return true;
        }
        
        return false;
    }

    // Get approval metadata
    public function getApprovalMetadata()
    {
        if ($this->new_values && isset($this->new_values['_approval_metadata'])) {
            return $this->new_values['_approval_metadata'];
        }
        return null;
    }

    // NEW: Get rejection metadata
    public function getRejectionMetadata()
    {
        if ($this->new_values && isset($this->new_values['_rejection_metadata'])) {
            return $this->new_values['_rejection_metadata'];
        }
        return null;
    }

    // NEW: Get rejector user
    public function getRejectorUser()
    {
        $metadata = $this->getRejectionMetadata();
        if ($metadata && isset($metadata['rejected_by'])) {
            return \App\Models\User::find($metadata['rejected_by']);
        }
        return null;
    }

    // NEW: Get rejection reason
    public function getRejectionReason()
    {
        $metadata = $this->getRejectionMetadata();
        if ($metadata && isset($metadata['rejection_reason'])) {
            return $metadata['rejection_reason'];
        }
        return null;
    }

    // NEW: Get approval ID
    public function getApprovalId()
    {
        $approvalMetadata = $this->getApprovalMetadata();
        if ($approvalMetadata && isset($approvalMetadata['approval_id'])) {
            return $approvalMetadata['approval_id'];
        }
        
        $rejectionMetadata = $this->getRejectionMetadata();
        if ($rejectionMetadata && isset($rejectionMetadata['approval_id'])) {
            return $rejectionMetadata['approval_id'];
        }
        
        return null;
    }

    // NEW: Get approver user
    public function getApproverUser()
    {
        $metadata = $this->getApprovalMetadata();
        if ($metadata && isset($metadata['approved_by'])) {
            return \App\Models\User::find($metadata['approved_by']);
        }
        return null;
    }
}