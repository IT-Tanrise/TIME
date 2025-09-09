{{-- resources/views/roles/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Role Details: ') . $role->name }}
            </h2>
            <div class="flex space-x-2">
                @can('roles.edit')
                    <a href="{{ route('roles.edit', $role) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit Role
                    </a>
                @endcan
                <a href="{{ route('roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Roles
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Role Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Information</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Role Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $role->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $role->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Permissions ({{ $role->permissions->count() }})
                        </h3>
                        @if($role->permissions->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                @foreach($role->permissions as $permission)
                                    <div class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                        {{ $permission->name }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">No permissions assigned to this role.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>