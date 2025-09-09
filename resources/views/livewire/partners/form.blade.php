<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ $isEdit ? 'Edit Partner' : 'Add New Partner' }}
            </h2>
            <button wire:click="backToIndex" 
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </button>
        </div>
    </div>

    <form wire:submit="save" class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                <input wire:model="name" 
                       type="text" 
                       id="name"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Business Unit -->
            <div>
                <label for="business_unit_id" class="block text-sm font-medium text-gray-700">Business Unit *</label>
                <select wire:model="business_unit_id" 
                        id="business_unit_id"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Business Unit</option>
                    @foreach($businessUnits as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
                @error('business_unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Percentage -->
            <div class="md:col-span-2">
                <label for="percentage" class="block text-sm font-medium text-gray-700">Percentage *</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input wire:model="percentage" 
                           type="number" 
                           id="percentage"
                           step="0.01"
                           min="0"
                           max="100"
                           class="block w-full px-3 py-2 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <span class="text-gray-500 sm:text-sm">%</span>
                    </div>
                </div>
                @error('percentage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" 
                    wire:click="backToIndex"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </button>
            <button type="submit"
                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                {{ $isEdit ? 'Update Partner' : 'Create Partner' }}
            </button>
        </div>
    </form>
</div>