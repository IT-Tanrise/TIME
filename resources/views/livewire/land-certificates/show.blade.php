@php
    $certificate = \App\Models\LandCertificate::with(['land.businessUnit', 'soils', 'createdBy', 'updatedBy'])
        ->findOrFail($certificateId);
@endphp

<div class="bg-white shadow-md rounded-lg">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Land Certificate Details</h2>
                <p class="text-sm text-gray-600 mt-1">Certificate #{{ $certificate->certificate_number }}</p>
            </div>
            <div class="flex space-x-2">
                <button
                    wire:click="showEditForm({{ $certificate->id }})"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>
                <button
                    wire:click="backToIndex"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
                    Back
                </button>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Status Badge -->
        <div class="mb-6">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $certificate->status_badge_color }}">
                {{ $certificate->formatted_status }}
            </span>
            @if($certificate->is_expired)
                <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    Expired
                </span>
            @elseif($certificate->days_until_expiration !== null && $certificate->days_until_expiration <= 90 && $certificate->days_until_expiration > 0)
                <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    Expires in {{ $certificate->days_until_expiration }} days
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Certificate Information</h3>
                
                <div>
                    <label class="text-sm font-medium text-gray-600">Certificate Type</label>
                    <p class="mt-1 text-gray-900">{{ $certificate->certificate_type }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Certificate Number</label>
                    <p class="mt-1 text-gray-900 font-mono">{{ $certificate->certificate_number }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Issued Date</label>
                    <p class="mt-1 text-gray-900">
                        {{ $certificate->formatted_issued_date ?? 'Not specified' }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Expired Date</label>
                    <p class="mt-1 text-gray-900">
                        {{ $certificate->formatted_expired_date ?? 'No expiration' }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Issued By</label>
                    <p class="mt-1 text-gray-900">
                        {{ $certificate->issued_by ?? 'Not specified' }}
                    </p>
                </div>
            </div>

            <!-- Land Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Land Information</h3>
                
                <div>
                    <label class="text-sm font-medium text-gray-600">Land Location</label>
                    <p class="mt-1 text-gray-900">{{ $certificate->land->lokasi_lahan }}</p>
                </div>

                @if($certificate->land->businessUnit)
                    <div>
                        <label class="text-sm font-medium text-gray-600">Business Unit</label>
                        <p class="mt-1 text-gray-900">{{ $certificate->land->businessUnit->name }}</p>
                    </div>
                @endif

                @if($certificate->land->kota_kabupaten)
                    <div>
                        <label class="text-sm font-medium text-gray-600">City/Regency</label>
                        <p class="mt-1 text-gray-900">{{ $certificate->land->kota_kabupaten }}</p>
                    </div>
                @endif

                @if($certificate->land->alamat)
                    <div>
                        <label class="text-sm font-medium text-gray-600">Address</label>
                        <p class="mt-1 text-gray-900">{{ $certificate->land->alamat }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Notes -->
        @if($certificate->notes)
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Notes</h3>
                <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $certificate->notes }}</p>
            </div>
        @endif

        <!-- Covered Soils -->
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">
                Soils Covered by This Certificate ({{ $certificate->soils->count() }})
            </h3>
            
            @if($certificate->soils->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Seller
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Owner Name
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Area
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($certificate->soils as $soil)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $soil->letak_tanah }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900">{{ $soil->nama_penjual }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900">{{ $soil->atas_nama }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($soil->luas, 0, ',', '.') }} m²
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $soil->status_badge_color }}">
                                            {{ $soil->formatted_status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="3" class="px-4 py-3 text-right text-sm text-gray-700">Total Area:</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900">
                                    {{ $certificate->formatted_total_soil_area }}
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">No Soils Assigned</p>
                        <p class="text-xs text-yellow-700 mt-1">This certificate has no soil records assigned to it yet.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Record Info -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
            <div>
                <label class="text-xs font-medium text-gray-600">Created</label>
                <p class="mt-1 text-sm text-gray-900">{{ $certificate->formatted_created_at }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600">Last Updated</label>
                <p class="mt-1 text-sm text-gray-900">{{ $certificate->formatted_updated_at }}</p>
            </div>
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
</script