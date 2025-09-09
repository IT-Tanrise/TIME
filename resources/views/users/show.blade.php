{{-- resources/views/users/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details: ') . $user->name }}
            </h2>
            <div class="flex space-x-2">
                @can('users.edit')
                    <a href="{{ route('users.edit', $user) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit User
                    </a>
                @endcan
                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Users
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- User Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-4">
                                <img class="h-20 w-20 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h4>
                                    <p class="text-gray-600">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($user->email_verified_at)
                                            <span class="text-green-600">✓ Verified on {{ $user->email_verified_at->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-red-600">✗ Not verified</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Assigned Roles ({{ $user->roles->count() }})
                        </h3>
                        @if($user->roles->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->roles as $role)
                                    <div class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                        {{ $role->name }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">No roles assigned to this user.</p>
                        @endif
                    </div>

                    <!-- Direct Permissions -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Direct Permissions ({{ $user->getDirectPermissions()->count() }})
                        </h3>
                        @if($user->getDirectPermissions()->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                @foreach($user->getDirectPermissions() as $permission)
                                    <div class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                                        {{ $permission->name }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">No direct permissions assigned.</p>
                        @endif
                    </div>

                    <!-- All Permissions (via roles + direct) -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            All Permissions ({{ $user->getAllPermissions()->count() }})
                        </h3>
                        @if($user->getAllPermissions()->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                @foreach($user->getAllPermissions() as $permission)
                                    <div class="bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                                        {{ $permission->name }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">No permissions available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>