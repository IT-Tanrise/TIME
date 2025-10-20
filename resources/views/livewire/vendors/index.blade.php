{{-- resources/views/livewire/vendors/index.blade.php --}}
<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <!-- Compact Header -->
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <!-- Title Section -->
                <div class="flex items-center gap-2">
                    <button onclick="history.back()" 
                            class="inline-flex items-center p-1.5 bg-gray-600 rounded-lg text-white hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Vendors & Suppliers</h1>
                        <p class="text-xs text-gray-500">Manage vendor contracts and purchase orders</p>
                    </div>
                </div>
            </div>

            <!-- Compact Filters -->
            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                <div class="lg:col-span-2">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search vendor, contact, entity, project..."
                           class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <select wire:model.live="filterType" 
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        @foreach($types as $type)
                            <option value="{{ $type->creditor_type }}">
                                {{ $type->descs }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model.live="filterEntity" 
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Entities</option>
                        @foreach($entities as $entity)
                            <option value="{{ $entity->entity_cd }}">
                                {{ $entity->entity_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button wire:click="resetFilters" 
                            type="button"
                            class="w-full px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg transition">
                        Reset Filters
                    </button>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if($search || $filterType || $filterEntity)
                <div class="mt-2 flex flex-wrap gap-2">
                    @if($search)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Search: {{ $search }}
                            <button wire:click="$set('search', '')" class="ml-1 hover:text-blue-900">×</button>
                        </span>
                    @endif
                    @if($filterType)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Type: {{ $types->firstWhere('creditor_type', $filterType)?->descs ?? $filterType }}
                            <button wire:click="$set('filterType', '')" class="ml-1 hover:text-purple-900">×</button>
                        </span>
                    @endif
                    @if($filterEntity)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Entity: {{ $entities->firstWhere('entity_cd', $filterEntity)?->entity_name ?? $filterEntity }}
                            <button wire:click="$set('filterEntity', '')" class="ml-1 hover:text-green-900">×</button>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Compact Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vendor Name</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Entity</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Contract/PO</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Contact Person</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($data as $vendor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">
                                <div class="text-sm font-medium text-gray-900">{{ $vendor->name }}</div>
                                <div class="text-xs text-gray-500">{{ $vendor->creditor_acct }}</div>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $vendor->type_descs }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <div class="text-sm text-gray-900">{{ $vendor->entity_name }}</div>
                                <div class="text-xs text-gray-500">{{ $vendor->entity_cd }}</div>
                            </td>
                            <td class="px-3 py-2">
                                @if($vendor->contract_no)
                                    <div class="text-sm font-medium text-blue-600">
                                        <span class="inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            {{ $vendor->contract_no }}
                                        </span>
                                    </div>
                                @endif
                                @if($vendor->order_no)
                                    <div class="text-sm font-medium text-green-600">
                                        <span class="inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            {{ $vendor->order_no }}
                                        </span>
                                    </div>
                                @endif
                                @if(!$vendor->contract_no && !$vendor->order_no)
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($vendor->project_descs)
                                    <div class="text-sm text-gray-900">{{ $vendor->project_descs }}</div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                                @if($vendor->remarks)
                                    <div class="text-xs text-gray-500" title="{{ $vendor->remarks }}">
                                        {{ Str::limit($vendor->remarks, 30) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($vendor->award_dt)
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Award:</span> {{ \Carbon\Carbon::parse($vendor->award_dt)->format('d M Y') }}
                                    </div>
                                @endif
                                @if($vendor->start_dt && $vendor->end_dt)
                                    <div class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($vendor->start_dt)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($vendor->end_dt)->format('d M Y') }}
                                    </div>
                                @elseif(!$vendor->award_dt)
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                {{ $vendor->contact_person ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-sm font-medium">
                                        @if($search || $filterType || $filterEntity)
                                            No vendors match your filters
                                        @else
                                            No vendors available
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($data->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $data->links() }}
            </div>
        @endif
    </div>
</div>