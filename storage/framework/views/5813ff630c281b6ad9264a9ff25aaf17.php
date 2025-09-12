<div class="container mx-auto px-4 py-6">
    
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

    
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Business Units</h2>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('business-units.edit')): ?>
                <button wire:click="showCreate" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add New Business Unit
                </button>
                <?php endif; ?>
            </div>
            
            
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input wire:model.live="search" type="text" placeholder="Search by name or code..." 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                
                <div class="flex gap-2">
                    <select wire:model.live="parentFilter" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Business Units</option>
                        <option value="root">Root Units Only</option>
                        <!--[if BLOCK]><![endif]--><?php if(isset($parentOptions)): ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $parentOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($parent->id); ?>">
                                    <?php echo e($parent->name); ?> (<?php echo e($parent->code); ?>)
                                    <!--[if BLOCK]><![endif]--><?php if($parent->children->count() > 0): ?>
                                        - <?php echo e($parent->children->count()); ?> children
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
            </div>

            
            <!--[if BLOCK]><![endif]--><?php if($parentFilter): ?>
                <div class="mt-3 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
                            </svg>
                            <span class="text-sm text-blue-700">
                                <!--[if BLOCK]><![endif]--><?php if($parentFilter === 'root'): ?>
                                    <strong>Filter Active:</strong> Showing root units only
                                <?php else: ?>
                                    <?php
                                        $selectedParent = App\Models\BusinessUnit::find($parentFilter);
                                    ?>
                                    <!--[if BLOCK]><![endif]--><?php if($selectedParent): ?>
                                        <strong>Filter Active:</strong> Showing <?php echo e($selectedParent->name); ?> and its descendants
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </span>
                        </div>
                        <button wire:click="$set('parentFilter', '')" 
                                class="text-blue-600 hover:text-blue-800 text-sm underline">
                            Clear filter
                        </button>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $businessUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="relative bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        
                        <div class="absolute top-1 right-1 z-10">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.away="open = false"
                                        class="p-1 rounded-full hover:bg-gray-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>
                                
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <button wire:click="showDetail(<?php echo e($unit->id); ?>)" @click="open = false"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View Details
                                        </button>
                                        <button wire:click="showEdit(<?php echo e($unit->id); ?>)" @click="open = false"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <!--[if BLOCK]><![endif]--><?php if($unit->children->count() > 0): ?>
                                            <button wire:click="$set('parentFilter', <?php echo e($unit->id); ?>)" @click="open = false"
                                                    class="flex items-center w-full px-4 py-2 text-sm text-purple-700 hover:bg-purple-50">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
                                                </svg>
                                                Filter by This Unit
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <button wire:click="delete(<?php echo e($unit->id); ?>)" @click="open = false"
                                                onclick="return confirm('Are you sure you want to delete this business unit? This action cannot be undone.')"
                                                class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="p-4 cursor-pointer" wire:click="showDetail(<?php echo e($unit->id); ?>)">
                            
                            <h3 class="text-md font-semibold text-gray-900 text-center mb-2"><?php echo e($unit->name); ?></h3>

                            
                            <div class="text-center mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo e($unit->code); ?>

                                </span>
                            </div>

                            
                            <div class="text-center space-y-1">
                                <!--[if BLOCK]><![endif]--><?php if($unit->parent): ?>
                                    <div class="text-xs text-gray-500">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                        </svg>
                                        <?php echo e($unit->parent->name); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!--[if BLOCK]><![endif]--><?php if($unit->children->count() > 0): ?>
                                    <div class="flex items-center justify-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                            </svg>
                                            <?php echo e($unit->children->count()); ?> children
                                        </span>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                
                                <!--[if BLOCK]><![endif]--><?php if($parentFilter && $parentFilter !== 'root'): ?>
                                    <div class="text-xs text-gray-400 mt-1 truncate" title="<?php echo e($unit->hierarchy_path); ?>">
                                        <?php echo e($unit->hierarchy_path); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <!--[if BLOCK]><![endif]--><?php if($search || $parentFilter): ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                <?php else: ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </svg>
                            <!--[if BLOCK]><![endif]--><?php if($search || $parentFilter): ?>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No business units found</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    No business units match your current search criteria.
                                    <!--[if BLOCK]><![endif]--><?php if($search && $parentFilter): ?>
                                        Try adjusting your search terms or filter.
                                    <?php elseif($search): ?>
                                        Try adjusting your search terms.
                                    <?php else: ?>
                                        Try selecting a different parent filter.
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </p>
                                <div class="mt-6 flex justify-center gap-3">
                                    <!--[if BLOCK]><![endif]--><?php if($search): ?>
                                        <button wire:click="$set('search', '')" class="text-blue-600 hover:text-blue-800 text-sm underline">
                                            Clear search
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($parentFilter): ?>
                                        <button wire:click="$set('parentFilter', '')" class="text-blue-600 hover:text-blue-800 text-sm underline">
                                            Clear filter
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php else: ?>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No business units found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new business unit.</p>
                                <div class="mt-6">
                                    <button wire:click="showCreate" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Add Your First Business Unit
                                    </button>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($businessUnits->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <?php echo e($businessUnits->links()); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>


<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<?php $__env->stopPush(); ?><?php /**PATH C:\xampp\tanrise-portal2\resources\views/livewire/business-unit/index.blade.php ENDPATH**/ ?>