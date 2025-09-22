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
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Dashboard')); ?>

        </h2>
     <?php $__env->endSlot(); ?>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Welcome, <?php echo e(auth()->user()->name); ?>!
                    </h1>
                    <p class="mt-6 text-gray-500 leading-relaxed">
                        Last login <?php echo e(auth()->user()->formatted_last_login); ?> 
                    </p>
                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Super Admin')): ?>
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
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['soil-data.approval', 'soil-data-costs.approval'])): ?>
                <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Pending Soil Data Approvals
                            </h2>
                            <div class="flex items-center space-x-2">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('soil-data.approval')): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo e(App\Models\SoilApproval::pending()->where('change_type', 'details')->count()); ?> Soil Data
                                    </span>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('soil-data-costs.approval')): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <?php echo e(App\Models\SoilApproval::pending()->where('change_type', 'costs')->count()); ?> Cost Data
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php
                            $totalPending = 0;
                            if(auth()->user()->can('soil-data.approval')) {
                                $totalPending += App\Models\SoilApproval::pending()->where('change_type', 'details')->count();
                            }
                            if(auth()->user()->can('soil-data-costs.approval')) {
                                $totalPending += App\Models\SoilApproval::pending()->where('change_type', 'costs')->count();
                            }
                        ?>

                        <?php if($totalPending > 0): ?>
                            <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            You have approval permissions. Changes to soil data require your review before being applied.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('soil-approvals', []);

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
            <?php endif; ?>

            
            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">
                        Recent Approval Activity
                    </h2>
                    
                    <?php
                        // Get recent approval activities - approved or rejected within last 30 days
                        $recentApprovals = App\Models\SoilApproval::with(['soil.land', 'soil.businessUnit', 'requestedBy', 'approvedBy'])
                            ->whereIn('status', ['approved', 'rejected'])
                            ->where('updated_at', '>=', now()->subDays(30))
                            ->latest('updated_at')
                            ->limit(10)
                            ->get();
                            
                        // Get user's pending approvals if they have submitted any
                        $userPendingApprovals = App\Models\SoilApproval::with(['soil.land', 'soil.businessUnit'])
                            ->where('requested_by', auth()->id())
                            ->where('status', 'pending')
                            ->latest('created_at')
                            ->limit(5)
                            ->get();
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
                                                    <span class="font-medium"><?php echo e(ucfirst(str_replace('_', ' ', $approval->change_type))); ?></span> 
                                                    for <?php echo e($approval->soil->nama_penjual ?? 'Unknown'); ?> 
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
                                                            <span class="font-medium text-gray-900">
                                                                <?php echo e(ucfirst(str_replace('_', ' ', $approval->change_type))); ?> changes
                                                            </span>
                                                            for <?php echo e($approval->soil->nama_penjual ?? 'Unknown Seller'); ?>

                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?php echo e($approval->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                                                <?php echo e(ucfirst($approval->status)); ?>

                                                            </span>
                                                        </p>
                                                        <p class="text-xs text-gray-400 mt-1">
                                                            Requested by: <?php echo e($approval->requestedBy->name ?? 'Unknown'); ?> |
                                                            Business Unit: <?php echo e($approval->soil->businessUnit->name ?? 'N/A'); ?> |
                                                            Land: <?php echo e($approval->soil->land->lokasi_lahan ?? 'N/A'); ?>

                                                        </p>
                                                        <?php if($approval->status === 'approved'): ?>
                                                            <p class="text-xs text-green-600 mt-1">
                                                                Approved by: <?php echo e($approval->approvedBy->name ?? 'Unknown'); ?>

                                                                <?php if($approval->approval_reason): ?>
                                                                    - <?php echo e(Str::limit($approval->approval_reason, 50)); ?>

                                                                <?php endif; ?>
                                                            </p>
                                                        <?php elseif($approval->status === 'rejected'): ?>
                                                            <p class="text-xs text-red-600 mt-1">
                                                                Rejected by: <?php echo e($approval->approvedBy->name ?? 'Unknown'); ?>

                                                                <?php if($approval->rejection_reason): ?>
                                                                    - <?php echo e(Str::limit($approval->rejection_reason, 50)); ?>

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
                        
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['soil-data.approval', 'soil-data-costs.approval'])): ?>
                            <div class="mt-6 text-center">
                                <a href="<?php echo e(route('soil-approvals')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    View All Approvals
                                    <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No recent approval activity</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['soil-data.approval', 'soil-data-costs.approval'])): ?>
                                    No approval requests have been processed recently.
                                <?php else: ?>
                                    No approval activities to show. Submit changes to soil data to see approval status here.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
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