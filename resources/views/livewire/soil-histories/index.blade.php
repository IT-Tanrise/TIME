{{-- resources/views/livewire/soil-histories/index.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <!-- Sticky Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <button wire:click="backToSoil" 
                            class="inline-flex items-center px-3 py-1.5 bg-gray-500 border border-transparent rounded-md text-xs text-white font-medium hover:bg-gray-600 transition-colors duration-150">
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back
                    </button>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Soil Record History</h2>
                        <p class="text-xs text-gray-600">
                            <span class="font-medium">{{ $soil->businessUnit->name ?? 'N/A' }}</span> • 
                            <span class="font-medium">{{ $soil->land->lokasi_lahan ?? 'N/A' }}</span> • 
                            <span class="text-blue-600">{{ $soil->nama_penjual }}</span>
                        </p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                    <button wire:click="resetFilters" 
                            class="inline-flex items-center px-2 py-1.5 bg-gray-200 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-300 transition-colors duration-150">
                        Reset Filters
                    </button>
                    <button onclick="expandAll()" 
                            class="inline-flex items-center px-2 py-1.5 bg-blue-200 rounded-md text-xs font-medium text-blue-700 hover:bg-blue-300 transition-colors duration-150">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7"/>
                        </svg>
                        Expand All
                    </button>
                    <button onclick="collapseAll()" 
                            class="inline-flex items-center px-2 py-1.5 bg-gray-200 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-300 transition-colors duration-150">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7"/>
                        </svg>
                        Collapse All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Filters -->
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="px-4 py-3">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <!-- Action Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Action</label>
                    <select wire:model.live="filterAction" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Actions</option>
                        @foreach($availableActions as $action)
                            <option value="{{ $action['value'] }}">{{ $action['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Column Filter -->
                <div class="{{ $filterAction === 'updated' ? '' : 'opacity-50' }}">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Updated Column
                        @if($filterAction !== 'updated')
                            <span class="text-xs text-gray-400">(Select "Updated" first)</span>
                        @endif
                    </label>
                    <select wire:model.live="filterColumn" 
                            {{ $filterAction !== 'updated' ? 'disabled' : '' }}
                            class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 {{ $filterAction !== 'updated' ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        <option value="">All Columns</option>
                        @if($filterAction === 'updated')
                            @foreach($availableColumns as $column)
                                <option value="{{ $column['value'] }}">{{ $column['label'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <!-- User Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">User</label>
                    <input type="text" wire:model.live="filterUser" placeholder="Search by user name..."
                           class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <!-- Date From -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" wire:model.live="filterDateFrom"
                           class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <!-- Date To -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" wire:model.live="filterDateTo"
                           class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>
            
            <!-- Active Filter Summary -->
            @if($filterAction || $filterColumn || $filterUser || $filterDateFrom || $filterDateTo)
                <div class="mt-3 p-2 bg-blue-50 rounded-md border border-blue-200">
                    <div class="flex flex-wrap items-center gap-1">
                        <span class="text-xs font-medium text-blue-800 mr-2">Active Filters:</span>
                        @if($filterAction)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Action: {{ collect($availableActions)->firstWhere('value', $filterAction)['label'] ?? $filterAction }}
                                <button wire:click="$set('filterAction', '')" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                            </span>
                        @endif
                        @if($filterColumn && $filterAction === 'updated')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Column: {{ collect($availableColumns)->firstWhere('value', $filterColumn)['label'] ?? $filterColumn }}
                                <button wire:click="$set('filterColumn', '')" class="ml-1 text-green-600 hover:text-green-800">×</button>
                            </span>
                        @endif
                        @if($filterUser)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                User: {{ $filterUser }}
                                <button wire:click="$set('filterUser', '')" class="ml-1 text-purple-600 hover:text-purple-800">×</button>
                            </span>
                        @endif
                        @if($filterDateFrom)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                From: {{ $filterDateFrom }}
                                <button wire:click="$set('filterDateFrom', '')" class="ml-1 text-orange-600 hover:text-orange-800">×</button>
                            </span>
                        @endif
                        @if($filterDateTo)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                To: {{ $filterDateTo }}
                                <button wire:click="$set('filterDateTo', '')" class="ml-1 text-orange-600 hover:text-orange-800">×</button>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="overflow-y-auto" style="height: calc(100vh - 180px);">
        <div class="px-4 py-3">
            <!-- History Timeline -->
            <div class="space-y-3">
                @forelse($histories as $history)
                    @php
                        $classes = $this->getActionClasses($history);
                        $approvalInfo = $this->getApprovalInfo($history);
                    @endphp
                    
                    <div class="relative">
                        <!-- Timeline vertical line -->
                        @if(!$loop->last)
                            <div class="absolute left-4 top-[48px] w-0.5 bg-gray-200 z-0" style="height: calc(100% + 12px);"></div>
                        @endif
                        
                        <div class="flex items-start">
                            <!-- Timeline Icon -->
                            <div class="relative flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center z-10 bg-white border-2 mt-2 {{ $classes['border'] }} {{ $classes['bg'] }} {{ $classes['text'] }}">
                                @if($history->isApprovedChange())
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($history->isRejectedChange())
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($history->action === 'created')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif(in_array($history->action, ['updated', 'approved_update']))
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                @elseif(in_array($history->action, ['deleted', 'approved_deletion']))
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 012 0v4a1 1 0 11-2 0V7zM12 7a1 1 0 012 0v4a1 1 0 11-2 0V7z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif(str_contains($history->action, 'additional_cost'))
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            
                            <!-- Content Card -->
                            <div class="ml-3 flex-1">
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <!-- Compact Header -->
                                    <button type="button" 
                                            onclick="toggleHistory('history-{{ $history->id }}')"
                                            class="w-full p-3 text-left hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $history->action_display }}</h3>
                                                
                                                <!-- Status Badge -->
                                                @if($history->isApprovedChange() || $history->isRejectedChange())
                                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full flex-shrink-0 {{ $classes['badge_bg'] }} {{ $classes['badge_text'] }}">
                                                    @if($history->isApprovedChange())
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Approved
                                                    @elseif($history->isRejectedChange())
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Rejected
                                                    @endif
                                                </span>
                                                @endif
                                                
                                                <div class="text-xs text-gray-500 flex-shrink-0">
                                                    {{ $history->formatted_created_at }}
                                                </div>
                                                <div class="text-xs text-gray-400 truncate">
                                                    by {{ $history->user_display }}
                                                </div>
                                            </div>
                                            <!-- Chevron Icon -->
                                            <svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200 chevron-icon flex-shrink-0"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </button>
                                    
                                    <!-- Collapsible Content -->
                                    <div id="history-{{ $history->id }}" class="history-content hidden border-t border-gray-100">
                                        <div class="p-3 bg-gray-50">
                                            <!-- User and IP info -->
                                            <div class="flex items-center justify-between text-xs text-gray-600 mb-3">
                                                <div class="flex items-center space-x-4">
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                        </svg>
                                                        {{ $history->user ? $history->user->name : 'System' }}
                                                    </span>
                                                    
                                                    {{-- Show approval/rejection information --}}
                                                    @if($approvalInfo)
                                                        <span class="flex items-center {{ $approvalInfo['type'] === 'approved' ? 'text-green-600' : 'text-red-600' }} font-medium">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                @if($approvalInfo['type'] === 'approved')
                                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                @else
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                @endif
                                                            </svg>
                                                            {{ $approvalInfo['type'] === 'approved' ? 'Approved' : 'Rejected' }} by: {{ $approvalInfo['user_name'] }}
                                                        </span>
                                                    @endif
                                                    
                                                    @if($history->ip_address)
                                                        <span class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            {{ $history->ip_address }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Show rejection reason if available --}}
                                            @if($approvalInfo && $approvalInfo['type'] === 'rejected' && isset($approvalInfo['reason']))
                                                <div class="mb-3 p-2 bg-red-50 border border-red-200 rounded">
                                                    <div class="text-xs font-medium text-red-800 mb-1">Rejection Reason:</div>
                                                    <div class="text-xs text-gray-700">{{ $approvalInfo['reason'] }}</div>
                                                </div>
                                            @endif

                                            <!-- Changes details -->
                                            @php $changeDetails = $this->getChangeDetails($history) @endphp
                                            
                                            @if($changeDetails)
                                                @if(in_array($history->action, ['updated', 'approved_update', 'rejected_update']))
                                                    {{-- Regular Update / Approved Update / Rejected Update --}}
                                                    <div class="bg-white border {{ $history->isApprovedChange() ? 'border-green-200' : ($history->isRejectedChange() ? 'border-red-200' : 'border-blue-200') }} rounded-lg p-3">
                                                        <div class="text-sm font-medium {{ $history->isApprovedChange() ? 'text-green-800' : ($history->isRejectedChange() ? 'text-red-800' : 'text-blue-800') }} mb-2">
                                                            {{ $history->isApprovedChange() ? 'Approved Changes' : ($history->isRejectedChange() ? 'Rejected Changes' : 'Changes Made') }}
                                                        </div>
                                                        <div class="text-sm {{ $history->isApprovedChange() ? 'text-green-700' : ($history->isRejectedChange() ? 'text-red-700' : 'text-blue-700') }} mb-3">
                                                            {{ $history->changes_summary }}
                                                        </div>
                                                        
                                                        <div class="space-y-2">
                                                            @foreach($changeDetails as $change)
                                                                <div class="border border-gray-200 rounded p-2">
                                                                    <div class="font-medium text-gray-900 text-xs mb-2">{{ $change['field'] }}</div>
                                                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                                                        <div class="bg-red-50 p-2 rounded border border-red-200">
                                                                            <div class="text-red-600 font-medium mb-1">{{ $history->isRejectedChange() ? 'Current Value:' : 'Before:' }}</div>
                                                                            <div class="text-gray-700 break-words">{{ $change['old'] ?: 'Empty' }}</div>
                                                                        </div>
                                                                        <div class="bg-green-50 p-2 rounded border border-green-200">
                                                                            <div class="text-green-600 font-medium mb-1">{{ $history->isRejectedChange() ? 'Rejected Change:' : 'After:' }}</div>
                                                                            <div class="text-gray-700 break-words">{{ $change['new'] ?: 'Empty' }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    
                                                @elseif(str_contains($history->action, 'additional_cost'))
                                                    {{-- Additional Cost Actions --}}
                                                    <div class="bg-white border {{ $history->isApprovedChange() ? 'border-green-200' : ($history->isRejectedChange() ? 'border-red-200' : 'border-orange-200') }} rounded-lg p-3">
                                                        <div class="text-sm font-medium {{ $history->isApprovedChange() ? 'text-green-800' : ($history->isRejectedChange() ? 'text-red-800' : 'text-orange-800') }} mb-2">
                                                            {{ $history->action_display }}
                                                        </div>
                                                        
                                                        <div class="space-y-2">
                                                            @foreach($changeDetails as $change)
                                                                <div class="border border-gray-200 rounded p-2">
                                                                    <div class="font-medium text-gray-900 text-xs mb-2">{{ $change['field'] }}</div>
                                                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                                                        @if(isset($change['type']) && $change['type'] === 'added')
                                                                            <div class="col-span-2 bg-green-50 p-2 rounded border border-green-200">
                                                                                <div class="text-green-600 font-medium mb-1">New Value:</div>
                                                                                <div class="text-gray-700 break-words">{{ $change['new'] ?: 'Empty' }}</div>
                                                                            </div>
                                                                        @elseif(isset($change['type']) && $change['type'] === 'deleted')
                                                                            <div class="col-span-2 bg-red-50 p-2 rounded border border-red-200">
                                                                                <div class="text-red-600 font-medium mb-1">Deleted Value:</div>
                                                                                <div class="text-gray-700 break-words">{{ $change['old'] ?: 'Empty' }}</div>
                                                                            </div>
                                                                        @else
                                                                            <div class="bg-red-50 p-2 rounded border border-red-200">
                                                                                <div class="text-red-600 font-medium mb-1">Before:</div>
                                                                                <div class="text-gray-700 break-words">{{ $change['old'] ?: 'Empty' }}</div>
                                                                            </div>
                                                                            <div class="bg-green-50 p-2 rounded border border-green-200">
                                                                                <div class="text-green-600 font-medium mb-1">After:</div>
                                                                                <div class="text-gray-700 break-words">{{ $change['new'] ?: 'Empty' }}</div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @elseif(str_contains($history->action, 'interest_cost'))
                                                    {{-- Interest Cost Actions --}}
                                                    <div class="bg-white border {{ $history->isApprovedChange() ? 'border-green-200' : ($history->isRejectedChange() ? 'border-red-200' : 'border-purple-200') }} rounded-lg p-3">
                                                        <div class="text-sm font-medium {{ $history->isApprovedChange() ? 'text-green-800' : ($history->isRejectedChange() ? 'text-red-800' : 'text-purple-800') }} mb-2">
                                                            {{ $history->action_display }}
                                                        </div>
                                                        
                                                        <div class="space-y-2">
                                                            @foreach($changeDetails as $change)
                                                                <div class="border border-gray-200 rounded p-2">
                                                                    <div class="font-medium text-gray-900 text-xs mb-2">{{ $change['field'] }}</div>
                                                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                                                        @if(isset($change['type']) && $change['type'] === 'added')
                                                                            <div class="col-span-2 bg-green-50 p-2 rounded border border-green-200">
                                                                                <div class="text-green-600 font-medium mb-1">New Value:</div>
                                                                                <div class="text-gray-700 break-words">{{ $change['new'] ?: 'Empty' }}</div>
                                                                            </div>
                                                                        @elseif(isset($change['type']) && $change['type'] === 'deleted')
                                                                            <div class="col-span-2 bg-red-50 p-2 rounded border border-red-200">
                                                                                <div class="text-red-600 font-medium mb-1">Deleted Value:</div>
                                                                                <div class="text-gray-700 break-words">{{ $change['old'] ?: 'Empty' }}</div>
                                                                            </div>
                                                                        @else
                                                                            <div class="bg-red-50 p-2 rounded border border-red-200">
                                                                                <div class="text-red-600 font-medium mb-1">Before:</div>
                                                                                <div class="text-gray-700 break-words">{{ $change['old'] ?: 'Empty' }}</div>
                                                                            </div>
                                                                            <div class="bg-green-50 p-2 rounded border border-green-200">
                                                                                <div class="text-green-600 font-medium mb-1">After:</div>
                                                                                <div class="text-gray-700 break-words">{{ $change['new'] ?: 'Empty' }}</div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @elseif(in_array($history->action, ['approved_interest_update', 'rejected_interest_update']))
                                                    {{-- Approved/Rejected Interest Update --}}
                                                    <div class="bg-white border {{ $history->isApprovedChange() ? 'border-green-200' : 'border-red-200' }} rounded-lg p-3">
                                                        <div class="text-sm font-medium {{ $history->isApprovedChange() ? 'text-green-800' : 'text-red-800' }} mb-2">
                                                            {{ $history->isApprovedChange() ? 'Approved Interest Cost Changes' : 'Rejected Interest Cost Changes' }}
                                                        </div>
                                                        <div class="text-sm {{ $history->isApprovedChange() ? 'text-green-700' : 'text-red-700' }} mb-3">
                                                            {{ $history->changes_summary }}
                                                        </div>
                                                        
                                                        <div class="space-y-3">
                                                            @foreach($changeDetails as $change)
                                                                @if($change['type'] === 'rejected_add' || $change['type'] === 'added')
                                                                    <div class="border {{ $history->isRejectedChange() ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50' }} rounded p-2">
                                                                        <div class="flex items-center mb-2">
                                                                            <svg class="w-4 h-4 {{ $history->isRejectedChange() ? 'text-red-600' : 'text-green-600' }} mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                                @if($history->isRejectedChange())
                                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                                @else
                                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                                @endif
                                                                            </svg>
                                                                            <span class="text-xs font-medium {{ $history->isRejectedChange() ? 'text-red-800' : 'text-green-800' }}">
                                                                                {{ $history->isRejectedChange() ? 'Rejected Addition' : 'Added Interest Period' }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="text-xs text-gray-700 space-y-1">
                                                                            <div><span class="font-medium">Period:</span> {{ $change['start_date'] ?? 'N/A' }} to {{ $change['end_date'] ?? 'N/A' }}</div>
                                                                            <div><span class="font-medium">Days:</span> {{ $change['days'] ?? 'N/A' }}</div>
                                                                            <div><span class="font-medium">Harga Perolehan:</span> {{ $change['harga_perolehan'] ?? 'N/A' }}</div>
                                                                            <div><span class="font-medium">Interest Rate:</span> {{ $change['bunga'] ?? 'N/A' }}%</div>
                                                                            <div><span class="font-medium">Remarks:</span> {{ $change['remarks'] ?? 'N/A' }}</div>
                                                                        </div>
                                                                    </div>
                                                                @elseif($change['type'] === 'rejected_delete' || $change['type'] === 'deleted')
                                                                    <div class="border {{ $history->isRejectedChange() ? 'border-red-200 bg-red-50' : 'border-red-200 bg-red-50' }} rounded p-2">
                                                                        <div class="flex items-center mb-2">
                                                                            <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                            <span class="text-xs font-medium text-red-800">
                                                                                {{ $history->isRejectedChange() ? 'Rejected Deletion' : 'Deleted Interest Period' }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="text-xs text-gray-700 space-y-1">
                                                                            <div><span class="font-medium">Period:</span> {{ $change['start_date'] ?? 'N/A' }} to {{ $change['end_date'] ?? 'N/A' }}</div>
                                                                            <div><span class="font-medium">Days:</span> {{ $change['days'] ?? 'N/A' }}</div>
                                                                            <div><span class="font-medium">Harga Perolehan:</span> {{ $change['harga_perolehan'] ?? 'N/A' }}</div>
                                                                            <div><span class="font-medium">Interest Rate:</span> {{ $change['bunga'] ?? 'N/A' }}%</div>
                                                                            <div><span class="font-medium">Remarks:</span> {{ $change['remarks'] ?? 'N/A' }}</div>
                                                                        </div>
                                                                    </div>
                                                                @elseif($change['type'] === 'rejected_modify' || $change['type'] === 'updated')
                                                                    <div class="border border-gray-200 rounded p-2 bg-gray-50">
                                                                        <div class="flex items-center mb-2">
                                                                            <svg class="w-4 h-4 {{ $history->isRejectedChange() ? 'text-red-600' : 'text-blue-600' }} mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                                            </svg>
                                                                            <span class="text-xs font-medium {{ $history->isRejectedChange() ? 'text-red-800' : 'text-blue-800' }}">
                                                                                {{ $history->isRejectedChange() ? 'Rejected Modification' : 'Modified Interest Period' }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="space-y-2">
                                                                            @foreach($change['changes'] as $fieldChange)
                                                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                                                    <div class="bg-white p-2 rounded border border-gray-200">
                                                                                        <div class="text-gray-600 font-medium mb-1">{{ $fieldChange['field'] }} {{ $history->isRejectedChange() ? '(Current)' : '(Before)' }}:</div>
                                                                                        <div class="text-gray-700">{{ $fieldChange['old'] }}</div>
                                                                                    </div>
                                                                                    <div class="bg-white p-2 rounded border border-gray-200">
                                                                                        <div class="{{ $history->isRejectedChange() ? 'text-red-600' : 'text-green-600' }} font-medium mb-1">{{ $fieldChange['field'] }} {{ $history->isRejectedChange() ? '(Rejected)' : '(After)' }}:</div>
                                                                                        <div class="text-gray-700">{{ $fieldChange['new'] }}</div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>                                                    
                                                @elseif($history->action === 'rejected_cost_update')
                                                    {{-- Rejected Cost Update --}}
                                                    <div class="bg-white border border-red-200 rounded-lg p-3">
                                                        <div class="text-sm font-medium text-red-800 mb-2">
                                                            Rejected Additional Cost Changes
                                                        </div>
                                                        <div class="text-sm text-red-700 mb-3">{{ $history->changes_summary }}</div>
                                                        
                                                        <div class="space-y-3">
                                                            @foreach($changeDetails as $change)
                                                                @if($change['type'] === 'rejected_add')
                                                                    <div class="border border-red-200 rounded p-2 bg-red-50">
                                                                        <div class="flex items-center mb-2">
                                                                            <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                            <span class="text-xs font-medium text-red-800">Rejected Addition</span>
                                                                        </div>
                                                                        <div class="text-xs text-gray-700 space-y-1">
                                                                            <div><span class="font-medium">Description:</span> {{ $change['description'] }}</div>
                                                                            <div><span class="font-medium">Amount:</span> {{ $change['amount'] }}</div>
                                                                            <div><span class="font-medium">Type:</span> {{ $change['cost_type'] }}</div>
                                                                            <div><span class="font-medium">Date:</span> {{ $change['date_cost'] }}</div>
                                                                        </div>
                                                                    </div>
                                                                @elseif($change['type'] === 'rejected_delete')
                                                                    <div class="border border-red-200 rounded p-2 bg-red-50">
                                                                        <div class="flex items-center mb-2">
                                                                            <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                            <span class="text-xs font-medium text-red-800">Rejected Deletion</span>
                                                                        </div>
                                                                        <div class="text-xs text-gray-700 space-y-1">
                                                                            <div><span class="font-medium">Description:</span> {{ $change['description'] }}</div>
                                                                            <div><span class="font-medium">Amount:</span> {{ $change['amount'] }}</div>
                                                                            <div><span class="font-medium">Type:</span> {{ $change['cost_type'] }}</div>
                                                                            <div><span class="font-medium">Date:</span> {{ $change['date_cost'] }}</div>
                                                                        </div>
                                                                    </div>
                                                                @elseif($change['type'] === 'rejected_modify')
                                                                    <div class="border border-red-200 rounded p-2 bg-red-50">
                                                                        <div class="flex items-center mb-2">
                                                                            <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                            </svg>
                                                                            <span class="text-xs font-medium text-red-800">Rejected Modification: {{ $change['description'] }}</span>
                                                                        </div>
                                                                        <div class="space-y-2">
                                                                            @foreach($change['changes'] as $fieldChange)
                                                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                                                    <div class="bg-white p-2 rounded border border-gray-200">
                                                                                        <div class="text-gray-600 font-medium mb-1">{{ $fieldChange['field'] }} (Current):</div>
                                                                                        <div class="text-gray-700">{{ $fieldChange['old'] }}</div>
                                                                                    </div>
                                                                                    <div class="bg-white p-2 rounded border border-gray-200">
                                                                                        <div class="text-red-600 font-medium mb-1">{{ $fieldChange['field'] }} (Rejected):</div>
                                                                                        <div class="text-gray-700">{{ $fieldChange['new'] }}</div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    
                                                @elseif(in_array($history->action, ['created', 'approved_creation', 'rejected_creation']))
                                                    {{-- Creation Actions --}}
                                                    <div class="bg-white border {{ $history->isApprovedChange() ? 'border-green-200' : ($history->isRejectedChange() ? 'border-red-200' : 'border-green-200') }} rounded-lg p-3">
                                                        <div class="text-sm font-medium {{ $history->isApprovedChange() ? 'text-green-800' : ($history->isRejectedChange() ? 'text-red-800' : 'text-green-800') }} mb-2">
                                                            {{ $history->isRejectedChange() ? 'Rejected Record Creation' : 'Record Created' }}
                                                        </div>
                                                        <div class="space-y-2">
                                                            @foreach($changeDetails as $detail)
                                                                <div class="flex justify-between text-xs py-1 border-b border-gray-100 last:border-0">
                                                                    <span class="font-medium text-gray-700">{{ $detail['field'] }}:</span>
                                                                    <span class="text-gray-900">{{ $detail['value'] }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    
                                                @elseif(in_array($history->action, ['deleted', 'approved_deletion', 'rejected_deletion']))
                                                    {{-- Deletion Actions --}}
                                                    <div class="bg-white border {{ $history->isApprovedChange() ? 'border-green-200' : ($history->isRejectedChange() ? 'border-red-200' : 'border-red-200') }} rounded-lg p-3">
                                                        <div class="text-sm font-medium {{ $history->isApprovedChange() ? 'text-green-800' : ($history->isRejectedChange() ? 'text-red-800' : 'text-red-800') }} mb-2">
                                                            {{ $history->isRejectedChange() ? 'Rejected Record Deletion' : 'Record Deleted' }}
                                                        </div>
                                                        <div class="space-y-2">
                                                            @foreach($changeDetails as $detail)
                                                                @if(isset($detail['type']) && $detail['type'] === 'reason')
                                                                    <div class="p-2 bg-yellow-50 border border-yellow-200 rounded">
                                                                        <div class="text-xs font-medium text-yellow-800 mb-1">{{ $detail['field'] }}:</div>
                                                                        <div class="text-xs text-gray-700">{{ $detail['value'] }}</div>
                                                                    </div>
                                                                @else
                                                                    <div class="flex justify-between text-xs py-1 border-b border-gray-100 last:border-0">
                                                                        <span class="font-medium text-gray-700">{{ $detail['field'] }}:</span>
                                                                        <span class="text-gray-900">{{ $detail['value'] }}</span>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                {{-- No details available --}}
                                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                                    <div class="text-sm text-gray-600">No detailed changes available for this action.</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No history found</h3>
                        <p class="mt-1 text-sm text-gray-500">No history records match your current filters.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($histories->hasPages())
                <div class="mt-4 border-t border-gray-200 pt-4">
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