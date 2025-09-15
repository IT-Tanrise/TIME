
<div class="min-h-screen bg-gray-50">
    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
        <?php echo $__env->make('livewire.soils.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($showAdditionalCostsForm): ?>
        <?php echo $__env->make('livewire.soils.costs-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($showDetailForm): ?>
        <?php echo $__env->make('livewire.soils.show', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php else: ?>
        <!-- Compact Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <div class="flex items-center space-x-3">
                        <h1 class="text-xl font-semibold text-gray-900">
                            Soils
                            <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?>
                                <span class="text-base text-blue-600 font-normal">- <a href="<?php echo e(route('business-units', ['view' => 'show', 'id' => $businessUnit->id])); ?>" class="hover:text-blue-800"><?php echo e($businessUnit->name); ?> (<?php echo e($businessUnit->code); ?>)</a></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </h1>
                    </div>
                    <div class="flex space-x-2">
                        <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?>
                            <button wire:click="clearBusinessUnitFilter" 
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Show All Soils
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <button wire:click="showExportModalView" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-600 bg-white border border-green-300 rounded-md hover:bg-green-50">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Excel
                        </button>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('soils.edit')): ?>
                        <button wire:click="showCreateForm" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add New
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Modal -->
        <!--[if BLOCK]><![endif]--><?php if($showExportModal): ?>
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
             x-data="{ show: <?php if ((object) ('showExportModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showExportModal'->value()); ?>')<?php echo e('showExportModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showExportModal'); ?>')<?php endif; ?> }" 
             x-show="show" 
             @click.self="$wire.hideExportModalView()">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white" 
                 @click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Export Soils to Excel</h3>
                        <button wire:click="hideExportModalView" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Export Options -->
                    <div class="space-y-4">
                        <!-- Export Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Export Type</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="exportType" value="current" class="mr-2">
                                    <span class="text-sm text-gray-700">Current View 
                                        <span class="text-xs text-gray-500">(<?php echo e($soils->total()); ?> records with current filters)</span>
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="exportType" value="all" class="mr-2">
                                    <span class="text-sm text-gray-700">All Records
                                        <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?>
                                            <span class="text-xs text-gray-500">(from <?php echo e($businessUnit->name); ?>)</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="exportType" value="date_range" class="mr-2">
                                    <span class="text-sm text-gray-700">Date Range</span>
                                </label>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['exportType'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <!-- Date Range Inputs -->
                        <!--[if BLOCK]><![endif]--><?php if($exportType === 'date_range'): ?>
                        <div class="space-y-3 border-t pt-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Date (PPJB Date)</label>
                                <input type="date" wire:model.live="exportDateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['exportDateFrom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">To Date (PPJB Date)</label>
                                <input type="date" wire:model.live="exportDateTo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['exportDateTo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if($exportDateFrom && $exportDateTo): ?>
                                <div class="bg-blue-50 border border-blue-200 rounded p-2 text-sm text-blue-700">
                                    Records count: <?php echo e($this->getExportSummary()); ?>

                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!-- Export Preview Info -->
                        <!--[if BLOCK]><![endif]--><?php if($exportType): ?>
                        <div class="border-t pt-3">
                            <div class="bg-gray-50 rounded p-3 text-sm">
                                <div class="font-medium text-gray-700 mb-1">Export Preview:</div>
                                <!--[if BLOCK]><![endif]--><?php if($exportType === 'current'): ?>
                                    <div class="text-gray-600"><?php echo e($soils->total()); ?> records from current filtered view</div>
                                    <!--[if BLOCK]><![endif]--><?php if($search || $filterBusinessUnit || $filterByBusinessUnit || $filterLand): ?>
                                        <div class="text-xs text-blue-600 mt-1">
                                            Applied filters:
                                            <!--[if BLOCK]><![endif]--><?php if($search): ?> Search: "<?php echo e($search); ?>" <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($filterBusinessUnit || $filterByBusinessUnit): ?> Business Unit filter <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($filterLand): ?> Land filter <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php elseif($exportType === 'all'): ?>
                                    <div class="text-gray-600">All soil records
                                        <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?> from <?php echo e($businessUnit->name); ?> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php elseif($exportType === 'date_range' && $exportDateFrom && $exportDateTo): ?>
                                    <div class="text-gray-600">Records from <?php echo e($exportDateFrom); ?> to <?php echo e($exportDateTo); ?></div>
                                    <div class="text-xs text-gray-500">Estimated: <?php echo e($this->getExportSummary()); ?> records</div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <button wire:click="hideExportModalView" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button wire:click="exportToExcel" 
                                wire:loading.attr="disabled"
                                wire:target="exportToExcel"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="exportToExcel">Export Excel</span>
                            <span wire:loading wire:target="exportToExcel" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Exporting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- Compact Filters Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <!--[if BLOCK]><![endif]--><?php if(!$this->isFiltered()): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <!-- Search -->
                        <div>
                            <input type="text" 
                                wire:model.live="search" 
                                placeholder="Search by seller, buyer, location..."
                                class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Business Unit Filter -->
                        <div class="relative">
                            <div class="relative">
                                <input type="text" 
                                    wire:model.live="filterBusinessUnitSearch"
                                    wire:click="openBusinessUnitFilterDropdown"
                                    placeholder="Search business units..."
                                    class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-8"
                                    autocomplete="off">
                                
                                <!--[if BLOCK]><![endif]--><?php if($filterBusinessUnit): ?>
                                    <button type="button" 
                                            wire:click="clearBusinessUnitFilterSearch"
                                            class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <!--[if BLOCK]><![endif]--><?php if($showBusinessUnitFilterDropdown): ?>
                                    <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-auto">
                                        <?php
                                            $filteredBusinessUnits = $this->getFilteredBusinessUnitsForFilter();
                                        ?>
                                        
                                        <!--[if BLOCK]><![endif]--><?php if($filteredBusinessUnits->count() > 0): ?>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filteredBusinessUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <button type="button"
                                                        wire:click="selectBusinessUnitFilter(<?php echo e($unit->id); ?>, '<?php echo e($unit->name); ?>')"
                                                        class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-b-0">
                                                    <div class="font-medium"><?php echo e($unit->name); ?></div>
                                                    <div class="text-xs text-gray-500"><?php echo e($unit->code); ?></div>
                                                </button>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <div class="px-3 py-2 text-sm text-gray-500">No business units found</div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        <!-- Land Filter -->
                        <div class="relative">
                            <div class="relative">
                                <input type="text" 
                                    wire:model.live="filterLandSearch"
                                    wire:click="openLandFilterDropdown"
                                    placeholder="Search lands..."
                                    class="w-full px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-8"
                                    autocomplete="off">
                                
                                <!--[if BLOCK]><![endif]--><?php if($filterLand): ?>
                                    <button type="button" 
                                            wire:click="clearLandFilter"
                                            class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <!--[if BLOCK]><![endif]--><?php if($showLandFilterDropdown): ?>
                                    <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-auto">
                                        <?php
                                            $filteredLands = $this->getFilteredLandsForFilter();
                                        ?>
                                        
                                        <!--[if BLOCK]><![endif]--><?php if($filteredLands->count() > 0): ?>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filteredLands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $land): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <button type="button"
                                                        wire:click="selectLandFilter(<?php echo e($land->id); ?>, '<?php echo e($land->lokasi_lahan); ?>')"
                                                        class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 last:border-b-0">
                                                    <?php echo e($land->lokasi_lahan); ?>

                                                </button>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <div class="px-3 py-2 text-sm text-gray-500">No lands found</div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        <!-- Reset Button -->
                        <div>
                            <button wire:click="resetFilters" 
                                    class="w-full px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-4">
                    <div class="flex justify-between items-center">
                        <input wire:model.live="search" 
                            type="text" 
                            placeholder="Search soils by no. PPJB/ AJB, buyer, location..." 
                            class="flex-1 px-3 py-2 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 mr-4">
                        <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <?php echo e($businessUnit->name); ?>

                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!-- Alert Messages -->
            <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded">
                    <?php echo e(session('message')); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <?php if(session()->has('error')): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!-- Compact Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Land & Location</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BU</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ownership</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PPJB/AJB</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area & Price</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investment</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $soils; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $soil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($soil->land->lokasi_lahan ?? 'N/A'); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($soil->letak_tanah); ?></div>
                                    </td>
                                    <td class="px-2 py-2">
                                        <div class="text-xs text-gray-600 font-medium"><?php echo e($soil->businessUnit->code ?? '-'); ?></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($soil->nama_penjual); ?></div>
                                        <div class="text-xs text-gray-500 truncate max-w-32"><?php echo e($soil->alamat_penjual); ?></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($soil->bukti_kepemilikan); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($soil->bukti_kepemilikan_details); ?></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($soil->nomor_ppjb); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($soil->tanggal_ppjb->format('d/m/Y')); ?></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e(number_format($soil->luas, 0, ',', '.')); ?> m²</div>
                                        <div class="text-xs text-gray-500"><?php echo e($soil->formatted_harga_per_meter); ?>/m²</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm font-medium text-green-700"><?php echo e($soil->formatted_total_biaya_keseluruhan); ?></div>
                                        <!--[if BLOCK]><![endif]--><?php if($soil->biayaTambahanSoils->count() > 0): ?>
                                            <div class="text-xs text-orange-600">
                                                +<?php echo e($soil->formatted_total_biaya_tambahan); ?>

                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center space-x-1">
                                                <span><?php echo e($soil->biayaTambahanSoils->count()); ?> items</span>
                                                <?php
                                                    $standardCosts = $soil->biayaTambahanSoils->where('cost_type', 'standard')->count();
                                                    $nonStandardCosts = $soil->biayaTambahanSoils->where('cost_type', 'non_standard')->count();
                                                ?>
                                                <!--[if BLOCK]><![endif]--><?php if($standardCosts > 0): ?>
                                                    <span class="inline-flex px-1 text-xs rounded bg-green-100 text-green-800"><?php echo e($standardCosts); ?>S</span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <!--[if BLOCK]><![endif]--><?php if($nonStandardCosts > 0): ?>
                                                    <span class="inline-flex px-1 text-xs rounded bg-orange-100 text-orange-800"><?php echo e($nonStandardCosts); ?>NS</span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        <?php else: ?>
                                            <div class="text-xs text-gray-400">No additional</div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="px-2 py-2">
                                        <div class="flex items-center space-x-1">
                                            <!-- View Button -->
                                            <button wire:click="showDetail(<?php echo e($soil->id); ?>)" 
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                                    title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Edit Dropdown -->
                                            <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false">
                                                <button type="button" 
                                                        @click="open = !open"
                                                        class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50"
                                                        title="Edit Options">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <div x-show="open" 
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                    class="absolute right-0 mt-1 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                                    @click="open = false">
                                                    <div class="py-1">
                                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('soils.edit')): ?>
                                                        <button wire:click="showEditForm(<?php echo e($soil->id); ?>, 'details')" 
                                                                class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            Edit Details
                                                        </button>
                                                        <?php endif; ?>
                                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('soil-costs.edit')): ?>
                                                        <button wire:click="showEditForm(<?php echo e($soil->id); ?>, 'costs')" 
                                                                class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            Manage Costs
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Delete Button -->
                                             <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('soils.delete')): ?>
                                            <button wire:click="delete(<?php echo e($soil->id); ?>)" 
                                                    wire:confirm="Are you sure you want to delete this soil record?"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                                                    title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="px-3 py-8 text-center text-gray-500">
                                        <!--[if BLOCK]><![endif]--><?php if($businessUnit): ?>
                                            No soil records found for <?php echo e($businessUnit->name); ?>.
                                        <?php elseif($search): ?>
                                            No soil records found matching "<?php echo e($search); ?>".
                                        <?php else: ?>
                                            No soil records found.
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                <!-- Compact Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    <?php echo e($soils->links()); ?>

                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<!-- Enhanced JavaScript for Dropdown Behavior -->
