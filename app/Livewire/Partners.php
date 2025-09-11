<?php
// app/Livewire/Partners.php

namespace App\Livewire;

use App\Models\Partner;
use App\Models\BusinessUnit;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\On;

class Partners extends Component
{
    use WithPagination;

    public $partner;
    public $partnerId;
    public $name = '';
    public $business_unit_id = '';
    public $percentage = '';
    public $lembar_saham = '';
    public $lembar_saham_display = ''; // Add display property for formatted value
    
    public $showForm = false;
    public $showDetailForm = false;
    public $isEdit = false;
    public $search = '';
    
    // Add this property to handle filtering by business unit
    public $filterByBusinessUnit = null;
    public $businessUnit = null;

    // Business unit search properties
    public $businessUnitSearch = '';
    public $showBusinessUnitDropdown = false;
    public $allowBusinessUnitChange = false;

    // Add these properties to your Partners class
    public $partnerNameSearch = '';
    public $showPartnerNameDropdown = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'business_unit_id' => 'required|exists:business_units,id',
        'percentage' => 'required|numeric|min:0|max:100',
        'lembar_saham' => 'nullable|integer|min:0'
    ];

    // Add mount method to handle business unit parameter
    public function mount($businessUnit = null)
    {
        if ($businessUnit) {
            // Handle different input types
            if (is_numeric($businessUnit)) {
                // If it's a numeric ID, find the business unit
                $this->businessUnit = BusinessUnit::find($businessUnit);
                if ($this->businessUnit) {
                    $this->filterByBusinessUnit = $this->businessUnit->id;
                    $this->business_unit_id = $this->businessUnit->id;
                    $this->businessUnitSearch = $this->businessUnit->name;
                }
            } elseif ($businessUnit instanceof BusinessUnit) {
                // If it's already a BusinessUnit model
                $this->filterByBusinessUnit = $businessUnit->id;
                $this->businessUnit = $businessUnit;
                $this->business_unit_id = $businessUnit->id;
                $this->businessUnitSearch = $businessUnit->name;
            }
        }
    }

    public function render()
    {
        $partners = Partner::with('businessUnit')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterByBusinessUnit, function($query) {
                $query->where('business_unit_id', $this->filterByBusinessUnit);
            })
            ->paginate(10);

        return view('livewire.partners.index', compact('partners'));
    }

    public function showCreateForm()
    {
        $this->resetForm();
        // Keep the business unit pre-selected if filtering
        if ($this->filterByBusinessUnit) {
            $this->business_unit_id = $this->filterByBusinessUnit;
            $this->businessUnitSearch = $this->businessUnit->name;
        }
        // Initialize display values
        $this->lembar_saham_display = '';
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        $this->partner = Partner::findOrFail($id);
        $this->partnerId = $this->partner->id;
        $this->name = $this->partner->name;
        $this->partnerNameSearch = $this->partner->name; // Add this line
        $this->business_unit_id = $this->partner->business_unit_id;
        $this->percentage = $this->partner->percentage;
        $this->lembar_saham = $this->partner->lembar_saham;
        $this->businessUnitSearch = $this->partner->businessUnit->name ?? '';
        
        // Format the display value for editing
        $this->lembar_saham_display = $this->partner->lembar_saham ? 
            number_format($this->partner->lembar_saham, 0, ',', '.') : '';
        
        $this->showForm = true;
        $this->isEdit = true;
    }

    public function showDetail($id)
    {
        $this->partnerId = $id;
        $this->showDetailForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'business_unit_id' => $this->business_unit_id,
            'percentage' => $this->percentage,
            'lembar_saham' => $this->lembar_saham ?: null
        ];

        if ($this->isEdit) {
            $this->partner->update($data);
            session()->flash('message', 'Partner updated successfully.');
        } else {
            Partner::create($data);
            session()->flash('message', 'Partner created successfully.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Partner::findOrFail($id)->delete();
        session()->flash('message', 'Partner deleted successfully.');
    }

    public function backToIndex()
    {
        $this->showForm = false;
        $this->showDetailForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['partner', 'partnerId', 'name', 'percentage', 'lembar_saham', 'lembar_saham_display', 'businessUnitSearch', 'showBusinessUnitDropdown', 'partnerNameSearch', 'showPartnerNameDropdown']);
        // Don't reset business_unit_id if we're filtering by business unit
        if (!$this->filterByBusinessUnit) {
            $this->business_unit_id = '';
        } else {
            $this->businessUnitSearch = $this->businessUnit->name;
        }
        $this->resetValidation();
    }

    // Add method to clear business unit filter
    public function clearBusinessUnitFilter()
    {
        $this->filterByBusinessUnit = null;
        $this->businessUnit = null;
        $this->business_unit_id = '';
        $this->businessUnitSearch = '';
        return redirect()->route('partners.index');
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

    // Business unit search methods
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
        $this->businessUnitSearch = '';
        $this->business_unit_id = '';
    }

    public function lockBusinessUnit()
    {
        $this->allowBusinessUnitChange = false;
        if ($this->filterByBusinessUnit) {
            $this->business_unit_id = $this->filterByBusinessUnit;
            $this->businessUnitSearch = $this->businessUnit->name;
        }
    }

    #[On('close-all-dropdowns')]
    // Close dropdowns when clicking outside
    public function closeDropdowns()
    {
        $this->showBusinessUnitDropdown = false;
        $this->showPartnerNameDropdown = false;
    }

    // Add method to handle lembar_saham formatting - similar to soils component
    public function updatedLembarSahamDisplay($value)
    {
        // Remove all non-numeric characters
        $numericValue = preg_replace('/[^\d]/', '', $value);
        
        if ($numericValue) {
            $this->lembar_saham = (int) $numericValue;
            // Don't set lembar_saham_display here to avoid conflicts
        } else {
            $this->lembar_saham = '';
        }
    }

    // Helper method to get formatted display value for lembar_saham
    public function getFormattedLembarSaham()
    {
        if (empty($this->lembar_saham)) {
            return '';
        }
        
        return number_format($this->lembar_saham, 0, ',', '.');
    }

    // Add these methods to your Partners class
    public function updatedPartnerNameSearch($value)
    {
        // Update the actual name field
        $this->name = $value;
    }

    public function searchPartnerNames()
    {
        $this->showPartnerNameDropdown = true;
        $this->showBusinessUnitDropdown = false; // Close other dropdowns
    }

    public function selectPartnerName($partnerName)
    {
        $this->partnerNameSearch = $partnerName;
        $this->name = $partnerName;
        $this->showPartnerNameDropdown = false;
    }

    public function getFilteredPartnerNames()
    {
        $search = $this->partnerNameSearch ?? '';
        
        $query = Partner::select('name')
            ->distinct()
            ->whereNotNull('name')
            ->where('name', '!=', '');
        
        // Only filter if there's search text, otherwise show all
        if (!empty(trim($search))) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        return $query->orderBy('name')->limit(20)->get();
    }
}