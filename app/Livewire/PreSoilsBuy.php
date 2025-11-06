<?php

namespace App\Livewire;

use App\Models\PreSoilBuy;
use App\Models\PreSoilBuyApproval;
use App\Models\PreSoilBuyCashOut;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PreSoilsBuy extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showForm = false;
    public $showDetailForm = false;
    public $editMode = false;

    // Filter properties
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterSoilId = '';
    public $showFilters = false;

    // Form fields
    public $preSoilBuyId;
    // public $soil_id;
    public $nomor_memo;
    public $tanggal;
    public $dari;
    public $kepada;
    public $cc;
    public $subject_perihal;
    public $subject_penjual;
    public $luas;
    public $objek_jual_beli;
    public $kesepakatan_harga_jual_beli;
    public $harga_per_meter;
    public $upload_file_im;
    public $existingFile;

    // For dropdowns
    public $soils = [];
    public $showSoilDropdown = false;
    public $soilSearch = '';
    public $allSoils = []; // For filter dropdown

    protected $rules = [
        'nomor_memo' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'dari' => 'required|string|max:255',
        'kepada' => 'required|string|max:255',
        'cc' => 'nullable|string|max:255',
        'subject_perihal' => 'required|string|max:255',
        'subject_penjual' => 'required|string|max:255',
        'luas' => 'required|integer|min:1',
        'objek_jual_beli' => 'required|string',
        'kesepakatan_harga_jual_beli' => 'required|integer|min:0',
        'harga_per_meter' => 'required|integer|min:0',
        'upload_file_im' => 'nullable|file|mimes:pdf|max:2048',
    ];


    public function updatedSoilSearch()
    {
        $this->loadSoils();
    }


    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset(['filterStatus', 'filterDateFrom', 'filterDateTo', 'filterSoilId']);
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function isFiltered()
    {
        return $this->filterStatus || $this->filterDateFrom || $this->filterDateTo || $this->filterSoilId;
    }

    #[On('close-all-dropdowns')]
    public function closeAllDropdowns()
    {
        $this->showSoilDropdown = false;
    }



    public function showEditForm($id)
    {
        $preSoilBuy = PreSoilBuy::findOrFail($id);

        $this->preSoilBuyId = $preSoilBuy->id;
        $this->nomor_memo = $preSoilBuy->nomor_memo;
        $this->tanggal = $preSoilBuy->tanggal?->format('Y-m-d');
        $this->dari = $preSoilBuy->dari;
        $this->kepada = $preSoilBuy->kepada;
        $this->cc = $preSoilBuy->cc;
        $this->subject_perihal = $preSoilBuy->subject_perihal;
        $this->subject_penjual = $preSoilBuy->subject_penjual;
        $this->luas = $preSoilBuy->luas;
        $this->objek_jual_beli = $preSoilBuy->objek_jual_beli;
        $this->kesepakatan_harga_jual_beli = $preSoilBuy->kesepakatan_harga_jual_beli;
        $this->harga_per_meter = $preSoilBuy->harga_per_meter;
        $this->existingFile = $preSoilBuy->upload_file_im;

        $this->showForm = true;
        $this->editMode = true;
    }

    public function showDetail($id)
    {
        $this->preSoilBuyId = $id;
        $this->showDetailForm = true;
    }

    public function save()
    {
        $this->validate();

        $exists = PreSoilBuy::whereRaw('LOWER(nomor_memo) = ?', [strtolower($this->nomor_memo)])
            ->when($this->editMode, function ($query) {
                $query->where('id', '!=', $this->preSoilBuyId);
            })
            ->exists();

        if ($exists) {
            session()->flash('error', 'Nomor memo sudah digunakan. Harap gunakan nomor memo yang berbeda.');
            return;
        }

        DB::beginTransaction();

        try {
            $data = [
                'nomor_memo' => $this->nomor_memo,
                'tanggal' => $this->tanggal,
                'dari' => $this->dari,
                'kepada' => $this->kepada,
                'cc' => $this->cc,
                'subject_perihal' => $this->subject_perihal,
                'subject_penjual' => $this->subject_penjual,
                'luas' => $this->luas,
                'objek_jual_beli' => $this->objek_jual_beli,
                'kesepakatan_harga_jual_beli' => $this->kesepakatan_harga_jual_beli,
                'harga_per_meter' => $this->harga_per_meter,
            ];

            if ($this->editMode) {
                // --- UPDATE MODE ---
                $preSoilBuy = PreSoilBuy::findOrFail($this->preSoilBuyId);

                // Hapus file lama jika ada file baru yang diupload
                if ($this->upload_file_im && $preSoilBuy->upload_file_im) {
                    // Pastikan path menggunakan storage/app/public
                    if (Storage::disk('public')->exists($preSoilBuy->upload_file_im)) {
                        Storage::disk('public')->delete($preSoilBuy->upload_file_im);
                    }
                }

                $data['updated_by'] = Auth::id();
                $preSoilBuy->update($data);

                // Simpan file baru
                if ($this->upload_file_im && $this->upload_file_im instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    $extension = $this->upload_file_im->getClientOriginalExtension();
                    // Bersihkan nomor memo dari karakter khusus untuk nama file
                    $cleanNomorMemo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $this->nomor_memo);
                    $filename = "{$preSoilBuy->id}_{$cleanNomorMemo}.{$extension}";

                    // File akan tersimpan di storage/app/public/pre-soil-buy/file_im
                    $path = $this->upload_file_im->storeAs('pre-soil-buy/file_im', $filename, 'public');
                    $preSoilBuy->update(['upload_file_im' => $path]);
                }

                PreSoilBuyApproval::updateOrCreate(
                    ['pre_soil_buy_id' => $preSoilBuy->id],
                    [
                        'requested_by' => Auth::id(),
                        'status' => 'pending',
                        'responded_at' => null,
                        'updated_at' => now(),
                    ]
                );

                session()->flash('message', 'Pre Soil Buy berhasil diperbarui.');
            } else {
                // --- CREATE MODE ---
                $data['created_by'] = Auth::id();
                $preSoilBuy = PreSoilBuy::create($data);

                // Simpan file
                if ($this->upload_file_im && $this->upload_file_im instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    $extension = $this->upload_file_im->getClientOriginalExtension();
                    // Bersihkan nomor memo dari karakter khusus untuk nama file
                    $cleanNomorMemo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $this->nomor_memo);
                    $filename = "{$preSoilBuy->id}_{$cleanNomorMemo}.{$extension}";

                    // File akan tersimpan di storage/app/public/pre-soil-buy/file_im
                    $path = $this->upload_file_im->storeAs('pre-soil-buy/file_im', $filename, 'public');
                    $preSoilBuy->update(['upload_file_im' => $path]);
                }

                PreSoilBuyApproval::create([
                    'pre_soil_buy_id' => $preSoilBuy->id,
                    'requested_by' => Auth::id(),
                    'change_type' => 'create',
                    'status' => 'pending',
                    'responded_at' => null,
                ]);

                session()->flash('message', 'Pre Soil Buy berhasil dibuat.');
            }

            DB::commit();
            $this->closeForm();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function delete($id)
    {
        try {
            $preSoilBuy = PreSoilBuy::findOrFail($id);

            // Check if has pending approvals
            if ($preSoilBuy->hasPendingApprovals()) {
                session()->flash('error', 'Cannot delete. This record has pending approvals.');
                return;
            }

            // Delete file if exists
            if ($preSoilBuy->upload_file_im) {
                Storage::disk('public')->delete($preSoilBuy->upload_file_im);
            }

            $preSoilBuy->delete();

            session()->flash('message', 'Pre Soil Buy deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->showDetailForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'preSoilBuyId',
            'nomor_memo',
            'dari',
            'kepada',
            'cc',
            'subject_perihal',
            'subject_penjual',
            'luas',
            'objek_jual_beli',
            'kesepakatan_harga_jual_beli',
            'harga_per_meter',
            'upload_file_im',
            'existingFile',
            'soilSearch',
            'showSoilDropdown'
        ]);

        $this->tanggal = now()->format('Y-m-d');
        $this->resetErrorBag();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }

    public function updatingFilterSoilId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $preSoilBuy = PreSoilBuy::with(['createdBy', 'approvals'])
            ->select('pre_soil_buy.*')
            ->addSelect([
                // Ambil status terakhir dari tabel pre_soil_buy_approvals
                'approval_status' => PreSoilBuyApproval::select('status')
                    ->whereColumn('pre_soil_buy_id', 'pre_soil_buy.id')
                    ->latest('created_at')
                    ->limit(1)
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nomor_memo', 'like', '%' . $this->search . '%')
                        ->orWhere('subject_perihal', 'like', '%' . $this->search . '%')
                        ->orWhere('subject_penjual', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                // Filter berdasarkan status dari tabel approvals
                $query->whereIn('id', function ($subquery) {
                    $subquery->select('pre_soil_buy_id')
                        ->from('pre_soil_buy_approvals')
                        ->where('status', $this->filterStatus);
                });
            })
            ->when($this->filterDateFrom, function ($query) {
                $query->whereDate('tanggal', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->whereDate('tanggal', '<=', $this->filterDateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        // Get statistics for the filter badges (berdasarkan tabel approvals)
        $stats = [
            'total' => PreSoilBuy::count(),
            'pending' => PreSoilBuyApproval::where('status', 'pending')->count(),
            'approved' => PreSoilBuyApproval::where('status', 'approved')->count(),
            'rejected' => PreSoilBuyApproval::where('status', 'rejected')->count(),
        ];

        return view('livewire.pre-soil-buy.index', compact('preSoilBuy', 'stats'));
    }


    public function showCreateForm()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
    }
}
