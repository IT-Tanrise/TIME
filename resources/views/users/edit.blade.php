{{-- resources/views/users/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit User: ') . $user->name }}
            </h2>
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                Name *
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                                   placeholder="Enter full name">
                            @error('name')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                                Email *
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror"
                                   placeholder="Enter email address">
                            @error('email')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                                Password (Leave blank to keep current password)
                            </label>
                            <input type="password" name="password" id="password" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror"
                                   placeholder="Enter new password">
                            @error('password')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                                Confirm Password
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                   placeholder="Confirm new password">
                        </div>

                        <!-- Roles -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Roles
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                               id="role_{{ $role->id }}"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label for="role_{{ $role->id }}" class="ml-2 block text-sm text-gray-900">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update User
                            </button>
                            <a href="{{ route('users.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>