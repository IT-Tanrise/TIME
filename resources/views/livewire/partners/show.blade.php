{{-- resources/views/livewire/partners/show.blade.php --}}
@php
    $partner = App\Models\Partner::with('businessUnit')->find($partnerId);
@endphp

<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-4 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Ownership Details</h2>
                <div class="flex space-x-2">
                    <button wire:click="showEditForm({{ $partner->id }})" 
                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-600 transition ease-in-out duration-150">
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                    <button wire:click="backToIndex" 
                            class="inline-flex items-center px-3 py-1.5 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to List
                    </button>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">Basic Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Partner Name</label>
                            <p class="text-sm text-gray-900 font-medium">{{ $partner->name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Business Unit</label>
                            <div class="flex items-center space-x-2">
                                <p class="text-sm text-gray-900">{{ $partner->businessUnit->name ?? '-' }}</p>
                                @if($partner->businessUnit)
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-800">
                                        {{ $partner->businessUnit->code }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ownership Details -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">Ownership Details</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Percentage</label>
                            <p class="text-lg font-semibold text-indigo-600">{{ $partner->formatted_percentage }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Lembar Saham</label>
                            <p class="text-sm text-gray-900 font-medium">{{ $partner->formatted_lembar_saham }}</p>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="bg-green-50 p-4 rounded-lg lg:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-900 border-b border-gray-200 pb-2 mb-4">Record Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Created At</label>
                            <p class="text-xs text-gray-600">{{ $partner->created_at->format('d M Y H:i:s') }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Last Updated</label>
                            <p class="text-xs text-gray-600">{{ $partner->updated_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Actions -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="text-xs text-gray-500">
                        Record ID: {{ $partner->id }}
                    </div>
                    <div class="flex space-x-2">
                        @if($businessUnit)
                            <button wire:click="backToBusinessUnit" 
                                    class="inline-flex items-center px-2 py-1 bg-blue-100 border border-blue-200 rounded text-xs font-medium text-blue-700 hover:bg-blue-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to {{ $businessUnit->name }}
                            </button>
                        @endif
                        
                        <button wire:click="delete({{ $partner->id }})" 
                                wire:confirm="Are you sure you want to delete this ownership record? This action cannot be undone."
                                class="inline-flex items-center px-2 py-1 bg-red-100 border border-red-200 rounded text-xs font-medium text-red-700 hover:bg-red-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Record
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>