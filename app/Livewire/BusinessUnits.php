<?php

namespace App\Livewire;

use App\Models\BusinessUnit;
use Livewire\Component;
use Livewire\WithPagination;

class BusinessUnits extends Component
{
    use WithPagination;

    public $view = 'index'; // index, create, edit, show
    public $businessUnitId;
    public $code = '';
    public $name = '';
    public $parent_id = null;
    public $search = '';
    public $unit = null;
    public $parentFilter = ''; // New property for parent filtering
    public $filteredUnits = null; // To store filtered units

    protected $rules = [
        'code' => 'required|string|max:255|unique:business_units,code',
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:business_units,id'
    ];

    protected $queryString = ['view', 'businessUnitId', 'parentFilter'];

    public function mount($view = 'index', $id = null)
    {
        $this->view = $view;
        
        if ($id) {
            $this->businessUnitId = $id;
            
            if ($view === 'edit') {
                $this->loadBusinessUnitForEdit($id);
            } elseif ($view === 'show') {
                $this->loadBusinessUnitForShow($id);
            }
        }
    }

    public function render()
    {
        $data = [];

        switch ($this->view) {
            case 'index':
                $data = $this->renderIndex();
                return view('livewire.business-unit.index', $data);
                
            case 'create':
                $data = $this->renderForm();
                return view('livewire.business-unit.form', $data);
                
            case 'edit':
                $data = $this->renderForm();
                return view('livewire.business-unit.form', $data);
                
            case 'show':
                $data = $this->renderShow();
                return view('livewire.business-unit.show', $data);
                
            default:
                return $this->redirectToIndex();
        }
    }

    private function renderIndex()
    {
        $businessUnits = BusinessUnit::with(['parent', 'children'])
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->parentFilter, function($query) {
                if ($this->parentFilter === 'root') {
                    $query->whereNull('parent_id');
                } else {
                    // Get all descendants of the selected parent
                    $parent = BusinessUnit::find($this->parentFilter);
                    if ($parent) {
                        $descendantIds = $this->getAllDescendantIds($parent);
                        $descendantIds[] = $parent->id; // Include the parent itself
                        $query->whereIn('id', $descendantIds);
                    }
                }
            })
            ->orderBy('name')
            ->paginate(12);

        // Get parent options for the filter dropdown
        $parentOptions = BusinessUnit::with('parent')
            ->whereNull('parent_id')
            ->orWhereHas('children')
            ->orderBy('name')
            ->get();

        return compact('businessUnits', 'parentOptions');
    }

    private function renderForm()
    {
        $parentOptions = BusinessUnit::whereNull('parent_id')
            ->orWhere('id', '!=', $this->businessUnitId)
            ->orderBy('name')
            ->get();

        $isEdit = $this->view === 'edit';

        return compact('parentOptions', 'isEdit');
    }

    private function renderShow()
    {
        if (!$this->unit) {
            $this->loadBusinessUnitForShow($this->businessUnitId);
        }

        // Get all root units for the complete hierarchy
        $allRootUnits = BusinessUnit::with(['allChildren'])->whereNull('parent_id')->get();
        
        // Get filtered units based on parent filter
        $filteredUnits = $this->getFilteredUnits();
        
        // Get all parent options for the filter dropdown
        $parentOptions = BusinessUnit::with('parent')
            ->whereNull('parent_id')
            ->orWhereHas('children')
            ->orderBy('name')
            ->get();

        return [
            'unit' => $this->unit,
            'allRootUnits' => $allRootUnits,
            'filteredUnits' => $filteredUnits,
            'parentOptions' => $parentOptions
        ];
    }

    private function getFilteredUnits()
    {
        if (empty($this->parentFilter)) {
            return BusinessUnit::with(['allChildren'])->whereNull('parent_id')->get();
        }
        
        if ($this->parentFilter === 'root') {
            return BusinessUnit::with(['allChildren'])->whereNull('parent_id')->get();
        }
        
        // Get the selected parent and its children
        $parent = BusinessUnit::with(['allChildren'])->find($this->parentFilter);
        if ($parent) {
            return collect([$parent]);
        }
        
        return collect();
    }

    private function loadBusinessUnitForEdit($id)
    {
        $businessUnit = BusinessUnit::findOrFail($id);
        $this->businessUnitId = $businessUnit->id;
        $this->code = $businessUnit->code;
        $this->name = $businessUnit->name;
        $this->parent_id = $businessUnit->parent_id;
    }

    private function loadBusinessUnitForShow($id)
    {
        $this->unit = BusinessUnit::with(['parent', 'allChildren'])->findOrFail($id);
    }

    // New method to handle parent filter changes
    public function updatedParentFilter()
    {
        $this->filteredUnits = $this->getFilteredUnits();
    }

    // New method to clear the parent filter
    public function clearParentFilter()
    {
        $this->parentFilter = '';
        $this->filteredUnits = $this->getFilteredUnits();
    }

    // Navigation methods
    public function showIndex()
    {
        return redirect()->route('business-units', ['view' => 'index']);
    }

    public function showCreate()
    {
        return redirect()->route('business-units', ['view' => 'create']);
    }

    public function showEdit($id)
    {
        return redirect()->route('business-units', ['view' => 'edit', 'id' => $id]);
    }

    public function showDetail($id)
    {
        return redirect()->route('business-units', ['view' => 'show', 'id' => $id]);
    }

    private function redirectToIndex()
    {
        return redirect()->route('business-units', ['view' => 'index']);
    }

    // CRUD Operations
    public function save()
    {
        $rules = $this->rules;
        
        if ($this->businessUnitId) {
            $rules['code'] = 'required|string|max:255|unique:business_units,code,' . $this->businessUnitId;
        }

        $this->validate($rules);

        // Prevent circular reference
        if ($this->parent_id && $this->businessUnitId) {
            $parent = BusinessUnit::find($this->parent_id);
            if ($parent && $this->isDescendant($parent, $this->businessUnitId)) {
                $this->addError('parent_id', 'Cannot set a descendant as parent.');
                return;
            }
        }

        if ($this->businessUnitId) {
            $businessUnit = BusinessUnit::findOrFail($this->businessUnitId);
            $businessUnit->update([
                'code' => $this->code,
                'name' => $this->name,
                'parent_id' => $this->parent_id ?: null,
            ]);
            session()->flash('message', 'Business Unit updated successfully.');
        } else {
            BusinessUnit::create([
                'code' => $this->code,
                'name' => $this->name,
                'parent_id' => $this->parent_id ?: null,
            ]);
            session()->flash('message', 'Business Unit created successfully.');
        }

        return $this->redirectToIndex();
    }

    public function delete($id)
    {
        $businessUnit = BusinessUnit::findOrFail($id);
        
        if ($businessUnit->children()->count() > 0) {
            session()->flash('error', 'Cannot delete business unit with children.');
            return;
        }

        $businessUnit->delete();
        session()->flash('message', 'Business Unit deleted successfully.');
        
        if ($this->view === 'show' && $this->businessUnitId == $id) {
            return $this->redirectToIndex();
        }
    }

    private function isDescendant($parent, $businessUnitId)
    {
        if ($parent->parent_id == $businessUnitId) {
            return true;
        }
        
        if ($parent->parent) {
            return $this->isDescendant($parent->parent, $businessUnitId);
        }
        
        return false;
    }

    // Helper method to get all descendant IDs
    private function getAllDescendantIds($parent)
    {
        $ids = [];
        foreach ($parent->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllDescendantIds($child));
        }
        return $ids;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingParentFilter()
    {
        $this->resetPage();
    }
}