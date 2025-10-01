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
    public $filterColumn = ''; // New filter for updated columns

    // Add these properties to track navigation context
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
        
        // Check if we came from a business unit filtered view or show page
        $referrer = request()->headers->get('referer');
        if ($referrer && str_contains($referrer, '/business-unit/')) {
            // Extract business unit ID from the referrer URL
            preg_match('/\/business-unit\/(\d+)/', $referrer, $matches);
            if (isset($matches[1])) {
                $this->businessUnitId = (int)$matches[1];
            }
        } elseif ($referrer && str_contains($referrer, '/show')) {
            // We came from the show page
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

        // Filter out additional_cost_updated entries with no meaningful changes
        $histories->getCollection()->transform(function($history) {
            if (!$this->hasMeaningfulChanges($history)) {
                return null; // Mark for removal
            }
            return $history;
        });

        $filteredItems = $histories->getCollection()->filter()->values();
        $histories->setCollection($filteredItems);

        // Get available actions and users for filters
        $availableActions = SoilHistory::where('soil_id', $this->soilId)
            ->distinct()
            ->pluck('action')
            ->map(function($action) {
                return [
                    'value' => $action,
                    'label' => match($action) {
                        'created' => 'Record Created',
                        'updated' => 'Record Updated',
                        'deleted' => 'Record Deleted',
                        'restored' => 'Record Restored',
                        'additional_cost_added' => 'Additional Cost Added',
                        'additional_cost_updated' => 'Additional Cost Updated',
                        'additional_cost_deleted' => 'Additional Cost Deleted',
                        'rejected_cost_update' => 'Rejected Cost',
                        default => ucfirst($action)
                    }
                ];
            });

        $availableUsers = SoilHistory::with('user')
            ->where('soil_id', $this->soilId)
            ->whereNotNull('user_id')
            ->get()
            ->pluck('user.name')
            ->unique()
            ->values();

        // Get available columns that have been updated (only for 'updated' actions)
        $availableColumns = $this->getAvailableColumns();

        return view('livewire.soil-histories.index', compact('histories', 'availableActions', 'availableUsers', 'availableColumns'));
    }

    private function hasMeaningfulChanges($history)
    {
        // Don't filter approved changes - always show them
        if ($history->isApprovedChange()) {
            return true;
        }
        
        if ($history->action !== 'additional_cost_updated') {
            return true; // Always show non-update actions
        }
        
        $changeDetails = $this->getChangeDetails($history);
        return !empty($changeDetails); // Only show if there are actual changes
    }

    public function backToSoil()
    {
        // Determine the correct route based on context
        if ($this->fromShow) {
            // Go back to the show page
            return redirect()->route('soils.show', ['soilId' => $this->soilId]);
        } elseif ($this->businessUnitId) {
            // Go back to the business unit filtered view and show this soil's detail
            return redirect()->route('soils.by-business-unit', [
                'businessUnit' => $this->businessUnitId,
                'soilId' => $this->soilId
            ]);
        } else {
            // Default: go back to soils show page
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
        
        // Reset column filter when action changes
        // Column filter only applies to 'updated' actions, not additional cost actions
        if ($this->filterAction !== 'updated') {
            $this->filterColumn = '';
        }
    }

    public function updatingFilterUser()
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }

    public function updatingFilterColumn()
    {
        $this->resetPage();
    }

    // Get available columns that have been updated in history
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

    // Helper method to get change details
    public function getChangeDetails($history)
    {
        // Special handling for additional cost actions
        if (in_array($history->action, [
            'additional_cost_added', 
            'additional_cost_updated', 
            'additional_cost_deleted',
            'additional_cost_approved'  // Add this
        ])) {
            return $this->getAdditionalCostDetails($history);
        }

        if (!$history->old_values || !$history->new_values) {
            return null;
        }

        $changes = [];
        
        // If changes array is empty or null, determine from old/new values
        $fieldsToCheck = $history->changes ?? array_keys(array_merge(
            $history->old_values ?? [],
            $history->new_values ?? []
        ));

        foreach ($fieldsToCheck as $field) {
            $oldValue = $history->old_values[$field] ?? '';
            $newValue = $history->new_values[$field] ?? '';
            
            // Format values for display
            $oldValue = $this->formatValue($field, $oldValue);
            $newValue = $this->formatValue($field, $newValue);

            $changes[] = [
                'field' => $this->getFieldDisplayName($field),
                'old' => $oldValue,
                'new' => $newValue
            ];
        }
        return $changes;
    }

    // New method to handle additional cost details
    private function getAdditionalCostDetails($history)
    {
        $details = [];
        
        if ($history->action === 'additional_cost_added' && $history->new_values) {
            // For added costs, show the new values
            $nv = $history->new_values;
            
            $details[] = [
                'field' => 'Cost Description',
                'old' => '',
                'new' => $nv['description'] ?? 'N/A'
            ];
            $details[] = [
                'field' => 'Amount',
                'old' => '',
                'new' => $this->formatValue('additional_cost_amount', $nv['harga'] ?? 0)
            ];
            $details[] = [
                'field' => 'Cost Type',
                'old' => '',
                'new' => $this->formatValue('additional_cost_type', $nv['cost_type'] ?? 'standard')
            ];
            if (isset($nv['date_cost'])) {
                $details[] = [
                    'field' => 'Date',
                    'old' => '',
                    'new' => $this->formatValue('additional_cost_date', $nv['date_cost'])
                ];
            }
        } 
        elseif ($history->action === 'additional_cost_updated' && $history->old_values && $history->new_values) {
            // For updated costs, show what changed
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
                        'new' => $newDesc
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
                        'new' => $this->formatValue('additional_cost_amount', $newAmount)
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
                        'new' => $this->formatValue('additional_cost_type', $newType)
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
                        'new' => $this->formatValue('additional_cost_date', $newDate)
                    ];
                }
            }
        } 
        elseif ($history->action === 'additional_cost_deleted' && $history->old_values) {
            // For deleted costs, show what was deleted
            $ov = $history->old_values;
            
            $details[] = [
                'field' => 'Cost Description',
                'old' => $ov['description'] ?? 'N/A',
                'new' => 'Deleted'
            ];
            $details[] = [
                'field' => 'Amount',
                'old' => $this->formatValue('additional_cost_amount', $ov['harga'] ?? 0),
                'new' => 'Deleted'
            ];
            $details[] = [
                'field' => 'Cost Type',
                'old' => $this->formatValue('additional_cost_type', $ov['cost_type'] ?? 'standard'),
                'new' => 'Deleted'
            ];
            if (isset($ov['date_cost'])) {
                $details[] = [
                    'field' => 'Date',
                    'old' => $this->formatValue('additional_cost_date', $ov['date_cost']),
                    'new' => 'Deleted'
                ];
            }
        }
        
        return !empty($details) ? $details : null;
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
                return $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '-';
            case 'created_at':
            case 'updated_at':
                // Handle datetime fields with GMT+7 timezone
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
            'harga' => 'Amount',
            'type' => 'Type',
            'cost_type' => 'Cost Type',
            'date' => 'Date',
        ];

        return $fieldMap[$field] ?? ucfirst(str_replace('_', ' ', $field));
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
            'created', 'approved_creation' => [
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

    public function getRejectedChangeDetails($history)
    {
        if (!$history->isRejectedChange()) {
            return null;
        }

        // Handle different rejection types
        if ($history->action === 'rejected_update') {
            return $this->getRejectedDetailChanges($history);
        } elseif ($history->action === 'rejected_cost_update') {
            return $this->getRejectedCostChanges($history);
        } elseif ($history->action === 'rejected_deletion') {
            return $this->getRejectedDeletionChanges($history);
        }

        return null;
    }

    // Get rejected detail changes
    private function getRejectedDetailChanges($history)
    {
        if (!$history->old_values || !$history->new_values) {
            return null;
        }

        $changes = [];
        $oldData = $history->old_values;
        $newData = $history->new_values;

        // Remove metadata from display
        unset($newData['_rejection_metadata']);

        $fieldsToCheck = $history->changes ?? array_keys($newData);

        foreach ($fieldsToCheck as $field) {
            if (isset($oldData[$field]) && isset($newData[$field])) {
                $oldValue = $oldData[$field];
                $newValue = $newData[$field];

                if ($oldValue != $newValue) {
                    $changes[] = [
                        'field' => $this->getFieldDisplayName($field),
                        'old' => $this->formatValue($field, $oldValue),
                        'new' => $this->formatValue($field, $newValue),
                        'status' => 'rejected'
                    ];
                }
            }
        }

        return $changes;
    }

    // Get rejected cost changes
    private function getRejectedCostChanges($history)
    {
        if (!$history->old_values || !$history->new_values) {
            return null;
        }

        $oldCosts = $history->old_values['costs'] ?? [];
        $newCosts = $history->new_values['costs'] ?? [];

        // Remove metadata
        unset($history->new_values['_rejection_metadata']);

        $changes = [];

        // Create lookup arrays
        $oldCostsById = collect($oldCosts)->keyBy('id');
        $newCostsById = collect($newCosts)->keyBy('id')->filter(fn($item) => !empty($item['id']));

        // Track all IDs
        $oldIds = $oldCostsById->keys();
        $newIds = $newCostsById->keys();

        // Check for costs that would be added
        $newCostsWithoutId = collect($newCosts)->filter(function ($cost) {
            return empty($cost['id']) || is_null($cost['id']);
        });

        foreach ($newCostsWithoutId as $newCost) {
            $changes[] = [
                'type' => 'add',
                'description' => $newCost['description'] ?? 'Unknown',
                'amount' => $this->formatValue('harga', $newCost['harga'] ?? 0),
                'cost_type' => $this->formatValue('cost_type', $newCost['cost_type'] ?? 'standard'),
                'date_cost' => $this->formatValue('date_cost', $newCost['date_cost'] ?? ''),
                'status' => 'rejected'
            ];
        }

        // Check for costs that would be deleted
        $deletedIds = $oldIds->diff($newIds);
        foreach ($deletedIds as $deletedId) {
            $cost = $oldCostsById->get($deletedId);
            $changes[] = [
                'type' => 'delete',
                'description' => $cost['description'] ?? 'Unknown',
                'amount' => $this->formatValue('harga', $cost['harga'] ?? 0),
                'cost_type' => $this->formatValue('cost_type', $cost['cost_type'] ?? 'standard'),
                'date_cost' => $this->formatValue('date_cost', $cost['date_cost'] ?? ''),
                'status' => 'rejected'
            ];
        }

        // Check for costs that would be modified
        foreach ($newIds as $costId) {
            $oldCost = $oldCostsById->get($costId);
            $newCost = $newCostsById->get($costId);

            if ($oldCost && $newCost) {
                $hasChanges = (
                    $oldCost['description_id'] != $newCost['description_id'] ||
                    $oldCost['harga'] != $newCost['harga'] ||
                    $oldCost['cost_type'] != $newCost['cost_type'] ||
                    $oldCost['date_cost'] != $newCost['date_cost']
                );

                if ($hasChanges) {
                    $changeDetails = [];

                    if ($oldCost['description'] != $newCost['description']) {
                        $changeDetails[] = [
                            'field' => 'Description',
                            'old' => $oldCost['description'] ?? 'Unknown',
                            'new' => $newCost['description'] ?? 'Unknown'
                        ];
                    }

                    if ($oldCost['harga'] != $newCost['harga']) {
                        $changeDetails[] = [
                            'field' => 'Amount',
                            'old' => $this->formatValue('harga', $oldCost['harga']),
                            'new' => $this->formatValue('harga', $newCost['harga'])
                        ];
                    }

                    if ($oldCost['cost_type'] != $newCost['cost_type']) {
                        $changeDetails[] = [
                            'field' => 'Cost Type',
                            'old' => $this->formatValue('cost_type', $oldCost['cost_type']),
                            'new' => $this->formatValue('cost_type', $newCost['cost_type'])
                        ];
                    }

                    if ($oldCost['date_cost'] != $newCost['date_cost']) {
                        $changeDetails[] = [
                            'field' => 'Date',
                            'old' => $this->formatValue('date_cost', $oldCost['date_cost']),
                            'new' => $this->formatValue('date_cost', $newCost['date_cost'])
                        ];
                    }

                    $changes[] = [
                        'type' => 'modify',
                        'description' => $newCost['description'] ?? $oldCost['description'] ?? 'Unknown',
                        'changes' => $changeDetails,
                        'status' => 'rejected'
                    ];
                }
            }
        }

        return $changes;
    }

    // Get rejected deletion changes
    private function getRejectedDeletionChanges($history)
    {
        $details = [];
        $oldData = $history->old_values;

        if ($oldData) {
            $details[] = [
                'field' => 'Seller Name',
                'value' => $oldData['nama_penjual'] ?? 'N/A'
            ];

            $details[] = [
                'field' => 'Location',
                'value' => $oldData['letak_tanah'] ?? 'N/A'
            ];

            $details[] = [
                'field' => 'PPJB Number',
                'value' => $oldData['nomor_ppjb'] ?? 'N/A'
            ];

            if (isset($oldData['luas'])) {
                $details[] = [
                    'field' => 'Area',
                    'value' => $this->formatValue('luas', $oldData['luas'])
                ];
            }

            if (isset($oldData['harga'])) {
                $details[] = [
                    'field' => 'Price',
                    'value' => $this->formatValue('harga', $oldData['harga'])
                ];
            }
        }

        // Get deletion reason from new_values
        if (isset($history->new_values['deletion_reason'])) {
            $details[] = [
                'field' => 'Requested Deletion Reason',
                'value' => $history->new_values['deletion_reason']
            ];
        }

        return $details;
    }
}