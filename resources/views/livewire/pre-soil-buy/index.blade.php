{{-- resources/views/livewire/pre-soil-buy/index.blade.php --}}
<div class="min-h-screen bg-gray-50">
    @if ($showForm)
        @include('livewire.pre-soil-buy.form')
    @elseif($showDetailForm)
        @include('livewire.pre-soil-buy.show')
    @else
        <p>{{ $showForm }}</p>
        <!-- Compact Header -->
        <div class="sticky top-0 z-10 bg-white border-b border-gray-200 shadow-sm">
            <div class="px-4 mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center space-x-3">
                        <button onclick="history.back()"
                            class="inline-flex items-center px-4 py-2.5 bg-gray-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-900">Pre Soil Buy</h1>
                    </div>
                    <div class="flex space-x-2">
                        <button wire:click="showCreateForm"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add New
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 py-4 mx-auto sm:px-6 lg:px-8">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 gap-4 mb-4 md:grid-cols-4">
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="text-xs font-medium text-gray-500 uppercase">Total</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                </div>
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm cursor-pointer hover:bg-yellow-50"
                    wire:click="$set('filterStatus', 'pending')">
                    <div class="text-xs font-medium text-gray-500 uppercase">Pending</div>
                    <div class="mt-1 text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                </div>
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm cursor-pointer hover:bg-green-50"
                    wire:click="$set('filterStatus', 'approved')">
                    <div class="text-xs font-medium text-gray-500 uppercase">Approved</div>
                    <div class="mt-1 text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
                </div>
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm cursor-pointer hover:bg-red-50"
                    wire:click="$set('filterStatus', 'rejected')">
                    <div class="text-xs font-medium text-gray-500 uppercase">Rejected</div>
                    <div class="mt-1 text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
                </div>
            </div>

            <!-- Search & Filter Section -->
            <div class="mb-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                <!-- Search Bar -->
                <div class="p-4">
                    <div class="flex flex-col gap-3 md:flex-row">
                        <!-- Search Input -->
                        <div class="flex-1">
                            <div class="relative">
                                <input type="text" wire:model.live="search"
                                    placeholder="Search by memo number, subject, or soil name..."
                                    class="w-full py-2 pl-10 pr-4 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <svg class="absolute w-5 h-5 text-gray-400 left-3 top-2.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>



                        <!-- Filter Toggle Button -->
                        <button wire:click="toggleFilters" type="button"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 {{ $showFilters ? 'bg-gray-300 text-gray-900' : '' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            Filters
                            @if ($this->isFiltered())
                                <span
                                    class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-blue-600 rounded-full">
                                    Active
                                </span>
                            @endif
                        </button>


                        <!-- Reset Filters Button -->
                        @if ($this->isFiltered())
                            <button wire:click="resetFilters" type="button"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </button>
                        @endif
                    </div>


                </div>


                <!-- Advanced Filters Panel -->
                @if ($showFilters)
                    <div class="px-4 pb-4 border-t border-gray-200 animate-slideDown">
                        <div class="grid grid-cols-1 gap-4 pt-4 md:grid-cols-3">
                            <!-- Status Filter -->
                            <div>
                                <label class="block mb-2 text-xs font-medium text-gray-700">Status</label>
                                <select wire:model.live="filterStatus"
                                    class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>

                            <!-- Date From Filter -->
                            <div>
                                <label class="block mb-2 text-xs font-medium text-gray-700">Date From</label>
                                <input type="date" wire:model.live="filterDateFrom"
                                    class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Date To Filter -->
                            <div>
                                <label class="block mb-2 text-xs font-medium text-gray-700">Date To</label>
                                <input type="date" wire:model.live="filterDateTo"
                                    class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>


                        </div>

                        <!-- Active Filters Display -->
                        @if ($this->isFiltered())
                            <div class="flex flex-wrap gap-2 pt-3 mt-3 border-t border-gray-200">
                                <span class="text-xs font-medium text-gray-500">Active Filters:</span>

                                @if ($filterStatus)
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                                        Status: {{ ucfirst($filterStatus) }}
                                        <button wire:click="$set('filterStatus', '')" class="ml-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @endif

                                @if ($filterDateFrom)
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                                        From: {{ \Carbon\Carbon::parse($filterDateFrom)->format('d M Y') }}
                                        <button wire:click="$set('filterDateFrom', '')" class="ml-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @endif

                                @if ($filterDateTo)
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                                        To: {{ \Carbon\Carbon::parse($filterDateTo)->format('d M Y') }}
                                        <button wire:click="$set('filterDateTo', '')" class="ml-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @endif

                                @if ($filterSoilId)
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-purple-800 bg-purple-100 rounded-full">
                                        Soil: {{ $allSoils->firstWhere('id', $filterSoilId)?->name }}
                                        <button wire:click="$set('filterSoilId', '')" class="ml-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Alert Messages -->
            @if (session()->has('message'))
                <div
                    class="flex items-center p-3 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500 rounded flash-message">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div
                    class="flex items-center p-3 mb-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded flash-message">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif


            <!-- Compact Table -->
            <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-3 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Memo No</th>
                                <th
                                    class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Date</th>
                                {{-- <th
            class="px-3 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
            Soil</th> --}}
                                <th
                                    class="px-3 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Subject</th>
                                <th
                                    class="px-3 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Area (m²)</th>
                                <th
                                    class="px-3 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Price</th>
                                <th
                                    class="px-3 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status</th>
                                <th
                                    class="px-2 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Actions</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($preSoilBuy as $item)
                                <tr class="transition-colors hover:bg-gray-50">
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->nomor_memo }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-gray-500">{{ $item->tanggal?->format('d M Y') }}
                                        </div>
                                    </td>
                                    {{-- <td class="px-3 py-3">
                                        <div class="text-sm text-gray-900">{{ $item->soils->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->soils->code ?? '-' }}</div>
                                    </td> --}}
                                    <td class="px-3 py-3">
                                        <div class="text-sm text-gray-900">
                                            {{ Str::limit($item->subject_perihal, 30) }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="text-sm text-gray-900">
                                            {{ number_format($item->luas, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-medium text-gray-900">Rp
                                            {{ number_format($item->kesepakatan_harga_jual_beli, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-500">Rp
                                            {{ number_format($item->harga_per_meter, 0, ',', '.') }}/m²</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if ($item->approval_status == 'approved') bg-green-100 text-green-800
                                            @elseif($item->approval_status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($item->approval_status == 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($item->approval_status) }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-3">
                                        <div class="flex items-center space-x-1">
                                            <!-- View Button -->
                                            <button wire:click="showDetail({{ $item->id }})"
                                                class="p-1 text-blue-600 transition-colors rounded hover:text-blue-900 hover:bg-blue-50"
                                                title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </button>

                                            <!-- Edit Button -->
                                            @if ($item->approval_status == 'pending')
                                                <button wire:click="showEditForm({{ $item->id }})"
                                                    class="p-1 text-yellow-600 transition-colors rounded hover:text-yellow-900 hover:bg-yellow-50"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif

                                            <!-- Delete Button blm kepake -->
                                            @can('pre-soil-buy.delete')
                                                <button wire:click="delete({{ $item->id }})"
                                                    wire:confirm="Are you sure you want to delete this record?"
                                                    class="p-1 text-red-600 transition-colors rounded hover:text-red-900 hover:bg-red-50"
                                                    title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        @if ($search || $this->isFiltered())
                                            <p class="text-sm font-medium">No records found matching your search or
                                                filters.</p>
                                            <p class="mt-1 text-xs">Try adjusting your search terms or clear the
                                                filters.</p>
                                        @else
                                            <p class="text-sm font-medium">No pre soil buy records found.</p>
                                            <p class="mt-1 text-xs">Get started by creating a new record.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Compact Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $preSoilBuy->links() }}
                </div>
            </div>
        </div>
    @endif
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        // Close dropdowns when clicking outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.relative').length) {
                if (window.Livewire) {
                    window.Livewire.dispatch('close-all-dropdowns');
                }
            }
        });

        // Handle escape key
        $(document).on('keydown', function(event) {
            if (event.key === 'Escape' && window.Livewire) {
                window.Livewire.dispatch('close-all-dropdowns');
            }
        });

        // Auto-hide flash messages after 5 seconds with fade effect
        setTimeout(function() {
            $('.flash-message').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);

        // Smooth animation for filter panel
        $('.animate-slideDown').hide().slideDown(300);

        // Highlight row on hover with smooth transition
        $('tbody tr').hover(
            function() {
                $(this).addClass('bg-blue-50');
            },
            function() {
                $(this).removeClass('bg-blue-50');
            }
        );

        // Statistics card click animation
        $('.cursor-pointer').on('click', function() {
            $(this).addClass('ring-2 ring-blue-500');
            setTimeout(() => {
                $(this).removeClass('ring-2 ring-blue-500');
            }, 300);
        });

        // Add loading indicator for filter changes
        let filterInputs = $('select[wire\\:model\\.live], input[wire\\:model\\.live]');
        filterInputs.on('change', function() {
            let $this = $(this);
            $this.css('opacity', '0.5');
            setTimeout(function() {
                $this.css('opacity', '1');
            }, 500);
        });

        // Tooltip for action buttons
        $('[title]').each(function() {
            $(this).tooltip({
                placement: 'top',
                trigger: 'hover'
            });
        });
    });
</script>
