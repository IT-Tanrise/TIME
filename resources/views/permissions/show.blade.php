{{-- resources/views/permissions/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Permission Details: ') . $permission->name }}
            </h2>
            <div class="flex space-x-2">
                @can('permissions.edit')
                    <a href="{{ route('permissions.edit', $permission) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit Permission
                    </a>
                @endcan
                <a href="{{ route('permissions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Permissions
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Permission Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Permission Information</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Permission Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $permission->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Guard Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $permission->guard_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $permission->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>