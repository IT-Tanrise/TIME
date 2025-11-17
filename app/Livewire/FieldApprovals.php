<?php

namespace App\Livewire;

use App\Models\FieldApproval;
use App\Models\Field;
use Livewire\Component;
use Livewire\WithPagination;

class FieldApprovals extends Component
{
    use WithPagination;

    // UI state
    public $showDetailModal = false;
    public $showApproveModal = false;
    public $showRejectModal = false;

    // Selected approval
    public $selectedApproval = null;
    public $approvalId = null;
    public $reason = '';

    // Search & filter
    public $search = '';
    public $filterByStatus = '';
    public $filterByChangeType = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'reason' => 'required|string|min:10',
    ];

    protected $messages = [
        'reason.required' => 'Alasan wajib diisi.',
        'reason.min' => 'Alasan minimal 10 karakter.',
    ];

    public function render()
    {
        $approvals = FieldApproval::with([
                'field.businessUnit',
                'requestedBy',
                'approvedBy'
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('field', function ($subQ) {
                        $subQ->where('nama_bidang', 'like', '%' . $this->search . '%')
                             ->orWhere('nomor_bidang', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('requestedBy', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->filterByStatus, function ($query) {
                $query->where('status', $this->filterByStatus);
            })
            ->when($this->filterByChangeType, function ($query) {
                $query->where('change_type', $this->filterByChangeType);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.field-approvals.index', compact('approvals'));
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
        $this->selectedApproval = FieldApproval::with([
            'field.businessUnit',
            'requestedBy',
            'approvedBy'
        ])->findOrFail($id);
        
        $this->showDetailModal = true;
    }

    public function showApproveModalView($id)
    {
        $this->approvalId = $id;
        $this->reason = '';
        $this->showApproveModal = true;
    }

    public function showRejectModalView($id)
    {
        $this->approvalId = $id;
        $this->reason = '';
        $this->showRejectModal = true;
    }

    public function approve()
    {
        // Reason is optional for approval
        try {
            $approval = FieldApproval::findOrFail($this->approvalId);

            if ($approval->status !== FieldApproval::STATUS_PENDING) {
                session()->flash('error', 'This approval request has already been processed.');
                $this->hideApproveModalView();
                return;
            }

            // Approve and apply changes
            $approval->approve(auth()->id(), $this->reason);

            session()->flash('message', 'Approval request approved successfully.');
            $this->hideApproveModalView();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function reject()
    {
        $this->validate();

        try {
            $approval = FieldApproval::findOrFail($this->approvalId);

            if ($approval->status !== FieldApproval::STATUS_PENDING) {
                session()->flash('error', 'This approval request has already been processed.');
                $this->hideRejectModalView();
                return;
            }

            // Reject with reason
            $approval->reject(auth()->id(), $this->reason);

            session()->flash('message', 'Approval request rejected.');
            $this->hideRejectModalView();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function bulkApprove()
    {
        try {
            $pendingApprovals = FieldApproval::where('status', FieldApproval::STATUS_PENDING)
                ->when($this->filterByChangeType, function ($query) {
                    $query->where('change_type', $this->filterByChangeType);
                })
                ->get();

            $count = 0;
            foreach ($pendingApprovals as $approval) {
                $approval->approve(auth()->id(), 'Bulk approval');
                $count++;
            }

            session()->flash('message', "{$count} approval request(s) approved successfully.");
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function hideDetailModalView()
    {
        $this->showDetailModal = false;
        $this->selectedApproval = null;
    }

    public function hideApproveModalView()
    {
        $this->showApproveModal = false;
        $this->approvalId = null;
        $this->reason = '';
        $this->resetValidation();
    }

    public function hideRejectModalView()
    {
        $this->showRejectModal = false;
        $this->approvalId = null;
        $this->reason = '';
        $this->resetValidation();
    }

    // Get changes in readable format
    public function getFormattedChanges($approval)
    {
        $changes = [];
        
        if ($approval->change_type === FieldApproval::TYPE_CREATE) {
            return $approval->new_values;
        }
        
        if ($approval->change_type === FieldApproval::TYPE_DELETE) {
            return [
                'reason' => $approval->new_values['deletion_reason'] ?? 'No reason provided'
            ];
        }

        foreach ($approval->new_values as $key => $newValue) {
            $oldValue = $approval->old_values[$key] ?? null;
            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'old' => $this->formatValue($key, $oldValue),
                    'new' => $this->formatValue($key, $newValue),
                ];
            }
        }

        return $changes;
    }

    private function formatValue($key, $value)
    {
        if ($key === 'business_unit_id') {
            $bu = \App\Models\BusinessUnit::find($value);
            return $bu ? $bu->name : $value;
        }

        if ($key === 'status') {
            return ucfirst($value);
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
        ];

        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }
}