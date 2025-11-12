<?php

namespace App\Livewire;

use App\Models\LandInterestRate;
use App\Models\BusinessUnit;
use App\Models\Land;
use Livewire\Component;
use Livewire\WithPagination;

class LandInterestRates extends Component
{
    use WithPagination;

    public $filterYear;
    public $filterBusinessUnit = '';
    public $filterLand = '';
    public $showForm = false;
    public $isEdit = false;
    
    // Business Unit Selection Modal
    public $showBusinessUnitSelectionModal = false;
    
    // Form fields
    public $rateId;
    public $land_id;
    public $business_unit_id;
    public $month;
    public $year;
    public $rate;
    public $notes;

    // Business Unit & Land Search
    public $businessUnitSearch = '';
    public $landSearch = '';
    public $showBusinessUnitDropdown = false;
    public $showLandDropdown = false;

    // Delete modal
    public $showDeleteModal = false;
    public $deleteId = null;

    // Add filter properties similar to Lands
    public $filterByBusinessUnit = null;
    public $businessUnit = null;

    protected function rules()
    {
        $rules = [
            'land_id' => 'required|exists:lands,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 10),
            'rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500'
        ];

        // Add unique validation when creating or editing
        if ($this->isEdit && $this->rateId) {
            $rules['land_id'] .= '|unique:land_interest_rates,land_id,' . $this->rateId . ',id,month,' . $this->month . ',year,' . $this->year;
        } else {
            $rules['land_id'] .= '|unique:land_interest_rates,land_id,NULL,id,month,' . $this->month . ',year,' . $this->year;
        }

        return $rules;
    }

    protected $messages = [
        'land_id.required' => 'Please select a land.',
        'land_id.unique' => 'An interest rate for this land, month, and year already exists.',
        'month.required' => 'Month is required.',
        'year.required' => 'Year is required.',
        'rate.required' => 'Interest rate is required.',
        'rate.numeric' => 'Interest rate must be a number.',
        'rate.min' => 'Interest rate cannot be negative.',
        'rate.max' => 'Interest rate cannot exceed 100%.',
    ];

    public function mount($businessUnit = null)
    {
        $this->filterYear = date('Y');
        
        if ($businessUnit) {
            if (is_numeric($businessUnit)) {
                $this->businessUnit = BusinessUnit::find($businessUnit);
                if ($this->businessUnit) {
                    $this->filterByBusinessUnit = $this->businessUnit->id;
                    $this->filterBusinessUnit = $this->businessUnit->id;
                }
            } elseif ($businessUnit instanceof BusinessUnit) {
                $this->filterByBusinessUnit = $businessUnit->id;
                $this->filterBusinessUnit = $businessUnit->id;
                $this->businessUnit = $businessUnit;
            }
        } else {
            // Show business unit selection modal only if not filtered
            $this->showBusinessUnitSelectionModal = true;
        }
    }

    public function selectBusinessUnitFilter($businessUnitId)
    {
        if ($businessUnitId === 'all') {
            $this->filterBusinessUnit = '';
            $this->filterByBusinessUnit = null;
            $this->businessUnit = null;
        } else {
            $this->filterBusinessUnit = $businessUnitId;
            $this->filterByBusinessUnit = $businessUnitId;
            $this->businessUnit = BusinessUnit::find($businessUnitId);
        }
        
        $this->filterLand = '';
        $this->closeBusinessUnitSelectionModal();
        $this->resetPage();
    }

    public function closeBusinessUnitSelectionModal()
    {
        $this->showBusinessUnitSelectionModal = false;
    }

    public function showBusinessUnitSelectionModalFunc()
    {
        $this->showBusinessUnitSelectionModal = true;
    }

