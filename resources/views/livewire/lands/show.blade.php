{{-- resources/views/livewire/lands/show.blade.php --}}
@php
    $land = App\Models\Land::with(['projects', 'soils.biayaTambahanSoils.description', 'soils.businessUnit'])->findOrFail($landId);
@endphp

<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <button wire:click="backToIndex" 
                        class="inline-flex items-center px-2 py-1 bg-gray-500 border border-transparent rounded text-xs text-white font-medium hover:bg-gray-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                <h2 class="text-xl font-semibold text-gray-800">Land Details</h2>
                
                @php
                    $pendingCount = App\Models\LandApproval::where('land_id', $land->id)->where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <div class="flex items-center px-1.5 py-0.5 bg-yellow-100 border border-yellow-200 rounded-full">
                        <div class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1 animate-pulse"></div>
                        <span class="text-xs font-medium text-yellow-800">{{ $pendingCount }}</span>
                    </div>
                @endif
            </div>
            <div class="flex items-center space-x-2">
                {{-- History Button --}}
                <a href="{{ route('lands.history', ['landId' => $land->id]) }}" 
                class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded-md hover:bg-purple-100">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    History
                </a>
                
                {{-- Edit Button --}}
                @can('lands.edit')
                    <button wire:click="showEditForm({{ $land->id }})" 
                            class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </button>
                @endcan
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
                    <label class="block text-sm font-medium text-gray-500">Business Unit</label>
                    @if($land->businessUnit)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                            {{ $land->businessUnit->code }} - {{ $land->businessUnit->name }}
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
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
                    <label class="block text-sm font-medium text-gray-500">Total Soil Price</label>
                    @if($land->total_soil_price > 0)
                         <p class="mt-1 text-sm text-gray-900">{{ $land->formatted_total_soil_price }}</p>
                    @else
                        <p class="mt-1 text-sm text-gray-900">No soils</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Price/m² (average)</label>
                    @if($land->total_soil_area > 0 && $land->total_soil_price > 0)
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $land->formatted_average_price_per_m2 }}/m²
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Calculated from total soil price ÷ total soil area
                        </p>
                    @else
                        <p class="mt-1 text-sm text-gray-900">-</p>
                        <p class="mt-1 text-xs text-red-600">
                            @if($land->soils_count == 0)
                                No soil records available
                            @else
                                No price data available
                            @endif
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Nominal Standart</label>
                    @php
                        // Calculate total additional costs standart for all soils in this land
                        $totalAdditionalCostsStd = $land->soils->sum(function($soil) {
                            return $soil->biayaTambahanSoils
                                ->where('cost_type', 'standard')
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
                    <label class="block text-sm font-medium text-gray-500">Total Interest</label>
                    @php
                        // Calculate total interest costs for all soils in this land
                        $totalInterestCosts = $land->soils->sum(function($soil) {
                            return $soil->biayaTambahanInterestSoils->sum('bunga_calculation');
                        });
                    @endphp
                    <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($totalInterestCosts, 0, ',', '.') }}</p>
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
                    $totalInterest = 0;
                    $grandTotal = 0;
                @endphp

                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-8"></th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Seller & Location</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Area</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Costs</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-16">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($land->soils as $soil)
                                    @php
                                        $soilBiayaTambahan = $soil->total_biaya_tambahan;
                                        $soilInterest = $soil->total_biaya_interest;
                                        $soilTotalCost = $soil->harga + $soilBiayaTambahan + $soilInterest;
                                        
                                        $totalSoilPrice += $soil->harga;
                                        $totalBiayaTambahan += $soilBiayaTambahan;
                                        $totalInterest += $soilInterest;
                                        $grandTotal += $soilTotalCost;
                                    @endphp
                                    
                                    <!-- Main Compact Row -->
                                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="document.getElementById('details-{{ $soil->id }}').classList.toggle('hidden')">
                                        <!-- Expand/Collapse Icon -->
                                        <td class="px-3 py-2 text-center">
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" id="icon-{{ $soil->id }}" 
                                                onclick="this.classList.toggle('rotate-90')" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </td>
                                        
                                        <!-- Seller & Location Combined -->
                                        <td class="px-3 py-2 text-xs">
                                            <div class="font-semibold text-gray-900">{{ $soil->nama_penjual }}</div>
                                            <div class="text-gray-600">{{ $soil->letak_tanah }}</div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs {{ $soil->status_badge_color }}">
                                                    {{ $soil->formatted_status }}
                                                </span>
                                                <span class="text-gray-500">{{ $soil->bukti_kepemilikan }}</span>
                                            </div>
                                        </td>
                                        
                                        <!-- Area -->
                                        <td class="px-3 py-2 text-xs text-right text-gray-900 whitespace-nowrap">
                                            {{ number_format($soil->luas, 0, ',', '.') }} m²
                                        </td>
                                        
                                        <!-- Soil Price -->
                                        <td class="px-3 py-2 text-xs text-right text-gray-900 whitespace-nowrap font-medium">
                                            {{ $soil->formatted_harga }}
                                        </td>
                                        
                                        <!-- Combined Costs (Additional + Interest) -->
                                        <td class="px-3 py-2 text-xs text-right whitespace-nowrap">
                                            @php $totalCosts = $soilBiayaTambahan + $soilInterest; @endphp
                                            @if($totalCosts > 0)
                                                <div class="text-gray-900 font-medium">Rp {{ number_format($totalCosts, 0, ',', '.') }}</div>
                                                <div class="text-gray-500 text-xs">
                                                    @if($soilBiayaTambahan > 0)
                                                        <span title="Additional Costs">+ {{ number_format($soilBiayaTambahan, 0, ',', '.') }}</span>
                                                    @endif
                                                    @if($soilInterest > 0)
                                                        @if($soilBiayaTambahan > 0) | @endif
                                                        <span title="Interest" class="text-purple-600">⚡ {{ number_format($soilInterest, 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Total -->
                                        <td class="px-3 py-2 text-xs text-right font-bold text-gray-900 whitespace-nowrap">
                                            Rp {{ number_format($soilTotalCost, 0, ',', '.') }}
                                        </td>
                                        
                                        <!-- Actions -->
                                        <td class="px-3 py-2 text-center whitespace-nowrap" onclick="event.stopPropagation()">
                                            <a href="{{ route('soils', ['view' => 'show', 'id' => $soil->id]) }}" 
                                            class="inline-flex items-center justify-center w-7 h-7 text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                            title="View details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- Expandable Details Row (Hidden by default) -->
                                    <tr id="details-{{ $soil->id }}" class="hidden bg-gray-50">
                                        <td colspan="7" class="px-3 py-3">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                                <!-- Column 1: Basic Info -->
                                                <div class="space-y-2">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">Owner:</span>
                                                        <span class="text-gray-900 font-medium">{{ $soil->atas_nama }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">PPJB No:</span>
                                                        <span class="text-gray-900">{{ $soil->nomor_ppjb }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">PPJB Date:</span>
                                                        <span class="text-gray-900">{{ $soil->tanggal_ppjb->format('d/m/Y') }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">Price/m²:</span>
                                                        <span class="text-gray-900 font-medium">{{ $soil->formatted_harga_per_meter }}</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Column 2: Additional Costs -->
                                                <div>
                                                    <div class="font-semibold text-gray-700 mb-2 flex justify-between items-center">
                                                        <span>Additional Costs</span>
                                                        @if($soil->biayaTambahanSoils->count() > 0)
                                                            <button type="button"
                                                                    class="text-blue-600 hover:text-blue-800"
                                                                    onclick="document.getElementById('biaya-modal-{{ $soil->id }}').classList.toggle('hidden')">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @if($soil->biayaTambahanSoils->count() > 0)
                                                        <div class="space-y-1">
                                                            @foreach($soil->biayaTambahanSoils->take(3) as $biaya)
                                                                <div class="flex justify-between text-xs">
                                                                    <span class="text-gray-600 truncate mr-2">{{ $biaya->description->description ?? '-' }}</span>
                                                                    <span class="text-gray-900 whitespace-nowrap">Rp {{ number_format($biaya->harga, 0, ',', '.') }}</span>
                                                                </div>
                                                            @endforeach
                                                            @if($soil->biayaTambahanSoils->count() > 3)
                                                                <div class="text-blue-600 text-xs">+ {{ $soil->biayaTambahanSoils->count() - 3 }} more...</div>
                                                            @endif
                                                            <div class="flex justify-between font-semibold pt-1 border-t border-gray-300">
                                                                <span class="text-gray-700">Total:</span>
                                                                <span class="text-gray-900">{{ $soil->formatted_total_biaya_tambahan }}</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <p class="text-gray-500">No additional costs</p>
                                                    @endif
                                                </div>
                                                
                                                <!-- Column 3: Interest -->
                                                <div>
                                                    <div class="font-semibold text-gray-700 mb-2 flex justify-between items-center">
                                                        <span>Interest Costs</span>
                                                        @if($soil->biayaTambahanInterestSoils->count() > 0)
                                                            <button type="button"
                                                                    class="text-purple-600 hover:text-purple-800"
                                                                    onclick="document.getElementById('interest-modal-{{ $soil->id }}').classList.toggle('hidden')">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @if($soil->biayaTambahanInterestSoils->count() > 0)
                                                        <div class="space-y-1">
                                                            @foreach($soil->biayaTambahanInterestSoils->take(3) as $interest)
                                                                <div class="text-xs">
                                                                    <div class="flex justify-between">
                                                                        <span class="text-gray-600">{{ $interest->start_date->format('d/m/y') }} - {{ $interest->end_date->format('d/m/y') }}</span>
                                                                        <span class="text-purple-600 font-medium">{{ number_format($interest->bunga, 1) }}%</span>
                                                                    </div>
                                                                    <div class="text-gray-900 text-right">Rp {{ number_format($interest->bunga_calculation, 0, ',', '.') }}</div>
                                                                </div>
                                                            @endforeach
                                                            @if($soil->biayaTambahanInterestSoils->count() > 0)
															<tr id="interest-modal-{{ $soil->id }}" class="hidden">
																<td colspan="12" class="px-6 py-4 bg-purple-50">
																	<div class="text-sm">
																		<div class="flex justify-between items-center mb-2">
																			<h4 class="font-semibold text-gray-900">Interest Calculation for {{ $soil->nama_penjual }}</h4>
																			<button 
																				onclick="document.getElementById('interest-modal-{{ $soil->id }}').classList.add('hidden')"
																				class="text-gray-500 hover:text-gray-700">
																				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
																				</svg>
																			</button>
																		</div>
																		<div class="overflow-x-auto">
																			<table class="min-w-full divide-y divide-gray-200">
																				<thead class="bg-purple-100">
																					<tr>
																						<th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Period</th>
																						<th class="px-4 py-2 text-center text-xs font-medium text-gray-700">Days</th>
																						<th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Remarks</th>
																						<th class="px-4 py-2 text-right text-xs font-medium text-gray-700">H. Perolehan</th>
																						<th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Before</th>
																						<th class="px-4 py-2 text-center text-xs font-medium text-gray-700">Rate %</th>
																						<th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Interest</th>
																						<th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Value</th>
																					</tr>
																				</thead>
																				<tbody class="bg-white divide-y divide-gray-200">
																					@foreach($soil->biayaTambahanInterestSoils as $interest)
																					<tr>
																						<td class="px-4 py-2 text-xs text-gray-900">
																							{{ $interest->start_date->format('d/m/Y') }} - {{ $interest->end_date->format('d/m/Y') }}
																						</td>
																						<td class="px-4 py-2 text-center">
																							<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
																								{{ $interest->hari }}
																							</span>
																						</td>
																						<td class="px-4 py-2 text-xs text-gray-900">
																							{{ $interest->remarks }}
																						</td>
																						<td class="px-4 py-2 text-xs text-gray-900 text-right">
																							{{ $interest->formatted_harga_perolehan }}
																						</td>
																						<td class="px-4 py-2 text-xs text-blue-700 text-right">
																							{{ $interest->formatted_nilai_tanah_sebelum }}
																						</td>
																						<td class="px-4 py-2 text-center">
																							<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
																								{{ number_format($interest->bunga, 2) }}%
																							</span>
																						</td>
																						<td class="px-4 py-2 text-xs text-orange-700 text-right font-semibold">
																							{{ $interest->formatted_bunga_calculation }}
																						</td>
																						<td class="px-4 py-2 text-xs text-green-700 text-right font-bold">
																							{{ $interest->formatted_nilai_tanah }}
																						</td>
																					</tr>
																					@endforeach
																				</tbody>
																				<tfoot class="bg-purple-50">
																					<tr>
																						<td colspan="6" class="px-4 py-2 text-xs font-semibold text-gray-900 text-right">
																							Total Interest:
																						</td>
																						<td class="px-4 py-2 text-xs font-semibold text-gray-900 text-right">
																							{{ $soil->formatted_total_biaya_interest }}
																						</td>
																						<td class="px-4 py-2 text-xs font-semibold text-gray-900 text-right">
																							{{ $soil->formatted_nilai_tanah_akhir }}
																						</td>
																					</tr>
																				</tfoot>
																			</table>
																		</div>
																	</div>
																</td>
															</tr>
															@endif
                                                        </div>
                                                    @else
                                                        <p class="text-gray-500">No interest costs</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Keep your modals but they open from the expandable section -->
                                    @if($soil->biayaTambahanSoils->count() > 0)
                                    <tr id="biaya-modal-{{ $soil->id }}" class="hidden">
                                        <td colspan="7" class="px-6 py-4 bg-blue-50">
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
                                    
                                    @if($soil->biayaTambahanInterestSoils->count() > 0)
                                    <tr id="interest-modal-{{ $soil->id }}" class="hidden">
                                        <td colspan="7" class="px-6 py-4 bg-purple-50">
                                            <!-- Your existing modal content -->
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>

                            <tfoot class="bg-gray-100 font-semibold">
                                <tr>
                                    <td colspan="3" class="px-3 py-3 text-xs text-gray-900 text-right">
                                        Grand Total ({{ $land->soils->count() }} records):
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-900 text-right">
                                        Rp {{ number_format($totalSoilPrice, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-900 text-right">
                                        Rp {{ number_format($totalBiayaTambahan + $totalInterest, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-900 text-right">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-sm">No soil records associated with this land.</p>
            @endif
        </div>

        {{-- Land Certificates Section --}}
        @php
            $landCertificates = \App\Models\LandCertificate::where('land_id', $landId)
                                    ->with('soils')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        @endphp

        

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