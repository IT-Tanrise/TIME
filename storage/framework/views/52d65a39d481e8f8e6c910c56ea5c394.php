
<div class="min-h-screen bg-gray-50">
    <!-- Sticky Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <button wire:click="backToSoil" 
                            class="inline-flex items-center px-3 py-1.5 bg-gray-500 border border-transparent rounded-md text-xs text-white font-medium hover:bg-gray-600 transition-colors duration-150">
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back
                    </button>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Soil Record History</h2>
                        <p class="text-xs text-gray-600">
                            <span class="font-medium"><?php echo e($soil->businessUnit->name ?? 'N/A'); ?></span> • 
                            <span class="font-medium"><?php echo e($soil->land->lokasi_lahan ?? 'N/A'); ?></span> • 
                            <span class="text-blue-600"><?php echo e($soil->nama_penjual); ?></span>
                        </p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                    <button wire:click="resetFilters" 
                            class="inline-flex items-center px-2 py-1.5 bg-gray-200 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-300 transition-colors duration-150">
                        Reset Filters
                    </button>
                    <button onclick="expandAll()" 
                            class="inline-flex items-center px-2 py-1.5 bg-blue-200 rounded-md text-xs font-medium text-blue-700 hover:bg-blue-300 transition-colors duration-150">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7"/>
                        </svg>
                        Expand All
                    </button>
                    <button onclick="collapseAll()" 
                            class="inline-flex items-center px-2 py-1.5 bg-gray-200 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-300 transition-colors duration-150">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7"/>
                        </svg>
                        Collapse All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Filters -->
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="px-4 py-3">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <!-- Action Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Action</label>
                    <select wire:model.live="filterAction" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Actions</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($action['value']); ?>"><?php echo e($action['label']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                
                <!-- Column Filter -->
                <div class="<?php echo e($filterAction === 'updated' ? '' : 'opacity-50'); ?>">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Updated Column
                        <!--[if BLOCK]><![endif]--><?php if($filterAction !== 'updated'): ?>
                            <span class="text-xs text-gray-400">(Select "Updated" first)</span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </label>
                    <select wire:model.live="filterColumn" 
                            <?php echo e($filterAction !== 'updated' ? 'disabled' : ''); ?>

                            class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 <?php echo e($filterAction !== 'updated' ? 'bg-gray-100 cursor-not-allowed' : ''); ?>">
                        <option value="">All Columns</option>
                        <!--[if BLOCK]><![endif]--><?php if($filterAction === 'updated'): ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($column['value']); ?>"><?php echo e($column['label']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                
                <!-- User Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">User</label>
                    <input type="text" wire:model.live="filterUser" placeholder="Search by user name..."
                           class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <!-- Date From -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" wire:model.live="filterDateFrom"
                           class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <!-- Date To -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" wire:model.live="filterDateTo"
                           class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>
            
            <!-- Active Filter Summary -->
            <!--[if BLOCK]><![endif]--><?php if($filterAction || $filterColumn || $filterUser || $filterDateFrom || $filterDateTo): ?>
                <div class="mt-3 p-2 bg-blue-50 rounded-md border border-blue-200">
                    <div class="flex flex-wrap items-center gap-1">
                        <span class="text-xs font-medium text-blue-800 mr-2">Active Filters:</span>
                        <!--[if BLOCK]><![endif]--><?php if($filterAction): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Action: <?php echo e(collect($availableActions)->firstWhere('value', $filterAction)['label'] ?? $filterAction); ?>

                                <button wire:click="$set('filterAction', '')" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($filterColumn && $filterAction === 'updated'): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Column: <?php echo e(collect($availableColumns)->firstWhere('value', $filterColumn)['label'] ?? $filterColumn); ?>

                                <button wire:click="$set('filterColumn', '')" class="ml-1 text-green-600 hover:text-green-800">×</button>
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($filterUser): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                User: <?php echo e($filterUser); ?>

                                <button wire:click="$set('filterUser', '')" class="ml-1 text-purple-600 hover:text-purple-800">×</button>
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($filterDateFrom): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                From: <?php echo e($filterDateFrom); ?>

                                <button wire:click="$set('filterDateFrom', '')" class="ml-1 text-orange-600 hover:text-orange-800">×</button>
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($filterDateTo): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                To: <?php echo e($filterDateTo); ?>

                                <button wire:click="$set('filterDateTo', '')" class="ml-1 text-orange-600 hover:text-orange-800">×</button>
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <!-- Main Content -->
    <div class="overflow-y-auto" style="height: calc(100vh - 180px);">
        <div class="px-4 py-3">
            <!-- History Timeline -->
            <div class="space-y-3">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="relative">
                        <!-- Timeline vertical line -->
                        <!--[if BLOCK]><![endif]--><?php if(!$loop->last): ?>
                            <div class="absolute left-4 top-[48px] w-0.5 bg-gray-200 z-0" style="height: calc(100% + 12px);"></div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        
                        <div class="flex items-start">
                            <!-- Timeline Icon -->
                            <div class="relative flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center z-10 bg-white border-2 mt-2
                                <?php if($history->action === 'created'): ?> border-green-200 bg-green-50 text-green-600
                                <?php elseif($history->action === 'updated'): ?> border-blue-200 bg-blue-50 text-blue-600
                                <?php elseif($history->action === 'deleted'): ?> border-red-200 bg-red-50 text-red-600
                                <?php else: ?> border-gray-200 bg-gray-50 text-gray-600 <?php endif; ?>">
                                <!--[if BLOCK]><![endif]--><?php if($history->action === 'created'): ?>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                <?php elseif($history->action === 'updated'): ?>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                <?php elseif($history->action === 'deleted'): ?>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 012 0v4a1 1 0 11-2 0V7zM12 7a1 1 0 012 0v4a1 1 0 11-2 0V7z" clip-rule="evenodd"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            
                            <!-- Content Card -->
                            <div class="ml-3 flex-1">
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <!-- Compact Header -->
                                    <button type="button" 
                                            onclick="toggleHistory('history-<?php echo e($history->id); ?>')"
                                            class="w-full p-3 text-left hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                <h3 class="text-sm font-medium text-gray-900 truncate"><?php echo e($history->action_display); ?></h3>
                                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full flex-shrink-0
                                                    <?php if($history->action === 'created'): ?> bg-green-100 text-green-800
                                                    <?php elseif($history->action === 'updated'): ?> bg-blue-100 text-blue-800
                                                    <?php elseif($history->action === 'deleted'): ?> bg-red-100 text-red-800
                                                    <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                                    <?php echo e(ucfirst($history->action)); ?>

                                                </span>
                                                <div class="text-xs text-gray-500 flex-shrink-0">
                                                    <?php echo e($history->formatted_created_at); ?>

                                                </div>
                                                <div class="text-xs text-gray-400 truncate">
                                                    by <?php echo e($history->user_display); ?>

                                                </div>
                                            </div>
                                            <!-- Chevron Icon -->
                                            <svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200 chevron-icon flex-shrink-0"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </button>
                                    
                                    <!-- Collapsible Content -->
                                    <div id="history-<?php echo e($history->id); ?>" class="history-content hidden border-t border-gray-100">
                                        <div class="p-3 bg-gray-50">
                                            <!-- User and IP info -->
                                            <div class="flex items-center justify-between text-xs text-gray-600 mb-3">
                                                <div class="flex items-center space-x-4">
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <?php echo e($history->user_display); ?>

                                                    </span>
                                                    <!--[if BLOCK]><![endif]--><?php if($history->ip_address): ?>
                                                        <span class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <?php echo e($history->ip_address); ?>

                                                        </span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                                <!--[if BLOCK]><![endif]--><?php if($history->updated_at != $history->created_at): ?>
                                                    <span class="text-orange-600">Modified: <?php echo e($history->formatted_updated_at); ?></span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>

                                            <!-- Changes details -->
                                            <!--[if BLOCK]><![endif]--><?php if($history->action === 'updated' && $history->changes): ?>
                                                <div class="bg-white border border-blue-200 rounded-lg p-3">
                                                    <div class="text-sm font-medium text-blue-800 mb-2">Changes Made</div>
                                                    <div class="text-sm text-blue-700 mb-3"><?php echo e($history->changes_summary); ?></div>
                                                    
                                                    <?php $changeDetails = $this->getChangeDetails($history) ?>
                                                    <!--[if BLOCK]><![endif]--><?php if($changeDetails): ?>
                                                        <div class="space-y-2">
                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $changeDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="border border-gray-200 rounded p-2">
                                                                    <div class="font-medium text-gray-900 text-xs mb-2"><?php echo e($change['field']); ?></div>
                                                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                                                        <div class="bg-red-50 p-2 rounded border border-red-200">
                                                                            <div class="text-red-600 font-medium mb-1">Before:</div>
                                                                            <div class="text-gray-700 break-words"><?php echo e($change['old'] ?: 'Empty'); ?></div>
                                                                        </div>
                                                                        <div class="bg-green-50 p-2 rounded border border-green-200">
                                                                            <div class="text-green-600 font-medium mb-1">After:</div>
                                                                            <div class="text-gray-700 break-words"><?php echo e($change['new'] ?: 'Empty'); ?></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            <?php elseif($history->action === 'created'): ?>
                                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                                    <div class="text-sm font-medium text-green-800 mb-1">Record Created</div>
                                                    <div class="text-sm text-green-700">New soil record was successfully created in the system.</div>
                                                </div>
                                            <?php elseif($history->action === 'deleted'): ?>
                                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                                    <div class="text-sm font-medium text-red-800 mb-1">Record Deleted</div>
                                                    <div class="text-sm text-red-700">Soil record was permanently removed from the system.</div>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No history found</h3>
                        <p class="mt-1 text-sm text-gray-500">No history records match your current filters.</p>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Pagination -->
            <!--[if BLOCK]><![endif]--><?php if($histories->hasPages()): ?>
                <div class="mt-4 border-t border-gray-200 pt-4">
                    <?php echo e($histories->links()); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function toggleHistory(elementId) {
            const content = document.getElementById(elementId);
            const button = content.previousElementSibling;
            const chevron = button.querySelector('.chevron-icon');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }
        
        function expandAll() {
            const contents = document.querySelectorAll('.history-content');
            const chevrons = document.querySelectorAll('.chevron-icon');
            
            contents.forEach(content => content.classList.remove('hidden'));
            chevrons.forEach(chevron => chevron.style.transform = 'rotate(180deg)');
        }
        
        function collapseAll() {
            const contents = document.querySelectorAll('.history-content');
            const chevrons = document.querySelectorAll('.chevron-icon');
            
            contents.forEach(content => content.classList.add('hidden'));
            chevrons.forEach(chevron => chevron.style.transform = 'rotate(0deg)');
        }
    </script>
</div><?php /**PATH C:\xampp\tanrise-portal2\resources\views/livewire/soil-histories/index.blade.php ENDPATH**/ ?>