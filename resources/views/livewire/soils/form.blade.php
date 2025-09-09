{{-- resources/views/livewire/soils/form.blade.php --}}
<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-4 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $isEdit ? 'Edit Soil Record Details' : 'Create New Soil Records' }}
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
                            <!-- Land with Dropdown Search -->
                            <div class="relative">
                                <label for="landSearch" class="block text-xs font-medium text-gray-700">Land *</label>
                                <input type="text" 
                                       wire:model.live.debounce.300ms="landSearch"
                                       wire:focus="searchLands"
                                       id="landSearch"
                                       placeholder="Search or select land..."
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs @error('land_id') border-red-300 @enderror"
                                       autocomplete="off">
                                
                                @if($showLandDropdown)
                                    <div class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-xs ring-1 ring-black ring-opacity-5 focus:outline-none"
                                         style="max-height: 240px; overflow-y: auto; overflow-x: hidden;"
                                         wire:click.stop>
                                        @php
                                            $lands = $this->getFilteredLands();
                                        @endphp
                                        
                                        @if(empty($landSearch ?? '') && $lands->count() > 0)
                                            <div class="px-3 py-1.5 text-xs text-blue-600 bg-blue-50 border-b border-blue-100 sticky top-0 z-10">
                                                Showing available lands - start typing to filter
                                            </div>
                                        @endif
                                        
                                        <div class="max-h-48 overflow-y-auto">
                                            @forelse($lands as $land)
                                                <button type="button"
                                                        wire:click.stop="selectLand({{ $land->id }}, '{{ addslashes($land->lokasi_lahan) }}')"
                                                        class="w-full text-left px-3 py-1.5 text-xs text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                                                    {{ $land->lokasi_lahan }}
                                                </button>
                                            @empty
                                                <div class="px-3 py-1.5 text-xs text-gray-500">No lands found</div>
                                            @endforelse
                                        </div>
                                        
                                        @if($lands->count() >= 20)
                                            <div class="px-3 py-1.5 text-xs text-gray-500 bg-gray-50 border-t border-gray-100 sticky bottom-0">
                                                Showing first 20 results - type to search for more
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                @error('land_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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

                    <!-- Soil Details Section -->
                    <div class="bg-green-50 p-3 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-medium text-gray-900">Soil Details</h3>
                            @if(!$isEdit)
                            <button type="button" 
                                    wire:click="addSoilDetail"
                                    class="inline-flex items-center px-2.5 py-1.5 bg-green-600 border border-transparent rounded-md text-xs font-semibold text-white hover:bg-green-700">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Soil Detail
                            </button>
                            @endif
                        </div>

                        @if(is_array($soilDetails) && count($soilDetails) > 0)
                            <div class="space-y-4">
                                @foreach($soilDetails as $index => $detail)
                                    <div class="border border-gray-200 rounded-lg p-3 space-y-3">
                                        <div class="flex justify-between items-center">
                                            <h4 class="text-xs font-medium text-gray-800">
                                                Soil Detail {{ $index + 1 }}
                                            </h4>
                                            @if($index > 0)
                                                <button type="button" wire:click="removeSoilDetail({{ $index }})" 
                                                        class="text-red-600 hover:text-red-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                                            <!-- Seller Name -->
                                            <div class="relative">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Seller Name *</label>
                                                <input wire:model.live="sellerNameSearch.{{ $index }}" 
                                                    wire:focus="searchSellerNames({{ $index }})"
                                                    type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.nama_penjual') border-red-500 @enderror">
                                                
                                                @if(isset($showSellerNameDropdown[$index]) && $showSellerNameDropdown[$index])
                                                    <div class="absolute z-30 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg"
                                                         style="max-height: 200px; overflow-y: auto; overflow-x: hidden;"
                                                         wire:click.stop>
                                                        <div class="max-h-48 overflow-y-auto">
                                                            @forelse($this->getFilteredSellerNames($index) as $seller)
                                                                <button type="button"
                                                                        wire:click="selectSellerName({{ $index }}, '{{ addslashes($seller->nama_penjual) }}')" 
                                                                        class="w-full text-left px-2 py-1.5 hover:bg-gray-100 cursor-pointer text-xs focus:bg-gray-100 focus:outline-none">
                                                                    {{ $seller->nama_penjual }}
                                                                </button>
                                                            @empty
                                                                <div class="px-2 py-1.5 text-xs text-gray-500">No sellers found</div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @error('soilDetails.'.$index.'.nama_penjual') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Seller Address -->
                                            <div class="relative">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Seller Address *</label>
                                                <input wire:model.live="sellerAddressSearch.{{ $index }}" 
                                                    wire:focus="searchSellerAddresses({{ $index }})"
                                                    type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.alamat_penjual') border-red-500 @enderror">
                                                
                                                @if(isset($showSellerAddressDropdown[$index]) && $showSellerAddressDropdown[$index])
                                                    <div class="absolute z-30 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg"
                                                         style="max-height: 200px; overflow-y: auto; overflow-x: hidden;"
                                                         wire:click.stop>
                                                        <div class="max-h-48 overflow-y-auto">
                                                            @forelse($this->getFilteredSellerAddresses($index) as $seller)
                                                                <button type="button"
                                                                        wire:click="selectSellerAddress({{ $index }}, '{{ addslashes($seller->alamat_penjual) }}')" 
                                                                        class="w-full text-left px-2 py-1.5 hover:bg-gray-100 cursor-pointer text-xs focus:bg-gray-100 focus:outline-none">
                                                                    {{ $seller->alamat_penjual }}
                                                                </button>
                                                            @empty
                                                                <div class="px-2 py-1.5 text-xs text-gray-500">No addresses found</div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @error('soilDetails.'.$index.'.alamat_penjual') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- PPJB Number -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">PPJB/AJB Number *</label>
                                                <input wire:model="soilDetails.{{ $index }}.nomor_ppjb" type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.nomor_ppjb') border-red-500 @enderror">
                                                @error('soilDetails.'.$index.'.nomor_ppjb') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- PPJB Date -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">PPJB/ AJB Date *</label>
                                                <input wire:model="soilDetails.{{ $index }}.tanggal_ppjb" type="date" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.tanggal_ppjb') border-red-500 @enderror">
                                                @error('soilDetails.'.$index.'.tanggal_ppjb') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Land Location -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Land Location *</label>
                                                <input wire:model="soilDetails.{{ $index }}.letak_tanah" type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.letak_tanah') border-red-500 @enderror">
                                                @error('soilDetails.'.$index.'.letak_tanah') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Area -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Area (m²) *</label>
                                                <input wire:model.live="soilDetails.{{ $index }}.luas_display" 
                                                    type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.luas') border-red-500 @enderror"
                                                    x-data 
                                                    x-on:input="
                                                        let value = $event.target.value.replace(/[^\d]/g, '');
                                                        if (value) {
                                                            $event.target.value = new Intl.NumberFormat('id-ID').format(value);
                                                            @this.set('soilDetails.{{ $index }}.luas', parseInt(value));
                                                        } else {
                                                            $event.target.value = '';
                                                            @this.set('soilDetails.{{ $index }}.luas', '');
                                                        }
                                                    ">
                                                @error('soilDetails.'.$index.'.luas') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Price (with thousand separator) -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Price (Rp) *</label>
                                                <input wire:model.live="soilDetails.{{ $index }}.harga_display" 
                                                    type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.harga') border-red-500 @enderror"
                                                    x-data 
                                                    x-on:input="
                                                        let value = $event.target.value.replace(/[^\d]/g, '');
                                                        if (value) {
                                                            $event.target.value = new Intl.NumberFormat('id-ID').format(value);
                                                            @this.set('soilDetails.{{ $index }}.harga', parseInt(value));
                                                        } else {
                                                            $event.target.value = '';
                                                            @this.set('soilDetails.{{ $index }}.harga', '');
                                                        }
                                                    ">
                                                @error('soilDetails.'.$index.'.harga') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Ownership Proof -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Ownership Proof *</label>
                                                <select wire:model="soilDetails.{{ $index }}.bukti_kepemilikan" 
                                                        class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.bukti_kepemilikan') border-red-500 @enderror">
                                                    <option value="">Select Ownership Proof</option>
                                                    @foreach($this->getBuktiKepemilikanOptions() as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                                @error('soilDetails.'.$index.'.bukti_kepemilikan') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Ownership Proof Details -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Ownership Proof Details *</label>
                                                <input wire:model="soilDetails.{{ $index }}.bukti_kepemilikan_details" type="text" 
                                                    placeholder="Certificate number, etc."
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.bukti_kepemilikan_details') border-red-500 @enderror">
                                                @error('soilDetails.'.$index.'.bukti_kepemilikan_details') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Owner Name -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Owner Name *</label>
                                                <input wire:model="soilDetails.{{ $index }}.atas_nama" type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.atas_nama') border-red-500 @enderror">
                                                @error('soilDetails.'.$index.'.atas_nama') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- NOP PBB -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">NOP PBB</label>
                                                <input wire:model="soilDetails.{{ $index }}.nop_pbb" type="text" 
                                                    placeholder="Enter NOP PBB number"
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.nop_pbb') border-red-500 @enderror">
                                                @error('soilDetails.'.$index.'.nop_pbb') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Nama Notaris/PPAT -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Notaris/PPAT</label>
                                                <input wire:model="soilDetails.{{ $index }}.nama_notaris_ppat" type="text" 
                                                    placeholder="Enter Notaris/PPAT name"
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.nama_notaris_ppat') border-red-500 @enderror">
                                                @error('soilDetails.'.$index.'.nama_notaris_ppat') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Notes (full width) -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                                            <textarea wire:model="soilDetails.{{ $index }}.keterangan" rows="2" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs @error('soilDetails.'.$index.'.keterangan') border-red-500 @enderror"></textarea>
                                            @error('soilDetails.'.$index.'.keterangan') 
                                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
                            {{ $isEdit ? 'Update' : 'Create' }} 
                            @if(!$isEdit && count($soilDetails) > 1)
                                ({{ count($soilDetails) }} Records)
                            @else
                                Soil Record
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Styles for Scrollable Dropdowns -->
<style>
/* Ensure proper scrolling for dropdown containers */
.dropdown-container {
    max-height: 240px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

/* Custom scrollbar styling for better UX */
.dropdown-container::-webkit-scrollbar {
    width: 6px;
}

.dropdown-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.dropdown-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.dropdown-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Force scrolling on all dropdown containers */
[style*="max-height"][style*="overflow-y: auto"] {
    scrollbar-width: thin;
    scrollbar-color: #c1c1c1 #f1f1f1;
}

/* Ensure dropdown items don't break layout */
.dropdown-item {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

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

// Enhanced click outside handler with better event handling
document.addEventListener('click', function(event) {
    // Only close dropdowns if clicking outside of any dropdown or input
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
    
    // If clicked outside, close all dropdowns
    if (!clickedInside && window.Livewire) {
        window.Livewire.dispatch('closeDropdowns');
    }
});

// Prevent form submission on search button clicks
document.addEventListener('DOMContentLoaded', function() {
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

// Add keyboard navigation for dropdowns
document.addEventListener('keydown', function(event) {
    const activeDropdown = document.querySelector('[style*="max-height"][style*="overflow-y: auto"]:not([style*="display: none"])');
    
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
            // Scroll item into view if needed
            items[nextIndex].scrollIntoView({ block: 'nearest' });
        }
    }
    
    // Handle Enter key to select focused item
    if (event.key === 'Enter' && document.activeElement.matches('button[wire\\:click*="select"]')) {
        event.preventDefault();
        document.activeElement.click();
    }
    
    // Handle Escape key to close dropdowns
    if (event.key === 'Escape' && window.Livewire) {
        window.Livewire.dispatch('closeDropdowns');
    }
});
</script>