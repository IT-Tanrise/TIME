<div class="container mx-auto px-4 py-6">
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ $isEdit ? 'Edit' : 'Create' }} Business Unit
                </h2>
                <button wire:click="showIndex" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back to List
                </button>
            </div>
        </div>

        <form wire:submit="save" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input wire:model="code" type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('code') border-red-500 @enderror">
                @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input wire:model="name" type="text" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Business Unit</label>
                <select wire:model="parent_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('parent_id') border-red-500 @enderror">
                    <option value="">-- No Parent --</option>
                    @foreach($parentOptions as $option)
                        @if($option->id != $businessUnitId)
                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                        @endif
                    @endforeach
                </select>
                @error('parent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex space-x-3 pt-4">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                    {{ $isEdit ? 'Update' : 'Create' }}
                </button>
                <button type="button" wire:click="showIndex" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
