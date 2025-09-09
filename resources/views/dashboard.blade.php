<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Welcome, {{ auth()->user()->name }}!
                    </h1>

                    <p class="mt-6 text-gray-500 leading-relaxed">
                        You are logged in! Your current role(s): 
                        @foreach(auth()->user()->roles as $role)
                            <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-1">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </p>
                    <p class="mt-6 text-gray-500 leading-relaxed">
                        You are logged in! Your current permission(s): 
                        @foreach(auth()->user()->permissions as $permission)
                            <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-1">
                                {{ $permission->name }}
                            </span>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-welcome />
            </div>
        </div>
    </div>
</x-app-layout>
