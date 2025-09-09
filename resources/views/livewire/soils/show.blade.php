{{-- resources/views/livewire/soils/show.blade.php --}}
<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-900">Soil Record Details</h2>
                <button wire:click="backToIndex" 
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Soil Records
                </button>
            </div>

            @php
                $soil = App\Models\Soil::with(['land', 'businessUnit', 'biayaTambahanSoils.category', 'biayaTambahanSoils.description'])->find($soilId);
            @endphp

            @if($soil)
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Land Name</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ $soil->land->lokasi_lahan ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Business Unit</dt>
                                <dd class="text-sm text-gray-900">{{ $soil->businessUnit->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">PPJB/ AJB Number</dt>
                                <dd class="text-sm text-gray-900">{{ $soil->nomor_ppjb }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">PPJB/ AJB Date</dt>
                                <dd class="text-sm text-gray-900">{{ $soil->tanggal_ppjb->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Record Created</dt>
                                <dd class="text-sm text-gray-900">{{ $soil->formatted_created_at }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="text-sm text-gray-900">{{ $soil->formatted_updated_at }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">History</dt>
                                <dd class="text-sm text-gray-900">
                                    <a href="{{ route('soils.history', $soil->id) }}" 
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:border-purple-700 focus:shadow-outline-purple active:bg-purple-600 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Show History
                                    </a>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Seller Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Seller Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Seller Name</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ $soil->nama_penjual }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Seller Address</dt>
                                <dd class="text-sm text-gray-900">{{ $soil->alamat_penjual }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Soil Details -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Soil Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Soil Location</dt>
                                <dd class="text-sm text-gray-900">{{ $soil->letak_tanah }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Area</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ $soil->formatted_luas }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ownership Proof</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $soil->bukti_kepemilikan }} - {{ $soil->bukti_kepemilikan_details }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Owner Name</dt>
                                <dd class="text-sm text-gray-900">{{ $soil->atas_nama }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Financial Information -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Land Price</dt>
                                <dd class="text-lg text-green-700 font-bold">{{ $soil->formatted_harga }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Price per m²</dt>
                                <dd class="text-lg text-green-600 font-semibold">{{ $soil->formatted_harga_per_meter }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Additional Costs</dt>
                                <dd class="text-lg text-orange-600 font-semibold">{{ $soil->formatted_total_biaya_tambahan }}</dd>
                            </div>
                            <div class="pt-2 border-t border-green-200">
                                <dt class="text-sm font-medium text-gray-500">Total Investment</dt>
                                <dd class="text-xl text-green-800 font-bold">{{ $soil->formatted_total_biaya_keseluruhan }}</dd>
                            </div>
                            <div class="pt-2 border-t border-green-200">
                                <div class="bg-green-100 p-3 rounded">
                                    <p class="text-sm text-green-800">
                                        <strong>Calculation:</strong> 
                                        {{ $soil->formatted_harga }} : {{ number_format($soil->luas, 0, ',', '.') }} m² = {{ $soil->formatted_harga_per_meter }}
                                    </p>
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mt-4 bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Notes</h3>
                    <p class="text-sm text-gray-700">{{ $soil->keterangan }}</p>
                </div>

                <!-- Additional Costs Detail -->
                @if($soil->biayaTambahanSoils->count() > 0)
                    <div class="mt-4 bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Costs Breakdown</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-yellow-200">
                                <thead class="bg-yellow-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Cost Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-yellow-800 uppercase tracking-wider">Cost Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-yellow-200">
                                    @foreach($soil->biayaTambahanSoils as $biaya)
                                        <tr>
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
                                <tfoot class="bg-yellow-100">
                                    <tr>
                                        <td colspan="2" class="px-6 py-3 text-sm font-bold text-yellow-900">Total Additional Costs:</td>
                                        <td class="px-6 py-3 text-sm font-bold text-yellow-900">{{ $soil->formatted_total_biaya_tambahan }}</td>
                                        <td class="px-6 py-3"></td>
                                        <td class="px-6 py-3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Summary Card -->
                <div class="mt-6 bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Investment Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($soil->luas, 0) }}</div>
                            <div class="text-sm text-gray-600">Square Meters</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($soil->harga, 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-600">Land Price (Rp)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ number_format($soil->total_biaya_tambahan, 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-600">Additional Costs (Rp)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($soil->total_biaya_keseluruhan, 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-600">Total Investment (Rp)</div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="showEditForm({{ $soil->id }})" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-600 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Record
                    </button>
                    <button wire:click="delete({{ $soil->id }})" 
                            wire:confirm="Are you sure you want to delete this soil record? This action cannot be undone."
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-600 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Record
                    </button>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Soil record not found</h3>
                    <p class="mt-1 text-sm text-gray-500">The requested soil record could not be found.</p>
                </div>
            @endif
        </div>
    </div>
</div>