{{-- resources/views/livewire/lands/index.blade.php --}}
<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if ($showForm)
        @include('livewire.lands.form')
    @elseif ($showDetailForm)
        @include('livewire.lands.show')
    @else

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            Lands
                            @if($businessUnit)
                                <span class="text-lg text-blue-600">- {{ $businessUnit->name }}</span>
                            @endif
                        </h2>
                        @if($businessUnit)
                            <p class="text-sm text-gray-600 mt-1">
                                Showing lands for business unit: <a href ="{{ route('business-units', ['view' => 'show', 'id' => $businessUnit->id]) }}"><strong>{{ $businessUnit->name }}</strong></a> ({{ $businessUnit->code }})
                            </p>
                        @endif
                    </div>    
                    <div class="flex space-x-3">
                        @if($businessUnit)
                            <button wire:click="clearBusinessUnitFilter" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Show All Lands
                            </button>
                        @endif
                        <button wire:click="showCreateForm" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Land Record
                        </button>
                    </div>
                </div>

                <!-- Filters and Search -->
                @if(!$this->isFiltered())
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" 
                               wire:model.live="search" 
                               id="search"
                               placeholder="Search by location, city, status..."
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <!-- Business Unit Filter -->
                    <div>
                        <label for="filterBusinessUnit" class="block text-sm font-medium text-gray-700">Business Unit</label>
                        <select wire:model.live="filterBusinessUnit" 
                                id="filterBusinessUnit"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Business Units</option>
                            @foreach($businessUnits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="filterStatus" class="block text-sm font-medium text-gray-700">Status</label>
                        <select wire:model.live="filterStatus" 
                                id="filterStatus"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset Filters -->
                    <div class="flex items-end">
                        <button wire:click="resetFilters" 
                                class="w-full px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- City/Regency Filter (on second row) -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="filterKotaKabupaten" class="block text-sm font-medium text-gray-700">City/Regency</label>
                        <select wire:model.live="filterKotaKabupaten" 
                                id="filterKotaKabupaten"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Cities/Regencies</option>
                            @foreach($kotaKabupaten as $kota)
                                <option value="{{ $kota }}">{{ $kota }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Empty columns to maintain layout -->
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                @else
                <div class="mt-4 flex justify-between items-center">
                    <input wire:model.live="search" 
                           type="text" 
                           placeholder="Search by location, city, status..." 
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

            <div class="p-6">
                {{-- Lands Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Units</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City/Regency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acquisition Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Soil Area</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Price/m²</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Additional Costs</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Related</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($lands as $land)
                                @php
                                    // Calculate total additional costs for all soils in this land
                                    $totalAdditionalCosts = $land->soils->sum(function($soil) {
                                        return $soil->biayaTambahanSoils->sum('harga');
                                    });
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $land->lokasi_lahan }}</div>
                                        @if($land->alamat)
                                            <div class="text-sm text-gray-500">{{ Str::limit($land->alamat, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($land->business_units_count > 0)
                                            <div class="text-sm text-gray-900">
                                                @if($land->business_units_count == 1)
                                                    <span class="font-medium">{{ $land->business_unit_codes }}</span>
                                                @else
                                                    <div class="space-y-1">
                                                        @foreach($land->soils->pluck('businessUnit')->filter()->unique('id')->take(2) as $unit)
                                                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mr-1 mb-1">
                                                                {{ $unit->name }}
                                                            </div>
                                                        @endforeach
                                                        @if($land->business_units_count > 2)
                                                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                                                +{{ $land->business_units_count - 2 }} more
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">No business units</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $land->kota_kabupaten ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $land->tahun_perolehan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $land->formatted_nilai_perolehan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($land->total_soil_area > 0)
                                            {{ $land->formatted_total_soil_area }}
                                        @else
                                            <span class="text-gray-400">No soils</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($land->total_soil_area > 0)
                                            {{ $land->formatted_average_price_per_m2 }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($totalAdditionalCosts > 0)
                                            <div class="font-medium text-blue-900">
                                                Rp {{ number_format($totalAdditionalCosts, 0, ',', '.') }}
                                            </div>
                                            @if($land->soils_count > 0)
                                                <div class="text-xs text-gray-500">
                                                    from {{ $land->soils_count }} soil(s)
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-gray-400">Rp 0</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($land->status === 'Available') bg-green-100 text-green-800
                                            @elseif($land->status === 'Reserved') bg-yellow-100 text-yellow-800
                                            @elseif($land->status === 'Sold') bg-red-100 text-red-800
                                            @elseif($land->status === 'Development') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $land->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex space-x-1">
                                            @if($land->soils_count > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $land->soils_count }} Soils
                                                </span>
                                            @endif
                                            @if($land->projects_count > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $land->projects_count }} Projects
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <button wire:click="showDetail({{ $land->id }})" 
                                                    title="View"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            @can('lands.edit')
                                            <button wire:click="showEditForm({{ $land->id }})" 
                                                    title="Edit"
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            @endcan
                                            @can('lands.delete')
                                            <button wire:click="delete({{ $land->id }})" 
                                                    wire:confirm="Are you sure you want to delete this land?"
                                                    title="Delete"
                                                    class="text-red-600 hover:text-red-900 transition-colors">
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
                                    <td colspan="11" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        @if($this->isFiltered())
                                            No lands found for business unit "{{ $this->getCurrentBusinessUnitName() }}".
                                        @elseif($search || $filterBusinessUnit || $filterStatus || $filterKotaKabupaten)
                                            No lands found matching the selected filters.
                                        @else
                                            No lands found.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($lands->hasPages())
                    <div class="mt-4">
                        {{ $lands->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>