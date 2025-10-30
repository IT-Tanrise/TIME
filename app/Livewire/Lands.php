<?php

namespace App\Livewire;

use App\Models\Land;
use App\Models\LandApproval;
use App\Models\BusinessUnit;
use Livewire\Component;
use Livewire\WithPagination;

class Lands extends Component
{
    use WithPagination;

    public $land;
    public $landId;
    public $lokasi_lahan = '';
    public $tahun_perolehan = '';
    public $alamat = '';
    public $link_google_maps = '';
    public $kota_kabupaten = '';
    public $status = '';
    public $keterangan = '';
    public $njop = '';
    public $est_harga_pasar = '';
    
    // Business Unit fields
    public $business_unit_id = '';
    public $businessUnitSearch = '';
    public $showBusinessUnitDropdown = false;
    public $allowBusinessUnitChange = false;
    
    // Add display properties for formatted inputs
    public $njop_display = '';
    public $est_harga_pasar_display = '';
    
    public $showForm = false;
    public $showDetailForm = false;
    public $isEdit = false;
    public $search = '';

    // Delete confirmation
    public $showDeleteModal = false;
    public $deleteId = null;
    public $deletionReason = '';

    // Add filter properties similar to soils
    public $filterBusinessUnit = '';
    public $filterStatus = '';
    public $filterKotaKabupaten = '';

    // Add these properties for business unit filtering
    public $filterByBusinessUnit = null;
    public $businessUnit = null;

    protected function rules()
    {
        return [
            'lokasi_lahan' => 'required|string|max:255',
            'tahun_perolehan' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'business_unit_id' => 'required|exists:business_units,id',
            'alamat' => 'nullable|string',
            'link_google_maps' => 'nullable|string|max:255',
            'kota_kabupaten' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'njop' => 'nullable|numeric|min:0',
            'est_harga_pasar' => 'nullable|numeric|min:0'
        ];
    }

    public function mount($businessUnit = null)
    {
        if ($businessUnit) {
            if (is_numeric($businessUnit)) {
                $this->businessUnit = BusinessUnit::find($businessUnit);
                if ($this->businessUnit) {
                    $this->filterByBusinessUnit = $this->businessUnit->id;
                }
            } elseif ($businessUnit instanceof BusinessUnit) {
                $this->filterByBusinessUnit = $businessUnit->id;
                $this->businessUnit = $businessUnit;
            }
        }
    }

