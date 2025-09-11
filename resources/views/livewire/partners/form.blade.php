{{-- resources/views/livewire/partners/form.blade.php --}}
<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-4 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $isEdit ? 'Edit Ownership Details' : 'Create New Ownership' }}
                </h2>
                <button wire:click="backToIndex" 
                        class="inline-flex items-center px-3 py-1.5 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </button>
            </div>

            <!-- Form -->
            <form wire:submit="save">
                <div class="space-y-4">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Name -->
                            <!-- Partner Name with Dropdown Search -->
                            <div class="relative">
                                <label for="partnerNameSearch" class="block text-xs font-medium text-gray-700">Partner Name *</label>
                                <input type="text" 
                                    wire:model.live.debounce.300ms="partnerNameSearch"
                                    wire:focus="searchPartnerNames"
                                    id="partnerNameSearch"
                                    placeholder="Search or enter partner name..."
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs @error('name') border-red-300 @enderror"
                                    autocomplete="off">
                                
                                @if($showPartnerNameDropdown)
                                    <div class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-xs ring-1 ring-black ring-opacity-5 focus:outline-none"
                                        style="max-height: 240px; overflow-y: auto; overflow-x: hidden;"
                                        wire:click.stop>
                                        @php
                                            $partnerNames = $this->getFilteredPartnerNames();
                                        @endphp
                                        
                                        @if(empty($partnerNameSearch ?? '') && $partnerNames->count() > 0)
                                            <div class="px-3 py-1.5 text-xs text-blue-600 bg-blue-50 border-b border-blue-100 sticky top-0 z-10">
                                                Showing existing partner names - start typing to filter
                                            </div>
                                        @endif
                                        
                                        <div class="max-h-48 overflow-y-auto">
                                            @forelse($partnerNames as $partner)
                                                <button type="button"
                                                        wire:click.stop="selectPartnerName('{{ addslashes($partner->name) }}')"
                                                        class="w-full text-left px-3 py-1.5 text-xs text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                                                    {{ $partner->name }}
                                                </button>
                                            @empty
                                                <div class="px-3 py-1.5 text-xs text-gray-500">No partner names found</div>
                                            @endforelse
                                        </div>
                                        
                                        @if($partnerNames->count() >= 20)
                                            <div class="px-3 py-1.5 text-xs text-gray-500 bg-gray-50 border-t border-gray-100 sticky bottom-0">
                                                Showing first 20 results - type to search for more
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <!-- Business Unit with Dropdown Search -->
                            <div class="relative">
                                <label for="businessUnitSearch" class="block text-xs font-medium text-gray-700">Business Unit *</label>
                                <input type="text" 
                                    wire:model.live.debounce.300ms="businessUnitSearch"
                                    wire:focus="searchBusinessUnits"
                                    id="businessUnitSearch"
                                    placeholder="Search or select business unit..."
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs @error('business_unit_id') border-red-300 @enderror"
                                    autocomplete="off"
                                    @if($filterByBusinessUnit && !$allowBusinessUnitChange) readonly @endif>
                                
                                @if($showBusinessUnitDropdown && (!$filterByBusinessUnit || $allowBusinessUnitChange))
                                    <div class="absolute z-40 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-xs ring-1 ring-black ring-opacity-5 focus:outline-none"
                                         style="max-height: 240px; overflow-y: auto; overflow-x: hidden;"
                                         wire:click.stop>
                                        @php
                                            $businessUnits = $this->getFilteredBusinessUnits();
                                        @endphp
                                        
                                        @if(empty($businessUnitSearch ?? '') && $businessUnits->count() > 0)
                                            <div class="px-3 py-1.5 text-xs text-blue-600 bg-blue-50 border-b border-blue-100 sticky top-0 z-10">
                                                Showing available business units - start typing to filter
                                            </div>
                                        @endif
                                        
                                        <div class="max-h-48 overflow-y-auto">
                                            @forelse($businessUnits as $unit)
                                                <button type="button"
                                                        wire:click.stop="selectBusinessUnit({{ $unit->id }}, '{{ addslashes($unit->name) }}')"
                                                        class="w-full text-left px-3 py-1.5 text-xs text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none
                                                            @if($business_unit_id == $unit->id) bg-blue-50 text-blue-900 font-medium @endif">
                                                    {{ $unit->name }}
                                                    @if($business_unit_id == $unit->id)
                                                        <span class="float-right text-blue-600">✓</span>
                                                    @endif
                                                </button>
                                            @empty
                                                <div class="px-3 py-1.5 text-xs text-gray-500">No business units found</div>
                                            @endforelse
                                        </div>
                                        
                                        @if($businessUnits->count() >= 20)
                                            <div class="px-3 py-1.5 text-xs text-gray-500 bg-gray-50 border-t border-gray-100 sticky bottom-0">
                                                Showing first 20 results - type to search for more
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($filterByBusinessUnit && !$allowBusinessUnitChange)
                                    <p class="mt-1 text-xs text-green-600">
                                        Pre-selected based on current filter
                                        <button type="button" 
                                                wire:click="allowBusinessUnitChangeFunc" 
                                                class="ml-2 text-blue-600 underline hover:text-blue-800">
                                            Change
                                        </button>
                                    </p>
                                @elseif($filterByBusinessUnit && $allowBusinessUnitChange)
                                    <p class="mt-1 text-xs text-amber-600">
                                        You can now change the business unit
                                        <button type="button" 
                                                wire:click="lockBusinessUnit" 
                                                class="ml-2 text-green-600 underline hover:text-green-800">
                                            Keep Current
                                        </button>
                                    </p>
                                @endif
                                
                                @error('business_unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Ownership Details -->
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Ownership Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Percentage -->
                            <div>
                                <label for="percentage" class="block text-xs font-medium text-gray-700">Percentage *</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input wire:model="percentage" 
                                           type="number" 
                                           id="percentage"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           class="block w-full px-2 py-1.5 pr-8 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-xs @error('percentage') border-red-300 @enderror">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <span class="text-gray-500 text-xs">%</span>
                                    </div>
                                </div>
                                @error('percentage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Lembar Saham with Fixed Formatting -->
                            <!-- Lembar Saham with Thousand Separator -->
                            <div>
                                <label for="lembar_saham" class="block text-xs font-medium text-gray-700">Lembar Saham</label>
                                <input wire:model.live="lembar_saham_display" 
                                    type="text" 
                                    placeholder="e.g. 1.000.000"
                                    class="mt-1 block w-full px-2 py-1.5 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs @error('lembar_saham') border-red-300 @enderror"
                                    x-data 
                                    x-on:input="
                                        let value = $event.target.value.replace(/[^\d]/g, '');
                                        if (value) {
                                            $event.target.value = new Intl.NumberFormat('id-ID').format(value);
                                            @this.set('lembar_saham', parseInt(value));
                                        } else {
                                            $event.target.value = '';
                                            @this.set('lembar_saham', '');
                                        }
                                    ">
                                @error('lembar_saham') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-2">
                        <button type="button" 
                                wire:click="backToIndex"
                                class="px-3 py-1.5 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray active:bg-gray-400 transition ease-in-out duration-150">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-600 transition ease-in-out duration-150">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $isEdit ? 'Update Ownership' : 'Create Ownership' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
