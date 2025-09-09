<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
    <div class="sm:flex sm:items-start">
        <div class="w-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $editMode ? 'Edit' : 'Add New' }} Land Rental
                </h3>
                <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form wire:submit.prevent="save" class="space-y-6">
                {{-- Land Selection --}}
                <div>
                    <label for="land_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Land <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="rent.land_id" id="land_id" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent.land_id') border-red-500 @enderror">
                        <option value="">Choose a land...</option>
                        @foreach($availableLands as $land)
                            <option value="{{ $land->id }}">
                                {{ $land->lokasi_lahan }}
                                @if($land->associatedBusinessUnits && $land->associatedBusinessUnits->count() > 0)
                                    ({{ $land->associatedBusinessUnits->pluck('name')->join(', ') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('rent.land_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Area and Price --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="area_m2" class="block text-sm font-medium text-gray-700 mb-2">
                            Area (m²) <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="rent.area_m2" type="number" id="area_m2" min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent.area_m2') border-red-500 @enderror"
                               placeholder="Enter area in square meters">
                        @error('rent.area_m2')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="rent.price" type="number" id="price" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent.price') border-red-500 @enderror"
                               placeholder="Enter rental price">
                        @error('rent.price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tenant Information --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Tenant Information</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="nama_penyewa" class="block text-sm font-medium text-gray-700 mb-2">
                                Tenant Name <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="rent.nama_penyewa" type="text" id="nama_penyewa"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent.nama_penyewa') border-red-500 @enderror"
                                   placeholder="Enter tenant full name">
                            @error('rent.nama_penyewa')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alamat_penyewa" class="block text-sm font-medium text-gray-700 mb-2">
                                Tenant Address <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="rent.alamat_penyewa" id="alamat_penyewa" rows="2"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent.alamat_penyewa') border-red-500 @enderror"
                                      placeholder="Enter tenant address"></textarea>
                            @error('rent.alamat_penyewa')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nomor_handphone_penyewa" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="rent.nomor_handphone_penyewa" type="text" id="nomor_handphone_penyewa"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent.nomor_handphone_penyewa') border-red-500 @enderror"
                                   placeholder="Enter phone number (e.g., +62812345678)">
                            @error('rent.nomor_handphone_penyewa')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Rental Period --}}
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Rental Period</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_rent" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="rent.start_rent" type="date" id="start_rent"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent.start_rent') border-red-500 @enderror">
                            @error('rent.start_rent')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_rent" class="block text-sm font-medium text-gray-700 mb-2">
                                End Date <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="rent.end_rent" type="date" id="end_rent"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rent.end_rent') border-red-500 @enderror">
                            @error('rent.end_rent')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Reminder Settings --}}
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Reminder Settings</h4>
                    
                    <div>
                        <label for="reminder_period" class="block text-sm font-medium text-gray-700 mb-2">
                            Remind me before rental expires
                        </label>
                        <select wire:model="rent.reminder_period" id="reminder_period"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">No reminder</option>
                            <option value="1month">1 Month before</option>
                            <option value="1week">1 Week before</option>
                            <option value="3days">3 Days before</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">
                            Choose when you want to be reminded about the rental expiration
                        </p>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="closeForm"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $editMode ? 'Update' : 'Save' }} Rental
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>