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
    public $nilai_perolehan = '';
    public $alamat = '';
    public $link_google_maps = '';
    public $kota_kabupaten = '';
    public $status = '';
    public $keterangan = '';
    public $nominal_b = '';
    public $njop = '';
    public $est_harga_pasar = '';
    
    // Add display properties for formatted inputs
    public $nilai_perolehan_display = '';
    public $nominal_b_display = '';
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
            'nilai_perolehan' => 'required|numeric|min:0',
            'alamat' => 'nullable|string',
            'link_google_maps' => 'nullable|string|max:255',
            'kota_kabupaten' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'njop' => 'nullable|numeric|min:0',
            'est_harga_pasar' => 'nullable|numeric|min:0'
        ];
    }

    // Add mount method to handle business unit parameter
    public function mount($businessUnit = null)
    {
        if ($businessUnit) {
            // Handle numeric businessUnit parameter from route
            if (is_numeric($businessUnit)) {
                // Find the business unit by ID
                $this->businessUnit = BusinessUnit::find($businessUnit);
                if ($this->businessUnit) {
                    $this->filterByBusinessUnit = $this->businessUnit->id;
                }
            } elseif ($businessUnit instanceof BusinessUnit) {
                // If it's already a BusinessUnit model
                $this->filterByBusinessUnit = $businessUnit->id;
                $this->businessUnit = $businessUnit;
            }
        }
    }

    public function render()
    {
        // Query lands with business unit filtering through related soils
        $lands = Land::with([
                'soils:id,land_id,luas,harga,business_unit_id', 
                'soils.businessUnit:id,name,code',
                'soils.biayaTambahanSoils:id,soil_id,harga',
                'businessUnits',
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
                // Filter lands that have soils belonging to the business unit
                $query->whereHas('soils', function($soilQuery) {
                    $soilQuery->where('business_unit_id', $this->filterByBusinessUnit);
                });
            })
            ->when($this->filterBusinessUnit, function($query) {
                // Filter lands that have soils belonging to the selected business unit
                $query->whereHas('soils', function($soilQuery) {
                    $soilQuery->where('business_unit_id', $this->filterBusinessUnit);
                });
            })
            ->when($this->filterStatus, function($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterKotaKabupaten, function($query) {
                $query->where('kota_kabupaten', 'like', '%' . $this->filterKotaKabupaten . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get business units for filter dropdown
        $businessUnits = BusinessUnit::orderBy('name')->get();
        
        // Get unique statuses for filter dropdown
        $statuses = Land::distinct()->pluck('status')->filter()->sort();
        
        // Get unique cities/regencies for filter dropdown
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
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        $this->land = Land::findOrFail($id);
        $this->landId = $this->land->id;
        $this->lokasi_lahan = $this->land->lokasi_lahan;
        $this->tahun_perolehan = $this->land->tahun_perolehan;
        $this->nilai_perolehan = $this->land->nilai_perolehan;
        $this->alamat = $this->land->alamat;
        $this->link_google_maps = $this->land->link_google_maps;
        $this->kota_kabupaten = $this->land->kota_kabupaten;
        $this->status = $this->land->status;
        $this->keterangan = $this->land->keterangan;
        $this->nominal_b = $this->land->nominal_b;
        $this->njop = $this->land->njop;
        $this->est_harga_pasar = $this->land->est_harga_pasar;

        // Set display values with formatting
        $this->nilai_perolehan_display = $this->land->nilai_perolehan ? number_format($this->land->nilai_perolehan, 0, ',', '.') : '';
        $this->nominal_b_display = $this->land->nominal_b ? number_format($this->land->nominal_b, 0, ',', '.') : '';
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

    public function save()
    {
        $this->validate();

        $data = [
            'lokasi_lahan' => $this->lokasi_lahan,
            'tahun_perolehan' => $this->tahun_perolehan,
            'nilai_perolehan' => $this->nilai_perolehan,
            'alamat' => $this->alamat,
            'link_google_maps' => $this->link_google_maps,
            'kota_kabupaten' => $this->kota_kabupaten,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            'nominal_b' => null,
            'njop' => $this->njop ?: null,
            'est_harga_pasar' => $this->est_harga_pasar ?: null
        ];

        if ($this->isEdit) {
            // Check if user has direct permission to update
            if (auth()->user()->can('land-data.update-direct')) {
                $this->land->update($data);
                session()->flash('message', 'Land updated successfully.');
            } else {
                // Create approval request for update
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
            // Check if user has direct permission to create
            if (auth()->user()->can('land-data.create-direct')) {
                Land::create($data);
                session()->flash('message', 'Land created successfully.');
            } else {
                // Create approval request for new land
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
        
        // Check if land has related projects or soils
        if ($land->projects()->count() > 0 || $land->soils()->count() > 0) {
            session()->flash('error', 'Cannot delete Land. It has related projects or soil records.');
            $this->hideDeleteModal();
            return;
        }

        // Check if user has direct permission to delete
        if (auth()->user()->can('land-data.delete-direct')) {
            $land->delete();
            session()->flash('message', 'Land deleted successfully.');
        } else {
            // Create approval request for deletion
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
            'land', 'landId', 'lokasi_lahan', 'tahun_perolehan', 'nilai_perolehan',
            'alamat', 'link_google_maps', 'kota_kabupaten', 'status', 'keterangan',
            'nominal_b', 'njop', 'est_harga_pasar',
            'nilai_perolehan_display', 'nominal_b_display', 'njop_display', 'est_harga_pasar_display'
        ]);
        $this->resetValidation();
    }

    // Add reset filters method
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

    // Add updating methods for new filters
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

    // Add method to clear business unit filter
    public function clearBusinessUnitFilter()
    {
        $this->filterByBusinessUnit = null;
        $this->businessUnit = null;
        return redirect()->route('lands');
    }

    // Helper method to get the current business unit name for display
    public function getCurrentBusinessUnitName()
    {
        return $this->businessUnit ? $this->businessUnit->name : null;
    }

    // Helper method to check if we're currently filtering
    public function isFiltered()
    {
        return !is_null($this->filterByBusinessUnit);
    }

    // Method to go back to the business unit detail page
    public function backToBusinessUnit()
    {
        if ($this->businessUnit) {
            return redirect()->route('business-units', ['view' => 'show', 'id' => $this->businessUnit->id]);
        }
        return redirect()->route('business-units');
    }

    // Helper method to get status options
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