    public function render()
    {
        $lands = Land::with([
                'businessUnit:id,name,code', // Direct business unit
                'soils:id,land_id,luas,harga', 
                'soils.biayaTambahanSoils:id,soil_id,harga',
                'pendingApprovals'
            ])
            ->withCount(['projects', 'soils'])
            ->when($this->search, function($query) {
                $query->where('lokasi_lahan', 'like', '%' . $this->search . '%')
                    ->orWhere('kota_kabupaten', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%')
                    ->orWhere('alamat', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterByBusinessUnit, function($query) {
                $query->where('business_unit_id', $this->filterByBusinessUnit);
            })
            ->when($this->filterBusinessUnit, function($query) {
                $query->where('business_unit_id', $this->filterBusinessUnit);
            })
            ->when($this->filterStatus, function($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterKotaKabupaten, function($query) {
                $query->where('kota_kabupaten', 'like', '%' . $this->filterKotaKabupaten . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $businessUnits = BusinessUnit::orderBy('name')->get();
        $statuses = Land::distinct()->pluck('status')->filter()->sort();
        $kotaKabupaten = Land::distinct()->whereNotNull('kota_kabupaten')
                            ->where('kota_kabupaten', '!=', '')
                            ->pluck('kota_kabupaten')
                            ->filter()
                            ->sort();

        return view('livewire.lands.index', compact('lands', 'businessUnits', 'statuses', 'kotaKabupaten'));
    }

    public function showCreateForm()
    {
        $this->resetForm();
        
        // Auto-fill business unit if filtered
        if ($this->filterByBusinessUnit && $this->businessUnit) {
            $this->business_unit_id = $this->filterByBusinessUnit;
            $this->businessUnitSearch = $this->businessUnit->name;
            $this->allowBusinessUnitChange = false;
        }
        
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        $this->land = Land::findOrFail($id);
        $this->landId = $this->land->id;
        $this->lokasi_lahan = $this->land->lokasi_lahan;
        $this->tahun_perolehan = $this->land->tahun_perolehan;
        $this->business_unit_id = $this->land->business_unit_id;
        $this->alamat = $this->land->alamat;
        $this->link_google_maps = $this->land->link_google_maps;
        $this->kota_kabupaten = $this->land->kota_kabupaten;
        $this->status = $this->land->status;
        $this->keterangan = $this->land->keterangan;
        $this->njop = $this->land->njop;
        $this->est_harga_pasar = $this->land->est_harga_pasar;

        // Set business unit search display
        if ($this->land->businessUnit) {
            $this->businessUnitSearch = $this->land->businessUnit->name;
        }
        $this->njop_display = $this->land->njop ? number_format($this->land->njop, 0, ',', '.') : '';
        $this->est_harga_pasar_display = $this->land->est_harga_pasar ? number_format($this->land->est_harga_pasar, 0, ',', '.') : '';
        
        $this->showForm = true;
        $this->isEdit = true;
    }

    public function showDetail($id)
    {
        $this->landId = $id;
        $this->showDetailForm = true;
    }

    // Business Unit Dropdown Methods
    public function searchBusinessUnits()
    {
        if (!$this->filterByBusinessUnit || $this->allowBusinessUnitChange) {
            $this->showBusinessUnitDropdown = true;
        }
    }

    public function getFilteredBusinessUnits()
    {
        return BusinessUnit::when($this->businessUnitSearch, function($query) {
                $query->where('name', 'like', '%' . $this->businessUnitSearch . '%')
                      ->orWhere('code', 'like', '%' . $this->businessUnitSearch . '%');
            })
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    public function selectBusinessUnit($id, $name)
    {
        $this->business_unit_id = $id;
        $this->businessUnitSearch = $name;
        $this->showBusinessUnitDropdown = false;
    }

    public function allowBusinessUnitChangeFunc()
    {
        $this->allowBusinessUnitChange = true;
        $this->showBusinessUnitDropdown = true;
    }

    public function lockBusinessUnit()
    {
        $this->allowBusinessUnitChange = false;
        $this->showBusinessUnitDropdown = false;
        if ($this->businessUnit) {
            $this->business_unit_id = $this->businessUnit->id;
            $this->businessUnitSearch = $this->businessUnit->name;
        }
    }

    public function closeDropdowns()
    {
        $this->showBusinessUnitDropdown = false;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'lokasi_lahan' => $this->lokasi_lahan,
            'tahun_perolehan' => $this->tahun_perolehan,
            'business_unit_id' => $this->business_unit_id ?: null,
            'alamat' => $this->alamat,
            'link_google_maps' => $this->link_google_maps,
            'kota_kabupaten' => $this->kota_kabupaten,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            'njop' => $this->njop ?: null,
            'est_harga_pasar' => $this->est_harga_pasar ?: null
        ];

        if ($this->isEdit) {
            if (auth()->user()->can('land-data.approval')) {
                $this->land->update($data);
                session()->flash('message', 'Land updated successfully.');
            } else {
                LandApproval::create([
                    'land_id' => $this->land->id,
                    'requested_by' => auth()->id(),
                    'change_type' => 'details',
                    'old_data' => $this->land->only(array_keys($data)),
                    'new_data' => $data,
                    'status' => 'pending'
                ]);
                session()->flash('message', 'Update request submitted for approval.');
            }
        } else {
            if (auth()->user()->can('land-data.approval')) {
                Land::create($data);
                session()->flash('message', 'Land created successfully.');
            } else {
                LandApproval::create([
                    'requested_by' => auth()->id(),
                    'change_type' => 'create',
                    'new_data' => $data,
                    'status' => 'pending'
                ]);
                session()->flash('message', 'Creation request submitted for approval.');
            }
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->deletionReason = '';
        $this->showDeleteModal = true;
    }

    public function hideDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deletionReason = '';
        $this->resetValidation(['deletionReason']);
    }

    public function delete()
    {
        $this->validate([
            'deletionReason' => 'required|string|min:10',
        ], [
            'deletionReason.required' => 'Please provide a reason for deletion.',
            'deletionReason.min' => 'Reason must be at least 10 characters.',
        ]);

        $land = Land::findOrFail($this->deleteId);
        
        if ($land->projects()->count() > 0 || $land->soils()->count() > 0) {
            session()->flash('error', 'Cannot delete Land. It has related projects or soil records.');
            $this->hideDeleteModal();
            return;
        }

        if (auth()->user()->can('land-data.approval')) {
            $land->delete();
            session()->flash('message', 'Land deleted successfully.');
        } else {
            LandApproval::create([
                'land_id' => $land->id,
                'requested_by' => auth()->id(),
                'change_type' => 'delete',
                'old_data' => $land->toArray(),
                'new_data' => ['deletion_reason' => $this->deletionReason],
                'status' => 'pending'
            ]);
            session()->flash('message', 'Deletion request submitted for approval.');
        }

        $this->hideDeleteModal();
    }

    public function backToIndex()
    {
        $this->showForm = false;
        $this->showDetailForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'land', 'landId', 'lokasi_lahan', 'tahun_perolehan',
            'business_unit_id', 'businessUnitSearch', 'alamat', 'link_google_maps', 
            'kota_kabupaten', 'status', 'keterangan', 'njop', 'est_harga_pasar',
            'njop_display', 'est_harga_pasar_display',
            'showBusinessUnitDropdown', 'allowBusinessUnitChange'
        ]);
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->filterBusinessUnit = '';
        $this->filterStatus = '';
        $this->filterKotaKabupaten = '';
        $this->search = '';
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterBusinessUnit()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterKotaKabupaten()
    {
        $this->resetPage();
    }

    public function clearBusinessUnitFilter()
    {
        $this->filterByBusinessUnit = null;
        $this->businessUnit = null;
        return redirect()->route('lands');
    }

    public function getCurrentBusinessUnitName()
    {
        return $this->businessUnit ? $this->businessUnit->name : null;
    }

    public function isFiltered()
    {
        return !is_null($this->filterByBusinessUnit);
    }

    public function backToBusinessUnit()
    {
        if ($this->businessUnit) {
            return redirect()->route('business-units', ['view' => 'show', 'id' => $this->businessUnit->id]);
        }
        return redirect()->route('business-units');
    }

    public function getStatusOptions()
    {
        return [
            'Available' => 'Available',
            'Reserved' => 'Reserved',
            'Sold' => 'Sold',
            'Development' => 'In Development',
            'Hold' => 'On Hold'
        ];
    }
}