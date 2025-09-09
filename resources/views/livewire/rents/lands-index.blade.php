{{-- resources/views/livewire/rents/lands-index.blade.php --}}
<div class="container mx-auto px-4 py-6">
    @if($showForm)
        @include('livewire.rents.lands-form')
    @elseif($showDetail)
        @include('livewire.rents.lands-detail')
    @else
        
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            Land Rentals
                            @if($currentBusinessUnitId)
                                @php
                                    $businessUnit = \App\Models\BusinessUnit::find($currentBusinessUnitId);
                                @endphp
                                @if($businessUnit)
                                    <span class="text-lg text-blue-600">- {{ $businessUnit->name }}</span>
                                @endif
                            @endif
                        </h2>
                        @if($currentBusinessUnitId && $businessUnit)
                            <p class="text-sm text-gray-600 mt-1">
                                Showing land rentals for business unit: <a href="{{ route('business-units', ['view' => 'show', 'id' => $businessUnit->id]) }}"><strong>{{ $businessUnit->name }}</strong></a> ({{ $businessUnit->code }})
                            </p>
                        @endif
                        @if($expiringCount > 0)
                            <p class="text-sm text-orange-600 mt-1">
                                ⚠️ {{ $expiringCount }} rental(s) expiring soon
                            </p>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        @if($currentBusinessUnitId)
                            <button wire:click="$set('currentBusinessUnitId', null)" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Show All Rentals
                            </button>
                        @endif
                        <button wire:click="showCreate" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Land Rental
                        </button>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" 
                               wire:model.live="search" 
                               id="search"
                               placeholder="Search by tenant name, phone, location..."
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="statusFilter" class="block text-sm font-medium text-gray-700">Status</label>
                        <select wire:model.live="statusFilter" 
                                id="statusFilter"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="expiring_1month">Expiring in 1 Month</option>
                            <option value="expiring_1week">Expiring in 1 Week</option>
                            <option value="expiring_3days">Expiring in 3 Days</option>
                        </select>
                    </div>

                    <!-- Reset Filters -->
                    <div class="flex items-end">
                        <button wire:click="resetFilters" 
                                class="w-full px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                            Reset Filters
                        </button>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-b">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-b">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Land & Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant Information</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area & Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rental Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rentLands as $rent)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $rent->land->lokasi_lahan ?? 'N/A' }}</div>
                                    @if($rent->land && $rent->land->businessUnits->count() > 0)
                                        <div class="text-sm text-gray-500">{{ $rent->land->businessUnits->pluck('name')->join(', ') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $rent->nama_penyewa }}</div>
                                    <div class="text-sm text-gray-500">{{ $rent->nomor_handphone_penyewa }}</div>
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $rent->alamat_penyewa }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($rent->area_m2, 0, ',', '.') }} m²</div>
                                    <div class="text-sm text-green-600">Rp {{ number_format($rent->price, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($rent->start_rent)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($rent->end_rent)->format('d M Y') }}
                                    </div>
                                    @if($rent->reminder_period)
                                        <div class="text-xs text-blue-600">
                                            🔔 {{ ucfirst(str_replace('_', ' ', $rent->reminder_period)) }} reminder
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $endDate = \Carbon\Carbon::parse($rent->end_rent);
                                        $now = \Carbon\Carbon::now();
                                        $daysUntilExpiry = floor($now->diffInDays($endDate, false));
                                    @endphp
                                    
                                    @if($daysUntilExpiry < 0)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Expired
                                        </span>
                                        <div class="text-xs text-red-600">{{ abs($daysUntilExpiry) }} days ago</div>
                                    @elseif($daysUntilExpiry <= 3)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Expires Soon
                                        </span>
                                        <div class="text-xs text-red-600">{{ $daysUntilExpiry }} days left</div>
                                    @elseif($daysUntilExpiry <= 7)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            Expiring Soon
                                        </span>
                                        <div class="text-xs text-orange-600">{{ $daysUntilExpiry }} days left</div>
                                    @elseif($daysUntilExpiry <= 30)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Expiring This Month
                                        </span>
                                        <div class="text-xs text-yellow-600">{{ $daysUntilExpiry }} days left</div>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                        <div class="text-xs text-green-600">{{ $daysUntilExpiry }} days left</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-1">
                                        <!-- View Button -->
                                        <button wire:click="showDetailView({{ $rent->id }})" 
                                                class="text-blue-600 hover:text-blue-900 text-left">View</button>
                                        
                                        <!-- Edit Button -->
                                        <button wire:click="showEdit({{ $rent->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900 text-left">Edit</button>
                                        
                                        <!-- Delete Button -->
                                        <button wire:click="delete({{ $rent->id }})" 
                                                wire:confirm="Are you sure you want to delete this rental record?"
                                                class="text-red-600 hover:text-red-900 text-left">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    @if($currentBusinessUnitId && $businessUnit)
                                        No rental records found for {{ $businessUnit->name }}.
                                    @elseif($search)
                                        No rental records found matching "{{ $search }}".
                                    @elseif($statusFilter)
                                        No rental records found with status "{{ $statusFilter }}".
                                    @else
                                        No rental records found.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-gray-200">
                {{ $rentLands->links() }}
            </div>
        </div>
    @endif
</div>