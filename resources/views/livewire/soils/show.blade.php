{{-- resources/views/livewire/soils/show.blade.php --}}
<div class="min-h-screen bg-gray-50">
    @php
        $soil = App\Models\Soil::with(['land', 'businessUnit', 'biayaTambahanSoils.description'])->find($soilId);
        // Get pending approvals for this soil
        $pendingApprovals = $soil ? App\Models\SoilApproval::where('soil_id', $soil->id)->where('status', 'pending')->with('requestedBy')->get() : collect();
        $recentApprovals = $soil ? App\Models\SoilApproval::where('soil_id', $soil->id)->whereIn('status', ['approved', 'rejected'])->with(['requestedBy', 'approvedBy'])->latest()->limit(3)->get() : collect();
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
                        
                        <!-- Approval Status Indicators -->
                        @if($pendingApprovals->count() > 0)
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center px-2 py-1 bg-yellow-100 border border-yellow-200 rounded-full">
                                    <div class="w-2 h-2 bg-yellow-400 rounded-full mr-1.5 animate-pulse"></div>
                                    <span class="text-xs font-medium text-yellow-800">{{ $pendingApprovals->count() }} Pending</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('soils.history', $soil->id) }}" 
                           class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded-md hover:bg-purple-100">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            History
                        </a>
                        
                        <!-- Edit Dropdown -->
                        @canany(['soils.edit', 'soil-costs.edit'])
                        <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" 
                                    @click="open = !open"
                                    class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                                <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-1 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                @click="open = false">
                                <div class="py-1">
                                    @can('soils.edit')
                                    <button wire:click="showEditForm({{ $soil->id }}, 'details', 'detail')" 
                                            class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit Details
                                        </div>
                                    </button>
                                    @endcan
                                    @can('soil-costs.edit')
                                    <button wire:click="showEditForm({{ $soil->id }}, 'costs', 'detail')" 
                                            class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                                            </svg>
                                            Manage Costs
                                        </div>
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        @endcan
                        
                        @can('soils.delete')
                        <button wire:click="delete({{ $soil->id }})" 
                                wire:confirm="Are you sure you want to delete this soil record? This action cannot be undone."
                                class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <!-- Approval Status Section - For users without approval permissions -->
            @if(!auth()->user()->can('soil-data.approval') && !auth()->user()->can('soil-data-costs.approval'))
                @if($pendingApprovals->count() > 0 || $recentApprovals->count() > 0)
                    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Approval Status
                            </h3>
                        </div>
                        <div class="p-6">
                            <!-- Pending Approvals -->
                            @if($pendingApprovals->count() > 0)
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                        <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></div>
                                        Pending Approvals ({{ $pendingApprovals->count() }})
                                    </h4>
                                    <div class="space-y-3">
                                        @foreach($pendingApprovals as $approval)
                                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center mb-2">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                @if($approval->change_type === 'details') bg-blue-100 text-blue-800 @else bg-purple-100 text-purple-800 @endif">
                                                                {{ $approval->change_type === 'details' ? 'Soil Data Changes' : 'Cost Changes' }}
                                                            </span>
                                                            <span class="ml-2 text-xs text-yellow-700">
                                                                Requested {{ $approval->created_at->diffForHumans() }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm text-yellow-800 mb-2">
                                                            Your changes are waiting for approval from an authorized user.
                                                        </p>
                                                        <div class="text-xs text-yellow-700">
                                                            <strong>Requested by:</strong> {{ $approval->requestedBy->name }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4 flex-shrink-0">
                                                        <div class="flex items-center">
                                                            <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                                                            <span class="ml-2 text-xs font-medium text-yellow-800">Waiting</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Recent Approvals History -->
                            @if($recentApprovals->count() > 0)
                                <div class="@if($pendingApprovals->count() > 0) border-t border-gray-200 pt-6 @endif">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Recent Approval History</h4>
                                    <div class="space-y-3">
                                        @foreach($recentApprovals as $approval)
                                            <div class="border rounded-lg p-4 
                                                @if($approval->status === 'approved') bg-green-50 border-green-200 @else bg-red-50 border-red-200 @endif">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center mb-2">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                @if($approval->change_type === 'details') bg-blue-100 text-blue-800 @else bg-purple-100 text-purple-800 @endif">
                                                                {{ $approval->change_type === 'details' ? 'Soil Data Changes' : 'Cost Changes' }}
                                                            </span>
                                                            <span class="ml-2 text-xs 
                                                                @if($approval->status === 'approved') text-green-700 @else text-red-700 @endif">
                                                                {{ ucfirst($approval->status) }} {{ $approval->updated_at->diffForHumans() }}
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="text-xs mb-2 
                                                            @if($approval->status === 'approved') text-green-700 @else text-red-700 @endif">
                                                            <div class="mb-1">
                                                                <strong>Requested by:</strong> {{ $approval->requestedBy->name }}
                                                            </div>
                                                            <div>
                                                                <strong>{{ $approval->status === 'approved' ? 'Approved' : 'Rejected' }} by:</strong> {{ $approval->approvedBy->name ?? 'Unknown' }}
                                                            </div>
                                                            <div>
                                                                @if($approval->status === 'rejected')
                                                                <strong>Reason:</strong> {{ $approval->reason }}
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if($approval->status === 'rejected' && $approval->rejection_reason)
                                                            <div class="mt-2 p-2 bg-red-100 border border-red-200 rounded text-xs text-red-800">
                                                                <strong>Rejection Reason:</strong> {{ $approval->rejection_reason }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4 flex-shrink-0">
                                                        <div class="flex items-center">
                                                            @if($approval->status === 'approved')
                                                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                                                <span class="ml-2 text-xs font-medium text-green-800">Approved</span>
                                                            @else
                                                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                                                <span class="ml-2 text-xs font-medium text-red-800">Rejected</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

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
                        <p class="text-2xl font-bold">{{ number_format($soil->luas, 0, ',', '.') }} m²</p>
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
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">PPJB/ AJB</dt>
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
                                                {{ $soil->bukti_kepemilikan }} - {{ $soil->bukti_kepemilikan_details }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <dt class="text-sm font-medium text-gray-500 flex-shrink-0 w-24">Owner Details</dt>
                                        <dd class="text-sm text-gray-900 text-right">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $soil->atas_nama }} 
                                            </span>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Costs Detail -->
                    @can('soil-costs.access')
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
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($soil->biayaTambahanSoils as $biaya)
                                            <tr class="hover:bg-gray-50">
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
                    @endcan
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
                                        {{ $soil->formatted_harga }} : {{ number_format($soil->luas, 0, ',', '.') }} m² = {{ $soil->formatted_harga_per_meter }}
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