<?php

namespace App\Livewire;

use App\Models\SoilApproval;
use Livewire\Component;
use Livewire\WithPagination;

class SoilApprovals extends Component
{
    use WithPagination;
    
    public $showDetails = [];
    public $rejectionReason = '';
    public $rejectionApprovalId = null;
    public $showRejectionModal = false;
    
    public function mount()
    {
        // Initialize show details array
        $this->showDetails = [];
    }
    
    public function toggleDetails($approvalId)
    {
        if (isset($this->showDetails[$approvalId])) {
            $this->showDetails[$approvalId] = !$this->showDetails[$approvalId];
        } else {
            $this->showDetails[$approvalId] = true;
        }
    }
    
    public function hideRejectModal()
    {
        $this->rejectionApprovalId = null;
        $this->rejectionReason = '';
        $this->showRejectionModal = false;
    }
    
    public function render()
    {
        $query = SoilApproval::with(['requestedBy'])
            ->leftJoin('soils', 'soil_approvals.soil_id', '=', 'soils.id')
            ->leftJoin('lands', 'soils.land_id', '=', 'lands.id') 
            ->leftJoin('business_units', 'soils.business_unit_id', '=', 'business_units.id')
            ->select('soil_approvals.*') // Make sure to select only approval fields
            ->with(['soil.land', 'soil.businessUnit']) // Still eager load relationships
            ->pending()
            ->orderBy('soil_approvals.created_at', 'desc');

        // Filter approvals based on user permissions
        $user = auth()->user();
        $canApproveData = $user->can('soil-data.approval');
        $canApproveCosts = $user->can('soil-data-costs.approval');

        if ($canApproveData && $canApproveCosts) {
            // User can approve both types - show all
            // No additional filtering needed
        } elseif ($canApproveData && !$canApproveCosts) {
            // User can only approve data changes, creations, and deletions
            $query->whereIn('soil_approvals.change_type', ['details', 'delete', 'create']);
        } elseif (!$canApproveData && $canApproveCosts) {
            // User can only approve cost changes
            $query->where('soil_approvals.change_type', 'costs');
        } else {
            // User has no approval permissions - show nothing
            $query->where('soil_approvals.id', '<', 0); // Force empty result
        }
            
        $pendingApprovals = $query->paginate(10);
            
        return view('livewire.soil-approvals.index', compact('pendingApprovals'));
    }
    
