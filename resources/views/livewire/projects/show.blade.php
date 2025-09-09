<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Project Details</h2>
                <button wire:click="backToIndex" 
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Projects
                </button>
            </div>

            @php
                $project = App\Models\Project::with('land')->find($projectId);
            @endphp

            @if($project)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Project Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Project Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Project Name</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ $project->nama_project }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                <dd class="text-sm text-gray-900">{{ $project->formatted_tgl_awal ?? 'Not set' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Update</dt>
                                <dd class="text-sm text-gray-900">{{ $project->formatted_tgl_update ?? 'Not updated' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Land Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Land Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Location</dt>
                                <dd class="text-sm text-gray-900">{{ $project->land->lokasi_lahan ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Area</dt>
                                <dd class="text-sm text-gray-900">{{ $project->land->luas_lahan ?? 'N/A' }} m²</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Land Type</dt>
                                <dd class="text-sm text-gray-900">{{ $project->land->jenis_lahan ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Status Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Land Acquisition Status</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($project->land_acquisition_status == 'Complete') bg-green-100 text-green-800
                                        @elseif($project->land_acquisition_status == 'Cancelled') bg-red-100 text-red-800
                                        @elseif(in_array($project->land_acquisition_status, ['Planning', 'Negotiation'])) bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $project->land_acquisition_status }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Project Status</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($project->status == 'Completed') bg-green-100 text-green-800
                                        @elseif(in_array($project->status, ['Cancelled', 'On Hold'])) bg-red-100 text-red-800
                                        @elseif($project->status == 'Initiation') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $project->status }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="text-sm text-gray-900">{{ $project->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Modified</dt>
                                <dd class="text-sm text-gray-900">{{ $project->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="showEditForm({{ $project->id }})" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-600 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Project
                    </button>
                    <button wire:click="delete({{ $project->id }})" 
                            wire:confirm="Are you sure you want to delete this project?"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-600 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Project
                    </button>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Project not found</h3>
                    <p class="mt-1 text-sm text-gray-500">The requested project could not be found.</p>
                </div>
            @endif
        </div>
    </div>
</div>