{{-- resources/views/livewire/projects/form.blade.php --}}
<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ $isEdit ? 'Edit Project' : 'Add New Project' }}
            </h2>
            <button wire:click="backToIndex" 
                    class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="px-6 py-4">
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Land Selection --}}
                <div>
                    <label for="land_id" class="block text-sm font-medium text-gray-700">Land *</label>
                    <select wire:model="land_id" 
                            id="land_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('land_id') border-red-500 @enderror">
                        <option value="">Select Land</option>
                        @foreach($lands as $land)
                            <option value="{{ $land->id }}">{{ $land->lokasi_lahan }} - {{ $land->kota_kabupaten }}</option>
                        @endforeach
                    </select>
                    @error('land_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Project Name --}}
                <div>
                    <label for="nama_project" class="block text-sm font-medium text-gray-700">Project Name *</label>
                    <input wire:model="nama_project" 
                           id="nama_project"
                           type="text" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('nama_project') border-red-500 @enderror">
                    @error('nama_project')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Start Date --}}
                <div>
                    <label for="tgl_awal" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input wire:model="tgl_awal" 
                           id="tgl_awal"
                           type="date" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('tgl_awal') border-red-500 @enderror">
                    @error('tgl_awal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Update Date --}}
                <div>
                    <label for="tgl_update" class="block text-sm font-medium text-gray-700">Last Update Date</label>
                    <input wire:model="tgl_update" 
                           id="tgl_update"
                           type="date" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('tgl_update') border-red-500 @enderror">
                    @error('tgl_update')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($isEdit)
                        <p class="mt-1 text-xs text-gray-500">This will be automatically updated when you modify project details.</p>
                    @endif
                </div>

                {{-- Land Acquisition Status --}}
                <div>
                    <label for="land_acquisition_status" class="block text-sm font-medium text-gray-700">Land Acquisition Status *</label>
                    <select wire:model="land_acquisition_status" 
                            id="land_acquisition_status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('land_acquisition_status') border-red-500 @enderror">
                        <option value="">Select Status</option>
                        @foreach($this->getLandAcquisitionStatusOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('land_acquisition_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Project Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Project Status *</label>
                    <select wire:model="status" 
                            id="status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                        <option value="">Select Status</option>
                        @foreach($this->getProjectStatusOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Status Legend --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Status Definitions</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-600">
                    <div>
                        <strong>Land Acquisition Status:</strong>
                        <ul class="mt-1 space-y-1">
                            <li>• <strong>Planning:</strong> Initial planning phase</li>
                            <li>• <strong>Negotiation:</strong> Negotiating terms</li>
                            <li>• <strong>Agreement:</strong> Agreement signed</li>
                            <li>• <strong>Payment:</strong> Payment in process</li>
                            <li>• <strong>Transfer:</strong> Transfer in process</li>
                            <li>• <strong>Complete:</strong> Fully acquired</li>
                            <li>• <strong>Cancelled:</strong> Acquisition cancelled</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Project Status:</strong>
                        <ul class="mt-1 space-y-1">
                            <li>• <strong>Initiation:</strong> Project initiated</li>
                            <li>• <strong>Planning:</strong> Project planning</li>
                            <li>• <strong>Execution:</strong> Project execution</li>
                            <li>• <strong>Monitoring:</strong> Monitoring phase</li>
                            <li>• <strong>Closing:</strong> Project closing</li>
                            <li>• <strong>Completed:</strong> Project completed</li>
                            <li>• <strong>On Hold:</strong> Project on hold</li>
                            <li>• <strong>Cancelled:</strong> Project cancelled</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" 
                        wire:click="backToIndex"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancel
                </button>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ $isEdit ? 'Update Project' : 'Create Project' }}
                </button>
            </div>
        </form>
    </div>
</div>