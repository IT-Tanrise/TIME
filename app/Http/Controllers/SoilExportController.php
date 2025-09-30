<?php

namespace App\Http\Controllers;

use App\Models\Soil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SoilExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        // Validate parameters
        $request->validate([
            'export_type' => 'required|in:current,all,date_range',
            'date_from' => 'required_if:export_type,date_range|nullable|date',
            'date_to' => 'required_if:export_type,date_range|nullable|date|after_or_equal:date_from',
            'search' => 'nullable|string',
            'business_unit_id' => 'nullable|integer',
            'land_id' => 'nullable|integer',
        ]);

        // Build query
        $query = Soil::with(['land', 'businessUnit', 'biayaTambahanSoils.description', 'createdBy', 'updatedBy']);

        // Apply filters based on export type
        switch ($request->export_type) {
            case 'current':
                if ($request->search) {
                    $query->where(function($q) use ($request) {
                        $q->where('nama_penjual', 'like', '%' . $request->search . '%')
                          ->orWhere('letak_tanah', 'like', '%' . $request->search . '%')
                          ->orWhere('nomor_ppjb', 'like', '%' . $request->search . '%');
                    });
                }
                if ($request->business_unit_id) {
                    $query->where('business_unit_id', $request->business_unit_id);
                }
                if ($request->land_id) {
                    $query->where('land_id', $request->land_id);
                }
                break;

            case 'date_range':
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
                if ($request->business_unit_id) {
                    $query->where('business_unit_id', $request->business_unit_id);
                }
                break;

            case 'all':
                if ($request->business_unit_id) {
                    $query->where('business_unit_id', $request->business_unit_id);
                }
                break;
        }

        $soils = $query->orderBy('created_at', 'desc')->get();

        if ($soils->isEmpty()) {
            return response()->json(['error' => 'No data found for the selected criteria.'], 400);
        }

        // Generate filename
        $filename = 'soils-export-' . now()->format('Y-m-d-H-i-s') . '(GMT +7).csv';
        if ($request->export_type === 'current') {
            $filename = 'soils-filtered-export-' . now()->setTimezone('Asia/Jakarta')->format('Y-m-d-H-i-s') . '(GMT +7).csv';
        } elseif ($request->export_type === 'date_range') {
            $filename = 'soils-' . $request->date_from . '-to-' . $request->date_to . '-' . now()->setTimezone('Asia/Jakarta')->format('Y-m-d-H-i-s') . '(GMT +7).csv';
        }

        // Create CSV content
        $headers = [
            'No.',
            'Business Unit',
            'BU Code',
            'Soil Location',
            'Seller Name',
            'Seller Address',
            'PPJB/AJB Number',
            'PPJB/AJB Date',
            'Soil Location Detail',
            'Area (m²)',
            'Price (Rp)',
            'Price per m² (Rp)',
            'Ownership Proof',
            'Ownership Details',
            'Owner Name',
            'NOP PBB',
            'Notaris/PPAT',
            'Notes',
            'Additional Costs (Rp)',
            'Total Investment (Rp)',
            'Additional Costs Details',
            'Created At (GMT +7)',
            'Created By',
            'Updated At (GMT +7)',
            'Updated By',
        ];

        // Start output buffering
        $output = fopen('php://temp', 'r+');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fwrite($output, "\xEF\xBB\xBF");
        
        // Write headers
        fputcsv($output, $headers);

        // Write data
        $counter = 1;
        foreach ($soils as $soil) {
            // Format additional costs details
            $additionalCostsDetails = '';
            if ($soil->biayaTambahanSoils->count() > 0) {
                $details = [];
                foreach ($soil->biayaTambahanSoils as $cost) {
                    $descriptionName = $cost->description->description ?? 'N/A';
                    $formattedPrice = 'Rp ' . number_format($cost->harga, 0, ',', '.');
                    $costType = ucfirst($cost->cost_type);
                    $details[] = "{$descriptionName}: {$formattedPrice} ({$costType})";
                }
                $additionalCostsDetails = implode('; ', $details);
            }

            $row = [
                $counter++,
                $soil->businessUnit->name ?? 'N/A',
                $soil->businessUnit->code ?? 'N/A',
                $soil->land->lokasi_lahan ?? 'N/A',
                $soil->nama_penjual,
                $soil->alamat_penjual,
                $soil->nomor_ppjb,
                $soil->tanggal_ppjb ? $soil->tanggal_ppjb->format('d/m/Y') : '',
                $soil->letak_tanah,
                $soil->luas,
                $soil->harga,
                $soil->harga_per_meter,
                $soil->bukti_kepemilikan,
                $soil->bukti_kepemilikan_details ?? '',
                $soil->atas_nama,
                $soil->nop_pbb ?? '',
                $soil->nama_notaris_ppat ?? '',
                $soil->keterangan,
                $soil->total_biaya_tambahan,
                $soil->total_biaya_keseluruhan,
                $additionalCostsDetails,
                $soil->created_at ? $soil->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '',
                $soil->createdBy->name ?? 'System',
                $soil->updated_at ? $soil->updated_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '',
                $soil->updatedBy->name ?? 'System',
            ];

            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}