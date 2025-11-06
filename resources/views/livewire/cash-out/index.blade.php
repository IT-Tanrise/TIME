<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div
            class="relative p-4 mb-4 overflow-hidden text-sm text-green-800 border border-green-200 bg-green-50 rounded-xl">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 -mt-16 -mr-16 bg-green-100 rounded-full opacity-20"></div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="relative p-4 mb-4 overflow-hidden text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-32 h-32 -mt-16 -mr-16 bg-red-100 rounded-full opacity-20"></div>
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="mb-6">
        <div class="p-1 bg-gray-100 rounded-xl w-fit">
            <nav class="flex space-x-1">
                <button wire:click="setFilter('pending')"
                    class="relative px-6 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 {{ $filter === 'pending' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <span class="relative z-10">Pending</span>

                </button>
                <button wire:click="setFilter('done')"
                    class="relative px-6 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 {{ $filter === 'done' ? 'bg-white text-green-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <span class="relative z-10">Completed</span>

                </button>
            </nav>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        @if ($cashOuts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                Memo No</th>
                            <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                Pre-Soil-Buy ID</th>
                            <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                Requested By</th>
                            <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                Status</th>
                            <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                Requested At</th>
                            @if ($filter === 'done')
                                <th
                                    class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                    Responded By</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                    Responded At</th>
                            @endif
                            <th class="px-6 py-4 text-xs font-bold tracking-wider text-left text-gray-700 uppercase">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($cashOuts as $cashOut)
                            <tr
                                class="transition-all duration-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900">
                                        {{ $cashOut->preSoilBuy->nomor_memo ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-lg">
                                        #{{ $cashOut->preSoilBuy->id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-10 h-10 text-white rounded-full shadow-md bg-gradient-to-br from-blue-500 to-blue-600">
                                            <span class="text-sm font-bold">
                                                {{ substr($cashOut->preSoilBuy->createdBy->name ?? 'U', 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-semibold text-gray-900">
                                                {{ $cashOut->preSoilBuy->createdBy->name ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold shadow-sm {{ $cashOut->status === 'pending' ? 'bg-gradient-to-r from-yellow-400 to-orange-400 text-white' : 'bg-gradient-to-r from-green-400 to-emerald-500 text-white' }}">
                                        <span class="w-1.5 h-1.5 mr-2 rounded-full bg-white animate-pulse"></span>
                                        {{ $cashOut->status === 'pending' ? 'Pending Cash Out' : 'Completed' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $cashOut->created_at->format('M d, Y H:i') }}
                                </td>
                                @if ($filter === 'done')
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex items-center justify-center flex-shrink-0 w-10 h-10 text-white rounded-full shadow-md bg-gradient-to-br from-green-500 to-emerald-600">
                                                <span class="text-sm font-bold">
                                                    {{ substr($cashOut->respondedBy->name ?? 'A', 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-semibold text-gray-900">
                                                    {{ $cashOut->respondedBy->name ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                        {{ $cashOut->responded_at ? $cashOut->responded_at->format('M d, Y H:i') : '-' }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="openModal({{ $cashOut->preSoilBuy->id }})"
                                            class="inline-flex items-center px-4 py-2 text-xs font-semibold text-gray-700 transition-all duration-200 bg-white border-2 border-gray-300 rounded-lg shadow-sm hover:border-blue-500 hover:text-blue-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </button>
                                        @if ($filter === 'pending')
                                            <button wire:click="processCashOut({{ $cashOut->id }})"
                                                wire:confirm="Are you sure you want to process this cash out request?"
                                                class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white transition-all duration-200 rounded-lg shadow-md bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Cash Out
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center">
                <div class="flex justify-center mb-4">
                    <div class="p-4 bg-gray-100 rounded-full">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-base font-semibold text-gray-900">No cash out requests</h3>
                <p class="mt-2 text-sm text-gray-500">
                    No {{ $filter }} cash out requests found at the moment.
                </p>
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if ($showModal && $preSoilBuyDetails)
        <!-- Modal Backdrop dengan Blur -->
        <div class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm" aria-hidden="true"></div>

        <!-- Modal -->
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 sm:p-0">
                <!-- Modal Panel -->
                <div class="relative w-full transition-all transform bg-white shadow-2xl rounded-2xl sm:max-w-3xl">
                    <!-- Header -->
                    <div
                        class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-2xl">
                        <div class="flex items-center">
                            <div
                                class="flex items-center justify-center w-10 h-10 mr-3 bg-blue-600 rounded-lg shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                                Pre-Soil-Buy Details
                            </h3>
                        </div>
                        <button wire:click="closeModal" type="button"
                            class="p-2 text-gray-400 transition-all duration-200 rounded-lg hover:text-gray-600 hover:bg-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                        <div class="space-y-6">
                            {{-- Basic Information --}}
                            <div
                                class="p-5 border-2 border-blue-100 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl">
                                <h4 class="flex items-center mb-4 text-base font-bold text-gray-900">
                                    <div class="w-1 h-6 mr-3 bg-blue-600 rounded-full"></div>
                                    Basic Information
                                </h4>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Memo
                                            Number</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                            {{ $preSoilBuyDetails->nomor_memo ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Date</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                            {{ $preSoilBuyDetails->tanggal ? \Carbon\Carbon::parse($preSoilBuyDetails->tanggal)->format('M d, Y') : '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">From</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                            {{ $preSoilBuyDetails->dari ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">To</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                            {{ $preSoilBuyDetails->kepada ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- CC Information --}}
                            <div class="p-5 border-2 border-gray-100 bg-gray-50 rounded-xl">
                                <label class="block text-xs font-bold tracking-wide text-gray-600 uppercase">CC</label>
                                <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                    {{ $preSoilBuyDetails->cc ?? '-' }}</p>
                            </div>

                            {{-- Subject Information --}}
                            <div
                                class="p-5 border-2 border-purple-100 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl">
                                <h4 class="flex items-center mb-4 text-base font-bold text-gray-900">
                                    <div class="w-1 h-6 mr-3 bg-purple-600 rounded-full"></div>
                                    Subject Information
                                </h4>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Subject
                                            Matter</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                            {{ $preSoilBuyDetails->subject_perihal ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Seller</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                            {{ $preSoilBuyDetails->subject_penjual ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Transaction Details --}}
                            <div
                                class="p-5 border-2 border-green-100 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl">
                                <h4 class="flex items-center mb-4 text-base font-bold text-gray-900">
                                    <div class="w-1 h-6 mr-3 bg-green-600 rounded-full"></div>
                                    Transaction Details
                                </h4>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Area
                                            (m²)</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                            {{ number_format($preSoilBuyDetails->luas ?? 0) }} m²</p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Price
                                            per m²</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">Rp
                                            {{ number_format($preSoilBuyDetails->harga_per_meter ?? 0) }}</p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Total
                                            Price</label>
                                        <p class="mt-1.5 text-base font-bold text-green-700">
                                            Rp
                                            {{ number_format($preSoilBuyDetails->kesepakatan_harga_jual_beli ?? 0) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label
                                        class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Transaction
                                        Object</label>
                                    <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                        {{ $preSoilBuyDetails->objek_jual_beli ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- File Attachment --}}
                            @if ($preSoilBuyDetails->upload_file_im)
                                <div class="p-5 border-2 bg-amber-50 border-amber-100 rounded-xl">
                                    <label
                                        class="block mb-3 text-xs font-bold tracking-wide text-gray-600 uppercase">Attached
                                        File</label>
                                    <a href="{{ asset('storage/' . $preSoilBuyDetails->upload_file_im) }}"
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-gradient-to-r from-amber-500 to-orange-500 rounded-lg shadow-md hover:from-amber-600 hover:to-orange-600 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download File
                                    </a>
                                </div>
                            @endif

                            {{-- Created Information --}}
                            <div class="p-5 border-2 border-gray-100 bg-gray-50 rounded-xl">
                                <h4 class="flex items-center mb-4 text-base font-bold text-gray-900">
                                    <div class="w-1 h-6 mr-3 bg-gray-600 rounded-full"></div>
                                    System Information
                                </h4>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Created
                                            By</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                            {{ $preSoilBuyDetails->createdBy->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold tracking-wide text-gray-600 uppercase">Last
                                            Updated By</label>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                            {{ $preSoilBuyDetails->updatedBy->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end px-6 py-4 border-t border-gray-200 rounded-b-2xl bg-gray-50">
                        <button wire:click="closeModal" type="button"
                            class="px-6 py-2.5 text-sm font-semibold text-gray-700 transition-all duration-200 bg-white border-2 border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
