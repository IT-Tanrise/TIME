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

    <div class="space-y-4">
        @forelse ($pendingApprovals as $approval)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <!-- Header -->
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">
                                Approval Request #{{ $approval->id }}
                            </h3>
                            <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                <span>{{ $approval->soil->land->lokasi_lahan ?? 'N/A' }}</span>
                                <span>•</span>
                                <span>{{ $approval->soil->businessUnit->name ?? 'N/A' }}</span>
                                <span>•</span>
                                <span>Requested by: {{ $approval->requestedBy->name }}</span>
                                <span>•</span>
                                <span>{{ $approval->formatted_created_at }}</span>
                            </div>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $approval->change_type === 'details' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $approval->change_type === 'details' ? 'Soil Details' : 'Additional Costs' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button 
                                wire:click="toggleDetails({{ $approval->id }})"
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ isset($showDetails[$approval->id]) && $showDetails[$approval->id] ? 'Hide Details' : 'Show Details' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Details (Collapsible) -->
                @if (isset($showDetails[$approval->id]) && $showDetails[$approval->id])
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Proposed Changes:</h4>
                        
                        @if ($approval->change_type === 'details')
                            <div class="space-y-2">
                                @foreach ($this->getChangeDetails($approval) as $change)
                                    <div class="flex items-start space-x-4 text-sm">
                                        <span class="font-medium text-gray-700 w-32 flex-shrink-0">{{ $change['field'] }}:</span>
                                        <div class="flex-1">
                                            <span class="text-red-600 line-through">{{ $change['old'] }}</span>
                                            <span class="mx-2">→</span>
                                            <span class="text-green-600 font-medium">{{ $change['new'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($approval->change_type === 'costs')
                            @php $changes = $this->getCostChangeDetails($approval); @endphp
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
                        @endif
                    </div>
                @endif

                <!-- Actions -->
                <div class="px-4 py-3 bg-gray-50 rounded-b-lg">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Soil Record:</span> {{ $approval->soil->nama_penjual }} - {{ $approval->soil->letak_tanah }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <button 
                                wire:click="approve({{ $approval->id }})"
                                onclick="return confirm('Are you sure you want to approve these changes?')"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Approve
                            </button>
                            <button 
                                wire:click="showRejectModal({{ $approval->id }})"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Reject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending approvals</h3>
                <p class="mt-1 text-sm text-gray-500">All changes have been processed.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($pendingApprovals->hasPages())
        <div class="mt-4">
            {{ $pendingApprovals->links() }}
        </div>
    @endif

    <!-- Rejection Modal -->
    @if ($showRejectionModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Changes</h3>
                    <p class="text-sm text-gray-600 mb-4">Please provide a reason for rejecting these changes:</p>
                    
                    <textarea 
                        wire:model="rejectionReason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        rows="4"
                        placeholder="Enter rejection reason..."
                    ></textarea>
                    
                    @error('rejectionReason') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                    
                    <div class="flex items-center justify-end space-x-2 mt-4">
                        <button 
                            wire:click="hideRejectModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button 
                            wire:click="rejectWithReason"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Reject Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>