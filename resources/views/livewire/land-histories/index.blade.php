<div class="min-h-screen bg-gray-50">
    <!-- Sticky Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="px-4 sm:px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <button wire:click="backToLand" 
                            class="inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-lg text-sm text-white font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Lands
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Land History</h2>
                        <p class="text-sm text-gray-600 mt-0.5">
                            <span class="font-medium">{{ $land->lokasi_lahan }}</span>
                            @if($land->kota_kabupaten)
                                <span class="text-gray-400 mx-1">•</span>
                                <span class="text-gray-500">{{ $land->kota_kabupaten }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                    <button wire:click="resetFilters" 
                            class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset Filters
                    </button>
                    <button onclick="expandAll()" 
                            class="inline-flex items-center px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg text-sm font-medium text-blue-700 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"/>
                        </svg>
                        Expand All
                    </button>
                    <button onclick="collapseAll()" 
                            class="inline-flex items-center px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-150">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                        Collapse All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-4 sm:px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Action Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Action Type</label>
                    <select wire:model.live="filterAction" 
                            class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-150">
                        <option value="">All Actions</option>
                        @foreach($availableActions as $action)
                            <option value="{{ $action['value'] }}">{{ $action['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Column Filter -->
                <div class="{{ in_array($filterAction, ['updated', 'approved_update']) ? '' : 'opacity-50' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Field Changed
                        @if(!in_array($filterAction, ['updated', 'approved_update']))
                            <span class="text-xs text-gray-400">(Select update action)</span>
                        @endif
                    </label>
                    <select wire:model.live="filterColumn" 
                            {{ !in_array($filterAction, ['updated', 'approved_update']) ? 'disabled' : '' }}
                            class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-150 {{ !in_array($filterAction, ['updated', 'approved_update']) ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        <option value="">All Fields</option>
                        @if(in_array($filterAction, ['updated', 'approved_update']))
                            @foreach($availableColumns as $column)
                                <option value="{{ $column['value'] }}">{{ $column['label'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">User</label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="filterUser" 
                           placeholder="Search by user..."
                           class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-150">
                </div>
                
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">From Date</label>
                    <input type="date" 
                           wire:model.live="filterDateFrom"
                           class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-150">
                </div>
                
                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">To Date</label>
                    <input type="date" 
                           wire:model.live="filterDateTo"
                           class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-150">
                </div>
            </div>
            
            <!-- Active Filters Display -->
            @if($filterAction || $filterColumn || $filterUser || $filterDateFrom || $filterDateTo)
                <div class="mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-medium text-indigo-900">Active Filters:</span>
                        @if($filterAction)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ collect($availableActions)->firstWhere('value', $filterAction)['label'] ?? $filterAction }}
                                <button wire:click="$set('filterAction', '')" class="ml-1.5 text-indigo-600 hover:text-indigo-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        @if($filterColumn && in_array($filterAction, ['updated', 'approved_update']))
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ collect($availableColumns)->firstWhere('value', $filterColumn)['label'] ?? $filterColumn }}
                                <button wire:click="$set('filterColumn', '')" class="ml-1.5 text-green-600 hover:text-green-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        @if($filterUser)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                User: {{ $filterUser }}
                                <button wire:click="$set('filterUser', '')" class="ml-1.5 text-purple-600 hover:text-purple-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        @if($filterDateFrom)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                From: {{ \Carbon\Carbon::parse($filterDateFrom)->format('M d, Y') }}
                                <button wire:click="$set('filterDateFrom', '')" class="ml-1.5 text-orange-600 hover:text-orange-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        @if($filterDateTo)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                To: {{ \Carbon\Carbon::parse($filterDateTo)->format('M d, Y') }}
                                <button wire:click="$set('filterDateTo', '')" class="ml-1.5 text-orange-600 hover:text-orange-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="overflow-y-auto" style="height: calc(100vh - 240px);">
        <div class="px-4 sm:px-6 py-6">
            <!-- History Timeline -->
            <div class="space-y-4">
                @forelse($histories as $history)
                    <div class="relative">
                        <!-- Timeline Line -->
                        @if(!$loop->last)
                            <div class="absolute left-5 top-14 w-0.5 h-full bg-gradient-to-b from-gray-300 to-transparent"></div>
                        @endif
                        
                        <div class="flex gap-4">
                            <!-- Timeline Icon -->
                            <div class="relative flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center z-10 shadow-md
                                @if($history->action === 'created' || $history->action === 'approved_creation') bg-gradient-to-br from-green-400 to-green-600 text-white
                                @elseif(in_array($history->action, ['updated', 'approved_update'])) bg-gradient-to-br from-blue-400 to-blue-600 text-white
                                @elseif(in_array($history->action, ['deleted', 'approved_deletion'])) bg-gradient-to-br from-red-400 to-red-600 text-white
                                @elseif($history->action === 'rejected') bg-gradient-to-br from-yellow-400 to-yellow-600 text-white
                                @else bg-gradient-to-br from-gray-400 to-gray-600 text-white @endif">
                                
                                @if($history->isApprovedChange())
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($history->action === 'created')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif(in_array($history->action, ['updated']))
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                @elseif(in_array($history->action, ['deleted']))
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($history->action === 'rejected')
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            
                            <!-- Content Card -->
                            <div class="flex-1">
                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                                    <!-- Header -->
                                    <button type="button" 
                                            onclick="toggleHistory('history-{{ $history->id }}')"
                                            class="w-full px-5 py-4 text-left hover:bg-gray-50 focus:outline-none rounded-xl transition-colors duration-150">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <h3 class="text-base font-semibold text-gray-900">{{ $history->action_display }}</h3>
                                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full
                                                        @if($history->action === 'created' || $history->action === 'approved_creation') bg-green-100 text-green-800
                                                        @elseif(in_array($history->action, ['updated', 'approved_update'])) bg-blue-100 text-blue-800
                                                        @elseif(in_array($history->action, ['deleted', 'approved_deletion'])) bg-red-100 text-red-800
                                                        @elseif($history->action === 'rejected') bg-yellow-100 text-yellow-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        @if($history->isApprovedChange())
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Approved
                                                        @else
                                                            {{ ucfirst(str_replace('_', ' ', $history->action)) }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                        </svg>
                                                        {{ $history->user_display }}
                                                    </span>
                                                    <span class="flex items-center text-gray-500">
                                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                        </svg>
                                                        {{ $history->formatted_created_at }}
                                                    </span>
                                                </div>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200 chevron-icon flex-shrink-0"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </button>
                                    
                                    <!-- Collapsible Content -->
                                    <div id="history-{{ $history->id }}" class="history-content hidden border-t border-gray-100">
                                        <div class="px-5 py-4 bg-gray-50 rounded-b-xl">
                                            <!-- Approval/Rejection Info -->
                                            @if($history->isApprovedChange())
                                                @php $approvalMetadata = $history->getApprovalMetadata() @endphp
                                                @if($approvalMetadata && isset($approvalMetadata['approved_by']))
                                                    @php $approver = \App\Models\User::find($approvalMetadata['approved_by']) @endphp
                                                    @if($approver)
                                                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                            <div class="flex items-center text-sm">
                                                                <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                </svg>
                                                                <span class="font-medium text-green-900">Approved by: {{ $approver->name }}</span>
                                                                @if(isset($approvalMetadata['requested_by']))
                                                                    @php $requester = \App\Models\User::find($approvalMetadata['requested_by']) @endphp
                                                                    @if($requester)
                                                                        <span class="ml-3 text-green-700">• Requested by: {{ $requester->name }}</span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                            
                                            @if($history->action === 'rejected')
                                                @php $approvalMetadata = $history->getApprovalMetadata() @endphp
                                                @if($approvalMetadata && isset($approvalMetadata['rejected_by']))
                                                    @php $rejector = \App\Models\User::find($approvalMetadata['rejected_by']) @endphp
                                                    @if($rejector)
                                                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                            <div class="flex items-center text-sm mb-2">
                                                                <svg class="w-4 h-4 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                </svg>
                                                                <span class="font-medium text-yellow-900">Rejected by: {{ $rejector->name }}</span>
                                                                @if(isset($approvalMetadata['requested_by']))
                                                                    @php $requester = \App\Models\User::find($approvalMetadata['requested_by']) @endphp
                                                                    @if($requester)
                                                                        <span class="ml-3 text-yellow-700">• Requested by: {{ $requester->name }}</span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                            @if(isset($approvalMetadata['rejection_reason']))
                                                                <div class="text-sm text-yellow-800 bg-white px-3 py-2 rounded border border-yellow-100">
                                                                    <span class="font-medium">Reason:</span> {{ $approvalMetadata['rejection_reason'] }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif

                                            <!-- Additional Metadata -->
                                            @if($history->ip_address)
                                                <div class="mb-3 flex items-center text-xs text-gray-500">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    IP Address: {{ $history->ip_address }}
                                                </div>
                                            @endif

                                            <!-- Changes Details -->
                                            @php $changeDetails = $this->getChangeDetails($history) @endphp
                                            
                                            @if(in_array($history->action, ['updated', 'approved_update']))
                                                @if($changeDetails)
                                                    <div class="bg-white border border-{{ $history->isApprovedChange() ? 'green' : 'blue' }}-200 rounded-lg p-4">
                                                        <h4 class="text-sm font-semibold text-{{ $history->isApprovedChange() ? 'green' : 'blue' }}-900 mb-3">
                                                            {{ $history->isApprovedChange() ? 'Approved Changes' : 'Changes Made' }}
                                                        </h4>
                                                        <div class="space-y-3">
                                                            @foreach($changeDetails as $change)
                                                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                                                    <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                                                                        <span class="font-medium text-sm text-gray-900">{{ $change['field'] }}</span>
                                                                    </div>
                                                                    <div class="grid grid-cols-2 divide-x divide-gray-200">
                                                                        <div class="p-3 bg-red-50">
                                                                            <div class="text-xs font-semibold text-red-700 mb-1.5">Before</div>
                                                                            <div class="text-sm text-gray-800 break-words">{{ $change['old'] ?: 'Empty' }}</div>
                                                                        </div>
                                                                        <div class="p-3 bg-green-50">
                                                                            <div class="text-xs font-semibold text-green-700 mb-1.5">After</div>
                                                                            <div class="text-sm text-gray-800 break-words">{{ $change['new'] ?: 'Empty' }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                            @elseif(in_array($history->action, ['created', 'approved_creation']))
                                                <div class="bg-white border border-green-200 rounded-lg p-4">
                                                    <h4 class="text-sm font-semibold text-green-900 mb-3">
                                                        {{ $history->action === 'approved_creation' ? 'Approved Creation Data' : 'Created Data' }}
                                                    </h4>
                                                    @if($changeDetails)
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            @foreach($changeDetails as $detail)
                                                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                                                    <div class="text-xs font-semibold text-green-800 mb-1">{{ $detail['field'] }}</div>
                                                                    <div class="text-sm text-gray-900 break-words">{{ $detail['value'] }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                            @elseif(in_array($history->action, ['deleted', 'approved_deletion']))
                                                <div class="bg-white border border-red-200 rounded-lg p-4">
                                                    <h4 class="text-sm font-semibold text-red-900 mb-3">
                                                        {{ $history->action === 'approved_deletion' ? 'Deleted Record Summary' : 'Deleted Record Data' }}
                                                    </h4>
                                                    
                                                    @if($history->new_values && isset($history->new_values['deletion_reason']))
                                                        <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded-lg">
                                                            <div class="text-xs font-semibold text-red-800 mb-1">Deletion Reason:</div>
                                                            <div class="text-sm text-gray-900">{{ $history->new_values['deletion_reason'] }}</div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($changeDetails)
                                                        <div class="space-y-2">
                                                            <div class="text-xs font-medium text-gray-700 mb-2">Record Information:</div>
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                @foreach($changeDetails as $detail)
                                                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                                                        <div class="text-xs font-semibold text-gray-700 mb-1">{{ $detail['field'] }}</div>
                                                                        <div class="text-sm text-gray-900 break-words">{{ $detail['value'] }}</div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                            @elseif($history->action === 'rejected')
                                                @php $metadata = $history->getApprovalMetadata(); @endphp
                                                <div class="bg-white border border-yellow-200 rounded-lg p-4">
                                                    <h4 class="text-sm font-semibold text-yellow-900 mb-3">
                                                        Rejected Request Details
                                                    </h4>
                                                    
                                                    @if($metadata && isset($metadata['change_type']))
                                                        <div class="mb-3 text-sm">
                                                            <span class="font-medium text-gray-700">Change Type:</span>
                                                            <span class="ml-2 px-2.5 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                                                {{ ucfirst(str_replace('_', ' ', $metadata['change_type'])) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($changeDetails)
                                                        <div class="space-y-3 mt-4">
                                                            @if(isset($changeDetails[0]['type']))
                                                                @if($changeDetails[0]['type'] === 'change')
                                                                    <div class="text-xs font-medium text-gray-700 mb-2">Rejected Changes:</div>
                                                                    @foreach($changeDetails as $change)
                                                                        <div class="border border-yellow-200 rounded-lg overflow-hidden">
                                                                            <div class="bg-yellow-50 px-3 py-2 border-b border-yellow-200">
                                                                                <span class="font-medium text-sm text-gray-900">{{ $change['field'] }}</span>
                                                                            </div>
                                                                            <div class="grid grid-cols-2 divide-x divide-yellow-200">
                                                                                <div class="p-3 bg-gray-50">
                                                                                    <div class="text-xs font-semibold text-gray-700 mb-1.5">Current</div>
                                                                                    <div class="text-sm text-gray-800 break-words">{{ $change['old'] ?: 'Empty' }}</div>
                                                                                </div>
                                                                                <div class="p-3 bg-yellow-50">
                                                                                    <div class="text-xs font-semibold text-yellow-700 mb-1.5">Rejected</div>
                                                                                    <div class="text-sm text-gray-800 break-words">{{ $change['new'] ?: 'Empty' }}</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                    
                                                                @elseif($changeDetails[0]['type'] === 'create')
                                                                    <div class="text-xs font-medium text-gray-700 mb-2">Rejected Creation Data:</div>
                                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                        @foreach($changeDetails as $detail)
                                                                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                                                                <div class="text-xs font-semibold text-gray-700 mb-1">{{ $detail['field'] }}</div>
                                                                                <div class="text-sm text-gray-900 break-words">{{ $detail['value'] }}</div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    
                                                                @elseif($changeDetails[0]['type'] === 'delete')
                                                                    <div class="text-xs font-medium text-gray-700 mb-2">Rejected Deletion - Record Details:</div>
                                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                        @foreach($changeDetails as $detail)
                                                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                                                                <div class="text-xs font-semibold text-gray-700 mb-1">{{ $detail['field'] }}</div>
                                                                                <div class="text-sm text-gray-900 break-words">{{ $detail['value'] }}</div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="mt-4 text-base font-medium text-gray-900">No History Found</h3>
                        <p class="mt-2 text-sm text-gray-500">No history records match your current filters.</p>
                        @if($filterAction || $filterColumn || $filterUser || $filterDateFrom || $filterDateTo)
                            <button wire:click="resetFilters" 
                                    class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Clear All Filters
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($histories->hasPages())
                <div class="mt-6 border-t border-gray-200 pt-6">
                    {{ $histories->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function toggleHistory(elementId) {
            const content = document.getElementById(elementId);
            const button = content.previousElementSibling;
            const chevron = button.querySelector('.chevron-icon');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }
        
        function expandAll() {
            const contents = document.querySelectorAll('.history-content');
            const chevrons = document.querySelectorAll('.chevron-icon');
            
            contents.forEach(content => content.classList.remove('hidden'));
            chevrons.forEach(chevron => chevron.style.transform = 'rotate(180deg)');
        }
        
        function collapseAll() {
            const contents = document.querySelectorAll('.history-content');
            const chevrons = document.querySelectorAll('.chevron-icon');
            
            contents.forEach(content => content.classList.add('hidden'));
            chevrons.forEach(chevron => chevron.style.transform = 'rotate(0deg)');
        }
    </script>
</div>