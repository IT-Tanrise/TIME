<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RentLand;

class RentNotification extends Component
{
    public $showNotifications = false;
    public $currentBusinessUnitId = null;

    public function mount($businessUnitId = null)
    {
        $this->currentBusinessUnitId = $businessUnitId;
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
    }

    public function closeNotifications()
    {
        $this->showNotifications = false;
    }

    public function getExpiringRentals()
    {
        $query = RentLand::with(['land', 'land.businessUnit'])->expiringSoon();
        
        if ($this->currentBusinessUnitId) {
            $query->byBusinessUnit($this->currentBusinessUnitId);
        }
        
        return $query->orderBy('end_rent', 'asc')->get();
    }

    public function getExpiringCount()
    {
        return $this->getExpiringRentals()->count();
    }

    public function render()
    {
        $expiringRentals = $this->getExpiringRentals();
        $expiringCount = $this->getExpiringCount();

        return view('livewire.rent-notification', [
            'expiringRentals' => $expiringRentals,
            'expiringCount' => $expiringCount
        ]);
    }
}