    public function render()
    {
        $rates = LandInterestRate::with(['land', 'land.businessUnit', 'creator', 'updater'])
            ->when($this->filterYear, function($query) {
                $query->forYear($this->filterYear);
            })
            ->when($this->filterByBusinessUnit, function($query) {
                $query->whereHas('land', function($q) {
                    $q->where('business_unit_id', $this->filterByBusinessUnit);
                });
            })
            ->when($this->filterBusinessUnit, function($query) {
                $query->whereHas('land', function($q) {
                    $q->where('business_unit_id', $this->filterBusinessUnit);
                });
            })
            ->when($this->filterLand, function($query) {
                $query->where('land_id', $this->filterLand);
            })
            ->ordered()
            ->paginate(12);

        $availableYears = LandInterestRate::getAvailableYears();
        
        // Add current year if not in list
        if (!in_array(date('Y'), $availableYears)) {
            $availableYears[] = (int) date('Y');
            rsort($availableYears);
        }

        // Get business units and lands for filters
        $businessUnits = BusinessUnit::orderBy('name')->get();
        $lands = $this->filterBusinessUnit 
            ? Land::where('business_unit_id', $this->filterBusinessUnit)->orderBy('lokasi_lahan')->get()
            : collect();

        // Calculate statistics for the filtered year
        $statistics = null;
        if ($this->filterYear) {
            $yearRatesQuery = LandInterestRate::forYear($this->filterYear);
            
            if ($this->filterByBusinessUnit) {
                $yearRatesQuery->whereHas('land', function($q) {
                    $q->where('business_unit_id', $this->filterByBusinessUnit);
                });
            }
            
            if ($this->filterBusinessUnit) {
                $yearRatesQuery->whereHas('land', function($q) {
                    $q->where('business_unit_id', $this->filterBusinessUnit);
                });
            }
            
            if ($this->filterLand) {
                $yearRatesQuery->where('land_id', $this->filterLand);
            }
            
            $yearRates = $yearRatesQuery->get();
            
            if ($yearRates->count() > 0) {
                $statistics = [
                    'count' => $yearRates->count(),
                    'average' => round($yearRates->avg('rate'), 2),
                    'min' => $yearRates->min('rate'),
                    'max' => $yearRates->max('rate'),
                ];
            }
        }

        return view('livewire.land-interest-rates.index', compact('rates', 'availableYears', 'statistics', 'businessUnits', 'lands'));
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->year = $this->filterYear ?? date('Y');
        
        // Auto-fill business unit if filtered
        if ($this->filterByBusinessUnit && $this->businessUnit) {
            $this->business_unit_id = $this->filterByBusinessUnit;
            $this->businessUnitSearch = $this->businessUnit->name;
        }
        
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        $rate = LandInterestRate::with('land.businessUnit')->findOrFail($id);
        
        $this->rateId = $rate->id;
        $this->land_id = $rate->land_id;
        $this->business_unit_id = $rate->land->business_unit_id;
        $this->month = $rate->month;
        $this->year = $rate->year;
        $this->rate = $rate->rate;
        $this->notes = $rate->notes;

        // Set search displays
        if ($rate->land) {
            $this->landSearch = $rate->land->lokasi_lahan;
            if ($rate->land->businessUnit) {
                $this->businessUnitSearch = $rate->land->businessUnit->name;
            }
        }
        
        $this->showForm = true;
        $this->isEdit = true;
    }

    // Business Unit Dropdown Methods
    public function searchBusinessUnits()
    {
        $this->showBusinessUnitDropdown = true;
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
        
        // Reset land when business unit changes
        $this->land_id = '';
        $this->landSearch = '';
    }

    // Land Dropdown Methods
    public function searchLands()
    {
        if ($this->business_unit_id) {
            $this->showLandDropdown = true;
        }
    }

    public function getFilteredLands()
    {
        if (!$this->business_unit_id) {
            return collect();
        }

        return Land::where('business_unit_id', $this->business_unit_id)
            ->when($this->landSearch, function($query) {
                $query->where(function($q) {
                    $q->where('lokasi_lahan', 'like', '%' . $this->landSearch . '%')
                      ->orWhere('kota_kabupaten', 'like', '%' . $this->landSearch . '%');
                });
            })
            ->orderBy('lokasi_lahan')
            ->limit(20)
            ->get();
    }

    public function selectLand($id, $name)
    {
        $this->land_id = $id;
        $this->landSearch = $name;
        $this->showLandDropdown = false;
    }

    public function closeDropdowns()
    {
        $this->showBusinessUnitDropdown = false;
        $this->showLandDropdown = false;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'land_id' => $this->land_id,
            'month' => $this->month,
            'year' => $this->year,
            'rate' => $this->rate,
            'notes' => $this->notes,
        ];

        if ($this->isEdit && $this->rateId) {
            $rate = LandInterestRate::findOrFail($this->rateId);
            $rate->update($data);
            session()->flash('message', 'Interest rate updated successfully.');
        } else {
            LandInterestRate::create($data);
            session()->flash('message', 'Interest rate created successfully.');
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
        if ($this->deleteId) {
            LandInterestRate::findOrFail($this->deleteId)->delete();
            session()->flash('message', 'Interest rate deleted successfully.');
        }
        
        $this->hideDeleteModal();
    }

    public function hideDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function backToIndex()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'rateId', 'land_id', 'business_unit_id', 'month', 'year', 'rate', 'notes',
            'businessUnitSearch', 'landSearch', 'showBusinessUnitDropdown', 'showLandDropdown'
        ]);
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->filterYear = date('Y');
        $this->filterBusinessUnit = '';
        $this->filterLand = '';
        $this->showBusinessUnitSelectionModalFunc();
        $this->resetPage();
    }

    public function updatingFilterYear()
    {
        $this->resetPage();
    }

    public function updatingFilterBusinessUnit()
    {
        $this->filterLand = '';
        $this->resetPage();
    }

    public function updatingFilterLand()
    {
        $this->resetPage();
    }

    public function clearBusinessUnitFilter()
    {
        $this->filterByBusinessUnit = null;
        $this->filterBusinessUnit = '';
        $this->businessUnit = null;
        return redirect()->route('land-interest-rates');
    }

    public function getCurrentBusinessUnitName()
    {
        return $this->businessUnit ? $this->businessUnit->name : null;
    }

    public function isFiltered()
    {
        return !is_null($this->filterByBusinessUnit);
    }

    public function getMonthOptions()
    {
        return [
            1 => 'January', 2 => 'February', 3 => 'March',
            4 => 'April', 5 => 'May', 6 => 'June',
            7 => 'July', 8 => 'August', 9 => 'September',
            10 => 'October', 11 => 'November', 12 => 'December'
        ];
    }
}