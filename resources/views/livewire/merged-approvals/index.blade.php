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

    {{-- Approvals Table --}}
    @if($approvals->count() > 0)
        <div class="overflow-x-auto bg-white shadow-sm rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="w-12 px-4 py-3">
                            <input 
                                type="checkbox" 
                                wire:model.live="selectAll"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Details
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Requested By
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($approvals as $approval)
                        <tr class="hover:bg-gray-50">
                            {{-- Checkbox --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="selectedApprovals"
                                    value="{{ $approval->unique_id }}"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </td>

                            {{-- ID --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $approval->id }}
                            </td>

                            {{-- Type Badge --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                                    {{ $approval->approval_type === 'land' ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ strtoupper($approval->approval_type) }}
                                </span>
                            </td>

                            {{-- Action Badge --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($approval->change_type === 'create')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">
                                        CREATE
                                    </span>
                                @elseif($approval->change_type === 'delete')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">
                                        DELETE
                                    </span>
                                @elseif($approval->change_type === 'costs')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                        COSTS
                                    </span>
                                @elseif($approval->change_type === 'interest')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-indigo-100 text-indigo-800">
                                        INTEREST
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        UPDATE
                                    </span>
                                @endif
                            </td>

                            {{-- Details --}}
                            <td class="px-4 py-3 text-sm text-gray-900 max-w-xs">
                                @if($approval->approval_type === 'land')
                                    @if($approval->change_type != 'create')
                                        <div class="truncate" title="{{ $approval->land->lokasi_lahan ?? 'N/A' }}">
                                            {{ $approval->land->lokasi_lahan ?? 'N/A' }}
                                        </div>
                                    @else
                                        <div class="truncate" title="{{ $approval->new_data['lokasi_lahan'] ?? 'N/A' }}">
                                            {{ $approval->new_data['lokasi_lahan'] ?? 'New land entry' }}
                                        </div>
                                    @endif
                                @else
                                    @if($approval->change_type === 'create')
                                        {{-- For create requests, get data from new_data --}}
                                        <div class="truncate" title="{{ $approval->new_data['nama_penjual'] ?? 'N/A' }} - {{ $approval->new_data['letak_tanah'] ?? 'N/A' }}">
                                            {{ $approval->new_data['nama_penjual'] ?? 'N/A' }} - {{ $approval->new_data['letak_tanah'] ?? 'N/A' }}
                                        </div>
                                    @else
                                        {{-- For other requests, get data from soil relationship --}}
                                        <div class="truncate" title="{{ $approval->soil->nama_penjual ?? 'N/A' }} - {{ $approval->soil->letak_tanah ?? 'N/A' }}">
                                            {{ $approval->soil->nama_penjual ?? 'N/A' }} - {{ $approval->soil->letak_tanah ?? 'N/A' }}
                                        </div>
                                    @endif
                                @endif
                            </td>

                            {{-- Requested By --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $approval->requestedBy->name }}
                            </td>

                            {{-- Date --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $approval->created_at->diffForHumans() }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <button 
                                        wire:click="toggleDetails('{{ $approval->unique_id }}')"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="showRejectModal('{{ $approval->unique_id }}')"
                                        class="text-red-600 hover:text-red-900"
                                        title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                                        <h4 class="font-medium text-gray-900 mb-3">Proposed Changes:</h4>
                                        
                                        @php $details = $this->getChangeDetails($approval);@endphp
                                        
                                        @if($approval->change_type === 'costs' && $approval->approval_type === 'soil')
                                            @php $changes = $this->getSoilCostChangeDetails($approval); @endphp
                                            @php $summary = $this->getCostChangeSummary($approval); @endphp
                                            @php $totals = $this->getCostDifference($approval); @endphp
                                            
                                            <div class="mt-2 p-4 bg-white rounded border border-gray-200">
                                                <h5 class="font-medium text-gray-900">Cost Changes Summary:</h5>
                                                <p class="text-sm text-gray-600 mt-2">
                                                    {{ $summary['added'] }} added, {{ $summary['modified'] }} modified, {{ $summary['deleted'] }} deleted
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Total change: {{ $totals['difference'] }}
                                                </p>
                                                
                                                <div class="mt-3 space-y-2">
                                                    @foreach($changes as $change)
                                                        <div class="p-2 border-l-4 
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
                                            </div>
                                        @elseif($approval->change_type === 'interest' && $approval->approval_type === 'soil')
                                            @php $changes = $this->getInterestChangeDetails($approval); @endphp
                                            @php $summary = $this->getInterestChangeSummary($approval); @endphp
                                            
                                            <div class="mt-2 p-4 bg-white rounded border border-gray-200">
                                                <h5 class="font-medium text-gray-900">Interest Cost Changes Summary:</h5>
                                                <p class="text-sm text-gray-600 mt-2">
                                                    {{ $summary['added'] }} added, {{ $summary['modified'] }} modified, {{ $summary['deleted'] }} deleted
                                                </p>
                                                
                                                <div class="mt-3 space-y-2">
                                                    @foreach($changes as $change)
                                                        <div class="p-2 border-l-4 
                                                            @if($change['type'] === 'added') border-green-400 bg-green-50
                                                            @elseif($change['type'] === 'modified') border-yellow-400 bg-yellow-50
                                                            @else border-red-400 bg-red-50 @endif">
                                                            
                                                            <strong>{{ ucfirst($change['type']) }}:</strong>
                                                            
                                                            @if($change['type'] === 'added')
                                                                Period: {{ $change['start_date'] }} to {{ $change['end_date'] }} ({{ $change['days'] }} days)
                                                                <br><small>Harga Perolehan: {{ $change['harga_perolehan'] }} | Interest: {{ $change['bunga'] }}%</small>
                                                                @if($change['remarks'])
                                                                    <br><small>Remarks: {{ $change['remarks'] }}</small>
                                                                @endif
                                                            @elseif($change['type'] === 'deleted')
                                                                Period: {{ $change['start_date'] }} to {{ $change['end_date'] }} ({{ $change['days'] }} days)
                                                                <br><small>Harga Perolehan: {{ $change['harga_perolehan'] }} | Interest: {{ $change['bunga'] }}%</small>
                                                                @if($change['remarks'])
                                                                    <br><small>Remarks: {{ $change['remarks'] }}</small>
                                                                @endif
                                                            @elseif($change['type'] === 'modified')
                                                                Period: {{ $change['period'] }}
                                                                @foreach($change['changes'] as $field => $values)
                                                                    <br><small>{{ ucfirst($field) }}: {{ $values['old'] }} → {{ $values['new'] }}</small>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-white rounded border border-gray-200 overflow-hidden">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-100">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                                            @if(isset($details[0]['old']) && isset($details[0]['new']))
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Old Value</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">New Value</th>
                                                            @else
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200">
                                                        @foreach ($details as $detail)
                                                            <tr>
                                                                <td class="px-4 py-2 text-sm font-medium text-gray-700">{{ $detail['field'] }}</td>
                                                                @if(isset($detail['old']) && isset($detail['new']))
                                                                    <td class="px-4 py-2 text-sm text-red-600 line-through">{{ $detail['old'] }}</td>
                                                                    <td class="px-4 py-2 text-sm text-green-600 font-medium">{{ $detail['new'] }}</td>
                                                                @else
                                                                    <td colspan="2" class="px-4 py-2 text-sm text-gray-600">{{ $detail['value'] }}</td>
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
            </table>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending approvals</h3>
            <p class="mt-1 text-sm text-gray-500">All changes have been processed.</p>
        </div>
    @endif

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