<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('submit-export-form', (event) => {
        const params = event.params;
        
        // Create a hidden form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("soils.export")); ?>';
        form.style.display = 'none';
        
        // Add parameters as hidden inputs
        Object.keys(params).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = params[key];
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });
});
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

// Enhanced click outside handler - exclude edit dropdown menus
document.addEventListener('click', function(event) {
    // Don't close dropdown search boxes if clicking on edit dropdowns
    const editDropdown = event.target.closest('[x-data*="open"]');
    if (editDropdown) {
        return; // Let Alpine.js handle edit dropdowns
    }

    // Only close search dropdowns if clicking outside of any dropdown or input
    const dropdowns = document.querySelectorAll('[wire\\:click\\.stop]');
    const inputs = document.querySelectorAll('input[wire\\:focus], textarea[wire\\:focus]');
    const buttons = document.querySelectorAll('button[wire\\:click*="search"]');
    
    let clickedInside = false;
    
    // Check if click was inside any dropdown, input, or search button
    [...dropdowns, ...inputs, ...buttons].forEach(element => {
        if (element && element.contains && element.contains(event.target)) {
            clickedInside = true;
        }
    });
    
    // If clicked outside, close all search dropdowns
    if (!clickedInside && window.Livewire) {
        window.Livewire.dispatch('closeDropdowns');
    }
});

