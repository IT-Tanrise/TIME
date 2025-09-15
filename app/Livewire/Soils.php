<?php

namespace App\Livewire;

use App\Models\Soil;
use App\Models\Land;
use App\Models\BusinessUnit;
use App\Models\CategoryBiayaTambahanSoil;
use App\Models\DescriptionBiayaTambahanSoil;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BiayaTambahanSoil;
use Livewire\Attributes\On;

class Soils extends Component
{
    use WithPagination;

    public $soil;
    public $soilId;
    public $land_id = '';
    public $business_unit_id = '';
    
    // Multiple soil details array
    public $soilDetails = [];
    
    public $showForm = false;
    public $showDetailForm = false;
    public $showAdditionalCostsForm = false;
    public $isEdit = false;
    public $editMode = 'details'; // 'details' or 'costs'
    public $search = '';
    public $filterBusinessUnit = '';
    public $filterLand = '';

    // Add these properties for business unit filtering
    public $filterByBusinessUnit = null;
    public $businessUnit = null;

    // Properties for category and description search
    public $categorySearch = [];
    public $descriptionSearch = [];
    public $showCategoryDropdown = [];
    public $showDescriptionDropdown = [];

    // Properties for seller name and address search
    public $sellerNameSearch = [];
    public $sellerAddressSearch = [];
    public $showSellerNameDropdown = [];
    public $showSellerAddressDropdown = [];

    // Properties for land search
    public $landSearch = '';
    public $showLandDropdown = false;

    // Properties for business unit search
    public $businessUnitSearch = '';
    public $showBusinessUnitDropdown = false;

    // Add this property for allowing business unit changes
    public $allowBusinessUnitChange = false;

    // Properties for filter dropdown search
    public $filterBusinessUnitSearch = '';
    public $filterLandSearch = '';
    public $showBusinessUnitFilterDropdown = false;
    public $showLandFilterDropdown = false;

    // Export properties
    public $showExportModal = false;
    public $exportDateFrom = '';
    public $exportDateTo = '';
    public $exportType = 'current'; // 'current', 'all', 'date_range'

    protected $rules = [
        'land_id' => 'required|exists:lands,id',
        'business_unit_id' => 'required|exists:business_units,id',
        'soilDetails.*.nama_penjual' => 'required|string|max:255',
        'soilDetails.*.alamat_penjual' => 'required|string',
        'soilDetails.*.nomor_ppjb' => 'required|string|max:255',
        'soilDetails.*.tanggal_ppjb' => 'required|date',
        'soilDetails.*.letak_tanah' => 'required|string|max:255',
        'soilDetails.*.luas' => 'required|numeric|min:0',
        'soilDetails.*.harga' => 'required|numeric|min:0',
        'soilDetails.*.bukti_kepemilikan' => 'required|string|max:255',
        'soilDetails.*.bukti_kepemilikan_details' => 'nullable|string|max:255',
        'soilDetails.*.atas_nama' => 'required|string|max:255',
        'soilDetails.*.nop_pbb' => 'nullable|string|max:255',
        'soilDetails.*.nama_notaris_ppat' => 'nullable|string|max:255',
        'soilDetails.*.keterangan' => 'required|string',
    ];

    // Export validation rules
    protected function getExportRules()
    {
        return [
            'exportType' => 'required|in:current,all,date_range',
            'exportDateFrom' => 'required_if:exportType,date_range|nullable|date',
            'exportDateTo' => 'required_if:exportType,date_range|nullable|date|after_or_equal:exportDateFrom',
        ];
    }

    protected $messages = [
        'land_id.required' => 'Please select a land.',
        'business_unit_id.required' => 'Please select a business unit.',
        'soilDetails.*.nama_penjual.required' => 'Seller name is required for all soil details.',
        'soilDetails.*.alamat_penjual.required' => 'Seller address is required for all soil details.',
        'soilDetails.*.nomor_ppjb.required' => 'PPJB number is required for all soil details.',
        'soilDetails.*.tanggal_ppjb.required' => 'PPJB date is required for all soil details.',
        'soilDetails.*.letak_tanah.required' => 'Land location is required for all soil details.',
        'soilDetails.*.luas.required' => 'Area is required for all soil details.',
        'soilDetails.*.harga.required' => 'Price is required for all soil details.',
        'soilDetails.*.bukti_kepemilikan.required' => 'Ownership proof is required for all soil details.',
        'soilDetails.*.atas_nama.required' => 'Owner name is required for all soil details.',
        'soilDetails.*.nop_pbb.max' => 'NOP PBB must not exceed 255 characters.',
        'soilDetails.*.nama_notaris_ppat.max' => 'Notaris/PPAT name must not exceed 255 characters.',
        'soilDetails.*.keterangan.required' => 'Notes are required for all soil details.',
    ];

    // Show export modal
    public function showExportModalView()
    {
        $this->showExportModal = true;
        $this->exportType = 'current';
        $this->exportDateFrom = '';
        $this->exportDateTo = '';
    }

    // Hide export modal
    public function hideExportModalView()
    {
        $this->showExportModal = false;
        $this->resetValidation(['exportType', 'exportDateFrom', 'exportDateTo']);
    }

