<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="px-4 py-3 mb-4 text-yellow-700 bg-yellow-100 border border-yellow-400 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('warning') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="px-4 py-3 mb-4 text-blue-700 bg-blue-100 border border-blue-400 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                {{ session('info') }}
            </div>
        </div>
    @endif

    @if ($showForm)
        @include('livewire.fields.form')
    @elseif ($showDetailModal)
        @include('livewire.fields.show')
    @else
        <!-- Delete Confirmation Modal -->
        @if($showDeleteModal)
            <div class="fixed inset-0 z-50 transition-opacity bg-gray-500 bg-opacity-75" wire:click="hideDeleteModalView"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                    <div class="relative overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="flex-1 mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900">
                                        Deactivate Field
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            @if(auth()->user()->can('field.approval'))
                                                This will deactivate the field and mark it as inactive. Please provide a reason.
                                            @else
                                                This will create a deactivation request that requires approval. Please provide a reason.
                                            @endif
                                        </p>
                                        <div class="mt-4">
                                            <label for="reason_delete" class="block mb-1 text-sm font-medium text-gray-700">
                                                Reason for Deactivation <span class="text-red-500">*</span>
                                            </label>
                                            <textarea 
                                                wire:model="reason_delete" 
                                                id="reason_delete"
                                                rows="3" 
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="Enter reason (minimum 10 characters)"></textarea>
                                            @error('reason_delete')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 sm:flex sm:flex-row-reverse sm:px-6">
                            <button 
                                type="button" 
                                wire:click="deleteWithReason"
                                class="inline-flex justify-center w-full px-3 py-2 text-sm font-semibold text-white bg-red-600 rounded-md shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                {{ auth()->user()->can('field.approval') ? 'Deactivate' : 'Submit Request' }}
                            </button>
                            <button 
                                type="button" 
                                wire:click="hideDeleteModalView"
                                class="inline-flex justify-center w-full px-3 py-2 mt-3 text-sm font-semibold text-gray-900 bg-white rounded-md shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <!-- Title Section -->
                    <div class="flex items-center gap-2">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">Fields / Bidang</h1>
                            <p class="text-xs text-gray-500">Manage all field data and assignments</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        @can('field.create')
                        <button wire:click="showCreateForm" 
                                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add New Field
                        </button>
                        @endcan
                        
                        <a href="{{ route('field-approvals') }}" 
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-orange-600 border border-orange-200 rounded-lg bg-orange-50 hover:bg-orange-100">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Approvals
                            @php
                                $pendingCount = \App\Models\FieldApproval::where('status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-orange-100 bg-orange-600 rounded-full">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('field-histories') }}" 
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-purple-600 border border-purple-200 rounded-lg bg-purple-50 hover:bg-purple-100">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            History
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 gap-2 mt-3 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="lg:col-span-2">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Search by name or number..."
                               class="w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <select wire:model.live="filterByBusinessUnit" 
                                class="w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Business Units</option>
                            @foreach($businessUnits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="filterByStatus" 
                                class="w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <button wire:click="resetFilters" 
                                class="w-full px-3 py-2 text-sm font-medium text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-2 mt-3 sm:grid-cols-4">
                    <div class="p-2 border border-green-200 rounded-lg bg-green-50">
                        <div class="text-xs font-medium text-green-800">Active</div>
                        <div class="text-lg font-bold text-green-900">
                            {{ \App\Models\Field::where('status', 'active')->count() }}
                        </div>
                    </div>
                    <div class="p-2 border border-red-200 rounded-lg bg-red-50">
                        <div class="text-xs font-medium text-red-800">Inactive</div>
                        <div class="text-lg font-bold text-red-900">
                            {{ \App\Models\Field::where('status', 'inactive')->count() }}
                        </div>
                    </div>
                    <div class="p-2 border border-yellow-200 rounded-lg bg-yellow-50">
                        <div class="text-xs font-medium text-yellow-800">Pending</div>
                        <div class="text-lg font-bold text-yellow-900">
                            {{ \App\Models\Field::where('status', 'pending')->count() }}
                        </div>
                    </div>
                    <div class="p-2 border border-blue-200 rounded-lg bg-blue-50">
                        <div class="text-xs font-medium text-blue-800">Total</div>
                        <div class="text-lg font-bold text-blue-900">
                            {{ \App\Models\Field::count() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th wire:click="sortBy('nomor_bidang')" 
                                class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Nomor Bidang
                                    @if($sortField === 'nomor_bidang')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('nama_bidang')" 
                                class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Nama Bidang
                                    @if($sortField === 'nama_bidang')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                Business Unit
                            </th>
                            <th class="px-4 py-3 text-xs font-medium text-center text-gray-500 uppercase">
                                Status
                            </th>
                            <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                Created By
                            </th>
                            <th wire:click="sortBy('created_at')" 
                                class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Created At
                                    @if($sortField === 'created_at')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-4 py-3 text-xs font-medium text-center text-gray-500 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($fields as $field)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-mono text-sm font-medium text-gray-900">
                                        {{ $field->nomor_bidang }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $field->nama_bidang }}
                                    </div>
                                    @if($field->reason_delete && $field->status === 'inactive')
                                        <div class="text-xs text-red-600 mt-0.5">
                                            Reason: {{ Str::limit($field->reason_delete, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($field->businessUnit)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $field->businessUnit->code }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ Str::limit($field->businessUnit->name, 25) }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $field->status_badge_color }}">
                                        {{ ucfirst($field->status) }}
                                    </span>
                                    @if($field->hasPendingApprovals())
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Pending Approval
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($field->createdBy)
                                        <div class="text-sm text-gray-900">{{ $field->createdBy->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $field->createdBy->email }}</div>
                                    @else
                                        <span class="text-xs text-gray-400">System</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">
                                        {{ $field->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $field->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="showDetail({{ $field->id }})" 
                                                title="View Details"
                                                class="p-1.5 text-blue-600 transition rounded hover:text-blue-900 hover:bg-blue-50">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        
                                        @can('field.update')
                                        <button wire:click="showEditForm({{ $field->id }})" 
                                                title="Edit"
                                                class="p-1.5 text-indigo-600 transition rounded hover:text-indigo-900 hover:bg-indigo-50">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        @endcan

                                        @if($field->status === 'active')
                                            @can('field.delete')
                                            <button wire:click="showDeleteModalView({{ $field->id }})" 
                                                    title="Deactivate"
                                                    class="p-1.5 text-red-600 transition rounded hover:text-red-900 hover:bg-red-50">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                            @endcan
                                        @elseif($field->status === 'inactive')
                                            @can('field.update')
                                            <button wire:click="activateField({{ $field->id }})" 
                                                    title="Activate"
                                                    class="p-1.5 text-green-600 transition rounded hover:text-green-900 hover:bg-green-50">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <p class="mb-1 text-base font-medium">No Fields Found</p>
                                        <p class="text-sm">
                                            @if($search || $filterByBusinessUnit || $filterByStatus)
                                                No fields match your filters
                                            @else
                                                Start by creating your first field
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($fields->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $fields->links() }}
                </div>
            @endif
        </div>
    @endif
</div>