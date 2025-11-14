<div>
    {{-- Business Unit Selection Modal (Initial) --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (!$showForm)
        {{-- Header Section --}}
        <div class="bg-white shadow-sm rounded-lg mb-4 p-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-xl font-bold text-gray-900">Land Interest Rates Management</h2>
                        
                        {{-- Business Unit Filter Indicator --}}
                        @if($filterBusinessUnit)
                            @php
                                $selectedBU = \App\Models\BusinessUnit::find($filterBusinessUnit);
                            @endphp
                            @if($selectedBU)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    {{ $selectedBU->code }} - {{ $selectedBU->name }}
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700 border border-gray-300">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                All Business Units
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Track monthly interest rates for each land</p>
                </div>
                
                @can('land-interest-rates.create')
                <button wire:click="showCreateForm" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Interest Rate
                </button>
                @endcan
            </div>
        </div>

        {{-- Statistics Cards --}}
        @if($statistics)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Total Rates</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $statistics['count'] }}</p>
                    </div>
                    <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Average Rate</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($statistics['average'], 2) }}%</p>
                    </div>
                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600">Minimum Rate</p>
                        <p class="text-2xl font-bold text-red-900">{{ number_format($statistics['min'], 2) }}%</p>
                    </div>
                    <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Maximum Rate</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($statistics['max'], 2) }}%</p>
                    </div>
                    <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        {{-- Filter Section --}}
        <div class="bg-white shadow-sm rounded-lg mb-4 p-4">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Year</label>
                    <select wire:model.live="filterYear" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Years</option>
                        @foreach($availableYears as $availableYear)
                            <option value="{{ $availableYear }}">{{ $availableYear }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Business Unit</label>
                    <select wire:model.live="filterBusinessUnit" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Business Units</option>
                        @foreach($businessUnits as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Land</label>
                    <select wire:model.live="filterLand" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            @if(!$filterBusinessUnit) disabled @endif>
                        <option value="">{{ $filterBusinessUnit ? 'All Lands' : 'Select Business Unit First' }}</option>
                        @foreach($lands as $land)
                            <option value="{{ $land->id }}">{{ $land->lokasi_lahan }}</option>
                        @endforeach
                    </select>
                </div>

                @if($filterYear || $filterBusinessUnit || $filterLand)
                <div>
                    <button wire:click="resetFilters" 
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Reset Filters
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Data Table --}}
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Land</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interest Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                            @canany(['land-interest-rates.edit', 'land-interest-rates.delete'])
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rates as $rate)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $rate->land->lokasi_lahan ?? 'N/A' }}</div>
                                    @if($rate->land && $rate->land->kota_kabupaten)
                                        <div class="text-xs text-gray-500">{{ $rate->land->kota_kabupaten }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($rate->land && $rate->land->businessUnit)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $rate->land->businessUnit->code }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $rate->period }}</div>
                                            <div class="text-xs text-gray-500">{{ $rate->month_name }} {{ $rate->year }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $rate->formatted_rate }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">
                                        {{ $rate->notes ? Str::limit($rate->notes, 50) : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $rate->updated_at->format('d/m/Y H:i') }}</div>
                                    @if($rate->updater)
                                        <div class="text-xs text-gray-400">by {{ $rate->updater->name }}</div>
                                    @endif
                                </td>
                                @canany(['land-interest-rates.edit', 'land-interest-rates.delete'])
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center space-x-2">
                                        @can('land-interest-rates.edit')
                                        <button wire:click="showEditForm({{ $rate->id }})" 
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        @endcan
                                        
                                        @can('land-interest-rates.delete')
                                        <button wire:click="confirmDelete({{ $rate->id }})" 
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No interest rates</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by adding a new interest rate.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $rates->links() }}
            </div>
        </div>

    @else
        {{-- Form Section --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $isEdit ? 'Edit Interest Rate' : 'Add New Interest Rate' }}
                </h3>
                <button wire:click="backToIndex" 
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-6">
                {{-- Business Unit & Land Selection --}}
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Land Selection</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Business Unit with Dropdown Search --}}
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Business Unit <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                wire:model.live.debounce.300ms="businessUnitSearch"
                                wire:focus="searchBusinessUnits"
                                placeholder="Search business unit..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('business_unit_id') border-red-500 @enderror"
                                autocomplete="off">
                            
                            @if($showBusinessUnitDropdown)
                                <div class="absolute z-40 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-sm ring-1 ring-black ring-opacity-5 focus:outline-none"
                                     style="max-height: 240px; overflow-y: auto;"
                                     wire:click.stop>
                                    @forelse($this->getFilteredBusinessUnits() as $unit)
                                        <button type="button"
                                                wire:click.stop="selectBusinessUnit({{ $unit->id }}, '{{ addslashes($unit->name) }}')"
                                                class="w-full text-left px-3 py-2 text-sm text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                                            {{ $unit->name }} ({{ $unit->code }})
                                        </button>
                                    @empty
                                        <div class="px-3 py-2 text-sm text-gray-500">No business units found</div>
                                    @endforelse
                                </div>
                            @endif
                            
                            @error('business_unit_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Land with Dropdown Search --}}
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Land <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                wire:model.live.debounce.300ms="landSearch"
                                wire:focus="searchLands"
                                placeholder="{{ $business_unit_id ? 'Search lands...' : 'Select business unit first' }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('land_id') border-red-500 @enderror"
                                autocomplete="off"
                                @if(!$business_unit_id) disabled @endif>
                            
                            @if(!$business_unit_id)
                                <p class="mt-1 text-sm text-amber-600">
                                    Please select a business unit first
                                </p>
                            @endif
                            
                            @if($showLandDropdown && $business_unit_id)
                                <div class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-sm ring-1 ring-black ring-opacity-5 focus:outline-none"
                                     style="max-height: 240px; overflow-y: auto;"
                                     wire:click.stop>
                                    @forelse($this->getFilteredLands() as $land)
                                        <button type="button"
                                                wire:click.stop="selectLand({{ $land->id }}, '{{ addslashes($land->lokasi_lahan) }}')"
                                                class="w-full text-left px-3 py-2 text-sm text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                                            {{ $land->lokasi_lahan }}
                                            @if($land->kota_kabupaten)
                                                <span class="text-gray-500"> - {{ $land->kota_kabupaten }}</span>
                                            @endif
                                        </button>
                                    @empty
                                        <div class="px-3 py-2 text-sm text-gray-500">No lands found</div>
                                    @endforelse
                                </div>
                            @endif
                            
                            @error('land_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Interest Rate Details --}}
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Interest Rate Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Month --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Month <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="month" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('month') border-red-500 @enderror">
                                <option value="">Select Month</option>
                                @foreach($this->getMonthOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('month') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Year --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Year <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   wire:model="year" 
                                   min="2000" 
                                   max="{{ date('Y') + 10 }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('year') border-red-500 @enderror">
                            @error('year') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Rate --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Interest Rate (%) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       wire:model="rate" 
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       placeholder="e.g., 7.50"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('rate') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                            @error('rate') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Notes
                    </label>
                    <textarea wire:model="notes" 
                              rows="3"
                              placeholder="Optional notes about this rate..."
                              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror"></textarea>
                    @error('notes') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" 
                            wire:click="backToIndex"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $isEdit ? 'Update Rate' : 'Create Rate' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Delete Interest Rate</h3>
                <p class="text-sm text-gray-500 text-center mb-6">
                    Are you sure you want to delete this interest rate? This action cannot be undone.
                </p>
                <div class="flex space-x-3">
                    <button wire:click="hideDeleteModal" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="delete" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Click outside to close dropdowns --}}
<script>
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        @this.call('closeDropdowns');
    }
});
</script>