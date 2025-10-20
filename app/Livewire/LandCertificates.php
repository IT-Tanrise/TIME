<?php

namespace App\Livewire;

use App\Models\LandCertificate;
use App\Models\Land;
use App\Models\Soil;
use App\Models\BusinessUnit;
use Livewire\Component;
use Livewire\WithPagination;

class LandCertificates extends Component
{
    use WithPagination;

    public $certificate;
    public $certificateId;
    public $land_id = '';
    public $business_unit_id = '';
    public $certificate_type = '';
    public $certificate_number = '';
    public $issued_date = '';
    public $expired_date = '';
    public $issued_by = '';
    public $notes = '';
    public $status = 'active';
    public $selectedSoils = [];
    
    public $showForm = false;
    public $showDetailForm = false;
    public $isEdit = false;
    public $search = '';

    // Filter properties
    public $filterLand = '';
    public $filterCertificateType = '';
    public $filterStatus = '';
    public $filterByBusinessUnit = null;
    public $businessUnit = null;

    // Dropdown search properties
    public $landSearch = '';
    public $showLandDropdown = false;
    public $certificateTypeSearch = '';
    public $showCertificateTypeDropdown = false;
    
    // Business unit dropdown properties
    public $businessUnitSearch = '';
    public $showBusinessUnitDropdown = false;
    public $allowBusinessUnitChange = false;

    // Filter dropdown properties
    public $filterLandSearch = '';
    public $showLandFilterDropdown = false;

    // Delete modal
    public $showDeleteModal = false;
    public $deleteId = null;

    public $soilSearchAvailable = '';
    public $soilSearchAssigned = '';

    protected $rules = [
        'land_id' => 'required|exists:lands,id',
        'business_unit_id' => 'required|exists:business_units,id',
        'certificate_type' => 'required|string|max:255',
        'certificate_number' => 'required|string|max:255',
        'issued_date' => 'nullable|date',
        'expired_date' => 'nullable|date|after_or_equal:issued_date',
        'issued_by' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'status' => 'required|in:active,expired,revoked,pending',
        'selectedSoils' => 'nullable|array',
        'selectedSoils.*' => 'exists:soils,id'
    ];

    protected $messages = [
        'business_unit_id.required' => 'Please select a business unit.',
        'land_id.required' => 'Please select a land.',
        'certificate_type.required' => 'Certificate type is required.',
        'certificate_number.required' => 'Certificate number is required.',
        'expired_date.after_or_equal' => 'Expiration date must be after or equal to issued date.',
    ];

    public function mount($businessUnit = null, $land = null)
    {
        if ($businessUnit) {
            if (is_numeric($businessUnit)) {
                $this->businessUnit = BusinessUnit::find($businessUnit);
                if ($this->businessUnit) {
                    $this->filterByBusinessUnit = $this->businessUnit->id;
                    $this->business_unit_id = $this->businessUnit->id;
                    $this->businessUnitSearch = $this->businessUnit->name;
                    $this->allowBusinessUnitChange = false;
                }
            } elseif ($businessUnit instanceof BusinessUnit) {
                $this->filterByBusinessUnit = $businessUnit->id;
                $this->businessUnit = $businessUnit;
                $this->business_unit_id = $businessUnit->id;
                $this->businessUnitSearch = $businessUnit->name;
                $this->allowBusinessUnitChange = false;
            }
        } else {
            $this->allowBusinessUnitChange = true;
        }

        if ($land) {
            $this->filterLand = $land;
        }
    }

