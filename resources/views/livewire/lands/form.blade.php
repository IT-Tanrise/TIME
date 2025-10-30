{{-- resources/views/livewire/lands/form.blade.php --}}
<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">
                    {{ $isEdit ? 'Edit Land' : 'Add New Land' }}
                </h2>
                <button wire:click="backToIndex" 
                        class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Location -->
                        <div>
                            <label for="lokasi_lahan" class="block text-sm font-medium text-gray-700">Location *</label>
                            <input type="text" 
                                   wire:model="lokasi_lahan" 
                                   id="lokasi_lahan"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('lokasi_lahan') border-red-300 @enderror">
                            @error('lokasi_lahan') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Business Unit with Dropdown Search -->
                        <div class="relative">
                            <label for="businessUnitSearch" class="block text-sm font-medium text-gray-700">Business Unit *</label>
                            <input type="text" 
                                wire:model.live.debounce.300ms="businessUnitSearch"
                                wire:focus="searchBusinessUnits"
                                id="businessUnitSearch"
                                placeholder="Search or select business unit..."
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('business_unit_id') border-red-300 @enderror"
                                autocomplete="off"
                                @if($filterByBusinessUnit && !$allowBusinessUnitChange) readonly @endif>
                            
                            @if($showBusinessUnitDropdown && (!$filterByBusinessUnit || $allowBusinessUnitChange))
                                <div class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-sm ring-1 ring-black ring-opacity-5 focus:outline-none"
                                     style="max-height: 240px; overflow-y: auto; overflow-x: hidden;"
                                     wire:click.stop>
                                    @php
                                        $businessUnits = $this->getFilteredBusinessUnits();
                                    @endphp
                                    
                                    @if(empty($businessUnitSearch ?? '') && $businessUnits->count() > 0)
                                        <div class="px-3 py-2 text-xs text-blue-600 bg-blue-50 border-b border-blue-100 sticky top-0 z-10">
                                            Showing available business units - start typing to filter
                                        </div>
                                    @endif
                                    
                                    <div class="max-h-48 overflow-y-auto">
                                        @forelse($businessUnits as $unit)
                                            <button type="button"
                                                    wire:click.stop="selectBusinessUnit({{ $unit->id }}, '{{ addslashes($unit->name) }}')"
                                                    class="w-full text-left px-3 py-2 text-sm text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none
                                                        @if($business_unit_id == $unit->id) bg-blue-50 text-blue-900 font-medium @endif">
                                                {{ $unit->name }} ({{ $unit->code }})
                                                @if($business_unit_id == $unit->id)
                                                    <span class="float-right text-blue-600">✓</span>
                                                @endif
                                            </button>
                                        @empty
                                            <div class="px-3 py-2 text-sm text-gray-500">No business units found</div>
                                        @endforelse
                                    </div>
                                    
                                    @if($businessUnits->count() >= 20)
                                        <div class="px-3 py-2 text-xs text-gray-500 bg-gray-50 border-t border-gray-100 sticky bottom-0">
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
                            
                            @error('business_unit_id') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>

                        

                        <!-- Address -->
                        <div>
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea wire:model="alamat" 
                                      id="alamat"
                                      rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('alamat') border-red-300 @enderror"></textarea>
                            @error('alamat') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Google Maps Link -->
                        <div>
                            <label for="link_google_maps" class="block text-sm font-medium text-gray-700">Google Maps Link</label>
                            <input type="url" 
                                   wire:model="link_google_maps" 
                                   id="link_google_maps"
                                   placeholder="https://maps.google.com/..."
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('link_google_maps') border-red-300 @enderror">
                            @error('link_google_maps') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- City/Regency -->
                        <div>
                            <label for="kota_kabupaten" class="block text-sm font-medium text-gray-700">City/Regency</label>
                            <input type="text" 
                                   wire:model="kota_kabupaten" 
                                   id="kota_kabupaten"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('kota_kabupaten') border-red-300 @enderror">
                            @error('kota_kabupaten') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>

                        
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Year -->
                        <div>
                            <label for="tahun_perolehan" class="block text-sm font-medium text-gray-700">Year Acquired *</label>
                            <input type="number" 
                                   wire:model="tahun_perolehan" 
                                   id="tahun_perolehan"
                                   min="1900" 
                                   max="{{ date('Y') + 10 }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tahun_perolehan') border-red-300 @enderror">
                            @error('tahun_perolehan') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select wire:model="status" 
                                    id="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('status') border-red-300 @enderror">
                                <option value="">Select Status</option>
                                @foreach($this->getStatusOptions() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('status') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>
                        <!-- NJOP - WITH FORMATTING -->
                        <div>
                            <label for="njop" class="block text-sm font-medium text-gray-700">NJOP (Rp)</label>
                            <input type="text" 
                                   wire:model="njop_display"
                                   id="njop"
                                   placeholder="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('njop') border-red-300 @enderror"
                                   x-data 
                                   x-on:input="
                                       let value = $event.target.value.replace(/[^\d]/g, '');
                                       if (value) {
                                           $event.target.value = new Intl.NumberFormat('id-ID').format(value);
                                           @this.set('njop', parseInt(value));
                                       } else {
                                           $event.target.value = '';
                                           @this.set('njop', '');
                                       }
                                   ">
                            @error('njop') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>
                        <!-- Estimated Market Price - WITH FORMATTING -->
                        <div>                            
                            <label for="est_harga_pasar" class="block text-sm font-medium text-gray-700">Estimated Market Price (Rp)</label>
                            <input type="text" 
                                   wire:model="nilai_perolehan_display"
                                   id="nilai_perolehan"
                                   placeholder="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nilai_perolehan') border-red-300 @enderror"
                                   x-data 
                                   x-on:input="
                                       let value = $event.target.value.replace(/[^\d]/g, '');
                                       if (value) {
                                           $event.target.value = new Intl.NumberFormat('id-ID').format(value);
                                           @this.set('est_harga_pasar', parseInt(value));
                                       } else {
                                           $event.target.value = '';
                                           @this.set('est_harga_pasar', '');
                                       }
                                   ">
                            @error('est_harga_pasar') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="keterangan" 
                                      id="keterangan"
                                      rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('keterangan') border-red-300 @enderror"></textarea>
                            @error('keterangan') 
                                <span class="text-red-500 text-xs">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" 
                            wire:click="backToIndex"
                            class="px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray active:bg-gray-400 transition ease-in-out duration-150">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-600 transition ease-in-out duration-150">
                        {{ $isEdit ? 'Update Land' : 'Create Land' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Click outside to close dropdowns -->
<script>
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        @this.call('closeDropdowns');
    }
});
</script>

<!-- Custom Styles for Scrollable Dropdowns -->
<style>
.dropdown-container {
    max-height: 240px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

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

[style*="max-height"][style*="overflow-y: auto"] {
    scrollbar-width: thin;
    scrollbar-color: #c1c1c1 #f1f1f1;
}
</style>