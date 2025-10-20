<div class="bg-white shadow-md rounded-lg p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $isEdit ? 'Edit Land Certificate' : 'Create New Land Certificate' }}
        </h2>
    </div>

    <form wire:submit.prevent="save">
        <!-- Business Unit Selection -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Business Unit <span class="text-red-500">*</span>
            </label>
            <div class="relative" x-data="{ open: @entangle('showBusinessUnitDropdown') }">
                <input
                    type="text"
                    wire:model.live="businessUnitSearch"
                    wire:click="searchBusinessUnits"
                    @click.away="closeDropdowns"
                    placeholder="Search business unit..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    autocomplete="off"
                    @if($filterByBusinessUnit && !$allowBusinessUnitChange) readonly @endif
                />
                @error('business_unit_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

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

                <!-- Dropdown -->
                <div x-show="open" 
                     class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    @php $filteredBusinessUnits = $this->getFilteredBusinessUnits(); @endphp
                    @if($filteredBusinessUnits->count() > 0)
                        @foreach($filteredBusinessUnits as $unit)
                            <div wire:click="selectBusinessUnit({{ $unit->id }}, '{{ $unit->name }}')"
                                 class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100">
                                <div class="font-medium text-gray-900">{{ $unit->name }}</div>
                                <div class="text-xs text-gray-500">{{ $unit->code }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="px-4 py-3 text-sm text-gray-500">
                            No business units found
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Land Selection -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Land <span class="text-red-500">*</span>
            </label>
            <div class="relative" x-data="{ open: @entangle('showLandDropdown') }">
                <input
                    type="text"
                    wire:model.live="landSearch"
                    wire:click="searchLands"
                    @click.away="closeDropdowns"
                    placeholder="Search land by location..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    autocomplete="off"
                />
                @error('land_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Dropdown -->
                <div x-show="open" 
                     class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    @php $filteredLands = $this->getFilteredLands(); @endphp
                    @if($filteredLands->count() > 0)
                        @foreach($filteredLands as $land)
                            <div wire:click="selectLand({{ $land->id }}, '{{ $land->lokasi_lahan }}')"
                                 class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100">
                                <div class="font-medium text-gray-900">{{ $land->lokasi_lahan }}</div>
                                @if($land->businessUnit)
                                    <div class="text-xs text-gray-500">{{ $land->businessUnit->name }}</div>
                                @endif
                                @if($land->kota_kabupaten)
                                    <div class="text-xs text-gray-500">{{ $land->kota_kabupaten }}</div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="px-4 py-3 text-sm text-gray-500">
                            No lands found
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Certificate Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Certificate Type <span class="text-red-500">*</span>
                </label>
                <div class="relative" x-data="{ open: @entangle('showCertificateTypeDropdown') }">
                    <input
                        type="text"
                        wire:model.live="certificateTypeSearch"
                        wire:click="searchCertificateTypes"
                        @click.away="closeDropdowns"
                        placeholder="Search certificate type..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        autocomplete="off"
                    />
                    @error('certificate_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Dropdown -->
                    <div x-show="open" 
                         class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        @php $filteredTypes = $this->getFilteredCertificateTypes(); @endphp
                        @if(count($filteredTypes) > 0)
                            @foreach($filteredTypes as $key => $label)
                                <div wire:click="selectCertificateType('{{ $key }}', '{{ $label }}')"
                                     class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100">
                                    <div class="font-medium text-gray-900">{{ $label }}</div>
                                    <div class="text-xs text-gray-500">{{ $key }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-4 py-3 text-sm text-gray-500">
                                No certificate types found
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Certificate Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Certificate Number <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model="certificate_number"
                    placeholder="e.g., 0123456"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                @error('certificate_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Issued Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Issued Date
                </label>
                <input
                    type="date"
                    wire:model="issued_date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                @error('issued_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Expired Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Expired Date
                </label>
                <input
                    type="date"
                    wire:model="expired_date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                @error('expired_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Issued By -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Issued By (BPN Office)
                </label>
                <input
                    type="text"
                    wire:model="issued_by"
                    placeholder="e.g., BPN Kota Surabaya"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
                @error('issued_by')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    wire:model="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    @foreach(\App\Models\LandCertificate::getStatusOptions() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Notes -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Notes
            </label>
            <textarea
                wire:model="notes"
                rows="3"
                placeholder="Additional notes about this certificate..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Soil Selection -->
        @if($land_id)
            <div class="mt-6 space-y-4">
                <!-- Available Soils -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Available Soils for This Certificate
                        </label>
                        @php 
                            $availableSoils = $this->getAvailableSoils();
                            $selectedCount = count($selectedSoils ?? []);
                        @endphp
                        <span class="text-xs text-gray-500">
                            {{ $availableSoils->count() }} available 
                            @if($selectedCount > 0)
                                | {{ $selectedCount }} selected
                            @endif
                        </span>
                    </div>
                    
                    <!-- Search Filter for Available Soils -->
                    @if($availableSoils->count() > 0 || !empty($soilSearchAvailable))
                        <div class="mb-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="soilSearchAvailable"
                                    placeholder="Search by location or seller name..."
                                    class="block w-full pl-10 pr-10 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                                @if(!empty($soilSearchAvailable))
                                    <button 
                                        type="button"
                                        wire:click="$set('soilSearchAvailable', '')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @if(!empty($soilSearchAvailable))
                                <p class="mt-1 text-xs text-gray-500">
                                    Filtering by: "{{ $soilSearchAvailable }}"
                                </p>
                            @endif
                        </div>
                    @endif
                    
                    @if($availableSoils->count() > 0)
                        <div class="border border-gray-300 rounded-lg p-4 max-h-80 overflow-y-auto bg-white">
                            <div class="space-y-2">
                                @foreach($availableSoils as $soil)
                                    <label class="flex items-start p-3 hover:bg-blue-50 rounded-lg border border-transparent hover:border-blue-200 cursor-pointer transition">
                                        <input
                                            type="checkbox"
                                            wire:model="selectedSoils"
                                            value="{{ $soil->id }}"
                                            id="soil-{{ $soil->id }}"
                                            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">{{ $soil->letak_tanah }}</div>
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        <span class="inline-flex items-center">
                                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                            </svg>
                                                            Seller: {{ $soil->nama_penjual }}
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        <span class="inline-flex items-center mr-3">
                                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                                            </svg>
                                                            Area: {{ number_format($soil->luas, 0, ',', '.') }} m²
                                                        </span>
                                                        <span class="inline-flex items-center">
                                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                            Owner: {{ $soil->atas_nama }}
                                                        </span>
                                                    </div>
                                                    @if($soil->bukti_kepemilikan)
                                                        <div class="mt-1">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                                {{ $soil->bukti_kepemilikan }}
                                                                @if($soil->bukti_kepemilikan_details)
                                                                    - {{ $soil->bukti_kepemilikan_details }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('selectedSoils')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @else
                        <div class="border border-gray-300 rounded-lg p-6 bg-gray-50 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if(!empty($soilSearchAvailable))
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                @endif
                            </svg>
                            <p class="mt-2 text-sm font-medium text-gray-900">
                                @if(!empty($soilSearchAvailable))
                                    No Soils Match Your Search
                                @else
                                    No Available Soils
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                @if(!empty($soilSearchAvailable))
                                    Try adjusting your search term or 
                                    <button type="button" wire:click="$set('soilSearchAvailable', '')" class="text-blue-600 hover:text-blue-800 underline">
                                        clear the filter
                                    </button>
                                @else
                                    All soils from this land are already assigned to other certificates.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Already Assigned Soils (Info Only) -->
                @php
                    $assignedSoils = $this->getAssignedSoilsInfo();
                @endphp
                
                @if($assignedSoils->count() > 0 || !empty($soilSearchAssigned))
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Soils Already Assigned to Other Certificates
                            </label>
                            <span class="text-xs text-gray-500">{{ $assignedSoils->count() }} assigned</span>
                        </div>
                        
                        <!-- Search Filter for Assigned Soils -->
                        <div class="mb-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="soilSearchAssigned"
                                    placeholder="Search assigned soils..."
                                    class="block w-full pl-10 pr-10 py-2 text-sm border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-amber-50"
                                />
                                @if(!empty($soilSearchAssigned))
                                    <button 
                                        type="button"
                                        wire:click="$set('soilSearchAssigned', '')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg class="h-4 w-4 text-amber-400 hover:text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @if(!empty($soilSearchAssigned))
                                <p class="mt-1 text-xs text-amber-600">
                                    Filtering by: "{{ $soilSearchAssigned }}"
                                </p>
                            @endif
                        </div>
                        
                        @if($assignedSoils->count() > 0)
                            <div class="border border-amber-200 rounded-lg bg-amber-50 p-4 max-h-64 overflow-y-auto">
                                <div class="space-y-2">
                                    @foreach($assignedSoils as $soilGroup)
                                        @php $soil = $soilGroup->first(); @endphp
                                        <div class="p-3 bg-white rounded-lg border border-amber-200">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <div class="font-medium text-gray-900">{{ $soil->letak_tanah }}</div>
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        Seller: {{ $soil->nama_penjual }} | 
                                                        Area: {{ number_format($soil->luas, 0, ',', '.') }} m² | 
                                                        Owner: {{ $soil->atas_nama }}
                                                    </div>
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        @foreach($soilGroup as $assignment)
                                                            <a href="{{ route('land-certificates') }}" 
                                                            target="_blank"
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800 hover:bg-amber-200">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                                {{ $assignment->certificate_type }} #{{ $assignment->certificate_number }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="border border-amber-200 rounded-lg bg-amber-50 p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-amber-900">No Assigned Soils Match Your Search</p>
                                <p class="text-xs text-amber-700 mt-1">
                                    Try adjusting your search term or 
                                    <button type="button" wire:click="$set('soilSearchAssigned', '')" class="text-amber-800 hover:text-amber-900 underline font-medium">
                                        clear the filter
                                    </button>
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-yellow-800">
                        Please select a land first to view and select available soils.
                    </p>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-end space-x-3">
            <button
                type="button"
                wire:click="backToIndex"
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                {{ $isEdit ? 'Update Certificate' : 'Create Certificate' }}
            </button>
        </div>
    </form>
</div>