    // Export to Excel
    public function exportToExcel()
    {
        $this->validate($this->getExportRules());
        
        $params = [
            'export_type' => $this->exportType,
            'date_from' => $this->exportDateFrom,
            'date_to' => $this->exportDateTo,
            'search' => $this->search,
            'business_unit_id' => $this->filterByBusinessUnit ?? $this->filterBusinessUnit,
            'land_id' => $this->filterLand,
        ];
        
        $this->hideExportModalView();
        session()->flash('message', 'Export completed successfully!');
        
        return redirect()->route('soils.export', $params);
    }

    // Get export summary
    public function getExportSummary()
    {
        $query = Soil::query();

        switch ($this->exportType) {
            case 'current':
                $query->when($this->search, function($q) {
                    $q->where('nama_penjual', 'like', '%' . $this->search . '%')
                      ->orWhere('letak_tanah', 'like', '%' . $this->search . '%')
                      ->orWhere('nomor_ppjb', 'like', '%' . $this->search . '%');
                })
                ->when($this->filterBusinessUnit, function($q) {
                    $q->where('business_unit_id', $this->filterBusinessUnit);
                })
                ->when($this->filterByBusinessUnit, function($q) {
                    $q->where('business_unit_id', $this->filterByBusinessUnit);
                })
                ->when($this->filterLand, function($q) {
                    $q->where('land_id', $this->filterLand);
                });
                break;

            case 'date_range':
                if ($this->exportDateFrom && $this->exportDateTo) {
                    $query->whereBetween('created_at', [$this->exportDateFrom, $this->exportDateTo]);
                }
                $query->when($this->filterByBusinessUnit, function($q) {
                    $q->where('business_unit_id', $this->filterByBusinessUnit);
                });
                break;

            case 'all':
            default:
                $query->when($this->filterByBusinessUnit, function($q) {
                    $q->where('business_unit_id', $this->filterByBusinessUnit);
                });
                break;
        }

        return $query->count();
    }

    // Add mount method to handle business unit parameter
    public function mount($businessUnit = null, $soilId = null)
    {
        // If a soilId is provided, show the detail immediately
        if ($soilId) {
            $this->showDetail($soilId);
        }
        
        // Initialize with one empty soil detail
        $this->initializeSoilDetails();

        // FIXED: Handle business unit parameter properly - support both ID and object
        if ($businessUnit) {
            if (is_numeric($businessUnit)) {
                $this->businessUnit = BusinessUnit::find($businessUnit);
                if ($this->businessUnit) {
                    $this->filterByBusinessUnit = $this->businessUnit->id;
                    $this->business_unit_id = $this->businessUnit->id;
                    $this->businessUnitSearch = $this->businessUnit->name;
                    $this->allowBusinessUnitChange = false; // Default to locked when filtered
                }
            } elseif ($businessUnit instanceof BusinessUnit) {
                $this->filterByBusinessUnit = $businessUnit->id;
                $this->businessUnit = $businessUnit;
                $this->business_unit_id = $businessUnit->id;
                $this->businessUnitSearch = $businessUnit->name;
                $this->allowBusinessUnitChange = false;
            } elseif (is_string($businessUnit)) {
                // Handle string IDs from route parameters
                $this->businessUnit = BusinessUnit::find((int)$businessUnit);
                if ($this->businessUnit) {
                    $this->filterByBusinessUnit = $this->businessUnit->id;
                    $this->business_unit_id = $this->businessUnit->id;
                    $this->businessUnitSearch = $this->businessUnit->name;
                    $this->allowBusinessUnitChange = false;
                }
            }
        } else {
            // No business unit parameter, allow normal dropdown behavior
            $this->allowBusinessUnitChange = true;
        }
    }

    // FIXED: Add helper method to initialize soil details with proper formatting
    private function initializeSoilDetails()
    {
        $this->soilDetails = [
            $this->createEmptySoilDetail()
        ];

        // Initialize corresponding search arrays
        $this->sellerNameSearch = [''];
        $this->sellerAddressSearch = [''];
        $this->showSellerNameDropdown = [false];
        $this->showSellerAddressDropdown = [false];

        // Initialize land search
        $this->landSearch = '';
        $this->showLandDropdown = false;

        // Initialize business unit search
        $this->businessUnitSearch = '';
        $this->showBusinessUnitDropdown = false;
    }

    // FIXED: Add helper method to create empty soil detail with proper structure
    private function createEmptySoilDetail()
    {
        return [
            'nama_penjual' => '',
            'alamat_penjual' => '',
            'nomor_ppjb' => '',
            'tanggal_ppjb' => '',
            'letak_tanah' => '',
            'luas' => '',
            'luas_display' => '',
            'harga' => '',
            'harga_display' => '',
            'bukti_kepemilikan' => '',
            'bukti_kepemilikan_details' => '',
            'atas_nama' => '',
            'nop_pbb' => '',
            'nama_notaris_ppat' => '',
            'keterangan' => ''
        ];
    }

    // FIXED: Helper method to format numbers consistently
    private function formatNumber($value)
    {
        if (empty($value) || $value === '' || $value === 0) {
            return '';
        }
        return number_format((int) $value, 0, ',', '.');
    }

    // FIXED: Helper method to parse formatted numbers back to integers
    private function parseFormattedNumber($value)
    {
        if (empty($value) || $value === '') {
            return '';
        }
        return (int) preg_replace('/[^\d]/', '', $value);
    }

