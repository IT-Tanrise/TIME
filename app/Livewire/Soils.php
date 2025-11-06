<?php

namespace App\Livewire;

use App\Models\Soil;
use App\Models\Land;
use App\Models\BusinessUnit;
use App\Models\DescriptionBiayaTambahanSoil;
use App\Models\BiayaTambahanInterestSoil;
use App\Models\SoilApproval; // Add this import
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

    // Add properties for delete confirmation
    public $showDeleteModal = false;
    public $deleteReason = '';
    public $deleteSoilId = null;

    public $soilPrice = '';
    public $soilPriceDisplay = '';

    public $biayaInterest = [];
    public $showInterestCostsForm = false;

    public $status = 'active';
    public $filterStatus = '';

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'land_id' => 'required|exists:lands,id',
        'business_unit_id' => 'required|exists:business_units,id',
        'soilDetails.*.nama_penjual' => 'required|string|max:255',
        'soilDetails.*.alamat_penjual' => 'required|string',
        'soilDetails.*.nomor_ppjb' => 'required|string|max:255',
        'soilDetails.*.tanggal_ppjb' => 'required|date',
        'soilDetails.*.letak_tanah' => 'required|string|max:255',
        'soilDetails.*.luas' => 'required|numeric|min:0.01',
        'soilDetails.*.bukti_kepemilikan' => 'required|string|max:255',
        'soilDetails.*.bukti_kepemilikan_details' => 'nullable|string|max:255',
        'soilDetails.*.shgb_expired_date' => 'nullable|date', // Simple date validation
        'soilDetails.*.atas_nama' => 'required|string|max:255',
        'soilDetails.*.nop_pbb' => 'nullable|string|max:255',
        'soilDetails.*.nama_notaris_ppat' => 'nullable|string|max:255',
        'soilDetails.*.keterangan' => 'required|string',
        'status' => 'nullable|string|in:active,sold,reserved,pending,inactive',
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

    // Delete validation rules
    protected function getDeleteRules()
    {
        return [
            'deleteReason' => 'required|string|min:10|max:500',
        ];
    }

    protected $messages = [
        'land_id.required' => 'Please select a land.',
        'business_unit_id.required' => 'Please select a business unit.',
        'soilDetails.*.nama_penjual.required' => 'Seller name is required for all soil details.',
        'soilDetails.*.alamat_penjual.required' => 'Seller address is required for all soil details.',
        'soilDetails.*.nomor_ppjb.required' => 'PPJB number is required for all soil details.',
        'soilDetails.*.tanggal_ppjb.required' => 'PPJB date is required for all soil details.',
        'soilDetails.*.letak_tanah.required' => 'Soil location is required for all soil details.',
        'soilDetails.*.luas.required' => 'Area is required for all soil details.',
        'soilDetails.*.harga.required' => 'Price is required for all soil details.',
        'soilDetails.*.bukti_kepemilikan.required' => 'Ownership proof is required for all soil details.',
        'soilDetails.*.atas_nama.required' => 'Owner name is required for all soil details.',
        'soilDetails.*.nop_pbb.max' => 'NOP PBB must not exceed 255 characters.',
        'soilDetails.*.nama_notaris_ppat.max' => 'Notaris/PPAT name must not exceed 255 characters.',
        'soilDetails.*.keterangan.required' => 'Notes are required for all soil details.',
    ];

    public $biayaTambahan = [];

    // Show export modal
    public function showExportModalView()
    {
        $this->showExportModal = true;
        $this->exportType = 'current';
        $this->exportDateFrom = '';
        $this->exportDateTo = '';
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

    // Hide export modal
    public function hideExportModalView()
    {
        $this->showExportModal = false;
        $this->resetValidation(['exportType', 'exportDateFrom', 'exportDateTo']);
    }

    // Show delete modal
    public function showDeleteModalView($id)
    {
        $this->deleteSoilId = $id;
        $this->deleteReason = '';
        $this->showDeleteModal = true;
    }

    // Hide delete modal
    public function hideDeleteModalView()
    {
        $this->deleteSoilId = null;
        $this->deleteReason = '';
        $this->showDeleteModal = false;
        $this->resetValidation(['deleteReason']);
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
                $query->when($this->search, function ($q) {
                    $q->where('nama_penjual', 'like', '%' . $this->search . '%')
                        ->orWhere('letak_tanah', 'like', '%' . $this->search . '%')
                        ->orWhere('nomor_ppjb', 'like', '%' . $this->search . '%');
                })
                    ->when($this->filterBusinessUnit, function ($q) {
                        $q->where('business_unit_id', $this->filterBusinessUnit);
                    })
                    ->when($this->filterByBusinessUnit, function ($q) {
                        $q->where('business_unit_id', $this->filterByBusinessUnit);
                    })
                    ->when($this->filterLand, function ($q) {
                        $q->where('land_id', $this->filterLand);
                    });
                break;

            case 'date_range':
                if ($this->exportDateFrom && $this->exportDateTo) {
                    $query->whereBetween('created_at', [$this->exportDateFrom, $this->exportDateTo]);
                }
                $query->when($this->filterByBusinessUnit, function ($q) {
                    $q->where('business_unit_id', $this->filterByBusinessUnit);
                });
                break;

            case 'all':
            default:
                $query->when($this->filterByBusinessUnit, function ($q) {
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
            'bukti_kepemilikan' => '',
            'bukti_kepemilikan_details' => '',
            'shgb_expired_date' => '', // ADD THIS LINE
            'atas_nama' => '',
            'nop_pbb' => '',
            'nama_notaris_ppat' => '',
            'keterangan' => '',
            'status' => 'active',
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
            ->when($this->search, function ($query) {
                $query->where('nama_penjual', 'like', '%' . $this->search . '%')
                    ->orWhere('letak_tanah', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_ppjb', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterBusinessUnit, function ($query) {
                $query->where('business_unit_id', $this->filterBusinessUnit);
            })
            ->when($this->filterByBusinessUnit, function ($query) {
                $query->where('business_unit_id', $this->filterByBusinessUnit);
            })
            ->when($this->filterLand, function ($query) {
                $query->where('land_id', $this->filterLand);
            })
            ->when($this->filterStatus, function ($query) { // ADD THIS
                $query->where('status', $this->filterStatus);
            })
            ->orderBy($this->sortField, $this->sortDirection)
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
        $this->editSource = $source;

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
                    'bukti_kepemilikan' => $this->soil->bukti_kepemilikan,
                    'bukti_kepemilikan_details' => $this->soil->bukti_kepemilikan_details,
                    'shgb_expired_date' => $this->soil->shgb_expired_date ? $this->soil->shgb_expired_date->format('Y-m-d') : '', // ADD THIS LINE
                    'atas_nama' => $this->soil->atas_nama,
                    'nop_pbb' => $this->soil->nop_pbb,
                    'nama_notaris_ppat' => $this->soil->nama_notaris_ppat,
                    'keterangan' => $this->soil->keterangan,
                    'status' => $this->soil->status,
                ]
            ];

            $this->sellerNameSearch = [$this->soil->nama_penjual];
            $this->sellerAddressSearch = [$this->soil->alamat_penjual];
            $this->showSellerNameDropdown = [false];
            $this->showSellerAddressDropdown = [false];

            $this->showForm = true;
        } elseif ($mode === 'costs') {
            $this->soilPrice = $this->soil->harga;
            $this->soilPriceDisplay = $this->formatNumber($this->soil->harga);

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
        } elseif ($mode === 'interest') {
            // NEW: Handle interest costs mode
            $this->biayaInterest = [];

            foreach ($this->soil->biayaTambahanInterestSoils as $index => $interest) {
                $this->biayaInterest[] = [
                    'id' => $interest->id,
                    'start_date' => $interest->start_date->format('Y-m-d'),
                    'end_date' => $interest->end_date->format('Y-m-d'),
                    'remarks' => $interest->remarks,
                    'harga_perolehan' => $interest->harga_perolehan,
                    'harga_perolehan_display' => $this->formatNumber($interest->harga_perolehan),
                    'bunga' => $interest->bunga,
                ];
            }

            $this->showInterestCostsForm = true;
        }

        $this->isEdit = true;
    }

    public function showDetail($id)
    {
        $this->soilId = $id;
        $this->showDetailForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEdit && $this->editMode === 'details') {
            // EXISTING EDIT LOGIC
            if (auth()->user()->can('soil-data.approval')) {
                $soil = Soil::findOrFail($this->soilId);
                $detail = $this->soilDetails[0];

                // Prepare SHGB date - handle empty string
                // Normalize dates - store only date part without time
                $tanggalPpjb = !empty($detail['tanggal_ppjb']) ? \Carbon\Carbon::parse($detail['tanggal_ppjb'])->format('Y-m-d') : null;
                $shgbDate = !empty($detail['shgb_expired_date']) ? \Carbon\Carbon::parse($detail['shgb_expired_date'])->format('Y-m-d') : null;

                $soil->update([
                    'land_id' => $this->land_id,
                    'business_unit_id' => $this->business_unit_id,
                    'nama_penjual' => $detail['nama_penjual'],
                    'alamat_penjual' => $detail['alamat_penjual'],
                    'nomor_ppjb' => $detail['nomor_ppjb'],
                    'tanggal_ppjb' => $tanggalPpjb,
                    'letak_tanah' => $detail['letak_tanah'],
                    'luas' => $this->parseFormattedNumber($detail['luas']),
                    'bukti_kepemilikan' => $detail['bukti_kepemilikan'],
                    'bukti_kepemilikan_details' => $detail['bukti_kepemilikan_details'],
                    'shgb_expired_date' => $shgbDate,
                    'atas_nama' => $detail['atas_nama'],
                    'nop_pbb' => $detail['nop_pbb'],
                    'nama_notaris_ppat' => $detail['nama_notaris_ppat'],
                    'keterangan' => $detail['keterangan'],
                    'status' => $detail['status'] ?? 'active',
                ]);

                session()->flash('message', 'Soil record details updated successfully.');
            } else {
                $soil = Soil::findOrFail($this->soilId);
                $detail = $this->soilDetails[0];

                $oldData = $soil->only([
                    'land_id',
                    'business_unit_id',
                    'nama_penjual',
                    'alamat_penjual',
                    'nomor_ppjb',
                    'tanggal_ppjb',
                    'letak_tanah',
                    'luas',
                    'bukti_kepemilikan',
                    'bukti_kepemilikan_details',
                    'shgb_expired_date',
                    'atas_nama',
                    'nop_pbb',
                    'nama_notaris_ppat',
                    'keterangan',
                    'status'
                ]);

                // Prepare SHGB date - handle empty string
                $shgbDate = !empty($detail['shgb_expired_date']) ? $detail['shgb_expired_date'] : null;

                $newData = [
                    'land_id' => $this->land_id,
                    'business_unit_id' => $this->business_unit_id,
                    'nama_penjual' => $detail['nama_penjual'],
                    'alamat_penjual' => $detail['alamat_penjual'],
                    'nomor_ppjb' => $detail['nomor_ppjb'],
                    'tanggal_ppjb' => $detail['tanggal_ppjb'],
                    'letak_tanah' => $detail['letak_tanah'],
                    'luas' => $this->parseFormattedNumber($detail['luas']),
                    'bukti_kepemilikan' => $detail['bukti_kepemilikan'],
                    'bukti_kepemilikan_details' => $detail['bukti_kepemilikan_details'],
                    'shgb_expired_date' => $shgbDate, // FIXED: Use prepared variable
                    'atas_nama' => $detail['atas_nama'],
                    'nop_pbb' => $detail['nop_pbb'],
                    'nama_notaris_ppat' => $detail['nama_notaris_ppat'],
                    'keterangan' => $detail['keterangan'],
                    'status' => $detail['status'] ?? 'active',
                ];

                // Normalize date format in oldData BEFORE comparison
                if (isset($oldData['tanggal_ppjb']) && $oldData['tanggal_ppjb'] instanceof \Carbon\Carbon) {
                    $oldData['tanggal_ppjb'] = $oldData['tanggal_ppjb']->format('Y-m-d');
                }
                if (isset($oldData['shgb_expired_date']) && $oldData['shgb_expired_date'] instanceof \Carbon\Carbon) {
                    $oldData['shgb_expired_date'] = $oldData['shgb_expired_date']->format('Y-m-d');
                }

                // CHECK IF DATA ACTUALLY CHANGED
                if ($this->hasDataChanged($oldData, $newData)) {
                    SoilApproval::create([
                        'soil_id' => $this->soilId,
                        'requested_by' => auth()->id(),
                        'old_data' => $oldData,
                        'new_data' => $newData,
                        'change_type' => 'details',
                        'status' => 'pending'
                    ]);

                    session()->flash('warning', 'Your soil data changes have been submitted for approval and are pending review.');
                } else {
                    session()->flash('info', 'No changes detected. The data is identical to the existing record.');
                }
            }

            if ($this->editSource === 'detail') {
                $this->showForm = false;
                $this->showDetailForm = true;
                $this->isEdit = false;
            } else {
                $this->resetForm();
                $this->showForm = false;
            }
        } else {
            // NEW: CREATE NEW RECORDS
            $validDetails = collect($this->soilDetails)->filter(function ($detail) {
                return !empty($detail['nama_penjual']) &&
                    !empty($detail['alamat_penjual']) &&
                    !empty($detail['nomor_ppjb']) &&
                    !empty($detail['letak_tanah']);
            })->values()->all();

            if (empty($validDetails)) {
                session()->flash('error', 'No valid soil details to save.');
                return;
            }

            if (auth()->user()->can('soil-data.approval')) {
                $createdCount = 0;

                foreach ($validDetails as $detail) {
                    // Prepare SHGB date - handle empty string
                    $shgbDate = !empty($detail['shgb_expired_date']) ? $detail['shgb_expired_date'] : null;

                    $createData = [
                        'land_id' => $this->land_id,
                        'business_unit_id' => $this->business_unit_id,
                        'nama_penjual' => trim($detail['nama_penjual']),
                        'alamat_penjual' => trim($detail['alamat_penjual']),
                        'nomor_ppjb' => trim($detail['nomor_ppjb']),
                        'tanggal_ppjb' => $detail['tanggal_ppjb'],
                        'letak_tanah' => trim($detail['letak_tanah']),
                        'luas' => $this->parseFormattedNumber($detail['luas'] ?? ''),
                        'harga' => 0, // Default value, will be set in costs management
                        'bukti_kepemilikan' => trim($detail['bukti_kepemilikan'] ?? ''),
                        'bukti_kepemilikan_details' => trim($detail['bukti_kepemilikan_details'] ?? ''),
                        'shgb_expired_date' => $shgbDate, // FIXED: Use prepared variable
                        'atas_nama' => trim($detail['atas_nama'] ?? ''),
                        'nop_pbb' => trim($detail['nop_pbb'] ?? ''),
                        'nama_notaris_ppat' => trim($detail['nama_notaris_ppat'] ?? ''),
                        'keterangan' => trim($detail['keterangan'] ?? ''),
                        'status' => trim($detail['status'] ?? 'active'),
                    ];

                    Soil::create($createData);
                    $createdCount++;
                }

                session()->flash('message', $createdCount . ' soil record(s) created successfully. Please set soil price in Manage Costs.');
            } else {
                $requestCount = 0;

                foreach ($validDetails as $detail) {
                    // Prepare SHGB date - handle empty string
                    $shgbDate = !empty($detail['shgb_expired_date']) ? $detail['shgb_expired_date'] : null;

                    $createData = [
                        'land_id' => $this->land_id,
                        'business_unit_id' => $this->business_unit_id,
                        'nama_penjual' => trim($detail['nama_penjual']),
                        'alamat_penjual' => trim($detail['alamat_penjual']),
                        'nomor_ppjb' => trim($detail['nomor_ppjb']),
                        'tanggal_ppjb' => $detail['tanggal_ppjb'],
                        'letak_tanah' => trim($detail['letak_tanah']),
                        'luas' => $this->parseFormattedNumber($detail['luas'] ?? ''),
                        'harga' => 0, // Default value
                        'bukti_kepemilikan' => trim($detail['bukti_kepemilikan'] ?? ''),
                        'bukti_kepemilikan_details' => trim($detail['bukti_kepemilikan_details'] ?? ''),
                        'shgb_expired_date' => $shgbDate, // FIXED: Use prepared variable
                        'atas_nama' => trim($detail['atas_nama'] ?? ''),
                        'nop_pbb' => trim($detail['nop_pbb'] ?? ''),
                        'nama_notaris_ppat' => trim($detail['nama_notaris_ppat'] ?? ''),
                        'keterangan' => trim($detail['keterangan'] ?? ''),
                    ];

                    SoilApproval::create([
                        'soil_id' => null,
                        'requested_by' => auth()->id(),
                        'old_data' => [],
                        'new_data' => $createData,
                        'change_type' => 'create',
                        'status' => 'pending'
                    ]);

                    $requestCount++;
                }

                session()->flash('warning', $requestCount . ' soil record creation request(s) have been submitted for approval and are pending review.');
            }

            $this->resetForm();
            $this->showForm = false;
        }
    }

    public function saveAdditionalCosts()
    {
        $this->validate([
            'soilPrice' => 'required|numeric|min:0',
            'biayaTambahan.*.description_id' => 'required|exists:description_biaya_tambahan_soils,id',
            'biayaTambahan.*.harga' => 'required|numeric|min:0',
            'biayaTambahan.*.cost_type' => 'required|in:standard,non_standard',
            'biayaTambahan.*.date_cost' => 'required|date',
        ]);

        $soil = Soil::findOrFail($this->soilId);

        if (auth()->user()->can('soil-data-costs.approval')) {
            // User has cost approval permission - update directly

            // Save soil price
            $soil->update([
                'harga' => $this->parseFormattedNumber($this->soilPrice)
            ]);

            // Save additional costs
            $this->updateBiayaTambahan($soil, $this->biayaTambahan);

            session()->flash('message', 'Soil price and additional costs updated successfully.');
        } else {
            // User needs approval - check if costs changed
            $oldCostData = $soil->biayaTambahanSoils()->with('description')->get()->map(function ($cost) {
                return [
                    'id' => $cost->id,
                    'description_id' => $cost->description_id,
                    'description' => $cost->description->description ?? '',
                    'harga' => $cost->harga,
                    'cost_type' => $cost->cost_type,
                    'date_cost' => $cost->date_cost ? $cost->date_cost->format('Y-m-d') : null,
                ];
            })->toArray();

            $newCostData = collect($this->biayaTambahan)->map(function ($biaya) {
                $description = \App\Models\DescriptionBiayaTambahanSoil::find($biaya['description_id']);
                return [
                    'id' => $biaya['id'] ?? null,
                    'description_id' => $biaya['description_id'],
                    'description' => $description->description ?? '',
                    'harga' => $this->parseFormattedNumber($biaya['harga']),
                    'cost_type' => $biaya['cost_type'],
                    'date_cost' => $biaya['date_cost'],
                ];
            })->toArray();

            // Check soil price change
            $oldSoilPrice = $soil->harga;
            $newSoilPrice = $this->parseFormattedNumber($this->soilPrice);
            $priceChanged = ($oldSoilPrice != $newSoilPrice);

            // Check if costs changed
            $costsChanged = $this->hasCostsChanged($oldCostData, $newCostData);

            if (!$priceChanged && !$costsChanged) {
                session()->flash('info', 'No changes detected. The costs are identical to the existing record.');
            } else {
                // Create approval requests only for what changed
                if ($costsChanged) {
                    SoilApproval::create([
                        'soil_id' => $this->soilId,
                        'requested_by' => auth()->id(),
                        'old_data' => $oldCostData,
                        'new_data' => $newCostData,
                        'change_type' => 'costs',
                        'status' => 'pending'
                    ]);
                }

                if ($priceChanged) {
                    SoilApproval::create([
                        'soil_id' => $this->soilId,
                        'requested_by' => auth()->id(),
                        'old_data' => ['harga' => $oldSoilPrice],
                        'new_data' => ['harga' => $newSoilPrice],
                        'change_type' => 'details',
                        'status' => 'pending'
                    ]);
                }

                session()->flash('warning', 'Your cost changes have been submitted for approval and are pending review.');
            }
        }

        // Return based on where the edit was initiated from
        if ($this->editSource === 'detail') {
            $this->showAdditionalCostsForm = false;
            $this->showDetailForm = true;
            $this->isEdit = false;
        } else {
            $this->showAdditionalCostsForm = false;
            $this->resetForm();
        }
    }

    public function updatedSoilPrice($value)
    {
        $numericValue = $this->parseFormattedNumber($value);

        if ($numericValue) {
            $this->soilPrice = $numericValue;
            $this->soilPriceDisplay = $this->formatNumber($numericValue);
        } else {
            $this->soilPrice = '';
            $this->soilPriceDisplay = '';
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
                ->filter(function ($item) {
                    return isset($item['id']);
                })
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
                                // Update without triggering model events
                                $existingCost->update([
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

    /**
     * UPDATED DELETE FUNCTION WITH APPROVAL WORKFLOW
     */
    public function delete($id)
    {
        $soil = Soil::findOrFail($id);

        // Check if user has deletion approval permission
        if (auth()->user()->can('soil-data.approval')) {
            // User has approval permission - delete directly
            $soil->biayaTambahanSoils()->delete();
            $soil->delete();
            session()->flash('message', 'Soil record deleted successfully.');
        } else {
            // User needs approval - create deletion approval request
            $oldData = $soil->only([
                'land_id',
                'business_unit_id',
                'nama_penjual',
                'alamat_penjual',
                'nomor_ppjb',
                'tanggal_ppjb',
                'letak_tanah',
                'luas',
                'harga',
                'bukti_kepemilikan',
                'bukti_kepemilikan_details',
                'atas_nama',
                'nop_pbb',
                'nama_notaris_ppat',
                'keterangan',
                'status'
            ]);

            // Include related costs in the deletion approval
            $oldCostData = $soil->biayaTambahanSoils()->with('description')->get()->map(function ($cost) {
                return [
                    'id' => $cost->id,
                    'description_id' => $cost->description_id,
                    'description' => $cost->description->description ?? '',
                    'harga' => $cost->harga,
                    'cost_type' => $cost->cost_type,
                    'date_cost' => $cost->date_cost,
                ];
            })->toArray();

            $oldData['additional_costs'] = $oldCostData;

            SoilApproval::create([
                'soil_id' => $soil->id,
                'requested_by' => auth()->id(),
                'old_data' => $oldData,
                'new_data' => [], // Empty for deletion
                'change_type' => 'delete',
                'status' => 'pending'
            ]);

            session()->flash('warning', 'Your deletion request has been submitted for approval and is pending review.');
        }
    }

    /**
     * DELETE WITH REASON (Called from delete modal)
     */
    public function deleteWithReason()
    {
        $this->validate($this->getDeleteRules());

        if ($this->deleteSoilId) {
            $soil = Soil::findOrFail($this->deleteSoilId);

            // Check if user has deletion approval permission
            if (auth()->user()->can('soil-data.approval')) {
                // User has approval permission - delete directly
                $soil->biayaTambahanSoils()->delete();
                $soil->delete();
                session()->flash('message', 'Soil record deleted successfully.');
            } else {
                // User needs approval - create deletion approval request with reason
                $oldData = $soil->only([
                    'land_id',
                    'business_unit_id',
                    'nama_penjual',
                    'alamat_penjual',
                    'nomor_ppjb',
                    'tanggal_ppjb',
                    'letak_tanah',
                    'luas',
                    'harga',
                    'bukti_kepemilikan',
                    'bukti_kepemilikan_details',
                    'atas_nama',
                    'nop_pbb',
                    'nama_notaris_ppat',
                    'keterangan',
                    'status'
                ]);

                // Include related costs in the deletion approval
                $oldCostData = $soil->biayaTambahanSoils()->with('description')->get()->map(function ($cost) {
                    return [
                        'id' => $cost->id,
                        'description_id' => $cost->description_id,
                        'description' => $cost->description->description ?? '',
                        'harga' => $cost->harga,
                        'cost_type' => $cost->cost_type,
                        'date_cost' => $cost->date_cost,
                    ];
                })->toArray();

                $oldData['additional_costs'] = $oldCostData;

                SoilApproval::create([
                    'soil_id' => $soil->id,
                    'requested_by' => auth()->id(),
                    'old_data' => $oldData,
                    'new_data' => ['deletion_reason' => $this->deleteReason], // Store reason
                    'change_type' => 'delete',
                    'status' => 'pending'
                ]);

                session()->flash('warning', 'Your deletion request has been submitted for approval and is pending review.');
            }

            $this->hideDeleteModalView();
        }
    }

    public function backToIndex()
    {
        $this->showForm = false;
        $this->showDetailForm = false;
        $this->showAdditionalCostsForm = false;
        $this->showInterestCostsForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'soil',
            'soilId',
            'land_id',
            'editMode',
            'editSource',
            'biayaTambahan',
            'biayaInterest',
            'descriptionSearch',
            'showDescriptionDropdown',
            'sellerNameSearch',
            'sellerAddressSearch',
            'showSellerNameDropdown',
            'showSellerAddressDropdown',
            'landSearch',
            'showLandDropdown',
            'businessUnitSearch',
            'showBusinessUnitDropdown'
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
        $this->filterStatus = ''; // ADD THIS LINE
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

        // Ensure arrays are properly sized
        if (!is_array($this->descriptionSearch)) {
            $this->descriptionSearch = [];
        }
        if (!is_array($this->showDescriptionDropdown)) {
            $this->showDescriptionDropdown = [];
        }

        $this->descriptionSearch[$index] = '';
        $this->showDescriptionDropdown[$index] = false;
    }

    public function removeBiayaTambahan($index)
    {
        // Remove from all related arrays
        unset($this->biayaTambahan[$index]);
        unset($this->descriptionSearch[$index]);
        unset($this->showDescriptionDropdown[$index]);

        // Re-index arrays to prevent gaps
        $this->biayaTambahan = array_values($this->biayaTambahan);
        $this->descriptionSearch = array_values($this->descriptionSearch);
        $this->showDescriptionDropdown = array_values($this->showDescriptionDropdown);

        // Dispatch event to clean up any DOM references
        $this->dispatch('cost-item-removed', removedIndex: $index);
    }

    public function getTotalBiayaTambahan()
    {
        if (empty($this->biayaTambahan) || !is_array($this->biayaTambahan)) {
            return 0;
        }

        return collect($this->biayaTambahan)->sum(function ($item) {
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
        // Simple approach - just ensure dropdown stays open while typing
        if (!is_array($this->showDescriptionDropdown)) {
            $this->showDescriptionDropdown = [];
        }

        // Keep dropdown open when there's a value or when empty (to show all options)
        $this->showDescriptionDropdown[$key] = true;
    }

    public function searchDescriptions($index)
    {
        // Initialize arrays if needed
        if (!is_array($this->showDescriptionDropdown)) {
            $this->showDescriptionDropdown = [];
        }

        // Close all other dropdowns first - same as business unit dropdown
        for ($i = 0; $i < count($this->biayaTambahan); $i++) {
            $this->showDescriptionDropdown[$i] = false;
        }

        // Open the requested dropdown
        $this->showDescriptionDropdown[$index] = true;

        // Initialize search if not set
        if (!isset($this->descriptionSearch[$index])) {
            $this->descriptionSearch[$index] = '';
        }
    }

    public function selectDescription($index, $descriptionId, $descriptionName)
    {
        // Set the values - same pattern as business unit
        if (isset($this->biayaTambahan[$index])) {
            $this->biayaTambahan[$index]['description_id'] = $descriptionId;
        }

        if (!is_array($this->descriptionSearch)) {
            $this->descriptionSearch = [];
        }
        $this->descriptionSearch[$index] = $descriptionName;

        // Close dropdown
        $this->showDescriptionDropdown[$index] = false;
    }

    public function getFilteredDescriptions($index)
    {
        $search = $this->descriptionSearch[$index] ?? '';

        $query = DescriptionBiayaTambahanSoil::query();

        // Apply search filter if there's text
        if (!empty(trim($search))) {
            $query->where('description', 'like', '%' . trim($search) . '%');
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

    #[On('closeDropdowns')]
    public function closeDropdowns()
    {
        // Close description dropdowns
        if (is_array($this->biayaTambahan) && count($this->biayaTambahan) > 0) {
            $this->showDescriptionDropdown = array_fill(0, count($this->biayaTambahan), false);
        } else {
            $this->showDescriptionDropdown = [];
        }

        // Close other dropdowns
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
        if (!empty($this->business_unit_id)) {
            $this->showLandDropdown = true;
            $this->showBusinessUnitDropdown = false;
        }
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

        // IMPORTANT: Only show lands if business unit is selected
        if (!empty($this->business_unit_id)) {
            $query->where('business_unit_id', $this->business_unit_id);
        } else {
            // If no business unit selected, return empty collection
            return collect();
        }

        // Apply search filter
        if (!empty(trim($search))) {
            $query->where(function ($q) use ($search) {
                $q->where('lokasi_lahan', 'like', '%' . $search . '%')
                    ->orWhere('kota_kabupaten', 'like', '%' . $search . '%');
            });
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
        // Store old business unit id to check if it changed
        $oldBusinessUnitId = $this->business_unit_id;

        $this->business_unit_id = $businessUnitId;
        $this->businessUnitSearch = $businessUnitName;
        $this->showBusinessUnitDropdown = false;

        // Reset land selection if business unit changed
        if ($oldBusinessUnitId != $businessUnitId) {
            $this->land_id = '';
            $this->landSearch = '';
            $this->showLandDropdown = false;

            if ($this->filterByBusinessUnit && $businessUnitId != $this->filterByBusinessUnit) {
                session()->flash('warning', 'You have changed the business unit from the filtered selection. Land options have been reset.');
            }
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
        // Store old filter to check if it changed
        $oldFilterBusinessUnit = $this->filterBusinessUnit;

        $this->filterBusinessUnit = $businessUnitId;
        $this->filterBusinessUnitSearch = $businessUnitName;
        $this->showBusinessUnitFilterDropdown = false;

        // Reset land filter if business unit filter changed
        if ($oldFilterBusinessUnit != $businessUnitId) {
            $this->filterLand = '';
            $this->filterLandSearch = '';
            $this->showLandFilterDropdown = false;
        }

        $this->resetPage();
    }

    public function clearBusinessUnitFilterSearch()
    {
        $this->filterBusinessUnit = '';
        $this->filterBusinessUnitSearch = '';
        $this->showBusinessUnitFilterDropdown = false;

        // Also clear land filter since it depends on business unit
        $this->filterLand = '';
        $this->filterLandSearch = '';
        $this->showLandFilterDropdown = false;

        $this->resetPage();
    }

    public function getFilteredBusinessUnitsForFilter()
    {
        $search = $this->filterBusinessUnitSearch ?? '';

        $query = BusinessUnit::query();

        if (!empty(trim($search))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
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
        // Only open if business unit filter is selected
        if ($this->filterBusinessUnit || $this->filterByBusinessUnit) {
            $this->showLandFilterDropdown = true;
            $this->showBusinessUnitFilterDropdown = false;
        }
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

        // Priority 1: Filter by business unit from filter dropdown
        if (!empty($this->filterBusinessUnit)) {
            $query->where('business_unit_id', $this->filterBusinessUnit);
        }
        // Priority 2: Filter by pre-selected business unit from route
        elseif (!empty($this->filterByBusinessUnit)) {
            $query->where('business_unit_id', $this->filterByBusinessUnit);
        }
        // If no business unit filter, return empty to prevent showing all lands
        else {
            return collect();
        }

        // Apply search filter
        if (!empty(trim($search))) {
            $query->where(function ($q) use ($search) {
                $q->where('lokasi_lahan', 'like', '%' . $search . '%')
                    ->orWhere('kota_kabupaten', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('lokasi_lahan')->limit(20)->get();
    }

    /**
     * Check if data has actually changed
     */
    private function hasDataChanged($oldData, $newData)
    {
        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;

            // Special handling for dates - normalize format
            if ($key === 'tanggal_ppjb' || str_ends_with($key, '_date')) {
                $oldValue = $this->normalizeDateValue($oldValue);
                $newValue = $this->normalizeDateValue($newValue);
            }

            // Normalize values for comparison
            $oldNormalized = $this->normalizeValue($oldValue);
            $newNormalized = $this->normalizeValue($newValue);

            if ($oldNormalized !== $newNormalized) {
                return true;
            }
        }

        return false;
    }

    private function normalizeDateValue($value)
    {
        if (empty($value)) {
            return '';
        }

        // If it's a Carbon instance
        if ($value instanceof \Carbon\Carbon) {
            return $value->format('Y-m-d');
        }

        // If it's a DateTime string with timezone info
        if (is_string($value) && str_contains($value, 'T')) {
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return $value;
            }
        }

        // If it's already in Y-m-d format
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // Try to parse any other format
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Check if costs have changed
     */
    private function hasCostsChanged($oldCostData, $newCostData)
    {
        // First check if count is different
        if (count($oldCostData) !== count($newCostData)) {
            return true;
        }

        // Create lookup arrays by ID
        $oldCostsById = collect($oldCostData)->keyBy('id');
        $newCostsById = collect($newCostData)->keyBy('id')->filter(fn($item) => !empty($item['id']));

        // Check for new costs (without ID)
        $hasNewCosts = collect($newCostData)->filter(fn($item) => empty($item['id']))->isNotEmpty();
        if ($hasNewCosts) {
            return true;
        }

        // Check for deleted costs
        $oldIds = $oldCostsById->keys();
        $newIds = $newCostsById->keys();
        if ($oldIds->diff($newIds)->isNotEmpty()) {
            return true;
        }

        // Check for modified costs
        foreach ($newIds as $costId) {
            $oldCost = $oldCostsById->get($costId);
            $newCost = $newCostsById->get($costId);

            if ($oldCost && $newCost) {
                if (
                    $oldCost['description_id'] != $newCost['description_id'] ||
                    $oldCost['harga'] != $newCost['harga'] ||
                    $oldCost['cost_type'] != $newCost['cost_type'] ||
                    $oldCost['date_cost'] != $newCost['date_cost']
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Normalize value for comparison
     */
    private function normalizeValue($value)
    {
        if (is_null($value)) {
            return '';
        }

        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    public function addBiayaInterest()
    {
        $index = count($this->biayaInterest);

        // Calculate default start_date from last entry's end_date
        $lastInterest = end($this->biayaInterest);
        $defaultStartDate = $lastInterest ? $lastInterest['end_date'] : date('Y-m-d');

        $this->biayaInterest[] = [
            'start_date' => $defaultStartDate,
            'end_date' => '',
            'remarks' => '',
            'harga_perolehan' => 0,
            'harga_perolehan_display' => '',
            'bunga' => 7.5, // Default interest rate
        ];
    }

    public function removeBiayaInterest($index)
    {
        unset($this->biayaInterest[$index]);
        $this->biayaInterest = array_values($this->biayaInterest);
    }

    public function saveInterestCosts()
    {
        $this->validate([
            'biayaInterest.*.start_date' => 'required|date',
            'biayaInterest.*.end_date' => 'required|date|after_or_equal:biayaInterest.*.start_date',
            'biayaInterest.*.remarks' => 'required|string|max:500',
            'biayaInterest.*.harga_perolehan' => 'required|numeric|min:0',
            'biayaInterest.*.bunga' => 'required|numeric|min:0|max:100',
        ]);

        $soil = Soil::findOrFail($this->soilId);

        if (auth()->user()->can('soil-data-costs.approval')) {
            // User has cost approval permission - update directly
            $this->updateBiayaInterest($soil, $this->biayaInterest);
            session()->flash('message', 'Interest costs updated successfully.');
        } else {
            // User needs approval - create approval request
            $oldInterestData = $soil->biayaTambahanInterestSoils()->get()->map(function ($interest) {
                return [
                    'id' => $interest->id,
                    'start_date' => $interest->start_date->format('Y-m-d'),
                    'end_date' => $interest->end_date->format('Y-m-d'),
                    'remarks' => $interest->remarks,
                    'harga_perolehan' => $interest->harga_perolehan,
                    'bunga' => $interest->bunga,
                ];
            })->toArray();

            $newInterestData = collect($this->biayaInterest)->map(function ($interest) {
                return [
                    'id' => $interest['id'] ?? null,
                    'start_date' => $interest['start_date'],
                    'end_date' => $interest['end_date'],
                    'remarks' => $interest['remarks'],
                    'harga_perolehan' => $this->parseFormattedNumber($interest['harga_perolehan']),
                    'bunga' => $interest['bunga'],
                ];
            })->toArray();

            // Check if data changed
            if ($this->hasInterestChanged($oldInterestData, $newInterestData)) {
                SoilApproval::create([
                    'soil_id' => $this->soilId,
                    'requested_by' => auth()->id(),
                    'old_data' => $oldInterestData,
                    'new_data' => $newInterestData,
                    'change_type' => 'interest',
                    'status' => 'pending'
                ]);

                session()->flash('warning', 'Your interest cost changes have been submitted for approval and are pending review.');
            } else {
                session()->flash('info', 'No changes detected. The interest data is identical to the existing record.');
            }
        }

        // Return based on where the edit was initiated from
        if ($this->editSource === 'detail') {
            $this->showInterestCostsForm = false;
            $this->showDetailForm = true;
            $this->isEdit = false;
        } else {
            $this->showInterestCostsForm = false;
            $this->resetForm();
        }
    }

    private function updateBiayaInterest($soil, $biayaInterest)
    {
        DB::transaction(function () use ($soil, $biayaInterest) {
            $soil->historyLogging = true;

            if (empty($biayaInterest) || !is_array($biayaInterest)) {
                $existingInterests = $soil->biayaTambahanInterestSoils()->get();
                foreach ($existingInterests as $interest) {
                    $oldData = [
                        'start_date' => $interest->start_date->format('Y-m-d'),
                        'end_date' => $interest->end_date->format('Y-m-d'),
                        'remarks' => $interest->remarks,
                        'harga_perolehan' => $interest->harga_perolehan,
                        'bunga' => $interest->bunga,
                    ];

                    $soil->logInterestHistory('deleted', [], $oldData);
                }

                $soil->biayaTambahanInterestSoils()->delete();
                $soil->historyLogging = false;
                return;
            }

            $existingIds = collect($biayaInterest)
                ->filter(function ($item) {
                    return isset($item['id']);
                })
                ->pluck('id')
                ->toArray();

            $interestsToDelete = $soil->biayaTambahanInterestSoils()
                ->whereNotIn('id', $existingIds)
                ->get();

            foreach ($interestsToDelete as $interest) {
                $oldData = [
                    'start_date' => $interest->start_date->format('Y-m-d'),
                    'end_date' => $interest->end_date->format('Y-m-d'),
                    'remarks' => $interest->remarks,
                    'harga_perolehan' => $interest->harga_perolehan,
                    'bunga' => $interest->bunga,
                ];

                $soil->logInterestHistory('deleted', [], $oldData);
            }

            $soil->biayaTambahanInterestSoils()->whereNotIn('id', $existingIds)->delete();

            foreach ($biayaInterest as $interest) {
                if (!empty($interest['start_date']) && !empty($interest['end_date'])) {
                    $hargaPerolehan = $this->parseFormattedNumber($interest['harga_perolehan']);

                    $interestData = [
                        'start_date' => $interest['start_date'],
                        'end_date' => $interest['end_date'],
                        'remarks' => $interest['remarks'],
                        'harga_perolehan' => $hargaPerolehan,
                        'bunga' => $interest['bunga'],
                    ];

                    if (isset($interest['id'])) {
                        $existingInterest = BiayaTambahanInterestSoil::find($interest['id']);

                        if ($existingInterest) {
                            $oldData = [
                                'start_date' => $existingInterest->start_date->format('Y-m-d'),
                                'end_date' => $existingInterest->end_date->format('Y-m-d'),
                                'remarks' => $existingInterest->remarks,
                                'harga_perolehan' => $existingInterest->harga_perolehan,
                                'bunga' => $existingInterest->bunga,
                            ];

                            $hasChanges = (
                                $existingInterest->start_date->format('Y-m-d') != $interest['start_date'] ||
                                $existingInterest->end_date->format('Y-m-d') != $interest['end_date'] ||
                                $existingInterest->remarks != $interest['remarks'] ||
                                $existingInterest->harga_perolehan != $hargaPerolehan ||
                                $existingInterest->bunga != $interest['bunga']
                            );

                            if ($hasChanges) {
                                $existingInterest->update($interestData);
                                $soil->logInterestHistory('updated', $interestData, $oldData);
                            }
                        }
                    } else {
                        BiayaTambahanInterestSoil::create(array_merge([
                            'soil_id' => $soil->id,
                        ], $interestData));

                        $soil->logInterestHistory('added', $interestData);
                    }
                }
            }

            $soil->historyLogging = false;
        });
    }

    private function hasInterestChanged($oldData, $newData)
    {
        if (count($oldData) !== count($newData)) {
            return true;
        }

        $oldById = collect($oldData)->keyBy('id');
        $newById = collect($newData)->keyBy('id')->filter(fn($item) => !empty($item['id']));

        $hasNewItems = collect($newData)->filter(fn($item) => empty($item['id']))->isNotEmpty();
        if ($hasNewItems) {
            return true;
        }

        $oldIds = $oldById->keys();
        $newIds = $newById->keys();
        if ($oldIds->diff($newIds)->isNotEmpty()) {
            return true;
        }

        foreach ($newIds as $id) {
            $old = $oldById->get($id);
            $new = $newById->get($id);

            if ($old && $new) {
                if (
                    $old['start_date'] != $new['start_date'] ||
                    $old['end_date'] != $new['end_date'] ||
                    $old['remarks'] != $new['remarks'] ||
                    $old['harga_perolehan'] != $new['harga_perolehan'] ||
                    $old['bunga'] != $new['bunga']
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function updatedBiayaInterestHargaPerolehan($value, $propertyName)
    {
        $parts = explode('.', $propertyName);
        $index = $parts[0];

        $numericValue = $this->parseFormattedNumber($value);

        if ($numericValue) {
            $this->biayaInterest[$index]['harga_perolehan'] = $numericValue;
            $this->biayaInterest[$index]['harga_perolehan_display'] = $this->formatNumber($numericValue);
        } else {
            $this->biayaInterest[$index]['harga_perolehan'] = 0;
            $this->biayaInterest[$index]['harga_perolehan_display'] = '';
        }
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function getStatusOptions()
    {
        return Soil::getStatusOptions();
    }
}
