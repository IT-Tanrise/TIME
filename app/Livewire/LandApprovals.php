<?php

namespace App\Livewire;

use App\Models\LandApproval;
use Livewire\Component;
use Livewire\WithPagination;

class LandApprovals extends Component
{
    use WithPagination;

    public $showDetails = [];
    public $showRejectionModal = false;
    public $rejectionApprovalId = null;
    public $rejectionReason = '';

    protected $listeners = ['refreshApprovals' => '$refresh'];

    public function render()
    {
        $pendingApprovals = LandApproval::with(['land', 'requestedBy', 'approvedBy'])
            ->pending()
            ->when(!auth()->user()->can('land-data.approval'), function($query) {
                // If user doesn't have approval permission, only show their own requests
                $query->where('requested_by', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return view('livewire.land-approvals.index', compact('pendingApprovals'));
    }

    public function toggleDetails($approvalId)
    {
        $this->showDetails[$approvalId] = !($this->showDetails[$approvalId] ?? false);
    }

    public function approve($approvalId)
    {
        if (!auth()->user()->can('land-data.approval')) {
            session()->flash('error', 'You do not have permission to approve changes.');
            return;
        }

        $approval = LandApproval::findOrFail($approvalId);
        
        try {
            $approval->approve(auth()->id());
            session()->flash('message', 'Changes approved and applied successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error approving changes: ' . $e->getMessage());
        }
    }

    public function showRejectModal($approvalId)
    {
        $this->rejectionApprovalId = $approvalId;
        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }

    public function hideRejectModal()
    {
        $this->showRejectionModal = false;
        $this->rejectionApprovalId = null;
        $this->rejectionReason = '';
        $this->resetValidation();
    }

    public function rejectWithReason()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5',
        ], [
            'rejectionReason.required' => 'Please provide a reason for rejection.',
            'rejectionReason.min' => 'Reason must be at least 5 characters.',
        ]);

        if (!auth()->user()->can('land-data.approval')) {
            session()->flash('error', 'You do not have permission to reject changes.');
            return;
        }

        $approval = LandApproval::findOrFail($this->rejectionApprovalId);
        $approval->reject(auth()->id(), $this->rejectionReason);

        session()->flash('message', 'Changes rejected successfully.');
        $this->hideRejectModal();
    }

    public function getChangeDetails($approval)
    {
        $details = [];

        if ($approval->change_type === 'details') {
            $oldData = $approval->old_data ?? [];
            $newData = $approval->new_data ?? [];

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
        } elseif ($approval->change_type === 'delete') {
            $oldData = $approval->old_data ?? [];
            foreach ($oldData as $key => $value) {
                $details[] = [
                    'field' => $this->formatFieldName($key),
                    'value' => $this->formatValue($key, $value),
                ];
            }
        } elseif ($approval->change_type === 'create') {
            $newData = $approval->new_data ?? [];
            foreach ($newData as $key => $value) {
                if (!in_array($key, ['created_at', 'updated_at'])) {
                    $details[] = [
                        'field' => $this->formatFieldName($key),
                        'value' => $this->formatValue($key, $value),
                    ];
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
            \Log::info('businessUnit: ', [
                'businessUnit' => $businessUnit
            ]);
            return $businessUnit ? "{$businessUnit->name} ({$businessUnit->code})" : "ID: {$value}";
        }

        $moneyFields = ['njop', 'est_harga_pasar'];
        if (in_array($key, $moneyFields)) {
            $numericValue = is_numeric($value) ? (float)$value : 0;
            return 'Rp ' . number_format($numericValue, 0, ',', '.');
        }

        return $value;
    }
}