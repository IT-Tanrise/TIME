
<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-4 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    <?php echo e($isEdit ? 'Edit Soil Record Details' : 'Create New Soil Records'); ?>

                </h2>
                <button wire:click="backToIndex" 
                        class="inline-flex items-center px-3 py-1.5 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </button>
            </div>

            <!-- Form -->
            <form wire:submit="save">
                <div class="space-y-4">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Land with Dropdown Search -->
                            <div class="relative">
                                <label for="landSearch" class="block text-xs font-medium text-gray-700">Land *</label>
                                <input type="text" 
                                       wire:model.live.debounce.300ms="landSearch"
                                       wire:focus="searchLands"
                                       id="landSearch"
                                       placeholder="Search or select land..."
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs <?php $__errorArgs = ['land_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       autocomplete="off">
                                
                                <!--[if BLOCK]><![endif]--><?php if($showLandDropdown): ?>
                                    <div class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-xs ring-1 ring-black ring-opacity-5 focus:outline-none"
                                         style="max-height: 240px; overflow-y: auto; overflow-x: hidden;"
                                         wire:click.stop>
                                        <?php
                                            $lands = $this->getFilteredLands();
                                        ?>
                                        
                                        <!--[if BLOCK]><![endif]--><?php if(empty($landSearch ?? '') && $lands->count() > 0): ?>
                                            <div class="px-3 py-1.5 text-xs text-blue-600 bg-blue-50 border-b border-blue-100 sticky top-0 z-10">
                                                Showing available lands - start typing to filter
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        
                                        <div class="max-h-48 overflow-y-auto">
                                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $lands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $land): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <button type="button"
                                                        wire:click.stop="selectLand(<?php echo e($land->id); ?>, '<?php echo e(addslashes($land->lokasi_lahan)); ?>')"
                                                        class="w-full text-left px-3 py-1.5 text-xs text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                                                    <?php echo e($land->lokasi_lahan); ?>

                                                </button>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <div class="px-3 py-1.5 text-xs text-gray-500">No lands found</div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        
                                        <!--[if BLOCK]><![endif]--><?php if($lands->count() >= 20): ?>
                                            <div class="px-3 py-1.5 text-xs text-gray-500 bg-gray-50 border-t border-gray-100 sticky bottom-0">
                                                Showing first 20 results - type to search for more
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['land_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <!-- Business Unit with Dropdown Search -->
                            <div class="relative">
                                <label for="businessUnitSearch" class="block text-xs font-medium text-gray-700">Business Unit *</label>
                                <input type="text" 
                                    wire:model.live.debounce.300ms="businessUnitSearch"
                                    wire:focus="searchBusinessUnits"
                                    id="businessUnitSearch"
                                    placeholder="Search or select business unit..."
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs <?php $__errorArgs = ['business_unit_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    autocomplete="off"
                                    <?php if($filterByBusinessUnit && !$allowBusinessUnitChange): ?> readonly <?php endif; ?>>
                                
                                <!--[if BLOCK]><![endif]--><?php if($showBusinessUnitDropdown && (!$filterByBusinessUnit || $allowBusinessUnitChange)): ?>
                                    <div class="absolute z-40 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-xs ring-1 ring-black ring-opacity-5 focus:outline-none"
                                         style="max-height: 240px; overflow-y: auto; overflow-x: hidden;"
                                         wire:click.stop>
                                        <?php
                                            $businessUnits = $this->getFilteredBusinessUnits();
                                        ?>
                                        
                                        <!--[if BLOCK]><![endif]--><?php if(empty($businessUnitSearch ?? '') && $businessUnits->count() > 0): ?>
                                            <div class="px-3 py-1.5 text-xs text-blue-600 bg-blue-50 border-b border-blue-100 sticky top-0 z-10">
                                                Showing available business units - start typing to filter
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        
                                        <div class="max-h-48 overflow-y-auto">
                                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $businessUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <button type="button"
                                                        wire:click.stop="selectBusinessUnit(<?php echo e($unit->id); ?>, '<?php echo e(addslashes($unit->name)); ?>')"
                                                        class="w-full text-left px-3 py-1.5 text-xs text-gray-900 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none
                                                            <?php if($business_unit_id == $unit->id): ?> bg-blue-50 text-blue-900 font-medium <?php endif; ?>">
                                                    <?php echo e($unit->name); ?>

                                                    <!--[if BLOCK]><![endif]--><?php if($business_unit_id == $unit->id): ?>
                                                        <span class="float-right text-blue-600">✓</span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </button>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <div class="px-3 py-1.5 text-xs text-gray-500">No business units found</div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        
                                        <!--[if BLOCK]><![endif]--><?php if($businessUnits->count() >= 20): ?>
                                            <div class="px-3 py-1.5 text-xs text-gray-500 bg-gray-50 border-t border-gray-100 sticky bottom-0">
                                                Showing first 20 results - type to search for more
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <!--[if BLOCK]><![endif]--><?php if($filterByBusinessUnit && !$allowBusinessUnitChange): ?>
                                    <p class="mt-1 text-xs text-green-600">
                                        Pre-selected based on current filter
                                        <button type="button" 
                                                wire:click="allowBusinessUnitChangeFunc" 
                                                class="ml-2 text-blue-600 underline hover:text-blue-800">
                                            Change
                                        </button>
                                    </p>
                                <?php elseif($filterByBusinessUnit && $allowBusinessUnitChange): ?>
                                    <p class="mt-1 text-xs text-amber-600">
                                        You can now change the business unit
                                        <button type="button" 
                                                wire:click="lockBusinessUnit" 
                                                class="ml-2 text-green-600 underline hover:text-green-800">
                                            Keep Current
                                        </button>
                                    </p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['business_unit_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>

                    <!-- Soil Details Section -->
                    <div class="bg-green-50 p-3 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-medium text-gray-900">Soil Details</h3>
                            <!--[if BLOCK]><![endif]--><?php if(!$isEdit): ?>
                            <button type="button" 
                                    wire:click="addSoilDetail"
                                    class="inline-flex items-center px-2.5 py-1.5 bg-green-600 border border-transparent rounded-md text-xs font-semibold text-white hover:bg-green-700">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Soil Detail
                            </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if(is_array($soilDetails) && count($soilDetails) > 0): ?>
                            <div class="space-y-4">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $soilDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="border border-gray-200 rounded-lg p-3 space-y-3">
                                        <div class="flex justify-between items-center">
                                            <h4 class="text-xs font-medium text-gray-800">
                                                Soil Detail <?php echo e($index + 1); ?>

                                            </h4>
                                            <!--[if BLOCK]><![endif]--><?php if($index > 0 && !$isEdit): ?>
                                                <button type="button" wire:click="removeSoilDetail(<?php echo e($index); ?>)" 
                                                        class="text-red-600 hover:text-red-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>

                                        <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                                            <!-- Seller Name -->
                                            <div class="relative">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Seller Name *</label>
                                                <input wire:model.live="sellerNameSearch.<?php echo e($index); ?>" 
                                                    wire:focus="searchSellerNames(<?php echo e($index); ?>)"
                                                    type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.nama_penjual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                
                                                <!--[if BLOCK]><![endif]--><?php if(isset($showSellerNameDropdown[$index]) && $showSellerNameDropdown[$index]): ?>
                                                    <div class="absolute z-30 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg"
                                                         style="max-height: 200px; overflow-y: auto; overflow-x: hidden;"
                                                         wire:click.stop>
                                                        <div class="max-h-48 overflow-y-auto">
                                                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $this->getFilteredSellerNames($index); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                <button type="button"
                                                                        wire:click="selectSellerName(<?php echo e($index); ?>, '<?php echo e(addslashes($seller->nama_penjual)); ?>')" 
                                                                        class="w-full text-left px-2 py-1.5 hover:bg-gray-100 cursor-pointer text-xs focus:bg-gray-100 focus:outline-none">
                                                                    <?php echo e($seller->nama_penjual); ?>

                                                                </button>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                <div class="px-2 py-1.5 text-xs text-gray-500">No sellers found</div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.nama_penjual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Seller Address -->
                                            <div class="relative">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Seller Address *</label>
                                                <input wire:model.live="sellerAddressSearch.<?php echo e($index); ?>" 
                                                    wire:focus="searchSellerAddresses(<?php echo e($index); ?>)"
                                                    type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.alamat_penjual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                
                                                <!--[if BLOCK]><![endif]--><?php if(isset($showSellerAddressDropdown[$index]) && $showSellerAddressDropdown[$index]): ?>
                                                    <div class="absolute z-30 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg"
                                                         style="max-height: 200px; overflow-y: auto; overflow-x: hidden;"
                                                         wire:click.stop>
                                                        <div class="max-h-48 overflow-y-auto">
                                                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $this->getFilteredSellerAddresses($index); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                <button type="button"
                                                                        wire:click="selectSellerAddress(<?php echo e($index); ?>, '<?php echo e(addslashes($seller->alamat_penjual)); ?>')" 
                                                                        class="w-full text-left px-2 py-1.5 hover:bg-gray-100 cursor-pointer text-xs focus:bg-gray-100 focus:outline-none">
                                                                    <?php echo e($seller->alamat_penjual); ?>

                                                                </button>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                <div class="px-2 py-1.5 text-xs text-gray-500">No addresses found</div>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.alamat_penjual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- PPJB Number -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">PPJB/AJB Number *</label>
                                                <input wire:model="soilDetails.<?php echo e($index); ?>.nomor_ppjb" type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.nomor_ppjb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.nomor_ppjb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- PPJB Date -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">PPJB/ AJB Date *</label>
                                                <input wire:model="soilDetails.<?php echo e($index); ?>.tanggal_ppjb" type="date" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.tanggal_ppjb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.tanggal_ppjb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Land Location -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Land Location *</label>
                                                <input wire:model="soilDetails.<?php echo e($index); ?>.letak_tanah" type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.letak_tanah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.letak_tanah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Area (FIXED: Better number formatting) -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Area (m²) *</label>
                                                <input 
                                                    wire:model.live="soilDetails.<?php echo e($index); ?>.luas_display" 
                                                    type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.luas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    placeholder="Enter area"
                                                    x-data="{
                                                        formatNumber(value) {
                                                            // Remove all non-numeric characters
                                                            let numericValue = value.replace(/[^\d]/g, '');
                                                            if (numericValue) {
                                                                // Format with thousand separators using Indonesian format (dot as thousand separator)
                                                                return new Intl.NumberFormat('id-ID').format(parseInt(numericValue));
                                                            }
                                                            return '';
                                                        }
                                                    }"
                                                    x-on:input="
                                                        let rawValue = $event.target.value;
                                                        let numericValue = rawValue.replace(/[^\d]/g, '');
                                                        if (numericValue) {
                                                            let formattedValue = new Intl.NumberFormat('id-ID').format(parseInt(numericValue));
                                                            $event.target.value = formattedValue;
                                                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('soilDetails.<?php echo e($index); ?>.luas', parseInt(numericValue));
                                                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('soilDetails.<?php echo e($index); ?>.luas_display', formattedValue);
                                                        } else {
                                                            $event.target.value = '';
                                                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('soilDetails.<?php echo e($index); ?>.luas', '');
                                                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('soilDetails.<?php echo e($index); ?>.luas_display', '');
                                                        }
                                                    "
                                                >
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.luas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Price (FIXED: Better number formatting) -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Price (Rp) *</label>
                                                <input 
                                                    wire:model.live="soilDetails.<?php echo e($index); ?>.harga_display" 
                                                    type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    placeholder="Enter price"
                                                    x-data="{
                                                        formatNumber(value) {
                                                            // Remove all non-numeric characters
                                                            let numericValue = value.replace(/[^\d]/g, '');
                                                            if (numericValue) {
                                                                // Format with thousand separators using Indonesian format (dot as thousand separator)
                                                                return new Intl.NumberFormat('id-ID').format(parseInt(numericValue));
                                                            }
                                                            return '';
                                                        }
                                                    }"
                                                    x-on:input="
                                                        let rawValue = $event.target.value;
                                                        let numericValue = rawValue.replace(/[^\d]/g, '');
                                                        if (numericValue) {
                                                            let formattedValue = new Intl.NumberFormat('id-ID').format(parseInt(numericValue));
                                                            $event.target.value = formattedValue;
                                                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('soilDetails.<?php echo e($index); ?>.harga', parseInt(numericValue));
                                                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('soilDetails.<?php echo e($index); ?>.harga_display', formattedValue);
                                                        } else {
                                                            $event.target.value = '';
                                                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('soilDetails.<?php echo e($index); ?>.harga', '');
                                                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('soilDetails.<?php echo e($index); ?>.harga_display', '');
                                                        }
                                                    "
                                                >
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Ownership Proof -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Ownership Proof *</label>
                                                <select wire:model="soilDetails.<?php echo e($index); ?>.bukti_kepemilikan" 
                                                        class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.bukti_kepemilikan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                    <option value="">Select Ownership Proof</option>
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->getBuktiKepemilikanOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </select>
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.bukti_kepemilikan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Ownership Proof Details -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Ownership Proof Details</label>
                                                <input wire:model="soilDetails.<?php echo e($index); ?>.bukti_kepemilikan_details" type="text" 
                                                    placeholder="Certificate number, etc."
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.bukti_kepemilikan_details'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.bukti_kepemilikan_details'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Owner Name -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Owner Name *</label>
                                                <input wire:model="soilDetails.<?php echo e($index); ?>.atas_nama" type="text" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.atas_nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.atas_nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- NOP PBB -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">NOP PBB</label>
                                                <input wire:model="soilDetails.<?php echo e($index); ?>.nop_pbb" type="text" 
                                                    placeholder="Enter NOP PBB number"
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.nop_pbb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.nop_pbb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Nama Notaris/PPAT -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Notaris/PPAT</label>
                                                <input wire:model="soilDetails.<?php echo e($index); ?>.nama_notaris_ppat" type="text" 
                                                    placeholder="Enter Notaris/PPAT name"
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.nama_notaris_ppat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.nama_notaris_ppat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>

                                        <!-- Notes (full width) -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Notes *</label>
                                            <textarea wire:model="soilDetails.<?php echo e($index); ?>.keterangan" rows="2" 
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs <?php $__errorArgs = ['soilDetails.'.$index.'.keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['soilDetails.'.$index.'.keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                <span class="text-red-500 text-xs"><?php echo e($message); ?></span> 
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-2">
                        <button type="button" 
                                wire:click="backToIndex"
                                class="px-3 py-1.5 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray active:bg-gray-400 transition ease-in-out duration-150">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-600 transition ease-in-out duration-150">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <?php echo e($isEdit ? 'Update' : 'Create'); ?> 
                            <!--[if BLOCK]><![endif]--><?php if(!$isEdit && count($soilDetails) > 1): ?>
                                (<?php echo e(count($soilDetails)); ?> Records)
                            <?php else: ?>
                                Soil Record
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Click outside to close dropdowns -->
<script>
document.addEventListener('click', function(event) {
    // Check if click is outside any dropdown
    if (!event.target.closest('.relative')) {
        window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('closeDropdowns');
    }
});
</script>

<!-- Custom Styles for Scrollable Dropdowns -->
<style>
/* Ensure proper scrolling for dropdown containers */
.dropdown-container {
    max-height: 240px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

/* Custom scrollbar styling for better UX */
.dropdown-container::-webkit-scrollbar {
    width: 6px;
}

.dropdown-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.dropdown-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.dropdown-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Force scrolling on all dropdown containers */
[style*="max-height"][style*="overflow-y: auto"] {
    scrollbar-width: thin;
    scrollbar-color: #c1c1c1 #f1f1f1;
}

/* Ensure dropdown items don't break layout */
.dropdown-item {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Improved number input styling */
input[type="text"]:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Better visual feedback for formatted numbers */
input[wire\:model*="display"] {
    font-family: 'Monaco', 'Menlo', 'Consolas', monospace;
    letter-spacing: 0.025em;
}
</style><?php /**PATH C:\xampp\tanrise-portal2\resources\views/livewire/soils/form.blade.php ENDPATH**/ ?>