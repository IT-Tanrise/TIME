{{-- resources/views/livewire/partners/index.blade.php --}}
<div class="min-h-screen bg-gray-50">
    @if($showForm)
        @include('livewire.partners.form')
    @elseif($showDetailForm)
        @include('livewire.partners.show')
    @else
        <!-- Compact Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <div class="flex items-center space-x-3">
                        <h1 class="text-xl font-semibold text-gray-900">
                            Ownerships
                            @if($businessUnit)
                                <span class="text-base text-blue-600 font-normal">- <a href="{{ route('business-units', ['view' => 'show', 'id' => $businessUnit->id]) }}" class="hover:text-blue-800">{{ $businessUnit->name }} ({{ $businessUnit->code }})</a></span>
                            @endif
                        </h1>
                    </div>
                    <div class="flex space-x-2">
                        @if($businessUnit)
                            <button wire:click="clearBusinessUnitFilter" 
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Show All Ownerships
                            </button>
                        @endif
                        @can('ownerships.edit')
                        <button wire:click="showCreateForm" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add New
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <!-- Compact Search Section -->
            @if(!$this->isFiltered())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <input type="text" 
                                wire:model.live="search" 
                                placeholder="Search ownerships by name, business unit..."
                                class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-4">
                    <div class="flex justify-between items-center">
                        <input wire:model.live="search" 
                            type="text" 
                            placeholder="Search ownerships by name..." 
                            class="flex-1 px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 mr-4">
                        @if($businessUnit)
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $businessUnit->name }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Alert Messages -->
            @if (session()->has('message'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Compact Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BU</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner Name</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lembar Saham</th>
                                <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($partners as $partner)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3">
                                        <div class="text-xs text-gray-500">{{ $partner->businessUnit->code ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $partner->name }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-medium text-indigo-600">{{ $partner->formatted_percentage }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="text-sm text-gray-900">{{ $partner->formatted_lembar_saham }}</div>
                                    </td>
                                    <td class="px-2 py-3">
                                        <div class="flex items-center space-x-1">
                                            <!-- View Button -->
                                            <button wire:click="showDetail({{ $partner->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                                    title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Edit Button -->
                                            @can('ownerships.edit')
                                            <button wire:click="showEditForm({{ $partner->id }})" 
                                                    class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50"
                                                    title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            @endcan
                                            
                                            <!-- Delete Button -->
                                            @can('ownerships.delete')
                                            <button wire:click="delete({{ $partner->id }})" 
                                                    wire:confirm="Are you sure you want to delete this ownership record?"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                                                    title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        @if($businessUnit)
                                            No ownership records found for {{ $businessUnit->name }}.
                                        @elseif($search)
                                            No ownership records found matching "{{ $search }}".
                                        @else
                                            No ownership records found.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Compact Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $partners->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Enhanced JavaScript for Dropdown Behavior -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(event) {
        // Check if click is outside dropdown areas
        const businessUnitContainer = event.target.closest('.relative');
        const isDropdownArea = businessUnitContainer && (
            businessUnitContainer.querySelector('#businessUnitSearch') || 
            businessUnitContainer.querySelector('#partnerNameSearch')
        );
        
        if (!isDropdownArea && !event.target.closest('[wire\\:click\\.stop]')) {
            // Use Livewire dispatch instead of direct method call
            if (window.Livewire) {
                window.Livewire.dispatch('close-all-dropdowns');
            }
        }
    });

    // Handle escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && window.Livewire) {
            window.Livewire.dispatch('close-all-dropdowns');
        }
    });
});
</script>