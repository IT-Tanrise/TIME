<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 bg-gradient-to-r from-blue-50 to-indigo-50 overflow-visible">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Welcome, <?php echo e(auth()->user()->name); ?>!
                    </h1>
                    <!-- <p class="mt-6 text-gray-500 leading-relaxed">
                        Last login <?php echo e(auth()->user()->formatted_last_login); ?> 
                    </p> -->
                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Super Admin')): ?>
                    <div class="flex items-center justify-between mb-6">
                        <button 
                            onclick="toggleSection('rolePermission')" 
                            class="flex items-center space-x-2 text-left w-full focus:outline-none group">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Your role & permission
                            </h2>
                            <svg id="rolePermission-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                    <div id="rolePermission-content" class="hidden">
                        <p class="mt-6 text-gray-500 leading-relaxed">
                            You are logged in!<br>Your current role(s):
                            <?php $__currentLoopData = auth()->user()->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-1">
                                    <?php echo e($role->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </p>
                        <p class="mt-6 text-gray-500 leading-relaxed">
                            Your current permission(s):
                            <?php if(auth()->user()->getAllPermissions()->count() > 0): ?>
                                <?php $__currentLoopData = auth()->user()->getAllPermissions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-1 mt-2">
                                        <?php echo e($permission->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-1">No permissions available.</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Management Systems</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-3 pb-2">
                        
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['lands.access', 'soils.access', 'ownerships.access'])): ?>
                        <div class="relative bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            <button onclick="toggleMenu('adem')" class="w-full p-4 text-left flex items-center justify-between hover:bg-gray-50 rounded-t-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-blue-100 p-2 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">ADEM</h3>
                                        <p class="text-xs text-gray-500">Asset Database</p>
                                    </div>
                                </div>
                                <svg id="adem-icon" class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="adem-menu" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ownerships.access')): ?>
                                <a href="<?php echo e(route('partners.index')); ?>" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-t-lg">
                                    🏢 Ownerships
                                </a>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('lands.access')): ?>
                                <button onclick="showBusinessUnitModal('lands')" class="w-full text-left px-6 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    🏞️ Lands
                                </button>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('soils.access')): ?>
                                <button onclick="showBusinessUnitModal('soils')" class="w-full text-left px-6 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-b-lg">
                                    🌱 Soils
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        
                        <div class="relative bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            <button onclick="toggleMenu('risol')" class="w-full p-4 text-left flex items-center justify-between hover:bg-gray-50 rounded-t-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 p-2 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">RISOL</h3>
                                        <p class="text-xs text-gray-500">Rental Monitoring</p>
                                    </div>
                                </div>
                                <svg id="risol-icon" class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="risol-menu" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                <a href="<?php echo e(route('rents.lands')); ?>" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors rounded-t-lg">
                                    📋 Land Rentals
                                </a>
                                <a href="#" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors">
                                    💰 Payment Tracking
                                </a>
                                <a href="#" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors rounded-b-lg">
                                    📅 Rental Schedule
                                </a>
                            </div>
                        </div>

                        
                        <div class="relative bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            <button onclick="toggleMenu('emas')" class="w-full p-4 text-left flex items-center justify-between hover:bg-gray-50 rounded-t-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-yellow-100 p-2 rounded-lg">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">EMAS</h3>
                                        <p class="text-xs text-gray-500">Estate Management</p>
                                    </div>
                                </div>
                                <svg id="emas-icon" class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="emas-menu" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                <a href="#" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-600 transition-colors rounded-t-lg">
                                    🏢 Property List
                                </a>
                                <a href="#" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-600 transition-colors rounded-b-lg">
                                    🔧 Maintenance
                                </a>
                            </div>
                        </div>

                        
                        <div class="relative bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            <button onclick="toggleMenu('dots')" class="w-full p-4 text-left flex items-center justify-between hover:bg-gray-50 rounded-t-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-purple-100 p-2 rounded-lg">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">DOTS</h3>
                                        <p class="text-xs text-gray-500">Deposito Tracking</p>
                                    </div>
                                </div>
                                <svg id="dots-icon" class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="dots-menu" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                <a href="#" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-colors rounded-t-lg">
                                    💵 Deposits List
                                </a>
                                <a href="#" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-colors rounded-b-lg">
                                    📈 Interest Tracking
                                </a>
                            </div>
                        </div>

                        
                        <div class="relative bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            <button onclick="toggleMenu('coms')" class="w-full p-4 text-left flex items-center justify-between hover:bg-gray-50 rounded-t-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-red-100 p-2 rounded-lg">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">COMS</h3>
                                        <p class="text-xs text-gray-500">Construction Management</p>
                                    </div>
                                </div>
                                <svg id="coms-icon" class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="coms-menu" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                <a href="<?php echo e(route('projects')); ?>" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors rounded-t-lg">
                                    🗂️ Projects
                                </a>
                                <a href="#" class="block px-6 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors rounded-b-lg">
                                    👷 Contractors
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div id="businessUnitModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                    
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Select Business Unit</h3>
                        <button onclick="closeBusinessUnitModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    
                    <p class="text-sm text-gray-500 mb-4">Choose a business unit to view filtered data, or view all data</p>
                    
                    
                    <div class="mb-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="businessUnitSearch"
                            placeholder="Search by name or code..." 
                            oninput="filterBusinessUnits()"
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    
                    <div class="space-y-2 max-h-96 overflow-y-auto" id="businessUnitList">
                        <?php
                            $businessUnits = \App\Models\BusinessUnit::with('parent')->orderBy('name')->get();
                        ?>
                        
                        
                        <button onclick="navigateToModule('all')" class="business-unit-item w-full text-left px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200" data-search="view all data">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                <span class="font-semibold text-blue-900">View All Data</span>
                            </div>
                        </button>
                        
                        
                        <?php $__currentLoopData = $businessUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button onclick="navigateToModule(<?php echo e($unit->id); ?>)" class="business-unit-item w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200" data-search="<?php echo e(strtolower($unit->name . ' ' . $unit->code)); ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1 min-w-0">
                                    <svg class="w-5 h-5 text-gray-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-medium text-gray-900 block"><?php echo e($unit->name); ?></span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                <?php echo e($unit->code); ?>

                                            </span>
                                            <?php if($unit->parent): ?>
                                                <span class="text-xs text-gray-500 truncate">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                                    </svg>
                                                    <?php echo e($unit->parent->name); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    
                    <div id="noResults" class="hidden text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No business units found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms.</p>
                    </div>
                </div>
            </div>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['land-data.approval', 'soil-data.approval', 'soil-data-costs.approval'])): ?>
                <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white">
                        <div class="flex items-center justify-between mb-6">
                            <button 
                                onclick="toggleSection('pendingApprovals')" 
                                class="flex items-center space-x-2 text-left w-full focus:outline-none group">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Pending Approvals
                                </h2>
                                <svg id="pendingApprovals-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="flex items-center space-x-2">
                                <?php
                                    $totalLandPending = 0;
                                    $totalSoilPending = 0;
                                    
                                    if(auth()->user()->can('land-data.approval')) {
                                        $totalLandPending = App\Models\LandApproval::pending()->count();
                                    }
                                    
                                    if(auth()->user()->can('soil-data.approval')) {
                                        $totalSoilPending += App\Models\SoilApproval::pending()->whereIn('change_type', ['details', 'delete', 'create'])->count();
                                    }
                                    if(auth()->user()->can('soil-data-costs.approval')) {
                                        $totalSoilPending += App\Models\SoilApproval::pending()->where('change_type', 'costs')->count();
                                    }
                                ?>
                                
                                <?php if($totalLandPending > 0): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800"><?php echo e($totalLandPending); ?> Land</span>
                                <?php endif; ?>
                                <?php if($totalSoilPending > 0): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><?php echo e($totalSoilPending); ?> Soil</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div id="pendingApprovals-content" class="hidden">
                            <?php if($totalLandPending + $totalSoilPending > 0): ?>
                                <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                You have approval permissions. Changes require your review before being applied.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('merged-approvals', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-476731992-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <button 
                            onclick="toggleSection('recentActivity')" 
                            class="flex items-center space-x-2 text-left w-full focus:outline-none group">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Recent Approval Activity
                            </h2>
                            <svg id="recentActivity-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    
                    <div id="recentActivity-content" class="hidden">
                        <?php
                            // Get recent soil approval activities
                            $recentSoilApprovals = App\Models\SoilApproval::with(['soil.land', 'soil.businessUnit', 'requestedBy', 'approvedBy'])
                                ->whereIn('status', ['approved', 'rejected'])
                                ->where('updated_at', '>=', now()->subDays(30))
                                ->latest('updated_at')
                                ->limit(5)
                                ->get();
                                
                            // Get recent land approval activities
                            $recentLandApprovals = App\Models\LandApproval::with(['land', 'requestedBy', 'approvedBy'])
                                ->whereIn('status', ['approved', 'rejected'])
                                ->where('updated_at', '>=', now()->subDays(30))
                                ->latest('updated_at')
                                ->limit(5)
                                ->get();
                                
                            // Merge and sort by updated_at
                            $recentApprovals = $recentSoilApprovals->concat($recentLandApprovals)
                                ->sortByDesc('updated_at')
                                ->take(10);
                                
                            // Get user's pending approvals
                            $userPendingSoilApprovals = App\Models\SoilApproval::with(['soil.land', 'soil.businessUnit'])
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
                                
                            $userPendingApprovals = $userPendingSoilApprovals->concat($userPendingLandApprovals)
                                ->sortByDesc('created_at');
                        ?>

                        <?php if($userPendingApprovals->count() > 0): ?>
                            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">
                                            Your Pending Approvals (<?php echo e($userPendingApprovals->count()); ?>)
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <?php $__currentLoopData = $userPendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <?php if($approval instanceof App\Models\LandApproval): ?>
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 mr-1">
                                                                LAND
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mr-1">
                                                                SOIL
                                                            </span>
                                                        <?php endif; ?>
                                                        
                                                        <?php if($approval->change_type === 'create'): ?>
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mr-1">
                                                                CREATE
                                                            </span>
                                                        <?php elseif($approval->change_type === 'delete'): ?>
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mr-1">
                                                                DELETE
                                                            </span>
                                                        <?php endif; ?>
                                                        
                                                        <span class="font-medium text-gray-900">
                                                            <?php if($approval instanceof App\Models\LandApproval): ?>
                                                                <?php echo e($approval->land->lokasi_lahan ?? 'Land Record'); ?>

                                                            <?php else: ?>
                                                                <?php echo e($approval->soil->nama_penjual ?? 'Soil Record'); ?>

                                                            <?php endif; ?>
                                                        </span>
                                                        (<?php echo e($approval->created_at->diffForHumans()); ?>)
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($recentApprovals->count() > 0): ?>
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <?php $__currentLoopData = $recentApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <div class="relative pb-8">
                                                <?php if($index < $recentApprovals->count() - 1): ?>
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                <?php endif; ?>
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <?php if($approval->status === 'approved'): ?>
                                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                <?php if($approval instanceof App\Models\LandApproval): ?>
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 mr-1">
                                                                        LAND
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mr-1">
                                                                        SOIL
                                                                    </span>
                                                                <?php endif; ?>
                                                                
                                                                <?php if($approval->change_type === 'delete'): ?>
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mr-1">
                                                                        DELETE
                                                                    </span>
                                                                <?php elseif($approval->change_type === 'create'): ?>
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mr-1">
                                                                        CREATE
                                                                    </span>
                                                                <?php endif; ?>
                                                                
                                                                <span class="font-medium text-gray-900">
                                                                    <?php if($approval instanceof App\Models\LandApproval): ?>
                                                                        <?php echo e($approval->land->lokasi_lahan ?? 'Deleted Land Record'); ?>

                                                                    <?php else: ?>
                                                                        <?php echo e($approval->soil->nama_penjual ?? 'Deleted Soil Record'); ?>

                                                                    <?php endif; ?>
                                                                </span>
                                                                
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?php echo e($approval->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                                                    <?php echo e(ucfirst($approval->status)); ?>

                                                                </span>
                                                            </p>
                                                            <p class="text-xs text-gray-400 mt-1">
                                                                Requested by: <?php echo e($approval->requestedBy->name ?? 'Unknown'); ?>

                                                            </p>
                                                            <?php if($approval->status === 'approved'): ?>
                                                                <p class="text-xs text-green-600 mt-1">
                                                                    Approved by: <?php echo e($approval->approvedBy->name ?? 'Unknown'); ?>

                                                                    <?php if($approval->reason): ?>
                                                                        - <?php echo e(Str::limit($approval->reason, 50)); ?>

                                                                    <?php endif; ?>
                                                                </p>
                                                            <?php elseif($approval->status === 'rejected'): ?>
                                                                <p class="text-xs text-red-600 mt-1">
                                                                    Rejected by: <?php echo e($approval->approvedBy->name ?? 'Unknown'); ?>

                                                                    <?php if($approval->reason): ?>
                                                                        - <?php echo e(Str::limit($approval->reason, 50)); ?>

                                                                    <?php endif; ?>
                                                                </p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                            <time datetime="<?php echo e($approval->updated_at->toISOString()); ?>">
                                                                <?php echo e($approval->updated_at->diffForHumans()); ?>

                                                            </time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            
                            <div class="mt-6 text-center space-x-2">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('land-data.approval')): ?>
                                    <a href="<?php echo e(route('land-approvals')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                        View All Land Approvals
                                        <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['soil-data.approval', 'soil-data-costs.approval'])): ?>
                                    <a href="<?php echo e(route('soil-approvals')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        View All Soil Approvals
                                        <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                        <?php else: ?>
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No recent approval activity</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    No approval requests have been processed recently.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        let currentModule = '';

        // Toggle main menu dropdowns
        function toggleMenu(menuId) {
            // Close all other menus first
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

            // Toggle the clicked menu
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

        // Close dropdown when clicking outside
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

        // Show business unit selection modal
        function showBusinessUnitModal(module) {
            currentModule = module;
            const modal = document.getElementById('businessUnitModal');
            const title = document.getElementById('modalTitle');
            
            if (module === 'lands') {
                title.textContent = 'Select Business Unit for Lands';
            } else if (module === 'soils') {
                title.textContent = 'Select Business Unit for Soils';
            }
            
            modal.classList.remove('hidden');
        }

        // Close business unit modal
        function closeBusinessUnitModal() {
            document.getElementById('businessUnitModal').classList.add('hidden');
        }

        // Navigate to selected module with business unit
        function navigateToModule(businessUnitId) {
            let url = '';
            
            if (businessUnitId === 'all') {
                if (currentModule === 'lands') {
                    url = '<?php echo e(route("lands")); ?>';
                } else if (currentModule === 'soils') {
                    url = '<?php echo e(route("soils")); ?>';
                }
            } else {
                if (currentModule === 'lands') {
                    url = '/lands/business-unit/' + businessUnitId;
                } else if (currentModule === 'soils') {
                    url = '/soils/business-unit/' + businessUnitId;
                }
            }
            
            if (url) {
                window.location.href = url;
            }
        }

        // Toggle collapsible sections
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

        // Close modal when clicking outside
        document.getElementById('businessUnitModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBusinessUnitModal();
            }
        });
        
        // Keyboard support
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

            // Show/hide no results message
            if (noResults) {
                if (visibleCount === 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\xampp\tanrise-portal2\resources\views/dashboard.blade.php ENDPATH**/ ?>