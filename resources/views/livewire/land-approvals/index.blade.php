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
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Approve Deletion
                                    </button>
                                @elseif($approval->change_type === 'create')
                                    <button 
                                        wire:click="approve({{ $approval->id }})"
                                        onclick="return confirm('Are you sure you want to approve the creation of this new land record?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V7z" clip-rule="evenodd"/>
                                        </svg>
                                        Approve Creation
                                    </button>
                                @else
                                    <button 
                                        wire:click="approve({{ $approval->id }})"
                                        onclick="return confirm('Are you sure you want to approve these changes?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Approve
                                    </button>
                                @endif
                                
                                <button 
                                    wire:click="showRejectModal({{ $approval->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0