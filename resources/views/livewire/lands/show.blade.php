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
                        // Calculate total interest from first cost date until current month
                        $firstCost = \App\Models\BiayaTambahanSoil::whereHas('soil', function($query) use ($land) {
                                $query->where('land_id', $land->id);
                            })
                            ->orderBy('date_cost', 'asc')
                            ->first();
                        
                        $totalLandInterest = 0;
                        
                        if ($firstCost) {
                            $startDate = \Carbon\Carbon::parse($firstCost->date_cost);
                            $startMonth = $startDate->month;
                            $startYear = $startDate->year;
                            
                            $currentMonth = date('n');
                            $currentYear = date('Y');
                            
                            // Loop through all months from start to current
                            $currentDate = \Carbon\Carbon::create($startYear, $startMonth, 1);
                            $endDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
                            
                            while ($currentDate <= $endDate) {
                                $interestData = $land->calculateInterestForMonth($currentDate->month, $currentDate->year);
                                $totalLandInterest += $interestData['total_interest'];
                                $currentDate->addMonth();
                            }
                        }
                    @endphp
                    <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($totalLandInterest, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        @if($firstCost)
                            From {{ \Carbon\Carbon::parse($firstCost->date_cost)->format('M Y') }} to {{ date('M Y') }}
                        @else
                            No costs recorded
                        @endif
                    </p>
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
            <div class="flex justify-between items-center mb-4 cursor-pointer hover:bg-gray-50 p-3 rounded-lg transition-colors"
                wire:click="$toggle('showProjects')">
                <div class="flex items-center space-x-3">
                    <h3 class="text-lg font-medium text-gray-900">Related Projects ({{ $land->projects->count() }})</h3>
                </div>
                <svg class="w-5 h-5 text-gray-600 transition-transform duration-200 {{ $showProjects ? 'rotate-180' : '' }}" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <div class="overflow-hidden transition-all duration-300 {{ $showProjects ? 'max-h-screen opacity-100' : 'max-h-0 opacity-0' }}">
                @if($land->projects->count() > 0)
                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-8"></th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Seller & Location</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Area</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Add. Costs</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-16">Action</th>
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
        </div>

        {{-- Related Soil Records --}}
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4 cursor-pointer hover:bg-gray-50 p-3 rounded-lg transition-colors"
                wire:click="$toggle('showSoils')">
                <div class="flex items-center space-x-3">
                    <h3 class="text-lg font-medium text-gray-900">Related Soil Records ({{ $land->soils->count() }})</h3>
                </div>
                <svg class="w-5 h-5 text-gray-600 transition-transform duration-200 {{ $showSoils ? 'rotate-180' : '' }}" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <div class="overflow-hidden transition-all duration-300 {{ $showSoils ? 'max-h-[9999px] opacity-100' : 'max-h-0 opacity-0' }}">
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
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Add. Costs</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-16">Action</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($land->soils as $soil)
                                        @php
                                            $soilBiayaTambahan = $soil->total_biaya_tambahan;
                                            $soilTotalCost = $soil->harga + $soilBiayaTambahan;
                                            
                                            $totalSoilPrice += $soil->harga;
                                            $totalBiayaTambahan += $soilBiayaTambahan;
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
                                            
                                            <!-- Additional Costs Only -->
                                            <td class="px-3 py-2 text-xs text-right whitespace-nowrap">
                                                @if($soilBiayaTambahan > 0)
                                                    <div class="text-gray-900 font-medium">Rp {{ number_format($soilBiayaTambahan, 0, ',', '.') }}</div>
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
                                        
                                        <!-- Expandable Details Row -->
                                        <tr id="details-{{ $soil->id }}" class="hidden bg-gray-50">
                                            <td colspan="7" class="px-3 py-3">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
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
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        {{-- Biaya Modal --}}
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
                                            Rp {{ number_format($totalBiayaTambahan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 text-xs text-gray-900 text-right">
                                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                    {{-- Total Land Interest Row --}}
                                    <tr class="bg-purple-50">
                                        <td colspan="5" class="px-3 py-3 text-xs text-purple-900 text-right font-semibold">
                                            Total Land Interest:
                                            @php
                                                $firstCostTable = \App\Models\BiayaTambahanSoil::whereHas('soil', function($query) use ($land) {
                                                        $query->where('land_id', $land->id);
                                                    })
                                                    ->orderBy('date_cost', 'asc')
                                                    ->first();
                                            @endphp
                                            @if($firstCostTable)
                                                <span class="text-xs font-normal text-purple-700">
                                                    ({{ \Carbon\Carbon::parse($firstCostTable->date_cost)->format('M Y') }} - {{ date('M Y') }})
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-xs text-purple-900 text-right font-semibold">
                                            @php
                                                $totalLandInterestTable = 0;
                                                
                                                if ($firstCostTable) {
                                                    $startDate = \Carbon\Carbon::parse($firstCostTable->date_cost);
                                                    $startMonth = $startDate->month;
                                                    $startYear = $startDate->year;
                                                    
                                                    $currentMonth = date('n');
                                                    $currentYear = date('Y');
                                                    
                                                    $currentDate = \Carbon\Carbon::create($startYear, $startMonth, 1);
                                                    $endDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
                                                    
                                                    while ($currentDate <= $endDate) {
                                                        $interestData = $land->calculateInterestForMonth($currentDate->month, $currentDate->year);
                                                        $totalLandInterestTable += $interestData['total_interest'];
                                                        $currentDate->addMonth();
                                                    }
                                                }
                                            @endphp
                                            Rp {{ number_format($totalLandInterestTable, 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-gray-200">
                                        <td colspan="5" class="px-3 py-3 text-xs text-gray-900 text-right font-bold">
                                            Final Total (with Interest):
                                        </td>
                                        <td class="px-3 py-3 text-xs text-gray-900 text-right font-bold">
                                            Rp {{ number_format($grandTotal + $totalLandInterestTable, 0, ',', '.') }}
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
        </div>

        {{-- Land Interest Calculation Section --}}
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4 cursor-pointer hover:bg-gray-50 p-3 rounded-lg transition-colors"
                wire:click="toggleInterestCalculation">
                <div class="flex items-center space-x-3">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Interest Cost Calculation
                    </h3>
                </div>
                <svg class="w-5 h-5 text-gray-600 transition-transform duration-200 {{ $showInterestCalculation ? 'rotate-180' : '' }}" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <div class="overflow-hidden transition-all duration-300 {{ $showInterestCalculation ? 'max-h-[9999px] opacity-100' : 'max-h-0 opacity-0' }}">
                {{-- Month/Year Filter --}}
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Year Dropdown --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select wire:model.live="interestYear" 
                                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                                @foreach($this->availableYears as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                            @if($this->firstCostInfo)
                                <p class="mt-1 text-xs text-gray-500">
                                    Available from {{ \Carbon\Carbon::create($this->firstCostInfo['date']->year, 1, 1)->format('Y') }}
                                </p>
                            @endif
                        </div>
                        
                        {{-- Month Dropdown --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                            <select wire:model.live="interestMonth" 
                                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                                @foreach($this->availableMonths as $m)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
                                @endforeach
                            </select>
                            @if($this->firstCostInfo && $interestYear == $this->firstCostInfo['date']->year)
                                <p class="mt-1 text-xs text-gray-500">
                                    Available from {{ $this->firstCostInfo['date']->format('F') }}
                                </p>
                            @elseif($interestYear == date('Y'))
                                <p class="mt-1 text-xs text-gray-500">
                                    Up to {{ \Carbon\Carbon::now()->format('F') }}
                                </p>
                            @endif
                        </div>
                        
                        {{-- Calculate Button --}}
                        <div class="flex items-end">
                            <button wire:click="updateInterestCalculation" 
                                    class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <span wire:loading.remove wire:target="updateInterestCalculation">Calculate</span>
                                <span wire:loading wire:target="updateInterestCalculation">Calculating...</span>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Info about selected period --}}
                    @if($this->firstCostInfo)
                        <div class="mt-3 pt-3 border-t border-purple-200">
                            <p class="text-xs text-purple-700">
                                <span class="font-semibold">First Cost Date:</span> {{ $this->firstCostInfo['formatted'] }}
                                <span class="mx-2">•</span>
                                <span class="font-semibold">Selected Period:</span> {{ \Carbon\Carbon::create($interestYear, $interestMonth, 1)->format('F Y') }}
                            </p>
                        </div>
                    @endif
                </div>

                @php
                    $interestData = $land->calculateInterestForMonth($interestMonth, $interestYear);
                    $calculations = $interestData['calculations'];
                @endphp

                {{-- Summary Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="text-xs font-medium text-blue-600 mb-1">Start Value</div>
                        <div class="text-lg font-bold text-blue-900">
                            Rp {{ number_format($interestData['start_value'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <div class="text-xs font-medium text-green-600 mb-1">Total Costs</div>
                        <div class="text-lg font-bold text-green-900">
                            Rp {{ number_format($interestData['total_costs'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                        <div class="text-xs font-medium text-purple-600 mb-1">Total Interest</div>
                        <div class="text-lg font-bold text-purple-900">
                            Rp {{ number_format($interestData['total_interest'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                        <div class="text-xs font-medium text-orange-600 mb-1">End Value</div>
                        <div class="text-lg font-bold text-orange-900">
                            Rp {{ number_format($interestData['end_value'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Calculation Table --}}
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-purple-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-purple-900 uppercase">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-purple-900 uppercase">Soil</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-purple-900 uppercase">Type</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-purple-900 uppercase">Period</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-purple-900 uppercase">Days</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-purple-900 uppercase">Acquisition</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-purple-900 uppercase">Before</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-purple-900 uppercase">Rate</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-purple-900 uppercase">Interest</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-purple-900 uppercase">After</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($calculations as $calc)
                                    <tr class="hover:bg-purple-50 
                                        {{ $calc['harga_perolehan'] > 0 ? 'bg-green-50' : ($calc['interest'] > 0 ? 'bg-purple-50' : '') }}">
                                        <td class="px-4 py-3 text-sm">
                                            <span class="font-medium text-gray-900">{{ $calc['description'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $calc['soil_name'] ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($calc['cost_type'])
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    {{ $calc['cost_type'] === 'standard' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $calc['cost_type'])) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-center text-gray-600">
                                            {{ \Carbon\Carbon::parse($calc['start_date'])->format('d/m/Y') }}
                                            @if($calc['start_date'] !== $calc['end_date'])
                                                - {{ \Carbon\Carbon::parse($calc['end_date'])->format('d/m/Y') }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($calc['days'] > 0)
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                    {{ $calc['days'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right">
                                            @if($calc['harga_perolehan'] > 0)
                                                <span class="font-semibold text-green-700">
                                                    Rp {{ number_format($calc['harga_perolehan'], 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-blue-700">
                                            Rp {{ number_format($calc['nilai_sebelum'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($calc['rate'] > 0)
                                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                                    {{ number_format($calc['rate'], 2) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right">
                                            @if($calc['interest'] > 0)
                                                <span class="font-semibold text-purple-700">
                                                    Rp {{ number_format($calc['interest'], 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-orange-700">
                                            Rp {{ number_format($calc['nilai_setelah'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-sm">No costs recorded for this period</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($calculations->isNotEmpty())
                            <tfoot class="bg-purple-100 font-semibold">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-sm text-gray-900 text-right">
                                        Total:
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">
                                        Rp {{ number_format($interestData['total_costs'], 0, ',', '.') }}
                                    </td>
                                    <td colspan="2" class="px-4 py-3 text-sm text-gray-900 text-right">
                                        Total Interest:
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">
                                        Rp {{ number_format($interestData['total_interest'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">
                                        Rp {{ number_format($interestData['end_value'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Info Note --}}
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-xs text-blue-800">
                            <p class="font-semibold mb-1">Calculation Notes:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Interest is calculated based on the land interest rate for the selected month</li>
                                <li>Costs from all soils in this land are included in the calculation</li>
                                <li>Interest accrues between cost dates using the formula: (Value × Rate% ÷ 365 × Days)</li>
                                <li>Green rows indicate cost additions, purple rows show interest accruals</li>
                                <li>If no interest rate is set for this month, interest calculation will be 0%</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
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