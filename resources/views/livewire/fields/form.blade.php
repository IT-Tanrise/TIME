<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="hideFormView"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-white rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($isEdit)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                @endif
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">
                                {{ $isEdit ? 'Edit Field' : 'Create New Field' }}
                            </h3>
                            <p class="text-sm text-blue-100">
                                {{ $isEdit ? 'Update field information' : 'Add a new field to the system' }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="hideFormView" class="text-white hover:text-blue-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="save">
                <div class="px-6 py-6 space-y-5">
                    <!-- Nama Bidang -->
                    <div>
                        <label for="nama_bidang" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Bidang <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nama_bidang"
                            wire:model="nama_bidang"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Enter field name">
                        @error('nama_bidang')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Business Unit -->
                    <div>
                        <label for="business_unit_search" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Business Unit <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="business_unit_search"
                                wire:model.live="businessUnitSearch"
                                wire:focus="$set('showBusinessUnitDropdown', true)"
                                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Search business unit..."
                                autocomplete="off">
                            
                            @if($showBusinessUnitDropdown && count($this->getFilteredBusinessUnits()) > 0)
                                <div class="absolute z-10 w-full mt-1 overflow-auto bg-white border border-gray-300 rounded-lg shadow-lg max-h-60">
                                    @foreach($this->getFilteredBusinessUnits() as $unit)
                                        <button 
                                            type="button"
                                            wire:click="selectBusinessUnit({{ $unit->id }})"
                                            class="w-full px-4 py-2.5 text-left hover:bg-blue-50 transition flex items-center justify-between group">
                                            <div>
                                                <div class="font-medium text-gray-900 group-hover:text-blue-600">
                                                    {{ $unit->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Code: {{ $unit->code }}
                                                </div>
                                            </div>
                                            @if($business_unit_id == $unit->id)
                                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @error('business_unit_id')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        
                        @if($business_unit_id)
                            @php
                                $selectedUnit = \App\Models\BusinessUnit::find($business_unit_id);
                            @endphp
                            @if($selectedUnit)
                                <div class="flex items-center gap-2 p-3 mt-2 border border-blue-200 rounded-lg bg-blue-50">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-blue-900">{{ $selectedUnit->name }}</div>
                                        <div class="text-xs text-blue-700">Code: {{ $selectedUnit->code }}</div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if($isEdit)
                    <!-- Status (Only for Edit) -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Status
                        </label>
                        <select 
                            id="status"
                            wire:model="status"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Nomor Bidang (Read-only for Edit) -->
                    <div>
                        <label for="nomor_bidang" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nomor Bidang
                        </label>
                        <input 
                            type="text" 
                            id="nomor_bidang"
                            value="{{ $nomor_bidang }}"
                            readonly
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                        <p class="mt-1.5 text-xs text-gray-500">Field number is auto-generated and cannot be changed</p>
                    </div>
                    @else
                    <!-- Info for Create -->
                    <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <h4 class="mb-1 text-sm font-medium text-blue-900">Information</h4>
                                <ul class="space-y-1 text-xs text-blue-800">
                                    <li>• Nomor bidang will be auto-generated after creation</li>
                                    <li>• New fields will have "Pending" status by default</li>
                                    @if(!auth()->user()->can('field.approval'))
                                        <li>• Your request will require approval before activation</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button 
                        type="button"
                        wire:click="hideFormView"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $isEdit ? 'Update Field' : 'Create Field' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>