
<?php
    $land = App\Models\Land::with(['projects', 'soils'])->findOrFail($landId);
?>

<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Land Details</h2>
            <div class="flex space-x-2">
                <button wire:click="showEditForm(<?php echo e($land->id); ?>)" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </button>
                <button wire:click="backToIndex" 
                        class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Location</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($land->lokasi_lahan); ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Total Area(m2)</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($land->formatted_total_soil_area); ?></p>
                    <!--[if BLOCK]><![endif]--><?php if($land->total_soil_area == 0): ?>
                        <p class="mt-1 text-sm text-gray-900">No soils</p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>                

                <div>
                    <label class="block text-sm font-medium text-gray-500">Year Acquired</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($land->tahun_perolehan); ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Acquisition Value</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($land->formatted_nilai_perolehan); ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">City/Regency</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($land->kota_kabupaten ?? '-'); ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        <?php if($land->status === 'Available'): ?> bg-green-100 text-green-800
                        <?php elseif($land->status === 'Reserved'): ?> bg-yellow-100 text-yellow-800
                        <?php elseif($land->status === 'Sold'): ?> bg-red-100 text-red-800
                        <?php elseif($land->status === 'Development'): ?> bg-blue-100 text-blue-800
                        <?php else: ?> bg-gray-100 text-gray-800
                        <?php endif; ?>">
                        <?php echo e($land->status); ?>

                    </span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Information</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Price/m2( average )</label>
                    <!--[if BLOCK]><![endif]--><?php if($land->total_soil_area > 0): ?>
                        <p class="mt-1 text-sm text-gray-900">
                            <?php echo e($land->formatted_average_price_per_m2); ?>/m²
                        </p>
                    <?php else: ?>
                        <p class="mt-1 text-sm text-gray-900">-</p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Nominal A</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($land->formatted_nominal_b ?? '-'); ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Nominal B</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($land->formatted_nominal_b ?? '-'); ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">NJOP</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($land->formatted_njop ?? '-'); ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Estimated Market Price</label>
                    <p class="mt-1 text-sm text-gray-900"><?php echo e($land->formatted_est_harga_pasar ?? '-'); ?></p>
                </div>

                <!--[if BLOCK]><![endif]--><?php if($land->link_google_maps): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Google Maps</label>
                    <a href="<?php echo e($land->link_google_maps); ?>" 
                       target="_blank"
                       class="mt-1 text-sm text-blue-600 hover:text-blue-800 underline">
                        View on Maps
                    </a>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($land->alamat): ?>
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-500">Address</label>
            <p class="mt-1 text-sm text-gray-900"><?php echo e($land->alamat); ?></p>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <!--[if BLOCK]><![endif]--><?php if($land->keterangan): ?>
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-500">Description</label>
            <p class="mt-1 text-sm text-gray-900"><?php echo e($land->keterangan); ?></p>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Related Projects (<?php echo e($land->projects->count()); ?>)</h3>
            </div>

            <!--[if BLOCK]><![endif]--><?php if($land->projects->count() > 0): ?>
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Update</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Land Acquisition Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $land->projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo e($project->nama_project); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($project->formatted_tgl_awal ?? '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($project->formatted_tgl_update ?? '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        <?php echo e($project->land_acquisition_status); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        <?php echo e($project->status); ?>

                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-sm">No projects associated with this land.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Related Soil Records (<?php echo e($land->soils->count()); ?>)</h3>
            </div>

            <!--[if BLOCK]><![endif]--><?php if($land->soils->count() > 0): ?>
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seller</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buyer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ownership Proof</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $land->soils; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $soil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo e($soil->nama_penjual); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($soil->nama_pembeli); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($soil->letak_tanah); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($soil->formatted_luas); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($soil->formatted_harga); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($soil->bukti_kepemilikan); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-sm">No soil records associated with this land.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="border-t border-gray-200 pt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                <div>
                    <span class="font-medium">Created:</span> <?php echo e($land->created_at->format('d/m/Y H:i')); ?>

                </div>
                <div>
                    <span class="font-medium">Last Updated:</span> <?php echo e($land->updated_at->format('d/m/Y H:i')); ?>

                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\xampp\tanrise-portal2\resources\views/livewire/lands/show.blade.php ENDPATH**/ ?>