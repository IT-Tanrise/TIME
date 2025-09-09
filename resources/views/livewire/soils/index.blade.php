{{-- resources/views/livewire/soils/index.blade.php --}}
<div class="container mx-auto px-4 py-6">
    @if($showForm)
        @include('livewire.soils.form')
    @elseif($showAdditionalCostsForm)
        @include('livewire.soils.costs-form')
    @elseif($showDetailForm)
        @include('livewire.soils.show')
    @else
        
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            Soils
                            @if($businessUnit)
                                <span class="text-lg text-blue-600">- <a href ="{{ route('business-units', ['view' => 'show', 'id' => $businessUnit->id]) }}">{{ $businessUnit->name }}({{ $businessUnit->code }})</a></span>
                            @endif
                        </h2>
                    </div>
                    <div class="flex space-x-3">
                        @if($businessUnit)
                            <button wire:click="clearBusinessUnitFilter" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Show All Soils
                            </button>
                        @endif
                        <button wire:click="showCreateForm" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Soil Record
                        </button>
                    </div>
                </div>

                <!-- Replace the existing filter section in index.blade.php -->
                @if(!$this->isFiltered())
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" 
                            wire:model.live="search" 
                            id="search"
                            placeholder="Search by seller, buyer, location..."
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <!-- Business Unit Filter with Dropdown Search -->
                    <div class="relative">
                        <label for="filterBusinessUnitSearch" class="block text-sm font-medium text-gray-700">Business Unit</label>
                        <div class="relative">
                            <input type="text" 
                                wire:model.live="filterBusinessUnitSearch"
                                wire:click="openBusinessUnitFilterDropdown"
                                id="filterBusinessUnitSearch"
                                placeholder="Search business units..."
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-8"
                                autocomplete="off">
                            
                            <!-- Clear button -->
                            @if($filterBusinessUnit)
                                <button type="button" 
                                        wire:click="clearBusinessUnitFilterSearch"
                                        class="absolute inset-y-0 right-0 flex items-center pr-2">
                                    <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                            
                            <!-- Dropdown -->
                            @if($showBusinessUnitFilterDropdown)
                                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                    @php
                                        $filteredBusinessUnits = $this->getFilteredBusinessUnitsForFilter();
                                    @endphp
                                    
                                    @if($filteredBusinessUnits->count() > 0)
                                        @foreach($filteredBusinessUnits as $unit)
                                            <button type="button"
                                                    wire:click="selectBusinessUnitFilter({{ $unit->id }}, '{{ $unit->name }}')"
                                                    class="w-full text-left px-3 py-2 hover:bg-blue-50 focus:bg-blue-50 focus:outline-none border-b border-gray-100 last:border-b-0">
                                                <div class="font-medium text-sm">{{ $unit->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $unit->code }}</div>
                                            </button>
                                        @endforeach
                                    @else
                                        <div class="px-3 py-2 text-sm text-gray-500">No business units found</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Land Filter with Dropdown Search -->
                    <div class="relative">
                        <label for="filterLandSearch" class="block text-sm font-medium text-gray-700">Land</label>
                        <div class="relative">
                            <input type="text" 
                                wire:model.live="filterLandSearch"
                                wire:click="openLandFilterDropdown"
                                id="filterLandSearch"
                                placeholder="Search lands..."
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-8"
                                autocomplete="off">
                            
                            <!-- Clear button -->
                            @if($filterLand)
                                <button type="button" 
                                        wire:click="clearLandFilterSearch"
                                        class="absolute inset-y-0 right-0 flex items-center pr-2">
                                    <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                            
                            <!-- Dropdown -->
                            @if($showLandFilterDropdown)
                                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                    @php
                                        $filteredLands = $this->getFilteredLandsForFilter();
                                    @endphp
                                    
                                    @if($filteredLands->count() > 0)
                                        @foreach($filteredLands as $land)
                                            <button type="button"
                                                    wire:click="selectLandFilter({{ $land->id }}, '{{ $land->lokasi_lahan }}')"
                                                    class="w-full text-left px-3 py-2 hover:bg-blue-50 focus:bg-blue-50 focus:outline-none border-b border-gray-100 last:border-b-0">
                                                <div class="font-medium text-sm">{{ $land->lokasi_lahan }}</div>
                                            </button>
                                        @endforeach
                                    @else
                                        <div class="px-3 py-2 text-sm text-gray-500">No lands found</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Reset Filters -->
                    <div class="flex items-end">
                        <button wire:click="resetFilters" 
                                class="w-full px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                            Reset Filters
                        </button>
                    </div>
                </div>
                @else
                <div class="mt-4 flex justify-between items-center">
                    <input wire:model.live="search" 
                        type="text" 
                        placeholder="Search soils by seller, buyer, location..." 
                        class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @if($businessUnit)
                        <div class="ml-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                Filtered by: {{ $businessUnit->name }}
                            </span>
                        </div>
                    @endif
                </div>
                @endif
            </div>

            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-b">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-b">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Land & Location</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Unit</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ownership Proof</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area & Price</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Investment</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PPJB/ AJB</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($soils as $soil)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $soil->land->lokasi_lahan ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $soil->letak_tanah }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $soil->businessUnit->code ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $soil->nama_penjual }}</div>
                                    <div class="text-sm text-gray-500">{{ $soil->alamat_penjual }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $soil->bukti_kepemilikan }}</div>
                                    <div class="text-sm text-gray-500">{{ $soil->bukti_kepemilikan_details }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($soil->luas, 0, ',', '.') }} m²</div>
                                    <div class="text-sm text-gray-500">{{ $soil->formatted_harga_per_meter }}/ m2</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-green-700">{{ $soil->formatted_total_biaya_keseluruhan }}</div>
                                    @if($soil->biayaTambahanSoils->count() > 0)
                                        <div class="text-xs text-orange-600">
                                            +{{ $soil->formatted_total_biaya_tambahan }} additional
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $soil->biayaTambahanSoils->count() }} cost item(s)
                                            @php
                                                $standardCosts = $soil->biayaTambahanSoils->where('cost_type', 'standard')->count();
                                                $nonStandardCosts = $soil->biayaTambahanSoils->where('cost_type', 'non_standard')->count();
                                            @endphp
                                            @if($standardCosts > 0)
                                                <span class="inline-flex px-1 text-xs rounded bg-green-100 text-green-800 ml-1">{{ $standardCosts }}S</span>
                                            @endif
                                            @if($nonStandardCosts > 0)
                                                <span class="inline-flex px-1 text-xs rounded bg-orange-100 text-orange-800 ml-1">{{ $nonStandardCosts }}NS</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400">No additional costs</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $soil->nomor_ppjb }}</div>
                                    <div class="text-sm text-gray-500">{{ $soil->tanggal_ppjb->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-1 py-2 whitespace-nowrap text-sm font-medium">
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
                                        
                                        <!-- Edit Options Dropdown -->
                                        <div class="relative inline-block text-left">
                                            <button type="button" 
                                                    onclick="document.getElementById('edit-menu-{{ $soil->id }}').classList.toggle('hidden')"
                                                    class="flex items-center justify-center text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50"
                                                    title="Edit Options">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <div id="edit-menu-{{ $soil->id }}" class="hidden absolute left-0 mt-1 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    <button wire:click="showEditForm({{ $soil->id }}, 'details')" 
                                                            class="flex items-center px-2 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                        Edit Soil Details
                                                    </button>
                                                    <button wire:click="showEditForm({{ $soil->id }}, 'costs')" 
                                                            class="flex items-center px-2 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                        Manage Additional Costs
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Delete Button -->
                                        <button wire:click="delete({{ $soil->id }})" 
                                                wire:confirm="Are you sure you want to delete this soil record?"
                                                class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                                                title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-2 text-center text-gray-500">
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

            <div class="p-6 border-t border-gray-200">
                {{ $soils->links() }}
            </div>
        </div>
    @endif
</div>
