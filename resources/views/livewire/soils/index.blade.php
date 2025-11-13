{{-- resources/views/livewire/soils/index.blade.php --}}
<div class="min-h-screen bg-gray-50">
    @if ($showForm)
        @include('livewire.soils.form')
    @elseif($showAdditionalCostsForm)
        @include('livewire.soils.costs-form')
    @elseif($showInterestCostsForm)
        @include('livewire.soils.costs-interest-form')
    @elseif($showDetailForm)
        @include('livewire.soils.show')
    @else
        <!-- Compact Header -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Header Section -->
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50">
                <!-- Alert Messages -->
                <div class="mb-3 space-y-2">
                    @if (session()->has('message'))
                        <div
                            class="flex items-center px-3 py-2 text-sm text-green-800 border-l-4 border-green-500 rounded bg-green-50">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div
                            class="flex items-center px-3 py-2 text-sm text-red-800 border-l-4 border-red-500 rounded bg-red-50">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session()->has('warning'))
                        <div
                            class="flex items-center px-3 py-2 text-sm text-yellow-800 border-l-4 border-yellow-500 rounded bg-yellow-50">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('warning') }}
                        </div>
                    @endif
                </div>

                <!-- Title and Actions -->
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <!-- Title Section -->
                    <div class="flex items-center gap-3">
                        <button onclick="history.back()"
                            class="inline-flex items-center justify-center p-2 text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300"
                            aria-label="Go back">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Soil Management</h1>
                            @if ($businessUnit)
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-sm text-gray-600">Business Unit:</span>
                                    <a href="{{ route('business-units', ['view' => 'show', 'id' => $businessUnit->id]) }}"
                                        class="text-sm font-medium text-blue-600 transition-colors hover:text-blue-800 hover:underline">
                                        {{ $businessUnit->name }} ({{ $businessUnit->code }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2">
                        @if ($businessUnit)
                            <button wire:click="clearBusinessUnitFilter"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Show All
                            </button>
                        @endif

                        <button wire:click="showExportModalView"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Data
                        </button>

                        @can('soils.edit')
                            <button wire:click="showImportModalView"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                                Import Data
                            </button>

                            <button wire:click="showCreateForm"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add New Soil
                            </button>
                        @endcan
                    </div>
                </div>

                <!-- Compact Filters Section -->
                <div class="mt-4">
                    @if (!$this->isFiltered())
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                            <!-- Search -->
                            <div class="lg:col-span-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" wire:model.live="search"
                                        placeholder="Search seller, location, PPJB/AJB..."
                                        class="block w-full py-2 pl-10 pr-3 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        aria-label="Search soils">
                                </div>
                            </div>

                            <!-- Business Unit Filter -->
                            <div class="relative">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <input type="text" wire:model.live.debounce.300ms="filterBusinessUnitSearch"
                                        wire:focus="openBusinessUnitFilterDropdown" placeholder="Business Unit..."
                                        class="block w-full py-2 pl-10 pr-8 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        autocomplete="off" aria-label="Filter by business unit">

                                    @if ($filterBusinessUnit)
                                        <button type="button" wire:click="clearBusinessUnitFilterSearch"
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400 transition-colors hover:text-gray-600"
                                            aria-label="Clear business unit filter">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif

                                    @if ($showBusinessUnitFilterDropdown)
                                        <div class="absolute z-50 w-full mt-1 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-lg max-h-60"
                                            wire:click.stop>
                                            @php
                                                $filteredBusinessUnits = $this->getFilteredBusinessUnitsForFilter();
                                            @endphp

                                            @if (empty($filterBusinessUnitSearch ?? '') && $filteredBusinessUnits->count() > 0)
                                                <div
                                                    class="sticky top-0 px-3 py-2 text-xs font-medium text-blue-600 border-b border-blue-100 bg-blue-50">
                                                    Start typing to filter...
                                                </div>
                                            @endif

                                            <div class="overflow-y-auto max-h-48">
                                                @forelse($filteredBusinessUnits as $unit)
                                                    <button type="button"
                                                        wire:click.stop="selectBusinessUnitFilter({{ $unit->id }}, '{{ addslashes($unit->name) }}')"
                                                        class="flex items-center justify-between w-full px-3 py-2 text-sm text-left transition-colors hover:bg-blue-50 border-b border-gray-100 last:border-b-0
                                                        @if ($filterBusinessUnit == $unit->id) bg-blue-50 text-blue-900 font-medium @endif"
                                                        aria-label="Select {{ $unit->name }}">
                                                        <div>
                                                            <div class="font-medium">{{ $unit->name }}</div>
                                                            <div class="text-xs text-gray-500">{{ $unit->code }}
                                                            </div>
                                                        </div>
                                                        @if ($filterBusinessUnit == $unit->id)
                                                            <span class="text-blue-600" aria-hidden="true">✓</span>
                                                        @endif
                                                    </button>
                                                @empty
                                                    <div class="px-3 py-2 text-sm text-gray-500">
                                                        No business units found
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Land Filter -->
                            <div class="relative">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" wire:model.live.debounce.300ms="filterLandSearch"
                                        wire:focus="openLandFilterDropdown"
                                        placeholder="{{ $filterBusinessUnit || $filterByBusinessUnit ? 'Land...' : 'Select BU first...' }}"
                                        class="block w-full py-2 pl-10 pr-8 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        autocomplete="off" aria-label="Filter by land"
                                        @if (!$filterBusinessUnit && !$filterByBusinessUnit) disabled @endif>

                                    @if ($filterLand)
                                        <button type="button" wire:click="clearLandFilter"
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400 transition-colors hover:text-gray-600"
                                            aria-label="Clear land filter">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif

                                    @if ($showLandFilterDropdown && ($filterBusinessUnit || $filterByBusinessUnit))
                                        <div class="absolute z-50 w-full mt-1 overflow-auto bg-white border border-gray-300 rounded-md shadow-lg max-h-48"
                                            wire:click.stop>
                                            @php
                                                $filteredLands = $this->getFilteredLandsForFilter();
                                            @endphp

                                            @if ($filteredLands->count() > 0)
                                                @foreach ($filteredLands as $land)
                                                    <button type="button"
                                                        wire:click.stop="selectLandFilter({{ $land->id }}, '{{ addslashes($land->lokasi_lahan) }}')"
                                                        class="flex items-center justify-between w-full px-3 py-2 text-sm text-left transition-colors hover:bg-blue-50 border-b border-gray-100 last:border-b-0 @if ($filterLand == $land->id) bg-blue-50 text-blue-900 font-medium @endif"
                                                        aria-label="Select {{ $land->lokasi_lahan }}">
                                                        <div>
                                                            <div class="font-medium">{{ $land->lokasi_lahan }}</div>
                                                            @if ($land->kota_kabupaten)
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $land->kota_kabupaten }}</div>
                                                            @endif
                                                        </div>
                                                        @if ($filterLand == $land->id)
                                                            <span class="text-blue-600" aria-hidden="true">✓</span>
                                                        @endif
                                                    </button>
                                                @endforeach
                                            @else
                                                <div class="px-3 py-2 text-sm text-gray-500">
                                                    No lands found
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <div class="relative">
                                    <select wire:model.live="filterStatus"
                                        class="block w-full py-2 pl-3 pr-8 text-sm bg-white border border-gray-300 rounded-lg appearance-none focus:ring-blue-500 focus:border-blue-500"
                                        aria-label="Filter by status">
                                        <option value="">All Status</option>
                                        @foreach ($this->getStatusOptions() as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Reset Filters -->
                            <div class="flex items-center">
                                <button wire:click="resetFilters"
                                    class="w-full px-3 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300"
                                    aria-label="Reset all filters">
                                    Reset Filters
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <div class="relative flex-1 max-w-md">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input wire:model.live="search" type="text" placeholder="Search soils..."
                                    class="block w-full py-2 pl-10 pr-3 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    aria-label="Search soils">
                            </div>
                            @if ($businessUnit)
                                <div
                                    class="flex items-center gap-2 px-3 py-1 bg-blue-100 border border-blue-200 rounded-lg">
                                    <span class="text-sm font-medium text-blue-800">{{ $businessUnit->name }}</span>
                                    <button wire:click="clearBusinessUnitFilter"
                                        class="text-blue-600 transition-colors hover:text-blue-800"
                                        aria-label="Remove business unit filter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Table Controls -->
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/30">
                <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                    <!-- Row Selection -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-700">Show</span>
                        <select wire:model.live="perPage"
                            class="px-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"
                            aria-label="Rows per page">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-700">rows per page</span>
                    </div>

                    <!-- Results Count -->
                    <div class="text-sm text-gray-600">
                        Showing {{ $soils->firstItem() ?? 0 }} to {{ $soils->lastItem() ?? 0 }} of
                        {{ $soils->total() }} results
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" aria-label="Soil data table">
                    <thead class="bg-gray-50">
                        <tr>
                            <!-- Land Column -->
                            <th wire:click="sortBy('land_id')"
                                class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase cursor-pointer select-none hover:bg-gray-100 group"
                                aria-sort="{{ $sortField === 'land_id' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <div class="flex items-center gap-1">
                                    <span>Land Location</span>
                                    <div class="flex flex-col">
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'land_id' && $sortDirection === 'asc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'land_id' && $sortDirection === 'desc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </th>

                            <!-- Business Unit Column -->
                            <th wire:click="sortBy('business_unit_id')"
                                class="px-3 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase cursor-pointer select-none hover:bg-gray-100 group"
                                aria-sort="{{ $sortField === 'business_unit_id' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <div class="flex items-center gap-1">
                                    <span>BU</span>
                                    <div class="flex flex-col">
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'business_unit_id' && $sortDirection === 'asc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'business_unit_id' && $sortDirection === 'desc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </th>

                            <!-- Seller Column -->
                            <th wire:click="sortBy('nama_penjual')"
                                class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase cursor-pointer select-none hover:bg-gray-100 group"
                                aria-sort="{{ $sortField === 'nama_penjual' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <div class="flex items-center gap-1">
                                    <span>Seller</span>
                                    <div class="flex flex-col">
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'nama_penjual' && $sortDirection === 'asc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'nama_penjual' && $sortDirection === 'desc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </th>

                            <!-- Ownership Column -->
                            <th wire:click="sortBy('bukti_kepemilikan')"
                                class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase cursor-pointer select-none hover:bg-gray-100 group"
                                aria-sort="{{ $sortField === 'bukti_kepemilikan' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <div class="flex items-center gap-1">
                                    <span>Ownership</span>
                                    <div class="flex flex-col">
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'bukti_kepemilikan' && $sortDirection === 'asc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'bukti_kepemilikan' && $sortDirection === 'desc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </th>

                            <!-- PPJB/AJB Column -->
                            <th wire:click="sortBy('tanggal_ppjb')"
                                class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase cursor-pointer select-none hover:bg-gray-100 group"
                                aria-sort="{{ $sortField === 'tanggal_ppjb' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <div class="flex items-center gap-1">
                                    <span>PPJB/AJB</span>
                                    <div class="flex flex-col">
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'tanggal_ppjb' && $sortDirection === 'asc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'tanggal_ppjb' && $sortDirection === 'desc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </th>

                            <!-- Area Column -->
                            <th wire:click="sortBy('luas')"
                                class="px-4 py-3 text-xs font-semibold tracking-wider text-right text-gray-700 uppercase cursor-pointer select-none hover:bg-gray-100 group"
                                aria-sort="{{ $sortField === 'luas' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <div class="flex items-center justify-end gap-1">
                                    <span>Area (m²)</span>
                                    <div class="flex flex-col">
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'luas' && $sortDirection === 'asc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'luas' && $sortDirection === 'desc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </th>

                            <!-- Status Column -->
                            <th wire:click="sortBy('status')"
                                class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase cursor-pointer select-none hover:bg-gray-100 group"
                                aria-sort="{{ $sortField === 'status' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <div class="flex items-center gap-1">
                                    <span>Status</span>
                                    <div class="flex flex-col">
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'status' && $sortDirection === 'asc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'status' && $sortDirection === 'desc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </th>

                            <!-- Investment Column -->
                            <th wire:click="sortBy('harga')"
                                class="px-4 py-3 text-xs font-semibold tracking-wider text-right text-gray-700 uppercase cursor-pointer select-none hover:bg-gray-100 group"
                                aria-sort="{{ $sortField === 'harga' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <div class="flex items-center justify-end gap-1">
                                    <span>Investment</span>
                                    <div class="flex flex-col">
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'harga' && $sortDirection === 'asc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 {{ $sortField === 'harga' && $sortDirection === 'desc' ? 'text-blue-600' : '' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </th>

                            <!-- Actions Column -->
                            <th
                                class="px-3 py-3 text-xs font-semibold tracking-wider text-center text-gray-700 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($soils as $soil)
                            <tr class="transition-colors hover:bg-gray-50/80" aria-rowindex="{{ $loop->index + 1 }}">
                                <!-- Land Location -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $soil->land->lokasi_lahan ?? 'N/A' }}
                                        </span>
                                        @if ($soil->letak_tanah)
                                            <span class="text-xs text-gray-500 truncate max-w-[180px]">
                                                {{ $soil->letak_tanah }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Business Unit -->
                                <td class="px-3 py-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $soil->businessUnit->code ?? '-' }}
                                    </span>
                                </td>

                                <!-- Seller -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ $soil->nama_penjual }}</span>
                                        @if ($soil->alamat_penjual)
                                            <span class="text-xs text-gray-500 truncate max-w-[150px]">
                                                {{ $soil->alamat_penjual }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Ownership -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col space-y-1">
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ $soil->bukti_kepemilikan }}</span>
                                        @if ($soil->bukti_kepemilikan_details)
                                            <span class="text-xs text-gray-500">
                                                No. {{ Str::limit($soil->bukti_kepemilikan_details, 25) }}
                                            </span>
                                        @endif

                                        <!-- SHGB Expiration Badge -->
                                        @if ($soil->bukti_kepemilikan === 'SHGB' && $soil->shgb_expired_date)
                                            <div class="mt-1">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if ($soil->is_shgb_expired) bg-red-100 text-red-800 border border-red-200
                                            @elseif($soil->shgb_days_until_expiration <= 365)
                                                bg-yellow-100 text-yellow-800 border border-yellow-200
                                            @else
                                                bg-green-100 text-green-800 border border-green-200 @endif">
                                                    @if ($soil->is_shgb_expired)
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Expired
                                                    @elseif($soil->shgb_days_until_expiration <= 365)
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        {{ abs($soil->shgb_days_until_expiration) }}d
                                                    @else
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        {{ $soil->formatted_shgb_expired_date }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- PPJB/AJB -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">No. {{ $soil->nomor_ppjb }}</span>
                                        <span
                                            class="text-xs text-gray-500">{{ $soil->tanggal_ppjb->format('d/m/Y') }}</span>
                                    </div>
                                </td>

                                <!-- Area -->
                                <td class="px-4 py-3 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ number_format($soil->luas, 0, ',', '.') }} m²
                                        </span>
                                        <span
                                            class="text-xs text-gray-500">{{ $soil->formatted_harga_per_meter }}/m²</span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $soil->status_badge_color }} border">
                                        {{ $soil->formatted_status }}
                                    </span>
                                </td>

                                <!-- Investment -->
                                <td class="px-4 py-3 text-right">
                                    <div class="flex flex-col items-end space-y-1">
                                        <!-- Total Investment -->
                                        <span class="text-sm font-semibold text-green-700">
                                            Rp
                                            {{ number_format($soil->harga + $soil->total_biaya_tambahan + $soil->total_biaya_interest, 0, ',', '.') }}
                                        </span>

                                        <!-- Additional Costs -->
                                        @if ($soil->biayaTambahanSoils->count() > 0)
                                            <div class="flex items-center justify-end gap-1">
                                                <span class="text-xs text-orange-600">
                                                    +{{ $soil->formatted_total_biaya_tambahan }}
                                                </span>
                                                <div class="flex gap-0.5">
                                                    @php
                                                        $standardCosts = $soil->biayaTambahanSoils
                                                            ->where('cost_type', 'standard')
                                                            ->count();
                                                        $nonStandardCosts = $soil->biayaTambahanSoils
                                                            ->where('cost_type', 'non_standard')
                                                            ->count();
                                                    @endphp
                                                    @if ($standardCosts > 0)
                                                        <span
                                                            class="inline-flex items-center justify-center w-4 h-4 text-xs text-green-800 bg-green-100 rounded">
                                                            {{ $standardCosts }}
                                                        </span>
                                                    @endif
                                                    @if ($nonStandardCosts > 0)
                                                        <span
                                                            class="inline-flex items-center justify-center w-4 h-4 text-xs text-orange-800 bg-orange-100 rounded">
                                                            {{ $nonStandardCosts }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Interest Costs -->
                                        @if ($soil->biayaTambahanInterestSoils->count() > 0)
                                            <div class="flex items-center justify-end gap-1">
                                                <span class="text-xs text-purple-600">
                                                    +{{ $soil->formatted_total_biaya_interest }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center justify-center w-4 h-4 text-xs text-purple-800 bg-purple-100 rounded">
                                                    {{ $soil->biayaTambahanInterestSoils->count() }}
                                                </span>
                                            </div>
                                        @endif

                                        <!-- No costs indicator -->
                                        @if ($soil->biayaTambahanSoils->count() === 0 && $soil->biayaTambahanInterestSoils->count() === 0)
                                            <span class="text-xs text-gray-400">No additional costs</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- View Button -->
                                        <button wire:click="showDetail({{ $soil->id }})"
                                            class="p-1.5 text-blue-600 transition-colors rounded-lg hover:text-blue-800 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                            title="View details" aria-label="View soil details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>

                                        <!-- Edit Dropdown -->
                                        @canany(['soils.edit', 'soil-costs.edit'])
                                            <div class="relative inline-block text-left" x-data="{ open: false }"
                                                @click.outside="open = false">
                                                <button type="button" @click="open = !open"
                                                    class="p-1.5 text-yellow-600 transition-colors rounded-lg hover:text-yellow-800 hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                                                    title="Edit options" aria-label="Edit soil options"
                                                    aria-expanded="false" :aria-expanded="open">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                    class="absolute right-0 z-50 w-48 mt-1 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                    @click="open = false">
                                                    <div class="py-1" role="menu" aria-orientation="vertical">
                                                        @can('soils.edit')
                                                            <button wire:click="showEditForm({{ $soil->id }}, 'details')"
                                                                class="flex items-center w-full px-3 py-2 text-sm text-left text-gray-700 transition-colors hover:bg-gray-100"
                                                                role="menuitem">
                                                                <svg class="w-4 h-4 mr-2" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Edit Details
                                                            </button>
                                                        @endcan
                                                        @canany(['soils.edit', 'soil-costs.edit'])
                                                            <button wire:click="showEditForm({{ $soil->id }}, 'costs')"
                                                                class="flex items-center w-full px-3 py-2 text-sm text-left text-gray-700 transition-colors hover:bg-gray-100"
                                                                role="menuitem">
                                                                <svg class="w-4 h-4 mr-2" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                                </svg>
                                                                Manage Costs
                                                            </button>
                                                        @endcan
                                                        @can('soil-data-interest-costs.edit')
                                                            <button wire:click="showEditForm({{ $soil->id }}, 'interest')"
                                                                class="flex items-center w-full px-3 py-2 text-sm text-left text-gray-700 transition-colors hover:bg-gray-100"
                                                                role="menuitem">
                                                                <svg class="w-4 h-4 mr-2" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                                </svg>
                                                                Manage Interest
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan

                                        <!-- Delete Button -->
                                        @can('soils.delete')
                                            <button wire:click="showDeleteModalView({{ $soil->id }})"
                                                class="p-1.5 text-red-600 transition-colors rounded-lg hover:text-red-800 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300"
                                                title="Delete" aria-label="Delete soil">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mb-2 text-lg font-medium text-gray-600">No soils found</p>
                                        <p class="max-w-md text-sm text-gray-500">
                                            @if ($businessUnit)
                                                No soils available for {{ $businessUnit->name }}.
                                            @elseif($search)
                                                No soils matching "{{ $search }}". Try adjusting your search
                                                terms.
                                            @else
                                                Get started by adding your first soil data.
                                            @endif
                                        </p>
                                        @can('soils.edit')
                                            @if (!$businessUnit && !$search)
                                                <button wire:click="showCreateForm"
                                                    class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    Add Your First Soil
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <!-- Summary Footer -->
                    @if ($soils->count() > 0)
                        <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-sm font-semibold text-right text-gray-700">
                                    Total Summary:
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-right text-gray-700">
                                    {{ number_format($this->totalArea, 0, ',', '.') }} m²
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-center text-gray-700">
                                    {{ $soils->total() }} records
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-right text-green-700">
                                    Rp {{ number_format($this->totalInvestment, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-4"></td>
                            </tr>

                            <!-- Additional Costs Breakdown -->
                            @if ($this->hasAdditionalCosts)
                                <tr class="bg-blue-50/50">
                                    <td colspan="5" class="px-4 py-3 text-xs font-medium text-right text-gray-600">
                                        Additional Costs:
                                    </td>
                                    <td class="px-4 py-3 text-xs text-right text-gray-600">
                                        <!-- Area breakdown jika diperlukan -->
                                    </td>
                                    <td class="px-4 py-3 text-xs text-center text-gray-600">
                                        {{ $this->totalAdditionalCostsCount }} costs
                                    </td>
                                    <td class="px-4 py-3 text-xs font-medium text-right text-orange-600">
                                        +Rp {{ number_format($this->totalAdditionalCostsAmount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3"></td>
                                </tr>
                            @endif
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Pagination -->
            @if ($soils->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50/30">
                    {{ $soils->links() }}
                </div>
            @endif
        </div>
        
        <!-- Export Modal -->
        @if ($showExportModal)
            <div class="fixed inset-0 z-50 w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50"
                x-data="{ show: @entangle('showExportModal') }" x-show="show" @click.self="$wire.hideExportModalView()">
                <div class="relative w-full max-w-md p-5 mx-auto bg-white border rounded-md shadow-lg top-20"
                    @click.stop>
                    <div class="mt-3">
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Export to Excel</h3>
                            <button wire:click="hideExportModalView" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Export Options -->
                        <div class="space-y-4">
                            <!-- Export Type Selection -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Export Type</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" wire:model.live="exportType" value="current"
                                            class="mr-2">
                                        <span class="text-sm text-gray-700">Current View
                                            <span class="text-xs text-gray-500">({{ $soils->total() }} records)</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" wire:model.live="exportType" value="all"
                                            class="mr-2">
                                        <span class="text-sm text-gray-700">All Records
                                            @if ($businessUnit)
                                                <span class="text-xs text-gray-500">(from
                                                    {{ $businessUnit->name }})</span>
                                            @endif
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" wire:model.live="exportType" value="date_range"
                                            class="mr-2">
                                        <span class="text-sm text-gray-700">Date Range</span>
                                    </label>
                                </div>
                                @error('exportType')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date Range Inputs -->
                            @if ($exportType === 'date_range')
                                <div class="pt-3 space-y-3 border-t">
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">From Date</label>
                                        <input type="date" wire:model.live="exportDateFrom"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        @error('exportDateFrom')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-gray-700">To Date</label>
                                        <input type="date" wire:model.live="exportDateTo"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        @error('exportDateTo')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    @if ($exportDateFrom && $exportDateTo)
                                        <div
                                            class="p-2 text-sm text-blue-700 border border-blue-200 rounded bg-blue-50">
                                            Records: {{ $this->getExportSummary() }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Export Preview -->
                            @if ($exportType)
                                <div class="pt-3 border-t">
                                    <div class="p-3 text-sm rounded bg-gray-50">
                                        <div class="mb-1 font-medium text-gray-700">Preview:</div>
                                        @if ($exportType === 'current')
                                            <div class="text-gray-600">{{ $soils->total() }} records from current view
                                            </div>
                                            @if ($search || $filterBusinessUnit || $filterByBusinessUnit || $filterLand)
                                                <div class="mt-1 text-xs text-blue-600">
                                                    Filters applied
                                                </div>
                                            @endif
                                        @elseif($exportType === 'all')
                                            <div class="text-gray-600">All soil records
                                                @if ($businessUnit)
                                                    from {{ $businessUnit->name }}
                                                @endif
                                            </div>
                                        @elseif($exportType === 'date_range' && $exportDateFrom && $exportDateTo)
                                            <div class="text-gray-600">{{ $exportDateFrom }} to {{ $exportDateTo }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $this->getExportSummary() }} records
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end gap-3 pt-4 mt-6 border-t">
                            <button wire:click="hideExportModalView"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Cancel
                            </button>
                            <button wire:click="exportToExcel" wire:loading.attr="disabled"
                                wire:target="exportToExcel"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:opacity-50">
                                <span wire:loading.remove wire:target="exportToExcel">Export</span>
                                <span wire:loading wire:target="exportToExcel" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 -ml-1 text-white animate-spin"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Exporting...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Import Excel --}}
        @if ($showImportModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                aria-modal="true">
                <!-- Background overlay dengan blur -->

                <div class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm"
                    wire:click="closeImportModal"></div>

                <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- This element is to trick the browser into centering the modal contents. -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal panel -->
                    <div
                        class="relative inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <!-- Close button -->
                        <button type="button" wire:click="closeImportModal"
                            class="absolute text-gray-400 transition-colors duration-200 top-3 right-3 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="px-6 pt-6 pb-4 bg-white">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-green-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-xl font-semibold leading-6 text-gray-900" id="modal-title">
                                        Import Soil Data
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Import soil data from Excel file with automatic data processing
                                    </p>
                                    <div class="mt-6">
                                        <div class="space-y-6">
                                            <!-- Basic Information -->
                                            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                                <h3 class="mb-3 text-sm font-semibold text-gray-900">Basic Information
                                                </h3>
                                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                    <!-- Business Unit with Dropdown Search -->
                                                    <div class="relative">
                                                        <label for="businessUnitSearch"
                                                            class="block mb-2 text-sm font-medium text-gray-700">
                                                            Business Unit *
                                                        </label>
                                                        <input type="text"
                                                            wire:model.live.debounce.300ms="importBusinessUnitSearch"
                                                            wire:focus="searchImportBusinessUnits"
                                                            id="businessUnitSearch"
                                                            placeholder="Search business unit..."
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('import_business_unit_id') border-red-300 @enderror"
                                                            autocomplete="off">

                                                        @if ($showImportBusinessUnitDropdown)
                                                            <div class="absolute z-40 w-full py-2 mt-1 text-sm bg-white border border-gray-200 rounded-lg shadow-xl"
                                                                style="max-height: 240px; overflow-y: auto; overflow-x: hidden;"
                                                                wire:click.stop>
                                                                @php
                                                                    $businessUnits = $this->getFilteredImportBusinessUnits();
                                                                @endphp

                                                                @if (empty($importBusinessUnitSearch ?? '') && $businessUnits->count() > 0)
                                                                    <div
                                                                        class="sticky top-0 z-10 px-3 py-2 text-sm text-blue-600 border-b border-blue-100 bg-blue-50">
                                                                        Available business units
                                                                    </div>
                                                                @endif

                                                                <div class="overflow-y-auto max-h-48">
                                                                    @forelse($businessUnits as $unit)
                                                                        <button type="button"
                                                                            wire:click.stop="selectImportBusinessUnit({{ $unit->id }}, '{{ addslashes($unit->name) }}')"
                                                                            class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 focus:bg-blue-50 focus:outline-none transition-colors duration-150
                                                                    @if ($import_business_unit_id == $unit->id) bg-blue-100 text-blue-800 font-medium @endif">
                                                                            <div
                                                                                class="flex items-center justify-between">
                                                                                <span>{{ $unit->name }}</span>
                                                                                @if ($import_business_unit_id == $unit->id)
                                                                                    <svg class="w-4 h-4 text-blue-600"
                                                                                        fill="currentColor"
                                                                                        viewBox="0 0 20 20">
                                                                                        <path fill-rule="evenodd"
                                                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                                            clip-rule="evenodd" />
                                                                                    </svg>
                                                                                @endif
                                                                            </div>
                                                                        </button>
                                                                    @empty
                                                                        <div
                                                                            class="px-3 py-2 text-sm text-center text-gray-500">
                                                                            No business units found
                                                                        </div>
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @error('import_business_unit_id')
                                                            <span
                                                                class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <!-- Land with Dropdown Search -->
                                                    <div class="relative">
                                                        <label for="landSearch"
                                                            class="block mb-2 text-sm font-medium text-gray-700">
                                                            Land *
                                                        </label>
                                                        <input type="text"
                                                            wire:model.live.debounce.300ms="importLandSearch"
                                                            wire:focus="searchImportLands" id="landSearch"
                                                            placeholder="{{ $import_business_unit_id ? 'Search land...' : 'Select business unit first' }}"
                                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('import_land_id') border-red-300 @enderror"
                                                            autocomplete="off"
                                                            @if (!$import_business_unit_id) disabled @endif>

                                                        @if (!$import_business_unit_id)
                                                            <p
                                                                class="px-2 py-1 mt-2 text-xs rounded text-amber-600 bg-amber-50">
                                                                Please select a business unit first
                                                            </p>
                                                        @endif

                                                        @if ($showImportLandDropdown && $import_business_unit_id)
                                                            <div class="absolute z-50 w-full py-2 mt-1 text-sm bg-white border border-gray-200 rounded-lg shadow-xl"
                                                                style="max-height: 240px; overflow-y: auto; overflow-x: hidden;"
                                                                wire:click.stop>
                                                                @php
                                                                    $lands = $this->getFilteredImportLands();
                                                                @endphp

                                                                @if (empty($importLandSearch ?? '') && $lands->count() > 0)
                                                                    <div
                                                                        class="sticky top-0 z-10 px-3 py-2 text-sm text-blue-600 border-b border-blue-100 bg-blue-50">
                                                                        Available lands
                                                                    </div>
                                                                @endif

                                                                <div class="overflow-y-auto max-h-48">
                                                                    @forelse($lands as $land)
                                                                        <button type="button"
                                                                            wire:click.stop="selectImportLand({{ $land->id }}, '{{ addslashes($land->lokasi_lahan) }}')"
                                                                            class="w-full px-3 py-2 text-sm text-left text-gray-700 transition-colors duration-150 hover:bg-blue-50 focus:bg-blue-50 focus:outline-none">
                                                                            <div class="flex flex-col">
                                                                                <span
                                                                                    class="font-medium">{{ $land->lokasi_lahan }}</span>
                                                                                @if ($land->kota_kabupaten)
                                                                                    <span
                                                                                        class="mt-1 text-xs text-gray-500">{{ $land->kota_kabupaten }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </button>
                                                                    @empty
                                                                        <div
                                                                            class="px-3 py-2 text-sm text-center text-gray-500">
                                                                            No lands found
                                                                            @if (!empty($importLandSearch))
                                                                                matching "{{ $importLandSearch }}"
                                                                            @endif
                                                                        </div>
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @error('import_land_id')
                                                            <span
                                                                class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- File Input -->
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">
                                                    Excel File (.xlsx, .xls)
                                                </label>
                                                <div class="flex items-center space-x-4">
                                                    <input type="file" wire:model="importFile" accept=".xlsx,.xls"
                                                        class="block w-full text-sm text-gray-900 transition-all duration-200 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                </div>
                                                @error('importFile')
                                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Loading indicator -->
                                            <div wire:loading wire:target="importFile"
                                                class="flex items-center justify-center p-3 text-sm text-blue-700 border border-blue-200 rounded-lg bg-blue-50">
                                                <svg class="w-4 h-4 mr-2 animate-spin"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Processing file...
                                            </div>

                                            <!-- Info Card -->
                                            <div
                                                class="p-4 text-sm text-blue-800 border border-blue-200 rounded-lg bg-blue-50">
                                                <div class="flex items-start">
                                                    <svg class="w-5 h-5 mr-2 mt-0.5 text-blue-600 flex-shrink-0"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <div>
                                                        <p class="mb-2 font-semibold">Import Information</p>
                                                        <ul class="space-y-1 text-blue-700 list-disc list-inside">
                                                            <li>Business Unit and Land selection is required</li>
                                                            <li>Merged prices will be distributed based on area</li>
                                                            <li>Data grouped by PPJB Number, Date, and Seller Name</li>
                                                            <li>PPJB details and ownership proofs are parsed
                                                                automatically</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3">
                                <button type="button" wire:click="closeImportModal"
                                    class="w-full px-4 py-2 mt-3 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg shadow-sm sm:mt-0 sm:w-auto hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="button" wire:click="importExcel" wire:loading.attr="disabled"
                                    wire:target="importExcel"
                                    class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white transition-colors duration-200 bg-green-600 border border-transparent rounded-lg shadow-sm sm:w-auto hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="importExcel">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        Import Data
                                    </span>
                                    <span wire:loading wire:target="importExcel" class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 2v4m0 12v4m8-10h-4M6 12H2" />
                                        </svg>Importing...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Confirmation Modal -->
        @if ($showDeleteModal)
            <div class="fixed inset-0 z-50 w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
                <div class="relative w-full max-w-lg p-5 mx-auto bg-white border rounded-md shadow-lg top-20">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                Delete Soil Record
                            </h3>
                            <button wire:click="hideDeleteModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="mb-3 text-sm text-gray-600">
                                        Are you sure you want to delete this soil record? This will also delete any
                                        associated costs.
                                    </p>
                                </div>
                            </div>

                            @if (auth()->user()->can('soil-data.approval'))
                                <div class="p-3 mb-4 border border-yellow-200 rounded-md bg-yellow-50">
                                    <div class="flex items-start gap-2">
                                        <svg class="h-5 w-5 text-yellow-400 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-sm text-yellow-800">
                                            <strong>Direct Deletion:</strong> This record will be deleted immediately.
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 mb-4 border border-blue-200 rounded-md bg-blue-50">
                                    <div class="flex items-start gap-2">
                                        <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-sm text-blue-800">
                                            <strong>Approval Required:</strong> Your request will be submitted for
                                            review.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label for="deleteReason" class="block mb-2 text-sm font-medium text-gray-700">
                                    Reason <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="deleteReason" id="deleteReason" rows="4"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Minimum 10 characters..."></textarea>
                                @error('deleteReason')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button wire:click="hideDeleteModalView" type="button"
                                class="px-4 py-2 text-sm text-gray-700 bg-gray-300 rounded-md hover:bg-gray-400">
                                Cancel
                            </button>
                            <button wire:click="deleteWithReason" wire:loading.attr="disabled"
                                wire:target="deleteWithReason" type="button"
                                class="px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50">
                                <span wire:loading.remove wire:target="deleteWithReason">Delete</span>
                                <span wire:loading wire:target="deleteWithReason" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 -ml-1 text-white animate-spin"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
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

<!-- JavaScript -->
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('submit-export-form', (event) => {
            const params = event.params;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('soils.export') }}';
            form.style.display = 'none';

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

    // Click outside handler
    document.addEventListener('click', function(event) {
        const editDropdown = event.target.closest('[x-data*="open"]');
        if (editDropdown) {
            return;
        }

        const dropdowns = document.querySelectorAll('[wire\\:click\\.stop]');
        const inputs = document.querySelectorAll('input[wire\\:focus], textarea[wire\\:focus]');
        const buttons = document.querySelectorAll('button[wire\\:click*="search"]');

        let clickedInside = false;

        [...dropdowns, ...inputs, ...buttons].forEach(element => {
            if (element && element.contains && element.contains(event.target)) {
                clickedInside = true;
            }
        });

        if (!clickedInside && window.Livewire) {
            window.Livewire.dispatch('closeDropdowns');
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(event) {
        const activeDropdown = document.querySelector(
            '[wire\\:click\\.stop][style*="max-height"][style*="overflow-y: auto"]:not([style*="display: none"])'
        );

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
                items[nextIndex].scrollIntoView({
                    block: 'nearest'
                });
            }
        }

        if (event.key === 'Enter' && document.activeElement.matches('button[wire\\:click*="select"]')) {
            event.preventDefault();
            document.activeElement.click();
        }

        if (event.key === 'Escape' && window.Livewire) {
            window.Livewire.dispatch('closeDropdowns');
            if (document.querySelector('[wire\\:click="hideExportModalView"]')) {
                window.Livewire.dispatch('hideExportModalView');
            }
            if (document.querySelector('[wire\\:click="hideDeleteModalView"]')) {
                window.Livewire.dispatch('hideDeleteModalView');
            }
        }
    });
</script>