// Ensure proper scrolling behavior for dynamically created dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Prevent form submission on search button clicks
    document.addEventListener('click', function(event) {
        if (event.target.matches('button[wire\\:click*="search"]') || 
            event.target.closest('button[wire\\:click*="search"]')) {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    // Ensure proper scrolling behavior for dynamically created dropdowns
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1 && node.style && 
                    node.style.maxHeight && node.style.overflowY === 'auto') {
                    // Force scrolling properties
                    node.style.overflowY = 'auto';
                    node.style.overflowX = 'hidden';
                    // Add smooth scrolling
                    node.style.scrollBehavior = 'smooth';
                }
            });
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Add keyboard navigation for search dropdowns only
document.addEventListener('keydown', function(event) {
    // Only handle search dropdowns, not edit dropdowns
    const activeDropdown = document.querySelector('[wire\\:click\\.stop][style*="max-height"][style*="overflow-y: auto"]:not([style*="display: none"])');
    
    if (activeDropdown && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
        event.preventDefault();
        
        const items = activeDropdown.querySelectorAll('button[wire\\:click*="select"]');
        const currentFocus = document.activeElement;
        const currentIndex = Array.from(items).indexOf(currentFocus);
        
        let nextIndex;
        if (event.key === 'ArrowDown') {
            nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
        } else {
            nextIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
        }
        
        if (items[nextIndex]) {
            items[nextIndex].focus();
            items[nextIndex].scrollIntoView({ block: 'nearest' });
        }
    }
    
    // Handle Enter key to select focused item
    if (event.key === 'Enter' && document.activeElement.matches('button[wire\\:click*="select"]')) {
        event.preventDefault();
        document.activeElement.click();
    }
    
    // Handle Escape key to close search dropdowns only
    if (event.key === 'Escape' && window.Livewire) {
        window.Livewire.dispatch('closeDropdowns');
        // Close export modal if open
        if (document.querySelector('[wire\\:click="hideExportModalView"]')) {
            window.Livewire.dispatch('hideExportModalView');
        }
    }
});
</script><?php /**PATH C:\xampp\tanrise-portal2\resources\views/livewire/soils/index.blade.php ENDPATH**/ ?>