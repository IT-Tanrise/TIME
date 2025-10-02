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
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Acquisition Value - WITH FORMATTING -->
                        <div>
                            <label for="nilai_perolehan" class="block text-sm font-medium text-gray-700">Acquisition Value (Rp) *</label>
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
                                           @this.set('nilai_perolehan', parseInt(value));
                                       } else {
                                           $event.target.value = '';
                                           @this.set('nilai_perolehan', '');
                                       }
                                   ">
                            @error('nilai_perolehan') 
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
                                   wire:model="est_harga_pasar_display"
                                   id="est_harga_pasar"
                                   placeholder="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('est_harga_pasar') border-red-300 @enderror"
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