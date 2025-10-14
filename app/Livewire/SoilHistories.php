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
                        'interest_cost_added' => 'Interest Cost Added',
                        'interest_cost_updated' => 'Interest Cost Updated',
                        'interest_cost_deleted' => 'Interest Cost Deleted',
                        'approved_interest_update' => 'Interest Costs Updated (Approved)',
                        'rejected_interest_update' => 'Interest Costs Update (Rejected)',
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

        // Handle interest cost actions
        if (str_contains($history->action, 'interest_cost')) {
            return $this->getInterestCostDetails($history);
        }

        // Handle rejected cost update
        if ($history->action === 'rejected_cost_update') {
            return $this->getRejectedCostDetails($history);
        }

        // Handle rejected interest update
        if ($history->action === 'rejected_interest_update') {
            return $this->getRejectedInterestDetails($history);
        }

        // Handle approved interest update
        if ($history->action === 'approved_interest_update') {
            return $this->getApprovedInterestDetails($history);
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

    private function getInterestCostDetails($history)
    {
        $details = [];
        
        if ($history->action === 'interest_cost_added' && $history->new_values) {
            $nv = $history->new_values;
            
            $startDate = isset($nv['start_date']) ? \Carbon\Carbon::parse($nv['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($nv['end_date']) ? \Carbon\Carbon::parse($nv['end_date'])->format('d/m/Y') : 'N/A';
            $days = 0;
            if (isset($nv['start_date']) && isset($nv['end_date'])) {
                $days = \Carbon\Carbon::parse($nv['start_date'])->diffInDays(\Carbon\Carbon::parse($nv['end_date']));
            }
            
            $details[] = [
                'field' => 'Period',
                'old' => '',
                'new' => $startDate . ' to ' . $endDate . ' (' . $days . ' days)',
                'type' => 'added'
            ];
            $details[] = [
                'field' => 'Harga Perolehan',
                'old' => '',
                'new' => $this->formatValue('harga', $nv['harga_perolehan'] ?? 0),
                'type' => 'added'
            ];
            $details[] = [
                'field' => 'Interest Rate',
                'old' => '',
                'new' => number_format($nv['bunga'] ?? 0, 2) . '%',
                'type' => 'added'
            ];
            if (isset($nv['remarks']) && $nv['remarks']) {
                $details[] = [
                    'field' => 'Remarks',
                    'old' => '',
                    'new' => $nv['remarks'],
                    'type' => 'added'
                ];
            }
        } 
        elseif ($history->action === 'interest_cost_deleted' && $history->old_values) {
            $ov = $history->old_values;
            
            $startDate = isset($ov['start_date']) ? \Carbon\Carbon::parse($ov['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($ov['end_date']) ? \Carbon\Carbon::parse($ov['end_date'])->format('d/m/Y') : 'N/A';
            $days = 0;
            if (isset($ov['start_date']) && isset($ov['end_date'])) {
                $days = \Carbon\Carbon::parse($ov['start_date'])->diffInDays(\Carbon\Carbon::parse($ov['end_date']));
            }
            
            $details[] = [
                'field' => 'Period',
                'old' => $startDate . ' to ' . $endDate . ' (' . $days . ' days)',
                'new' => 'Deleted',
                'type' => 'deleted'
            ];
            $details[] = [
                'field' => 'Harga Perolehan',
                'old' => $this->formatValue('harga', $ov['harga_perolehan'] ?? 0),
                'new' => 'Deleted',
                'type' => 'deleted'
            ];
            $details[] = [
                'field' => 'Interest Rate',
                'old' => number_format($ov['bunga'] ?? 0, 2) . '%',
                'new' => 'Deleted',
                'type' => 'deleted'
            ];
            if (isset($ov['remarks']) && $ov['remarks']) {
                $details[] = [
                    'field' => 'Remarks',
                    'old' => $ov['remarks'],
                    'new' => 'Deleted',
                    'type' => 'deleted'
                ];
            }
        } 
        elseif ($history->action === 'interest_cost_updated' && $history->old_values && $history->new_values) {
            $ov = $history->old_values;
            $nv = $history->new_values;
            
            // Period
            if ((isset($ov['start_date']) && isset($ov['end_date'])) || (isset($nv['start_date']) && isset($nv['end_date']))) {
                $oldStart = isset($ov['start_date']) ? \Carbon\Carbon::parse($ov['start_date'])->format('d/m/Y') : '-';
                $oldEnd = isset($ov['end_date']) ? \Carbon\Carbon::parse($ov['end_date'])->format('d/m/Y') : '-';
                $newStart = isset($nv['start_date']) ? \Carbon\Carbon::parse($nv['start_date'])->format('d/m/Y') : '-';
                $newEnd = isset($nv['end_date']) ? \Carbon\Carbon::parse($nv['end_date'])->format('d/m/Y') : '-';
                
                $oldDays = 0;
                $newDays = 0;
                if (isset($ov['start_date']) && isset($ov['end_date'])) {
                    $oldDays = \Carbon\Carbon::parse($ov['start_date'])->diffInDays(\Carbon\Carbon::parse($ov['end_date']));
                }
                if (isset($nv['start_date']) && isset($nv['end_date'])) {
                    $newDays = \Carbon\Carbon::parse($nv['start_date'])->diffInDays(\Carbon\Carbon::parse($nv['end_date']));
                }
                
                if ($oldStart !== $newStart || $oldEnd !== $newEnd) {
                    $details[] = [
                        'field' => 'Period',
                        'old' => $oldStart . ' to ' . $oldEnd . ' (' . $oldDays . ' days)',
                        'new' => $newStart . ' to ' . $newEnd . ' (' . $newDays . ' days)',
                        'type' => 'updated'
                    ];
                }
            }
            
            // Harga Perolehan
            if (isset($ov['harga_perolehan']) || isset($nv['harga_perolehan'])) {
                $oldAmount = $ov['harga_perolehan'] ?? 0;
                $newAmount = $nv['harga_perolehan'] ?? 0;
                if ($oldAmount != $newAmount) {
                    $details[] = [
                        'field' => 'Harga Perolehan',
                        'old' => $this->formatValue('harga', $oldAmount),
                        'new' => $this->formatValue('harga', $newAmount),
                        'type' => 'updated'
                    ];
                }
            }
            
            // Interest Rate
            if (isset($ov['bunga']) || isset($nv['bunga'])) {
                $oldRate = $ov['bunga'] ?? 0;
                $newRate = $nv['bunga'] ?? 0;
                if ($oldRate != $newRate) {
                    $details[] = [
                        'field' => 'Interest Rate',
                        'old' => number_format($oldRate, 2) . '%',
                        'new' => number_format($newRate, 2) . '%',
                        'type' => 'updated'
                    ];
                }
            }
            
            // Remarks
            if ((isset($ov['remarks']) && $ov['remarks']) || (isset($nv['remarks']) && $nv['remarks'])) {
                $oldRemarks = $ov['remarks'] ?? '-';
                $newRemarks = $nv['remarks'] ?? '-';
                if ($oldRemarks !== $newRemarks) {
                    $details[] = [
                        'field' => 'Remarks',
                        'old' => $oldRemarks,
                        'new' => $newRemarks,
                        'type' => 'updated'
                    ];
                }
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

    // NEW: Get rejected interest update details
    private function getRejectedInterestDetails($history)
    {
        if (!$history->old_values || !$history->new_values) {
            return null;
        }

        $details = [];
        $oldInterests = $history->old_values['interests'] ?? [];
        $newInterests = $history->new_values['interests'] ?? [];

        // Create lookup arrays
        $oldInterestsById = collect($oldInterests)->keyBy('id');
        $newInterestsById = collect($newInterests)->keyBy('id')->filter(fn($item) => !empty($item['id']));

        // Track all IDs
        $oldIds = $oldInterestsById->keys();
        $newIds = $newInterestsById->keys();

        // Check for interests that would be added (rejected)
        $newInterestsWithoutId = collect($newInterests)->filter(function ($interest) {
            return empty($interest['id']) || is_null($interest['id']);
        });

        foreach ($newInterestsWithoutId as $newInterest) {
            $startDate = isset($newInterest['start_date']) ? \Carbon\Carbon::parse($newInterest['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($newInterest['end_date']) ? \Carbon\Carbon::parse($newInterest['end_date'])->format('d/m/Y') : 'N/A';
            $days = 0;
            if (isset($newInterest['start_date']) && isset($newInterest['end_date'])) {
                $days = \Carbon\Carbon::parse($newInterest['start_date'])->diffInDays(\Carbon\Carbon::parse($newInterest['end_date']));
            }
            
            $details[] = [
                'type' => 'rejected_add',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => $days,
                'harga_perolehan' => $this->formatValue('harga', $newInterest['harga_perolehan'] ?? 0),
                'bunga' => number_format($newInterest['bunga'] ?? 0, 2),
                'remarks' => $newInterest['remarks'] ?? '-',
            ];
        }

        // Check for interests that would be deleted (rejected)
        $deletedIds = $oldIds->diff($newIds);
        foreach ($deletedIds as $deletedId) {
            $interest = $oldInterestsById->get($deletedId);
            $startDate = isset($interest['start_date']) ? \Carbon\Carbon::parse($interest['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($interest['end_date']) ? \Carbon\Carbon::parse($interest['end_date'])->format('d/m/Y') : 'N/A';
            $days = 0;
            if (isset($interest['start_date']) && isset($interest['end_date'])) {
                $days = \Carbon\Carbon::parse($interest['start_date'])->diffInDays(\Carbon\Carbon::parse($interest['end_date']));
            }
            
            $details[] = [
                'type' => 'rejected_delete',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => $days,
                'harga_perolehan' => $this->formatValue('harga', $interest['harga_perolehan'] ?? 0),
                'bunga' => number_format($interest['bunga'] ?? 0, 2),
                'remarks' => $interest['remarks'] ?? '-',
            ];
        }

        // Check for interests that would be modified (rejected)
        foreach ($newIds as $interestId) {
            $oldInterest = $oldInterestsById->get($interestId);
            $newInterest = $newInterestsById->get($interestId);

            if ($oldInterest && $newInterest) {
                $hasChanges = (
                    ($oldInterest['start_date'] ?? null) != ($newInterest['start_date'] ?? null) ||
                    ($oldInterest['end_date'] ?? null) != ($newInterest['end_date'] ?? null) ||
                    ($oldInterest['harga_perolehan'] ?? 0) != ($newInterest['harga_perolehan'] ?? 0) ||
                    ($oldInterest['bunga'] ?? 0) != ($newInterest['bunga'] ?? 0) ||
                    ($oldInterest['remarks'] ?? '') != ($newInterest['remarks'] ?? '')
                );

                if ($hasChanges) {
                    $changeDetails = [];

                    if (($oldInterest['start_date'] ?? '') != ($newInterest['start_date'] ?? '')) {
                        $changeDetails[] = [
                            'field' => 'Start Date',
                            'old' => isset($oldInterest['start_date']) ? \Carbon\Carbon::parse($oldInterest['start_date'])->format('d/m/Y') : '-',
                            'new' => isset($newInterest['start_date']) ? \Carbon\Carbon::parse($newInterest['start_date'])->format('d/m/Y') : '-'
                        ];
                    }

                    if (($oldInterest['end_date'] ?? '') != ($newInterest['end_date'] ?? '')) {
                        $changeDetails[] = [
                            'field' => 'End Date',
                            'old' => isset($oldInterest['end_date']) ? \Carbon\Carbon::parse($oldInterest['end_date'])->format('d/m/Y') : '-',
                            'new' => isset($newInterest['end_date']) ? \Carbon\Carbon::parse($newInterest['end_date'])->format('d/m/Y') : '-'
                        ];
                    }

                    if (($oldInterest['harga_perolehan'] ?? 0) != ($newInterest['harga_perolehan'] ?? 0)) {
                        $changeDetails[] = [
                            'field' => 'Harga Perolehan',
                            'old' => $this->formatValue('harga', $oldInterest['harga_perolehan'] ?? 0),
                            'new' => $this->formatValue('harga', $newInterest['harga_perolehan'] ?? 0)
                        ];
                    }

                    if (($oldInterest['bunga'] ?? 0) != ($newInterest['bunga'] ?? 0)) {
                        $changeDetails[] = [
                            'field' => 'Interest Rate',
                            'old' => number_format($oldInterest['bunga'] ?? 0, 2) . '%',
                            'new' => number_format($newInterest['bunga'] ?? 0, 2) . '%'
                        ];
                    }

                    if (($oldInterest['remarks'] ?? '') != ($newInterest['remarks'] ?? '')) {
                        $changeDetails[] = [
                            'field' => 'Remarks',
                            'old' => $oldInterest['remarks'] ?? '-',
                            'new' => $newInterest['remarks'] ?? '-'
                        ];
                    }

                    $details[] = [
                        'type' => 'rejected_modify',
                        'changes' => $changeDetails,
                    ];
                }
            }
        }

        return $details;
    }

    private function getApprovedInterestDetails($history)
    {
        if (!$history->old_values || !$history->new_values) {
            return null;
        }

        $details = [];
        $oldInterests = $history->old_values['interests'] ?? [];
        $newInterests = $history->new_values['interests'] ?? [];

        // Create lookup arrays
        $oldInterestsById = collect($oldInterests)->keyBy('id');
        $newInterestsById = collect($newInterests)->keyBy('id')->filter(fn($item) => !empty($item['id']));

        // Track all IDs
        $oldIds = $oldInterestsById->keys();
        $newIds = $newInterestsById->keys();

        // Check for interests that were added (approved)
        $newInterestsWithoutId = collect($newInterests)->filter(function ($interest) {
            return empty($interest['id']) || is_null($interest['id']);
        });

        foreach ($newInterestsWithoutId as $newInterest) {
            $startDate = isset($newInterest['start_date']) ? \Carbon\Carbon::parse($newInterest['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($newInterest['end_date']) ? \Carbon\Carbon::parse($newInterest['end_date'])->format('d/m/Y') : 'N/A';
            $days = 0;
            if (isset($newInterest['start_date']) && isset($newInterest['end_date'])) {
                $days = \Carbon\Carbon::parse($newInterest['start_date'])->diffInDays(\Carbon\Carbon::parse($newInterest['end_date']));
            }
            
            $details[] = [
                'type' => 'added',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => $days,
                'harga_perolehan' => $this->formatValue('harga', $newInterest['harga_perolehan'] ?? 0),
                'bunga' => number_format($newInterest['bunga'] ?? 0, 2),
                'remarks' => $newInterest['remarks'] ?? '-',
            ];
        }

        // Check for interests that were deleted (approved)
        $deletedIds = $oldIds->diff($newIds);
        foreach ($deletedIds as $deletedId) {
            $interest = $oldInterestsById->get($deletedId);
            $startDate = isset($interest['start_date']) ? \Carbon\Carbon::parse($interest['start_date'])->format('d/m/Y') : 'N/A';
            $endDate = isset($interest['end_date']) ? \Carbon\Carbon::parse($interest['end_date'])->format('d/m/Y') : 'N/A';
            $days = 0;
            if (isset($interest['start_date']) && isset($interest['end_date'])) {
                $days = \Carbon\Carbon::parse($interest['start_date'])->diffInDays(\Carbon\Carbon::parse($interest['end_date']));
            }
            
            $details[] = [
                'type' => 'deleted',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => $days,
                'harga_perolehan' => $this->formatValue('harga', $interest['harga_perolehan'] ?? 0),
                'bunga' => number_format($interest['bunga'] ?? 0, 2),
                'remarks' => $interest['remarks'] ?? '-',
            ];
        }

        // Check for interests that were modified (approved)
        foreach ($newIds as $interestId) {
            $oldInterest = $oldInterestsById->get($interestId);
            $newInterest = $newInterestsById->get($interestId);

            if ($oldInterest && $newInterest) {
                $hasChanges = (
                    ($oldInterest['start_date'] ?? null) != ($newInterest['start_date'] ?? null) ||
                    ($oldInterest['end_date'] ?? null) != ($newInterest['end_date'] ?? null) ||
                    ($oldInterest['harga_perolehan'] ?? 0) != ($newInterest['harga_perolehan'] ?? 0) ||
                    ($oldInterest['bunga'] ?? 0) != ($newInterest['bunga'] ?? 0) ||
                    ($oldInterest['remarks'] ?? '') != ($newInterest['remarks'] ?? '')
                );

                if ($hasChanges) {
                    $changeDetails = [];

                    if (($oldInterest['start_date'] ?? '') != ($newInterest['start_date'] ?? '')) {
                        $changeDetails[] = [
                            'field' => 'Start Date',
                            'old' => isset($oldInterest['start_date']) ? \Carbon\Carbon::parse($oldInterest['start_date'])->format('d/m/Y') : '-',
                            'new' => isset($newInterest['start_date']) ? \Carbon\Carbon::parse($newInterest['start_date'])->format('d/m/Y') : '-'
                        ];
                    }

                    if (($oldInterest['end_date'] ?? '') != ($newInterest['end_date'] ?? '')) {
                        $changeDetails[] = [
                            'field' => 'End Date',
                            'old' => isset($oldInterest['end_date']) ? \Carbon\Carbon::parse($oldInterest['end_date'])->format('d/m/Y') : '-',
                            'new' => isset($newInterest['end_date']) ? \Carbon\Carbon::parse($newInterest['end_date'])->format('d/m/Y') : '-'
                        ];
                    }

                    if (($oldInterest['harga_perolehan'] ?? 0) != ($newInterest['harga_perolehan'] ?? 0)) {
                        $changeDetails[] = [
                            'field' => 'Harga Perolehan',
                            'old' => $this->formatValue('harga', $oldInterest['harga_perolehan'] ?? 0),
                            'new' => $this->formatValue('harga', $newInterest['harga_perolehan'] ?? 0)
                        ];
                    }

                    if (($oldInterest['bunga'] ?? 0) != ($newInterest['bunga'] ?? 0)) {
                        $changeDetails[] = [
                            'field' => 'Interest Rate',
                            'old' => number_format($oldInterest['bunga'] ?? 0, 2) . '%',
                            'new' => number_format($newInterest['bunga'] ?? 0, 2) . '%'
                        ];
                    }

                    if (($oldInterest['remarks'] ?? '') != ($newInterest['remarks'] ?? '')) {
                        $changeDetails[] = [
                            'field' => 'Remarks',
                            'old' => $oldInterest['remarks'] ?? '-',
                            'new' => $newInterest['remarks'] ?? '-'
                        ];
                    }

                    $details[] = [
                        'type' => 'updated',
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