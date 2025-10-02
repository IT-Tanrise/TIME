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

    @if (session()->has('warning'))
        <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
            {{ session('warning') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($pendingApprovals as $approval)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm
                {{ $approval->change_type === 'delete' ? 'border-red-300' : '' }}">
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 rounded-t-lg
                    {{ $approval->change_type === 'delete' ? 'bg-red-50' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Approval Request #{{ $approval->id }}
                                </h3>
                                @if($approval->change_type === 'create')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V7z" clip-rule="evenodd"/>
                                        </svg>
                                        CREATE REQUEST
                                    </span>
                                @elseif($approval->change_type === 'delete')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        DELETE REQUEST
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                        UPDATE REQUEST
                                    </span>
                                @endif
                            </div>
                            <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                @if($approval->land)
                                    <span>{{ $approval->land->lokasi_lahan ?? 'N/A' }}</span>
                                    <span>•</span>
                                    <span>{{ $approval->land->kota_kabupaten ?? 'N/A' }}</span>
                                    <span>•</span>
                                @elseif($approval->change_type === 'create')
                                    @php
                                        $landLocation = $approval->new_data['lokasi_lahan'] ?? 'N/A';
                                        $landCity = $approval->new_data['kota_kabupaten'] ?? 'N/A';
                                    @endphp
                                    <span>{{ $landLocation }}</span>
                                    <span>•</span>
                                    <span>{{ $landCity }}</span>
                                    <span>•</span>
                                @else
                                    <span class="text-red-500">Record may be deleted</span>
                                    <span>•</span>
                                @endif
                                <span>Requested by: {{ $approval->requestedBy->name }}</span>
                                <span>•</span>
                                <span>{{ $approval->formatted_created_at }}</span>
                            </div>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $approval->change_type === 'details' ? 'bg-blue-100 text-blue-800' : 
                                    ($approval->change_type === 'create' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $approval->change_type === 'details' ? 'Land Details' : 
                                    ($approval->change_type === 'create' ? 'Create Record' : 'Delete Record') }}
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
                        <h4 class="text-sm font-medium text-gray-900 mb-3">
                            {{ $approval->change_type === 'delete' ? 'Deletion Request Details:' : 'Proposed Changes:' }}
                        </h4>
                        
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
                            
                        @elseif($approval->change_type === 'delete')
                            @php $deleteDetails = $this->getChangeDetails($approval); @endphp
                            
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-red-800 mb-2">
                                            This request will permanently delete the land record and all associated data.
                                        </h4>
                                        
                                        <div class="space-y-2 text-sm">
                                            @foreach($deleteDetails as $detail)
                                                <div class="flex items-start">
                                                    <span class="font-medium text-red-700 w-32 flex-shrink-0">{{ $detail['field'] }}:</span>
                                                    <span class="text-red-600">{{ $detail['value'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        @if($approval->getDeletionReason())
                                            <div class="mt-3 p-3 bg-red-100 rounded">
                                                <span class="font-medium text-red-700">Deletion Reason:</span>
                                                <p class="text-red-600 mt-1">{{ $approval->getDeletionReason() }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif($approval->change_type === 'create')
                            @php $createDetails = $this->getChangeDetails($approval); @endphp
                            
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V7z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-green-800 mb-2">
                                            This request will create a new land record with the following data:
                                        </h4>
                                        
                                        <div class="space-y-2 text-sm">
                                            @foreach($createDetails as $detail)
                                                <div class="flex items-start">
                                                    <span class="font-medium text-green-700 w-32 flex-shrink-0">{{ $detail['field'] }}:</span>
                                                    <span class="text-green-600">{{ $detail['value'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Actions -->
                <div class="px-4 py-3 rounded-b-lg
                    {{ $approval->change_type === 'delete' ? 'bg-red-50' : 'bg-gray-50' }}">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">
                                {{ $approval->change_type === 'delete' ? 'Record to Delete:' : 
                                ($approval->change_type === 'create' ? 'New Record:' : 'Land Record:') }}
                            </span>
                            @if($approval->land)
                                {{ $approval->land->lokasi_lahan }} - {{ $approval->land->kota_kabupaten }}
                            @elseif($approval->change_type === 'create' && $approval->new_data)
                                {{ $approval->new_data['lokasi_lahan'] ?? 'Unknown Location' }} - {{ $approval->new_data['kota_kabupaten'] ?? 'Unknown City' }}
                            @else
                                <span class="text-red-500 italic">Record no longer exists</span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            @can('land-data.approval')
                                @if($approval->change_type === 'delete')
                                    <button 
                                        wire:click="approve({{ $approval->id }})"
                                        onclick="return confirm('⚠️ WARNING: This will permanently delete the land record and all associated data. This action cannot be undone.\n\nAre you absolutely sure you want to approve this deletion?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Approve Deletion
                                    </button>
                                @elseif($approval->change_type === 'create')
                                    <button 
                                        wire:click="approve({{ $approval->id }})"
                                        onclick="return confirm('Are you sure you want to approve the creation of this new land record?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Approve Creation
                                    </button>
                                @else
                                    <button 
                                        wire:click="approve({{ $approval->id }})"
                                        onclick="return confirm('Are you sure you want to approve these changes?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Approve
                                    </button>
                                @endif
                                
                                <button 
                                    wire:click="showRejectModal({{ $approval->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $approval->change_type === 'delete' ? 'Deny Deletion' : 
                                    ($approval->change_type === 'create' ? 'Reject Creation' : 'Reject') }}
                                </button>
                            @else
                                <span class="text-sm text-gray-500 italic">Waiting for approval...</span>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending approvals</h3>
                <p class="mt-1 text-sm text-gray-500">All approval requests have been processed.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $pendingApprovals->links() }}
    </div>

    <!-- Rejection Modal -->
    @if($showRejectionModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click="hideRejectModal"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">
                                    Reject Approval Request
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Please provide a reason for rejecting this approval request.
                                    </p>
                                    <div class="mt-4">
                                        <label for="rejectionReason" class="block text-sm font-medium text-gray-700 mb-1">
                                            Rejection Reason <span class="text-red-500">*</span>
                                        </label>
                                        <textarea 
                                            wire:model="rejectionReason" 
                                            id="rejectionReason"
                                            rows="3" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Enter the reason for rejection (minimum 5 characters)"></textarea>
                                        @error('rejectionReason')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button 
                            type="button" 
                            wire:click="rejectWithReason"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Reject Request
                        </button>
                        <button 
                            type="button" 
                            wire:click="hideRejectModal"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>