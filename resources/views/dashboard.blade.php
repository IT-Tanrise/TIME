<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Welcome Section --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Welcome, {{ auth()->user()->name }}!
                    </h1>
                    <p class="mt-6 text-gray-500 leading-relaxed">
                        Last login {{ auth()->user()->formatted_last_login }} 
                    </p>
                    @role('Super Admin')
                    <p class="mt-6 text-gray-500 leading-relaxed">
                        You are logged in!<br>Your current role(s):
                        @foreach(auth()->user()->roles as $role)
                            <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-1">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </p>
                    <p class="mt-6 text-gray-500 leading-relaxed">
                        Your current permission(s):
                        @if(auth()->user()->getAllPermissions()->count() > 0)
                            @foreach(auth()->user()->getAllPermissions() as $permission)
                                <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-1 mt-2">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                        @else
                            <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-1">No permissions available.</span>
                        @endif
                    </p>
                    @endrole
                </div>
            </div>

            {{-- Soil Data Approvals Section - UPDATED: Collapsible --}}
            @canany(['soil-data.approval', 'soil-data-costs.approval'])
                <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white">
                        <div class="flex items-center justify-between mb-6">
                            <button 
                                onclick="toggleSection('pendingApprovals')" 
                                class="flex items-center space-x-2 text-left w-full focus:outline-none group">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Pending Soil Data Approvals
                                </h2>
                                <svg id="pendingApprovals-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="flex items-center space-x-2">
                                @can('soil-data.approval')
                                    @php
                                        $pendingDetailsCount = App\Models\SoilApproval::pending()->where('change_type', 'details')->count();
                                        $pendingDeleteCount = App\Models\SoilApproval::pending()->where('change_type', 'delete')->count();
                                        $pendingCreateCount = App\Models\SoilApproval::pending()->where('change_type', 'create')->count();
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $pendingDetailsCount }} Soil Data
                                    </span>
                                    @if($pendingCreateCount > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $pendingCreateCount }} New Records
                                        </span>
                                    @endif
                                    @if($pendingDeleteCount > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $pendingDeleteCount }} Deletions
                                        </span>
                                    @endif
                                @endcan
                                @can('soil-data-costs.approval')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ App\Models\SoilApproval::pending()->where('change_type', 'costs')->count() }} Cost Data
                                    </span>
                                @endcan
                            </div>
                        </div>

                        {{-- Collapsible Content --}}
                        <div id="pendingApprovals-content" class="hidden">
                            @php
                                $totalPending = 0;
                                if(auth()->user()->can('soil-data.approval')) {
                                    $totalPending += App\Models\SoilApproval::pending()->whereIn('change_type', ['details', 'delete', 'create'])->count();
                                }
                                if(auth()->user()->can('soil-data-costs.approval')) {
                                    $totalPending += App\Models\SoilApproval::pending()->where('change_type', 'costs')->count();
                                }
                            @endphp

                            @if($totalPending > 0)
                                <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                You have approval permissions. Changes to soil data require your review before being applied.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <livewire:soil-approvals />
                        </div>
                    </div>
                </div>
            @endcanany

            {{-- Recent Activity Section - UPDATED: Collapsible --}}
            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white">
                    <div class="flex items-center justify-between mb-6">
                        <button 
                            onclick="toggleSection('recentActivity')" 
                            class="flex items-center space-x-2 text-left w-full focus:outline-none group">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Recent Approval Activity
                            </h2>
                            <svg id="recentActivity-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Collapsible Content --}}
                    <div id="recentActivity-content" class="hidden">
                        @php
                            // Get recent approval activities - approved or rejected within last 30 days
                            $recentApprovals = App\Models\SoilApproval::with(['soil.land', 'soil.businessUnit', 'requestedBy', 'approvedBy'])
                                ->whereIn('status', ['approved', 'rejected'])
                                ->where('updated_at', '>=', now()->subDays(30))
                                ->latest('updated_at')
                                ->limit(10)
                                ->get();
                                
                            // Get user's pending approvals if they have submitted any
                            $userPendingApprovals = App\Models\SoilApproval::with(['soil.land', 'soil.businessUnit'])
                                ->where('requested_by', auth()->id())
                                ->where('status', 'pending')
                                ->latest('created_at')
                                ->limit(5)
                                ->get();
                        @endphp

                        @if($userPendingApprovals->count() > 0)
                            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">
                                            Your Pending Approvals ({{ $userPendingApprovals->count() }})
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach($userPendingApprovals as $approval)
                                                    <li>
                                                       @if($approval->change_type === 'create')
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mr-1">
                                                                CREATE
                                                            </span>
                                                            <span class="font-medium text-gray-900">
                                                                Creation request
                                                            </span>
                                                        @elseif($approval->change_type === 'delete')
                                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $approval->change_type)) }}</span>
                                                        @endif
                                                        for {{ $approval->soil->nama_penjual ?? 'Unknown' }} 
                                                        ({{ $approval->created_at->diffForHumans() }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($recentApprovals->count() > 0)
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach($recentApprovals as $index => $approval)
                                        <li>
                                            <div class="relative pb-8">
                                                @if($index < $recentApprovals->count() - 1)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        @if($approval->status === 'approved')
                                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </span>
                                                        @else
                                                            <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-500">
                                                                @if($approval->change_type === 'delete')
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mr-1">
                                                                        DELETE
                                                                    </span>
                                                                    <span class="font-medium text-gray-900">
                                                                        Deletion request
                                                                    </span>
                                                                @elseif($approval->change_type === 'create')
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mr-1">
                                                                        CREATE
                                                                    </span>
                                                                    <span class="font-medium text-gray-900">
                                                                        Creation request
                                                                    </span>
                                                                @else
                                                                    <span class="font-medium text-gray-900">
                                                                        {{ ucfirst(str_replace('_', ' ', $approval->change_type)) }} changes
                                                                    </span>
                                                                @endif
                                                                for {{ $approval->soil->nama_penjual ?? ($approval->change_type === 'delete' ? 'Deleted Record' : 'Unknown Seller') }}
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $approval->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                                    {{ ucfirst($approval->status) }}
                                                                </span>
                                                            </p>
                                                            <p class="text-xs text-gray-400 mt-1">
                                                                Requested by: {{ $approval->requestedBy->name ?? 'Unknown' }} |
                                                                @if($approval->soil)
                                                                    {{ $approval->soil->nama_penjual ?? 'Unknown Seller' }}
                                                                @elseif($approval->change_type === 'create' && $approval->new_data)
                                                                    {{ $approval->new_data['nama_penjual'] ?? 'New Record' }}
                                                                @else
                                                                    <span class="text-red-500">Record has been deleted or doesn't exist</span>
                                                                @endif
                                                            </p>
                                                            @if($approval->status === 'approved')
                                                                <p class="text-xs text-green-600 mt-1">
                                                                    Approved by: {{ $approval->approvedBy->name ?? 'Unknown' }}
                                                                    @if($approval->reason)
                                                                        - {{ Str::limit($approval->reason, 50) }}
                                                                    @endif
                                                                </p>
                                                            @elseif($approval->status === 'rejected')
                                                                <p class="text-xs text-red-600 mt-1">
                                                                    Rejected by: {{ $approval->approvedBy->name ?? 'Unknown' }}
                                                                    @if($approval->reason)
                                                                        - {{ Str::limit($approval->reason, 50) }}
                                                                    @endif
                                                                </p>
                                                            @endif
                                                            
                                                            {{-- Show deletion reason for delete approvals --}}
                                                            @if($approval->change_type === 'delete' && $approval->getDeletionReason())
                                                                <p class="text-xs text-gray-600 mt-1 bg-gray-50 p-2 rounded">
                                                                    <strong>Deletion Reason:</strong> {{ Str::limit($approval->getDeletionReason(), 100) }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                            <time datetime="{{ $approval->updated_at->toISOString() }}">
                                                                {{ $approval->updated_at->diffForHumans() }}
                                                            </time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            @canany(['soil-data.approval', 'soil-data-costs.approval'])
                                <div class="mt-6 text-center">
                                    <a href="{{ route('soil-approvals') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        View All Approvals
                                        <svg class="ml-2 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                </div>
                            @endcanany
                            
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No recent approval activity</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    @canany(['soil-data.approval', 'soil-data-costs.approval'])
                                        No approval requests have been processed recently.
                                    @else
                                        No approval activities to show. Submit changes to soil data to see approval status here.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for collapsible functionality --}}
    <script>
        function toggleSection(sectionId) {
            const content = document.getElementById(sectionId + '-content');
            const icon = document.getElementById(sectionId + '-icon');
            
            if (content.classList.contains('hidden')) {
                // Show content
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                // Hide content
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
        
        // Optional: Add keyboard support
        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'BUTTON' && e.key === 'Enter') {
                e.target.click();
            }
        });
    </script>
</x-app-layout>