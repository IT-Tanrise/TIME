{{-- resources/views/livewire/pre-soil-buy/form.blade.php --}}
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 font-inter">

    @php
        $detail = \App\Models\PreSoilBuy::with(['createdBy', 'updatedBy', 'approvals.respondedBy', 'cashOuts'])
            ->with([
                'approvals' => function ($query) {
                    $query->latest('id')->limit(1);
                },
            ])
            ->findOrFail($preSoilBuyId);

        $latestStatus = optional($detail->approvals->first())->status ?? 'N/A';
    @endphp

    <!-- HEADER -->
    <div class="px-6 py-5 bg-white border-b shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 shadow-md rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600">
                    <svg class="text-white w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">
                        Pre Soil Buy Detail
                    </h3>
                    <div class="flex items-center mt-1 space-x-2">
                        <p class="text-sm text-gray-500">{{ $detail->nomor_memo }}</p>
                        <span class="text-gray-300">•</span>
                        <p class="text-sm text-gray-500">{{ $detail->tanggal?->format('d F Y') }}</p>
                    </div>
                </div>
            </div>
            <button wire:click="closeForm" type="button"
                class="p-2 text-gray-400 transition-all duration-150 rounded-lg hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="px-6 py-6 space-y-6 overflow-y-auto max-h-[75vh]">

        <!-- STATUS BADGE -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center space-x-3">
                <span class="text-sm font-medium text-gray-600">Approval Status:</span>
                @if ($detail->approval_status === 'pending')
                    <span
                        class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-yellow-800 bg-yellow-100 rounded-full shadow-sm border border-yellow-200">
                        <span class="w-2.5 h-2.5 mr-2 bg-yellow-500 rounded-full animate-pulse"></span>
                        Pending Approval
                    </span>
                @elseif($detail->approval_status === 'approved')
                    <span
                        class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-green-800 bg-green-100 rounded-full shadow-sm border border-green-200">
                        <span class="w-2.5 h-2.5 mr-2 bg-green-500 rounded-full"></span>
                        Approved
                    </span>
                @elseif($detail->approval_status === 'rejected')
                    <span
                        class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-red-800 bg-red-100 rounded-full shadow-sm border border-red-200">
                        <span class="w-2.5 h-2.5 mr-2 bg-red-500 rounded-full"></span>
                        Rejected
                    </span>
                @endif
            </div>
            <div class="text-sm text-gray-500">
                Last updated: {{ $detail->updated_at->format('d M Y, H:i') }}
            </div>
        </div>

        <!-- INFORMATION CARD -->
        <div class="p-6 transition-all bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center pb-3 mb-4 border-b">
                <div class="p-2 mr-3 rounded-lg bg-blue-50">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-800">Memo Information</h4>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Memo Number</label>
                    <p class="text-base font-semibold text-gray-900">{{ $detail->nomor_memo }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Date</label>
                    <p class="text-base font-semibold text-gray-900">{{ $detail->tanggal?->format('d F Y') }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">From</label>
                    <p class="text-base font-semibold text-gray-900">{{ $detail->dari }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">To</label>
                    <p class="text-base font-semibold text-gray-900">{{ $detail->kepada }}</p>
                </div>
                @if ($detail->cc)
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-gray-500">CC</label>
                        <p class="text-base font-semibold text-gray-900">{{ $detail->cc }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- SUBJECT DETAILS -->
        <div class="p-6 transition-all bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center pb-3 mb-4 border-b">
                <div class="p-2 mr-3 rounded-lg bg-purple-50">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-800">Subject Details</h4>
            </div>
            <div class="space-y-4">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Subject</label>
                    <p class="text-base font-semibold text-gray-900">{{ $detail->subject_perihal }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Seller</label>
                    <p class="text-base font-semibold text-gray-900">{{ $detail->subject_penjual }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-500">Sales Object</label>
                    <p class="text-base font-semibold text-gray-900">{{ $detail->objek_jual_beli }}</p>
                </div>
            </div>
        </div>

        <!-- FINANCIAL INFORMATION -->
        <div
            class="p-6 transition-all border border-green-200 shadow-sm rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 hover:shadow-md">
            <div class="flex items-center pb-3 mb-4 border-b border-green-200">
                <div class="p-2 mr-3 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-800">Financial Information</h4>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-600">Area (m²)</label>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($detail->luas, 0, ',', '.') }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-600">Price per Meter</label>
                    <p class="text-lg font-bold text-green-600">Rp
                        {{ number_format($detail->harga_per_meter, 0, ',', '.') }}</p>
                </div>
                <div class="pt-3 border-t border-green-200 md:col-span-2">
                    <label class="text-sm font-medium text-gray-600">Total Agreement Price</label>
                    <p class="mt-1 text-2xl font-bold text-green-800">Rp
                        {{ number_format($detail->kesepakatan_harga_jual_beli, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- FILE ATTACHMENT -->
        @if ($detail->upload_file_im)
            <div class="p-6 transition-all bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center pb-3 mb-4 border-b">
                    <div class="p-2 mr-3 rounded-lg bg-blue-50">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800">File Attachment</h4>
                </div>
                <div
                    class="flex items-center justify-between p-4 transition-all duration-200 border border-blue-100 rounded-lg bg-blue-50 hover:bg-blue-100">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-white rounded-lg shadow-sm">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ basename($detail->upload_file_im) }}</p>
                            <p class="mt-1 text-xs text-gray-500">Attachment document</p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $detail->upload_file_im) }}" target="_blank"
                        class="flex items-center px-4 py-2 space-x-2 text-sm font-medium text-white transition-all bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true" focusable="false">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>

                        <span>View</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- APPROVAL HISTORY -->
        @if ($detail->approvals->isNotEmpty())
            <div class="p-6 transition-all bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center pb-3 mb-4 border-b">
                    <div class="p-2 mr-3 rounded-lg bg-orange-50">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800">Approval History</h4>
                </div>
                <div class="space-y-4">
                    @foreach ($detail->approvals as $approval)
                        <div
                            class="flex items-start p-4 border rounded-lg shadow-sm
                            {{ $approval->status === 'approved' ? 'bg-green-50 border-green-200' : '' }}
                            {{ $approval->status === 'rejected' ? 'bg-red-50 border-red-200' : '' }}
                            {{ $approval->status === 'pending' ? 'bg-gray-50 border-gray-200' : '' }}">
                            <div class="flex-shrink-0 mt-1 mr-4">
                                @if ($approval->status === 'approved')
                                    <div class="p-2 bg-green-100 rounded-full">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                @elseif($approval->status === 'rejected')
                                    <div class="p-2 bg-red-100 rounded-full">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="p-2 bg-gray-100 rounded-full">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $approval->respondedBy->name ?? 'Not Yet Responded' }}
                                    </p>
                                    <span
                                        class="mt-1 text-xs text-gray-500 sm:mt-0">{{ $approval->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="mt-1 text-xs font-medium text-gray-600">{{ $approval->level }} -
                                    {{ ucfirst($approval->status) }}</p>
                                @if ($approval->notes)
                                    <div
                                        class="p-3 mt-2 text-sm text-gray-700 bg-white border rounded-lg shadow-inner">
                                        <p class="font-medium text-gray-800">Notes:</p>
                                        <p class="mt-1">{{ $approval->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- CASH OUT HISTORY -->
        {{-- @if ($detail->cashOuts->isNotEmpty())
            <div class="p-6 transition-all bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center pb-3 mb-4 border-b">
                    <div class="p-2 mr-3 rounded-lg bg-emerald-50">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800">Cash Out History</h4>
                </div>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-medium">Date</th>
                                <th class="px-4 py-3 font-medium">Amount</th>
                                <th class="px-4 py-3 font-medium">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($detail->cashOuts as $cashOut)
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $cashOut->tanggal?->format('d M Y') }}</td>
                                    <td class="px-4 py-3 font-semibold text-green-600">Rp {{ number_format($cashOut->jumlah, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ $cashOut->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif --}}

        <!-- METADATA -->
        <div class="p-6 text-sm text-gray-700 border border-gray-200 shadow-sm rounded-xl bg-gray-50">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Created By</p>
                        <p class="font-semibold text-gray-900">{{ $detail->createdBy->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Created Date</p>
                        <p class="font-semibold text-gray-900">{{ $detail->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @if ($detail->updatedBy)
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Updated By</p>
                            <p class="font-semibold text-gray-900">{{ $detail->updatedBy->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">Updated Date</p>
                            <p class="font-semibold text-gray-900">{{ $detail->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="flex justify-end px-6 py-4 bg-white border-t shadow-inner">
        <button wire:click="closeForm" type="button"
            class="px-6 py-2.5 text-sm font-medium text-gray-700 transition-all bg-gray-100 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50">
            Close
        </button>
    </div>

</div>