    public function render()
    {
        $certificates = LandCertificate::with(['land', 'land.businessUnit', 'soils'])
            ->when($this->search, function($query) {
                $query->where('certificate_number', 'like', '%' . $this->search . '%')
                    ->orWhere('certificate_type', 'like', '%' . $this->search . '%')
                    ->orWhereHas('land', function($q) {
                        $q->where('lokasi_lahan', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterByBusinessUnit, function($query) {
                $query->whereHas('land', function($q) {
                    $q->where('business_unit_id', $this->filterByBusinessUnit);
                });
            })
            ->when($this->filterLand, function($query) {
                $query->where('land_id', $this->filterLand);
            })
            ->when($this->filterCertificateType, function($query) {
                $query->where('certificate_type', $this->filterCertificateType);
            })
            ->when($this->filterStatus, function($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.land-certificates.index', compact('certificates'));
    }

    public function showCreateForm()
    {
        $this->resetForm();
        
        if ($this->filterByBusinessUnit && $this->businessUnit) {
            $this->business_unit_id = $this->filterByBusinessUnit;
            $this->businessUnitSearch = $this->businessUnit->name;
            $this->allowBusinessUnitChange = false;
        } else {
            $this->allowBusinessUnitChange = true;
        }
        
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        $this->certificate = LandCertificate::with('soils')->findOrFail($id);
        $this->certificateId = $this->certificate->id;
        $this->land_id = $this->certificate->land_id;
        $this->business_unit_id = $this->certificate->land->business_unit_id;
        $this->certificate_type = $this->certificate->certificate_type;
        $this->certificate_number = $this->certificate->certificate_number;
        $this->issued_date = $this->certificate->issued_date ? $this->certificate->issued_date->format('Y-m-d') : '';
        $this->expired_date = $this->certificate->expired_date ? $this->certificate->expired_date->format('Y-m-d') : '';
        $this->issued_by = $this->certificate->issued_by;
        $this->notes = $this->certificate->notes;
        $this->status = $this->certificate->status;
        $this->selectedSoils = $this->certificate->soils->pluck('id')->toArray();

        if ($this->certificate->land) {
            $this->landSearch = $this->certificate->land->lokasi_lahan;
            if ($this->certificate->land->businessUnit) {
                $this->businessUnitSearch = $this->certificate->land->businessUnit->name;
            }
        }

        $this->showForm = true;
        $this->isEdit = true;
    }

    public function showDetail($id)
    {
        $this->certificateId = $id;
        $this->showDetailForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'land_id' => $this->land_id,
            'certificate_type' => $this->certificate_type,
            'certificate_number' => $this->certificate_number,
            'issued_date' => $this->issued_date ?: null,
            'expired_date' => $this->expired_date ?: null,
            'issued_by' => $this->issued_by,
            'notes' => $this->notes,
            'status' => $this->status,
        ];

        if ($this->isEdit) {
            $this->certificate->update($data);
            
            // Sync soils
            $this->certificate->soils()->sync($this->selectedSoils);
            
            session()->flash('message', 'Land certificate updated successfully.');
        } else {
            $certificate = LandCertificate::create($data);
            
            // Attach soils
            if (!empty($this->selectedSoils)) {
                $certificate->soils()->attach($this->selectedSoils);
            }
            
            session()->flash('message', 'Land certificate created successfully.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $certificate = LandCertificate::findOrFail($this->deleteId);
        $certificate->soils()->detach();
        $certificate->delete();
        
        session()->flash('message', 'Land certificate deleted successfully.');
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
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
            'certificate', 'certificateId', 'land_id', 'certificate_type', 
            'certificate_number', 'issued_date', 'expired_date', 'issued_by', 
            'notes', 'status', 'selectedSoils',
            'landSearch', 'showLandDropdown', 
            'certificateTypeSearch', 'showCertificateTypeDropdown',
            'businessUnitSearch', 'showBusinessUnitDropdown',
            'soilSearchAvailable', 'soilSearchAssigned' // ADD THESE TWO
        ]);
        
        // Restore business unit if filtered
        if ($this->filterByBusinessUnit && $this->businessUnit) {
            $this->business_unit_id = $this->businessUnit->id;
            $this->businessUnitSearch = $this->businessUnit->name;
            $this->allowBusinessUnitChange = false;
        } else {
            $this->business_unit_id = '';
            $this->allowBusinessUnitChange = true;
        }
        
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->filterLand = '';
        $this->filterCertificateType = '';
        $this->filterStatus = '';
        $this->search = '';
        $this->filterLandSearch = '';
        $this->resetPage();
    }

    // Business Unit dropdown methods
    public function searchBusinessUnits()
    {
        if ($this->allowBusinessUnitChange || !$this->filterByBusinessUnit) {
            $this->showBusinessUnitDropdown = true;
            $this->showLandDropdown = false;
            $this->showCertificateTypeDropdown = false;
        }
    }

    public function selectBusinessUnit($businessUnitId, $businessUnitName)
    {
        $oldBusinessUnitId = $this->business_unit_id;
        
        $this->business_unit_id = $businessUnitId;
        $this->businessUnitSearch = $businessUnitName;
        $this->showBusinessUnitDropdown = false;

        // Reset land selection if business unit changed
        if ($oldBusinessUnitId != $businessUnitId) {
            $this->land_id = '';
            $this->landSearch = '';
            $this->selectedSoils = [];
            $this->showLandDropdown = false;
            
            if ($this->filterByBusinessUnit && $businessUnitId != $this->filterByBusinessUnit) {
                session()->flash('warning', 'You have changed the business unit from the filtered selection.');
            }
        }
    }

    public function getFilteredBusinessUnits()
    {
        $query = BusinessUnit::query();
        
        if ($this->filterByBusinessUnit && !$this->allowBusinessUnitChange) {
            $query->where('id', $this->filterByBusinessUnit);
        } else {
            if (!empty(trim($this->businessUnitSearch))) {
                $query->where('name', 'like', '%' . $this->businessUnitSearch . '%')
                      ->orWhere('code', 'like', '%' . $this->businessUnitSearch . '%');
            }
        }
        
        return $query->orderBy('name')->limit(20)->get();
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
        
        if ($this->filterByBusinessUnit && $this->businessUnit) {
            $this->business_unit_id = $this->businessUnit->id;
            $this->businessUnitSearch = $this->businessUnit->name;
        }
    }

    // Land dropdown methods
    public function searchLands()
    {
        if (!empty($this->business_unit_id)) {
            $this->showLandDropdown = true;
            $this->showBusinessUnitDropdown = false;
            $this->showCertificateTypeDropdown = false;
        }
    }

    public function selectLand($landId, $landName)
    {
        $this->land_id = $landId;
        $this->landSearch = $landName;
        $this->showLandDropdown = false;
        $this->selectedSoils = []; // Reset soil selection when land changes
    }

    public function getFilteredLands()
    {
        $query = Land::with('businessUnit');
        
        // Only show lands if business unit is selected
        if (!empty($this->business_unit_id)) {
            $query->where('business_unit_id', $this->business_unit_id);
        } else {
            return collect();
        }
        
        if (!empty(trim($this->landSearch))) {
            $query->where(function($q) {
                $q->where('lokasi_lahan', 'like', '%' . $this->landSearch . '%')
                  ->orWhere('kota_kabupaten', 'like', '%' . $this->landSearch . '%');
            });
        }
        
        return $query->orderBy('lokasi_lahan')->limit(20)->get();
    }

    // Certificate type dropdown methods
    public function searchCertificateTypes()
    {
        $this->showCertificateTypeDropdown = true;
        $this->showLandDropdown = false;
        $this->showBusinessUnitDropdown = false;
    }

    public function selectCertificateType($type, $label)
    {
        $this->certificate_type = $type;
        $this->certificateTypeSearch = $label;
        $this->showCertificateTypeDropdown = false;
    }

    public function getFilteredCertificateTypes()
    {
        $types = LandCertificate::getCertificateTypeOptions();
        
        if (!empty(trim($this->certificateTypeSearch))) {
            $search = strtolower($this->certificateTypeSearch);
            return collect($types)->filter(function($label, $key) use ($search) {
                return str_contains(strtolower($label), $search) || 
                       str_contains(strtolower($key), $search);
            })->toArray();
        }
        
        return $types;
    }

    // Get available soils for selected land
    public function getAvailableSoils()
    {
        if (!$this->land_id) {
            return collect();
        }

        // Get IDs of soils that are already assigned to other certificates
        $assignedSoilIds = \DB::table('certificate_soil')
            ->join('land_certificates', 'certificate_soil.land_certificate_id', '=', 'land_certificates.id')
            ->where('land_certificates.land_id', $this->land_id)
            ->when($this->isEdit && $this->certificateId, function($query) {
                $query->where('land_certificates.id', '!=', $this->certificateId);
            })
            ->whereNull('land_certificates.deleted_at')
            ->pluck('certificate_soil.soil_id')
            ->toArray();

        $query = Soil::where('land_id', $this->land_id)
                ->whereNotIn('id', $assignedSoilIds);
        
        // Apply search filter
        if (!empty(trim($this->soilSearchAvailable))) {
            $search = $this->soilSearchAvailable;
            $query->where(function($q) use ($search) {
                $q->where('letak_tanah', 'like', '%' . $search . '%')
                ->orWhere('nama_penjual', 'like', '%' . $search . '%')
                ->orWhere('atas_nama', 'like', '%' . $search . '%');
            });
        }
        
        return $query->orderBy('letak_tanah')->get();
    }

    public function getAssignedSoilsInfo()
    {
        if (!$this->land_id) {
            return collect();
        }

        $query = \DB::table('certificate_soil')
            ->join('land_certificates', 'certificate_soil.land_certificate_id', '=', 'land_certificates.id')
            ->join('soils', 'certificate_soil.soil_id', '=', 'soils.id')
            ->where('land_certificates.land_id', $this->land_id)
            ->when($this->isEdit && $this->certificateId, function($q) {
                $q->where('land_certificates.id', '!=', $this->certificateId);
            })
            ->whereNull('land_certificates.deleted_at');
        
        // Apply search filter
        if (!empty(trim($this->soilSearchAssigned))) {
            $search = $this->soilSearchAssigned;
            $query->where(function($q) use ($search) {
                $q->where('soils.letak_tanah', 'like', '%' . $search . '%')
                ->orWhere('soils.nama_penjual', 'like', '%' . $search . '%')
                ->orWhere('soils.atas_nama', 'like', '%' . $search . '%');
            });
        }
        
        return $query->select(
                'soils.id',
                'soils.letak_tanah',
                'soils.nama_penjual',
                'soils.luas',
                'soils.atas_nama',
                'land_certificates.certificate_type',
                'land_certificates.certificate_number',
                'land_certificates.id as certificate_id'
            )
            ->get()
            ->groupBy('id');
    }

    // Filter dropdown methods
    public function openLandFilterDropdown()
    {
        $this->showLandFilterDropdown = true;
    }

    public function selectLandFilter($landId, $landName)
    {
        $this->filterLand = $landId;
        $this->filterLandSearch = $landName;
        $this->showLandFilterDropdown = false;
        $this->resetPage();
    }

    public function clearLandFilter()
    {
        $this->filterLand = '';
        $this->filterLandSearch = '';
        $this->showLandFilterDropdown = false;
        $this->resetPage();
    }

    public function getFilteredLandsForFilter()
    {
        $query = Land::query();
        
        if ($this->filterByBusinessUnit) {
            $query->where('business_unit_id', $this->filterByBusinessUnit);
        }
        
        if (!empty(trim($this->filterLandSearch))) {
            $query->where(function($q) {
                $q->where('lokasi_lahan', 'like', '%' . $this->filterLandSearch . '%')
                  ->orWhere('kota_kabupaten', 'like', '%' . $this->filterLandSearch . '%');
            });
        }
        
        return $query->orderBy('lokasi_lahan')->limit(20)->get();
    }

    public function closeDropdowns()
    {
        $this->showLandDropdown = false;
        $this->showCertificateTypeDropdown = false;
        $this->showLandFilterDropdown = false;
        $this->showBusinessUnitDropdown = false;
    }

    public function clearBusinessUnitFilter()
    {
        $this->filterByBusinessUnit = null;
        $this->businessUnit = null;
        return redirect()->route('land-certificates');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLand()
    {
        $this->resetPage();
    }

    public function updatingFilterCertificateType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
}