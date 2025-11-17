<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="hideDetailModalView"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-white rounded-lg">
                            <svg class="text-indigo-600 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                Field Details
                            </h3>
                            <p class="text-sm text-indigo-100">
                                {{ $selectedField->nomor_bidang }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="hideDetailModalView" class="text-white transition hover:text-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <!-- Status Banner -->
                <div class="mb-6 p-4 rounded-lg border-2 {{ $selectedField->status_badge_color }} border-current">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white rounded-full">
                                @if($selectedField->status === 'active')
                                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($selectedField->status === 'inactive')
                                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-medium">Current Status</div>
                                <div class="text-lg font-bold">{{ ucfirst($selectedField->status) }}</div>
                            </div>
                        </div>
                        @if($selectedField->hasPendingApprovals())
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-orange-100 text-orange-800 border border-orange-300">
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Has Pending Approvals
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="mb-6">
                    <h4 class="flex items-center gap-2 mb-4 text-lg font-semibold text-gray-900">
                        <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                        Basic Information
                    </h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="mb-1 text-xs font-medium text-gray-500">Nomor Bidang</div>
                            <div class="font-mono text-base font-semibold text-gray-900">
                                {{ $selectedField->nomor_bidang }}
                            </div>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="mb-1 text-xs font-medium text-gray-500">Nama Bidang</div>
                            <div class="text-base font-semibold text-gray-900">
                                {{ $selectedField->nama_bidang }}
                            </div>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 md:col-span-2">
                            <div class="mb-1 text-xs font-medium text-gray-500">Business Unit</div>
                            <div class="flex items-center gap-2">
                                @if($selectedField->businessUnit)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                        {{ $selectedField->businessUnit->code }}
                                    </span>
                                    <div class="text-base font-semibold text-gray-900">
                                        {{ $selectedField->businessUnit->name }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($selectedField->reason_delete && $selectedField->status === 'inactive')
                <!-- Deactivation Reason -->
                <div class="mb-6">
                    <div class="p-4 border border-red-200 rounded-lg bg-red-50">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <h4 class="mb-1 text-sm font-semibold text-red-900">Deactivation Reason</h4>
                                <p class="text-sm text-red-800">{{ $selectedField->reason_delete }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Audit Information -->
                <div class="mb-6">
                    <h4 class="flex items-center gap-2 mb-4 text-lg font-semibold text-gray-900">
                        <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        Audit Information
                    </h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="mb-2 text-xs font-medium text-gray-500">Created By</div>
                            @if($selectedField->createdBy)
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-full">
                                        <span class="text-sm font-semibold text-indigo-600">
                                            {{ substr($selectedField->createdBy->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $selectedField->createdBy->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $selectedField->createdBy->email }}
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $selectedField->created_at->format('d M Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">System</span>
                            @endif
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="mb-2 text-xs font-medium text-gray-500">Last Updated By</div>
                            @if($selectedField->updatedBy)
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-full">
                                        <span class="text-sm font-semibold text-purple-600">
                                            {{ substr($selectedField->updatedBy->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $selectedField->updatedBy->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $selectedField->updatedBy->email }}
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $selectedField->updated_at->format('d M Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pending Approvals -->
                @if($selectedField->pendingApprovals->count() > 0)
                <div class="mb-6">
                    <h4 class="flex items-center gap-2 mb-4 text-lg font-semibold text-gray-900">
                        <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Pending Approvals ({{ $selectedField->pendingApprovals->count() }})
                    </h4>
                    <div class="space-y-3">
                        @foreach($selectedField->pendingApprovals as $approval)
                            <div class="p-4 border border-orange-200 rounded-lg bg-orange-50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $approval->change_type_badge_color }}">
                                                {{ ucfirst($approval->change_type) }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                Requested {{ $approval->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-700">
                                            <span class="font-medium">Requested by:</span>
                                            {{ $approval->requestedBy->name ?? 'Unknown' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Recent History -->
                @if($selectedField->histories->count() > 0)
                <div>
                    <h4 class="flex items-center gap-2 mb-4 text-lg font-semibold text-gray-900">
                        <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Recent History (Last 5)
                    </h4>
                    <div class="space-y-2 overflow-y-auto max-h-64">
                        @foreach($selectedField->histories->take(5) as $history)
                            <div class="p-3 transition border border-gray-200 rounded-lg bg-gray-50 hover:bg-gray-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $history->action_badge_color }}">
                                        {{ ucfirst($history->action) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $history->created_at->format('d M Y H:i') }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-700">
                                    <span class="font-medium">By:</span>
                                    {{ $history->user->name ?? 'System' }}
                                </div>
                                @if($history->ip_address)
                                    <div class="mt-1 text-xs text-gray-500">
                                        IP: {{ $history->ip_address }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($selectedField->histories->count() > 5)
                        <div class="mt-3 text-center">
                            <a href="{{ route('field-histories', ['filterByField' => $selectedField->id]) }}" 
                               class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                View all {{ $selectedField->histories->count() }} history records →
                            </a>
                        </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex gap-2">
                    @can('field.update')
                    <button 
                        wire:click="showEditForm({{ $selectedField->id }})"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Field
                    </button>
                    @endcan

                    @if($selectedField->status === 'active')
                        @can('field.delete')
                        <button 
                            wire:click="showDeleteModalView({{ $selectedField->id }})"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Deactivate
                        </button>
                        @endcan
                    @elseif($selectedField->status === 'inactive')
                        @can('field.update')
                        <button 
                            wire:click="activateField({{ $selectedField->id }})"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Activate
                        </button>
                        @endcan
                    @endif
                </div>

                <button 
                    wire:click="hideDetailModalView"
                    class="px-5 py-2 text-sm font-medium text-gray-700 transition bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>