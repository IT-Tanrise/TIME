<?php

namespace App\Livewire;

use App\Models\Field;
use App\Models\BusinessUnit;
use App\Models\FieldApproval;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Fields extends Component
{
    use WithPagination;

    // Form properties
    public $fieldId = null;
    public $nama_bidang = '';
    public $business_unit_id = '';
    public $nomor_bidang = '';
    public $status = '';
    public $reason_delete = '';

    // UI state
    public $showForm = false;
    public $showDetailModal = false;
    public $showDeleteModal = false;
    public $isEdit = false;

    // Search & filter
    public $search = '';
    public $filterByBusinessUnit = '';
    public $filterByStatus = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Business unit search
    public $businessUnitSearch = '';
    public $showBusinessUnitDropdown = false;

    // Detail view
    public $selectedField = null;

    protected $rules = [
        'nama_bidang' => 'required|string|max:255',
        'business_unit_id' => 'required|exists:business_units,id',
        'status' => 'nullable|in:active,inactive,pending',
    ];

    protected $messages = [
        'nama_bidang.required' => 'Nama bidang wajib diisi.',
        'business_unit_id.required' => 'Business unit wajib dipilih.',
    ];

    public function mount()
    {
        $this->status = Field::STATUS_PENDING;
    }

    public function render()
    {
        $fields = Field::with(['businessUnit', 'createdBy', 'updatedBy'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_bidang', 'like', '%' . $this->search . '%')
                      ->orWhere('nomor_bidang', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterByBusinessUnit, function ($query) {
                $query->where('business_unit_id', $this->filterByBusinessUnit);
            })
            ->when($this->filterByStatus, function ($query) {
                $query->where('status', $this->filterByStatus);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $businessUnits = BusinessUnit::orderBy('name')->get();

        return view('livewire.fields.index', compact('fields', 'businessUnits'));
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

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEdit = false;
        $this->status = Field::STATUS_PENDING;
    }

    public function showEditForm($id)
    {
        $field = Field::findOrFail($id);
        
        $this->fieldId = $field->id;
        $this->nama_bidang = $field->nama_bidang;
        $this->business_unit_id = $field->business_unit_id;
        $this->nomor_bidang = $field->nomor_bidang;
        $this->status = $field->status;
        
        if ($field->businessUnit) {
            $this->businessUnitSearch = $field->businessUnit->name;
        }
        
        $this->showForm = true;
        $this->isEdit = true;
    }

    public function showDetail($id)
    {
        $this->selectedField = Field::with([
            'businessUnit', 
            'createdBy', 
            'updatedBy',
            'histories.user',
            'approvals.requestedBy',
            'approvals.approvedBy'
        ])->findOrFail($id);
        
        $this->showDetailModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                if ($this->isEdit) {
                    $this->updateField();
                } else {
                    $this->createField();
                }
            });

            $this->resetForm();
            $this->showForm = false;
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    private function createField()
    {
        // Check if user has approval permission
        if (auth()->user()->can('field.approval')) {
            // Generate nomor bidang
            $nomorBidang = Field::generateNomorBidang($this->business_unit_id);
            
            // Create directly with active status
            Field::create([
                'nama_bidang' => $this->nama_bidang,
                'business_unit_id' => $this->business_unit_id,
                'nomor_bidang' => $nomorBidang,
                'status' => Field::STATUS_ACTIVE,
            ]);

            session()->flash('message', 'Field created successfully.');
        } else {
            // Generate nomor bidang for approval
            $nomorBidang = Field::generateNomorBidang($this->business_unit_id);
            
            // Create approval request
            FieldApproval::create([
                'field_id' => null,
                'requested_by' => auth()->id(),
                'status' => FieldApproval::STATUS_PENDING,
                'change_type' => FieldApproval::TYPE_CREATE,
                'old_values' => [],
                'new_values' => [
                    'nama_bidang' => $this->nama_bidang,
                    'business_unit_id' => $this->business_unit_id,
                    'nomor_bidang' => $nomorBidang,
                    'status' => Field::STATUS_PENDING,
                ],
            ]);

            session()->flash('warning', 'Your field creation request has been submitted for approval.');
        }
    }

    private function updateField()
    {
        $field = Field::findOrFail($this->fieldId);
        
        $oldValues = $field->only(['nama_bidang', 'business_unit_id', 'status']);
        $newValues = [
            'nama_bidang' => $this->nama_bidang,
            'business_unit_id' => $this->business_unit_id,
            'status' => $this->status,
        ];

        // Check if data actually changed
        if (!$this->hasDataChanged($oldValues, $newValues)) {
            session()->flash('info', 'No changes detected.');
            return;
        }

        if (auth()->user()->can('field.approval')) {
            // Update directly
            $field->update($newValues);
            session()->flash('message', 'Field updated successfully.');
        } else {
            // Create approval request
            FieldApproval::create([
                'field_id' => $field->id,
                'requested_by' => auth()->id(),
                'status' => FieldApproval::STATUS_PENDING,
                'change_type' => FieldApproval::TYPE_UPDATE,
                'old_values' => $oldValues,
                'new_values' => $newValues,
            ]);

            session()->flash('warning', 'Your field update request has been submitted for approval.');
        }
    }

    public function showDeleteModalView($id)
    {
        $this->fieldId = $id;
        $this->reason_delete = '';
        $this->showDeleteModal = true;
    }

    public function deleteWithReason()
    {
        $this->validate([
            'reason_delete' => 'required|string|min:10',
        ], [
            'reason_delete.required' => 'Alasan penghapusan wajib diisi.',
            'reason_delete.min' => 'Alasan penghapusan minimal 10 karakter.',
        ]);

        try {
            $field = Field::findOrFail($this->fieldId);

            if (auth()->user()->can('field.approval')) {
                // Delete directly (soft delete + set inactive)
                $field->historyLogging = true;
                $field->update([
                    'status' => Field::STATUS_INACTIVE,
                    'reason_delete' => $this->reason_delete,
                ]);
                $field->delete();
                $field->historyLogging = false;
                
                $field->logHistory('deleted');

                session()->flash('message', 'Field deactivated successfully.');
            } else {
                // Create approval request
                FieldApproval::create([
                    'field_id' => $field->id,
                    'requested_by' => auth()->id(),
                    'status' => FieldApproval::STATUS_PENDING,
                    'change_type' => FieldApproval::TYPE_DELETE,
                    'old_values' => $field->only(['nama_bidang', 'business_unit_id', 'nomor_bidang', 'status']),
                    'new_values' => [
                        'deletion_reason' => $this->reason_delete,
                    ],
                ]);

                session()->flash('warning', 'Your field deactivation request has been submitted for approval.');
            }

            $this->hideDeleteModalView();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function activateField($id)
    {
        try {
            $field = Field::findOrFail($id);

            if (auth()->user()->can('field.approval')) {
                // Activate directly
                $field->update(['status' => Field::STATUS_ACTIVE]);
                session()->flash('message', 'Field activated successfully.');
            } else {
                // Create approval request
                FieldApproval::create([
                    'field_id' => $field->id,
                    'requested_by' => auth()->id(),
                    'status' => FieldApproval::STATUS_PENDING,
                    'change_type' => FieldApproval::TYPE_ACTIVATE,
                    'old_values' => ['status' => $field->status],
                    'new_values' => ['status' => Field::STATUS_ACTIVE],
                ]);

                session()->flash('warning', 'Your field activation request has been submitted for approval.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function deactivateField($id)
    {
        try {
            $field = Field::findOrFail($id);

            if (auth()->user()->can('field.approval')) {
                // Deactivate directly
                $field->update(['status' => Field::STATUS_INACTIVE]);
                session()->flash('message', 'Field deactivated successfully.');
            } else {
                // Create approval request
                FieldApproval::create([
                    'field_id' => $field->id,
                    'requested_by' => auth()->id(),
                    'status' => FieldApproval::STATUS_PENDING,
                    'change_type' => FieldApproval::TYPE_DEACTIVATE,
                    'old_values' => ['status' => $field->status],
                    'new_values' => ['status' => Field::STATUS_INACTIVE],
                ]);

                session()->flash('warning', 'Your field deactivation request has been submitted for approval.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // Business unit search
    public function updatedBusinessUnitSearch()
    {
        $this->showBusinessUnitDropdown = !empty($this->businessUnitSearch);
    }

    public function selectBusinessUnit($id)
    {
        $businessUnit = BusinessUnit::find($id);
        if ($businessUnit) {
            $this->business_unit_id = $businessUnit->id;
            $this->businessUnitSearch = $businessUnit->name;
            $this->showBusinessUnitDropdown = false;
        }
    }

    public function getFilteredBusinessUnits()
    {
        if (empty($this->businessUnitSearch)) {
            return BusinessUnit::orderBy('name')->limit(20)->get();
        }

        return BusinessUnit::where('name', 'like', '%' . $this->businessUnitSearch . '%')
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    private function hasDataChanged($oldData, $newData)
    {
        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                return true;
            }
        }
        return false;
    }

    public function resetForm()
    {
        $this->fieldId = null;
        $this->nama_bidang = '';
        $this->business_unit_id = '';
        $this->nomor_bidang = '';
        $this->status = Field::STATUS_PENDING;
        $this->reason_delete = '';
        $this->businessUnitSearch = '';
        $this->showBusinessUnitDropdown = false;
        $this->resetValidation();
    }

    public function hideFormView()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function hideDetailModalView()
    {
        $this->showDetailModal = false;
        $this->selectedField = null;
    }

    public function hideDeleteModalView()
    {
        $this->showDeleteModal = false;
        $this->fieldId = null;
        $this->reason_delete = '';
        $this->resetValidation(['reason_delete']);
    }
}