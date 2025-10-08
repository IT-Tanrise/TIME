<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter and Bulk Actions --}}
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <label class="text-sm font-medium text-gray-700">Filter:</label>
            <select wire:model.live="filterType" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="all">All Types</option>
                @can('land-data.approval')
                    <option value="land">Land Only</option>
                @endcan
                @canany(['soil-data.approval', 'soil-data-costs.approval'])
                    <option value="soil">Soil Only</option>
                @endcanany
            </select>
        </div>

        @if(count($selectedApprovals) > 0)
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">{{ count($selectedApprovals) }} selected</span>
                <button 
                    wire:click="bulkApprove"
                    onclick="return confirm('Are you sure you want to approve {{ count($selectedApprovals) }} selected items?')"
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    Approve Selected
                </button>
                <button 
                    wire:click="showBulkRejectModal"
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Reject Selected
                </button>
            </div>
        @endif
    </div>

    {{-- Select All Checkbox --}}
    @if($approvals->count() > 0)
        <div class="mb-2 flex items-center">
            <input 
                type="checkbox" 
                wire:model.live="selectAll"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <label class="ml-2 text-sm text-gray-600">Select All</label>
        </div>
    @endif

    {{-- Approvals List --}}
    <div class="space-y-4">
        @forelse ($approvals as $approval)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 rounded-t-lg bg-gray-50">
                    <div class="flex items-center space-x-3">
                        {{-- Checkbox --}}
                        <input 
                            type="checkbox" 
                            wire:model.live="selectedApprovals"
                            value="{{ $approval->unique_id }}"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Approval Request #{{ $approval->id }}
                                </h3>
                                
                                {{-- Type Badge --}}
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                                    {{ $approval->approval_type === 'land' ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ strtoupper($approval->approval_type) }}
                                </span>
                                
                                {{-- Action Badge --}}
                                @if($approval->change_type === 'create')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">
                                        CREATE
                                    </span>
                                @elseif($approval->change_type === 'delete')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">
                                        DELETE
                                    </span>
                                @endif
                            </div>
                            
                            <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                @if($approval->approval_type === 'land')
                                    @if($approval->change_type != 'create')
                                    <span>{{ $approval->land->lokasi_lahan ?? 'N/A' }}</span>
                                    @endif
                                @else
                                    <span>{{ $approval->soil->nama_penjual ?? 'N/A' }} - {{ $approval->soil->letak_tanah ?? 'N/A' }}</span>
                                @endif
                                @if($approval->change_type != 'create')
                                <span>•</span>
                                @endif
                                <span>Requested by: {{ $approval->requestedBy->name }}</span>
                                <span>•</span>
                                <span>{{ $approval->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        
                        <button 
                            wire:click="toggleDetails('{{ $approval->unique_id }}')"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            {{ isset($showDetails[$approval->unique_id]) && $showDetails[$approval->unique_id] ? 'Hide' : 'Show' }} Details
                        </button>
                    </div>
                </div>

                {{-- Details --}}
                @if (isset($showDetails[$approval->unique_id]) && $showDetails[$approval->unique_id])
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Proposed Changes:</h4>
                        
                        @php $details = $this->getChangeDetails($approval);@endphp
                        
                        @if($approval->change_type === 'costs' && $approval->approval_type === 'soil')
                            @php $changes = $this->getSoilCostChangeDetails($approval); @endphp
                            @php $summary = $this->getCostChangeSummary($approval); @endphp
                            @php $totals = $this->getCostDifference($approval); @endphp
                            
                            <div class="mt-4 p-4 bg-gray-50 rounded">
                                <h4 class="font-medium">Cost Changes Summary:</h4>
                                <p class="text-sm text-gray-600 mt-2">
                                    {{ $summary['added'] }} added, {{ $summary['modified'] }} modified, {{ $summary['deleted'] }} deleted
                                </p>
                                <p class="text-sm text-gray-600">
                                    Total change: {{ $totals['difference'] }}
                                </p>
                                
                                @foreach($changes as $change)
                                    <div class="mt-3 p-2 border-l-4 
                                        @if($change['type'] === 'added') border-green-400 bg-green-50
                                        @elseif($change['type'] === 'modified') border-yellow-400 bg-yellow-50
                                        @else border-red-400 bg-red-50 @endif">
                                        
                                        <strong>{{ ucfirst($change['type']) }}:</strong> {{ $change['description'] }}
                                        
                                        @if($change['type'] === 'modified')
                                            @foreach($change['changes'] as $field => $values)
                                                <br><small>{{ ucfirst($field) }}: {{ $values['old'] }} → {{ $values['new'] }}</small>
                                            @endforeach
                                        @else
                                            <br><small>{{ $change['cost_type'] ?? '' }} - {{ $change['amount'] ?? '' }} ({{ $change['date_cost'] ?? '' }})</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach ($details as $detail)
                                    <div class="flex items-start space-x-4 text-sm">
                                        <span class="font-medium text-gray-700 w-32 flex-shrink-0">{{ $detail['field'] }}:</span>
                                        @if(isset($detail['old']) && isset($detail['new']))
                                            <div class="flex-1">
                                                <span class="text-red-600 line-through">{{ $detail['old'] }}</span>
                                                <span class="mx-2">→</span>
                                                <span class="text-green-600 font-medium">{{ $detail['new'] }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-600">{{ $detail['value'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Actions --}}
                <div class="px-4 py-3 bg-gray-50 rounded-b-lg">
                    <div class="flex justify-end space-x-2">
                        <button 
                            wire:click="showRejectModal('{{ $approval->unique_id }}')"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Reject
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending approvals</h3>
                <p class="mt-1 text-sm text-gray-500">All changes have been processed.</p>
            </div>
        @endforelse
    </div>

    {{-- Rejection Modal --}}
    @if ($showRejectionModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $bulkAction ? 'Reject Selected Approvals' : 'Reject Approval' }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Please provide a reason for rejecting {{ $bulkAction ? 'these approvals' : 'this approval' }}:
                    </p>
                    <textarea 
                        wire:model="rejectionReason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        rows="4"
                        placeholder="Enter rejection reason (minimum 5 characters)..."
                    ></textarea>

                    @error('rejectionReason') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                    
                    <div class="flex items-center justify-end space-x-2 mt-4">
                        <button 
                            wire:click="hideRejectModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button 
                            wire:click="rejectSelected"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Reject {{ $bulkAction ? 'Selected' : '' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>