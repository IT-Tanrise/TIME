<?php

namespace App\Livewire;

use App\Models\FieldHistory;
use App\Models\Field;
use Livewire\Component;
use Livewire\WithPagination;

class FieldHistories extends Component
{
    use WithPagination;

    // UI state
    public $showDetailModal = false;

    // Selected history
    public $selectedHistory = null;

    // Search & filter
    public $search = '';
    public $filterByField = '';
    public $filterByAction = '';
    public $filterByUser = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function render()
    {
        $histories = FieldHistory::with(['field.businessUnit', 'user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('field', function ($subQ) {
                        $subQ->where('nama_bidang', 'like', '%' . $this->search . '%')
                             ->orWhere('nomor_bidang', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterByField, function ($query) {
                $query->where('field_id', $this->filterByField);
            })
            ->when($this->filterByAction, function ($query) {
                $query->where('action', $this->filterByAction);
            })
            ->when($this->filterByUser, function ($query) {
                $query->where('user_id', $this->filterByUser);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        $fields = Field::orderBy('nama_bidang')->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('livewire.field-histories.index', compact('histories', 'fields', 'users'));
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function showDetail($id)
    {
        $this->selectedHistory = FieldHistory::with([
            'field.businessUnit',
            'user'
        ])->findOrFail($id);
        
        $this->showDetailModal = true;
    }

    public function hideDetailModalView()
    {
        $this->showDetailModal = false;
        $this->selectedHistory = null;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterByField = '';
        $this->filterByAction = '';
        $this->filterByUser = '';
        $this->dateFrom = '';
        $this->dateTo = '';
    }

    public function exportHistories()
    {
        // Export logic here - can be CSV, Excel, or PDF
        // For now, just show a message
        session()->flash('message', 'Export feature will be implemented soon.');
    }

    // Get formatted changes
    public function getFormattedChanges($history)
    {
        $changes = [];
        
        if ($history->action === FieldHistory::ACTION_CREATED) {
            foreach ($history->new_values as $key => $value) {
                if (!in_array($key, ['created_at', 'updated_at', 'created_by', 'updated_by'])) {
                    $changes[$key] = [
                        'label' => $this->getFieldLabel($key),
                        'new' => $this->formatValue($key, $value),
                    ];
                }
            }
            return $changes;
        }
        
        if ($history->action === FieldHistory::ACTION_DELETED) {
            foreach ($history->old_values as $key => $value) {
                if (!in_array($key, ['created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at'])) {
                    $changes[$key] = [
                        'label' => $this->getFieldLabel($key),
                        'old' => $this->formatValue($key, $value),
                    ];
                }
            }
            return $changes;
        }

        // For updates, show what changed
        if ($history->action === FieldHistory::ACTION_UPDATED && $history->old_values && $history->new_values) {
            foreach ($history->new_values as $key => $newValue) {
                // Skip metadata
                if ($key === '_approval_metadata') continue;
                
                $oldValue = $history->old_values[$key] ?? null;
                if ($oldValue != $newValue) {
                    $changes[$key] = [
                        'label' => $this->getFieldLabel($key),
                        'old' => $this->formatValue($key, $oldValue),
                        'new' => $this->formatValue($key, $newValue),
                    ];
                }
            }
        }

        return $changes;
    }

    private function formatValue($key, $value)
    {
        if ($value === null) return '-';
        
        if ($key === 'business_unit_id') {
            $bu = \App\Models\BusinessUnit::find($value);
            return $bu ? $bu->name : $value;
        }

        if ($key === 'status') {
            return ucfirst($value);
        }

        if ($key === 'created_by' || $key === 'updated_by') {
            $user = \App\Models\User::find($value);
            return $user ? $user->name : $value;
        }

        return $value;
    }

    public function getFieldLabel($key)
    {
        $labels = [
            'nama_bidang' => 'Nama Bidang',
            'business_unit_id' => 'Business Unit',
            'nomor_bidang' => 'Nomor Bidang',
            'status' => 'Status',
            'reason_delete' => 'Reason Delete',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];

        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }

    // Get statistics
    public function getStatistics()
    {
        $totalHistories = FieldHistory::count();
        $todayHistories = FieldHistory::whereDate('created_at', today())->count();
        $thisWeekHistories = FieldHistory::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        $thisMonthHistories = FieldHistory::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total' => $totalHistories,
            'today' => $todayHistories,
            'this_week' => $thisWeekHistories,
            'this_month' => $thisMonthHistories,
        ];
    }

    // Get action breakdown
    public function getActionBreakdown()
    {
        return [
            'created' => FieldHistory::where('action', FieldHistory::ACTION_CREATED)->count(),
            'updated' => FieldHistory::where('action', FieldHistory::ACTION_UPDATED)->count(),
            'deleted' => FieldHistory::where('action', FieldHistory::ACTION_DELETED)->count(),
            'restored' => FieldHistory::where('action', FieldHistory::ACTION_RESTORED)->count(),
        ];
    }

    // Get top users (most active)
    public function getTopUsers($limit = 5)
    {
        return FieldHistory::select('user_id', \DB::raw('count(*) as total'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get();
    }
}