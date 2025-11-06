<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PreSoilBuyCashOut;
use App\Models\PreSoilBuy;
use Illuminate\Support\Facades\Auth;

class CashOut extends Component
{
    public $filter = 'pending';
    public $showModal = false;
    public $selectedPreSoilBuy = null;
    public $preSoilBuyDetails = null;

    protected $listeners = ['refreshCashout' => '$refresh'];

    public function mount()
    {
        $this->filter = 'pending';
    }

    public function setFilter($status)
    {
        $this->filter = $status;
    }

    public function openModal($preSoilBuyId)
    {
        $this->selectedPreSoilBuy = $preSoilBuyId;
        $this->preSoilBuyDetails = PreSoilBuy::with(['createdBy', 'updatedBy'])->find($preSoilBuyId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPreSoilBuy = null;
        $this->preSoilBuyDetails = null;
    }

    public function processCashOut($cashOutId)
    {
        try {
            $cashOut = PreSoilBuyCashOut::findOrFail($cashOutId);

            if ($cashOut->status !== 'pending') {
                session()->flash('error', 'Cash out request is not pending');
                return;
            }

            $cashOut->cashOut();

            session()->flash('success', 'Cash out processed successfully!');
            $this->dispatch('refreshCashout');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process cash out: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $cashOuts = PreSoilBuyCashOut::with([
            'preSoilBuy.createdBy',
            'respondedBy',
        ])
        ->where('status', $this->filter)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('livewire.cash-out.index', [
            'cashOuts' => $cashOuts
        ]);
    }
}