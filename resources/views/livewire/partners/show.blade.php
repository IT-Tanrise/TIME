@php
    $partner = App\Models\Partner::with('businessUnit')->find($partnerId);
@endphp

<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Ownership Details</h2>
            <div class="space-x-2">
                <button wire:click="showEditForm({{ $partner->id }})" 
                        class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </button>
                <button wire:click="backToIndex" 
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </button>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Partner Information</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="mt-1 text-sm text-gray-900 font-medium">{{ $partner->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Business Unit</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $partner->businessUnit->name ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Percentage</label>
                    <p class="mt-1 text-lg font-semibold text-indigo-600">{{ $partner->formatted_percentage }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Metadata</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                    <p class="mt-1 text-sm text-gray-600">{{ $partner->created_at->format('d M Y H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-600">{{ $partner->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>