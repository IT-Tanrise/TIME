{{-- resources/views/livewire/soils/index.blade.php --}}
<div class="min-h-screen bg-gray-50">
    @if($showForm)
        @include('livewire.soils.form')
    @elseif($showAdditionalCostsForm)
        @include('livewire.soils.costs-form')
    @elseif($showDetailForm)
        @include('livewire.soils.show')
    @else
        <!-- Compact Header -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <button onclick="history.back()" 
                                class="inline-flex items-center px-2 py-2 bg-gray-600 border border-transparent rounded-lg font-medium text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back
                        </button>
                        <h1 class="text-xl font-semibold text-gray-900">
                            Soils
                            @if($businessUnit)
                                <span class="text-base text-blue-600 font-normal">- <a href="{{ route('business-units', ['view' => 'show', 'id' => $businessUnit->id]) }}" class="hover:text-blue-800">{{ $businessUnit->name }} ({{ $businessUnit->code }})</a></span>
                            @endif
                        </h1>
                    </div>
                    <div class="flex space-x-2">
                        @if($businessUnit)
                            <button wire:click="clearBusinessUnitFilter" 
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Show All Soils
                            </button>
                        @endif
                        <button wire:click="showExportModalView" 
                                class="inline-flex items-center px-3 py-2 text-xs font-medium text-green-600 bg-white border border-green-300 rounded-md hover:bg-green-50">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Excel
                        </button>
                        @can('soils.edit')
                        <button wire:click="showCreateForm" 
                                class="inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add New
                        </button>
                        @endcan
                    </div>
                </div>
                <!-- Compact Filters Section -->
                 @if(!$this->isFiltered())
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-5 gap-3">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" 
                                wire:model.live="search" 
                                id="search"
                                placeholder="Search by seller, location..."
                                class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Business Unit Filter -->
                        <div class="relative">
                            <label for="filterBusinessUnit" class="block text-sm font-medium text-gray-700 mb-1">Business Unit</label>
                            <div class="relative">
                                <input type="text" 
                                    wire:model.live.debounce.300ms="filterBusinessUnitSearch"
                                    wire:focus="openBusinessUnitFilterDropdown"
                                    id="filterBusinessUnit"
                                    placeholder="Search business units..."
                                    class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-8"
                                    autocomplete="off">
                                
                                @if($filterBusinessUnit)
                                    <button type="button" 
                                            wire:click="clearBusinessUnitFilterSearch"
                                            class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                                
                                @if($showBusinessUnitFilterDropdown)
                                    <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
                                        wire:click.stop>
                                        @php
                                            $filteredBusinessUnits = $this->getFilteredBusinessUnitsForFilter();
                                        @endphp
                                        
                                        @if(empty($filterBusinessUnitSearch ?? '') && $filteredBusinessUnits->count() > 0)
                                            <div class="px-3 py-2 text-xs text-blue-600 bg-blue-50 border-b border-blue-100 sticky top-0 z-10">
                                                Showing available business units - start typing to filter
                                            </div>
                                        @endif
                                        
                                        <div class="max-h-48 overflow-y-auto">
                                            @forelse($filteredBusinessUnits as $unit)
                                                <button type="button"
                                                        wire:click.stop="selectBusinessUnitFilter({{ $unit->id }}, '{{ addslashes($unit->name) }}')"
                                                        class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-b-0 focus:bg-blue-100 focus:outline-none
                                                            @if($filterBusinessUnit == $unit->id) bg-blue-50 text-blue-900 font-medium @endif">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <div class="font-medium">{{ $unit->name }}</div>
                                                            <div class="text-xs text-gray-500">{{ $unit->code }}</div>
                                                        </div>
                                                        @if($filterBusinessUnit == $unit->id)
                                                            <span class="text-blue-600">✓</span>
                                                        @endif
                                                    </div>
                                                </button>
                                            @empty
                                                <div class="px-3 py-2 text-sm text-gray-500">
                                                    No business units found
                                                    @if(!empty($filterBusinessUnitSearch))
                                                        matching "{{ $filterBusinessUnitSearch }}"
                                                    @endif
                                                </div>
                                            @endforelse
                                        </div>
                                        
                                        @if($filteredBusinessUnits->count() >= 20)
                                            <div class="px-3 py-2 text-xs text-gray-500 bg-gray-50 border-t border-gray-100 sticky bottom-0">
                                                Showing first 20 results - type to search for more
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Land Filter -->
                        <div class="relative">
                            <label for="filterLand" class="block text-sm font-medium text-gray-700 mb-1">Land</label>
                            <div class="relative">
                                <input type="text" 
                                    wire:model.live.debounce.300ms="filterLandSearch"
                                    wire:focus="openLandFilterDropdown"
                                    id="filterLand"
                                    placeholder="{{ ($filterBusinessUnit || $filterByBusinessUnit) ? 'Search lands...' : 'Select business unit first...' }}"
                                    class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-8"
                                    autocomplete="off"
                                    @if(!$filterBusinessUnit && !$filterByBusinessUnit) disabled @endif>
                                
                                @if($filterLand)
                                    <button type="button" 
                                            wire:click="clearLandFilter"
                                            class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                                
                                @if(!$filterBusinessUnit && !$filterByBusinessUnit)
                                    <p class="mt-1 text-xs text-amber-600">
                                        Select a business unit first
                                    </p>
                                @endif
                                
                                @if($showLandFilterDropdown && ($filterBusinessUnit || $filterByBusinessUnit))
                                    <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-auto"
                                        wire:click.stop>
                                        @php
                                            $filteredLands = $this->getFilteredLandsForFilter();
                                        @endphp
                                        
                                        @if(empty($filterLandSearch ?? '') && $filteredLands->count() > 0)
                                            <div class="px-3 py-2 text-xs text-blue-600 bg-blue-50 border-b border-blue-100 sticky top-0 z-10">
                                                Showing lands from selected business unit - start typing to filter
                                            </div>
                                        @endif
                                        
                                        @if($filteredLands->count() > 0)
                                            @foreach($filteredLands as $land)
                                                <button type="button"
                                                        wire:click.stop="selectLandFilter({{ $land->id }}, '{{ addslashes($land->lokasi_lahan) }}')"
                                                        class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-b-0 @if($filterLand == $land->id) bg-blue-50 text-blue-900 font-medium @endif">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <div class="font-medium">{{ $land->lokasi_lahan }}</div>
                                                            @if($land->kota_kabupaten)
                                                                <div class="text-xs text-gray-500">{{ $land->kota_kabupaten }}</div>
                                                            @endif
                                                        </div>
                                                        @if($filterLand == $land->id)
                                                            <span class="text-blue-600">✓</span>
                                                        @endif
                                                    </div>
                                                </button>
                                            @endforeach
                                        @else
                                            <div class="px-3 py-2 text-sm text-gray-500">
                                                No lands found
                                                @if(!empty($filterLandSearch))
                                                    matching "{{ $filterLandSearch }}"
                                                @endif
                                                for this business unit
                                            </div>
                                        @endif
                                        
                                        @if($filteredLands->count() >= 20)
                                            <div class="px-3 py-2 text-xs text-gray-500 bg-gray-50 border-t border-gray-100 sticky bottom-0">
                                                Showing first 20 results - type to search for more
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div></div>

                        <!-- Reset Filters -->
                        <div class="flex items-center">
                            <button wire:click="resetFilters" 
                                    class="w-full px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                                Reset Filters
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex justify-between items-center">
                        <input wire:model.live="search" 
                            type="text" 
                            placeholder="Search soils by no. PPJB/ AJB, seller, location..." 
                            class="flex-1 px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 mr-4">
                        @if($businessUnit)
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                Filtered by: {{ $businessUnit->name }}
                            </span>
                        @endif
                    </div>
                @endif

                <!-- Alert Messages -->
                @if (session()->has('message'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session()->has('warning'))
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-4 rounded">
                        {{ session('warning') }}
                    </div>
                @endif
            </div>
            <div class="p-6">
                <!-- Compact Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Land & Location</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BU</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ownership</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PPJB/AJB</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area & Price</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investment</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($soils as $soil)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $soil->land->lokasi_lahan ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $soil->letak_tanah }}</div>
                                    </td>
                                    <td class="px-2 py-2">
                                        <div class="text-xs text-gray-600 font-medium">{{ $soil->businessUnit->code ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $soil->nama_penjual }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-32">{{ $soil->alamat_penjual }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $soil->bukti_kepemilikan }}</div>
                                        <div class="text-xs text-gray-500">{{ $soil->bukti_kepemilikan_details }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $soil->nomor_ppjb }}</div>
                                        <div class="text-xs text-gray-500">{{ $soil->tanggal_ppjb->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($soil->luas, 0, ',', '.') }} m²</div>
                                        <div class="text-xs text-gray-500">{{ $soil->formatted_harga_per_meter }}/m²</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-green-700">{{ $soil->formatted_total_biaya_keseluruhan }}</div>
                                        @if($soil->biayaTambahanSoils->count() > 0)
                                            <div class="text-xs text-orange-600">
                                                +{{ $soil->formatted_total_biaya_tambahan }}
                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center space-x-1">
                                                <span>{{ $soil->biayaTambahanSoils->count() }} items</span>
                                                @php
                                                    $standardCosts = $soil->biayaTambahanSoils->where('cost_type', 'standard')->count();
                                                    $nonStandardCosts = $soil->biayaTambahanSoils->where('cost_type', 'non_standard')->count();
                                                @endphp
                                                @if($standardCosts > 0)
                                                    <span class="inline-flex px-1 text-xs rounded bg-green-100 text-green-800">{{ $standardCosts }}S</span>
                                                @endif
                                                @if($nonStandardCosts > 0)
                                                    <span class="inline-flex px-1 text-xs rounded bg-orange-100 text-orange-800">{{ $nonStandardCosts }}NS</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-400">No additional</div>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2">
                                        <div class="flex items-center space-x-1">
                                            <!-- View Button -->
                                            <button wire:click="showDetail({{ $soil->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                                    title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Edit Dropdown -->
                                            @canany(['soils.edit', 'soil-costs.edit'])
                                            <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                                <button type="button" 
                                                        @click="open = !open"
                                                        class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50"
                                                        title="Edit Options">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <div x-show="open" 
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                    class="absolute right-0 mt-1 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                                    @click="open = false">
                                                    <div class="py-1">
                                                        @can('soils.edit')
                                                        <button wire:click="showEditForm({{ $soil->id }}, 'details')" 
                                                                class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            Edit Details
                                                        </button>
                                                        @endcan
                                                        @canany(['soils.edit', 'soil-costs.edit'])
                                                        <button wire:click="showEditForm({{ $soil->id }}, 'costs')" 
                                                                class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            Manage Costs
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                            @endcan
                                            
                                            <!-- Delete Button - Updated to use modal -->
                                            @can('soils.delete')
                                            <button wire:click="showDeleteModalView({{ $soil->id }})" 
                                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                                                    title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-8 text-center text-gray-500">
                                        @if($businessUnit)
                                            No soil records found for {{ $businessUnit->name }}.
                                        @elseif($search)
                                            No soil records found matching "{{ $search }}".
                                        @else
                                            No soil records found.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Compact Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $soils->links() }}
                </div>
            </div>
        </div>

        <!-- Export Modal -->
        @if($showExportModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
             x-data="{ show: @entangle('showExportModal') }" 
             x-show="show" 
             @click.self="$wire.hideExportModalView()">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white" 
                 @click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Export Soils to Excel</h3>
                        <button wire:click="hideExportModalView" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Export Options -->
                    <div class="space-y-4">
                        <!-- Export Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Export Type</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="exportType" value="current" class="mr-2">
                                    <span class="text-sm text-gray-700">Current View 
                                        <span class="text-xs text-gray-500">({{ $soils->total() }} records with current filters)</span>
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="exportType" value="all" class="mr-2">
                                    <span class="text-sm text-gray-700">All Records
                                        @if($businessUnit)
                                            <span class="text-xs text-gray-500">(from {{ $businessUnit->name }})</span>
                                        @endif
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="exportType" value="date_range" class="mr-2">
                                    <span class="text-sm text-gray-700">Date Range</span>
                                </label>
                            </div>
                            @error('exportType')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Range Inputs -->
                        @if($exportType === 'date_range')
                        <div class="space-y-3 border-t pt-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Date (PPJB Date)</label>
                                <input type="date" wire:model.live="exportDateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('exportDateFrom')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">To Date (PPJB Date)</label>
                                <input type="date" wire:model.live="exportDateTo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('exportDateTo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @if($exportDateFrom && $exportDateTo)
                                <div class="bg-blue-50 border border-blue-200 rounded p-2 text-sm text-blue-700">
                                    Records count: {{ $this->getExportSummary() }}
                                </div>
                            @endif
                        </div>
                        @endif

                        <!-- Export Preview Info -->
                        @if($exportType)
                        <div class="border-t pt-3">
                            <div class="bg-gray-50 rounded p-3 text-sm">
                                <div class="font-medium text-gray-700 mb-1">Export Preview:</div>
                                @if($exportType === 'current')
                                    <div class="text-gray-600">{{ $soils->total() }} records from current filtered view</div>
                                    @if($search || $filterBusinessUnit || $filterByBusinessUnit || $filterLand)
                                        <div class="text-xs text-blue-600 mt-1">
                                            Applied filters:
                                            @if($search) Search: "{{ $search }}" @endif
                                            @if($filterBusinessUnit || $filterByBusinessUnit) Business Unit filter @endif
                                            @if($filterLand) Land filter @endif
                                        </div>
                                    @endif
                                @elseif($exportType === 'all')
                                    <div class="text-gray-600">All soil records
                                        @if($businessUnit) from {{ $businessUnit->name }} @endif
                                    </div>
                                @elseif($exportType === 'date_range' && $exportDateFrom && $exportDateTo)
                                    <div class="text-gray-600">Records from {{ $exportDateFrom }} to {{ $exportDateTo }}</div>
                                    <div class="text-xs text-gray-500">Estimated: {{ $this->getExportSummary() }} records</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <button wire:click="hideExportModalView" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button wire:click="exportToExcel" 
                                wire:loading.attr="disabled"
                                wire:target="exportToExcel"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="exportToExcel">Export Excel</span>
                            <span wire:loading wire:target="exportToExcel" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Exporting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Delete Confirmation Modal -->
        @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Confirm Delete Soil Record
                        </h3>
                        <button wire:click="hideDeleteModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-start space-x-3 mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-3">
                                    Are you sure you want to delete this soil record? This action cannot be undone and will also delete any associated additional costs.
                                </p>
                            </div>
                        </div>
                        
                        @if(auth()->user()->can('soil-data.approval'))
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                                <div class="flex items-start space-x-2">
                                    <svg class="h-5 w-5 text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm text-yellow-800">
                                        <strong>Direct Deletion:</strong> You have approval permissions. This record will be deleted immediately after confirmation.
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4">
                                <div class="flex items-start space-x-2">
                                    <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm text-blue-800">
                                        <strong>Approval Required:</strong> Your deletion request will be submitted for approval and is subject to review.
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <div>
                            <label for="deleteReason" class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for deletion <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                wire:model="deleteReason" 
                                id="deleteReason"
                                rows="4" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Please provide a detailed reason for deleting this record (minimum 10 characters)..."
                            ></textarea>
                            @error('deleteReason')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button 
                            wire:click="hideDeleteModalView"
                            type="button" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="deleteWithReason"
                            wire:loading.attr="disabled"
                            wire:target="deleteWithReason"
                            type="button" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="deleteWithReason">Delete Record</span>
                            <span wire:loading wire:target="deleteWithReason" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Deleting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        
    @endif
</div>

<!-- Enhanced JavaScript for Dropdown Behavior -->
<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('submit-export-form', (event) => {
        const params = event.params;
        
        // Create a hidden form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("soils.export") }}';
        form.style.display = 'none';
        
        // Add parameters as hidden inputs
        Object.keys(params).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = params[key];
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });
});
document.addEventListener('alpine:init', () => {
    Alpine.data('numberFormat', () => ({
        formatNumber(event) {
            let value = event.target.value.replace(/[^\d]/g, '');
            if (value) {
                event.target.value = new Intl.NumberFormat('id-ID').format(value);
            }
        }
    }));
});

