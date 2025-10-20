<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class VendorsIFCA extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $filterEntity = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterEntity' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterEntity()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->filterEntity = '';
        $this->resetPage();
    }

    public function render()
    {
        try {
            $query = DB::connection('sqlsrv')->table('mgr.ap_creditor as ap')
                ->select([
                    'ap.creditor_acct',
                    'ap.type',
                    'ty.descs as type_descs',
                    'ent.entity_cd',
                    'ent.entity_name',
                    'ap.name',
                    DB::raw('ISNULL(pl.contract_no, \'\') as contract_no'),
                    'pro.descs as project_descs',
                    'pl.award_dt',
                    'pl.start_dt',
                    'pl.end_dt',
                    DB::raw('ISNULL(po.order_no, \'\') as order_no'),
                    'po.remarks',
                    'ap.contact_person'
                ])
                ->join('mgr.ap_creditor_type as ty', 'ty.creditor_type', '=', 'ap.type')
                ->leftJoin('mgr.pl_contract as pl', function($join) {
                    $join->on('pl.creditor_acct', '=', 'ap.creditor_acct')
                         ->where('pl.contract_no', '<>', '');
                })
                ->leftJoin('mgr.po_orderhd as po', function($join) {
                    $join->on('po.supplier_cd', '=', 'ap.creditor_acct')
                         ->where('po.order_no', '<>', '');
                })
                ->join('mgr.cf_entity as ent', function($join) {
                    $join->on(function($query) {
                        $query->on('pl.entity_cd', '=', 'ent.entity_cd')
                              ->orOn('po.entity_cd', '=', 'ent.entity_cd');
                    });
                })
                ->leftJoin('mgr.pl_project as pro', 'pl.project_no', '=', 'pro.project_no')
                ->where(function($query) {
                    $query->where('pl.contract_no', '<>', '')
                          ->orWhere('po.order_no', '<>', '');
                })
                ->whereNotNull('pl.project_no');

            // Apply search filter
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('ap.name', 'like', "%{$this->search}%")
                      ->orWhere('ap.contact_person', 'like', "%{$this->search}%")
                      ->orWhere('ent.entity_name', 'like', "%{$this->search}%")
                      ->orWhere('pro.descs', 'like', "%{$this->search}%")
                      ->orWhere('pl.contract_no', 'like', "%{$this->search}%")
                      ->orWhere('po.order_no', 'like', "%{$this->search}%");
                });
            }

            // Apply type filter
            if ($this->filterType) {
                $query->where('ap.type', $this->filterType);
            }

            // Apply entity filter
            if ($this->filterEntity) {
                $query->where('ent.entity_cd', $this->filterEntity);
            }

            $data = $query->paginate(15);

            // Get distinct values for filters
            $types = DB::connection('sqlsrv')
                ->table('mgr.ap_creditor_type')
                ->select('creditor_type', 'descs')
                ->get();

            $entities = DB::connection('sqlsrv')
                ->table('mgr.cf_entity')
                ->select('entity_cd', 'entity_name')
                ->distinct()
                ->orderBy('entity_name')
                ->get();

            return view('livewire.vendors.index', [
                'data' => $data,
                'types' => $types,
                'entities' => $entities
            ]);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading vendors: ' . $e->getMessage());
            
            return view('livewire.vendors.index', [
                'data' => collect()->paginate(15),
                'types' => collect(),
                'entities' => collect()
            ]);
        }
    }
}