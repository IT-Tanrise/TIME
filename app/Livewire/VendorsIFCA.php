<?php

namespace App\Livewire;

use App\Models\ApCreditor;
use App\Models\ApCreditorType;
use Livewire\Component;
use Livewire\WithPagination;

class VendorsIFCA extends Component
{
    use WithPagination;

    public $view = 'index'; // 'index' or 'show'
    public $vendorId = null;
    
    public $search = '';
    public $filterType = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'view' => ['except' => 'index'],
        'vendorId' => ['except' => null],
    ];

    public function mount()
    {
        // Check if we have view and id in query string
        if (request()->has('view') && request()->get('view') === 'show') {
            $this->view = 'show';
            $this->vendorId = request()->get('id');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->resetPage();
    }

    public function showDetail($vendorId)
    {
        $this->view = 'show';
        $this->vendorId = $vendorId;
    }

    public function backToIndex()
    {
        $this->view = 'index';
        $this->vendorId = null;
    }

    public function render()
    {
        if ($this->view === 'show' && $this->vendorId) {
            return view('livewire.vendors.show', [
                'vendorId' => $this->vendorId
            ]);
        }

        $query = ApCreditor::with(['creditorType', 'contracts.project.entity'])
            ->withCount('contracts');

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('contact_person', 'like', "%{$this->search}%")
                  ->orWhere('creditor_acct', 'like', "%{$this->search}%");
            });
        }

        // Apply type filter
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        $vendors = $query->orderBy('name')->paginate(15);

        // Get filter options
        $types = ApCreditorType::orderBy('descs')->get();

        return view('livewire.vendors.index', [
            'vendors' => $vendors,
            'types' => $types
        ]);
    }
}