{{-- resources/views/livewire/projects/index.blade.php --}}
<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if ($showForm)
        @include('livewire.projects.form')
    @elseif ($showDetail)
        @include('livewire.projects.show')
    @else
        {{-- Main Index View --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Land Management - Projects</h2>
                        <nav class="flex flex-wrap gap-2">
                            <a href="{{ route('projects') }}" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                <svg viewBox="0 0 24 24" class="text-white w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <!-- left annex -->
                                    <path d="M3 10h6v8H3z" />
                                    <!-- main tower -->
                                    <path d="M9 6h12v12H9z" />
                                    <!-- door -->
                                    <path d="M12 14h3v4h-3z" />
                                    <!-- windows -->
                                    <path d="M11 8h2v2h-2zM15 8h2v2h-2zM11 11h2v2h-2zM15 11h2v2h-2z" />
                                    <!-- ground -->
                                    <path d="M2 20.5h20" />
                                </svg>
                                Projects
                            </a>
                            <a href="{{ route('soils') }}" 
                            class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors duration-200">
                                <svg viewBox="0 0 24 24" class="w-4 h-4 mr-2 text-white">
                                    <polygon points="4,6 20,6 21,18 3,18" fill="#90EE90" fill-opacity="0.7" 
                                            stroke="#006400" stroke-width="0.5" stroke-dasharray="1,1"/>
                                    <circle cx="4" cy="6" r="1" fill="#FF0000"/>
                                    <circle cx="20" cy="6" r="1" fill="#FF0000"/>
                                    <circle cx="21" cy="18" r="1" fill="#FF0000"/>
                                    <circle cx="3" cy="18" r="1" fill="#FF0000"/>
                                </svg>
                                Soils
                            </a>
                        </nav>
                    </div>
                    <button wire:click="showCreateForm" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add New Project
                    </button>
                </div>
            </div>

            <div class="px-6 py-4">
                {{-- Search --}}
                <div class="mb-4">
                    <input wire:model.live="search" 
                           type="text" 
                           placeholder="Search by project name, land acquisition status, or project status..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Projects Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Land Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Update</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Land Acquisition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($projects as $project)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $project->nama_project }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $project->land->lokasi_lahan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $project->formatted_tgl_awal ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $project->formatted_tgl_update ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($project->land_acquisition_status === 'Complete') bg-green-100 text-green-800
                                            @elseif($project->land_acquisition_status === 'Agreement') bg-blue-100 text-blue-800
                                            @elseif($project->land_acquisition_status === 'Negotiation') bg-yellow-100 text-yellow-800
                                            @elseif($project->land_acquisition_status === 'Planning') bg-gray-100 text-gray-800
                                            @elseif($project->land_acquisition_status === 'Cancelled') bg-red-100 text-red-800
                                            @else bg-orange-100 text-orange-800
                                            @endif">
                                            {{ $project->land_acquisition_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($project->status === 'Completed') bg-green-100 text-green-800
                                            @elseif($project->status === 'Execution') bg-blue-100 text-blue-800
                                            @elseif($project->status === 'Planning') bg-yellow-100 text-yellow-800
                                            @elseif($project->status === 'Initiation') bg-gray-100 text-gray-800
                                            @elseif($project->status === 'On Hold') bg-orange-100 text-orange-800
                                            @elseif($project->status === 'Cancelled') bg-red-100 text-red-800
                                            @else bg-purple-100 text-purple-800
                                            @endif">
                                            {{ $project->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button wire:click="showDetail({{ $project->id }})" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                                        <button wire:click="showEditForm({{ $project->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <button wire:click="delete({{ $project->id }})" 
                                                onclick="return confirm('Are you sure you want to delete this project?')"
                                                class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No projects found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    @endif
</div>