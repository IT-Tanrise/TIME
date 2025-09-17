
<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">
                        Manage Additional Costs
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Soil Record: <?php echo e($soil->nomor_ppjb); ?> - <?php echo e($soil->nama_penjual); ?>

                    </p>
                </div>
                <button wire:click="backToIndex" 
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-600 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </button>
            </div>

            <!-- Basic Record Info -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Record Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Land Price:</span>
                        <span class="text-gray-900"><?php echo e($soil->formatted_harga); ?></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Area:</span>
                        <span class="text-gray-900"><?php echo e(number_format($soil->luas, 0, ',', '.')); ?> m²</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Location:</span>
                        <span class="text-gray-900"><?php echo e($soil->letak_tanah); ?></span>
                    </div>
                </div>
            </div>

            <!-- Additional Costs Form -->
            <form wire:submit="saveAdditionalCosts">
                <div class="space-y-6">
                    <!-- Additional Costs Section -->
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Additional Costs</h3>
                            <button type="button" 
                                    wire:click="addBiayaTambahan"
                                    class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md text-sm font-semibold text-white hover:bg-green-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Cost
                            </button>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if(is_array($biayaTambahan) && count($biayaTambahan) > 0): ?>
                            <div class="space-y-4">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $biayaTambahan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $biaya): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-white p-4 rounded border" wire:key="biaya-<?php echo e($index); ?>">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="text-md font-medium text-gray-800">Cost Item #<?php echo e($index + 1); ?></h4>
                                            <button type="button" 
                                                    wire:click="removeBiayaTambahan(<?php echo e($index); ?>)"
                                                    class="text-red-600 hover:text-red-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <!-- Description (with search dropdown) -->
                                            <div class="relative">
                                                <label class="block text-sm font-medium text-gray-700">Description *</label>
                                                <input type="text" 
                                                       wire:model.live.debounce.300ms="descriptionSearch.<?php echo e($index); ?>"
                                                       wire:focus="searchDescriptions(<?php echo e($index); ?>)"
                                                       placeholder="Search or select description..."
                                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php $__errorArgs = ['biayaTambahan.'.$index.'.description_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                       autocomplete="off">
                                                
                                                <!--[if BLOCK]><![endif]--><?php if(isset($showDescriptionDropdown[$index]) && $showDescriptionDropdown[$index]): ?>
                                                    <div class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                                         wire:click.stop>
                                                        <?php
                                                            $descriptions = $this->getFilteredDescriptions($index);
                                                        ?>
                                                        
                                                        <!--[if BLOCK]><![endif]--><?php if(empty($descriptionSearch[$index] ?? '') && $descriptions->count() > 0): ?>
                                                            <div class="px-4 py-2 text-xs text-blue-600 bg-blue-50 border-b border-blue-100">
                                                                Showing all descriptions - start typing to filter
                                                            </div>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        
                                                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $descriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $description): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                            <button type="button"
                                                                    wire:click.stop="selectDescription(<?php echo e($index); ?>, <?php echo e($description->id); ?>, '<?php echo e(addslashes($description->description)); ?>')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-900 hover:bg-gray-100">
                                                                <?php echo e($description->description); ?>

                                                            </button>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                            <div class="px-4 py-2 text-sm text-gray-500">No descriptions found</div>
                                                        <?php endif; ?>
                                                        
                                                        <!--[if BLOCK]><![endif]--><?php if($descriptions->count() >= 20): ?>
                                                            <div class="px-4 py-2 text-xs text-gray-500 bg-gray-50 border-t border-gray-100">
                                                                Showing first 20 results - type to search for more
                                                            </div>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['biayaTambahan.'.$index.'.description_id'];
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

                                            <!-- Amount (with thousand separator) -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Amount (Rp) *</label>
                                                <input type="text" 
                                                       wire:model.live="biayaTambahan.<?php echo e($index); ?>.harga_display"
                                                       placeholder="0"
                                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php $__errorArgs = ['biayaTambahan.'.$index.'.harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                       data-index="<?php echo e($index); ?>"
                                                       x-data="{ 
                                                           formatInput(event) {
                                                               let value = event.target.value.replace(/[^\d]/g, '');
                                                               if (value) {
                                                                   let formatted = new Intl.NumberFormat('id-ID').format(value);
                                                                   event.target.value = formatted;
                                                                   window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('biayaTambahan.<?php echo e($index); ?>.harga', parseInt(value));
                                                                   window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('biayaTambahan.<?php echo e($index); ?>.harga_display', formatted);
                                                               } else {
                                                                   event.target.value = '';
                                                                   window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('biayaTambahan.<?php echo e($index); ?>.harga', '');
                                                                   window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('biayaTambahan.<?php echo e($index); ?>.harga_display', '');
                                                               }
                                                           }
                                                       }"
                                                       x-on:input="formatInput($event)"
                                                       value="<?php echo e($biaya['harga_display'] ?? ''); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['biayaTambahan.'.$index.'.harga'];
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

                                            <!-- Cost Type -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Cost Type *</label>
                                                <select wire:model="biayaTambahan.<?php echo e($index); ?>.cost_type" 
                                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php $__errorArgs = ['biayaTambahan.'.$index.'.cost_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->getCostTypeOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </select>
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['biayaTambahan.'.$index.'.cost_type'];
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

                                            <!-- Date Cost -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Cost Date *</label>
                                                <input type="date" 
                                                    wire:model="biayaTambahan.<?php echo e($index); ?>.date_cost"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php $__errorArgs = ['biayaTambahan.'.$index.'.date_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['biayaTambahan.'.$index.'.date_cost'];
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
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <!-- Total Summary -->
                            <div class="mt-4 bg-blue-50 p-4 rounded-lg">
                                <h4 class="text-md font-medium text-blue-900 mb-2">Cost Summary</h4>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">Land Price:</span>
                                        <span class="text-blue-900 font-medium"><?php echo e($soil->formatted_harga); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">Additional Costs:</span>
                                        <span class="text-blue-900 font-medium">Rp <?php echo e(number_format($this->getTotalBiayaTambahan(), 0, ',', '.')); ?></span>
                                    </div>
                                    <div class="flex justify-between border-t border-blue-200 pt-1">
                                        <span class="text-blue-900 font-semibold">Total Investment:</span>
                                        <span class="text-blue-900 font-bold">Rp <?php echo e(number_format((int)$soil->harga + (int)$this->getTotalBiayaTambahan(), 0, ',', '.')); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                <p class="mt-2">No additional costs added yet.</p>
                                <p class="text-sm">Click "Add Cost" button to add additional costs like notary fees, taxes, etc.</p>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                wire:click="backToIndex"
                                class="px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray active:bg-gray-400 transition ease-in-out duration-150">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:shadow-outline-yellow active:bg-yellow-600 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Additional Costs
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add click outside handler -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('numberFormat', () => ({
        formatNumber(event) {
            let value = event.target.value.replace(/[^\d]/g, '');
            if (value) {
                event.target.value = new Intl.NumberFormat('id-ID').format(value);
            }
        }
    }));
});

// Handle click outside to close dropdowns
document.addEventListener('click', function(event) {
    // Only close dropdowns if clicking outside of any dropdown or input
    const dropdowns = document.querySelectorAll('[wire\\:click\\.stop]');
    const inputs = document.querySelectorAll('input[wire\\:focus]');
    
    let clickedInside = false;
    
    // Check if click was inside any dropdown or input
    dropdowns.forEach(dropdown => {
        if (dropdown.contains(event.target)) {
            clickedInside = true;
        }
    });
    
    inputs.forEach(input => {
        if (input.contains(event.target)) {
            clickedInside = true;
        }
    });
    
    // If clicked outside, close all dropdowns
    if (!clickedInside) {
        window.Livewire.dispatch('closeDropdowns');
    }
});
</script><?php /**PATH C:\xampp\tanrise-portal2\resources\views/livewire/soils/costs-form.blade.php ENDPATH**/ ?>