<?php

namespace App\Livewire;

use App\Models\LandApproval;
use App\Models\SoilApproval;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

class MergedApprovals extends Component
{
    use WithPagination;

    public $showDetails = [];
    public $selectedApprovals = [];
    public $selectAll = false;
    public $filterType = 'all'; // 'all', 'land', 'soil'
    public $showRejectionModal = false;
    public $rejectionReason = '';
    public $bulkAction = false;
    
    protected $listeners = ['refreshApprovals' => '$refresh'];

    public function render()
    {
        $approvals = $this->getMergedApprovals();

        return view('livewire.merged-approvals.index', compact('approvals'));
    }

    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    private function getMergedApprovals()
    {
        $landApprovals = collect();
        $soilApprovals = collect();

        // Get Land Approvals if user has permission
        if (auth()->user()->can('land-data.approval') && in_array($this->filterType, ['all', 'land'])) {
            $landApprovals = LandApproval::with(['land', 'requestedBy', 'approvedBy'])
                ->pending()
                ->latest()
                ->get()
                ->map(function($approval) {
                    $approval->approval_type = 'land';
                    $approval->unique_id = 'land_' . $approval->id;
                    return $approval;
                });
        }

        // Get Soil Approvals if user has permission
        if (in_array($this->filterType, ['all', 'soil'])) {
            $query = SoilApproval::with(['soil.land', 'soil.businessUnit', 'requestedBy', 'approvedBy'])
                ->pending()
                ->latest();

            // Filter based on user permissions
            $user = auth()->user();
            $canApproveData = $user->can('soil-data.approval');
            $canApproveCosts = $user->can('soil-data-costs.approval');

            if ($canApproveData && $canApproveCosts) {
                // Show all
            } elseif ($canApproveData && !$canApproveCosts) {
                $query->whereIn('change_type', ['details', 'delete', 'create']);
            } elseif (!$canApproveData && $canApproveCosts) {
                $query->where('change_type', 'costs');
            } else {
                $query->where('id', '<', 0); // Show nothing
            }

            $soilApprovals = $query->get()->map(function($approval) {
                $approval->approval_type = 'soil';
                $approval->unique_id = 'soil_' . $approval->id;
                return $approval;
            });
        }

        // Merge and sort by created_at
        return $landApprovals->concat($soilApprovals)
            ->sortByDesc('created_at')
            ->values();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $approvals = $this->getMergedApprovals();
            $this->selectedApprovals = $approvals->pluck('unique_id')->toArray();
        } else {
            $this->selectedApprovals = [];
        }
    }

    public function updatedSelectedApprovals()
    {
        $approvals = $this->getMergedApprovals();
        $this->selectAll = count($this->selectedApprovals) === $approvals->count();
    }

    public function toggleDetails($uniqueId)
    {
        $this->showDetails[$uniqueId] = !($this->showDetails[$uniqueId] ?? false);
    }

    public function bulkApprove()
    {
        if (empty($this->selectedApprovals)) {
            session()->flash('error', 'Please select at least one approval to process.');
            return;
        }

        $successCount = 0;
        $errors = [];

        foreach ($this->selectedApprovals as $uniqueId) {
            try {
                [$type, $id] = explode('_', $uniqueId);
                
                if ($type === 'land') {
                    $approval = LandApproval::find($id);
                    if ($approval && auth()->user()->can('land-data.approval')) {
                        $approval->approve(auth()->id());
                        $successCount++;
                    }
                } elseif ($type === 'soil') {
                    $approval = SoilApproval::find($id);
                    if ($approval) {
                        $canApprove = false;
                        if (in_array($approval->change_type, ['details', 'delete', 'create']) && auth()->user()->can('soil-data.approval')) {
                            $canApprove = true;
                        } elseif (in_array($approval->change_type, ['costs', 'interest']) && auth()->user()->can('soil-data-costs.approval')) {
                            $canApprove = true;
                        }
                        
                        if ($canApprove) {
                            $approval->approve();
                            $successCount++;
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to approve {$uniqueId}: " . $e->getMessage();
            }
        }

        $this->selectedApprovals = [];
        $this->selectAll = false;

        if ($successCount > 0) {
            session()->flash('message', "{$successCount} approval(s) processed successfully.");

            $this->dispatch('reload-page');
        }
        
        if (!empty($errors)) {
            session()->flash('error', implode(' ', $errors));
        }
    }

    public function showBulkRejectModal()
    {
        if (empty($this->selectedApprovals)) {
            session()->flash('error', 'Please select at least one approval to reject.');
            return;
        }
        
        $this->bulkAction = true;
        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }

    public function showRejectModal($uniqueId)
    {
        $this->selectedApprovals = [$uniqueId];
        $this->bulkAction = false;
        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }

    public function hideRejectModal()
    {
        $this->showRejectionModal = false;
        if (!$this->bulkAction) {
            $this->selectedApprovals = [];
        }
        $this->rejectionReason = '';
        $this->resetValidation();
    }

    public function rejectSelected()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5',
        ], [
            'rejectionReason.required' => 'Please provide a reason for rejection.',
            'rejectionReason.min' => 'Reason must be at least 5 characters.',
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($this->selectedApprovals as $uniqueId) {
            try {
                [$type, $id] = explode('_', $uniqueId);
                
                if ($type === 'land') {
                    $approval = LandApproval::find($id);
                    if ($approval && auth()->user()->can('land-data.approval')) {
                        $approval->reject(auth()->id(), $this->rejectionReason);
                        $successCount++;
                    }
                } elseif ($type === 'soil') {
                    $approval = SoilApproval::find($id);
                    if ($approval) {
                        $canReject = false;
                        if (in_array($approval->change_type, ['details', 'delete', 'create']) && auth()->user()->can('soil-data.approval')) {
                            $canReject = true;
                        } elseif (in_array($approval->change_type, ['costs']) && auth()->user()->can('soil-data-costs.approval')) {
                            $canReject = true;
                        } elseif (in_array($approval->change_type, ['interest']) && auth()->user()->can('soil-data-interest-costs.approval')) {
                            $canReject = true;
                        }
                        
                        if ($canReject) {
                            $approval->reject($this->rejectionReason);
                            $successCount++;
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to reject {$uniqueId}: " . $e->getMessage();
            }
        }

        $this->hideRejectModal();
        $this->selectedApprovals = [];
        $this->selectAll = false;

        if ($successCount > 0) {
            session()->flash('message', "{$successCount} approval(s) rejected successfully.");

            $this->dispatch('reload-page');
        }
        
        if (!empty($errors)) {
            session()->flash('error', implode(' ', $errors));
        }
    }

    public function getChangeDetails($approval)
    {
        if ($approval->approval_type === 'land') {
            return $this->getLandChangeDetails($approval);
        } elseif ($approval->change_type === 'interest') {
            return $this->getInterestChangeDetails($approval);
        } else {
            return $this->getSoilChangeDetails($approval);
        }
    }

    public function getInterestChangeDetails($approval)
    {
        $changes = [];
        $oldData = $approval->old_data ?? [];
        $newData = $approval->new_data ?? [];
        
        $oldInterestsById = collect($oldData)->keyBy('id');
        $newInterestsById = collect($newData)->keyBy('id')->filter(fn($item) => !empty($item['id']));
        
        $allInterestIds = collect($oldData)->pluck('id')->merge(collect($newData)->pluck('id'))->filter()->unique();
        
        // Check for new interests
        $newInterestsWithoutId = collect($newData)->filter(function ($interest) {
            return empty($interest['id']) || is_null($interest['id']);
        });
        
        foreach ($newInterestsWithoutId as $newInterest) {
            $startDate = isset($newInterest['start_date']) ? \Carbon\Carbon::parse($newInterest['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($newInterest['end_date']) ? \Carbon\Carbon::parse($newInterest['end_date'])->format('d/m/Y') : 'N/A';
            $days = 0;
            if (isset($newInterest['start_date']) && isset($newInterest['end_date'])) {
                $days = \Carbon\Carbon::parse($newInterest['start_date'])->diffInDays(\Carbon\Carbon::parse($newInterest['end_date']));
            }
            
            $changes[] = [
                'type' => 'added',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => $days,
                'harga_perolehan' => $this->formatCurrency($newInterest['harga_perolehan'] ?? 0),
                'bunga' => number_format($newInterest['bunga'] ?? 0, 2),
                'remarks' => $newInterest['remarks'] ?? '',
            ];
        }
        
        // Check for modifications and deletions
        foreach ($allInterestIds as $interestId) {
            $oldInterest = $oldInterestsById->get($interestId);
            $newInterest = $newInterestsById->get($interestId);
            
            if ($oldInterest && $newInterest) {
                $hasChanges = false;
                $changeDetails = [];
                
                if ($oldInterest['start_date'] != $newInterest['start_date']) {
                    $hasChanges = true;
                    $changeDetails['start_date'] = [
                        'old' => \Carbon\Carbon::parse($oldInterest['start_date'])->format('d/m/Y'),
                        'new' => \Carbon\Carbon::parse($newInterest['start_date'])->format('d/m/Y')
                    ];
                }
                
                if ($oldInterest['end_date'] != $newInterest['end_date']) {
                    $hasChanges = true;
                    $changeDetails['end_date'] = [
                        'old' => \Carbon\Carbon::parse($oldInterest['end_date'])->format('d/m/Y'),
                        'new' => \Carbon\Carbon::parse($newInterest['end_date'])->format('d/m/Y')
                    ];
                }
                
                if ($oldInterest['harga_perolehan'] != $newInterest['harga_perolehan']) {
                    $hasChanges = true;
                    $changeDetails['harga_perolehan'] = [
                        'old' => $this->formatCurrency($oldInterest['harga_perolehan']),
                        'new' => $this->formatCurrency($newInterest['harga_perolehan'])
                    ];
                }
                
                if ($oldInterest['bunga'] != $newInterest['bunga']) {
                    $hasChanges = true;
                    $changeDetails['interest_rate'] = [
                        'old' => number_format($oldInterest['bunga'], 2) . '%',
                        'new' => number_format($newInterest['bunga'], 2) . '%'
                    ];
                }
                
                if (($oldInterest['remarks'] ?? '') != ($newInterest['remarks'] ?? '')) {
                    $hasChanges = true;
                    $changeDetails['remarks'] = [
                        'old' => $oldInterest['remarks'] ?? '-',
                        'new' => $newInterest['remarks'] ?? '-'
                    ];
                }
                
                if ($hasChanges) {
                    $startDate = \Carbon\Carbon::parse($newInterest['start_date'])->format('d/m/Y');
                    $endDate = \Carbon\Carbon::parse($newInterest['end_date'])->format('d/m/Y');
                    
                    $changes[] = [
                        'type' => 'modified',
                        'period' => "{$startDate} - {$endDate}",
                        'changes' => $changeDetails
                    ];
                }
                
            } elseif ($oldInterest && !$newInterest) {
                $startDate = \Carbon\Carbon::parse($oldInterest['start_date'])->format('d/m/Y');
                $endDate = \Carbon\Carbon::parse($oldInterest['end_date'])->format('d/m/Y');
                $days = \Carbon\Carbon::parse($oldInterest['start_date'])->diffInDays(\Carbon\Carbon::parse($oldInterest['end_date']));
                
                $changes[] = [
                    'type' => 'deleted',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'days' => $days,
                    'harga_perolehan' => $this->formatCurrency($oldInterest['harga_perolehan'] ?? 0),
                    'bunga' => number_format($oldInterest['bunga'] ?? 0, 2),
                    'remarks' => $oldInterest['remarks'] ?? '',
                ];
            }
        }
        
        return $changes;
    }

    public function getInterestChangeSummary($approval)
    {
        $changes = $this->getInterestChangeDetails($approval);
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

    private function getLandChangeDetails($approval)
    {
        // Reuse logic from LandApprovals component
        $details = [];

        if (in_array($approval->change_type, ['updated', 'details'])) {
            $oldData = $approval->old_data ?? [];
            $newData = $approval->new_data ?? [];

            foreach ($newData as $key => $newValue) {
                $oldValue = $oldData[$key] ?? '';
                if ($oldValue != $newValue) {
                    $details[] = [
                        'field' => $this->formatLandFieldName($key),
                        'old' => $this->formatLandValue($key, $oldValue),
                        'new' => $this->formatLandValue($key, $newValue),
                    ];
                }
            }
        } elseif ($approval->change_type === 'delete') {
            $oldData = $approval->old_data ?? [];
            foreach ($oldData as $key => $value) {
                if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                    $details[] = [
                        'field' => $this->formatLandFieldName($key),
                        'value' => $this->formatLandValue($key, $value),
                    ];
                }
            }
        } elseif ($approval->change_type === 'create') {
            $newData = $approval->new_data ?? [];
            foreach ($newData as $key => $value) {
                if (!in_array($key, ['created_at', 'updated_at'])) {
                    $details[] = [
                        'field' => $this->formatLandFieldName($key),
                        'value' => $this->formatLandValue($key, $value),
                    ];
                }
            }
        }

        return $details;
    }

    private function getSoilChangeDetails($approval)
    {
        // Reuse logic from SoilApprovals component
        if ($approval->change_type === 'details') {
            return $this->getSoilDetailChanges($approval);
        } elseif ($approval->change_type === 'costs') {
            return $this->getSoilCostChangeDetails($approval);
        } elseif ($approval->change_type === 'delete') {
            return $this->getSoilDeleteChanges($approval);
        } elseif ($approval->change_type === 'create') {
            return $this->getSoilCreateChanges($approval);
        }
        
        return [];
    }

    private function getSoilCostChangeDetails($approval)
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
        $changes = $this->getSoilCostChangeDetails($approval);
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

    private function getSoilDetailChanges($approval)
    {
        $changes = [];
        $oldData = $approval->old_data;
        $newData = $approval->new_data;
        
        foreach ($newData as $field => $newValue) {
            if (isset($oldData[$field]) && $oldData[$field] != $newValue) {
                $changes[] = [
                    'field' => $this->formatSoilFieldName($field),
                    'old' => $this->formatSoilValue($field, $oldData[$field]),
                    'new' => $this->formatSoilValue($field, $newValue)
                ];
            }
        }
        
        return $changes;
    }

    private function getSoilCostChanges($approval)
    {
        // Return summary for display
        $oldData = $approval->old_data ?? [];
        $newData = $approval->new_data ?? [];
        
        return [
            'type' => 'costs',
            'old_count' => count($oldData),
            'new_count' => count($newData),
            'old_total' => collect($oldData)->sum('harga'),
            'new_total' => collect($newData)->sum('harga'),
        ];
    }

    private function getSoilDeleteChanges($approval)
    {
        $details = [];
        $oldData = $approval->old_data;
        
        if ($oldData) {
            $details[] = ['field' => 'Seller Name', 'value' => $oldData['nama_penjual'] ?? 'N/A'];
            $details[] = ['field' => 'Location', 'value' => $oldData['letak_tanah'] ?? 'N/A'];
            $details[] = ['field' => 'PPJB Number', 'value' => $oldData['nomor_ppjb'] ?? 'N/A'];
        }
        
        if (isset($approval->new_data['deletion_reason'])) {
            $details[] = ['field' => 'Deletion Reason', 'value' => $approval->new_data['deletion_reason']];
        }
        
        return $details;
    }

    private function getSoilCreateChanges($approval)
    {
        $details = [];
        $newData = $approval->new_data;
        
        if (!$newData) return [];
        
        $fields = ['nama_penjual', 'alamat_penjual', 'nomor_ppjb', 'letak_tanah', 'luas', 'harga'];
        
        foreach ($fields as $field) {
            if (isset($newData[$field])) {
                $details[] = [
                    'field' => $this->formatSoilFieldName($field),
                    'value' => $this->formatSoilValue($field, $newData[$field])
                ];
            }
        }
        
        return $details;
    }

    private function formatLandFieldName($key)
    {
        $labels = [
            'lokasi_lahan' => 'Location',
            'tahun_perolehan' => 'Acquisition Year',
            'nilai_perolehan' => 'Acquisition Value',
            'alamat' => 'Address',
            'kota_kabupaten' => 'City/Regency',
            'status' => 'Status',
        ];
        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }

    private function formatLandValue($key, $value)
    {
        if (is_null($value) || $value === '') return 'N/A';

        // Handle business_unit_id - show the business unit name
        if ($key === 'business_unit_id' && is_numeric($value)) {
            $businessUnit = \App\Models\BusinessUnit::find($value);
            \Log::info('businessUnit: ', [
                'businessUnit' => $businessUnit
            ]);
            return $businessUnit ? "{$businessUnit->name} ({$businessUnit->code})" : "ID: {$value}";
        }
        
        $moneyFields = ['nilai_perolehan', 'njop', 'est_harga_pasar'];
        if (in_array($key, $moneyFields)) {
            return 'Rp ' . number_format((float)$value, 0, ',', '.');
        }
        
        return $value;
    }

    private function formatSoilFieldName($key)
    {
        $labels = [
            'nama_penjual' => 'Seller Name',
            'alamat_penjual' => 'Seller Address',
            'nomor_ppjb' => 'PPJB Number',
            'letak_tanah' => 'Soil Location',
            'luas' => 'Area (m²)',
            'harga' => 'Price',
        ];
        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }

    private function formatSoilValue($key, $value)
    {
        if (is_null($value) || $value === '') return 'N/A';
        
        if (in_array($key, ['luas', 'harga'])) {
            return 'Rp ' . number_format($value, 0, ',', '.');
        }
        
        return $value;
    }
}