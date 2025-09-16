<?php

namespace App\Livewire;

use App\Models\Soil;
use App\Models\Land;
use App\Models\BusinessUnit;
use App\Models\DescriptionBiayaTambahanSoil;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BiayaTambahanSoil;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

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

    // Properties for description search (removed category)
    public $descriptionSearch = [];
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

    public $editSource = 'index';

    protected $rules = [
        'land_id' => 'required|exists:lands,id',
        'business_unit_id' => 'required|exists:business_units,id',
        'soilDetails.*.nama_penjual' => 'required|string|max:255',
        'soilDetails.*.alamat_penjual' => 'required|string',
        'soilDetails.*.nomor_ppjb' => 'required|string|max:255',
        'soilDetails.*.tanggal_ppjb' => 'required|date',
        'soilDetails.*.letak_tanah' => 'required|string|max:255',
        'soilDetails.*.luas' => 'required|numeric|min:0.01', // Changed from min:0 to min:0.01
        'soilDetails.*.harga' => 'required|numeric|min:0.01', // Changed from min:0 to min:0.01
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
            '_token' => csrf_token(),
        ];
        
        $this->hideExportModalView();
        session()->flash('message', 'Export completed successfully!');
        
        $this->dispatch('submit-export-form', params: $params);
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

    public function mount($businessUnit = null, $soilId = null)
    {
        if ($soilId) {
            $this->showDetail($soilId);
        }

        // Initialize BEFORE setting business unit data
        $this->initializeSoilDetails();
        
        \Log::info('Mount called', [
            'businessUnit' => $businessUnit,
            'soilId' => $soilId,
            'soilDetails_count' => count($this->soilDetails)
        ]);

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
            } elseif (is_string($businessUnit)) {
                $this->businessUnit = BusinessUnit::find((int)$businessUnit);
                if ($this->businessUnit) {
                    $this->filterByBusinessUnit = $this->businessUnit->id;
                    $this->business_unit_id = $this->businessUnit->id;
                    $this->businessUnitSearch = $this->businessUnit->name;
                    $this->allowBusinessUnitChange = false;
                }
            }
        } else {
            $this->allowBusinessUnitChange = true;
        }
    }

    private function initializeSoilDetails()
    {
        // Make sure we start with exactly one empty detail
        $this->soilDetails = [
            $this->createEmptySoilDetail()
        ];

        // Reset search arrays to match
        $this->sellerNameSearch = [''];
        $this->sellerAddressSearch = [''];
        $this->showSellerNameDropdown = [false];
        $this->showSellerAddressDropdown = [false];

        $this->landSearch = '';
        $this->showLandDropdown = false;

        $this->businessUnitSearch = '';
        $this->showBusinessUnitDropdown = false;
    }

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

    private function formatNumber($value)
    {
        if (empty($value) || $value === '' || $value === 0) {
            return '';
        }
        return number_format((int) $value, 0, ',', '.');
    }

    private function parseFormattedNumber($value)
    {
        if (empty($value) || $value === '') {
            return 0;
        }
        $cleaned = preg_replace('/[^\d]/', '', $value);
        return $cleaned ? (int) $cleaned : 0;
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

        $businessUnits = BusinessUnit::orderBy('name')->get();
        $lands = Land::orderBy('lokasi_lahan')->get();

        return view('livewire.soils.index', compact('soils', 'businessUnits', 'lands'));
    }

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

    public function showEditForm($id, $mode = 'details', $source = 'index')
    {
        $this->soil = Soil::findOrFail($id);
        $this->soilId = $this->soil->id;
        $this->editMode = $mode;
        $this->editSource = $source; // NEW: Store the source of the edit
        
        if ($mode === 'details') {
            $this->land_id = $this->soil->land_id;
            $this->business_unit_id = $this->soil->business_unit_id;

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

            $this->sellerNameSearch = [$this->soil->nama_penjual];
            $this->sellerAddressSearch = [$this->soil->alamat_penjual];
            $this->showSellerNameDropdown = [false];
            $this->showSellerAddressDropdown = [false];
            
            $this->showForm = true;
        } elseif ($mode === 'costs') {
            $this->biayaTambahan = [];
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

    public $biayaTambahan = [];

    public function save()
    {
        $this->validate();

        // DEBUG: Log what's in soilDetails
        \Log::info('soilDetails content:', [
            'count' => count($this->soilDetails),
            'data' => $this->soilDetails
        ]);

        if ($this->isEdit && $this->editMode === 'details') {
            // Update logic - this seems fine
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
            
            if ($this->editSource === 'detail') {
                $this->showForm = false;
                $this->showDetailForm = true;
                $this->isEdit = false;
            } else {
                $this->resetForm();
                $this->showForm = false;
            }
            
        } else {
            // CREATE NEW RECORDS - This is where the double entry happens
            
            // PROBLEM: Your soilDetails array might have empty or duplicate entries
            // SOLUTION: Filter and validate before creating
            
            $validDetails = collect($this->soilDetails)->filter(function($detail) {
                // Only process details that have required fields filled
                return !empty($detail['nama_penjual']) && 
                    !empty($detail['alamat_penjual']) && 
                    !empty($detail['nomor_ppjb']) &&
                    !empty($detail['letak_tanah']);
            })->values()->all();
            
            \Log::info('Valid details after filtering:', [
                'original_count' => count($this->soilDetails),
                'valid_count' => count($validDetails),
                'valid_data' => $validDetails
            ]);
            
            if (empty($validDetails)) {
                session()->flash('error', 'No valid soil details to save.');
                return;
            }
            
            foreach ($validDetails as $detail) {
                // Validate each field before creating
                $createData = [
                    'land_id' => $this->land_id,
                    'business_unit_id' => $this->business_unit_id,
                    'nama_penjual' => trim($detail['nama_penjual']),
                    'alamat_penjual' => trim($detail['alamat_penjual']),
                    'nomor_ppjb' => trim($detail['nomor_ppjb']),
                    'tanggal_ppjb' => $detail['tanggal_ppjb'],
                    'letak_tanah' => trim($detail['letak_tanah']),
                    'luas' => $this->parseFormattedNumber($detail['luas'] ?? ''),
                    'harga' => $this->parseFormattedNumber($detail['harga'] ?? ''),
                    'bukti_kepemilikan' => trim($detail['bukti_kepemilikan'] ?? ''),
                    'bukti_kepemilikan_details' => trim($detail['bukti_kepemilikan_details'] ?? ''),
                    'atas_nama' => trim($detail['atas_nama'] ?? ''),
                    'nop_pbb' => trim($detail['nop_pbb'] ?? ''),
                    'nama_notaris_ppat' => trim($detail['nama_notaris_ppat'] ?? ''),
                    'keterangan' => trim($detail['keterangan'] ?? ''),
                ];
                
                \Log::info('Creating soil record:', $createData);
                
                Soil::create($createData);
            }
            
            session()->flash('message', count($validDetails) . ' soil records created successfully.');
            $this->resetForm();
            $this->showForm = false;
        }
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
        
        // NEW: Return based on where the edit was initiated from
        if ($this->editSource === 'detail') {
            // Return to detail view
            $this->showAdditionalCostsForm = false;
            $this->showDetailForm = true;
            $this->isEdit = false;
            // Keep soilId to stay on the detail page
        } else {
            // Return to index view
            $this->showAdditionalCostsForm = false;
            $this->resetForm();
        }
    }

    private function updateBiayaTambahan($soil, $biayaTambahan)
    {
        // Use database transaction to prevent partial updates
        DB::transaction(function () use ($soil, $biayaTambahan) {
            
            // Set history logging flag to prevent automatic history creation
            $soil->historyLogging = true;
            
            if (empty($biayaTambahan) || !is_array($biayaTambahan)) {
                // Delete all existing costs
                $existingCosts = $soil->biayaTambahanSoils()->with('description')->get();
                foreach ($existingCosts as $cost) {
                    $description = $cost->description->description ?? 'Unknown';
                    $oldCostData = [
                        'description' => $description,
                        'harga' => $cost->harga,
                        'cost_type' => $cost->cost_type,
                        'date_cost' => $cost->date_cost,
                    ];
                    
                    $soil->logAdditionalCostHistory('deleted', [], $oldCostData);
                }
                
                $soil->biayaTambahanSoils()->delete();
                
                // Reset history logging flag
                $soil->historyLogging = false;
                return;
            }

            $existingIds = collect($biayaTambahan)
                ->filter(function($item) { return isset($item['id']); })
                ->pluck('id')
                ->toArray();

            // Delete costs that are no longer in the array
            $costsToDelete = $soil->biayaTambahanSoils()
                ->whereNotIn('id', $existingIds)
                ->with('description')
                ->get();
                
            foreach ($costsToDelete as $cost) {
                $description = $cost->description->description ?? 'Unknown';
                $oldCostData = [
                    'description' => $description,
                    'harga' => $cost->harga,
                    'cost_type' => $cost->cost_type,
                    'date_cost' => $cost->date_cost,
                ];
                
                $soil->logAdditionalCostHistory('deleted', [], $oldCostData);
            }
            
            $soil->biayaTambahanSoils()->whereNotIn('id', $existingIds)->delete();

            foreach ($biayaTambahan as $biaya) {
                if (!empty($biaya['description_id']) && !empty($biaya['harga'])) {
                    $harga = $this->parseFormattedNumber($biaya['harga']);
                    
                    $description = \App\Models\DescriptionBiayaTambahanSoil::find($biaya['description_id']);
                    $descriptionName = $description->description ?? 'Unknown';
                    
                    $costData = [
                        'description' => $descriptionName,
                        'harga' => $harga,
                        'cost_type' => $biaya['cost_type'],
                        'date_cost' => $biaya['date_cost'],
                    ];

                    if (isset($biaya['id'])) {
                        // UPDATE EXISTING COST
                        $existingCost = BiayaTambahanSoil::with('description')->find($biaya['id']);
                        
                        if ($existingCost) {
                            $oldCostData = [
                                'description' => $existingCost->description->description ?? 'Unknown',
                                'harga' => $existingCost->harga,
                                'cost_type' => $existingCost->cost_type,
                                'date_cost' => $existingCost->date_cost,
                            ];
                            
                            $hasChanges = (
                                $existingCost->description_id != $biaya['description_id'] ||
                                $existingCost->harga != $harga ||
                                $existingCost->cost_type != $biaya['cost_type'] ||
                                $existingCost->date_cost != $biaya['date_cost']
                            );
                            
                            if ($hasChanges) {
                                // SOLUTION: Use updateQuietly to bypass model events
                                $existingCost->updateQuietly([
                                    'description_id' => $biaya['description_id'],
                                    'harga' => $harga,
                                    'cost_type' => $biaya['cost_type'],
                                    'date_cost' => $biaya['date_cost']
                                ]);
                                
                                // Only create our custom history entry
                                $soil->logAdditionalCostHistory('updated', $costData, $oldCostData);
                            }
                        }
                    } else {
                        // CREATE NEW COST
                        BiayaTambahanSoil::create([
                            'soil_id' => $soil->id,
                            'description_id' => $biaya['description_id'],
                            'harga' => $harga,
                            'cost_type' => $biaya['cost_type'],
                            'date_cost' => $biaya['date_cost'],
                        ]);
                        
                        $soil->logAdditionalCostHistory('added', $costData);
                    }
                }
            }
            
            // Reset history logging flag
            $soil->historyLogging = false;
        });
    }

    public function delete($id)
    {
        $soil = Soil::findOrFail($id);
        
        $soil->biayaTambahanSoils()->delete();
        
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

    public function resetForm()
    {
        $this->reset([
            'soil', 'soilId', 'land_id', 'editMode', 'editSource', 'biayaTambahan',
            'descriptionSearch', 'showDescriptionDropdown',
            'sellerNameSearch', 'sellerAddressSearch', 'showSellerNameDropdown', 'showSellerAddressDropdown',
            'landSearch', 'showLandDropdown', 'businessUnitSearch', 'showBusinessUnitDropdown'
        ]);
        
        // Reinitialize with clean state
        $this->initializeSoilDetails();

        if ($this->filterByBusinessUnit && $this->businessUnit) {
            $this->business_unit_id = $this->businessUnit->id;
            $this->businessUnitSearch = $this->businessUnit->name;
            $this->allowBusinessUnitChange = false;
        } else {
            $this->business_unit_id = '';
            $this->businessUnitSearch = '';
            $this->allowBusinessUnitChange = true;
        }
        
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->filterBusinessUnit = '';
        $this->filterLand = '';
        $this->search = '';
        
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

    public function clearBusinessUnitFilter()
    {
        $this->filterByBusinessUnit = null;
        $this->businessUnit = null;
        $this->business_unit_id = '';
        return redirect()->route('soils');
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

    public function addSoilDetail()
    {
        $this->soilDetails[] = $this->createEmptySoilDetail();
        
        $newIndex = count($this->soilDetails) - 1;
        
        $this->sellerNameSearch[$newIndex] = '';
        $this->sellerAddressSearch[$newIndex] = '';
        $this->showSellerNameDropdown[$newIndex] = false;
        $this->showSellerAddressDropdown[$newIndex] = false;
    }

    public function removeSoilDetail($index)
    {
        if (count($this->soilDetails) > 1) {
            // Remove the specific index
            array_splice($this->soilDetails, $index, 1);
            array_splice($this->sellerNameSearch, $index, 1);
            array_splice($this->sellerAddressSearch, $index, 1);
            array_splice($this->showSellerNameDropdown, $index, 1);
            array_splice($this->showSellerAddressDropdown, $index, 1);
            
            // Re-index arrays to prevent gaps
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
        if (isset($this->soilDetails[$key])) {
            $this->soilDetails[$key]['nama_penjual'] = $value;
        }
    }

    public function searchSellerNames($index)
    {
        if (!is_array($this->showSellerNameDropdown)) {
            $this->showSellerNameDropdown = [];
        }
        if (!is_array($this->showSellerAddressDropdown)) {
            $this->showSellerAddressDropdown = [];
        }
        
        for ($i = 0; $i < count($this->soilDetails); $i++) {
            $this->showSellerNameDropdown[$i] = ($i === $index);
            $this->showSellerAddressDropdown[$i] = false;
        }
        
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
        
        if (!empty(trim($search))) {
            $query->where('nama_penjual', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('nama_penjual')->limit(20)->get();
    }

    // Seller address search methods
    public function updatedSellerAddressSearch($value, $key)
    {
        if (isset($this->soilDetails[$key])) {
            $this->soilDetails[$key]['alamat_penjual'] = $value;
        }
    }

    public function searchSellerAddresses($index)
    {
        if (!is_array($this->showSellerNameDropdown)) {
            $this->showSellerNameDropdown = [];
        }
        if (!is_array($this->showSellerAddressDropdown)) {
            $this->showSellerAddressDropdown = [];
        }
        
        for ($i = 0; $i < count($this->soilDetails); $i++) {
            $this->showSellerNameDropdown[$i] = false;
            $this->showSellerAddressDropdown[$i] = ($i === $index);
        }
        
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
        
        if (!empty(trim($search))) {
            $query->where('alamat_penjual', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('alamat_penjual')->limit(20)->get();
    }

    // Additional costs methods
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
        $this->descriptionSearch[$index] = '';
        $this->showDescriptionDropdown[$index] = false;
    }

    public function removeBiayaTambahan($index)
    {
        unset($this->biayaTambahan[$index]);
        unset($this->descriptionSearch[$index]);
        unset($this->showDescriptionDropdown[$index]);
        
        $this->biayaTambahan = array_values($this->biayaTambahan);
        $this->descriptionSearch = array_values($this->descriptionSearch);
        $this->showDescriptionDropdown = array_values($this->showDescriptionDropdown);
    }

    public function getTotalBiayaTambahan()
    {
        if (empty($this->biayaTambahan) || !is_array($this->biayaTambahan)) {
            return 0;
        }
        
        return collect($this->biayaTambahan)->sum(function($item) {
            if (isset($item['harga'])) {
                $parsed = $this->parseFormattedNumber($item['harga']);
                return is_numeric($parsed) ? (int)$parsed : 0;
            }
            return 0;
        });
    }

    public function getCostTypeOptions()
    {
        return BiayaTambahanSoil::getCostTypeOptions();
    }

    // Description search methods (removed category)
    public function updatedDescriptionSearch($value, $key)
    {
        if (!isset($this->showDescriptionDropdown[$key])) {
            $this->showDescriptionDropdown[$key] = false;
        }
    }

    public function searchDescriptions($index)
    {
        if (!is_array($this->showDescriptionDropdown)) {
            $this->showDescriptionDropdown = [];
        }
        
        for ($i = 0; $i < count($this->biayaTambahan); $i++) {
            $this->showDescriptionDropdown[$i] = ($i === $index);
        }
        
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
        
        $query = DescriptionBiayaTambahanSoil::query();
        
        if (!empty(trim($search))) {
            $query->where('description', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('description')->limit(20)->get();
    }

    public function updatedBiayaTambahanHarga($value, $propertyName)
    {
        $parts = explode('.', $propertyName);
        $index = $parts[0];
        
        $numericValue = $this->parseFormattedNumber($value);
        
        if ($numericValue) {
            $this->biayaTambahan[$index]['harga'] = $numericValue;
            $this->biayaTambahan[$index]['harga_display'] = $this->formatNumber($numericValue);
        } else {
            $this->biayaTambahan[$index]['harga'] = '';
            $this->biayaTambahan[$index]['harga_display'] = '';
        }
    }

    public function updatedSoilDetailsLuas($value, $propertyName)
    {
        $parts = explode('.', $propertyName);
        $index = $parts[0];
        
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
        $parts = explode('.', $propertyName);
        $index = $parts[0];
        
        $numericValue = $this->parseFormattedNumber($value);
        
        if ($numericValue) {
            $this->soilDetails[$index]['harga'] = $numericValue;
            $this->soilDetails[$index]['harga_display'] = $this->formatNumber($numericValue);
        } else {
            $this->soilDetails[$index]['harga'] = '';
            $this->soilDetails[$index]['harga_display'] = '';
        }
    }

    #[On('closeDropdowns')]
    public function closeDropdowns()
    {
        if (is_array($this->biayaTambahan) && count($this->biayaTambahan) > 0) {
            $this->showDescriptionDropdown = array_fill(0, count($this->biayaTambahan), false);
        } else {
            $this->showDescriptionDropdown = [];
        }

        if (is_array($this->soilDetails) && count($this->soilDetails) > 0) {
            $this->showSellerNameDropdown = array_fill(0, count($this->soilDetails), false);
            $this->showSellerAddressDropdown = array_fill(0, count($this->soilDetails), false);
        } else {
            $this->showSellerNameDropdown = [];
            $this->showSellerAddressDropdown = [];
        }

        $this->showLandDropdown = false;
        $this->showBusinessUnitDropdown = false;
        $this->showBusinessUnitFilterDropdown = false;
        $this->showLandFilterDropdown = false;
    }

    // Land search methods
    public function updatedLandSearch($value)
    {
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
        
        if (!empty(trim($search))) {
            $query->where('lokasi_lahan', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('lokasi_lahan')->limit(20)->get();
    }

    // Business Unit search methods
    public function updatedBusinessUnitSearch($value)
    {
        if (!$this->showBusinessUnitDropdown && !$this->filterByBusinessUnit) {
            $this->business_unit_id = '';
        }
    }

    public function searchBusinessUnits()
    {
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

        if ($this->filterByBusinessUnit && $businessUnitId != $this->filterByBusinessUnit) {
            session()->flash('warning', 'You have changed the business unit from the filtered selection.');
        }
    }

    public function getFilteredBusinessUnits()
    {
        $search = $this->businessUnitSearch ?? '';
        
        $query = BusinessUnit::query();
        
        if ($this->filterByBusinessUnit && !$this->allowBusinessUnitChange) {
            $query->where('id', $this->filterByBusinessUnit);
        } else {
            if (!empty(trim($search))) {
                $query->where('name', 'like', '%' . $search . '%');
            }
        }
        
        return $query->orderBy('name')->limit(20)->get();
    }

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
        $this->resetPage();
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
        
        if (!empty(trim($search))) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('name')->limit(20)->get();
    }

    // Land Filter Dropdown Methods
    public function updatedFilterLandSearch($value)
    {
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
        $search = $this->filterLandSearch ?? '';
        
        $query = Land::query();
        
        if (!empty(trim($search))) {
            $query->where('lokasi_lahan', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('lokasi_lahan')->limit(20)->get();
    }
}