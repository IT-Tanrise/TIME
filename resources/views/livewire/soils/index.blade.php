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
        <div class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <div class="flex items-center space-x-3">
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
                        @can('soils.edit')
                        <button wire:click="showCreateForm" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add New
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Filters Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            @if(!$this->isFiltered())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <!-- Search -->
                        <div>
                            <input type="text" 
                                wire:model.live="search" 
                                placeholder="Search by seller, buyer, location..."
                                class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Business Unit Filter -->
                        <div class="relative">
                            <div class="relative">
                                <input type="text" 
                                    wire:model.live="filterBusinessUnitSearch"
                                    wire:click="openBusinessUnitFilterDropdown"
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
                                    <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-auto">
                                        @php
                                            $filteredBusinessUnits = $this->getFilteredBusinessUnitsForFilter();
                                        @endphp
                                        
                                        @if($filteredBusinessUnits->count() > 0)
                                            @foreach($filteredBusinessUnits as $unit)
                                                <button type="button"
                                                        wire:click="selectBusinessUnitFilter({{ $unit->id }}, '{{ $unit->name }}')"
                                                        class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-b-0">
                                                    <div class="font-medium">{{ $unit->name }}</div>
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

                        <!-- Land Filter -->
                        <div class="relative">
                            <div class="relative">
                                <input type="text" 
                                    wire:model.live="filterLandSearch"
                                    wire:click="openLandFilterDropdown"
                                    placeholder="Search lands..."
                                    class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-8"
                                    autocomplete="off">
                                
                                @if($filterLand)
                                    <button type="button" 
                                            wire:click="clearLandFilter"
                                            class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                                
                                @if($showLandFilterDropdown)
                                    <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-auto">
                                        @php
                                            $filteredLands = $this->getFilteredLandsForFilter();
                                        @endphp
                                        
                                        @if($filteredLands->count() > 0)
                                            @foreach($filteredLands as $land)
                                                <button type="button"
                                                        wire:click="selectLandFilter({{ $land->id }}, '{{ $land->lokasi_lahan }}')"
                                                        class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-b-0">
                                                    {{ $land->lokasi_lahan }}
                                                </button>
                                            @endforeach
                                        @else
                                            <div class="px-3 py-2 text-sm text-gray-500">No lands found</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Reset Button -->
                        <div>
                            <button wire:click="resetFilters" 
                                    class="w-full px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-4">
                    <div class="flex justify-between items-center">
                        <input wire:model.live="search" 
                            type="text" 
                            placeholder="Search soils by no. PPJB/ AJB, buyer, location..." 
                            class="flex-1 px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 mr-4">
                        @if($businessUnit)
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $businessUnit->name }}
                            </span>
                        @endif
                    </div>
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

            <!-- Compact Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
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
                                                        <button wire:click="showEditForm({{ $soil->id }}, 'costs')" 
                                                                class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            Manage Costs
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Delete Button -->
                                             @can('soils.delete')
                                            <button wire:click="delete({{ $soil->id }})" 
                                                    wire:confirm="Are you sure you want to delete this soil record?"
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
    @endif
</div>
<!-- Enhanced JavaScript for Dropdown Behavior -->
<script>
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
    
    // Handle Escape key to close search dropdowns only
    if (event.key === 'Escape' && window.Livewire) {
        window.Livewire.dispatch('closeDropdowns');
    }
});
</script>