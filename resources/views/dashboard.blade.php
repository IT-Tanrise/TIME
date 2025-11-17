<x-app-layout>
    <div class="py-3">
        <div class="mx-auto sm:px-6 lg:px-8">
            {{-- Compact Header Section --}}
            <div class="p-4 mb-4 shadow-sm bg-gradient-to-r from-blue-50 to-indigo-50 sm:rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Welcome, {{ auth()->user()->name }}!</h1>
                        @role('Super Admin')
                            <button onclick="toggleSection('rolePermission')"
                                class="flex items-center mt-1 text-xs text-blue-600 hover:text-blue-800">
                                View role & permissions
                                <svg id="rolePermission-icon" class="w-3 h-3 ml-1 transition-transform duration-200 transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        @endrole
                    </div>

                    {{-- Pending Approvals Summary Badge --}}
                    @canany(['fields.approval', 'pre-soil-buy.approval', 'land-data.approval', 'soil-data.approval',
                        'soil-data-costs.approval'])
                        @php
                            $totalLandPending = 0;
                            $totalSoilPending = 0;
                            $totalPreSoilBuyPending = 0;

                            if (auth()->user()->can('land-data.approval')) {
                                $totalLandPending = App\Models\LandApproval::pending()->count();
                            }

                            $allowedChangeTypes = [];

                            if (auth()->user()->can('soil-data.approval')) {
                                $allowedChangeTypes = array_merge($allowedChangeTypes, ['details', 'delete', 'create']);
                            }

                            if (auth()->user()->can('soil-data-costs.approval')) {
                                $allowedChangeTypes[] = 'costs';
                            }

                            if (auth()->user()->can('soil-data-interest-costs.approval')) {
                                $allowedChangeTypes[] = 'interest';
                            }

                            if (!empty($allowedChangeTypes)) {
                                $totalSoilPending = App\Models\SoilApproval::pending()
                                    ->whereIn('change_type', $allowedChangeTypes)
                                    ->count();
                            }

                            if (auth()->user()->can('pre-soil-buy.approval')) {
                                $totalPreSoilBuyPending = App\Models\PreSoilBuyApproval::where(
                                    'status',
                                    'pending',
                                )->count();
                            }

                            $totalPending = $totalLandPending + $totalSoilPending + $totalPreSoilBuyPending;
                        @endphp

                        @if ($totalPending > 0)
                            <button onclick="toggleSection('pendingApprovals')"
                                class="flex items-center px-4 py-2 space-x-2 transition-colors bg-yellow-100 rounded-lg hover:bg-yellow-200">
                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div class="text-left">
                                    <div class="text-xs font-medium text-yellow-800">Pending Approvals</div>
                                    <div class="text-lg font-bold text-yellow-900">{{ $totalPending }}</div>
                                </div>
                            </button>
                        @endif
                    @endcanany

                </div>

                {{-- Collapsible Role & Permissions --}}
                @role('Super Admin')
                    <div id="rolePermission-content" class="hidden pt-3 mt-3 border-t border-blue-200">
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div>
                                <p class="mb-1 text-xs font-medium text-gray-700">Your Roles:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach (auth()->user()->roles as $role)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <p class="mb-1 text-xs font-medium text-gray-700">Your Permissions:</p>
                                <div class="flex flex-wrap gap-1 overflow-y-auto max-h-20">
                                    @if (auth()->user()->getAllPermissions()->count() > 0)
                                        @foreach (auth()->user()->getAllPermissions() as $permission)
                                            <span
                                                class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-xs text-gray-500">No permissions available.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endrole
            </div>

            {{-- Compact Management Systems Grid --}}
            <div class="p-4 mb-4 bg-white shadow-sm sm:rounded-lg">
                <h2 class="mb-3 text-base font-bold text-gray-900">TIME - Tanrise Information Management System</h2>

                <div class="grid grid-cols-2 gap-2 md:grid-cols-3 lg:grid-cols-5">
                    {{-- ADEM --}}
                    @canany(['fields.access', 'lands.access', 'soils.access', 'ownerships.access',
                        'pre-soil-buy.access'])
                        <div class="relative">
                            <button onclick="toggleMenu('adem')"
                                class="flex items-center justify-between w-full p-2 text-left transition-colors rounded-lg bg-blue-50 hover:bg-blue-100">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-blue-200 p-1.5 rounded">
                                        <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">ADEM</h3>
                                        <p class="text-xs text-gray-600">Asset Database</p>
                                    </div>
                                </div>
                                <svg id="adem-icon"
                                    class="w-4 h-4 text-gray-400 transition-transform duration-200 transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="adem-menu"
                                class="absolute left-0 right-0 z-10 hidden mt-1 bg-white border border-gray-200 rounded-lg shadow-lg top-full min-w-max">

                                {{-- Ownerships --}}
                                @can('ownerships.access')
                                    <a href="{{ route('partners.index') }}"
                                        class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-blue-50 rounded-t-lg">
                                        <i class="mr-1 fa-solid fa-user-tie"></i>
                                        Ownerships
                                    </a>
                                @endcan

                                {{-- Lands --}}
                                @can('lands.access')
                                    <button onclick="showBusinessUnitModal('lands')"
                                        class="w-full text-left px-3 py-1.5 text-xs text-gray-700 hover:bg-blue-50">
                                        <i class="mr-1 fa-solid fa-mountain-city"></i>
                                        Lands
                                    </button>
                                @endcan

                                {{-- Soils --}}
                                @can('soils.access')
                                    <button onclick="showBusinessUnitModal('soils')"
                                        class="w-full text-left px-3 py-1.5 text-xs text-gray-700 hover:bg-blue-50">
                                        <i class="mr-1 fa-solid fa-seedling"></i>
                                        Soils
                                    </button>
                                @endcan

                                {{-- Internal Memo --}}
                                @can('pre-soil-buy.access')
                                    <a href="{{ route('preSoilBuy.index') }}"
                                        class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-blue-50">
                                        <i class="mr-1 fa-solid fa-file"></i>
                                        Internal Memo
                                    </a>
                                @endcan

                                {{-- Fields --}}
                                @can('fields.access')
                                    <a href="{{ route('fields.index') }}"
                                        class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-blue-50 rounded-b-lg">
                                        <i class="mr-1 fa-solid fa-chart-area"></i>
                                        Fields
                                    </a>
                                @endcan
                            </div>

                        </div>
                    @endcanany

                    {{-- RISOL --}}
                    @canany(['rentals.access'])
                        <div class="relative">
                            <button onclick="toggleMenu('risol')"
                                class="flex items-center justify-between w-full p-2 text-left transition-colors rounded-lg bg-green-50 hover:bg-green-100">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-green-200 p-1.5 rounded">
                                        <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">RISOL</h3>
                                        <p class="text-xs text-gray-600">Rental</p>
                                    </div>
                                </div>
                                <svg id="risol-icon"
                                    class="w-4 h-4 text-gray-400 transition-transform duration-200 transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="risol-menu"
                                class="absolute left-0 right-0 z-10 hidden mt-1 bg-white border border-gray-200 rounded-lg shadow-lg top-full min-w-max">
                                <a href="{{ route('rents.lands') }}"
                                    class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-green-50 rounded-t-lg">📋 Land
                                    Rentals</a>
                                <a href="#" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-green-50">💰
                                    Payment Tracking</a>
                                <a href="#"
                                    class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-green-50 rounded-b-lg">📅
                                    Rental
                                    Schedule</a>
                            </div>
                        </div>
                    @endcanany

                    {{-- EMAS --}}
                    @canany(['estates.access'])
                        <div class="relative">
                            <button onclick="toggleMenu('emas')"
                                class="flex items-center justify-between w-full p-2 text-left transition-colors rounded-lg bg-yellow-50 hover:bg-yellow-100">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-yellow-200 p-1.5 rounded">
                                        <svg class="w-4 h-4 text-yellow-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">EMAS</h3>
                                        <p class="text-xs text-gray-600">Estate</p>
                                    </div>
                                </div>
                                <svg id="emas-icon"
                                    class="w-4 h-4 text-gray-400 transition-transform duration-200 transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="emas-menu"
                                class="absolute left-0 right-0 z-10 hidden mt-1 bg-white border border-gray-200 rounded-lg shadow-lg top-full min-w-max">
                                <a href="#"
                                    class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-yellow-50 rounded-t-lg">🏢
                                    Property List</a>
                                <a href="#"
                                    class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-yellow-50 rounded-b-lg">🔧
                                    Maintenance</a>
                            </div>
                        </div>
                    @endcanany

                    {{-- DOTS --}}
                    @canany(['interests.access', 'depositos.access'])
                        <div class="relative">
                            <button onclick="toggleMenu('dots')"
                                class="flex items-center justify-between w-full p-2 text-left transition-colors rounded-lg bg-purple-50 hover:bg-purple-100">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-purple-200 p-1.5 rounded">
                                        <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">DOTS</h3>
                                        <p class="text-xs text-gray-600">Deposito</p>
                                    </div>
                                </div>
                                <svg id="dots-icon"
                                    class="w-4 h-4 text-gray-400 transition-transform duration-200 transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="dots-menu"
                                class="absolute left-0 right-0 z-10 hidden mt-1 bg-white border border-gray-200 rounded-lg shadow-lg top-full min-w-max">
                                @can('depositos.access')
                                    <a href="#"
                                        class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-purple-50 rounded-t-lg">💵
                                        Deposits List</a>
                                @endcan
                                @can('interests.access')
                                    <a href="#"
                                        class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-purple-50 rounded-b-lg">📈
                                        Interest Tracking</a>
                                @endcan
                            </div>
                        </div>
                    @endcanany

                    {{-- COMS --}}
                    @canany(['contractors.access', 'projects.access', 'vendors.access'])
                        <div class="relative">
                            <button onclick="toggleMenu('coms')"
                                class="flex items-center justify-between w-full p-2 text-left transition-colors rounded-lg bg-red-50 hover:bg-red-100">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-red-200 p-1.5 rounded">
                                        <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">COMS</h3>
                                        <p class="text-xs text-gray-600">Construction</p>
                                    </div>
                                </div>
                                <svg id="coms-icon"
                                    class="w-4 h-4 text-gray-400 transition-transform duration-200 transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="coms-menu"
                                class="absolute left-0 right-0 z-10 hidden mt-1 bg-white border border-gray-200 rounded-lg shadow-lg top-full min-w-max">
                                @can('projects.access')
                                    <a href="{{ route('projects') }}"
                                        class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-red-50 rounded-t-lg">🗂️
                                        Projects</a>
                                @endcan
                                @can('contractors.access')
                                    <a href="#"
                                        class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-red-50 rounded-b-lg">👷
                                        Contractors</a>
                                @endcan
                                @can('vendors.access')
                                    <a href="{{ route('vendors') }}"
                                        class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-red-50 rounded-t-lg">🗂️
                                        Vendors</a>
                                @endcan
                            </div>
                        </div>
                    @endcanany
                </div>
            </div>

            {{-- Business Unit Selection Modal --}}
            <div id="businessUnitModal"
                class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
                <div
                    class="relative w-11/12 p-5 mx-auto bg-white border rounded-md shadow-lg top-20 md:w-3/4 lg:w-1/2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Select Business Unit</h3>
                        <button onclick="closeBusinessUnitModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="mb-4 text-sm text-gray-500">Choose a business unit to view filtered data, or view all
                        data</p>

                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="businessUnitSearch" placeholder="Search by name or code..."
                            oninput="filterBusinessUnits()"
                            class="w-full py-2 pl-10 pr-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="space-y-2 overflow-y-auto max-h-96" id="businessUnitList">
                        @php
                            $businessUnits = \App\Models\BusinessUnit::with('parent')->orderBy('name')->get();
                        @endphp

                        <button onclick="navigateToModule('all')"
                            class="w-full px-2 py-1 text-left transition-colors border border-blue-200 rounded-lg business-unit-item bg-blue-50 hover:bg-blue-100"
                            data-search="view all data">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                <span class="font-semibold text-blue-900">View All Data</span>
                            </div>
                        </button>

                        @foreach ($businessUnits as $unit)
                            <button onclick="navigateToModule({{ $unit->id }})"
                                class="w-full px-2 py-1 text-left transition-colors border border-gray-200 rounded-lg business-unit-item bg-gray-50 hover:bg-gray-100"
                                data-search="{{ strtolower($unit->name . ' ' . $unit->code) }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <svg class="flex-shrink-0 w-5 h-5 mr-3 text-gray-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mt-1">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $unit->code }}
                                                </span>
                                                <span
                                                    class="block font-medium text-gray-900">{{ $unit->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <div id="noResults" class="hidden py-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No business units found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms.</p>
                    </div>
                </div>
            </div>

            {{-- Pending Approvals Section - Compact --}}
            @canany(['pre-soil-buy.approval', 'land-data.approval', 'soil-data.approval', 'soil-data-costs.approval',
                'soil-data-interest-costs.approval'])
                <div class="mb-4 bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <button onclick="toggleSection('pendingApprovals')"
                            class="flex items-center justify-between w-full mb-3 text-left group">
                            <div class="flex items-center space-x-2">
                                <h2 class="text-base font-bold text-gray-900">Pending Approvals</h2>
                                @if ($totalPending > 0)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $totalPending }}
                                    </span>
                                @endif
                            </div>
                            <svg id="pendingApprovals-icon"
                                class="w-4 h-4 text-gray-500 transition-transform duration-200 transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="pendingApprovals-content" class="hidden">
                            @if ($totalPending > 0)
                                <div class="p-2 mb-3 text-xs text-yellow-700 border-l-4 border-yellow-400 bg-yellow-50">
                                    You have approval permissions. Changes require your review before being applied.
                                </div>
                            @endif
                            <livewire:merged-approvals />
                        </div>
                    </div>
                </div>
            @endcanany

            @can('pre-soil-buy.cashOut')
                @php

                    $totalPreSoilBuyCashOutPending = 0;

                    if (auth()->user()->can('pre-soil-buy.cashOut')) {
                        $totalPreSoilBuyCashOutPending = App\Models\PreSoilBuyCashOut::where(
                            'status',
                            'pending',
                        )->count();
                    }

                @endphp
                <div class="mb-4 bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <button onclick="toggleSection('preSoilBuyCashOut')"
                            class="flex items-center justify-between w-full mb-3 text-left group">
                            <div class="flex items-center space-x-2">
                                <h2 class="text-base font-bold text-gray-900">Pre-Soil-Buy Cash Out Requests</h2>
                                @if ($totalPreSoilBuyCashOutPending > 0)
                                    <span
                                        class="px-2 py-0.5 text-xs font-bold rounded-full  bg-yellow-100 text-yellow-700 }}">
                                        {{ $totalPreSoilBuyCashOutPending }}
                                    </span>
                                @endif
                            </div>
                            <svg id="preSoilBuyCashOut-icon"
                                class="w-4 h-4 text-gray-500 transition-transform duration-200 transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="preSoilBuyCashOut-content" class="hidden">

                            @if ($totalPreSoilBuyCashOutPending > 0)
                                <div class="p-2 mb-3 text-xs text-yellow-700 border-l-4 border-yellow-400 bg-yellow-50">
                                    You have approval permissions. Changes require your review before being applied.
                                </div>
                            @endif
                            <livewire:cash-out />
                        </div>
                    </div>
                </div>
            @endcan

            {{-- Recent Activity Section - Compact --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <button onclick="toggleSection('recentActivity')"
                        class="flex items-center justify-between w-full mb-3 text-left group">
                        <h2 class="text-base font-bold text-gray-900">Recent Approval Activity</h2>
                        <svg id="recentActivity-icon"
                            class="w-4 h-4 text-gray-500 transition-transform duration-200 transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="recentActivity-content" class="hidden">
                        @php
                            $recentSoilApprovals = App\Models\SoilApproval::with([
                                'soil.land',
                                'soil.businessUnit',
                                'requestedBy',
                                'approvedBy',
                            ])
                                ->whereIn('status', ['approved', 'rejected'])
                                ->where('updated_at', '>=', now()->subDays(30))
                                ->latest('updated_at')
                                ->limit(5)
                                ->get();

                            $recentLandApprovals = App\Models\LandApproval::with(['land', 'requestedBy', 'approvedBy'])
                                ->whereIn('status', ['approved', 'rejected'])
                                ->where('updated_at', '>=', now()->subDays(30))
                                ->latest('updated_at')
                                ->limit(5)
                                ->get();

                            $recentPreSoilBuyApprovals = App\Models\PreSoilBuyApproval::with([
                                'preSoilBuy',
                                'requestedBy',
                                'approvedBy',
                            ])
                                ->whereIn('status', ['approved', 'rejected'])
                                ->where('updated_at', '>=', now()->subDays(30))
                                ->latest('updated_at')
                                ->limit(5)
                                ->get();

                            $recentApprovals = $recentSoilApprovals
                                ->concat($recentLandApprovals)
                                ->concat($recentPreSoilBuyApprovals)
                                ->sortByDesc('updated_at')
                                ->take(10);

                            $userPendingSoilApprovals = App\Models\SoilApproval::with([
                                'soil.land',
                                'soil.businessUnit',
                            ])
                                ->where('requested_by', auth()->id())
                                ->where('status', 'pending')
                                ->latest('created_at')
                                ->limit(3)
                                ->get();

                            $userPendingLandApprovals = App\Models\LandApproval::with(['land'])
                                ->where('requested_by', auth()->id())
                                ->where('status', 'pending')
                                ->latest('created_at')
                                ->limit(3)
                                ->get();

                            $userPendingPreSoilBuyApprovals = App\Models\PreSoilBuyApproval::with(['preSoilBuy'])
                                ->where('requested_by', auth()->id())
                                ->where('status', 'pending')
                                ->latest('created_at')
                                ->limit(3)
                                ->get();

                            $userPendingApprovals = $userPendingSoilApprovals
                                ->concat($userPendingLandApprovals)
                                ->concat($userPendingPreSoilBuyApprovals)
                                ->sortByDesc('created_at');
                        @endphp

                        @if ($userPendingApprovals->count() > 0)
                            <div class="p-2 mb-3 border-l-4 border-blue-400 bg-blue-50">
                                <h3 class="mb-2 text-xs font-medium text-blue-800">
                                    Your Pending Approvals ({{ $userPendingApprovals->count() }})
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-blue-100">
                                            <tr>
                                                <th class="px-2 py-1 font-medium text-left text-blue-800">Type</th>
                                                <th class="px-2 py-1 font-medium text-left text-blue-800">Action</th>
                                                <th class="px-2 py-1 font-medium text-left text-blue-800">Details</th>
                                                <th class="px-2 py-1 font-medium text-left text-blue-800">Submitted
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-blue-200">
                                            @foreach ($userPendingApprovals as $approval)
                                                <tr>
                                                    <td class="px-2 py-1 whitespace-nowrap">
                                                        @if ($approval instanceof App\Models\LandApproval)
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-800">LAND</span>
                                                        @elseif($approval instanceof App\Models\PreSoilBuyApproval)
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800">PRE
                                                                SOIL BUY</span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">SOIL</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-2 py-1 whitespace-nowrap">
                                                        @if ($approval->change_type === 'create')
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">CREATE</span>
                                                        @elseif($approval->change_type === 'delete')
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">DELETE</span>
                                                        @elseif($approval->change_type === 'costs')
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">COSTS</span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">UPDATE</span>
                                                        @endif
                                                    </td>
                                                    <td class="max-w-xs px-2 py-1 truncate">
                                                        @if ($approval instanceof App\Models\LandApproval)
                                                            {{ $approval->land->lokasi_lahan ?? 'Land Record' }}
                                                        @elseif($approval instanceof App\Models\PreSoilBuyApproval)
                                                            {{ $approval->preSoilBuy->nomor_memo ?? 'Pre Soil Buy Record' }}
                                                        @else
                                                            {{ $approval->soil->nama_penjual ?? 'Soil Record' }}
                                                        @endif
                                                    </td>
                                                    <td class="px-2 py-1 text-gray-500 whitespace-nowrap">
                                                        {{ $approval->created_at->diffForHumans() }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if ($recentApprovals->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 font-medium text-left text-gray-500 uppercase">Status
                                            </th>
                                            <th class="px-3 py-2 font-medium text-left text-gray-500 uppercase">Type
                                            </th>
                                            <th class="px-3 py-2 font-medium text-left text-gray-500 uppercase">Action
                                            </th>
                                            <th class="px-3 py-2 font-medium text-left text-gray-500 uppercase">Details
                                            </th>
                                            <th class="px-3 py-2 font-medium text-left text-gray-500 uppercase">
                                                Requested By</th>
                                            <th class="px-3 py-2 font-medium text-left text-gray-500 uppercase">
                                                Processed By</th>
                                            <th class="px-3 py-2 font-medium text-left text-gray-500 uppercase">Reason
                                            </th>
                                            <th class="px-3 py-2 font-medium text-left text-gray-500 uppercase">Time
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recentApprovals as $approval)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    @if ($approval->status === 'approved')
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Approved
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            Rejected
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    @if ($approval instanceof App\Models\LandApproval)
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-800">LAND</span>
                                                    @elseif($approval instanceof App\Models\PreSoilBuyApproval)
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800">PRE
                                                            SOIL BUY</span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">SOIL</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    @if ($approval->change_type === 'delete')
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">DELETE</span>
                                                    @elseif($approval->change_type === 'create')
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">CREATE</span>
                                                    @elseif($approval->change_type === 'costs')
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">COSTS</span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">UPDATE</span>
                                                    @endif
                                                </td>
                                                <td class="max-w-xs px-3 py-2">
                                                    <div class="truncate"
                                                        title="@if ($approval instanceof App\Models\LandApproval) {{ $approval->land->lokasi_lahan ?? 'Deleted Land Record' }}@elseif($approval instanceof App\Models\PreSoilBuyApproval){{ $approval->preSoilBuy->nomor_memo ?? 'Deleted Pre Soil Buy Record' }}@else{{ $approval->soil->nama_penjual ?? 'Deleted Soil Record' }} @endif">
                                                        @if ($approval instanceof App\Models\LandApproval)
                                                            {{ $approval->land->lokasi_lahan ?? 'Deleted Land Record' }}
                                                        @elseif($approval instanceof App\Models\PreSoilBuyApproval)
                                                            {{ $approval->preSoilBuy->nomor_memo ?? 'Deleted Pre Soil Buy Record' }}
                                                        @else
                                                            {{ $approval->soil->nama_penjual ?? 'Deleted Soil Record' }}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2 text-gray-500 whitespace-nowrap">
                                                    {{ $approval->requestedBy->name ?? 'Unknown' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span
                                                        class="{{ $approval->status === 'approved' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $approval->approvedBy->name ?? ($approval->respondedBy->name ?? 'Unknown') }}
                                                    </span>
                                                </td>
                                                <td class="max-w-xs px-3 py-2">
                                                    @if ($approval->reason)
                                                        <div class="truncate" title="{{ $approval->reason }}">
                                                            {{ Str::limit($approval->reason, 30) }}
                                                        </div>
                                                    @else
                                                        <span class="italic text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 text-gray-500 whitespace-nowrap">
                                                    <div title="{{ $approval->updated_at->format('Y-m-d H:i:s') }}">
                                                        {{ $approval->updated_at->diffForHumans() }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- <div class="flex justify-center gap-2 mt-3">
                                @can('land-data.approval')
                                    <a href="{{ route('land-approvals') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                        View All Land Approvals
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                @endcan
                                
                                @canany(['soil-data.approval', 'soil-data-costs.approval'])
                                    <a href="{{ route('soil-approvals') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        View All Soil Approvals
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                @endcanany

                                @can('pre-soil-buy.approval')
                                    <a href="{{ route('pre-soil-buy-approvals') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        View All Pre Soil Buy Approvals
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                @endcan
                            </div> --}}
                        @else
                            <div class="py-8 text-center">
                                <svg class="w-10 h-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No recent approval activity</h3>
                                <p class="mt-1 text-xs text-gray-500">No approval requests have been processed
                                    recently.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Expiry Monitoring Section - Compact --}}
            <div class="mt-4 bg-white shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <button onclick="toggleSection('expiryMonitoring')"
                        class="flex items-center justify-between w-full mb-3 text-left group">
                        <div class="flex items-center space-x-2">
                            <h2 class="text-base font-bold text-gray-900">Expiry Monitoring</h2>
                            @php
                                // Get SHGB expiring soon (within 90 days) or already expired
                                $shgbExpiring = App\Models\Soil::whereNotNull('shgb_expired_date')
                                    ->where('shgb_expired_date', '<=', now()->addDays(90))
                                    ->with(['land', 'businessUnit'])
                                    ->orderBy('shgb_expired_date', 'asc')
                                    ->get();

                                $shgbExpired = $shgbExpiring->filter(fn($soil) => $soil->shgb_expired_date->isPast());
                                $shgbExpiringSoon = $shgbExpiring->filter(
                                    fn($soil) => !$soil->shgb_expired_date->isPast(),
                                );

                                $totalExpiring = $shgbExpiring->count();
                            @endphp

                            @if ($totalExpiring > 0)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                    {{ $shgbExpired->count() > 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $totalExpiring }}
                                </span>
                            @endif
                        </div>
                        <svg id="expiryMonitoring-icon"
                            class="w-4 h-4 text-gray-500 transition-transform duration-200 transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="expiryMonitoring-content" class="hidden">
                        @if ($totalExpiring > 0)
                            {{-- Summary Cards --}}
                            <div class="grid grid-cols-1 gap-3 mb-4 md:grid-cols-3">
                                <div class="p-3 border border-red-200 rounded-lg bg-red-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-medium text-red-600">Expired</p>
                                            <p class="text-2xl font-bold text-red-900">{{ $shgbExpired->count() }}</p>
                                        </div>
                                        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="p-3 border border-yellow-200 rounded-lg bg-yellow-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-medium text-yellow-600">Expiring Soon (≤90 days)</p>
                                            <p class="text-2xl font-bold text-yellow-900">
                                                {{ $shgbExpiringSoon->count() }}</p>
                                        </div>
                                        <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="p-3 border border-blue-200 rounded-lg bg-blue-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-medium text-blue-600">Total Monitored</p>
                                            <p class="text-2xl font-bold text-blue-900">{{ $totalExpiring }}</p>
                                        </div>
                                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Expired Items --}}
                            @if ($shgbExpired->count() > 0)
                                <div class="mb-4">
                                    <h3 class="flex items-center mb-2 text-sm font-semibold text-red-900">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Expired SHGB Certificates ({{ $shgbExpired->count() }})
                                    </h3>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-xs divide-y divide-gray-200">
                                            <thead class="bg-red-50">
                                                <tr>
                                                    <th class="px-3 py-2 font-medium text-left text-red-800 uppercase">
                                                        Soil Location</th>
                                                    <th class="px-3 py-2 font-medium text-left text-red-800 uppercase">
                                                        Land</th>
                                                    <th class="px-3 py-2 font-medium text-left text-red-800 uppercase">
                                                        Business Unit</th>
                                                    <th class="px-3 py-2 font-medium text-left text-red-800 uppercase">
                                                        Expired Date</th>
                                                    <th class="px-3 py-2 font-medium text-left text-red-800 uppercase">
                                                        Days Overdue</th>
                                                    <th
                                                        class="px-3 py-2 font-medium text-center text-red-800 uppercase">
                                                        Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($shgbExpired as $soil)
                                                    <tr class="hover:bg-red-50">
                                                        <td class="px-3 py-2">
                                                            <div class="font-medium text-gray-900">
                                                                {{ $soil->letak_tanah }}</div>
                                                            <div class="text-gray-500">{{ $soil->nama_penjual }}</div>
                                                        </td>
                                                        <td class="px-3 py-2 text-gray-600">
                                                            {{ $soil->land->lokasi_lahan ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                {{ $soil->businessUnit->code ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2 font-medium text-red-600">
                                                            {{ $soil->shgb_expired_date->format('d/m/Y') }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                                {{ abs($soil->shgb_days_until_expiration) }} days
                                                                overdue
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2 text-center">
                                                            <a href="{{ route('soils.show', $soil->id) }}"
                                                                class="text-blue-600 hover:text-blue-900"
                                                                title="View Details">
                                                                <svg class="inline w-5 h-5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- Expiring Soon Items --}}
                            @if ($shgbExpiringSoon->count() > 0)
                                <div>
                                    <h3 class="flex items-center mb-2 text-sm font-semibold text-yellow-900">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Expiring Soon ({{ $shgbExpiringSoon->count() }})
                                    </h3>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-xs divide-y divide-gray-200">
                                            <thead class="bg-yellow-50">
                                                <tr>
                                                    <th
                                                        class="px-3 py-2 font-medium text-left text-yellow-800 uppercase">
                                                        Soil Location</th>
                                                    <th
                                                        class="px-3 py-2 font-medium text-left text-yellow-800 uppercase">
                                                        Land</th>
                                                    <th
                                                        class="px-3 py-2 font-medium text-left text-yellow-800 uppercase">
                                                        Business Unit</th>
                                                    <th
                                                        class="px-3 py-2 font-medium text-left text-yellow-800 uppercase">
                                                        Expiry Date</th>
                                                    <th
                                                        class="px-3 py-2 font-medium text-left text-yellow-800 uppercase">
                                                        Days Remaining</th>
                                                    <th
                                                        class="px-3 py-2 font-medium text-center text-yellow-800 uppercase">
                                                        Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($shgbExpiringSoon as $soil)
                                                    @php
                                                        $daysRemaining = $soil->shgb_days_until_expiration;
                                                        $urgencyClass =
                                                            $daysRemaining <= 30
                                                                ? 'bg-red-100 text-red-800'
                                                                : ($daysRemaining <= 60
                                                                    ? 'bg-orange-100 text-orange-800'
                                                                    : 'bg-yellow-100 text-yellow-800');
                                                    @endphp
                                                    <tr class="hover:bg-yellow-50">
                                                        <td class="px-3 py-2">
                                                            <div class="font-medium text-gray-900">
                                                                {{ $soil->letak_tanah }}</div>
                                                            <div class="text-gray-500">{{ $soil->nama_penjual }}</div>
                                                        </td>
                                                        <td class="px-3 py-2 text-gray-600">
                                                            {{ $soil->land->lokasi_lahan ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                {{ $soil->businessUnit->code ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2 font-medium">
                                                            {{ $soil->shgb_expired_date->format('d/m/Y') }}
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $urgencyClass }}">
                                                                {{ $daysRemaining }} days
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2 text-center">
                                                            <a href="{{ route('soils.show', $soil->id) }}"
                                                                class="text-blue-600 hover:text-blue-900"
                                                                title="View Details">
                                                                <svg class="inline w-5 h-5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- Note for future additions --}}
                            <div class="p-3 mt-4 border border-gray-200 rounded-lg bg-gray-50">
                                <p class="flex items-center text-xs text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">Note:</span> More expiry types can be added here in the
                                    future (e.g. permits, licenses).
                                </p>
                            </div>
                        @else
                            <div class="py-8 text-center">
                                <svg class="w-10 h-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">All Clear!</h3>
                                <p class="mt-1 text-xs text-gray-500">No SHGB certificates expiring within the next 90
                                    days.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        let currentModule = '';

        function toggleMenu(menuId) {
            const allMenus = ['adem', 'risol', 'emas', 'dots', 'coms'];
            allMenus.forEach(id => {
                if (id !== menuId) {
                    const menu = document.getElementById(id + '-menu');
                    const icon = document.getElementById(id + '-icon');
                    if (menu && !menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                        icon.style.transform = 'rotate(0deg)';
                    }
                }
            });

            const menu = document.getElementById(menuId + '-menu');
            const icon = document.getElementById(menuId + '-icon');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                menu.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('button[onclick^="toggleMenu"]') && !e.target.closest('[id$="-menu"]')) {
                const allMenus = ['adem', 'risol', 'emas', 'dots', 'coms'];
                allMenus.forEach(id => {
                    const menu = document.getElementById(id + '-menu');
                    const icon = document.getElementById(id + '-icon');
                    if (menu && !menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                        icon.style.transform = 'rotate(0deg)';
                    }
                });
            }
        });

        function showBusinessUnitModal(module) {
            currentModule = module;
            const modal = document.getElementById('businessUnitModal');
            const title = document.getElementById('modalTitle');

            if (module === 'lands') {
                title.textContent = 'Select Business Unit for Lands';
            } else if (module === 'soils') {
                title.textContent = 'Select Business Unit for Soils';
            } else if (module === 'soils') {
                title.textContent = 'Select Business Unit for Soils';
            }

            modal.classList.remove('hidden');
        }

        function closeBusinessUnitModal() {
            document.getElementById('businessUnitModal').classList.add('hidden');
        }

        function navigateToModule(businessUnitId) {
            let url = '';

            if (businessUnitId === 'all') {
                if (currentModule === 'lands') {
                    url = '{{ route('lands') }}';
                } else if (currentModule === 'soils') {
                    url = '{{ route('soils') }}';
                } else if (currentModule === 'land-interest-rates') {
                    url = '{{ route('land-interest-rates') }}';
                }
            } else {
                if (currentModule === 'lands') {
                    url = '/lands/business-unit/' + businessUnitId;
                } else if (currentModule === 'soils') {
                    url = '/soils/business-unit/' + businessUnitId;
                } else if (currentModule === 'land-interest-rates') {
                    url = '/land-interest-rates/business-unit/' + businessUnitId;
                }
            }

            if (url) {
                window.location.href = url;
            }
        }

        function toggleSection(sectionId) {
            const content = document.getElementById(sectionId + '-content');
            const icon = document.getElementById(sectionId + '-icon');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        document.getElementById('businessUnitModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBusinessUnitModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBusinessUnitModal();
            }
        });

        function filterBusinessUnits() {
            const searchInput = document.getElementById('businessUnitSearch');
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const items = document.querySelectorAll('.business-unit-item');
            const noResults = document.getElementById('noResults');
            let visibleCount = 0;

            items.forEach(item => {
                const searchData = item.getAttribute('data-search');

                if (searchData && searchData.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (noResults) {
                if (visibleCount === 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }
        }
    </script>
</x-app-layout>
