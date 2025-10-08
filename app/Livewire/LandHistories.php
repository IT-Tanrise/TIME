<?php

namespace App\Livewire;

use App\Models\Land;
use App\Models\LandHistory;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class LandHistories extends Component
{
    use WithPagination;

    public $land;
    public $landId;
    
    // Filters
    public $filterAction = '';
    public $filterColumn = '';
    public $filterUser = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    
    public $showDetails = [];

    public function mount($landId)
    {
        $this->landId = $landId;
        $this->land = Land::with(['businessUnits', 'soils.businessUnit'])->findOrFail($landId);
    }

    public function render()
    {
        $histories = LandHistory::with(['user'])
            ->where('land_id', $this->landId)
            ->when($this->filterAction, function($query) {
                $query->where('action', $this->filterAction);
            })
            ->when($this->filterColumn && $this->filterAction === 'updated', function($query) {
                $query->whereRaw("JSON_EXTRACT(new_values, '$.{$this->filterColumn}') IS NOT NULL");
            })
            ->when($this->filterUser, function($query) {
                $query->whereHas('user', function($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->filterUser . '%');
                });
            })
            ->when($this->filterDateFrom, function($query) {
                $query->whereDate('created_at', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function($query) {
                $query->whereDate('created_at', '<=', $this->filterDateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $availableActions = $this->getAvailableActions();
        $availableColumns = $this->getAvailableColumns();

        return view('livewire.land-histories.index', compact('histories', 'availableActions', 'availableColumns'));
    }

    public function getAvailableActions()
    {
        return [
            ['value' => 'created', 'label' => 'Created'],
            ['value' => 'updated', 'label' => 'Updated'],
            ['value' => 'deleted', 'label' => 'Deleted'],
            ['value' => 'approved_update', 'label' => 'Approved Update'],
            ['value' => 'approved_deletion', 'label' => 'Approved Deletion'],
            ['value' => 'approved_creation', 'label' => 'Approved Creation'],
            ['value' => 'rejected', 'label' => 'Rejected'],
        ];
    }

    public function getAvailableColumns()
    {
        return [
            ['value' => 'lokasi_lahan', 'label' => 'Location'],
            ['value' => 'tahun_perolehan', 'label' => 'Acquisition Year'],
            ['value' => 'business_unit_id', 'label' => 'Business Unit'],
            ['value' => 'alamat', 'label' => 'Address'],
            ['value' => 'link_google_maps', 'label' => 'Google Maps Link'],
            ['value' => 'kota_kabupaten', 'label' => 'City/Regency'],
            ['value' => 'status', 'label' => 'Status'],
            ['value' => 'keterangan', 'label' => 'Notes'],
            ['value' => 'njop', 'label' => 'NJOP'],
            ['value' => 'est_harga_pasar', 'label' => 'Est. Market Price'],
        ];
    }

    public function toggleDetails($historyId)
    {
        $this->showDetails[$historyId] = !($this->showDetails[$historyId] ?? false);
    }

    public function resetFilters()
    {
        $this->filterAction = '';
        $this->filterColumn = '';
        $this->filterUser = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage();
    }

    public function backToLand()
    {
        return redirect()->route('lands');
    }

    public function getChangeDetails($history)
    {
        $details = [];

        if (in_array($history->action, ['updated', 'approved_update'])) {
            $oldData = $history->old_values ?? [];
            $newData = $history->new_values ?? [];

            foreach ($newData as $key => $newValue) {
                $oldValue = $oldData[$key] ?? '';
                if ($oldValue != $newValue) {
                    $details[] = [
                        'field' => $this->formatFieldName($key),
                        'old' => $this->formatValue($key, $oldValue),
                        'new' => $this->formatValue($key, $newValue),
                    ];
                }
            }
        } elseif ($history->action === 'deleted' || $history->action === 'approved_deletion') {
            $oldData = $history->old_values ?? [];
            foreach ($oldData as $key => $value) {
                if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                    $details[] = [
                        'field' => $this->formatFieldName($key),
                        'value' => $this->formatValue($key, $value),
                    ];
                }
            }
        } elseif ($history->action === 'created' || $history->action === 'approved_creation') {
            $newData = $history->new_values ?? [];
            foreach ($newData as $key => $value) {
                if (!in_array($key, ['created_at', 'updated_at'])) {
                    $details[] = [
                        'field' => $this->formatFieldName($key),
                        'value' => $this->formatValue($key, $value),
                    ];
                }
            }
        } elseif ($history->action === 'rejected') {
            // For rejected changes, show what was attempted
            $metadata = $history->metadata ?? [];
            $changeType = $metadata['change_type'] ?? null;
            
            if ($changeType === 'details') {
                // Show the changes that were rejected
                $oldData = $history->old_values ?? [];
                $newData = $history->new_values ?? [];
                
                foreach ($newData as $key => $newValue) {
                    $oldValue = $oldData[$key] ?? '';
                    if ($oldValue != $newValue) {
                        $details[] = [
                            'field' => $this->formatFieldName($key),
                            'old' => $this->formatValue($key, $oldValue),
                            'new' => $this->formatValue($key, $newValue),
                            'type' => 'change'
                        ];
                    }
                }
            } elseif ($changeType === 'create') {
                // Show the data that was rejected for creation
                $newData = $history->new_values ?? [];
                foreach ($newData as $key => $value) {
                    if (!in_array($key, ['created_at', 'updated_at', 'deletion_reason'])) {
                        $details[] = [
                            'field' => $this->formatFieldName($key),
                            'value' => $this->formatValue($key, $value),
                            'type' => 'create'
                        ];
                    }
                }
            } elseif ($changeType === 'delete') {
                // Show the data that was attempted to be deleted
                $oldData = $history->old_values ?? [];
                foreach ($oldData as $key => $value) {
                    if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                        $details[] = [
                            'field' => $this->formatFieldName($key),
                            'value' => $this->formatValue($key, $value),
                            'type' => 'delete'
                        ];
                    }
                }
            }
        }

        return $details;
    }

    private function formatFieldName($key)
    {
        $labels = [
            'lokasi_lahan' => 'Location',
            'tahun_perolehan' => 'Acquisition Year',
            'business_unit_id' => 'Business Unit',
            'alamat' => 'Address',
            'link_google_maps' => 'Google Maps Link',
            'kota_kabupaten' => 'City/Regency',
            'status' => 'Status',
            'keterangan' => 'Notes',
            'njop' => 'NJOP',
            'est_harga_pasar' => 'Est. Market Price',
        ];

        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }

    private function formatValue($key, $value)
    {
        if (is_null($value) || $value === '') {
            return 'N/A';
        }

        // Handle business_unit_id - show the business unit name
        if ($key === 'business_unit_id' && is_numeric($value)) {
            $businessUnit = \App\Models\BusinessUnit::find($value);
            echo $businessUnit;
            return $businessUnit ? "{$businessUnit->name} ({$businessUnit->code})" : "ID: {$value}";
        }

        $moneyFields = ['njop', 'est_harga_pasar'];
        if (in_array($key, $moneyFields)) {
            $numericValue = is_numeric($value) ? (float)$value : 0;
            return 'Rp ' . number_format($numericValue, 0, ',', '.');
        }

        return $value;
    }

    // Updating methods for filters
    public function updatingFilterAction()
    {
        $this->resetPage();
        if ($this->filterAction !== 'updated') {
            $this->filterColumn = '';
        }
    }

    public function updatingFilterColumn()
    {
        $this->resetPage();
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
}