<div>
    @if (session()->has('message'))
        <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter and Bulk Actions --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            <label class="text-sm font-medium text-gray-700">Filter:</label>
            <select wire:model.live="filterType"
                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="all">All Types</option>
                @can('land-data.approval')
                    <option value="land">Land Only</option>
                @endcan
                @canany(['soil-data.approval', 'soil-data-costs.approval', 'soil-data-interest-costs.approval'])
                    <option value="soil">Soil Only</option>
                @endcanany
                @can('pre-soil-buy.approval')
                    <option value="pre-soil-buy">Pre Soil Buy Only</option>
                @endcan
            </select>
        </div>

        @if (count($selectedApprovals) > 0)
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">{{ count($selectedApprovals) }} selected</span>
                <button wire:click="bulkApprove"
                    onclick="return confirm('Are you sure you want to approve {{ count($selectedApprovals) }} selected items?')"
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    Approve Selected
                </button>
                <button wire:click="showBulkRejectModal"
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Reject Selected
                </button>
            </div>
        @endif
    </div>

    {{-- Approvals Table --}}
    @if ($approvals->count() > 0)
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="w-12 px-4 py-3">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="text-indigo-600 border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            ID
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Type
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Action
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Details
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Requested By
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Date
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($approvals as $approval)
                        <tr class="hover:bg-gray-50">
                            {{-- Checkbox --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="checkbox" wire:model.live="selectedApprovals"
                                    value="{{ $approval->unique_id }}"
                                    class="text-indigo-600 border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </td>

                            {{-- ID --}}
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                #{{ $approval->id }}
                            </td>

                            {{-- Type Badge --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $typeColors = [
                                        'land' => 'bg-orange-100 text-orange-800',
                                        'soil' => 'bg-purple-100 text-purple-800',
                                        'pre-soil-buy' => 'bg-blue-100 text-blue-800',
                                    ];

                                    $colorClass = $typeColors[$approval->approval_type] ?? 'bg-gray-100 text-gray-800';
                                @endphp

                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $colorClass }}">
                                    {{ strtoupper($approval->approval_type) }}
                                </span>
                            </td>

                            {{-- Action Badge --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($approval->change_type === 'create')
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">
                                        CREATE
                                    </span>
                                @elseif($approval->change_type === 'delete')
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">
                                        DELETE
                                    </span>
                                @elseif($approval->change_type === 'costs')
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded">
                                        COSTS
                                    </span>
                                @elseif($approval->change_type === 'interest')
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold text-indigo-800 bg-indigo-100 rounded">
                                        INTEREST
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded">
                                        UPDATE
                                    </span>
                                @endif
                            </td>

                            {{-- Details --}}
                            <td class="max-w-xs px-4 py-3 text-sm text-gray-900">
                                @if ($approval->approval_type === 'land')
                                    @if ($approval->change_type != 'create')
                                        <div class="truncate" title="{{ $approval->land->lokasi_lahan ?? 'N/A' }}">
                                            {{ $approval->land->lokasi_lahan ?? 'N/A' }}
                                        </div>
                                    @else
                                        <div class="truncate"
                                            title="{{ $approval->new_data['lokasi_lahan'] ?? 'N/A' }}">
                                            {{ $approval->new_data['lokasi_lahan'] ?? 'New land entry' }}
                                        </div>
                                    @endif
                                @elseif($approval->approval_type === 'pre-soil-buy')
                                    @if ($approval->change_type == 'create')
                                        <div class="truncate"
                                            title="{{ $approval->preSoilBuy->subject_perihal ?? 'N/A' }}">
                                            {{ $approval->preSoilBuy->subject_perihal ?? 'N/A' }}
                                        </div>
                                    @else
                                        <div class="truncate"
                                            title="{{ $approval->new_data['subject_perihal'] ?? 'N/A' }}">
                                            {{ $approval->new_data['subject_perihal'] ?? 'N/A' }}
                                        </div>
                                    @endif
                                @else
                                    @if ($approval->change_type === 'create')
                                        <div class="truncate"
                                            title="{{ $approval->new_data['nama_penjual'] ?? 'N/A' }} - {{ $approval->new_data['letak_tanah'] ?? 'N/A' }}">
                                            {{ $approval->new_data['nama_penjual'] ?? 'N/A' }} -
                                            {{ $approval->new_data['letak_tanah'] ?? 'N/A' }}
                                        </div>
                                    @else
                                        <div class="truncate"
                                            title="{{ $approval->soil->nama_penjual ?? 'N/A' }} - {{ $approval->soil->letak_tanah ?? 'N/A' }}">
                                            {{ $approval->soil->nama_penjual ?? 'N/A' }} -
                                            {{ $approval->soil->letak_tanah ?? 'N/A' }}
                                        </div>
                                    @endif
                                @endif
                            </td>

                            {{-- Requested By --}}
                            <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                {{ $approval->requestedBy->name ?? 'N/A' }}
                            </td>

                            {{-- Date --}}
                            <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                {{ $approval->created_at->diffForHumans() }}
                            </td>

                            {{-- Table Actions --}}
                            <td class="px-4 py-3 text-sm font-medium text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-2">
                                    @if ($approval->approval_type === 'pre-soil-buy')
                                        <button wire:click="openPreSoilBuy('{{ $approval->pre_soil_buy_id }}')"
                                            class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="toggleDetails('{{ $approval->unique_id }}')"
                                            class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    @endif

                                    <button wire:click="showRejectModal('{{ $approval->unique_id }}')"
                                        class="text-red-600 hover:text-red-900" title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Expandable Details Row --}}
                        @if (isset($showDetails[$approval->unique_id]) && $showDetails[$approval->unique_id])
                            <tr>
                                <td colspan="8" class="px-4 py-4 bg-gray-50">
                                    <div class="text-sm">
                                        <h4 class="mb-3 font-medium text-gray-900">Proposed Changes:</h4>

                                        @php $details = $this->getChangeDetails($approval);@endphp

                                        @if ($approval->change_type === 'costs' && $approval->approval_type === 'soil')
                                            @php $changes = $this->getSoilCostChangeDetails($approval); @endphp
                                            @php $summary = $this->getCostChangeSummary($approval); @endphp
                                            @php $totals = $this->getCostDifference($approval); @endphp

                                            <div class="p-4 mt-2 bg-white border border-gray-200 rounded">
                                                <h5 class="font-medium text-gray-900">Cost Changes Summary:</h5>
                                                <p class="mt-2 text-sm text-gray-600">
                                                    {{ $summary['added'] }} added, {{ $summary['modified'] }}
                                                    modified, {{ $summary['deleted'] }} deleted
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Total change: {{ $totals['difference'] }}
                                                </p>

                                                <div class="mt-3 space-y-2">
                                                    @foreach ($changes as $change)
                                                        <div
                                                            class="p-2 border-l-4 
                                            @if ($change['type'] === 'added') border-green-400 bg-green-50
                                            @elseif($change['type'] === 'modified') border-yellow-400 bg-yellow-50
                                            @else border-red-400 bg-red-50 @endif">

                                                            <strong>{{ ucfirst($change['type']) }}:</strong>
                                                            {{ $change['description'] }}

                                                            @if ($change['type'] === 'modified')
                                                                @foreach ($change['changes'] as $field => $values)
                                                                    <br><small>{{ ucfirst($field) }}:
                                                                        {{ $values['old'] }} →
                                                                        {{ $values['new'] }}</small>
                                                                @endforeach
                                                            @else
                                                                <br><small>{{ $change['cost_type'] ?? '' }} -
                                                                    {{ $change['amount'] ?? '' }}
                                                                    ({{ $change['date_cost'] ?? '' }})
                                                                </small>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @elseif($approval->change_type === 'interest' && $approval->approval_type === 'soil')
                                            @php $changes = $this->getInterestChangeDetails($approval); @endphp
                                            @php $summary = $this->getInterestChangeSummary($approval); @endphp

                                            <div class="p-4 mt-2 bg-white border border-gray-200 rounded">
                                                <h5 class="font-medium text-gray-900">Interest Cost Changes Summary:
                                                </h5>
                                                <p class="mt-2 text-sm text-gray-600">
                                                    {{ $summary['added'] }} added, {{ $summary['modified'] }}
                                                    modified, {{ $summary['deleted'] }} deleted
                                                </p>

                                                <div class="mt-3 space-y-2">
                                                    @foreach ($changes as $change)
                                                        <div
                                                            class="p-2 border-l-4 
                                            @if ($change['type'] === 'added') border-green-400 bg-green-50
                                            @elseif($change['type'] === 'modified') border-yellow-400 bg-yellow-50
                                            @else border-red-400 bg-red-50 @endif">

                                                            <strong>{{ ucfirst($change['type']) }}:</strong>

                                                            @if ($change['type'] === 'added')
                                                                Period: {{ $change['start_date'] }} to
                                                                {{ $change['end_date'] }} ({{ $change['days'] }} days)
                                                                <br><small>Harga Perolehan:
                                                                    {{ $change['harga_perolehan'] }} | Interest:
                                                                    {{ $change['bunga'] }}%</small>
                                                                @if ($change['remarks'])
                                                                    <br><small>Remarks:
                                                                        {{ $change['remarks'] }}</small>
                                                                @endif
                                                            @elseif($change['type'] === 'deleted')
                                                                Period: {{ $change['start_date'] }} to
                                                                {{ $change['end_date'] }} ({{ $change['days'] }} days)
                                                                <br><small>Harga Perolehan:
                                                                    {{ $change['harga_perolehan'] }} | Interest:
                                                                    {{ $change['bunga'] }}%</small>
                                                                @if ($change['remarks'])
                                                                    <br><small>Remarks:
                                                                        {{ $change['remarks'] }}</small>
                                                                @endif
                                                            @elseif($change['type'] === 'modified')
                                                                Period: {{ $change['period'] }}
                                                                @foreach ($change['changes'] as $field => $values)
                                                                    <br><small>{{ ucfirst($field) }}:
                                                                        {{ $values['old'] }} →
                                                                        {{ $values['new'] }}</small>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="overflow-hidden bg-white border border-gray-200 rounded">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-100">
                                                        <tr>
                                                            <th
                                                                class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">
                                                                Field</th>
                                                            @if (isset($details[0]['old']) && isset($details[0]['new']))
                                                                <th
                                                                    class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">
                                                                    Old Value</th>
                                                                <th
                                                                    class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">
                                                                    New Value</th>
                                                            @else
                                                                <th
                                                                    class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">
                                                                    Value</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200">
                                                        @foreach ($details as $detail)
                                                            <tr>
                                                                <td
                                                                    class="px-4 py-2 text-sm font-medium text-gray-700">
                                                                    {{ $detail['field'] }}</td>
                                                                @if (isset($detail['old']) && isset($detail['new']))
                                                                    <td
                                                                        class="px-4 py-2 text-sm text-red-600 line-through">
                                                                        {{ $detail['old'] }}</td>
                                                                    <td
                                                                        class="px-4 py-2 text-sm font-medium text-green-600">
                                                                        {{ $detail['new'] }}</td>
                                                                @else
                                                                    <td colspan="2"
                                                                        class="px-4 py-2 text-sm text-gray-600">
                                                                        {{ $detail['value'] }}</td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>

                {{-- MODAL PRE SOIL BUY - PINDAHKAN KE LUAR LOOP --}}
                @if ($showModalPreSoilBuy && $selectedPreSoilBuy)
                    <div x-data="{ show: @entangle('showModalPreSoilBuy') }" x-show="show" x-cloak @click.self="show = false"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                        <div x-show="show"
                            class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto bg-white shadow-2xl rounded-2xl"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-90" @click.stop>

                            {{-- Header --}}
                            <div
                                class="sticky top-0 z-10 flex items-center justify-between p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Pre Soil Buy Details</h2>
                                    <p class="mt-1 text-sm text-gray-600">Complete information about soil purchase
                                        proposal</p>
                                </div>
                                <button wire:click="closeModalSoilBuy"
                                    class="p-2 text-gray-400 transition-colors rounded-full hover:bg-white hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Content --}}
                            <div class="p-6 space-y-6">
                                {{-- Memo Info --}}
                                <div class="p-4 border-l-4 border-blue-500 rounded-r-lg bg-blue-50">
                                    <h3 class="mb-3 text-lg font-semibold text-blue-900">Memo Information</h3>
                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                        <div>
                                            <label class="text-xs font-medium text-gray-600 uppercase">Nomor
                                                Memo</label>
                                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                                {{ $selectedPreSoilBuy->nomor_memo }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-gray-600 uppercase">Tanggal</label>
                                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                                {{ $selectedPreSoilBuy->tanggal }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Recipients --}}
                                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                    <h3 class="mb-3 text-lg font-semibold text-gray-900">Recipients</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="text-xs font-medium text-gray-600 uppercase">Dari</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $selectedPreSoilBuy->dari }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-gray-600 uppercase">Kepada</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $selectedPreSoilBuy->kepada }}
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-gray-600 uppercase">CC</label>
                                            <p class="mt-1 text-sm text-gray-900">{{ $selectedPreSoilBuy->cc }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Subject --}}
                                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                    <label class="text-xs font-medium text-gray-600 uppercase">Subject /
                                        Perihal</label>
                                    <p class="mt-2 text-base text-gray-900">{{ $selectedPreSoilBuy->subject_perihal }}
                                    </p>
                                </div>

                                {{-- Property Details --}}
                                <div class="p-4 border-l-4 border-green-500 rounded-r-lg bg-green-50">
                                    <h3 class="mb-3 text-lg font-semibold text-green-900">Property Details</h3>
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="text-xs font-medium text-gray-600 uppercase">Penjual</label>
                                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                                {{ $selectedPreSoilBuy->subject_penjual }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-gray-600 uppercase">Luas
                                                Tanah</label>
                                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                                {{ number_format($selectedPreSoilBuy->luas, 0, ',', '.') }} m²</p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-xs font-medium text-gray-600 uppercase">Objek Jual
                                                Beli</label>
                                            <p class="mt-1 text-sm text-gray-900">
                                                {{ $selectedPreSoilBuy->objek_jual_beli }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pricing --}}
                                <div class="p-4 border-l-4 border-purple-500 rounded-r-lg bg-purple-50">
                                    <h3 class="mb-3 text-lg font-semibold text-purple-900">Pricing Information</h3>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Harga Kesepakatan</span>
                                            <span class="text-lg font-bold text-purple-900">
                                                Rp
                                                {{ number_format($selectedPreSoilBuy->kesepakatan_harga_jual_beli, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Harga per m²</span>
                                            <span class="text-lg font-bold text-purple-900">
                                                Rp
                                                {{ number_format($selectedPreSoilBuy->harga_per_meter, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- File View --}}
                                <div class="p-4 mt-4 border-l-4 border-indigo-500 rounded-r-lg bg-indigo-50">
                                    <h3 class="mb-3 text-lg font-semibold text-indigo-900">Document</h3>

                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                        <span class="text-sm font-medium text-gray-600">File IM</span>

                                        @if ($selectedPreSoilBuy->upload_file_im)
                                            <a href="{{ asset('storage/' . $selectedPreSoilBuy->upload_file_im) }}"
                                                target="_blank"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </a>
                                        @else
                                            <span class="text-sm italic text-gray-400">No file uploaded</span>
                                        @endif
                                    </div>
                                </div>


                                {{-- Created By --}}
                                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                    <label class="text-xs font-medium text-gray-600 uppercase">Dibuat Oleh</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $selectedPreSoilBuy->createdBy->name ?? 'N/A' }}</p>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="sticky bottom-0 flex justify-end gap-3 p-6 border-t bg-gray-50">
                                <button wire:click="closeModalSoilBuy"
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </table>
        </div>
    @else
        <div class="py-12 text-center bg-white rounded-lg shadow-sm">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending approvals</h3>
            <p class="mt-1 text-sm text-gray-500">All changes have been processed.</p>
        </div>
    @endif

    {{-- Rejection Modal --}}
    @if ($showRejectionModal)
        <div class="fixed inset-0 z-50 w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
            <div class="relative p-5 mx-auto bg-white border rounded-md shadow-lg top-20 w-96">
                <div class="mt-3">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">
                        {{ $bulkAction ? 'Reject Selected Approvals' : 'Reject Approval' }}
                    </h3>
                    <p class="mb-4 text-sm text-gray-600">
                        Please provide a reason for rejecting {{ $bulkAction ? 'these approvals' : 'this approval' }}:
                    </p>
                    <textarea wire:model="rejectionReason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        rows="4" placeholder="Enter rejection reason (minimum 5 characters)..."></textarea>

                    @error('rejectionReason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-end mt-4 space-x-2">
                        <button wire:click="hideRejectModal"
                            class="px-4 py-2 text-gray-700 bg-gray-300 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button wire:click="rejectSelected"
                            class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                            Reject {{ $bulkAction ? 'Selected' : '' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('reload-page', () => {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });
        });
    </script>
</div>
