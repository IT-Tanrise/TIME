{{-- Notification Bell --}}
@if($expiringCount > 0)
    <div class="relative" x-data="{ open: @entangle('showNotifications') }">
        {{-- Notification Bell Button --}}
        <button @click="open = !open" 
                class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 4.828A9.973 9.973 0 019 3a9.973 9.973 0 014.172 1.828M4.828 4.828L3 3m1.828 1.828L12 12m0 0l7.072-7.072M12 12l-7.072 7.072m7.072-7.072v5.586a1 1 0 01-.293.707L12 21"/>
            </svg>
            
            {{-- Notification Badge --}}
            @if($expiringCount > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                    {{ $expiringCount > 99 ? '99+' : $expiringCount }}
                </span>
            @endif
        </button>

        {{-- Notification Dropdown --}}
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             @click.away="open = false"
             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50">
            
            {{-- Header --}}
            <div class="px-4 py-3 bg-red-50 border-b border-red-200 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-red-800">
                        Rentals Expiring Soon
                    </h3>
                    <button @click="open = false" class="text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-red-600 mt-1">
                    {{ $expiringCount }} {{ $expiringCount === 1 ? 'rental' : 'rentals' }} need your attention
                </p>
            </div>

            {{-- Notification List --}}
            <div class="max-h-96 overflow-y-auto">
                @foreach($expiringRentals as $rental)
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-start space-x-3">
                            {{-- Status Icon --}}
                            <div class="flex-shrink-0 mt-1">
                                @if($rental->is_expired)
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                @elseif($rental->days_until_expiry <= 3)
                                    <div class="w-2 h-2 bg-red-400 rounded-full animate-pulse"></div>
                                @elseif($rental->days_until_expiry <= 7)
                                    <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                                @else
                                    <div class="w-2 h-2 bg-orange-400 rounded-full"></div>
                                @endif
                            </div>
                            
                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $rental->land->name }}
                                </p>
                                <p class="text-sm text-gray-600 truncate">
                                    Tenant: {{ $rental->nama_penyewa }}
                                </p>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-xs text-gray-500">
                                        Expires: {{ $rental->end_rent->format('M d, Y') }}
                                    </p>
                                    <span class="text-xs font-medium
                                        @if($rental->is_expired)
                                            text-red-600
                                        @elseif($rental->days_until_expiry <= 3)
                                            text-red-500
                                        @elseif($rental->days_until_expiry <= 7)
                                            text-yellow-600
                                        @else
                                            text-orange-600
                                        @endif">
                                        @if($rental->is_expired)
                                            {{ abs($rental->days_until_expiry) }} days overdue
                                        @elseif($rental->days_until_expiry === 0)
                                            Expires today
                                        @else
                                            {{ $rental->days_until_expiry }} days left
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="px-4 py-3 bg-gray-50 rounded-b-lg">
                <a href="{{ route('rents.lands') }}" 
                   @click="open = false"
                   class="block w-full text-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                    View All Rentals
                </a>
            </div>
        </div>
    </div>
@endif