{{-- Navigation Menu --}}
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 border-b border-gray-200">
        {{-- Full Menu (when expanded) --}}
        <div id="menu-content">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Menu</h2>
            <nav class="space-y-2">
                <!-- Ownerships -->
                <a href="{{ $this->getOwnershipsUrl() }}" 
                   class="block px-2 py-1 text-gray-800 rounded-lg hover:font-semibold transition-colors duration-200 {{ $this->isActive('partners') ? 'bg-blue-100 font-semibold' : '' }}">
                    Ownerships
                    @if($this->getCurrentBusinessUnitId())
                        <span class="text-xs text-blue-600 block">
                            (Filtered by Current Unit)
                        </span>
                    @endif
                </a>
                
                <!-- Lands -->
                <a href="{{ $this->getLandsUrl() }}" 
                   class="block px-2 py-1 text-gray-800 rounded-lg hover:font-semibold transition-colors duration-200 {{ $this->isActive('lands') ? 'bg-blue-100 font-semibold' : '' }}">
                    Lands
                    @if($this->getCurrentBusinessUnitId())
                        <span class="text-xs text-blue-600 block">
                            (Filtered by Current Unit)
                        </span>
                    @endif
                </a>
                
                <!-- Soils -->
                <a href="{{ $this->getSoilsUrl() }}" 
                   class="block px-2 py-1 text-gray-800 rounded-lg hover:font-semibold transition-colors duration-200 {{ $this->isActive('soils') ? 'bg-blue-100 font-semibold' : '' }}">
                    Soils
                    @if($this->getCurrentBusinessUnitId())
                        <span class="text-xs text-blue-600 block">
                            (Filtered by Current Unit)
                        </span>
                    @endif
                </a>\

                <!-- Rent Submenu -->
                <div class="relative" x-data="{ open: {{ $this->isActive('rents') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="flex items-center justify-between w-full px-2 py-1 text-gray-800 rounded-lg hover:font-semibold transition-colors duration-200 {{ $this->isActive('rents') ? 'bg-blue-100 font-semibold' : '' }}">
                        <span>Rent</span>
                        <svg class="w-4 h-4 transition-transform duration-200" 
                             :class="{ 'rotate-180': open }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Submenu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="ml-4 mt-1 space-y-1">
                        
                        <!-- Land Rentals -->
                        <a href="{{ $this->getRentLandsUrl() }}" 
                           class="block px-2 py-1 text-gray-700 rounded-lg hover:bg-gray-100 hover:font-semibold transition-colors duration-200 {{ $this->isActive('rents.lands') ? 'bg-blue-50 font-semibold text-blue-700' : '' }}">
                            Land Rentals
                            @if($this->getCurrentBusinessUnitId())
                                <span class="text-xs text-blue-600 block">
                                    (Filtered by Current Unit)
                                </span>
                            @endif
                        </a>
                        
                        <!-- Placeholder for future rent types -->
                        <div class="px-2 py-1 text-gray-400 text-sm italic">
                            Other rent types coming soon...
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        
        {{-- Collapsed Menu (when collapsed) --}}
        <div id="menu-collapsed" class="text-center" style="display: none;">
            <button id="menu-expand-btn" 
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                    aria-label="Expand menu">
                {{-- Hamburger Icon --}}
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
</div>