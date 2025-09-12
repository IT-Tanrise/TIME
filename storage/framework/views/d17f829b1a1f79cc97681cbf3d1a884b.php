
<div>
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('message')); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php if(session()->has('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
        <?php echo $__env->make('livewire.lands.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($showDetailForm): ?>
        <?php echo $__env->make('livewire.lands.show', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php else: ?>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            Lands
                            <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?>
                                <span class="text-lg text-blue-600">- <?php echo e($businessUnit->name); ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </h2>
                        <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?>
                            <p class="text-sm text-gray-600 mt-1">
                                Showing lands for business unit: <a href ="<?php echo e(route('business-units', ['view' => 'show', 'id' => $businessUnit->id])); ?>"><strong><?php echo e($businessUnit->name); ?></strong></a> (<?php echo e($businessUnit->code); ?>)
                            </p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>    
                    <div class="flex space-x-3">
                        <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?>
                            <button wire:click="clearBusinessUnitFilter" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Show All Lands
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <button wire:click="showCreateForm" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Land Record
                        </button>
                    </div>
                </div>

                <!-- Filters and Search -->
                <!--[if BLOCK]><![endif]--><?php if(!$this->isFiltered()): ?>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" 
                               wire:model.live="search" 
                               id="search"
                               placeholder="Search by location, city, status..."
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <!-- Business Unit Filter -->
                    <div>
                        <label for="filterBusinessUnit" class="block text-sm font-medium text-gray-700">Business Unit</label>
                        <select wire:model.live="filterBusinessUnit" 
                                id="filterBusinessUnit"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Business Units</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $businessUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($unit->id); ?>"><?php echo e($unit->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="filterStatus" class="block text-sm font-medium text-gray-700">Status</label>
                        <select wire:model.live="filterStatus" 
                                id="filterStatus"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>"><?php echo e($status); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
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

                <!-- City/Regency Filter (on second row) -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="filterKotaKabupaten" class="block text-sm font-medium text-gray-700">City/Regency</label>
                        <select wire:model.live="filterKotaKabupaten" 
                                id="filterKotaKabupaten"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Cities/Regencies</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $kotaKabupaten; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($kota); ?>"><?php echo e($kota); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                    <!-- Empty columns to maintain layout -->
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <?php else: ?>
                <div class="mt-4 flex justify-between items-center">
                    <input wire:model.live="search" 
                           type="text" 
                           placeholder="Search by location, city, status..." 
                           class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?>
                        <div class="ml-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                Filtered by: <?php echo e($businessUnit->name); ?>

                            </span>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="p-6">
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Units</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City/Regency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acquisition Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Soil Area</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Price/m²</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Related</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $lands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $land): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($land->lokasi_lahan); ?></div>
                                        <!--[if BLOCK]><![endif]--><?php if($land->alamat): ?>
                                            <div class="text-sm text-gray-500"><?php echo e(Str::limit($land->alamat, 50)); ?></div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="px-6 py-4">
                                        <!--[if BLOCK]><![endif]--><?php if($land->business_units_count > 0): ?>
                                            <div class="text-sm text-gray-900">
                                                <!--[if BLOCK]><![endif]--><?php if($land->business_units_count == 1): ?>
                                                    <span class="font-medium"><?php echo e($land->business_unit_names); ?></span>
                                                <?php else: ?>
                                                    <div class="space-y-1">
                                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $land->soils->pluck('businessUnit')->filter()->unique('id')->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mr-1 mb-1">
                                                                <?php echo e($unit->name); ?>

                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <!--[if BLOCK]><![endif]--><?php if($land->business_units_count > 2): ?>
                                                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                                                +<?php echo e($land->business_units_count - 2); ?> more
                                                            </div>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-400">No business units</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($land->kota_kabupaten ?? '-'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($land->tahun_perolehan); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($land->formatted_nilai_perolehan); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <!--[if BLOCK]><![endif]--><?php if($land->total_soil_area > 0): ?>
                                            <?php echo e($land->formatted_total_soil_area); ?>

                                        <?php else: ?>
                                            <span class="text-gray-400">No soils</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <!--[if BLOCK]><![endif]--><?php if($land->total_soil_area > 0): ?>
                                            <?php echo e($land->formatted_average_price_per_m2); ?>

                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php if($land->status === 'Available'): ?> bg-green-100 text-green-800
                                            <?php elseif($land->status === 'Reserved'): ?> bg-yellow-100 text-yellow-800
                                            <?php elseif($land->status === 'Sold'): ?> bg-red-100 text-red-800
                                            <?php elseif($land->status === 'Development'): ?> bg-blue-100 text-blue-800
                                            <?php else: ?> bg-gray-100 text-gray-800
                                            <?php endif; ?>">
                                            <?php echo e($land->status); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex space-x-1">
                                            <!--[if BLOCK]><![endif]--><?php if($land->soils_count > 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <?php echo e($land->soils_count); ?> Soils
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($land->projects_count > 0): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <?php echo e($land->projects_count); ?> Projects
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button wire:click="showDetail(<?php echo e($land->id); ?>)" 
                                                    class="text-blue-600 hover:text-blue-900">View</button>
                                            <button wire:click="showEditForm(<?php echo e($land->id); ?>)" 
                                                    class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                            <button wire:click="delete(<?php echo e($land->id); ?>)" 
                                                    wire:confirm="Are you sure you want to delete this land?" 
                                                    class="text-red-600 hover:text-red-900">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="10" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <!--[if BLOCK]><![endif]--><?php if($this->isFiltered()): ?>
                                            No lands found for business unit "<?php echo e($this->getCurrentBusinessUnitName()); ?>".
                                        <?php elseif($search || $filterBusinessUnit || $filterStatus || $filterKotaKabupaten): ?>
                                            No lands found matching the selected filters.
                                        <?php else: ?>
                                            No lands found.
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                
                <!--[if BLOCK]><![endif]--><?php if($lands->hasPages()): ?>
                    <div class="mt-4">
                        <?php echo e($lands->links()); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\xampp\tanrise-portal2\resources\views/livewire/lands/index.blade.php ENDPATH**/ ?>