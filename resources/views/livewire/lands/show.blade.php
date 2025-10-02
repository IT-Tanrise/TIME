{{-- resources/views/livewire/lands/show.blade.php --}}
@php
    $land = App\Models\Land::with(['projects', 'soils.biayaTambahanSoils.description', 'soils.businessUnit'])->findOrFail($landId);
@endphp

<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Land Details</h2>
            <div class="flex space-x-2">
                <button wire:click="showEditForm({{ $land->id }})" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </button>
                <button wire:click="backToIndex" 
                        class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        {{-- Basic Information --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-8">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Location</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $land->lokasi_lahan }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Total Area (m²)</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $land->formatted_total_soil_area }}</p>
                    @if($land->total_soil_area == 0)
                        <p class="mt-1 text-sm text-red-600">No soils</p>
                    @endif
                </div>                

                <div>
                    <label class="block text-sm font-medium text-gray-500">Year Acquired</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $land->tahun_perolehan }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">City/Regency</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $land->kota_kabupaten ?? '-' }}</p>
                </div>

                @if($land->link_google_maps)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Google Maps</label>
                        <a href="{{ $land->link_google_maps }}" 
                        target="_blank"
                        class="mt-1 text-sm text-blue-600 hover:text-blue-800 underline">
                            View on Maps
                        </a>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($land->status === 'Available') bg-green-100 text-green-800
                        @elseif($land->status === 'Reserved') bg-yellow-100 text-yellow-800
                        @elseif($land->status === 'Sold') bg-red-100 text-red-800
                        @elseif($land->status === 'Development') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $land->status }}
                    </span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Acquisition Value</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $land->formatted_nilai_perolehan }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Price/m² (average)(acq value)</label>
                    @if($land->total_soil_area > 0)
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $land->formatted_average_price_per_m2 }}/m²
                        </p>
                    @else
                        <p class="mt-1 text-sm text-gray-900">-</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Nominal Standart</label>
                    @php
                        // Calculate total additional costs standart for all soils in this land
                        $totalAdditionalCostsStd = $land->soils->sum(function($soil) {
                            return $soil->biayaTambahanSoils
                                ->where('cost_type', 'standard') // Note: it's 'standard' not 'standart'
                                ->sum('harga');
                        });
                    @endphp
                    <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($totalAdditionalCostsStd, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nominal Non Standart</label>
                    @php
                        // Calculate total additional costs standart for all soils in this land
                        $totalAdditionalCostsNonStd = $land->soils->sum(function($soil) {
                            return $soil->biayaTambahanSoils
                                ->where('cost_type', 'non standard') // Note: it's 'standard' not 'standart'
                                ->sum('harga');
                        });
                    @endphp
                    <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($totalAdditionalCostsNonStd, 0, ',', '.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">NJOP</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $land->formatted_njop ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Estimated Market Price</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $land->formatted_est_harga_pasar ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Address --}}
        @if($land->alamat)
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-500">Address</label>
            <p class="mt-1 text-sm text-gray-900">{{ $land->alamat }}</p>
        </div>
        @endif

        {{-- Description --}}
        @if($land->keterangan)
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-500">Description</label>
            <p class="mt-1 text-sm text-gray-900">{{ $land->keterangan }}</p>
        </div>
        @endif

        {{-- Related Projects --}}
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Related Projects ({{ $land->projects->count() }})</h3>
            </div>

            @if($land->projects->count() > 0)
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Update</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Land Acquisition Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($land->projects as $project)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $project->nama_project }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $project->formatted_tgl_awal ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $project->formatted_tgl_update ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ $project->land_acquisition_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        {{ $project->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-sm">No projects associated with this land.</p>
            @endif
        </div>

        {{-- Related Soil Records --}}
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Related Soil Records ({{ $land->soils->count() }})</h3>
            </div>

            @if($land->soils->count() > 0)
                @php
                    $totalSoilPrice = 0;
                    $totalBiayaTambahan = 0;
                    $grandTotal = 0;
                @endphp

                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Business Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seller</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Soil Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Additional Costs</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Cost</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ownership Proof</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PPJB Number</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($land->soils as $soil)
                                    @php
                                        $soilBiayaTambahan = $soil->total_biaya_tambahan;
                                        $soilTotalCost = $soil->total_biaya_keseluruhan;
                                        
                                        $totalSoilPrice += $soil->harga;
                                        $totalBiayaTambahan += $soilBiayaTambahan;
                                        $grandTotal += $soilTotalCost;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($soil->businessUnit)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                    {{ $soil->businessUnit->code }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-medium">{{ $soil->nama_penjual }}</div>
                                            @if($soil->atas_nama && $soil->atas_nama !== $soil->nama_penjual)
                                                <div class="text-xs text-gray-500">On behalf of: {{ $soil->atas_nama }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $soil->letak_tanah }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $soil->formatted_luas }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ $soil->formatted_harga }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            @if($soilBiayaTambahan > 0)
                                                <span class="text-gray-900">{{ $soil->formatted_total_biaya_tambahan }}</span>
                                                @if($soil->biayaTambahanSoils->count() > 0)
                                                    <button 
                                                        type="button"
                                                        class="ml-1 text-blue-600 hover:text-blue-800"
                                                        onclick="document.getElementById('biaya-modal-{{ $soil->id }}').classList.toggle('hidden')"
                                                        title="View additional costs breakdown">
                                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            @else
                                                <span class="text-gray-400">Rp 0</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                            {{ $soil->formatted_total_biaya_keseluruhan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ $soil->bukti_kepemilikan }}</div>
                                            @if($soil->bukti_kepemilikan_details)
                                                <div class="text-xs text-gray-500">{{ $soil->bukti_kepemilikan_details }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $soil->nomor_ppjb }}
                                        </td>
                                    </tr>
                                    
                                    {{-- Hidden Modal for Additional Costs Breakdown --}}
                                    @if($soil->biayaTambahanSoils->count() > 0)
                                    <tr id="biaya-modal-{{ $soil->id }}" class="hidden">
                                        <td colspan="9" class="px-6 py-4 bg-blue-50">
                                            <div class="text-sm">
                                                <div class="flex justify-between items-center mb-2">
                                                    <h4 class="font-semibold text-gray-900">Additional Costs Breakdown for {{ $soil->nama_penjual }}</h4>
                                                    <button 
                                                        onclick="document.getElementById('biaya-modal-{{ $soil->id }}').classList.add('hidden')"
                                                        class="text-gray-500 hover:text-gray-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @foreach($soil->biayaTambahanSoils as $biaya)
                                                            <tr>
                                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                                    {{ $biaya->description->description ?? 'N/A' }}
                                                                </td>
                                                                <td class="px-4 py-2 text-sm">
                                                                    <span class="px-2 py-1 text-xs rounded-full {{ $biaya->cost_type === 'standard' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                                        {{ ucfirst(str_replace('_', ' ', $biaya->cost_type)) }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                                    {{ $biaya->date_cost ? $biaya->date_cost->format('d/m/Y') : '-' }}
                                                                </td>
                                                                <td class="px-4 py-2 text-sm text-gray-900 text-right">
                                                                    {{ $biaya->formatted_harga }}
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot class="bg-gray-50">
                                                            <tr>
                                                                <td colspan="3" class="px-4 py-2 text-sm font-semibold text-gray-900 text-right">
                                                                    Total Additional Costs:
                                                                </td>
                                                                <td class="px-4 py-2 text-sm font-semibold text-gray-900 text-right">
                                                                    {{ $soil->formatted_total_biaya_tambahan }}
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100 font-semibold">
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-sm text-gray-900 text-right">
                                        Grand Total:
                                    </td>
                                    <td class="px-3 py-6 text-sm text-gray-900 text-right">
                                        Rp {{ number_format($totalSoilPrice, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-6 text-sm text-gray-900 text-right">
                                        Rp {{ number_format($totalBiayaTambahan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-6 text-sm text-gray-900 text-right">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-sm">No soil records associated with this land.</p>
            @endif
        </div>

        {{-- Timestamps --}}
        <div class="border-t border-gray-200 pt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                <div>
                    <span class="font-medium">Created:</span> {{ $land->created_at->format('d/m/Y H:i') }}
                </div>
                <div>
                    <span class="font-medium">Last Updated:</span> {{ $land->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>
                                            