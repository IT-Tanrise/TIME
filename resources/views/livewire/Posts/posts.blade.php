<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Manage Posts
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">

            @if (session()->has('message'))
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                  <div class="flex">
                    <div>
                      <p class="text-sm">{{ session('message') }}</p>
                    </div>
                  </div>
                </div>
            @endif

            <!-- Header Actions -->
            <div class="flex justify-between items-center mb-4">
                <button wire:click="showModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create New Post
                </button>
            </div>

            <!-- 🔧 FIX: Improved search with live updates -->
            <div class="mb-6">
                <div class="flex items-center space-x-2">
                    <div class="flex-1">
                        <input 
                            wire:model.live="search" 
                            type="text" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            placeholder="Search posts by title or description..."
                        >
                    </div>
                    @if($search)
                        <button 
                            wire:click="clearSearch()" 
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Clear
                        </button>
                    @endif
                </div>
                @if($search)
                    <p class="text-sm text-gray-600 mt-2">
                        Searching for: "<strong>{{ $search }}</strong>"
                    </p>
                @endif
            </div>

            @if($isOpen)
                @include('livewire.posts.create')
            @endif

            <!-- Posts Table -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">No</th>
                            <th class="px-4 py-2 text-left">Title</th>
                            <th class="px-4 py-2 text-left">Description</th>
                            <th class="px-4 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $key => $post)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $posts->firstitem() + $key }}</td>
                            <td class="px-4 py-3">{{ $post->title }}</td>
                            <td class="px-4 py-3">{{ Str::limit($post->description, 100) }}</td>
                            <td class="px-4 py-3 text-center">
                                <button 
                                    wire:click="edit({{ $post->id }})" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded mr-2"
                                >
                                    Edit
                                </button>
                                <button 
                                    wire:click="delete({{ $post->id }})" 
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded"
                                    onclick="return confirm('Are you sure you want to delete this post?')"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                @if($search)
                                    No posts found matching "{{ $search }}"
                                @else
                                    No posts available
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>