{{-- resources/views/livewire/soils/costs-interest-form.blade.php --}}
<div>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Header -->
            <div class="bg-purple-50 p-4 rounded-lg border-2 border-purple-200 mb-6">
                <h3 class="text-lg font-medium text-purple-900 mb-2">Interest Costs Calculation</h3>
                <p class="text-sm text-purple-700">Calculate compound interest for land acquisition over time periods</p>
                
                <div class="mt-3 bg-white p-3 rounded border">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Soil Location:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $soil->letak_tanah }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Area:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ number_format($soil->luas, 0, ',', '.') }} m²</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Base Price:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $soil->formatted_harga }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interest Costs Form -->
            <form wire:submit="saveInterestCosts">
                <div class="space-y-6">
                    @can('soil-costs.edit')
                    <!-- Interest Costs Section -->
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Interest Period Entries</h3>
                            <button type="button" 
                                    wire:click="addBiayaInterest"
                                    class="inline-flex items-center px-3 py-2 bg-purple-600 border border-transparent rounded-md text-sm font-semibold text-white hover:bg-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Period
                            </button>
                        </div>

                        @if(is_array($biayaInterest) && count($biayaInterest) > 0)
                            <div class="space-y-4">
                                @foreach($biayaInterest as $index => $interest)
                                    <div class="bg-white p-4 rounded border" wire:key="interest-{{ $index }}">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="text-md font-medium text-gray-800">Period #{{ $index + 1 }}</h4>
                                            <button type="button" 
                                                    wire:click="removeBiayaInterest({{ $index }})"
                                                    class="text-red-600 hover:text-red-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                            <!-- Start Date -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Start Date *</label>
                                                <input type="date" 
                                                    wire:model.live="biayaInterest.{{ $index }}.start_date"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('biayaInterest.'.$index.'.start_date') border-red-300 @enderror">
                                                @error('biayaInterest.'.$index.'.start_date') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- End Date -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">End Date *</label>
                                                <input type="date" 
                                                    wire:model.live="biayaInterest.{{ $index }}.end_date"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('biayaInterest.'.$index.'.end_date') border-red-300 @enderror">
                                                @error('biayaInterest.'.$index.'.end_date') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Harga Perolehan -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Harga Perolehan (Rp) *</label>
                                                <input type="text" 
                                                    wire:model.live="biayaInterest.{{ $index }}.harga_perolehan_display"
                                                    placeholder="0"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('biayaInterest.'.$index.'.harga_perolehan') border-red-300 @enderror"
                                                    x-data="{ 
                                                        formatInput(event) {
                                                            let value = event.target.value.replace(/[^\d]/g, '');
                                                            if (value) {
                                                                let formatted = new Intl.NumberFormat('id-ID').format(value);
                                                                event.target.value = formatted;
                                                                @this.set('biayaInterest.{{ $index }}.harga_perolehan', parseInt(value));
                                                                @this.set('biayaInterest.{{ $index }}.harga_perolehan_display', formatted);
                                                            } else {
                                                                event.target.value = '';
                                                                @this.set('biayaInterest.{{ $index }}.harga_perolehan', 0);
                                                                @this.set('biayaInterest.{{ $index }}.harga_perolehan_display', '');
                                                            }
                                                        }
                                                    }"
                                                    x-on:input="formatInput($event)"
                                                    value="{{ $interest['harga_perolehan_display'] ?? '' }}">
                                                @error('biayaInterest.'.$index.'.harga_perolehan') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Bunga % -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Bunga (%) *</label>
                                                <input type="number" 
                                                    wire:model.live="biayaInterest.{{ $index }}.bunga"
                                                    step="0.01"
                                                    min="0"
                                                    max="100"
                                                    placeholder="7.5"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('biayaInterest.'.$index.'.bunga') border-red-300 @enderror">
                                                @error('biayaInterest.'.$index.'.bunga') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <!-- Remarks (Full Width) -->
                                            <div class="md:col-span-2 lg:col-span-5">
                                                <label class="block text-sm font-medium text-gray-700">Remarks *</label>
                                                <textarea 
                                                    wire:model="biayaInterest.{{ $index }}.remarks"
                                                    rows="2"
                                                    placeholder="e.g., PT Gapura Mandiri : UTJ atas pembelian tanah"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('biayaInterest.'.$index.'.remarks') border-red-300 @enderror"></textarea>
                                                @error('biayaInterest.'.$index.'.remarks') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- CALCULATION SUMMARY TABLE -->
                            <div class="mt-6 bg-white rounded-lg border-2 border-purple-200 overflow-hidden">
                                <div class="bg-purple-100 px-4 py-3 border-b border-purple-200">
                                    <h4 class="text-md font-semibold text-purple-900">Interest Calculation Summary</h4>
                                </div>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Days</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga Perolehan</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Nilai Tanah Before</th>
                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Bunga %</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Bunga (Rp)</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Nilai Tanah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @php
                                                $nilaiTanahBefore = 0;
                                                $totalHargaPerolehan = 0;
                                                $totalBunga = 0;
                                                $totalNilaiTanah = 0;
                                            @endphp

                                            @foreach($biayaInterest as $index => $interest)
                                                @php
                                                    $startDate = $interest['start_date'] ? \Carbon\Carbon::parse($interest['start_date']) : null;
                                                    $endDate = $interest['end_date'] ? \Carbon\Carbon::parse($interest['end_date']) : null;
                                                    $days = ($startDate && $endDate) ? $startDate->diffInDays($endDate) : 0;
                                                    
                                                    $hargaPerolehan = $this->parseFormattedNumber($interest['harga_perolehan'] ?? 0);
                                                    $bunga = $interest['bunga'] ?? 0;
                                                    
                                                    // Calculate interest: (nilaiTanahBefore + hargaPerolehan) * bunga% / 365 * days
                                                    $baseValue = $nilaiTanahBefore + $hargaPerolehan;
                                                    $bungaCalculation = ($baseValue * ($bunga / 100) / 365 * $days);
                                                    
                                                    $nilaiTanah = $nilaiTanahBefore + $hargaPerolehan + $bungaCalculation;
                                                    
                                                    // Accumulate totals
                                                    $totalHargaPerolehan += $hargaPerolehan;
                                                    $totalBunga += $bungaCalculation;
                                                    $totalNilaiTanah = $nilaiTanah;
                                                @endphp
                                                
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $index + 1 }}</td>
                                                    <td class="px-3 py-2 text-sm text-gray-900">
                                                        {{ $startDate ? $startDate->format('d/m/Y') : '-' }}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-gray-900">
                                                        {{ $endDate ? $endDate->format('d/m/Y') : '-' }}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-center text-gray-900 font-medium">
                                                        {{ $days }}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-gray-900">
                                                        <div class="max-w-xs truncate" title="{{ $interest['remarks'] ?? '' }}">
                                                            {{ $interest['remarks'] ?? '-' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-right text-gray-900 font-medium">
                                                        Rp {{ number_format($hargaPerolehan, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-right text-blue-700">
                                                        Rp {{ number_format($nilaiTanahBefore, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-center text-purple-700 font-medium">
                                                        {{ number_format($bunga, 2) }}%
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-right text-orange-700 font-medium">
                                                        Rp {{ number_format($bungaCalculation, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-3 py-2 text-sm text-right text-green-700 font-bold">
                                                        Rp {{ number_format($nilaiTanah, 0, ',', '.') }}
                                                    </td>
                                                </tr>

                                                @php
                                                    // Update nilai tanah before for next iteration
                                                    $nilaiTanahBefore = $nilaiTanah;
                                                @endphp
                                            @endforeach

                                            <!-- TOTALS ROW -->
                                            <tr class="bg-purple-50 font-bold border-t-2 border-purple-300">
                                                <td colspan="5" class="px-3 py-3 text-sm text-right text-purple-900 uppercase">
                                                    Total:
                                                </td>
                                                <td class="px-3 py-3 text-sm text-right text-purple-900">
                                                    Rp {{ number_format($totalHargaPerolehan, 0, ',', '.') }}
                                                </td>
                                                <td class="px-3 py-3 text-sm text-right text-purple-900">
                                                    -
                                                </td>
                                                <td class="px-3 py-3 text-sm text-center text-purple-900">
                                                    -
                                                </td>
                                                <td class="px-3 py-3 text-sm text-right text-purple-900">
                                                    Rp {{ number_format($totalBunga, 0, ',', '.') }}
                                                </td>
                                                <td class="px-3 py-3 text-sm text-right text-purple-900">
                                                    Rp {{ number_format($totalNilaiTanah, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Additional Summary Info -->
                                <div class="bg-purple-50 px-4 py-3 border-t border-purple-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-purple-700">Total Harga Perolehan:</span>
                                            <span class="text-purple-900 font-bold">Rp {{ number_format($totalHargaPerolehan, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-purple-700">Total Interest Accrued:</span>
                                            <span class="text-orange-700 font-bold">Rp {{ number_format($totalBunga, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-purple-700">Final Land Value:</span>
                                            <span class="text-green-700 font-bold">Rp {{ number_format($totalNilaiTanah, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2">No interest periods added yet.</p>
                                <p class="text-sm">Click "Add Period" button to start calculating compound interest.</p>
                            </div>
                        @endif
                    </div>
                    @endcan

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" 
                                wire:click="backToIndex"
                                class="px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray active:bg-gray-400 transition ease-in-out duration-150">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:border-purple-700 focus:shadow-outline-purple active:bg-purple-600 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Interest Costs
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for number formatting -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('numberFormat', () => ({
        formatNumber(event) {
            let value = event.target.value.replace(/[^\d]/g, '');
            if (value) {
                event.target.value = new Intl.NumberFormat('id-ID').format(value);
            }
        }
    }));
});
</script>

<style>
/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>