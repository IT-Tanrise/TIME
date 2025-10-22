{{-- resources/views/livewire/vendors/show.blade.php --}}
@php
    $vendor = App\Models\ApCreditor::with([
        'creditorType', 
        'contracts.project.entity',
        'contracts' => function($query) {
            $query->orderBy('award_dt', 'desc');
        }
    ])->findOrFail($vendorId);
@endphp

<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <button wire:click="backToIndex" 
                        class="inline-flex items-center px-3 py-2 bg-gray-500 border border-transparent rounded-md text-sm text-white font-medium hover:bg-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Vendor Details</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Complete vendor information and project history</p>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        {{-- Basic Information --}}
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Basic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-lg">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Vendor Code</label>
                        <p class="mt-1 text-sm font-mono text-gray-900 bg-white px-3 py-2 rounded border border-gray-200">
                            {{ $vendor->creditor_acct }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Vendor Name</label>
                        <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $vendor->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Vendor Type</label>
                        <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            {{ $vendor->type_description }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Contact Person</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $vendor->contact_person ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Telephone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $vendor->telephone_no ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Fax</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $vendor->fax_no ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-500">Balance Amount</label>
                        <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $vendor->formatted_bal_amt }}</p>
                    </div> -->

                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-500">Currency</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $vendor->currency_cd ?: 'IDR' }}</p>
                    </div> -->

                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            {{ $vendor->active_status === 'A' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $vendor->active_status === 'A' ? 'Active' : 'Inactive' }}
                        </span>
                    </div> -->
                </div>
            </div>
        </div>

        {{-- Address Information --}}
        @if($vendor->full_address)
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Address Information
            </h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-900">{{ $vendor->full_address }}</p>
                @if($vendor->post_cd)
                    <p class="text-sm text-gray-600 mt-1">Postal Code: {{ $vendor->post_cd }}</p>
                @endif
            </div>
        </div>
        @endif

        {{-- Payment Information --}}
        <!-- <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Financial Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <label class="block text-xs font-medium text-blue-700">Claim Amount</label>
                    <p class="mt-1 text-lg font-semibold text-blue-900">
                        Rp {{ number_format($vendor->claim_amt, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <label class="block text-xs font-medium text-green-700">Payment Amount</label>
                    <p class="mt-1 text-lg font-semibold text-green-900">
                        Rp {{ number_format($vendor->payment_amt, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <label class="block text-xs font-medium text-yellow-700">Retention Amount</label>
                    <p class="mt-1 text-lg font-semibold text-yellow-900">
                        Rp {{ number_format($vendor->retention_amt, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <label class="block text-xs font-medium text-purple-700">Invoice Amount</label>
                    <p class="mt-1 text-lg font-semibold text-purple-900">
                        Rp {{ number_format($vendor->inv_amt, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div> -->

        {{-- Remarks --}}
        @if($vendor->remarks)
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Remarks</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-900">{{ $vendor->remarks }}</p>
            </div>
        </div>
        @endif

        {{-- Project History --}}
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Project History 
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $vendor->contracts->count() }} contract(s)
                    </span>
                </h3>
            </div>

            @if($vendor->contracts->count() > 0)
                <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contract No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Contract Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($vendor->contracts as $contract)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-sm font-mono font-medium text-blue-600">{{ $contract->contract_no }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $contract->project->descs ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $contract->project_no }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900">{{ $contract->entity->entity_name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $contract->entity_cd }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm text-gray-900" title="{{ $contract->contract_descs }}">
                                            {{ Str::limit($contract->contract_descs, 50) }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <span class="text-sm font-semibold text-gray-900">{{ $contract->formatted_contract_amt }}</span>
                                        @if($contract->auth_vo != 0)
                                            <div class="text-xs text-blue-600">VO: Rp {{ number_format($contract->auth_vo, 0, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($contract->award_dt)
                                            <div class="text-xs text-gray-600">
                                                <span class="font-medium">Award:</span> {{ $contract->formatted_award_dt }}
                                            </div>
                                        @endif
                                        @if($contract->start_dt && $contract->end_dt)
                                            <div class="text-xs text-gray-600">
                                                {{ $contract->formatted_start_dt }} - {{ $contract->formatted_end_dt }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                        Total Contract Value:
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                                        Rp {{ number_format($vendor->contracts->sum('contract_amt'), 0, ',', '.') }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-yellow-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm font-medium text-yellow-800">No Project History</p>
                    <p class="text-xs text-yellow-700 mt-1">This vendor has no contract records yet.</p>
                </div>
            @endif
        </div>

        {{-- Audit Information --}}
        <!-- <div class="border-t border-gray-200 pt-6">
            <h3 class="text-sm font-medium text-gray-500 mb-3">Audit Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-medium">Created by:</span>
                    <span class="ml-2">{{ $vendor->audit_user ?: '-' }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium">Date:</span>
                    <span class="ml-2">{{ $vendor->audit_date ? $vendor->audit_date->format('d/m/Y H:i') : '-' }}</span>
                </div>
            </div>
        </div> -->
    </div>
</div>