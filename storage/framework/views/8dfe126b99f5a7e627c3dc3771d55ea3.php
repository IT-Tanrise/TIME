
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
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('User Details: ') . $user->name); ?>

            </h2>
            <div class="flex space-x-2">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.edit')): ?>
                    <a href="<?php echo e(route('users.edit', $user)); ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit User
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('users.index')); ?>" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Users
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- User Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-4">
                                <img class="h-20 w-20 rounded-full" src="<?php echo e($user->profile_photo_url); ?>" alt="<?php echo e($user->name); ?>">
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-gray-900"><?php echo e($user->name); ?></h4>
                                    <p class="text-gray-600"><?php echo e($user->email); ?></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <?php if($user->email_verified_at): ?>
                                            <span class="text-green-600">✓ Verified on <?php echo e($user->email_verified_at->format('M d, Y')); ?></span>
                                        <?php else: ?>
                                            <span class="text-red-600">✗ Not verified</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                                    <p class="mt-1 text-sm text-gray-900"><?php echo e($user->created_at->format('M d, Y h:i A')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Assigned Roles (<?php echo e($user->roles->count()); ?>)
                        </h3>
                        <?php if($user->roles->count() > 0): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                        <?php echo e($role->name); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 italic">No roles assigned to this user.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Direct Permissions -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Direct Permissions (<?php echo e($user->getDirectPermissions()->count()); ?>)
                        </h3>
                        <?php if($user->getDirectPermissions()->count() > 0): ?>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <?php $__currentLoopData = $user->getDirectPermissions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                                        <?php echo e($permission->name); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 italic">No direct permissions assigned.</p>
                        <?php endif; ?>
                    </div>

                    <!-- All Permissions (via roles + direct) -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            All Permissions (<?php echo e($user->getAllPermissions()->count()); ?>)
                        </h3>
                        <?php if($user->getAllPermissions()->count() > 0): ?>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                <?php $__currentLoopData = $user->getAllPermissions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                                        <?php echo e($permission->name); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 italic">No permissions available.</p>
                        <?php endif; ?>
                    </div>
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
<?php endif; ?><?php /**PATH C:\xampp\tanrise-portal2\resources\views/users/show.blade.php ENDPATH**/ ?>