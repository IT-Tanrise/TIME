<div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal()"></div>
    
    <!-- Modal container -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full" wire:click.stop>
            <form wire:submit.prevent="store">
                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                        <input type="text" 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                               placeholder="Enter Name" 
                               wire:model="name">
                        @error('name') <span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex flex-row-reverse gap-3">
                    <button type="submit" 
                            class="inline-flex justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Save
                    </button>
                    <button type="button" 
                            wire:click="closeModal()"
                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>