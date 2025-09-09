{{-- resources/views/roles/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Roles Management') }}
            </h2>
            @can('roles.create')
                <a href="{{ route('roles.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Role
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
                                <th class="px-4 py-2 text-left">Permissions</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $role->name }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-sm text-gray-600">
                                            {{ $role->permissions->count() }} permissions
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            @can('roles.show')
                                                <a href="{{ route('roles.show', $role) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            @endcan
                                            @can('roles.edit')
                                                <a href="{{ route('roles.edit', $role) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                            @endcan
                                            @can('roles.delete')
                                                @if($role->name !== 'Super Admin')
                                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" class="inline">
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