    public function approve($approvalId, $reason = null)
    {
        $approval = SoilApproval::findOrFail($approvalId);
        
        // Check if user has permission for this specific approval type
        $canApprove = false;
        if (($approval->change_type === 'details' || $approval->change_type === 'delete' || $approval->change_type === 'create') && auth()->user()->can('soil-data.approval')) {
            $canApprove = true;
        } elseif ($approval->change_type === 'costs' && auth()->user()->can('soil-data-costs.approval')) {
            $canApprove = true;
        }
        
        if (!$canApprove) {
            session()->flash('error', 'You do not have permission to approve this type of change.');
            return;
        }
        
        try {
            $approval->approve($reason);
            
            if ($approval->change_type === 'delete') {
                session()->flash('message', 'Deletion approved and soil record has been deleted successfully.');
            } elseif ($approval->change_type === 'create') {
                session()->flash('message', 'Creation approved and new soil record has been created successfully.');
            } else {
                session()->flash('message', 'Changes approved and applied successfully.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve changes: ' . $e->getMessage());
        }
    }
    
    public function showRejectModal($approvalId)
    {
        $approval = SoilApproval::findOrFail($approvalId);
        
        // Check if user has permission for this specific approval type
        $canReject = false;
        if (($approval->change_type === 'details' || $approval->change_type === 'delete' || $approval->change_type === 'create') && auth()->user()->can('soil-data.approval')) {
            $canReject = true;
        } elseif ($approval->change_type === 'costs' && auth()->user()->can('soil-data-costs.approval')) {
            $canReject = true;
        }
        
        if (!$canReject) {
            session()->flash('error', 'You do not have permission to reject this type of change.');
            return;
        }
        
        $this->rejectionApprovalId = $approvalId;
        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }
    
    public function rejectWithReason()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10|max:500'
        ], [
            'rejectionReason.required' => 'Please provide a reason for rejection.',
            'rejectionReason.min' => 'Rejection reason must be at least 10 characters.',
            'rejectionReason.max' => 'Rejection reason cannot exceed 500 characters.'
        ]);
        
        if ($this->rejectionApprovalId) {
            $approval = SoilApproval::findOrFail($this->rejectionApprovalId);
            
            // Double-check permission before rejecting
            $canReject = false;
            if (($approval->change_type === 'details' || $approval->change_type === 'delete' || $approval->change_type === 'create') && auth()->user()->can('soil-data.approval')) {
                $canReject = true;
            } elseif ($approval->change_type === 'costs' && auth()->user()->can('soil-data-costs.approval')) {
                $canReject = true;
            }
            
            if (!$canReject) {
                session()->flash('error', 'You do not have permission to reject this type of change.');
                $this->hideRejectModal();
                return;
            }
            
            $approval->reject($this->rejectionReason);
            
            if ($approval->change_type === 'delete') {
                session()->flash('message', 'Deletion request rejected successfully.');
            } elseif ($approval->change_type === 'create') {
                session()->flash('message', 'Creation request rejected successfully.');
            } else {
                session()->flash('message', 'Changes rejected successfully.');
            }
            
            $this->hideRejectModal();
        }
    }

    private function getCreateChangeDetails($approval)
    {
        $details = [];
        $newData = $approval->new_data;
        
        if (!$newData) {
            return [];
        }
        
        $fieldLabels = [
            'land_id' => 'Land',
            'business_unit_id' => 'Business Unit',
            'nama_penjual' => 'Seller Name',
            'alamat_penjual' => 'Seller Address',
            'nomor_ppjb' => 'PPJB Number',
            'tanggal_ppjb' => 'PPJB Date',
            'letak_tanah' => 'Soil Location',
            'luas' => 'Area (mÂ²)',
            'harga' => 'Price',
            'bukti_kepemilikan' => 'Ownership Proof',
            'bukti_kepemilikan_details' => 'Ownership Details',
            'atas_nama' => 'Owner Name',
            'nop_pbb' => 'PBB Number',
            'nama_notaris_ppat' => 'Notary Name',
            'keterangan' => 'Notes'
        ];
        
        foreach ($newData as $field => $value) {
            if ($value !== null && $value !== '') {
                $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                
                if (in_array($field, ['luas', 'harga'])) {
                    $details[] = [
                        'field' => $label,
                        'value' => number_format($value, 0, ',', '.')
                    ];
                } elseif (in_array($field, ['land_id', 'business_unit_id'])) {
                    // For foreign keys, resolve to names if possible
                    if ($field === 'land_id') {
                        $land = \App\Models\Land::find($value);
                        $details[] = [
                            'field' => $label,
                            'value' => $land ? $land->lokasi_lahan : "ID: {$value}"
                        ];
                    } elseif ($field === 'business_unit_id') {
                        $businessUnit = \App\Models\BusinessUnit::find($value);
                        $details[] = [
                            'field' => $label,
                            'value' => $businessUnit ? $businessUnit->name : "ID: {$value}"
                        ];
                    }
                } else {
                    $details[] = [
                        'field' => $label,
                        'value' => $value
                    ];
                }
            }
        }
        
        return $details;
    }
    
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
    
    public function getChangeDetails($approval)
    {
        if ($approval->change_type === 'details') {
            return $this->getDetailChangeDetails($approval);
        } elseif ($approval->change_type === 'costs') {
            return $this->getCostChangeDetails($approval);
        } elseif ($approval->change_type === 'delete') {
            return $this->getDeleteChangeDetails($approval);
        } elseif ($approval->change_type === 'create') {
            return $this->getCreateChangeDetails($approval);
        }
        
        return [];
    }
    
    private function getDetailChangeDetails($approval)
    {
        $changes = [];
        $oldData = $approval->old_data;
        $newData = $approval->new_data;
        
        $fieldLabels = [
            'land_id' => 'Land',
            'business_unit_id' => 'Business Unit',
            'nama_penjual' => 'Seller Name',
            'alamat_penjual' => 'Seller Address',
            'nomor_ppjb' => 'PPJB Number',
            'tanggal_ppjb' => 'PPJB Date',
            'letak_tanah' => 'Soil Location',
            'luas' => 'Area (m²)',
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
                    $changes[] = [
                        'field' => $label,
                        'old' => $oldFormatted,
                        'new' => $newFormatted
                    ];
                } elseif (in_array($field, ['land_id', 'business_unit_id'])) {
                    // For foreign keys, you might want to resolve to names
                    $changes[] = [
                        'field' => $label,
                        'old' => $oldData[$field],
                        'new' => $newValue
                    ];
                } else {
                    $changes[] = [
                        'field' => $label,
                        'old' => $oldData[$field],
                        'new' => $newValue
                    ];
                }
            }
        }
        
        return $changes;
    }

    private function getDeleteChangeDetails($approval)
    {
        $details = [];
        $soil = $approval->soil;
        $deletionReason = $approval->getDeletionReason();
        
        if ($soil) {
            $details[] = [
                'field' => 'Record Details',
                'value' => "Seller: {$soil->nama_penjual}, Location: {$soil->letak_tanah}, PPJB: {$soil->nomor_ppjb}"
            ];
            
            $details[] = [
                'field' => 'Area & Price',
                'value' => number_format($soil->luas, 0, ',', '.') . ' m² - ' . $this->formatCurrency($soil->harga)
            ];
            
            if ($soil->biayaTambahanSoils->count() > 0) {
                $totalCosts = $soil->biayaTambahanSoils->sum('harga');
                $details[] = [
                    'field' => 'Additional Costs',
                    'value' => $soil->biayaTambahanSoils->count() . ' items - ' . $this->formatCurrency($totalCosts)
                ];
            }
        }
        
        if ($deletionReason) {
            $details[] = [
                'field' => 'Deletion Reason',
                'value' => $deletionReason
            ];
        }
        
        return $details;
    }
    
    private function getCostChangeDetails($approval)
    {
        $changes = [];
        $oldData = $approval->old_data ?? [];
        $newData = $approval->new_data ?? [];
        
        // Create lookup arrays for easier comparison
        $oldCostsById = collect($oldData)->keyBy('id');
        $newCostsById = collect($newData)->keyBy('id');
        
        // Track all cost IDs from both old and new data
        $allCostIds = collect($oldData)->pluck('id')->merge(collect($newData)->pluck('id'))->filter()->unique();
        
        // Check for new costs (costs without an ID in new data)
        $newCostsWithoutId = collect($newData)->filter(function ($cost) {
            return empty($cost['id']) || is_null($cost['id']);
        });
        
        foreach ($newCostsWithoutId as $newCost) {
            $changes[] = [
                'type' => 'added',
                'description' => $newCost['description'] ?? 'Unknown Description',
                'cost_type' => ucfirst(str_replace('_', ' ', $newCost['cost_type'] ?? 'standard')),
                'date_cost' => $newCost['date_cost'] ?? '',
                'amount' => $this->formatCurrency($newCost['harga'] ?? 0),
                'old_amount' => null
            ];
        }
        
        // Check for modifications and deletions
        foreach ($allCostIds as $costId) {
            $oldCost = $oldCostsById->get($costId);
            $newCost = $newCostsById->get($costId);
            
            if ($oldCost && $newCost) {
                // MODIFIED COST - check if any field has changed
                $hasChanges = false;
                $changeDetails = [];
                
                if ($oldCost['description_id'] != $newCost['description_id'] || 
                    $oldCost['description'] != $newCost['description']) {
                    $hasChanges = true;
                    $changeDetails['description'] = [
                        'old' => $oldCost['description'] ?? 'Unknown',
                        'new' => $newCost['description'] ?? 'Unknown'
                    ];
                }
                
                if ($oldCost['harga'] != $newCost['harga']) {
                    $hasChanges = true;
                    $changeDetails['amount'] = [
                        'old' => $this->formatCurrency($oldCost['harga']),
                        'new' => $this->formatCurrency($newCost['harga'])
                    ];
                }
                
                if ($oldCost['cost_type'] != $newCost['cost_type']) {
                    $hasChanges = true;
                    $changeDetails['cost_type'] = [
                        'old' => ucfirst(str_replace('_', ' ', $oldCost['cost_type'])),
                        'new' => ucfirst(str_replace('_', ' ', $newCost['cost_type']))
                    ];
                }
                
                if ($oldCost['date_cost'] != $newCost['date_cost']) {
                    $hasChanges = true;
                    $changeDetails['date'] = [
                        'old' => $oldCost['date_cost'],
                        'new' => $newCost['date_cost']
                    ];
                }
                
                if ($hasChanges) {
                    $changes[] = [
                        'type' => 'modified',
                        'description' => $newCost['description'] ?? $oldCost['description'] ?? 'Unknown Description',
                        'changes' => $changeDetails
                    ];
                }
                
            } elseif ($oldCost && !$newCost) {
                // DELETED COST
                $changes[] = [
                    'type' => 'deleted',
                    'description' => $oldCost['description'] ?? 'Unknown Description',
                    'cost_type' => ucfirst(str_replace('_', ' ', $oldCost['cost_type'] ?? 'standard')),
                    'date_cost' => $oldCost['date_cost'] ?? '',
                    'amount' => $this->formatCurrency($oldCost['harga'] ?? 0)
                ];
            }
        }
        
        return $changes;
    }
    
    // Helper method to get cost change summary for display
    public function getCostChangeSummary($approval)
    {
        $changes = $this->getCostChangeDetails($approval);
        $summary = [
            'added' => 0,
            'modified' => 0,
            'deleted' => 0,
            'total_changes' => count($changes)
        ];
        
        foreach ($changes as $change) {
            $summary[$change['type']]++;
        }
        
        return $summary;
    }
    
    // Helper method to calculate total cost difference
    public function getCostDifference($approval)
    {
        $oldData = $approval->old_data ?? [];
        $newData = $approval->new_data ?? [];
        
        $oldTotal = collect($oldData)->sum('harga');
        $newTotal = collect($newData)->sum('harga');
        
        return [
            'old_total' => $this->formatCurrency($oldTotal),
            'new_total' => $this->formatCurrency($newTotal),
            'difference' => $this->formatCurrency($newTotal - $oldTotal),
            'difference_raw' => $newTotal - $oldTotal
        ];
    }

    // NEW: Get approval information for display
    public function getApprovalInfo($history)
    {
        if (!$history->isApprovedChange()) {
            return null;
        }

        $approvalMetadata = $history->getApprovalMetadata();
        if (!$approvalMetadata || !isset($approvalMetadata['approved_by'])) {
            return null;
        }

        $approver = \App\Models\User::find($approvalMetadata['approved_by']);
        if (!$approver) {
            return null;
        }

        return [
            'approver_name' => $approver->name,
            'approval_id' => $approvalMetadata['approval_id'] ?? null,
            'is_approved_change' => $approvalMetadata['is_approved_change'] ?? false
        ];
    }

    // Helper method to check if history entry should show approval badge
    public function shouldShowApprovalBadge($history)
    {
        return in_array($history->action, [
            'approved_update', 
            'approved_deletion', 
            'additional_cost_approved'
        ]) || $history->isApprovedChange();
    }

    // Helper method to get the appropriate CSS classes for history action
    public function getActionClasses($history)
    {
        if ($history->isApprovedChange()) {
            return [
                'border' => 'border-green-200',
                'bg' => 'bg-green-50',
                'text' => 'text-green-600',
                'badge_bg' => 'bg-green-100',
                'badge_text' => 'text-green-800'
            ];
        }

        return match($history->action) {
            'created' => [
                'border' => 'border-green-200',
                'bg' => 'bg-green-50', 
                'text' => 'text-green-600',
                'badge_bg' => 'bg-green-100',
                'badge_text' => 'text-green-800'
            ],
            'updated' => [
                'border' => 'border-blue-200',
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-600', 
                'badge_bg' => 'bg-blue-100',
                'badge_text' => 'text-blue-800'
            ],
            'deleted' => [
                'border' => 'border-red-200',
                'bg' => 'bg-red-50',
                'text' => 'text-red-600',
                'badge_bg' => 'bg-red-100', 
                'badge_text' => 'text-red-800'
            ],
            default => [
                'border' => 'border-orange-200',
                'bg' => 'bg-orange-50',
                'text' => 'text-orange-600',
                'badge_bg' => 'bg-orange-100',
                'badge_text' => 'text-orange-800'
            ]
        };
    }
}