{{-- resources/views/livewire/soils/show.blade.php --}}
<div class="min-h-screen bg-gray-50">
    @php
        $soil = App\Models\Soil::with(['land', 'businessUnit', 'biayaTambahanSoils.category', 'biayaTambahanSoils.description'])->find($soilId);
    @endphp

    @if($soil)
        <!-- Header Section - Compact -->
        <div class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-3">
                        <button wire:click="backToIndex" 
                                class="inline-flex items-center px-3 py-1.5 bg-gray-500 border border-transparent rounded-md text-xs text-white font-medium hover:bg-gray-600 transition-colors duration-150">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back
                        </button>
                        <h2 class="text-lg font-semibold text-gray-900">Soil Record Details</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('soils.history', $soil->id) }}" 
                           class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded-md hover:bg-purple-100">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            History
                        </a>
                        <button wire:click="showEditForm({{ $soil->id }})" 
                                class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        <button wire:click="delete({{ $soil->id }})" 
                                wire:confirm="Are you sure you want to delete this soil record? This action cannot be undone."
                                class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <!-- Financial Summary Cards - Most Important Info First -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3 text-white">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Investment</p>
                        <p class="text-2xl font-bold">{{ $soil->formatted_total_biaya_keseluruhan }}</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-3 text-white">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Land Price</p>
                        <p class="text-xl font-bold">{{ $soil->formatted_harga }}</p>
                        <p class="text-green-100 text-xs">{{ $soil->formatted_harga_per_meter }}/m²</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-3 text-white">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Additional Costs</p>
                        <p class="text-xl font-bold">{{ $soil->formatted_total_biaya_tambahan }}</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-3 text-white">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Land Area</p>
                        <p class="text-2xl font-bold">{{ number_format($soil->luas, 0) }}</p>
                        <p class="text-purple-100 text-sm">square meters</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <!-- Left Column - 2/3 Width -->
                <div class="lg:col-span-2 space-y-3">
                    <!-- Property & Legal Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Soil Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">Land Name</dt>
                                        <dd class="text-sm text-gray-900 font-medium text-right">{{ $soil->land->lokasi_lahan ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">Business Unit</dt>
                                        <dd class="text-sm text-gray-900 text-right">{{ $soil->businessUnit->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">Location</dt>
                                        <dd class="text-sm text-gray-900 text-right">{{ $soil->letak_tanah }}</dd>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">Seller Name</dt>
                                        <dd class="text-sm text-gray-900 text-right">{{ $soil->nama_penjual }}</dd>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">Seller Address</dt>
                                        <dd class="text-sm text-gray-900 text-right">{{ $soil->alamat_penjual }}</dd>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">PPJB/AJB</dt>
                                        <dd class="text-sm text-gray-900 text-right">{{ $soil->nomor_ppjb }}</dd>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">Date</dt>
                                        <dd class="text-sm text-gray-900 text-right">{{ $soil->tanggal_ppjb->format('d/m/Y') }}</dd>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">Ownership</dt>
                                        <dd class="text-sm text-gray-900 text-right">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $soil->bukti_kepemilikan }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">Owner Details</dt>
                                        <dd class="text-sm text-gray-900 text-right">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $soil->atas_nama }} - {{ $soil->bukti_kepemilikan_details }}
                                            </span>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Costs Detail -->
                    @if($soil->biayaTambahanSoils->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Additional Costs Breakdown</h3>
                                <span class="text-sm font-medium text-orange-600">{{ $soil->formatted_total_biaya_tambahan }}</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($soil->biayaTambahanSoils as $biaya)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {{ $biaya->category->category ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-gray-900">{{ $biaya->description->description ?? '-' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $biaya->formatted_harga }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                        @if($biaya->cost_type === 'standard') bg-green-100 text-green-800 @else bg-orange-100 text-orange-800 @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $biaya->cost_type)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $biaya->date_cost ? $biaya->date_cost->format('d/m/Y') : '-' }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - 1/3 Width -->
                <div class="space-y-3">
                    <!-- Quick Info Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h3 class="text-sm font-medium text-gray-900">Quick Info</h3>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Created</span>
                                <span class="text-xs text-gray-900">{{ $soil->formatted_created_at }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Updated</span>
                                <span class="text-xs text-gray-900">{{ $soil->formatted_updated_at }}</span>
                            </div>
                            <div class="pt-2 border-t border-gray-100">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-xs text-blue-800 font-medium mb-1">Price Calculation</div>
                                    <div class="text-xs text-blue-700">
                                        {{ $soil->formatted_harga }} ÷ {{ number_format($soil->luas, 0, ',', '.') }} m² = {{ $soil->formatted_harga_per_meter }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($soil->keterangan)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900">Notes</h3>
                            </div>
                            <div class="p-4">
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $soil->keterangan }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Investment Summary -->
                    <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-lg border border-gray-200 p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Investment Breakdown</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Land Price</span>
                                <span class="font-medium text-green-700">{{ number_format($soil->harga, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Additional Costs</span>
                                <span class="font-medium text-orange-600">{{ number_format($soil->total_biaya_tambahan, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <div class="flex justify-between text-sm font-bold">
                                    <span class="text-gray-900">Total Investment</span>
                                    <span class="text-blue-700">{{ number_format($soil->total_biaya_keseluruhan, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Header Section for Error State -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-3">
                        <button wire:click="backToIndex" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back
                        </button>
                        <h1 class="text-xl font-semibold text-gray-900">Soil Record Details</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Soil record not found</h3>
                <p class="mt-1 text-sm text-gray-500">The requested soil record could not be found.</p>
            </div>
        </div>
    @endif
</div>