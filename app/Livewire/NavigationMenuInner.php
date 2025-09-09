<?php

namespace App\Livewire;

use Livewire\Component;

class NavigationMenuInner extends Component
{
    public $active;
    public $currentBusinessUnitId = null;
    
    public function mount($businessUnitId = null)
    {
        $this->active = request()->route()->getName();
        $this->currentBusinessUnitId = $businessUnitId;
    }

    public function render()
    {
        return view('livewire.navigation-menu-inner');
    }
    
    public function getOwnershipsUrl()
    {
        // If we have a current business unit ID, use the filtered route
        if ($this->currentBusinessUnitId) {
            return route('partners.by-business-unit', $this->currentBusinessUnitId);
        }
        
        // Otherwise, use the general partners index
        return route('partners.index');
    }
    
    public function getLandsUrl()
    {
        // If we have a current business unit ID, use the filtered route
        if ($this->currentBusinessUnitId) {
            return route('lands.by-business-unit', ['businessUnit' => $this->currentBusinessUnitId]);
        }
        
        // Otherwise, use the general lands index
        return route('lands');
    }
    
    public function getSoilsUrl()
    {
        // If we have a current business unit ID, use the filtered route
        if ($this->currentBusinessUnitId) {
            return route('soils.by-business-unit', ['businessUnit' => $this->currentBusinessUnitId]);
        }
        
        // Otherwise, use the general soils index
        return route('soils');
    }
    
    public function getRentLandsUrl()
    {
        // If we have a current business unit ID, use the filtered route
        if ($this->currentBusinessUnitId) {
            return route('rents.lands.by-business-unit', ['businessUnit' => $this->currentBusinessUnitId]);
        }
        
        // Otherwise, use the general rent lands index
        return route('rents.lands');
    }
    
    public function getCurrentBusinessUnitId()
    {
        return $this->currentBusinessUnitId;
    }
    
    public function isActive($routeName)
    {
        // Check if current route name contains the given route name
        return str_contains($this->active, $routeName);
    }
}