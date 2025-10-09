<?php
// app/Livewire/SoilHistories.php

namespace App\Livewire;

use App\Models\Soil;
use App\Models\SoilHistory;
use Livewire\Component;
use Livewire\WithPagination;

class SoilHistories extends Component
{
    use WithPagination;

    public $soil;
    public $soilId;
    public $filterAction = '';
    public $filterUser = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterColumn = '';

    public $businessUnitId = null;
    public $fromShow = false;

    protected $queryString = [
        'filterAction' => ['except' => ''],
        'filterUser' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'filterColumn' => ['except' => ''],
        'businessUnitId' => ['except' => null],
        'fromShow' => ['except' => false],
    ];

    public function mount($soilId)
    {
        $this->soilId = $soilId;
        $this->soil = Soil::with(['land', 'businessUnit'])->findOrFail($soilId);
        
        $referrer = request()->headers->get('referer');
        if ($referrer && str_contains($referrer, '/business-unit/')) {
            preg_match('/\/business-unit\/(\d+)/', $referrer, $matches);
            if (isset($matches[1])) {
                $this->businessUnitId = (int)$matches[1];
            }
        } elseif ($referrer && str_contains($referrer, '/show')) {
            $this->fromShow = true;
        }
    }

    public function render()
    {
        $histories = SoilHistory::where('soil_id', $this->soilId)
            ->with(['user'])
            ->when($this->filterAction, function($q) {
                $q->where('action', $this->filterAction);
            })
            ->when($this->filterUser, function($q) {
                $q->whereHas('user', function($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->filterUser . '%');
                });
            })
            ->when($this->filterDateFrom, function($q) {
                $q->whereDate('created_at', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function($q) {
                $q->whereDate('created_at', '<=', $this->filterDateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Filter out entries with no meaningful changes
        $histories->getCollection()->transform(function($history) {
            if (!$this->hasMeaningfulChanges($history)) {
                return null;
            }
            return $history;
        });

        $filteredItems = $histories->getCollection()->filter()->values();
        $histories->setCollection($filteredItems);

        // Get available actions for filter
        $availableActions = $this->getAvailableActions();
        
        $availableUsers = SoilHistory::with('user')
            ->where('soil_id', $this->soilId)
            ->whereNotNull('user_id')
            ->get()
            ->pluck('user.name')
            ->unique()
            ->values();

        $availableColumns = $this->getAvailableColumns();

        return view('livewire.soil-histories.index', compact('histories', 'availableActions', 'availableUsers', 'availableColumns'));
    }

    // COMPLETE: Get all available actions
    private function getAvailableActions()
    {
        return SoilHistory::where('soil_id', $this->soilId)
            ->distinct()
            ->pluck('action')
            ->map(function($action) {
                return [
                    'value' => $action,
                    'label' => match($action) {
                        'created' => 'Record Created',
                        'approved_creation' => 'Record Created (Approved)',
                        'rejected_creation' => 'Record Creation (Rejected)',
                        'updated' => 'Record Updated',
                        'approved_update' => 'Record Updated (Approved)',
                        'rejected_update' => 'Record Update (Rejected)',
                        'deleted' => 'Record Deleted',
                        'approved_deletion' => 'Record Deleted (Approved)',
                        'rejected_deletion' => 'Record Deletion (Rejected)',
                        'restored' => 'Record Restored',
                        'additional_cost_added' => 'Additional Cost Added',
                        'additional_cost_updated' => 'Additional Cost Updated',
                        'additional_cost_deleted' => 'Additional Cost Deleted',
                        'rejected_cost_update' => 'Additional Costs Update (Rejected)',
                        default => ucfirst(str_replace('_', ' ', $action))
                    }
                ];
            })
            ->values()
            ->toArray();
    }

    private function hasMeaningfulChanges($history)
    {
        // Always show approved and rejected changes
        if ($history->isApprovedChange() || $history->isRejectedChange()) {
            return true;
        }
        
        // For other actions, check if there are actual changes
        if ($history->action === 'additional_cost_updated') {
            $changeDetails = $this->getChangeDetails($history);
            return !empty($changeDetails);
        }
        
        return true; // Show all other actions
    }

    public function backToSoil()
    {
        if ($this->fromShow) {
            return redirect()->route('soils.show', ['soilId' => $this->soilId]);
        } elseif ($this->businessUnitId) {
            return redirect()->route('soils.by-business-unit', [
                'businessUnit' => $this->businessUnitId,
                'soilId' => $this->soilId
            ]);
        } else {
            return redirect()->route('soils.show', ['soilId' => $this->soilId]);
        }
    }

    public function resetFilters()
    {
        $this->filterAction = '';
        $this->filterUser = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterColumn = '';
        $this->resetPage();
    }

    public function updatingFilterAction()
    {
        $this->resetPage();
        if ($this->filterAction !== 'updated') {
            $this->filterColumn = '';
        }
    }

    public function updatingFilterUser() { $this->resetPage(); }
    public function updatingFilterDateFrom() { $this->resetPage(); }
    public function updatingFilterDateTo() { $this->resetPage(); }
    public function updatingFilterColumn() { $this->resetPage(); }

    private function getAvailableColumns()
    {
        $columns = SoilHistory::where('soil_id', $this->soilId)
            ->where('action', 'updated')
            ->whereNotNull('changes')
            ->get()
            ->flatMap(function ($history) {
                return $history->changes ?? [];
            })
            ->unique()
            ->sort()
            ->map(function ($column) {
                return [
                    'value' => $column,
                    'label' => $this->getFieldDisplayName($column)
                ];
            })
            ->values();

        return $columns;
    }

    // COMPLETE: Get change details for ALL scenarios
    public function getChangeDetails($history)
    {
        // Handle additional cost actions
        if (str_contains($history->action, 'additional_cost')) {
            return $this->getAdditionalCostDetails($history);
        }

        // Handle rejected cost update
        if ($history->action === 'rejected_cost_update') {
            return $this->getRejectedCostDetails($history);
        }

        // Handle rejected updates
        if ($history->action === 'rejected_update') {
            return $this->getRejectedDetailChanges($history);
        }

        // Handle rejected deletion
        if ($history->action === 'rejected_deletion') {
            return $this->getRejectedDeletionDetails($history);
        }

        // Handle rejected creation
        if ($history->action === 'rejected_creation') {
            return $this->getRejectedCreationDetails($history);
        }

        // Handle regular updates and approved updates
        if (in_array($history->action, ['updated', 'approved_update'])) {
            return $this->getRegularUpdateDetails($history);
        }

        // Handle creation
        if (in_array($history->action, ['created', 'approved_creation'])) {
            return $this->getCreationDetails($history);
        }

        // Handle deletion
        if (in_array($history->action, ['deleted', 'approved_deletion'])) {
            return $this->getDeletionDetails($history);
        }

        return null;
    }

    // Get regular update details
    private function getRegularUpdateDetails($history)
    {
        if (!$history->old_values || !$history->new_values) {
            return null;
        }

        $changes = [];
        $fieldsToCheck = $history->changes ?? array_keys(array_merge(
            $history->old_values ?? [],
            $history->new_values ?? []
        ));

        foreach ($fieldsToCheck as $field) {
            // Skip metadata fields
            if (str_starts_with($field, '_')) continue;
            
            $oldValue = $history->old_values[$field] ?? '';
            $newValue = $history->new_values[$field] ?? '';
            
            if ($oldValue != $newValue) {
                $changes[] = [
                    'field' => $this->getFieldDisplayName($field),
                    'old' => $this->formatValue($field, $oldValue),
                    'new' => $this->formatValue($field, $newValue),
                    'type' => 'update'
                ];
            }
        }
        
        return $changes;
    }

    // Get creation details
    private function getCreationDetails($history)
    {
        if (!$history->new_values) {
            return null;
        }

        $details = [];
        foreach ($history->new_values as $field => $value) {
            if (str_starts_with($field, '_')) continue;
            if ($value !== null && $value !== '') {
                $details[] = [
                    'field' => $this->getFieldDisplayName($field),
                    'value' => $this->formatValue($field, $value),
                    'type' => 'creation'
                ];
            }
        }
        
        return $details;
    }

    // Get deletion details
    private function getDeletionDetails($history)
    {
        $details = [];
        
        if ($history->old_values) {
            foreach ($history->old_values as $field => $value) {
                if (str_starts_with($field, '_')) continue;
                if ($value !== null && $value !== '') {
                    $details[] = [
                        'field' => $this->getFieldDisplayName($field),
                        'value' => $this->formatValue($field, $value),
                        'type' => 'deletion'
                    ];
                }
            }
        }

        // Add deletion reason if available
        if ($history->new_values && isset($history->new_values['deletion_reason'])) {
            $details[] = [
                'field' => 'Deletion Reason',
                'value' => $history->new_values['deletion_reason'],
                'type' => 'reason'
            ];
        }
        
        return $details;
    }

    // COMPLETE: Get additional cost details
    private function getAdditionalCostDetails($history)
    {
        $details = [];
        
        if ($history->action === 'additional_cost_added' && $history->new_values) {
            $nv = $history->new_values;
            
            $details[] = [
                'field' => 'Cost Description',
                'old' => '',
                'new' => $nv['description'] ?? 'N/A',
                'type' => 'added'
            ];
            $details[] = [
                'field' => 'Amount',
                'old' => '',
                'new' => $this->formatValue('additional_cost_amount', $nv['harga'] ?? 0),
                'type' => 'added'
            ];
            $details[] = [
                'field' => 'Cost Type',
                'old' => '',
                'new' => $this->formatValue('additional_cost_type', $nv['cost_type'] ?? 'standard'),
                'type' => 'added'
            ];
            if (isset($nv['date_cost'])) {
                $details[] = [
                    'field' => 'Date',
                    'old' => '',
                    'new' => $this->formatValue('additional_cost_date', $nv['date_cost']),
                    'type' => 'added'
                ];
            }
        } 
        elseif ($history->action === 'additional_cost_updated' && $history->old_values && $history->new_values) {
            $ov = $history->old_values;
            $nv = $history->new_values;
            
            // Description
            if (isset($ov['description']) || isset($nv['description'])) {
                $oldDesc = $ov['description'] ?? '-';
                $newDesc = $nv['description'] ?? '-';
                if ($oldDesc !== $newDesc) {
                    $details[] = [
                        'field' => 'Cost Description',
                        'old' => $oldDesc,
                        'new' => $newDesc,
                        'type' => 'updated'
                    ];
                }
            }
            
            // Amount
            if (isset($ov['harga']) || isset($nv['harga'])) {
                $oldAmount = $ov['harga'] ?? 0;
                $newAmount = $nv['harga'] ?? 0;
                if ($oldAmount != $newAmount) {
                    $details[] = [
                        'field' => 'Amount',
                        'old' => $this->formatValue('additional_cost_amount', $oldAmount),
                        'new' => $this->formatValue('additional_cost_amount', $newAmount),
                        'type' => 'updated'
                    ];
                }
            }
            
            // Cost Type
            if (isset($ov['cost_type']) || isset($nv['cost_type'])) {
                $oldType = $ov['cost_type'] ?? 'standard';
                $newType = $nv['cost_type'] ?? 'standard';
                if ($oldType !== $newType) {
                    $details[] = [
                        'field' => 'Cost Type',
                        'old' => $this->formatValue('additional_cost_type', $oldType),
                        'new' => $this->formatValue('additional_cost_type', $newType),
                        'type' => 'updated'
                    ];
                }
            }
            
            // Date
            if (isset($ov['date_cost']) || isset($nv['date_cost'])) {
                $oldDate = $ov['date_cost'] ?? null;
                $newDate = $nv['date_cost'] ?? null;
                if ($oldDate !== $newDate) {
                    $details[] = [
                        'field' => 'Date',
                        'old' => $this->formatValue('additional_cost_date', $oldDate),
                        'new' => $this->formatValue('additional_cost_date', $newDate),
                        'type' => 'updated'
                    ];
                }
            }
        } 
        elseif ($history->action === 'additional_cost_deleted' && $history->old_values) {
            $ov = $history->old_values;
            
            $details[] = [
                'field' => 'Cost Description',
                'old' => $ov['description'] ?? 'N/A',
                'new' => 'Deleted',
                'type' => 'deleted'
            ];
            $details[] = [
                'field' => 'Amount',
                'old' => $this->formatValue('additional_cost_amount', $ov['harga'] ?? 0),
                'new' => 'Deleted',
                'type' => 'deleted'
            ];
            $details[] = [
                'field' => 'Cost Type',
                'old' => $this->formatValue('additional_cost_type', $ov['cost_type'] ?? 'standard'),
                'new' => 'Deleted',
                'type' => 'deleted'
            ];
            if (isset($ov['date_cost'])) {
                $details[] = [
                    'field' => 'Date',
                    'old' => $this->formatValue('additional_cost_date', $ov['date_cost']),
                    'new' => 'Deleted',
                    'type' => 'deleted'
                ];
            }
        }
        
        return !empty($details) ? $details : null;
    }

    // NEW: Get rejected cost update details
    private function getRejectedCostDetails($history)
    {
        if (!$history->old_values || !$history->new_values) {
            return null;
        }

        $details = [];
        $oldCosts = $history->old_values['costs'] ?? [];
        $newCosts = $history->new_values['costs'] ?? [];

        // Create lookup arrays
        $oldCostsById = collect($oldCosts)->keyBy('id');
        $newCostsById = collect($newCosts)->keyBy('id')->filter(fn($item) => !empty($item['id']));

        // Track all IDs
        $oldIds = $oldCostsById->keys();
        $newIds = $newCostsById->keys();

        // Check for costs that would be added (rejected)
        $newCostsWithoutId = collect($newCosts)->filter(function ($cost) {
            return empty($cost['id']) || is_null($cost['id']);
        });

        foreach ($newCostsWithoutId as $newCost) {
            $details[] = [
                'type' => 'rejected_add',
                'description' => $newCost['description'] ?? 'Unknown',
                'amount' => $this->formatValue('harga', $newCost['harga'] ?? 0),
                'cost_type' => $this->formatValue('cost_type', $newCost['cost_type'] ?? 'standard'),
                'date_cost' => $this->formatValue('date_cost', $newCost['date_cost'] ?? ''),
            ];
        }

        // Check for costs that would be deleted (rejected)
        $deletedIds = $oldIds->diff($newIds);
        foreach ($deletedIds as $deletedId) {
            $cost = $oldCostsById->get($deletedId);
            $details[] = [
                'type' => 'rejected_delete',
                'description' => $cost['description'] ?? 'Unknown',
                'amount' => $this->formatValue('harga', $cost['harga'] ?? 0),
                'cost_type' => $this->formatValue('cost_type', $cost['cost_type'] ?? 'standard'),
                'date_cost' => $this->formatValue('date_cost', $cost['date_cost'] ?? ''),
            ];
        }

        // Check for costs that would be modified (rejected)
        foreach ($newIds as $costId) {
            $oldCost = $oldCostsById->get($costId);
            $newCost = $newCostsById->get($costId);

            if ($oldCost && $newCost) {
                $hasChanges = (
                    ($oldCost['description_id'] ?? null) != ($newCost['description_id'] ?? null) ||
                    ($oldCost['harga'] ?? 0) != ($newCost['harga'] ?? 0) ||
                    ($oldCost['cost_type'] ?? null) != ($newCost['cost_type'] ?? null) ||
                    ($oldCost['date_cost'] ?? null) != ($newCost['date_cost'] ?? null)
                );

                if ($hasChanges) {
                    $changeDetails = [];

                    if (($oldCost['description'] ?? '') != ($newCost['description'] ?? '')) {
                        $changeDetails[] = [
                            'field' => 'Description',
                            'old' => $oldCost['description'] ?? 'Unknown',
                            'new' => $newCost['description'] ?? 'Unknown'
                        ];
                    }

                    if (($oldCost['harga'] ?? 0) != ($newCost['harga'] ?? 0)) {
                        $changeDetails[] = [
                            'field' => 'Amount',
                            'old' => $this->formatValue('harga', $oldCost['harga'] ?? 0),
                            'new' => $this->formatValue('harga', $newCost['harga'] ?? 0)
                        ];
                    }

                    if (($oldCost['cost_type'] ?? '') != ($newCost['cost_type'] ?? '')) {
                        $changeDetails[] = [
                            'field' => 'Cost Type',
                            'old' => $this->formatValue('cost_type', $oldCost['cost_type'] ?? 'standard'),
                            'new' => $this->formatValue('cost_type', $newCost['cost_type'] ?? 'standard')
                        ];
                    }

                    if (($oldCost['date_cost'] ?? '') != ($newCost['date_cost'] ?? '')) {
                        $changeDetails[] = [
                            'field' => 'Date',
                            'old' => $this->formatValue('date_cost', $oldCost['date_cost'] ?? ''),
                            'new' => $this->formatValue('date_cost', $newCost['date_cost'] ?? '')
                        ];
                    }

                    $details[] = [
                        'type' => 'rejected_modify',
                        'description' => $newCost['description'] ?? $oldCost['description'] ?? 'Unknown',
                        'changes' => $changeDetails,
                    ];
                }
            }
        }

        return $details;
    }

    // NEW: Get rejected detail changes
    private function getRejectedDetailChanges($history)
    {
        if (!$history->old_values || !$history->new_values) {
            return null;
        }

        $changes = [];
        $oldData = $history->old_values;
        $newData = $history->new_values;

        // Remove metadata from display
        if (isset($newData['_rejection_metadata'])) {
            unset($newData['_rejection_metadata']);
        }

        $fieldsToCheck = $history->changes ?? array_keys($newData);

        foreach ($fieldsToCheck as $field) {
            if (str_starts_with($field, '_')) continue;
            
            if (isset($oldData[$field]) && isset($newData[$field])) {
                $oldValue = $oldData[$field];
                $newValue = $newData[$field];

                if ($oldValue != $newValue) {
                    $changes[] = [
                        'field' => $this->getFieldDisplayName($field),
                        'old' => $this->formatValue($field, $oldValue),
                        'new' => $this->formatValue($field, $newValue),
                        'type' => 'rejected_update',
                        'status' => 'rejected'
                    ];
                }
            }
        }

        return $changes;
    }

    // NEW: Get rejected deletion details
    private function getRejectedDeletionDetails($history)
    {
        $details = [];
        $oldData = $history->old_values;

        if ($oldData) {
            foreach ($oldData as $field => $value) {
                if (str_starts_with($field, '_')) continue;
                if ($value !== null && $value !== '') {
                    $details[] = [
                        'field' => $this->getFieldDisplayName($field),
                        'value' => $this->formatValue($field, $value),
                        'type' => 'rejected_deletion'
                    ];
                }
            }
        }

        // Get deletion reason from new_values
        if (isset($history->new_values['deletion_reason'])) {
            $details[] = [
                'field' => 'Requested Deletion Reason',
                'value' => $history->new_values['deletion_reason'],
                'type' => 'reason'
            ];
        }

        return $details;
    }

    // NEW: Get rejected creation details
    private function getRejectedCreationDetails($history)
    {
        if (!$history->new_values) {
            return null;
        }

        $details = [];
        foreach ($history->new_values as $field => $value) {
            if (str_starts_with($field, '_')) continue;
            if ($value !== null && $value !== '') {
                $details[] = [
                    'field' => $this->getFieldDisplayName($field),
                    'value' => $this->formatValue($field, $value),
                    'type' => 'rejected_creation'
                ];
            }
        }
        
        return $details;
    }

    private function formatValue($field, $value)
    {
        if (is_null($value) || $value === '') {
            return '-';
        }

        // Format specific fields
        switch ($field) {
            case 'harga':
            case 'luas':
            case 'additional_cost_amount':
            case 'additional_cost_harga':
            case 'amount':
                return is_numeric($value) ? 'Rp ' . number_format($value, 0, ',', '.') : $value;
            case 'tanggal_ppjb':
            case 'additional_cost_date':
            case 'additional_cost_date_cost':
            case 'date':
            case 'date_cost':
                return $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-';
            case 'created_at':
            case 'updated_at':
                if ($value) {
                    $date = \Carbon\Carbon::parse($value)->setTimezone('Asia/Jakarta');
                    return $date->format('d/m/Y H:i') . ' (GMT+7)';
                }
                return '-';
            case 'land_id':
                $land = \App\Models\Land::find($value);
                return $land ? $land->lokasi_lahan : "ID: {$value}";
            case 'business_unit_id':
                $businessUnit = \App\Models\BusinessUnit::find($value);
                return $businessUnit ? $businessUnit->name : "ID: {$value}";
            case 'additional_cost_type':
            case 'additional_cost_cost_type':
            case 'cost_type':
            case 'type':
                return $value === 'standard' ? 'Standard' : 'Non Standard';
            default:
                return $value;
        }
    }

    private function getFieldDisplayName($field)
    {
        $fieldMap = [
            'nama_penjual' => 'Seller Name',
            'alamat_penjual' => 'Seller Address',
            'nomor_ppjb' => 'PPJB Number',
            'tanggal_ppjb' => 'PPJB Date',
            'letak_tanah' => 'Soil Location',
            'luas' => 'Area (m²)',
            'harga' => 'Price (Rp)',
            'bukti_kepemilikan' => 'Ownership Proof',
            'bukti_kepemilikan_details' => 'Ownership Proof Details',
            'atas_nama' => 'Owner Name',
            'nop_pbb' => 'NOP PBB',
            'nama_notaris_ppat' => 'Notaris/PPAT Name',
            'keterangan' => 'Notes',
            'land_id' => 'Land',
            'business_unit_id' => 'Business Unit',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'additional_cost_added' => 'Additional Cost Added',
            'additional_cost_updated' => 'Additional Cost Updated', 
            'additional_cost_deleted' => 'Additional Cost Deleted',
            'additional_cost_description' => 'Cost Description',
            'additional_cost_amount' => 'Cost Amount',
            'additional_cost_harga' => 'Cost Amount',
            'additional_cost_type' => 'Cost Type',
            'additional_cost_cost_type' => 'Cost Type',
            'additional_cost_date' => 'Cost Date',
            'additional_cost_date_cost' => 'Cost Date',
            'description' => 'Description',
            'amount' => 'Amount',
            'type' => 'Type',
            'cost_type' => 'Cost Type',
            'date' => 'Date',
            'date_cost' => 'Cost Date',
            'costs' => 'Additional Costs',
        ];

        return $fieldMap[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    // Get approval/rejection information for display
    public function getApprovalInfo($history)
    {
        if ($history->isApprovedChange()) {
            $approvalMetadata = $history->getApprovalMetadata();
            if ($approvalMetadata && isset($approvalMetadata['approved_by'])) {
                $approver = \App\Models\User::find($approvalMetadata['approved_by']);
                if ($approver) {
                    return [
                        'type' => 'approved',
                        'user_name' => $approver->name,
                        'approval_id' => $approvalMetadata['approval_id'] ?? null,
                    ];
                }
            }
        }

        if ($history->isRejectedChange()) {
            $rejectionMetadata = $history->getRejectionMetadata();
            if ($rejectionMetadata && isset($rejectionMetadata['rejected_by'])) {
                $rejector = \App\Models\User::find($rejectionMetadata['rejected_by']);
                if ($rejector) {
                    return [
                        'type' => 'rejected',
                        'user_name' => $rejector->name,
                        'approval_id' => $rejectionMetadata['approval_id'] ?? null,
                        'reason' => $rejectionMetadata['rejection_reason'] ?? null,
                    ];
                }
            }
        }

        return null;
    }

    // Helper method to check if history entry should show approval/rejection badge
    public function shouldShowStatusBadge($history)
    {
        return $history->isApprovedChange() || $history->isRejectedChange();
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

        if ($history->isRejectedChange()) {
            return [
                'border' => 'border-red-200',
                'bg' => 'bg-red-50',
                'text' => 'text-red-600',
                'badge_bg' => 'bg-red-100',
                'badge_text' => 'text-red-800'
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
            ],
        };
    }
}