// Enhanced click outside handler - exclude edit dropdown menus
document.addEventListener('click', function(event) {
    // Don't close dropdown search boxes if clicking on edit dropdowns
    const editDropdown = event.target.closest('[x-data*="open"]');
    if (editDropdown) {
        return; // Let Alpine.js handle edit dropdowns
    }

    // Only close search dropdowns if clicking outside of any dropdown or input
    const dropdowns = document.querySelectorAll('[wire\\:click\\.stop]');
    const inputs = document.querySelectorAll('input[wire\\:focus], textarea[wire\\:focus]');
    const buttons = document.querySelectorAll('button[wire\\:click*="search"]');
    
    let clickedInside = false;
    
    // Check if click was inside any dropdown, input, or search button
    [...dropdowns, ...inputs, ...buttons].forEach(element => {
        if (element && element.contains && element.contains(event.target)) {
            clickedInside = true;
        }
    });
    
    // If clicked outside, close all search dropdowns
    if (!clickedInside && window.Livewire) {
        window.Livewire.dispatch('closeDropdowns');
    }
});

// Ensure proper scrolling behavior for dynamically created dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Prevent form submission on search button clicks
    document.addEventListener('click', function(event) {
        if (event.target.matches('button[wire\\:click*="search"]') || 
            event.target.closest('button[wire\\:click*="search"]')) {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    // Ensure proper scrolling behavior for dynamically created dropdowns
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1 && node.style && 
                    node.style.maxHeight && node.style.overflowY === 'auto') {
                    // Force scrolling properties
                    node.style.overflowY = 'auto';
                    node.style.overflowX = 'hidden';
                    // Add smooth scrolling
                    node.style.scrollBehavior = 'smooth';
                }
            });
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Add keyboard navigation for search dropdowns only
document.addEventListener('keydown', function(event) {
    // Only handle search dropdowns, not edit dropdowns
    const activeDropdown = document.querySelector('[wire\\:click\\.stop][style*="max-height"][style*="overflow-y: auto"]:not([style*="display: none"])');
    
    if (activeDropdown && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
        event.preventDefault();
        
        const items = activeDropdown.querySelectorAll('button[wire\\:click*="select"]');
        const currentFocus = document.activeElement;
        const currentIndex = Array.from(items).indexOf(currentFocus);
        
        let nextIndex;
        if (event.key === 'ArrowDown') {
            nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
        } else {
            nextIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
        }
        
        if (items[nextIndex]) {
            items[nextIndex].focus();
            items[nextIndex].scrollIntoView({ block: 'nearest' });
        }
    }
    
    // Handle Enter key to select focused item
    if (event.key === 'Enter' && document.activeElement.matches('button[wire\\:click*="select"]')) {
        event.preventDefault();
        document.activeElement.click();
    }
    
    // Handle Escape key to close search dropdowns and modals
    if (event.key === 'Escape' && window.Livewire) {
        window.Livewire.dispatch('closeDropdowns');
        // Close modals if open
        if (document.querySelector('[wire\\:click="hideExportModalView"]')) {
            window.Livewire.dispatch('hideExportModalView');
        }
        if (document.querySelector('[wire\\:click="hideDeleteModalView"]')) {
            window.Livewire.dispatch('hideDeleteModalView');
        }
    }
});
</script>
                                        
                                    