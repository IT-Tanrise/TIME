<div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-500 opacity-75" wire:click="closeModal()"></div>
    
    <!-- Modal container -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full" wire:click.stop>
            <form>
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                  <div>
                    <h1 class="font-bold text-center mb-4">CREATE POSTS</h1>
                  </div>
                    <div>
                        <div class="mb-2">
                            <input wire:model="postId" type="hidden" class="shadow appearance-none border rounded w-full py-2 px-3 text-blue-900" placeholder="Input Post">
                        </div>
                        <div class="mb-2">
                            <label for="title" class="block">Title</label>
                            <input wire:model="title" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-blue-900" placeholder="Input Post">
                            @error('title') <h1 class="text-red-500">{{$message}}</h1>@enderror
                        </div>
                        <div class="mb-2">
                            <label for="description" class="block">Description</label>
                            <textarea wire:model="description" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-blue-900" placeholder="Input Description"></textarea>
                            @error('description') <h1 class="text-red-500">{{$message}}</h1>@enderror
                        </div>
                    </div>       
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                  <button wire:click.prevent="store()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Submit
                  </button>
                </span>
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                  <button wire:click="hideModal()" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                    Cancel
                  </button>
                </span>
              </div>
            </form>
        </div>
    </div>
</div>