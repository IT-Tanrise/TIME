<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RentLand;
use App\Models\Land;
use App\Models\BusinessUnit;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class RentLands extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $businessUnitId = null;
    public $currentBusinessUnitId = null;
    public $showForm = false;
    public $showDetail = false;
    public $editMode = false;
    public $selectedRent = null;

    // Form properties
    public $rent = [
        'id' => null,
        'land_id' => '',
        'area_m2' => '',
        'price' => '',
        'nama_penyewa' => '',
        'alamat_penyewa' => '',
        'nomor_handphone_penyewa' => '',
        'start_rent' => '',
        'end_rent' => '',
        'reminder_period' => ''
    ];

    protected $queryString = ['search', 'statusFilter'];

    protected $rules = [
        'rent.land_id' => 'required|exists:lands,id',
        'rent.area_m2' => 'required|integer|min:1',
        'rent.price' => 'required|integer|min:0',
        'rent.nama_penyewa' => 'required|string|max:255',
        'rent.alamat_penyewa' => 'required|string|max:255',
        'rent.nomor_handphone_penyewa' => 'required|string|max:255',
        'rent.start_rent' => 'required|date',
        'rent.end_rent' => 'required|date|after:rent.start_rent',
        'rent.reminder_period' => 'nullable|in:1month,1week,3days'
    ];

    protected $messages = [
        'rent.land_id.required' => 'Please select a land.',
        'rent.land_id.exists' => 'Selected land is invalid.',
        'rent.area_m2.required' => 'Area is required.',
        'rent.area_m2.integer' => 'Area must be a number.',
        'rent.area_m2.min' => 'Area must be at least 1 m².',
        'rent.price.required' => 'Price is required.',
        'rent.price.integer' => 'Price must be a number.',
        'rent.price.min' => 'Price cannot be negative.',
        'rent.nama_penyewa.required' => 'Tenant name is required.',
        'rent.alamat_penyewa.required' => 'Tenant address is required.',
        'rent.nomor_handphone_penyewa.required' => 'Tenant phone number is required.',
        'rent.start_rent.required' => 'Start date is required.',
        'rent.start_rent.date' => 'Start date must be a valid date.',
        'rent.end_rent.required' => 'End date is required.',
        'rent.end_rent.date' => 'End date must be a valid date.',
        'rent.end_rent.after' => 'End date must be after start date.',
    ];

    public function mount($businessUnit = null)
    {
        $this->businessUnitId = $businessUnit;
        $this->currentBusinessUnitId = $businessUnit;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function showCreate()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }

    public function showEdit($id)
    {
        $rent = RentLand::with('land')->findOrFail($id);
        
        $this->rent = [
            'id' => $rent->id,
            'land_id' => $rent->land_id,
            'area_m2' => $rent->area_m2,
            'price' => $rent->price,
            'nama_penyewa' => $rent->nama_penyewa,
            'alamat_penyewa' => $rent->alamat_penyewa,
            'nomor_handphone_penyewa' => $rent->nomor_handphone_penyewa,
            'start_rent' => $rent->start_rent->format('Y-m-d'),
            'end_rent' => $rent->end_rent->format('Y-m-d'),
            'reminder_period' => $rent->reminder_period
        ];
        
        $this->showForm = true;
        $this->editMode = true;
    }

    public function showDetailView($id)
    {
        $this->selectedRent = RentLand::with(['land', 'land.businessUnits'])->findOrFail($id);
        $this->showDetail = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $rent = RentLand::findOrFail($this->rent['id']);
            $rent->update($this->rent);
            session()->flash('message', 'Land rent updated successfully!');
        } else {
            RentLand::create($this->rent);
            session()->flash('message', 'Land rent created successfully!');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $rent = RentLand::findOrFail($id);
        $rent->delete();
        
        session()->flash('message', 'Land rent deleted successfully!');
    }

    public function resetForm()
    {
        $this->rent = [
            'id' => null,
            'land_id' => '',
            'area_m2' => '',
            'price' => '',
            'nama_penyewa' => '',
            'alamat_penyewa' => '',
            'nomor_handphone_penyewa' => '',
            'start_rent' => '',
            'end_rent' => '',
            'reminder_period' => ''
        ];
        $this->resetErrorBag();
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedRent = null;
    }

    // FIXED: Get available lands based on business unit relationship through soils
    public function getAvailableLands()
    {
        $query = Land::query();
        
        if ($this->currentBusinessUnitId) {
            // Filter lands that have soils belonging to the business unit
            $query->whereHas('soils', function ($soilQuery) {
                $soilQuery->where('business_unit_id', $this->currentBusinessUnitId);
            });
        }
        
        // Use the correct column name from your Land model
        return $query->orderBy('lokasi_lahan')->get();
    }

    public function getExpiringCount()
    {
        $query = RentLand::expiringSoon();
        
        if ($this->currentBusinessUnitId) {
            $query->byBusinessUnit($this->currentBusinessUnitId);
        }
        
        return $query->count();
    }

    public function render()
    {
        $query = RentLand::with(['land', 'land.businessUnits']);

        // Apply business unit filter
        if ($this->currentBusinessUnitId) {
            $query->byBusinessUnit($this->currentBusinessUnitId);
        }

        // Apply search filter
        if ($this->search) {
            $query->where(function (Builder $q) {
                $q->where('nama_penyewa', 'like', '%' . $this->search . '%')
                  ->orWhere('nomor_handphone_penyewa', 'like', '%' . $this->search . '%')
                  ->orWhereHas('land', function (Builder $landQuery) {
                      // Use the correct column name
                      $landQuery->where('lokasi_lahan', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            switch ($this->statusFilter) {
                case 'expired':
                    $query->expired();
                    break;
                case 'expiring_1month':
                    $query->expiringSoon('1month');
                    break;
                case 'expiring_1week':
                    $query->expiringSoon('1week');
                    break;
                case 'expiring_3days':
                    $query->expiringSoon('3days');
                    break;
                case 'active':
                    $query->where('end_rent', '>=', now());
                    break;
            }
        }

        $rentLands = $query->orderBy('end_rent', 'asc')->paginate(15);
        $availableLands = $this->getAvailableLands();
        $expiringCount = $this->getExpiringCount();

        return view('livewire.rents.lands-index', [
            'rentLands' => $rentLands,
            'availableLands' => $availableLands,
            'expiringCount' => $expiringCount
        ]);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage(); // This is important to reset pagination
    }
}