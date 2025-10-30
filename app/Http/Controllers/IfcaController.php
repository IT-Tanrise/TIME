<?php

namespace App\Http\Controllers;

use App\Models\IfcaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IfcaController extends Controller
{
    //
    public function index(){
       try {
        // Test SQL Server
        $data = DB::connection('sqlsrv')->table('mgr.ap_creditor as ap')->select([
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
    ->whereNotNull('pl.project_no') // Untuk memastikan join dengan pro berhasil
    // ->get();
    ->paginate(10);
        // echo $data;
        return view('test', compact('data'));
        
        } catch (\Exception $e) {
            die("SQL Server Connection failed: " . $e->getMessage());
        }
    }
}
