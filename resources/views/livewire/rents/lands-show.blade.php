<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
    <div class="sm:flex sm:items-start">
        <div class="w-full">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl leading-6 font-semibold text-gray-900">
                    Land Rental Details
                </h3>
                <div class="flex items-center gap-2">
                    {{-- Status Badge --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($selectedRent->status === 'expired')
                            bg-red-100 text-red-800
                        @elseif($selectedRent->status === 'expiring_soon')
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-green-100 text-green-800
                        @endif">
                        {{ $selectedRent->status_label }}
                    </span>
                    
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Land Information --}}
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Land Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-700">Created Date</label>
                            <p class="text-gray-900 mt-1">{{ $selectedRent->created_at->format('F d, Y \a\t H:i') }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-700">Last Updated</label>
                            <p class="text-gray-900 mt-1">{{ $selectedRent->updated_at->format('F d, Y \a\t H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Tenant Information --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Tenant Information
                    </h4>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Full Name</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $selectedRent->nama_penyewa }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Address</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $selectedRent->alamat_penyewa }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Phone Number</label>
                            <p class="text-sm text-gray-900 mt-1">
                                <a href="tel:{{ $selectedRent->nomor_handphone_penyewa }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $selectedRent->nomor_handphone_penyewa }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Rental Period --}}
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Rental Period
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Start Date</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $selectedRent->start_rent->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">End Date</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $selectedRent->end_rent->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Time Remaining</label>
                            <p class="text-sm mt-1
                                @if($selectedRent->days_until_expiry < 0)
                                    text-red-600 font-semibold
                                @elseif($selectedRent->days_until_expiry <= 7)
                                    text-yellow-600 font-semibold
                                @else
                                    text-gray-900
                                @endif
                                @if($selectedRent->days_until_expiry < 0)
                                    {{ abs($selectedRent->days_until_expiry) }} days overdue
                                @elseif($selectedRent->days_until_expiry === 0)
                                    Expires today
                                @else
                                    {{ $selectedRent->days_until_expiry }} days remaining
                                @endif">
                            </p>
                        </div>
                    </div>

                    {{-- Rental Duration --}}
                    <div class="mt-4 pt-4 border-t border-green-200">
                        @php
                            $totalDays = $selectedRent->start_rent->diffInDays($selectedRent->end_rent);
                            $months = floor($totalDays / 30);
                            $days = $totalDays % 30;
                        @endphp
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Total Duration</label>
                            <p class="text-sm text-gray-900">
                                @if($months > 0)
                                    {{ $months }} {{ $months === 1 ? 'month' : 'months' }}
                                    @if($days > 0)
                                        and {{ $days }} {{ $days === 1 ? 'day' : 'days' }}
                                    @endif
                                @else
                                    {{ $totalDays }} {{ $totalDays === 1 ? 'day' : 'days' }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Reminder Settings --}}
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 4.828A9.973 9.973 0 019 3a9.973 9.973 0 014.172 1.828M4.828 4.828L3 3m1.828 1.828L12 12m0 0l7.072-7.072M12 12l-7.072 7.072m7.072-7.072v5.586a1 1 0 01-.293.707L12 21"/>
                        </svg>
                        Reminder Settings
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Reminder Period</label>
                            <p class="text-sm text-gray-900 mt-1">
                                {{ $selectedRent->reminder_period ? $selectedRent->reminder_period_label : 'No reminder set' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Status</label>
                            <p class="text-sm mt-1">
                                @if($selectedRent->is_expiring_soon && $selectedRent->reminder_period)
                                    <span class="text-orange-600 font-medium">⚠️ Reminder Active</span>
                                @elseif($selectedRent->reminder_period)
                                    <span class="text-gray-600">✅ Reminder Set</span>
                                @else
                                    <span class="text-gray-500">No reminder</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Additional Information --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Additional Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Land Name</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $selectedRent->land->name }}</p>
                        </div>
                        @if($selectedRent->land->businessUnit)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Business Unit</label>
                                <p class="text-sm text-gray-900 mt-1">{{ $selectedRent->land->businessUnit->name }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="text-sm font-medium text-gray-700">Rental Area</label>
                            <p class="text-sm text-gray-900 mt-1">{{ number_format($selectedRent->area_m2) }} m²</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Rental Price</label>
                            <p class="text-sm text-gray-900 mt-1 font-semibold">{{ $selectedRent->formatted_price }}</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button wire:click="closeDetail"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Close
                    </button>
                    <button wire:click="showEdit({{ $selectedRent->id }})"
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Rental
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

                