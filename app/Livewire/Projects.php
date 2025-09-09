<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Land;
use Livewire\Component;
use Livewire\WithPagination;

class Projects extends Component
{
    use WithPagination;

    public $project;
    public $projectId;
    public $land_id = '';
    public $nama_project = '';
    public $tgl_awal = '';
    public $tgl_update = '';
    public $land_acquisition_status = '';
    public $status = '';
    
    public $showForm = false;
    public $showDetail = false;
    public $isEdit = false;
    public $search = '';

    protected $rules = [
        'land_id' => 'required|exists:lands,id',
        'nama_project' => 'required|string|max:255',
        'tgl_awal' => 'nullable|date',
        'tgl_update' => 'nullable|date|after_or_equal:tgl_awal',
        'land_acquisition_status' => 'required|string|max:255',
        'status' => 'required|string|max:255'
    ];

    public function render()
    {
        $projects = Project::with('land')
            ->when($this->search, function($query) {
                $query->where('nama_project', 'like', '%' . $this->search . '%')
                      ->orWhere('land_acquisition_status', 'like', '%' . $this->search . '%')
                      ->orWhere('status', 'like', '%' . $this->search . '%')
                      ->orWhereHas('land', function($q) {
                          $q->where('lokasi_lahan', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $lands = Land::orderBy('lokasi_lahan')->get();

        return view('livewire.projects.index', compact('projects', 'lands'));
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        $this->project = Project::findOrFail($id);
        $this->projectId = $this->project->id;
        $this->land_id = $this->project->land_id;
        $this->nama_project = $this->project->nama_project;
        $this->tgl_awal = $this->project->tgl_awal?->format('Y-m-d');
        $this->tgl_update = $this->project->tgl_update?->format('Y-m-d');
        $this->land_acquisition_status = $this->project->land_acquisition_status;
        $this->status = $this->project->status;
        
        $this->showForm = true;
        $this->isEdit = true;
    }

    public function showDetail($id)
    {
        $this->projectId = $id;
        $this->showDetail = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'land_id' => $this->land_id,
            'nama_project' => $this->nama_project,
            'tgl_awal' => $this->tgl_awal,
            'tgl_update' => $this->tgl_update,
            'land_acquisition_status' => $this->land_acquisition_status,
            'status' => $this->status
        ];

        if ($this->isEdit) {
            $this->project->update($data);
            session()->flash('message', 'Project updated successfully.');
        } else {
            Project::create($data);
            session()->flash('message', 'Project created successfully.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        Project::findOrFail($id)->delete();
        session()->flash('message', 'Project deleted successfully.');
    }

    public function backToIndex()
    {
        $this->showForm = false;
        $this->showDetail = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'project', 'projectId', 'land_id', 'nama_project', 'tgl_awal',
            'tgl_update', 'land_acquisition_status', 'status'
        ]);
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Helper method to get land acquisition status options
    public function getLandAcquisitionStatusOptions()
    {
        return [
            'Planning' => 'Planning',
            'Negotiation' => 'Negotiation',
            'Agreement' => 'Agreement Signed',
            'Payment' => 'Payment Process',
            'Transfer' => 'Transfer Process',
            'Complete' => 'Complete',
            'Cancelled' => 'Cancelled'
        ];
    }

    // Helper method to get project status options
    public function getProjectStatusOptions()
    {
        return [
            'Initiation' => 'Initiation',
            'Planning' => 'Planning',
            'Execution' => 'Execution',
            'Monitoring' => 'Monitoring',
            'Closing' => 'Closing',
            'Completed' => 'Completed',
            'On Hold' => 'On Hold',
            'Cancelled' => 'Cancelled'
        ];
    }

    // Auto-update tgl_update when saving
    public function updated($field)
    {
        if (in_array($field, ['nama_project', 'land_acquisition_status', 'status']) && $this->isEdit) {
            $this->tgl_update = now()->format('Y-m-d');
        }
    }
}