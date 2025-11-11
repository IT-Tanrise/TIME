{{-- resources/views/livewire/pre-soil-buy/form.blade.php --}}
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Header -->
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 shadow-lg">
        <div class="px-4 mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-4">
                    <button wire:click="closeForm"
                        class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-gray-600 to-gray-700 border border-transparent rounded-lg font-medium text-sm text-white hover:from-gray-700 hover:to-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </button>
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">
                                {{ $editMode ? 'Edit Pre Soil Buy' : 'Create New Pre Soil Buy' }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $editMode ? 'Update pre soil buy data' : 'Fill the form to create new pre soil buy' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="px-4 py-6 mx-auto sm:px-6 lg:px-8 max-w-7xl">
        <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-xl">
            <form wire:submit.prevent="save" class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <!-- Memo Number -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Memo Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nomor_memo"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter memo number">
                        @error('nomor_memo')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="tanggal"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('tanggal')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- From -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            From <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="dari"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter sender name">
                        @error('dari')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- To -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            To <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="kepada"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter recipient name">
                        @error('kepada')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- CC -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            CC
                        </label>
                        <input type="text" wire:model="cc"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter CC (optional)">
                        @error('cc')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="subject_perihal"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter subject">
                        @error('subject_perihal')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Seller -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Seller <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="subject_penjual"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter seller name">
                        @error('subject_penjual')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Area Size -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Area Size (m²) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" wire:model.live="luas" step="0.01"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter area size">
                        @error('luas')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Total Agreement Price -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Total Agreement Price (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" wire:model.live="kesepakatan_harga_jual_beli"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter total price">
                        @error('kesepakatan_harga_jual_beli')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Price Per Meter -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Price Per Meter (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" wire:model="harga_per_meter" step="0.01"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter price per meter">

                        @if ($luas && $kesepakatan_harga_jual_beli && $luas > 0)
                            <div class="flex items-center p-3 mt-2 border border-blue-200 rounded-lg bg-blue-50">
                                <svg class="flex-shrink-0 w-4 h-4 mr-2 text-blue-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm font-medium text-blue-700">
                                    Auto-suggestion: Rp
                                    {{ number_format($kesepakatan_harga_jual_beli / $luas, 0, '', '.') }} /m²
                                </p>
                            </div>
                        @endif
                        @error('harga_per_meter')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Sales Object Description -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Sales Object (Objek Jual Beli)<span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="objek_jual_beli" rows="4"
                            class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter sales object description"></textarea>
                        @error('objek_jual_beli')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Upload File with Loading Indicator -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Upload File (IM)
                        </label>

                        @if ($existingFile && !$upload_file_im)
                            <div
                                class="flex items-center p-4 mb-3 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700">Current file:
                                        {{ basename($existingFile) }}</span>
                                </div>
                                <a href="{{ Storage::url($existingFile) }}" target="_blank"
                                    class="ml-4 px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                    View
                                </a>
                            </div>
                        @endif

                        <div class="relative">
                            <input type="file" wire:model="upload_file_im" id="fileUpload"
                                class="w-full px-4 py-3 transition-all duration-200 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">

                            <!-- Loading Indicator -->
                            <div wire:loading wire:target="upload_file_im"
                                class="absolute inset-0 flex items-center justify-center bg-white rounded-lg bg-opacity-90 backdrop-blur-sm">
                                <div
                                    class="flex items-center p-4 space-x-3 bg-white border border-gray-200 rounded-lg shadow-lg">
                                    <svg class="w-5 h-5 text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-600">Uploading file...</span>
                                </div>
                            </div>
                        </div>

                        @if ($upload_file_im)
                            <div
                                class="flex items-center p-4 mt-3 border border-green-200 rounded-lg shadow-sm bg-green-50">
                                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="text-sm font-medium text-green-700">
                                    File ready: {{ $upload_file_im->getClientOriginalName() }}
                                </p>
                            </div>
                        @endif

                        <p class="flex items-center mt-2 text-xs text-gray-500">
                            <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Maximum file size: 2MB. Allowed formats: PNG
                        </p>

                        @error('upload_file_im')
                            <span class="flex items-center mt-1 text-xs font-medium text-red-600">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>

                <!-- Form Actions -->
                <div class="flex justify-end pt-6 mt-6 space-x-3 border-t border-gray-200">
                    <button type="button" wire:click="closeForm"
                        class="px-6 py-3 text-sm font-semibold text-gray-700 transition-all duration-200 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:shadow-md">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="upload_file_im"
                        class="px-6 py-3 text-sm font-semibold text-white transition-all duration-200 border border-transparent rounded-lg shadow-sm bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-md">
                        <span wire:loading.remove wire:target="upload_file_im">
                            {{ $editMode ? 'Update' : 'Create New' }}
                        </span>
                        <span wire:loading wire:target="upload_file_im" class="flex items-center">
                            <svg class="w-4 h-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Uploading...
                        </span>
                    </button>
                </div>


                <!-- Notification Container -->
                <div aria-live="assertive"
                    class="fixed inset-0 z-50 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start">
                    <div class="flex flex-col items-center w-full space-y-4 sm:items-end">
                        <!-- Error Notification -->
                        @if (session()->has('error'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                                x-transition:enter="transform ease-out duration-300 transition"
                                x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                                x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="w-full max-w-sm overflow-hidden border border-red-200 rounded-lg shadow-lg pointer-events-auto bg-red-50">
                                <div class="p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3 w-0 flex-1 pt-0.5">
                                            <p class="text-sm font-medium text-red-800">Error</p>
                                            <p class="mt-1 text-sm text-red-600">{{ session('error') }}</p>
                                        </div>
                                        <div class="flex flex-shrink-0 ml-4">
                                            <button @click="show = false"
                                                class="inline-flex text-red-400 rounded-md bg-red-50 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Success Notification -->
                        @if (session()->has('message'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                                x-transition:enter="transform ease-out duration-300 transition"
                                x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                                x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="w-full max-w-sm overflow-hidden border border-green-200 rounded-lg shadow-lg pointer-events-auto bg-green-50">
                                <div class="p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3 w-0 flex-1 pt-0.5">
                                            <p class="text-sm font-medium text-green-800">Success</p>
                                            <p class="mt-1 text-sm text-green-600">{{ session('message') }}</p>
                                        </div>
                                        <div class="flex flex-shrink-0 ml-4">
                                            <button @click="show = false"
                                                class="inline-flex text-green-400 rounded-md bg-green-50 hover:text-green-500 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery for Auto-calculation -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    $(document).ready(function() {
        // Auto-focus on nomor_memo field when there's a duplicate error
        @if (session()->has('error') && str_contains(session('error'), 'Nomor memo'))
            $('input[wire:model="nomor_memo"]').focus().select();
        @endif

        // Auto-focus on soil search when dropdown opens
        $(document).on('click', 'button[wire\\:click*="showSoilDropdown"]', function() {
            setTimeout(function() {
                $('#soilSearchInput').focus();
            }, 100);
        });

        // Prevent form submission while file is uploading
        $('form').on('submit', function(e) {
            if ($('[wire\\:loading][wire\\:target="upload_file_im"]').is(':visible')) {
                e.preventDefault();
                alert('Please wait for file upload to complete.');
            }
        });
    });
</script>
