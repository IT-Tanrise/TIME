<?php
// app/Livewire/Partners.php

namespace App\Livewire;

use App\Models\Partner;
use App\Models\BusinessUnit;
use Livewire\Component;
use Livewire\WithPagination;

class Partners extends Component
{
    use WithPagination;

    public $partner;
    public $partnerId;
    public $name = '';
    public $business_unit_id = '';
    public $percentage = '';
    
    public $showForm = false;
    public $showDetailForm = false;
    public $isEdit = false;
    public $search = '';
    
    // Add this property to handle filtering by business unit
    public $filterByBusinessUnit = null;
    public $businessUnit = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'business_unit_id' => 'required|exists:business_units,id',
        'percentage' => 'required|numeric|min:0|max:100'
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
                }
            } elseif ($businessUnit instanceof BusinessUnit) {
                // If it's already a BusinessUnit model
                $this->filterByBusinessUnit = $businessUnit->id;
                $this->businessUnit = $businessUnit;
                $this->business_unit_id = $businessUnit->id;
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

        $businessUnits = BusinessUnit::all();

        return view('livewire.partners.index', compact('partners', 'businessUnits'));
    }

    public function showCreateForm()
    {
        $this->resetForm();
        // Keep the business unit pre-selected if filtering
        if ($this->filterByBusinessUnit) {
            $this->business_unit_id = $this->filterByBusinessUnit;
        }
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        $this->partner = Partner::findOrFail($id);
        $this->partnerId = $this->partner->id;
        $this->name = $this->partner->name;
        $this->business_unit_id = $this->partner->business_unit_id;
        $this->percentage = $this->partner->percentage;
        
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
            'percentage' => $this->percentage
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
        $this->reset(['partner', 'partnerId', 'name', 'percentage']);
        // Don't reset business_unit_id if we're filtering by business unit
        if (!$this->filterByBusinessUnit) {
            $this->business_unit_id = '';
        }
        $this->resetValidation();
    }

    // Add method to clear business unit filter
    public function clearBusinessUnitFilter()
    {
        $this->filterByBusinessUnit = null;
        $this->businessUnit = null;
        $this->business_unit_id = '';
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
}