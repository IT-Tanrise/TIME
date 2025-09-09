{{-- resources/views/users/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users Management') }}
            </h2>
            @can('users.create')
                <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create User
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Roles</th>
                                <th class="px-4 py-2 text-left">Created At</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="border-b">
                                    <td class="px-4 py-2">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                            <span class="ml-2">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2">{{ $user->email }}</td>
                                    <td class="px-4 py-2">
                                        @foreach($user->roles as $role)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-2">{{ $user->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            @can('users.show')
                                                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            @endcan
                                            @can('users.edit')
                                                <a href="{{ route('users.edit', $user) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                            @endcan
                                            @can('users.delete')
                                                @if(!$user->hasRole('Super Admin'))
                                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>