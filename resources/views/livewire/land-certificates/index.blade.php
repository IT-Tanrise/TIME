{{-- resources/views/livewire/land-certificates/index.blade.php --}}
<div class="min-h-screen bg-gray-50">
    @if($showForm)
        @include('livewire.land-certificates.form')
    @elseif($showDetailForm)
        @include('livewire.land-certificates.show')
    @else
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-3 border-b border-gray-200">
                @if (session()->has('message'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-3 py-2 mb-3 rounded text-sm">
                        {{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-3 py-2 mb-3 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <button onclick="history.back()" 
                                class="inline-flex items-center p-1.5 bg-gray-600 rounded-lg text-white hover:bg-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">Land Certificates</h1>
                            @if($businessUnit)
                                <a href="{{ route('business-units', ['view' => 'show', 'id' => $businessUnit->id]) }}" 
                                   class="text-xs text-blue-600 hover:text-blue-800">
                                    {{ $businessUnit->name }} ({{ $businessUnit->code }})
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2">
                        @if($businessUnit)
                            <button wire:click="clearBusinessUnitFilter" 
                                    class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                Show All
                            </button>
                        @endif
                        <button wire:click="showCreateForm" 
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Certificate
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                    <!-- Search -->
                    <div>
                        <input type="text" 
                            wire:model.live="search" 
                            placeholder="Search certificate..."
                            class="w-full px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Land Filter -->
                    <div class="relative">
                        <input type="text" 
                            wire:model.live.debounce.300ms="filterLandSearch"
                            wire:focus="openLandFilterDropdown"
                            placeholder="Land..."
                            class="w-full px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 pr-8"
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
                            <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-auto" wire:click.stop>
                                @foreach($this->getFilteredLandsForFilter() as $land)
                                    <button type="button"
                                            wire:click.stop="selectLandFilter({{ $land->id }}, '{{ addslashes($land->lokasi_lahan) }}')"
                                            class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-b-0">
                                        {{ $land->lokasi_lahan }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Certificate Type Filter -->
                    <div>
                        <select wire:model.live="filterCertificateType"
                                class="w-full px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            @foreach(App\Models\LandCertificate::getCertificateTypeOptions() as $key => $value)
                                <option value="{{ $key }}">{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select wire:model.live="filterStatus"
                                class="w-full px-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            @foreach(App\Models\LandCertificate::getStatusOptions() as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset -->
                    <div>
                        <button wire:click="resetFilters" 
                                class="w-full px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg transition">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Land</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Certificate</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Soils</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($certificates as $cert)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <div class="text-sm font-medium text-gray-900">{{ $cert->land->lokasi_lahan ?? 'N/A' }}</div>
                                    @if($cert->land && $cert->land->businessUnit)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $cert->land->businessUnit->code }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="text-sm font-medium text-gray-900">{{ $cert->certificate_type }}</div>
                                    @if($cert->issued_by)
                                        <div class="text-xs text-gray-500">{{ $cert->issued_by }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="text-sm font-medium text-gray-900">{{ $cert->certificate_number }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if($cert->issued_date)
                                        <div class="text-xs text-gray-500">Issued: {{ $cert->formatted_issued_date }}</div>
                                    @endif
                                    @if($cert->expired_date)
                                        <div class="text-xs font-medium
                                            @if($cert->is_expired) text-red-600
                                            @elseif($cert->days_until_expiration && $cert->days_until_expiration <= 365) text-yellow-600
                                            @else text-green-600 @endif">
                                            Exp: {{ $cert->formatted_expired_date }}
                                        </div>
                                        @if(!$cert->is_expired && $cert->days_until_expiration)
                                            <div class="text-xs text-gray-500">{{ abs($cert->days_until_expiration) }} days</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <div class="text-sm font-medium text-gray-900">{{ $cert->soils->count() }} soil(s)</div>
                                    <div class="text-xs text-gray-500">{{ $cert->formatted_total_soil_area }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $cert->status_badge_color }}">
                                        {{ $cert->formatted_status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="showDetail({{ $cert->id }})" 
                                                class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition"
                                                title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="showEditForm({{ $cert->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50 transition"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $cert->id }})" 
                                                class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition"
                                                title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-sm font-medium">No certificates found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($certificates->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $certificates->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Modal -->
        @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Certificate</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Are you sure you want to delete this certificate? This action cannot be undone.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button wire:click="closeDeleteModal"
                                class="px-4 py-2 text-sm bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button wire:click="delete"
                                class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>

<script>
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        @this.call('closeDropdowns');
    }
});
</script>