    public function render()
    {
        $soils = Soil::with(['land', 'businessUnit', 'createdBy', 'updatedBy'])
            ->when($this->search, function($query) {
                $query->where('nama_penjual', 'like', '%' . $this->search . '%')
                      ->orWhere('letak_tanah', 'like', '%' . $this->search . '%')
                      ->orWhere('nomor_ppjb', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterBusinessUnit, function($query) {
                $query->where('business_unit_id', $this->filterBusinessUnit);
            })
            ->when($this->filterByBusinessUnit, function($query) {
                $query->where('business_unit_id', $this->filterByBusinessUnit);
            })
            ->when($this->filterLand, function($query) {
                $query->where('land_id', $this->filterLand);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // FIXED: Add missing variables for the index view
        $businessUnits = BusinessUnit::orderBy('name')->get();
        $lands = Land::orderBy('lokasi_lahan')->get();

        return view('livewire.soils.index', compact('soils', 'businessUnits', 'lands'));
    }

    // FIXED: Add method to handle direct business unit ID from route
    public function setBusinessUnitFilter($businessUnitId)
    {
        $businessUnit = BusinessUnit::find($businessUnitId);
        if ($businessUnit) {
            $this->filterByBusinessUnit = $businessUnit->id;
            $this->businessUnit = $businessUnit;
            $this->business_unit_id = $businessUnit->id;
            $this->businessUnitSearch = $businessUnit->name;
        }
    }

    // Update the showCreateForm method
    public function showCreateForm()
    {
        $this->resetForm();
        
        // Set business unit behavior for create form
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

    public function showEditForm($id, $mode = 'details')
    {
        $this->soil = Soil::findOrFail($id);
        $this->soilId = $this->soil->id;
        $this->editMode = $mode;
        
        if ($mode === 'details') {
            // Load soil details for editing
            $this->land_id = $this->soil->land_id;
            $this->business_unit_id = $this->soil->business_unit_id;

            // Set land and business unit search values for edit mode
            if ($this->soil->land) {
                $this->landSearch = $this->soil->land->lokasi_lahan;
            }
            if ($this->soil->businessUnit) {
                $this->businessUnitSearch = $this->soil->businessUnit->name;
            }

            $this->soilDetails = [
                [
                    'nama_penjual' => $this->soil->nama_penjual,
                    'alamat_penjual' => $this->soil->alamat_penjual,
                    'nomor_ppjb' => $this->soil->nomor_ppjb,
                    'tanggal_ppjb' => $this->soil->tanggal_ppjb ? $this->soil->tanggal_ppjb->format('Y-m-d') : '',
                    'letak_tanah' => $this->soil->letak_tanah,
                    'luas' => $this->soil->luas,
                    'luas_display' => $this->formatNumber($this->soil->luas),
                    'harga' => $this->soil->harga,
                    'harga_display' => $this->formatNumber($this->soil->harga),
                    'bukti_kepemilikan' => $this->soil->bukti_kepemilikan,
                    'bukti_kepemilikan_details' => $this->soil->bukti_kepemilikan_details,
                    'atas_nama' => $this->soil->atas_nama,
                    'nop_pbb' => $this->soil->nop_pbb,
                    'nama_notaris_ppat' => $this->soil->nama_notaris_ppat,
                    'keterangan' => $this->soil->keterangan,
                ]
            ];

            // Initialize seller search for edit mode
            $this->sellerNameSearch = [$this->soil->nama_penjual];
            $this->sellerAddressSearch = [$this->soil->alamat_penjual];
            $this->showSellerNameDropdown = [false];
            $this->showSellerAddressDropdown = [false];
            
            $this->showForm = true;
        } elseif ($mode === 'costs') {
            // Load additional costs for editing
            $this->biayaTambahan = [];
            $this->categorySearch = [];
            $this->descriptionSearch = [];
            
            foreach ($this->soil->biayaTambahanSoils as $index => $biaya) {
                $this->biayaTambahan[] = [
                    'id' => $biaya->id,
                    'description_id' => $biaya->description_id,
                    'harga' => $biaya->harga,
                    'harga_display' => $this->formatNumber($biaya->harga),
                    'cost_type' => $biaya->cost_type,
                    'date_cost' => $biaya->date_cost ? $biaya->date_cost->format('Y-m-d') : '',
                ];
                
                // Get category through description relationship
                $category = $biaya->description->category ?? null;
                $this->categorySearch[$index] = $category ? $category->category : '';
                $this->descriptionSearch[$index] = $biaya->description->description ?? '';
            }
            
            $this->showAdditionalCostsForm = true;
        }
        
        $this->isEdit = true;
    }

    public function showDetail($id)
    {
        $this->soilId = $id;
        $this->showDetailForm = true;
    }

    public $biayaTambahan = []; // For managing additional costs separately

    public function save()
    {
        $this->validate();

        if ($this->isEdit && $this->editMode === 'details') {
            // Update existing record details only
            $soil = Soil::findOrFail($this->soilId);
            $detail = $this->soilDetails[0];
            
            $soil->update([
                'land_id' => $this->land_id,
                'business_unit_id' => $this->business_unit_id,
                'nama_penjual' => $detail['nama_penjual'],
                'alamat_penjual' => $detail['alamat_penjual'],
                'nomor_ppjb' => $detail['nomor_ppjb'],
                'tanggal_ppjb' => $detail['tanggal_ppjb'],
                'letak_tanah' => $detail['letak_tanah'],
                'luas' => $this->parseFormattedNumber($detail['luas']),
                'harga' => $this->parseFormattedNumber($detail['harga']),
                'bukti_kepemilikan' => $detail['bukti_kepemilikan'],
                'bukti_kepemilikan_details' => $detail['bukti_kepemilikan_details'],
                'atas_nama' => $detail['atas_nama'],
                'nop_pbb' => $detail['nop_pbb'],
                'nama_notaris_ppat' => $detail['nama_notaris_ppat'],
                'keterangan' => $detail['keterangan'],
            ]);
            
            session()->flash('message', 'Soil record details updated successfully.');
        } else {
            // Create multiple records from soil details (no additional costs)
            foreach ($this->soilDetails as $detail) {
                Soil::create([
                    'land_id' => $this->land_id,
                    'business_unit_id' => $this->business_unit_id,
                    'nama_penjual' => $detail['nama_penjual'],
                    'alamat_penjual' => $detail['alamat_penjual'],
                    'nomor_ppjb' => $detail['nomor_ppjb'],
                    'tanggal_ppjb' => $detail['tanggal_ppjb'],
                    'letak_tanah' => $detail['letak_tanah'],
                    'luas' => $this->parseFormattedNumber($detail['luas']),
                    'harga' => $this->parseFormattedNumber($detail['harga']),
                    'bukti_kepemilikan' => $detail['bukti_kepemilikan'],
                    'bukti_kepemilikan_details' => $detail['bukti_kepemilikan_details'],
                    'atas_nama' => $detail['atas_nama'],
                    'nop_pbb' => $detail['nop_pbb'],
                    'nama_notaris_ppat' => $detail['nama_notaris_ppat'],
                    'keterangan' => $detail['keterangan'],
                ]);
            }
            
            session()->flash('message', count($this->soilDetails) . ' soil records created successfully.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function saveAdditionalCosts()
    {
        $this->validate([
            'biayaTambahan.*.description_id' => 'required|exists:description_biaya_tambahan_soils,id',
            'biayaTambahan.*.harga' => 'required|numeric|min:0',
            'biayaTambahan.*.cost_type' => 'required|in:standard,non_standard',
            'biayaTambahan.*.date_cost' => 'required|date',
        ]);

        $soil = Soil::findOrFail($this->soilId);
        $this->updateBiayaTambahan($soil, $this->biayaTambahan);
        
        session()->flash('message', 'Additional costs updated successfully.');
        $this->showAdditionalCostsForm = false;
        $this->resetForm();
    }

    private function updateBiayaTambahan($soil, $biayaTambahan)
    {
        if (empty($biayaTambahan) || !is_array($biayaTambahan)) {
            // Delete all existing if no biayaTambahan
            $soil->biayaTambahanSoils()->delete();
            return;
        }

        // Get existing IDs
        $existingIds = collect($biayaTambahan)
            ->filter(function($item) { return isset($item['id']); })
            ->pluck('id')
            ->toArray();

        // Delete removed items
        $soil->biayaTambahanSoils()
            ->whereNotIn('id', $existingIds)
            ->delete();

        // Update or create items
        foreach ($biayaTambahan as $biaya) {
            if (!empty($biaya['description_id']) && !empty($biaya['harga'])) {
                // Convert harga from formatted string to integer
                $harga = $this->parseFormattedNumber($biaya['harga']);

                if (isset($biaya['id'])) {
                    // Update existing
                    BiayaTambahanSoil::where('id', $biaya['id'])->update([
                        'description_id' => $biaya['description_id'],
                        'harga' => $harga,
                        'cost_type' => $biaya['cost_type'],
                        'date_cost' => $biaya['date_cost'],
                    ]);
                } else {
                    // Create new
                    BiayaTambahanSoil::create([
                        'soil_id' => $soil->id,
                        'description_id' => $biaya['description_id'],
                        'harga' => $harga,
                        'cost_type' => $biaya['cost_type'],
                        'date_cost' => $biaya['date_cost'],
                    ]);
                }
            }
        }
    }

    public function delete($id)
    {
        $soil = Soil::findOrFail($id);
        
        // Delete related additional costs first
        $soil->biayaTambahanSoils()->delete();
        
        // Delete the soil record
        $soil->delete();
        session()->flash('message', 'Soil record deleted successfully.');
    }

    public function backToIndex()
    {
        $this->showForm = false;
        $this->showDetailForm = false;
        $this->showAdditionalCostsForm = false;
        $this->resetForm();
    }

    // FIXED: Update the resetForm method
    public function resetForm()
    {
        $this->reset([
            'soil', 'soilId', 'land_id', 'editMode', 'biayaTambahan',
            'categorySearch', 'descriptionSearch', 'showCategoryDropdown', 'showDescriptionDropdown',
            'sellerNameSearch', 'sellerAddressSearch', 'showSellerNameDropdown', 'showSellerAddressDropdown',
            'landSearch', 'showLandDropdown', 'businessUnitSearch', 'showBusinessUnitDropdown'
        ]);
        
        // Reset soil details with proper initialization
        $this->initializeSoilDetails();

        // Handle business unit reset based on filter status
        if ($this->filterByBusinessUnit && $this->businessUnit) {
            // Keep the filtered business unit
            $this->business_unit_id = $this->businessUnit->id;
            $this->businessUnitSearch = $this->businessUnit->name;
            $this->allowBusinessUnitChange = false;
        } else {
            // Reset everything for normal mode
            $this->business_unit_id = '';
            $this->businessUnitSearch = '';
            $this->allowBusinessUnitChange = true;
        }
        
        $this->resetValidation();
    }

    // Update the existing resetFilters method to clear search values
    public function resetFilters()
    {
        $this->filterBusinessUnit = '';
        $this->filterLand = '';
        $this->search = '';
        
        // Clear dropdown search values
        $this->filterBusinessUnitSearch = '';
        $this->filterLandSearch = '';
        $this->showBusinessUnitFilterDropdown = false;
        $this->showLandFilterDropdown = false;
        
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

    public function updatingFilterLand()
    {
        $this->resetPage();
    }

    // Add method to clear business unit filter
    public function clearBusinessUnitFilter()
    {
        $this->filterByBusinessUnit = null;
        $this->businessUnit = null;
        $this->business_unit_id = '';
        return redirect()->route('soils');
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

    // Helper method to get bukti kepemilikan options
    public function getBuktiKepemilikanOptions()
    {
        return [
            'SHM' => 'Sertifikat Hak Milik (SHM)',
            'SHGB' => 'Sertifikat Hak Guna Bangunan (SHGB)',
            'SHGU' => 'Sertifikat Hak Guna Usaha (SHGU)',
            'SHP' => 'Sertifikat Hak Pakai (SHP)',
            'Girik/Letter C' => 'Girik/Letter C',
            'AJB' => 'Akta Jual Beli (AJB)',
            'Petok D' => 'Petok D',
            'Lainnya' => 'Lainnya'
        ];
    }

    // FIXED: Methods for managing soil details with proper number formatting
    public function addSoilDetail()
    {
        $index = count($this->soilDetails);
        
        // Add new soil detail with proper structure
        $this->soilDetails[] = $this->createEmptySoilDetail();

        // Add corresponding search arrays
        $this->sellerNameSearch[$index] = '';
        $this->sellerAddressSearch[$index] = '';
        $this->showSellerNameDropdown[$index] = false;
        $this->showSellerAddressDropdown[$index] = false;
    }

    public function removeSoilDetail($index)
    {
        if (count($this->soilDetails) > 1) {
            unset($this->soilDetails[$index]);
            unset($this->sellerNameSearch[$index]);
            unset($this->sellerAddressSearch[$index]);
            unset($this->showSellerNameDropdown[$index]);
            unset($this->showSellerAddressDropdown[$index]);
            
            $this->soilDetails = array_values($this->soilDetails);
            $this->sellerNameSearch = array_values($this->sellerNameSearch);
            $this->sellerAddressSearch = array_values($this->sellerAddressSearch);
            $this->showSellerNameDropdown = array_values($this->showSellerNameDropdown);
            $this->showSellerAddressDropdown = array_values($this->showSellerAddressDropdown);
        }
    }

    // Seller name search methods
    public function updatedSellerNameSearch($value, $key)
    {
        // Update the actual soil detail
        if (isset($this->soilDetails[$key])) {
            $this->soilDetails[$key]['nama_penjual'] = $value;
        }
    }

    public function searchSellerNames($index)
    {
        // Initialize arrays if not set
        if (!is_array($this->showSellerNameDropdown)) {
            $this->showSellerNameDropdown = [];
        }
        if (!is_array($this->showSellerAddressDropdown)) {
            $this->showSellerAddressDropdown = [];
        }
        
        // Only show the current dropdown, hide others
        for ($i = 0; $i < count($this->soilDetails); $i++) {
            $this->showSellerNameDropdown[$i] = ($i === $index);
            $this->showSellerAddressDropdown[$i] = false;
        }
        
        // Ensure the dropdown stays open
        $this->showSellerNameDropdown[$index] = true;
    }

    public function selectSellerName($index, $sellerName)
    {
        $this->sellerNameSearch[$index] = $sellerName;
        $this->soilDetails[$index]['nama_penjual'] = $sellerName;
        $this->showSellerNameDropdown[$index] = false;
    }

    public function getFilteredSellerNames($index)
    {
        $search = $this->sellerNameSearch[$index] ?? '';
        
        $query = Soil::select('nama_penjual')
            ->distinct()
            ->whereNotNull('nama_penjual')
            ->where('nama_penjual', '!=', '');
        
        // Only filter if there's search text, otherwise show all
        if (!empty(trim($search))) {
            $query->where('nama_penjual', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('nama_penjual')->limit(20)->get();
    }

    // Seller address search methods
    public function updatedSellerAddressSearch($value, $key)
    {
        // Update the actual soil detail
        if (isset($this->soilDetails[$key])) {
            $this->soilDetails[$key]['alamat_penjual'] = $value;
        }
    }

    public function searchSellerAddresses($index)
    {
        // Initialize arrays if not set
        if (!is_array($this->showSellerNameDropdown)) {
            $this->showSellerNameDropdown = [];
        }
        if (!is_array($this->showSellerAddressDropdown)) {
            $this->showSellerAddressDropdown = [];
        }
        
        // Only show the current dropdown, hide others
        for ($i = 0; $i < count($this->soilDetails); $i++) {
            $this->showSellerNameDropdown[$i] = false;
            $this->showSellerAddressDropdown[$i] = ($i === $index);
        }
        
        // Ensure the dropdown stays open
        $this->showSellerAddressDropdown[$index] = true;
    }

    public function selectSellerAddress($index, $sellerAddress)
    {
        $this->sellerAddressSearch[$index] = $sellerAddress;
        $this->soilDetails[$index]['alamat_penjual'] = $sellerAddress;
        $this->showSellerAddressDropdown[$index] = false;
    }

    public function getFilteredSellerAddresses($index)
    {
        $search = $this->sellerAddressSearch[$index] ?? '';
        
        $query = Soil::select('alamat_penjual')
            ->distinct()
            ->whereNotNull('alamat_penjual')
            ->where('alamat_penjual', '!=', '');
        
        // Only filter if there's search text, otherwise show all
        if (!empty(trim($search))) {
            $query->where('alamat_penjual', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('alamat_penjual')->limit(20)->get();
    }

    // Additional costs methods (for separate management)
    public function addBiayaTambahan()
    {
        $index = count($this->biayaTambahan);
        $this->biayaTambahan[] = [
            'description_id' => '',
            'harga' => '',
            'harga_display' => '',
            'cost_type' => 'standard',
            'date_cost' => '',
        ];
        $this->categorySearch[$index] = '';
        $this->descriptionSearch[$index] = '';
        $this->showCategoryDropdown[$index] = false;
        $this->showDescriptionDropdown[$index] = false;
    }

    public function removeBiayaTambahan($index)
    {
        unset($this->biayaTambahan[$index]);
        unset($this->categorySearch[$index]);
        unset($this->descriptionSearch[$index]);
        unset($this->showCategoryDropdown[$index]);
        unset($this->showDescriptionDropdown[$index]);
        
        $this->biayaTambahan = array_values($this->biayaTambahan);
        $this->categorySearch = array_values($this->categorySearch);
        $this->descriptionSearch = array_values($this->descriptionSearch);
        $this->showCategoryDropdown = array_values($this->showCategoryDropdown);
        $this->showDescriptionDropdown = array_values($this->showDescriptionDropdown);
    }

    public function getTotalBiayaTambahan()
    {
        if (empty($this->biayaTambahan) || !is_array($this->biayaTambahan)) {
            return 0;
        }
        
        return collect($this->biayaTambahan)->sum(function($item) {
            if (isset($item['harga'])) {
                return $this->parseFormattedNumber($item['harga']);
            }
            return 0;
        });
    }

    public function getCostTypeOptions()
    {
        return BiayaTambahanSoil::getCostTypeOptions();
    }

    // FIXED: Category search methods - improved to prevent auto-closing
    public function updatedCategorySearch($value, $key)
    {
        // Don't close dropdowns during typing - just update the search
        if (!isset($this->showCategoryDropdown[$key])) {
            $this->showCategoryDropdown[$key] = false;
        }
        if (!isset($this->showDescriptionDropdown[$key])) {
            $this->showDescriptionDropdown[$key] = false;
        }
        
        // Reset description when category search changes
        $this->descriptionSearch[$key] = '';
        if (isset($this->biayaTambahan[$key])) {
            $this->biayaTambahan[$key]['description_id'] = '';
        }
    }

    public function searchCategories($index)
    {
        // Initialize arrays if not set
        if (!is_array($this->showCategoryDropdown)) {
            $this->showCategoryDropdown = [];
        }
        if (!is_array($this->showDescriptionDropdown)) {
            $this->showDescriptionDropdown = [];
        }
        
        // Only show the current dropdown, hide others
        for ($i = 0; $i < count($this->biayaTambahan); $i++) {
            $this->showCategoryDropdown[$i] = ($i === $index);
            $this->showDescriptionDropdown[$i] = false;
        }
        
        // Ensure the dropdown stays open
        $this->showCategoryDropdown[$index] = true;
    }

    public function selectCategory($index, $categoryId, $categoryName)
    {
        // Set the selected category
        $this->categorySearch[$index] = $categoryName;
        $this->showCategoryDropdown[$index] = false;
        
        // Reset description when category changes
        $this->descriptionSearch[$index] = '';
        $this->showDescriptionDropdown[$index] = false;
        
        // Clear the description_id since category changed
        if (isset($this->biayaTambahan[$index])) {
            $this->biayaTambahan[$index]['description_id'] = '';
        }
    }

    public function getFilteredCategories($index)
    {
        $search = $this->categorySearch[$index] ?? '';
        
        $query = CategoryBiayaTambahanSoil::query();
        
        // Only filter if there's search text, otherwise show all
        if (!empty(trim($search))) {
            $query->where('category', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('category')->limit(20)->get();
    }

    // FIXED: Description search methods - improved to prevent auto-closing
    public function updatedDescriptionSearch($value, $key)
    {
        // Don't auto-close during typing - just update search
        $categorySearch = $this->categorySearch[$key] ?? '';
        if (!empty($categorySearch)) {
            if (!isset($this->showDescriptionDropdown[$key])) {
                $this->showDescriptionDropdown[$key] = false;
            }
        }
    }

    public function searchDescriptions($index)
    {
        // Only show if category is selected
        $categorySearch = $this->categorySearch[$index] ?? '';
        if (empty($categorySearch)) {
            return;
        }
        
        // Initialize arrays if not set
        if (!is_array($this->showCategoryDropdown)) {
            $this->showCategoryDropdown = [];
        }
        if (!is_array($this->showDescriptionDropdown)) {
            $this->showDescriptionDropdown = [];
        }
        
        // Only show the current dropdown, hide others
        for ($i = 0; $i < count($this->biayaTambahan); $i++) {
            $this->showCategoryDropdown[$i] = false;
            $this->showDescriptionDropdown[$i] = ($i === $index);
        }
        
        // Ensure the dropdown stays open
        $this->showDescriptionDropdown[$index] = true;
    }

    public function selectDescription($index, $descriptionId, $descriptionName)
    {
        $this->biayaTambahan[$index]['description_id'] = $descriptionId;
        $this->descriptionSearch[$index] = $descriptionName;
        $this->showDescriptionDropdown[$index] = false;
    }

    public function getFilteredDescriptions($index)
    {
        $search = $this->descriptionSearch[$index] ?? '';
        $categorySearch = $this->categorySearch[$index] ?? '';
        
        $query = DescriptionBiayaTambahanSoil::with('category');
        
        // Filter by category first (this is always required)
        if (!empty($categorySearch)) {
            $query->whereHas('category', function($q) use ($categorySearch) {
                $q->where('category', 'like', '%' . $categorySearch . '%');
            });
        }
        
        // Only filter by description search if there's search text
        if (!empty(trim($search))) {
            $query->where('description', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('description')->limit(20)->get();
    }

    // FIXED: Updated number formatting methods for biaya tambahan
    public function updatedBiayaTambahanHarga($value, $propertyName)
    {
        // Extract index from property name (e.g., "0.harga" -> 0)
        $parts = explode('.', $propertyName);
        $index = $parts[0];
        
        // Parse and format the number
        $numericValue = $this->parseFormattedNumber($value);
        
        if ($numericValue) {
            $this->biayaTambahan[$index]['harga'] = $numericValue;
            $this->biayaTambahan[$index]['harga_display'] = $this->formatNumber($numericValue);
        } else {
            $this->biayaTambahan[$index]['harga'] = '';
            $this->biayaTambahan[$index]['harga_display'] = '';
        }
    }

    // FIXED: Updated number formatting methods for soil details
    public function updatedSoilDetailsLuas($value, $propertyName)
    {
        // Extract index from property name (e.g., "0.luas_display" -> 0)
        $parts = explode('.', $propertyName);
        $index = $parts[0];
        
        // Parse and format the number
        $numericValue = $this->parseFormattedNumber($value);
        
        if ($numericValue) {
            $this->soilDetails[$index]['luas'] = $numericValue;
            $this->soilDetails[$index]['luas_display'] = $this->formatNumber($numericValue);
        } else {
            $this->soilDetails[$index]['luas'] = '';
            $this->soilDetails[$index]['luas_display'] = '';
        }
    }

    public function updatedSoilDetailsHarga($value, $propertyName)
    {
        // Extract index from property name (e.g., "0.harga_display" -> 0)
        $parts = explode('.', $propertyName);
        $index = $parts[0];
        
        // Parse and format the number
        $numericValue = $this->parseFormattedNumber($value);
        
        if ($numericValue) {
            $this->soilDetails[$index]['harga'] = $numericValue;
            $this->soilDetails[$index]['harga_display'] = $this->formatNumber($numericValue);
        } else {
            $this->soilDetails[$index]['harga'] = '';
            $this->soilDetails[$index]['harga_display'] = '';
        }
    }

    // FIXED: Close dropdowns when clicking outside - use event listener
    #[On('closeDropdowns')]
    public function closeDropdowns()
    {
        // Close additional costs dropdowns
        if (is_array($this->biayaTambahan) && count($this->biayaTambahan) > 0) {
            $this->showCategoryDropdown = array_fill(0, count($this->biayaTambahan), false);
            $this->showDescriptionDropdown = array_fill(0, count($this->biayaTambahan), false);
        } else {
            $this->showCategoryDropdown = [];
            $this->showDescriptionDropdown = [];
        }

        // Close seller search dropdowns
        if (is_array($this->soilDetails) && count($this->soilDetails) > 0) {
            $this->showSellerNameDropdown = array_fill(0, count($this->soilDetails), false);
            $this->showSellerAddressDropdown = array_fill(0, count($this->soilDetails), false);
        } else {
            $this->showSellerNameDropdown = [];
            $this->showSellerAddressDropdown = [];
        }

        // Close land search dropdown
        $this->showLandDropdown = false;

        // Close business unit search dropdown
        $this->showBusinessUnitDropdown = false;

        // Close filter dropdowns
        $this->showBusinessUnitFilterDropdown = false;
        $this->showLandFilterDropdown = false;
    }

    // Land search methods
    public function updatedLandSearch($value)
    {
        // Don't auto-select if typing
        if (!$this->showLandDropdown) {
            $this->land_id = '';
        }
    }

    public function searchLands()
    {
        $this->showLandDropdown = true;
        $this->showBusinessUnitDropdown = false;
    }

    public function selectLand($landId, $landName)
    {
        $this->land_id = $landId;
        $this->landSearch = $landName;
        $this->showLandDropdown = false;
    }

    public function getFilteredLands()
    {
        $search = $this->landSearch ?? '';
        
        $query = Land::query();
        
        // Only filter if there's search text, otherwise show all
        if (!empty(trim($search))) {
            $query->where('lokasi_lahan', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('lokasi_lahan')->limit(20)->get();
    }

    // FIXED: Business Unit search methods - add missing updater method
    public function updatedBusinessUnitSearch($value)
    {
        // Don't auto-select if typing (unless we're in filter mode)
        if (!$this->showBusinessUnitDropdown && !$this->filterByBusinessUnit) {
            $this->business_unit_id = '';
        }
    }

    public function searchBusinessUnits()
    {
        // Allow search if business unit change is allowed OR not filtering by business unit
        if ($this->allowBusinessUnitChange || !$this->filterByBusinessUnit) {
            $this->showBusinessUnitDropdown = true;
            $this->showLandDropdown = false;
        }
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
        
        // If we have a filtered business unit, revert to it
        if ($this->filterByBusinessUnit && $this->businessUnit) {
            $this->business_unit_id = $this->businessUnit->id;
            $this->businessUnitSearch = $this->businessUnit->name;
        }
    }

    public function selectBusinessUnit($businessUnitId, $businessUnitName)
    {
        $this->business_unit_id = $businessUnitId;
        $this->businessUnitSearch = $businessUnitName;
        $this->showBusinessUnitDropdown = false;

        // If we're in filter mode and user changed business unit, 
        // we might want to update the filter or warn them
        if ($this->filterByBusinessUnit && $businessUnitId != $this->filterByBusinessUnit) {
            // Optionally, you can add a warning or confirmation here
            session()->flash('warning', 'You have changed the business unit from the filtered selection.');
        }
    }

    public function getFilteredBusinessUnits()
    {
        $search = $this->businessUnitSearch ?? '';
        
        $query = BusinessUnit::query();
        
        // If we're filtering by business unit and change is not allowed,
        // only show the filtered business unit
        if ($this->filterByBusinessUnit && !$this->allowBusinessUnitChange) {
            $query->where('id', $this->filterByBusinessUnit);
        } else {
            // Normal search behavior
            if (!empty(trim($search))) {
                $query->where('name', 'like', '%' . $search . '%');
            }
        }
        
        return $query->orderBy('name')->limit(20)->get();
    }

    // Get grand total of all soil details
    public function getGrandTotal()
    {
        $total = 0;
        foreach ($this->soilDetails as $detail) {
            $landPrice = $this->parseFormattedNumber($detail['harga'] ?? '');
            $total += $landPrice;
        }
        return $total;
    }

    // Business Unit Filter Dropdown Methods
    public function updatedFilterBusinessUnitSearch($value)
    {
        // Don't auto-select if typing
        if (!$this->showBusinessUnitFilterDropdown) {
            $this->filterBusinessUnit = '';
        }
    }

    public function openBusinessUnitFilterDropdown()
    {
        $this->showBusinessUnitFilterDropdown = true;
        $this->showLandFilterDropdown = false;
    }

    public function selectBusinessUnitFilter($businessUnitId, $businessUnitName)
    {
        $this->filterBusinessUnit = $businessUnitId;
        $this->filterBusinessUnitSearch = $businessUnitName;
        $this->showBusinessUnitFilterDropdown = false;
        $this->resetPage(); // Reset pagination when filter changes
    }

    public function clearBusinessUnitFilterSearch()
    {
        $this->filterBusinessUnit = '';
        $this->filterBusinessUnitSearch = '';
        $this->showBusinessUnitFilterDropdown = false;
        $this->resetPage();
    }

    public function getFilteredBusinessUnitsForFilter()
    {
        $search = $this->filterBusinessUnitSearch ?? '';
        
        $query = BusinessUnit::query();
        
        // Only filter if there's search text, otherwise show all
        if (!empty(trim($search))) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('name')->limit(20)->get();
    }

    // Land Filter Dropdown Methods
    public function updatedFilterLandSearch($value)
    {
        // Don't auto-select if typing
        if (!$this->showLandFilterDropdown) {
            $this->filterLand = '';
        }
    }

    public function openLandFilterDropdown()
    {
        $this->showLandFilterDropdown = true;
        $this->showBusinessUnitFilterDropdown = false;
    }

    public function selectLandFilter($landId, $landName)
    {
        $this->filterLand = $landId;
        $this->filterLandSearch = $landName;
        $this->showLandFilterDropdown = false;
        $this->resetPage(); // Reset pagination when filter changes
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
        $search = $this->filterLandSearch ?? '';
        
        $query = Land::query();
        
        // Only filter if there's search text, otherwise show all
        if (!empty(trim($search))) {
            $query->where('lokasi_lahan', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('lokasi_lahan')->limit(20)->get();
    }
}