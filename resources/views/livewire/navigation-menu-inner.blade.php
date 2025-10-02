{{-- Navigation Menu --}}
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 border-b border-gray-200">
        {{-- Full Menu (when expanded) --}}
        <div id="menu-content">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Menu</h2>
            <nav class="space-y-2">
                <!-- Ownerships -->
                @can('ownerships.access')
                <a href="{{ $this->getOwnershipsUrl() }}" 
                   class="block px-2 py-1 text-gray-800 rounded-lg hover:font-semibold transition-colors duration-200 {{ $this->isActive('partners') ? 'bg-blue-100 font-semibold' : '' }}">
                    Ownerships
                    @if($this->getCurrentBusinessUnitId())
                        <span class="text-xs text-blue-600 block">
                            (Filtered by Current Unit)
                        </span>
                    @endif
                </a>
                @endcan
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
                @can('soils.access')
                <a href="{{ $this->getSoilsUrl() }}" 
                   class="block px-2 py-1 text-gray-800 rounded-lg hover:font-semibold transition-colors duration-200 {{ $this->isActive('soils') ? 'bg-blue-100 font-semibold' : '' }}">
                    Soils
                    @if($this->getCurrentBusinessUnitId())
                        <span class="text-xs text-blue-600 block">
                            (Filtered by Current Unit)
                        </span>
                    @endif
                </a>
                @endcan
                
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