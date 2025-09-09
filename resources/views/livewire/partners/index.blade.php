{{-- resources/views/livewire/partners/index.blade.php --}}
<div>
    @if($showForm)
        @include('livewire.partners.form')
    @elseif($showDetailForm)
        @include('livewire.partners.show')
    @else
        
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            Ownerships
                            @if($businessUnit)
                                <span class="text-lg text-blue-600">- <a href="{{ route('business-units', ['view' => 'show', 'id' => $businessUnit->id]) }}">{{ $businessUnit->name }} ({{ $businessUnit->code }})</a></span>
                            @endif
                        </h2>
                    </div>
                    <div class="flex space-x-3">
                        @if($businessUnit)
                            <button wire:click="clearBusinessUnitFilter" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Show All Ownerships
                            </button>
                        @endif
                        <button wire:click="showCreateForm" 
                                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Add Partner
                        </button>
                    </div>
                </div>
                
                <div class="mt-4 flex justify-between items-center">
                    <input wire:model.live="search" 
                           type="text" 
                           placeholder="Search ownerships..." 
                           class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @if($businessUnit)
                        <div class="ml-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                Filtered by: {{ $businessUnit->name }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-b">
                    {{ session('message') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($partners as $partner)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $partner->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $partner->businessUnit->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $partner->formatted_percentage }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="showDetail({{ $partner->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">View</button>
                                    <button wire:click="showEditForm({{ $partner->id }})" 
                                            class="text-yellow-600 hover:text-yellow-900">Edit</button>
                                    <button wire:click="delete({{ $partner->id }})" 
                                            onclick="return confirm('Are you sure?')" 
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    @if($businessUnit)
                                        No partners found for {{ $businessUnit->name }}.
                                    @elseif($search)
                                        No partners found matching "{{ $search }}".
                                    @else
                                        No partners found.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-gray-200">
                {{ $partners->links() }}
            </div>
        </div>
    @endif
</div>