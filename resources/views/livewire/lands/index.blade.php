{{-- resources/views/livewire/lands/index.blade.php --}}
<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if ($showForm)
        @include('livewire.lands.form')
    @elseif ($showDetailForm)
        @include('livewire.lands.show')
    @else
        
        <!-- Delete Confirmation Modal -->
        @if($showDeleteModal)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click="hideDeleteModal"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                        Delete Land Record
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            @if(auth()->user()->can('land-data.approval'))
                                                Are you sure you want to delete this land record? This action cannot be undone.
                                            @else
                                                This will create a deletion request that requires approval. Please provide a reason for the deletion.
                                            @endif
                                        </p>
                                        <div class="mt-4">
                                            <label for="deletionReason" class="block text-sm font-medium text-gray-700 mb-1">
                                                Deletion Reason <span class="text-red-500">*</span>
                                            </label>
                                            <textarea 
                                                wire:model="deletionReason" 
                                                id="deletionReason"
                                                rows="3" 
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="Enter the reason for deletion (minimum 10 characters)"></textarea>
                                            @error('deletionReason')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button 
                                type="button" 
                                wire:click="delete"
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                {{ auth()->user()->can('land-data.approval') ? 'Delete' : 'Submit Request' }}
                            </button>
                            <button 
                                type="button" 
                                wire:click="hideDeleteModal"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg">
            <!-- Compact Header -->
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <!-- Title Section -->
                    <div class="flex items-center gap-2">
                        <button onclick="history.back()" 
                                class="inline-flex items-center p-1.5 bg-gray-600 rounded-lg text-white hover:bg-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">Lands</h1>
                            @if($businessUnit)
                                <a href="{{ route('business-units', ['view' => 'show', 'id' => $businessUnit->id]) }}" 
                                   class="text-xs text-blue-600 hover:text-blue-800">
                                    {{ $businessUnit->name }} ({{ $businessUnit->code }})
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        @if($businessUnit)
                            <button wire:click="clearBusinessUnitFilter" 
                                    class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg transition">
                                Show All
                            </button>
                        @endif
                        @can('lands.edit')
                        <button wire:click="showCreateForm" 
                                class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition">
                                + Add New
                        </button>
                        @endcan
                        @if($businessUnit)
                            <a href="{{ route('land-certificates', ['businessUnit' => $businessUnit->id]) }}" 
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded hover:bg-purple-100">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Certificates
                                @php
                                    $certCount = \App\Models\LandCertificate::whereHas('land', function($q) use ($businessUnit) {
                                        $q->where('business_unit_id', $businessUnit->id);
                                    })->count();
                                @endphp
                                @if($certCount > 0)
                                    <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-purple-100 bg-purple-600 rounded-full">
                                        {{ $certCount }}
                                    </span>
                                @endif
                            </a>
                        @else
                            <a href="{{ route('land-certificates') }}" 
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded hover:bg-purple-100">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Certificates
                                @php
                                    $certCount = \App\Models\LandCertificate::count();
                                @endphp
                                @if($certCount > 0)
                                    <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-purple-100 bg-purple-600 rounded-full">
                                        {{ $certCount }}
                                    </span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Compact Filters -->
                @if(!$this->isFiltered())
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2">
                    <div class="lg:col-span-2">
                        <input type="text" 
                               wire:model.live="search" 
                               placeholder="Search location, city, status..."
                               class="w-full px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <select wire:model.live="filterKotaKabupaten" 
                                class="w-full px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Cities</option>
                            @foreach($kotaKabupaten as $kota)
                                <option value="{{ $kota }}">{{ $kota }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="filterBusinessUnit" 
                                class="w-full px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Units</option>
                            @foreach($businessUnits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="filterStatus" 
                                class="w-full px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button wire:click="resetFilters" 
                                class="w-full px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg transition">
                            Reset
                        </button>
                    </div>
                </div>
                @else
                <div class="mt-3 flex items-center gap-2">
                    <input wire:model.live="search" 
                           type="text" 
                           placeholder="Search..." 
                           class="flex-1 px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @if($businessUnit)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap">
                            {{ $businessUnit->name }}
                        </span>
                    @endif
                </div>
                @endif
            </div>

            <!-- Compact Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">BU</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">City</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Area</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price/m²</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Related</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($lands as $land)
                            @php
                                $totalAdditionalCosts = $land->soils->sum(function($soil) {
                                    return $soil->biayaTambahanSoils->sum('harga');
                                });
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <div class="text-sm font-medium text-gray-900">{{ $land->lokasi_lahan }}</div>
                                    @if($land->alamat)
                                        <div class="text-xs text-gray-500">{{ Str::limit($land->alamat, 40) }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if($land->businessUnit)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $land->businessUnit->code }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-sm text-gray-900">
                                    {{ $land->kota_kabupaten ?? '-' }}
                                </td>
                                <td class="px-3 py-2 text-sm text-gray-900">
                                    {{ $land->tahun_perolehan }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    @if($land->total_soil_price > 0)
                                        <div class="text-sm font-medium text-gray-900">{{ $land->formatted_total_soil_price }}</div>
                                    @else
                                        <div class="text-xs text-gray-400">Rp 0</div>
                                    @endif
                                    @if($totalAdditionalCosts > 0)
                                        <div class="text-xs text-blue-700">+{{ number_format($totalAdditionalCosts, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right text-sm text-gray-900">
                                    @if($land->total_soil_area > 0)
                                        {{ $land->formatted_total_soil_area }}
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right text-sm text-gray-900">
                                    @if($land->total_soil_area > 0)
                                        {{ $land->formatted_average_price_per_m2 }}
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                                        @if($land->status === 'Available') bg-green-100 text-green-800
                                        @elseif($land->status === 'Reserved') bg-yellow-100 text-yellow-800
                                        @elseif($land->status === 'Sold') bg-red-100 text-red-800
                                        @elseif($land->status === 'Development') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $land->status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex flex-wrap gap-1 justify-center">
                                        @if($land->soils_count > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                {{ $land->soils_count }}S
                                            </span>
                                        @endif
                                        @if($land->projects_count > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $land->projects_count }}P
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="showDetail({{ $land->id }})" 
                                                title="View"
                                                class="text-blue-600 hover:text-blue-900 p-1 hover:bg-blue-50 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        @can('lands.edit')
                                        <button wire:click="showEditForm({{ $land->id }})" 
                                                title="Edit"
                                                class="text-indigo-600 hover:text-indigo-900 p-1 hover:bg-indigo-50 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        @endcan
                                        @can('lands.delete')
                                        <button wire:click="confirmDelete({{ $land->id }})" 
                                            title="Delete"
                                            class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded transition">
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
                                <td colspan="10" class="px-6 py-8 text-center">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-sm font-medium">
                                            @if($this->isFiltered())
                                                No lands found for "{{ $this->getCurrentBusinessUnitName() }}"
                                            @elseif($search || $filterBusinessUnit || $filterStatus || $filterKotaKabupaten)
                                                No lands match your filters
                                            @else
                                                No lands available
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($lands->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $lands->links() }}
                </div>
            @endif
        </div>
    @endif
</div>