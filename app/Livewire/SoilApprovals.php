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
        $query = SoilApproval::with(['soil.land', 'soil.businessUnit', 'requestedBy'])
            ->pending()
            ->orderBy('created_at', 'desc');

        // Filter approvals based on user permissions
        $user = auth()->user();
        $canApproveData = $user->can('soil-data.approval');
        $canApproveCosts = $user->can('soil-data-costs.approval');

        if ($canApproveData && $canApproveCosts) {
            // User can approve both types - show all
            // No additional filtering needed
        } elseif ($canApproveData && !$canApproveCosts) {
            // User can only approve data changes
            $query->where('change_type', 'details');
        } elseif (!$canApproveData && $canApproveCosts) {
            // User can only approve cost changes
            $query->where('change_type', 'costs');
        } else {
            // User has no approval permissions - show nothing
            $query->where('id', '<', 0); // Force empty result
        }
            
        $pendingApprovals = $query->paginate(10);
            
        return view('livewire.soil-approvals.index', compact('pendingApprovals'));
    }
    
    public function approve($approvalId, $reason = null)
    {
        $approval = SoilApproval::findOrFail($approvalId);
        
        // Check if user has permission for this specific approval type
        $canApprove = false;
        if ($approval->change_type === 'details' && auth()->user()->can('soil-data.approval')) {
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
            session()->flash('message', 'Changes approved and applied successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve changes: ' . $e->getMessage());
        }
    }
    
    public function showRejectModal($approvalId)
    {
        $approval = SoilApproval::findOrFail($approvalId);
        
        // Check if user has permission for this specific approval type
        $canReject = false;
        if ($approval->change_type === 'details' && auth()->user()->can('soil-data.approval')) {
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
            if ($approval->change_type === 'details' && auth()->user()->can('soil-data.approval')) {
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
            
            session()->flash('message', 'Changes rejected successfully.');
            $this->hideRejectModal();
        }
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
            'letak_tanah' => 'Land Location',
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
}