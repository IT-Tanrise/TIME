
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 border-b border-gray-200">
        
        <div id="menu-content">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Menu</h2>
            <nav class="space-y-2">
                <!-- Ownerships -->
                <a href="<?php echo e($this->getOwnershipsUrl()); ?>" 
                   class="block px-2 py-1 text-gray-800 rounded-lg hover:font-semibold transition-colors duration-200 <?php echo e($this->isActive('partners') ? 'bg-blue-100 font-semibold' : ''); ?>">
                    Ownerships
                    <!--[if BLOCK]><![endif]--><?php if($this->getCurrentBusinessUnitId()): ?>
                        <span class="text-xs text-blue-600 block">
                            (Filtered by Current Unit)
                        </span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </a>
                
                <!-- Soils -->
                <a href="<?php echo e($this->getSoilsUrl()); ?>" 
                   class="block px-2 py-1 text-gray-800 rounded-lg hover:font-semibold transition-colors duration-200 <?php echo e($this->isActive('soils') ? 'bg-blue-100 font-semibold' : ''); ?>">
                    Soils
                    <!--[if BLOCK]><![endif]--><?php if($this->getCurrentBusinessUnitId()): ?>
                        <span class="text-xs text-blue-600 block">
                            (Filtered by Current Unit)
                        </span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </a>
                
            </nav>
        </div>
        
        
        <div id="menu-collapsed" class="text-center" style="display: none;">
            <button id="menu-expand-btn" 
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                    aria-label="Expand menu">
                
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
</div><?php /**PATH C:\xampp\tanrise-portal2\resources\views/livewire/navigation-menu-inner.blade.php ENDPATH**/ ?>