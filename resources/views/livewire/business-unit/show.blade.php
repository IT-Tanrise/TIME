<div class="container mx-auto px-4 py-6">
    @php
        function renderHierarchy($units, $level = 0, $component = null, $isLast = [], $parentPrefix = '') {
            $html = '';
            $count = count($units);
            
            foreach ($units as $index => $unit) {
                $isLastItem = ($index === $count - 1);
                
                // Create the tree structure with proper lines
                $prefix = '';
                for ($i = 0; $i < $level; $i++) {
                    if (isset($isLast[$i]) && $isLast[$i]) {
                        $prefix .= '    '; // 4 spaces for completed branches
                    } else {
                        $prefix .= '│   '; // vertical line with 3 spaces
                    }
                }
                
                // Add the current level connector
                if ($level > 0) {
                    $connector = $isLastItem ? '└── ' : '├── ';
                    $prefix .= $connector;
                } else {
                    $prefix .= '• '; // Root level bullet
                }
                
                $html .= '<div class="flex items-center py-1 group hover:bg-gray-50 rounded px-2 -mx-2">';
                $html .= '<span class="text-gray-400 font-mono text-sm mr-2 whitespace-pre">' . $prefix . '</span>';
                $html .= '<span class="font-medium text-gray-900">' . htmlspecialchars($unit->name) . '</span>';
                $html .= '<span class="ml-2 text-sm text-gray-500">(' . htmlspecialchars($unit->code) . ')</span>';
                $html .= '<button wire:click="showDetail(' . $unit->id . ')" class="ml-auto text-blue-600 hover:text-blue-900 text-sm opacity-0 group-hover:opacity-100 transition-opacity">View</button>';
                $html .= '</div>';
                
                // Recursively render children
                if ($unit->allChildren && $unit->allChildren->count() > 0) {
                    $newIsLast = $isLast;
                    $newIsLast[$level] = $isLastItem;
                    $html .= renderHierarchy($unit->allChildren, $level + 1, $component, $newIsLast, $prefix);
                }
            }
            return $html;
        }

        function renderSimpleHierarchy($units, $level = 0) {
            $html = '';
            foreach ($units as $unit) {
                $indent = str_repeat('   ', $level); // 3 spaces per level
                $bullet = $level > 0 ? '└── ' : '• ';
                
                $html .= '<div class="py-1">';
                $html .= '<span class="text-gray-600 font-mono text-sm">' . $indent . $bullet . '</span>';
                $html .= '<span class="font-medium text-gray-900">' . htmlspecialchars($unit->name) . '</span>';
                $html .= '<span class="ml-2 text-sm text-gray-500">(' . htmlspecialchars($unit->code) . ')</span>';
                $html .= '</div>';
                
                if ($unit->allChildren && $unit->allChildren->count() > 0) {
                    $html .= renderSimpleHierarchy($unit->allChildren, $level + 1);
                }
            }
            return $html;
        }
    @endphp

    {{-- Flash Messages --}}
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

    {{-- Side by Side Layout with JavaScript Toggle --}}
    <div class="flex gap-6" id="layout-container">
        {{-- Sidebar Navigation --}}
        <div class="w-40 flex-shrink-0 transition-all duration-300" id="sidebar">
            <div class="relative">
                {{-- Toggle Button --}}
                <button onclick="toggleSidebar()" 
                        class="absolute -right-3 top-4 z-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-1.5 shadow-md transition-colors duration-200">
                    <svg class="w-4 h-4 transition-transform duration-200" id="toggle-icon" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                {{-- Navigation Content with Business Unit Context --}}
                <div class="overflow-hidden">
                    @livewire('navigation-menu-inner', ['businessUnitId' => $unit->id])
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Business Unit Details</h2>
                            <p class="text-sm text-gray-600 mt-1">ID: {{ $unit->id }} | Navigation will filter by this unit</p>
                        </div>
                        <button wire:click="showIndex" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Back to List
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {{-- Current Unit Info --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Current Business Unit</h3>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <div class="flex items-center mb-2">
                                    <span class="text-lg font-bold text-blue-900">{{ $unit->name }}</span>
                                    <span class="ml-2 text-blue-700 bg-blue-200 px-2 py-1 rounded text-sm">{{ $unit->code }}</span>
                                </div>
                                @if($unit->parent)
                                    <p class="text-sm text-blue-700">
                                        <strong>Parent:</strong> 
                                        <button wire:click="showDetail({{ $unit->parent->id }})" class="hover:underline">
                                            {{ $unit->parent->name }}
                                        </button>
                                    </p>
                                @endif
                                <p class="text-sm text-blue-700">
                                    <strong>Path:</strong> {{ $unit->hierarchy_path }}
                                </p>
                            </div>

                            {{-- Related Data Summary Cards --}}
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                @can('ownerships.access')
                                <div class="bg-indigo-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-indigo-600">Ownerships</p>
                                            <p class="text-2xl font-bold text-indigo-900">
                                                {{ App\Models\Partner::where('business_unit_id', $unit->id)->count() }}
                                            </p>
                                        </div>
                                        {{-- Updated link to use the filtered route --}}
                                        <a href="{{ route('partners.by-business-unit', $unit) }}" 
                                        class="text-indigo-600 hover:text-indigo-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <p class="text-xs text-indigo-600 mt-1">Click sidebar "Ownerships" for filtered view</p>
                                </div>
                                @endcan
                                @can('lands.access')
                                <div class="bg-yellow-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-indigo-600">Lands</p>
                                            <p class="text-2xl font-bold text-indigo-900">
                                                {{ App\Models\Land::whereHas('soils', function($query) use ($unit) {
                                                    $query->where('business_unit_id', $unit->id);
                                                })->count() }}
                                            </p>
                                        </div>
                                        {{-- Updated link to use the filtered route --}}
                                        <a href="{{ route('lands.by-business-unit', $unit) }}" 
                                        class="text-indigo-600 hover:text-indigo-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <p class="text-xs text-indigo-600 mt-1">Click sidebar "Lands" for filtered view</p>
                                </div>
                                @endcan
                                @can('soils.access')
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-green-600">Soils</p>
                                            <p class="text-2xl font-bold text-green-900">
                                                {{ App\Models\Soil::where('business_unit_id', $unit->id)->count() }}
                                            </p>
                                        </div>
                                        <a href="{{ route('soils.by-business-unit', ['businessUnit' => $unit->id]) }}" 
                                        class="text-green-600 hover:text-green-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <p class="text-xs text-green-600 mt-1">Click sidebar "Soils" for filtered view</p>
                                </div>
                                @endcan
                            </div>
                            @canany(['business-units.edit', 'business-units.delete'])
                            <div class="mt-6 pt-6 border-gray-200">
                                <div class="flex space-x-3 pt-4 border-t">
                                    @can('business-units.edit')
                                    <button wire:click="showEdit({{ $unit->id }})" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                        Edit
                                    </button>
                                    @endcan
                                    @can('business-units.delete')
                                    <button wire:click="delete({{ $unit->id }})" 
                                            onclick="return confirm('Are you sure you want to delete this business unit?')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                        Delete
                                    </button>
                                    @endcan
                                </div>
                            </div>
                            @endcan
                        </div>

                        {{-- Complete Hierarchy Tree --}}
                        <div x-data="{ open: false }">
                            {{-- Collapsible Header --}}
                            <div class="flex items-center justify-between cursor-pointer mb-4" @click="open = !open">
                                <h3 class="text-lg font-medium text-gray-900">Complete Business Units Hierarchy</h3>
                                <svg :class="{ 'rotate-180': !open }" class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div x-show="open" x-transition:enter="transition-all duration-300" 
                                         x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition-all duration-300" 
                                         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 max-h-0"
                                         class="mb-3">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-96 overflow-y-auto">
                                    <div class="font-mono text-sm space-y-1">
                                        {!! renderHierarchy($allRootUnits, 0, $this, [], '') !!}
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Current Unit's Subtree Only --}}
                            @if($unit->children->count() > 0 || $unit->parent)
                                <div class="mt-6" x-data="{ open: false }">
                                    {{-- Collapsible Header --}}
                                    <div class="flex items-center justify-between cursor-pointer mb-3" @click="open = !open">
                                        <h4 class="text-md font-medium text-gray-700">
                                            {{ $unit->name }} & Descendants
                                        </h4>
                                        <svg :class="{ 'rotate-180': !open }" class="w-4 h-4 text-gray-500 transition-transform duration-200" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div x-show="open" x-transition:enter="transition-all duration-300" 
                                         x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition-all duration-300" 
                                         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 max-h-0"
                                         class="mb-3">
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                            <div class="font-mono text-sm space-y-1">
                                                @php
                                                    // Show current unit with its children
                                                    $currentUnitCollection = collect([$unit]);
                                                @endphp
                                                {!! renderSimpleHierarchy($currentUnitCollection, 0) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Direct Children List --}}
                            @if($unit->children->count() > 0)
                                <div class="mt-6" x-data="{ open: true }">
                                    {{-- Collapsible Header --}}
                                    <div class="flex items-center justify-between cursor-pointer mb-3" @click="open = !open">
                                        <h4 class="text-md font-medium text-gray-700">
                                            Direct Children Actions 
                                            <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                                {{ $unit->children->count() }} items
                                            </span>
                                        </h4>
                                        <svg :class="{ 'rotate-180': !open }" class="w-4 h-4 text-gray-500 transition-transform duration-200" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <div x-show="open" x-transition:enter="transition-all duration-300" 
                                         x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition-all duration-300" 
                                         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 max-h-0">
                                        <div class="space-y-2">
                                            @foreach($unit->children as $child)
                                                <div class="flex justify-between items-center p-3 bg-white border border-gray-200 rounded-lg">
                                                    <div>
                                                        <span class="font-medium text-gray-900">{{ $child->name }}</span>
                                                        <span class="ml-2 text-sm text-gray-500">({{ $child->code }})</span>
                                                        @if($child->children->count() > 0)
                                                            <span class="ml-2 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                                                {{ $child->children->count() }} children
                                                            </span>
                                                        @endif
                                                        {{-- Show partner count for each child --}}
                                                        @php
                                                            $partnerCount = App\Models\Partner::where('business_unit_id', $child->id)->count();
                                                        @endphp
                                                        @if($partnerCount > 0)
                                                            <span class="ml-2 bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs">
                                                                {{ $partnerCount }} partners
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex space-x-2">
                                                        <button wire:click="showDetail({{ $child->id }})" 
                                                                class="text-blue-600 hover:text-blue-900 text-sm">View</button>
                                                        <button wire:click="showEdit({{ $child->id }})" 
                                                                class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                                                        {{-- Add Partners link for children with partners --}}
                                                        @if($partnerCount > 0)
                                                            <a href="{{ route('partners.by-business-unit', $child) }}" 
                                                               class="text-purple-600 hover:text-purple-900 text-sm">Partners</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let sidebarOpen = true;

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleIcon = document.getElementById('toggle-icon');
    const menuContent = document.getElementById('menu-content');
    const menuCollapsed = document.getElementById('menu-collapsed');
    
    sidebarOpen = !sidebarOpen;
    
    if (sidebarOpen) {
        // Open sidebar
        sidebar.classList.remove('w-16');
        sidebar.classList.add('w-40');
        toggleIcon.classList.remove('rotate-180');
        if (menuContent) menuContent.style.display = 'block';
        if (menuCollapsed) menuCollapsed.style.display = 'none';
    } else {
        // Close sidebar
        sidebar.classList.remove('w-40');
        sidebar.classList.add('w-16');
        toggleIcon.classList.add('rotate-180');
        if (menuContent) menuContent.style.display = 'none';
        if (menuCollapsed) menuCollapsed.style.display = 'block';
    }
}
</script>