{{-- resources/views/livewire/soils/show.blade.php --}}
<div class="min-h-screen bg-gray-50">
    @php
        $soil = App\Models\Soil::with(['land', 'businessUnit', 'biayaTambahanSoils.description'])->find($soilId);
        $pendingApprovals = $soil ? App\Models\SoilApproval::where('soil_id', $soil->id)->where('status', 'pending')->with('requestedBy')->get() : collect();
        $recentApprovals = $soil ? App\Models\SoilApproval::where('soil_id', $soil->id)->whereIn('status', ['approved', 'rejected'])->with(['requestedBy', 'approvedBy'])->latest()->limit(3)->get() : collect();
    @endphp

    @if($soil)
        <!-- Compact Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="mx-auto px-3 sm:px-4 lg:px-6">
                <div class="flex justify-between items-center py-2.5">
                    <div class="flex items-center space-x-2">
                        <button wire:click="backToIndex" 
                                class="inline-flex items-center px-2 py-1 bg-gray-500 border border-transparent rounded text-xs text-white font-medium hover:bg-gray-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </button>
                        <h2 class="text-base font-semibold text-gray-900">Soil Details</h2>
                        
                        @if($pendingApprovals->count() > 0)
                            <div class="flex items-center px-1.5 py-0.5 bg-yellow-100 border border-yellow-200 rounded-full">
                                <div class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1 animate-pulse"></div>
                                <span class="text-xs font-medium text-yellow-800">{{ $pendingApprovals->count() }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <a href="{{ route('soils.history', $soil->id) }}" 
                           class="inline-flex items-center px-2 py-1 text-xs font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded hover:bg-purple-100">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            History
                        </a>
                        
                        @canany(['soils.edit', 'soil-costs.edit'])
                        <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" @click="open = !open"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-blue-600 border border-transparent rounded hover:bg-blue-700">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                                <svg class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="absolute right-0 mt-1 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50" @click="open = false">
                                <div class="py-1">
                                    @can('soils.edit')
                                    <button wire:click="showEditForm({{ $soil->id }}, 'details', 'detail')" 
                                            class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 w-full text-left">
                                        <div class="flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit Details
                                        </div>
                                    </button>
                                    @endcan
                                    @can('soil-costs.edit')
                                    <button wire:click="showEditForm({{ $soil->id }}, 'costs', 'detail')" 
                                            class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 w-full text-left">
                                        <div class="flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                                            </svg>
                                            Manage Costs
                                        </div>
                                    </button>
                                    @endcan
                                    @can('soil-data-interest-costs.edit')
                                    <button wire:click="showEditForm({{ $soil->id }}, 'interest', 'detail')" 
                                            class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 w-full text-left">
                                        <div class="flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                                            </svg>
                                            Manage Interest
                                        </div>
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        @endcan
                        
                        @can('soils.delete')
                        <button wire:click="delete({{ $soil->id }})" 
                                wire:confirm="Are you sure you want to delete this soil record?"
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-red-600 border border-transparent rounded hover:bg-red-700">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto px-3 sm:px-4 lg:px-6 py-2">
            <!-- Approval Status - Compact -->
            @if(!auth()->user()->can('soil-data.approval') && !auth()->user()->can('soil-data-costs.approval'))
                @if($pendingApprovals->count() > 0 || $recentApprovals->count() > 0)
                    <div class="mb-2 bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="w-full px-4 py-2.5 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center space-x-2">
                                <h3 class="text-sm font-medium text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Approval Status
                                </h3>
                                <div class="flex items-center space-x-1.5">
                                    @if($pendingApprovals->count() > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                            <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1 animate-pulse"></span>
                                            {{ $pendingApprovals->count() }}
                                        </span>
                                    @endif
                                    @if($recentApprovals->count() > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                            {{ $recentApprovals->count() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" x-collapse x-cloak>
                            <div class="overflow-x-auto border-t border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pendingApprovals as $approval)
                                            <tr class="hover:bg-yellow-50">
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1 animate-pulse"></span>
                                                        Pending
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                                        @if($approval->change_type === 'details') bg-blue-100 text-blue-800
                                                        @elseif($approval->change_type === 'costs') bg-purple-100 text-purple-800
                                                        @elseif($approval->change_type === 'interest') bg-indigo-100 text-indigo-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ ucfirst($approval->change_type) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-gray-900 max-w-xs truncate">{{ $approval->getChangeSummary() }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $approval->requestedBy->name }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $approval->formatted_created_at }}(GMT+7)</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-400">-</td>
                                            </tr>
                                        @endforeach
                                        @foreach($recentApprovals as $approval)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                                        @if($approval->status === 'approved') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                        {{ ucfirst($approval->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                                        @if($approval->change_type === 'details') bg-blue-100 text-blue-800
                                                        @elseif($approval->change_type === 'costs') bg-purple-100 text-purple-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ ucfirst($approval->change_type) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-gray-900 max-w-xs truncate">{{ $approval->getChangeSummary() }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $approval->requestedBy->name }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $approval->updated_at->format('d/m/Y H:i') }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $approval->approvedBy?->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Compact Financial Summary -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-2">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-2.5 text-white">
                    <p class="text-blue-100 text-xs font-medium">Total Investment</p>
                    <p class="text-lg font-bold">Rp {{ number_format($soil->harga + $soil->total_biaya_tambahan + $soil->total_biaya_interest, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-2.5 text-white">
                    <p class="text-green-100 text-xs font-medium">Soil Price</p>
                    <p class="text-base font-bold">{{ $soil->formatted_harga }}</p>
                    <p class="text-green-100 text-xs">{{ $soil->formatted_harga_per_meter }}/m²</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-2.5 text-white">
                    <p class="text-orange-100 text-xs font-medium">Additional Costs</p>
                    <p class="text-base font-bold">{{ $soil->formatted_total_biaya_tambahan }}</p>
                    <p class="text-orange-100 text-xs">{{ $soil->biayaTambahanSoils->count() }} item(s)</p>
                </div>
                
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-2.5 text-white">
                    <p class="text-purple-100 text-xs font-medium">Interest</p>
                    @if($soil->biayaTambahanInterestSoils->count() > 0)
                        <p class="text-base font-bold">{{ $soil->formatted_total_biaya_interest }}</p>
                        <p class="text-purple-100 text-xs">{{ $soil->biayaTambahanInterestSoils->count() }} period(s)</p>
                    @else
                        <p class="text-base font-bold">Rp 0</p>
                    @endif
                </div>
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-2.5 text-white">
                    <p class="text-indigo-100 text-xs font-medium">Area</p>
                    <p class="text-lg font-bold">{{ number_format($soil->luas, 0, ',', '.') }} m²</p>
                </div>
            </div>

            <!-- Main Content Grid - Reduced Gap -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-2">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-2">
                    <!-- Soil Information - Compact -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <h3 class="text-sm font-medium text-gray-900">Soil Information</h3>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-3 gap-y-2">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 font-medium">Land Name</span>
                                        <span class="text-gray-900 font-medium">{{ $soil->land->lokasi_lahan ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 font-medium">Business Unit</span>
                                        <span class="text-gray-900">{{ $soil->businessUnit->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 font-medium">Location</span>
                                        <span class="text-gray-900">{{ $soil->letak_tanah }}</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 font-medium">Seller</span>
                                        <span class="text-gray-900">{{ $soil->nama_penjual }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 font-medium">PPJB/AJB</span>
                                        <span class="text-gray-900">{{ $soil->nomor_ppjb }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 font-medium">Date</span>
                                        <span class="text-gray-900">{{ $soil->tanggal_ppjb->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 font-medium">Ownership</span>
                                        <span class="inline-flex px-1.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $soil->bukti_kepemilikan }} - {{ $soil->bukti_kepemilikan_details }}
                                        </span>
                                    </div>
                                    <!-- SHGB Expired Date Display -->
                                    @if($soil->bukti_kepemilikan === 'SHGB' && $soil->shgb_expired_date)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-semibold rounded-full 
                                                @if($soil->is_shgb_expired) 
                                                    bg-red-100 text-red-800
                                                @elseif($soil->shgb_days_until_expiration <= 365)
                                                    bg-yellow-100 text-yellow-800
                                                @else
                                                    bg-green-100 text-green-800
                                                @endif">
                                                @if($soil->is_shgb_expired)
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Expired
                                                @elseif($soil->shgb_days_until_expiration <= 365)
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Expiring Soon
                                                @else
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Valid
                                                @endif
                                                {{ $soil->formatted_shgb_expired_date }}
                                            </span>
                                            
                                            @if(!$soil->is_shgb_expired && $soil->shgb_days_until_expiration)
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    {{ abs($soil->shgb_days_until_expiration) }} days 
                                                    {{ $soil->shgb_days_until_expiration >= 0 ? 'remaining' : 'overdue' }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 font-medium">Owner</span>
                                        <span class="inline-flex px-1.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $soil->atas_nama }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500 font-medium">Status</span>
                                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $soil->status_badge_color }}">
                                            {{ $soil->formatted_status }} - {{ $soil->keterangan }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Costs - Compact -->
                    @can('soil-costs.access')
                    @if($soil->biayaTambahanSoils->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-4 py-2 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-sm font-medium text-gray-900">Additional Costs</h3>
                                <span class="text-xs font-medium text-orange-600">{{ $soil->formatted_total_biaya_tambahan }}</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($soil->biayaTambahanSoils as $biaya)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 text-xs text-gray-900">{{ $biaya->description->description ?? '-' }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900">{{ $biaya->formatted_harga }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="inline-flex px-1.5 py-0.5 text-xs font-semibold rounded-full 
                                                        @if($biaya->cost_type === 'standard') bg-green-100 text-green-800 @else bg-orange-100 text-orange-800 @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $biaya->cost_type)) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $biaya->date_cost ? $biaya->date_cost->format('d/m/Y') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    @endcan
                    
                    <!-- Interest Costs - Compact -->
                    @can('soil-data-interest-costs.access')
                    @if($soil->biayaTambahanInterestSoils->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900">Interest Calculation</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-purple-50">
                                        <tr>
                                            <th class="px-2 py-2 text-left text-xs font-medium text-purple-900">No</th>
                                            <th class="px-2 py-2 text-left text-xs font-medium text-purple-900">Period</th>
                                            <th class="px-2 py-2 text-center text-xs font-medium text-purple-900">Days</th>
                                            <th class="px-2 py-2 text-left text-xs font-medium text-purple-900">Remarks</th>
                                            <th class="px-2 py-2 text-right text-xs font-medium text-purple-900">H. Perolehan</th>
                                            <th class="px-2 py-2 text-right text-xs font-medium text-purple-900">Before</th>
                                            <th class="px-2 py-2 text-center text-xs font-medium text-purple-900">%</th>
                                            <th class="px-2 py-2 text-right text-xs font-medium text-purple-900">Interest</th>
                                            <th class="px-2 py-2 text-right text-xs font-medium text-purple-900">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                            $totalPerolehan = 0;
                                            $totalBunga = 0;
                                            $finalValue = 0;
                                        @endphp
                                        @foreach($soil->biayaTambahanInterestSoils as $idx => $interest)
                                            @php
                                                $totalPerolehan += $interest->harga_perolehan;
                                                $totalBunga += $interest->bunga_calculation;
                                                $finalValue = $interest->nilai_tanah;
                                            @endphp
                                            <tr class="hover:bg-purple-50">
                                                <td class="px-2 py-2 text-xs">{{ $idx + 1 }}</td>
                                                <td class="px-2 py-2 whitespace-nowrap text-xs">
                                                    {{ $interest->start_date->format('d/m/y') }} - {{ $interest->end_date->format('d/m/y') }}
                                                </td>
                                                <td class="px-2 py-2 text-center">
                                                    <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800">
                                                        {{ $interest->hari }}
                                                    </span>
                                                </td>
                                                <td class="px-2 py-2 text-xs max-w-xs truncate">{{ $interest->remarks }}</td>
                                                <td class="px-2 py-2 whitespace-nowrap text-right text-xs">{{ $interest->formatted_harga_perolehan }}</td>
                                                <td class="px-2 py-2 whitespace-nowrap text-right text-xs text-blue-700">{{ $interest->formatted_nilai_tanah_sebelum }}</td>
                                                <td class="px-2 py-2 text-center">
                                                    <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs bg-purple-100 text-purple-800">
                                                        {{ number_format($interest->bunga, 2) }}%
                                                    </span>
                                                </td>
                                                <td class="px-2 py-2 whitespace-nowrap text-right text-xs text-orange-700">{{ $interest->formatted_bunga_calculation }}</td>
                                                <td class="px-2 py-2 whitespace-nowrap text-right text-xs font-bold text-green-700">{{ $interest->formatted_nilai_tanah }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="bg-purple-100 font-bold border-t-2 border-purple-300">
                                            <td colspan="4" class="px-2 py-2 text-xs text-right uppercase">Total:</td>
                                            <td class="px-2 py-2 text-right text-xs">Rp {{ number_format($totalPerolehan, 0, ',', '.') }}</td>
                                            <td class="px-2 py-2 text-center text-xs">-</td>
                                            <td class="px-2 py-2 text-center text-xs">-</td>
                                            <td class="px-2 py-2 text-right text-xs">Rp {{ number_format($totalBunga, 0, ',', '.') }}</td>
                                            <td class="px-2 py-2 text-right text-xs">Rp {{ number_format($finalValue, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    @endcan
                </div>

                <!-- Right Column - Compact -->
                <div class="space-y-2">
                    <!-- Quick Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-3 py-2 border-b border-gray-200">
                            <h3 class="text-xs font-medium text-gray-900">Quick Info</h3>
                        </div>
                        <div class="p-3 space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500">Created</span>
                                <span class="text-gray-900">{{ $soil->formatted_created_at }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500">Updated</span>
                                <span class="text-gray-900">{{ $soil->formatted_updated_at }}</span>
                            </div>
                            <div class="pt-2 border-t border-gray-100">
                                <div class="bg-blue-50 p-2 rounded text-xs">
                                    <div class="text-blue-800 font-medium mb-0.5">Price/m²</div>
                                    <div class="text-blue-700">{{ $soil->formatted_harga }} : {{ number_format($soil->luas, 0, ',', '.') }} m²</div>
                                    <div class="text-blue-900 font-bold">= {{ $soil->formatted_harga_per_meter }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Summary - Compact -->
                    <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-lg border border-gray-200 p-3">
                        <h3 class="text-xs font-medium text-gray-900 mb-2">Investment Breakdown</h3>
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Soil Price</span>
                                <span class="font-medium text-green-700">Rp {{ number_format($soil->harga, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Additional</span>
                                <span class="font-medium text-orange-600">Rp {{ number_format($soil->total_biaya_tambahan, 0, ',', '.') }}</span>
                            </div>
                            @if($soil->biayaTambahanInterestSoils->count() > 0)
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">Interest</span>
                                <span class="font-medium text-purple-600">Rp {{ number_format($soil->total_biaya_interest, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="border-t border-gray-200 pt-1.5 mt-1.5">
                                <div class="flex justify-between text-xs font-bold">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-blue-700">Rp {{ number_format($soil->harga + $soil->total_biaya_tambahan + $soil->total_biaya_interest, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Error State - Compact -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="mx-auto px-3 sm:px-4 lg:px-6">
                <div class="flex justify-between items-center py-2.5">
                    <div class="flex items-center space-x-2">
                        <button wire:click="backToIndex" 
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded hover:bg-gray-50">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </button>
                        <h1 class="text-base font-semibold text-gray-900">Soil Details</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto px-3 sm:px-4 lg:px-6 py-8">
            <div class="text-center">
                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Soil record not found</h3>
                <p class="mt-1 text-xs text-gray-500">The requested soil record could not be found.</p>
            </div>
        </div>
    @endif
</div>