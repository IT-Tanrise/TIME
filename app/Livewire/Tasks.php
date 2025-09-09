<?php
  
namespace App\Livewire;
  
use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use Livewire\WithPagination;
  
class Tasks extends Component
{
    use WithPagination;

    public $tasks, $name, $body, $task_id, $search;
    public $isOpen = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function render()
    {
        // // Assign role to user
        // $user = User::find(10);

        // // Sync roles (removes all current roles and assigns new ones)
        // $user->syncRoles(['Super Admin']);
        $searchParams = '%'.$this->search.'%';

        $this->tasks = Task::where('name','like', $searchParams)->latest()->paginate(5);
        return view('livewire.tasks.tasks');
    }
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function openModal()
    {
        $this->isOpen = true;
    }
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function closeModal()
    {
        $this->isOpen = false;
    }
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->name = '';
        $this->task_id = '';
    }
     
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);
   
        Task::updateOrCreate(['id' => $this->task_id], [
            'name' => $this->name
        ]);
  
        session()->flash('message', 
            $this->task_id ? 'Task Updated Successfully.' : 'Task Created Successfully.');
  
        $this->closeModal();
        $this->resetInputFields();
    }
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $this->task_id = $id;
        $this->name = $task->name;
    
        $this->openModal();
    }
     
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function delete($id)
    {
        Task::find($id)->delete();
        session()->flash('message', 'Task Deleted Successfully.');
    }
}