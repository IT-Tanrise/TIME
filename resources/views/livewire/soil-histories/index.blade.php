{{-- resources/views/livewire/soil-histories/index.blade.php --}}
<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Soil Record History</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="font-medium">{{ $soil->businessUnit->name ?? 'N/A' }}</span> - 
                        <span class="font-medium">{{ $soil->land->lokasi_lahan ?? 'N/A' }}</span> - 
                        <span class="text-blue-600">{{ $soil->nama_penjual }}</span>
                    </p>
                </div>
                <button wire:click="backToSoil" 
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                        <select wire:model.live="filterAction" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">All Actions</option>
                            @foreach($availableActions as $action)
                                <option value="{{ $action['value'] }}">{{ $action['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <input type="text" wire:model.live="filterUser" placeholder="Search by user name..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" wire:model.live="filterDateFrom"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" wire:model.live="filterDateTo"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                </div>
                <div class="mt-4">
                    <button wire:click="resetFilters" 
                            class="inline-flex items-center px-3 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none transition ease-in-out duration-150">
                        Reset Filters
                    </button>
                </div>
            </div>

            <!-- History Timeline -->
            <div class="space-y-6">
                @forelse($histories as $history)
                    <div class="relative">
                        <!-- Timeline line -->
                        @if(!$loop->last)
                            <div class="absolute left-4 top-12 w-0.5 h-full bg-gray-200"></div>
                        @endif
                        
                        <div class="flex items-start">
                            <!-- Timeline dot -->
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                @if($history->action === 'created') bg-green-100 text-green-600
                                @elseif($history->action === 'updated') bg-blue-100 text-blue-600
                                @elseif($history->action === 'deleted') bg-red-100 text-red-600
                                @else bg-gray-100 text-gray-600 @endif">
                                @if($history->action === 'created')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($history->action === 'updated')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                @elseif($history->action === 'deleted')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 012 0v4a1 1 0 11-2 0V7zM12 7a1 1 0 012 0v4a1 1 0 11-2 0V7z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="ml-4 flex-1">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-sm font-medium text-gray-900">{{ $history->action_display }}</h3>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($history->action === 'created') bg-green-100 text-green-800
                                                @elseif($history->action === 'updated') bg-blue-100 text-blue-800
                                                @elseif($history->action === 'deleted') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($history->action) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $history->formatted_created_at }}
                                        </div>
                                    </div>
                                    
                                    <!-- User and IP info -->
                                    <div class="flex items-center text-xs text-gray-600 mb-3">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="mr-4">{{ $history->user_display }}</span>
                                        @if($history->ip_address)
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ $history->ip_address }}</span>
                                        @endif
                                    </div>

                                    <!-- Changes details -->
                                    @if($history->action === 'updated' && $history->changes)
                                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded">
                                            <div class="text-sm font-medium text-blue-800 mb-2">Changes Made:</div>
                                            <div class="text-sm text-blue-700">{{ $history->changes_summary }}</div>
                                            
                                            @php $changeDetails = $this->getChangeDetails($history) @endphp
                                            @if($changeDetails)
                                                <div class="mt-3 space-y-2">
                                                    @foreach($changeDetails as $change)
                                                        <div class="bg-white rounded p-2 border border-blue-200">
                                                            <div class="font-medium text-gray-900 text-xs mb-1">{{ $change['field'] }}</div>
                                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                                <div>
                                                                    <span class="text-red-600 font-medium">Before:</span>
                                                                    <div class="text-gray-600 mt-1 break-words">{{ $change['old'] }}</div>
                                                                </div>
                                                                <div>
                                                                    <span class="text-green-600 font-medium">After:</span>
                                                                    <div class="text-gray-600 mt-1 break-words">{{ $change['new'] }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($history->action === 'created')
                                        <div class="bg-green-50 border-l-4 border-green-400 p-3 rounded">
                                            <div class="text-sm text-green-700">New soil record was created</div>
                                        </div>
                                    @elseif($history->action === 'deleted')
                                        <div class="bg-red-50 border-l-4 border-red-400 p-3 rounded">
                                            <div class="text-sm text-red-700">Soil record was deleted</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No history found</h3>
                        <p class="mt-1 text-sm text-gray-500">No history records match your current filters.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($histories->hasPages())
                <div class="mt-6">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>