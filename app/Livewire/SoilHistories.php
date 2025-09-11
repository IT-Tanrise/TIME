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
        $histories = SoilHistory::with(['user'])
            ->where('soil_id', $this->soilId)
            ->when($this->filterAction, function($query) {
                $query->where('action', $this->filterAction);
            })
            ->when($this->filterUser, function($query) {
                $query->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->filterUser . '%');
                });
            })
            ->when($this->filterDateFrom, function($query) {
                $query->whereDate('created_at', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function($query) {
                $query->whereDate('created_at', '<=', $this->filterDateTo);
            })
            ->when($this->filterColumn, function($query) {
                $query->whereJsonContains('changes', $this->filterColumn);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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

        // Get available columns that have been updated
        $availableColumns = $this->getAvailableColumns();

        return view('livewire.soil-histories.index', compact('histories', 'availableActions', 'availableUsers', 'availableColumns'));
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
        if (!$history->old_values || !$history->new_values) {
            return null;
        }

        $changes = [];
        foreach ($history->changes as $field) {
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

    private function formatValue($field, $value)
    {
        if (is_null($value) || $value === '') {
            return '-';
        }

        // Format specific fields
        switch ($field) {
            case 'harga':
            case 'luas':
                return is_numeric($value) ? number_format($value, 0, ',', '.') : $value;
            case 'tanggal_ppjb':
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
            'letak_tanah' => 'Land Location',
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
        ];

        return $fieldMap[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}