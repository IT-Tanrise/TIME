<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;

class Posts extends Component
{
    use WithPagination;

    public $search = '';  // Initialize with empty string
    public $postId, $title, $description;
    public $isOpen = 0;

    // 🔧 FIX: Add this to reset pagination when search changes
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {       
        return view('livewire.posts.posts', [
            'posts' => Post::when($this->search, function($query) {
                    return $query->where('title', 'like', '%' . $this->search . '%')
                                 ->orWhere('description', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(5)
        ]);
    }

    public function showModal() 
    {
        $this->isOpen = true;
    }

    public function hideModal() 
    {
        $this->isOpen = false;
    }

    public function store()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        Post::updateOrCreate(['id' => $this->postId], [
            'title' => $this->title,
            'description' => $this->description
        ]);

        $this->hideModal();

        session()->flash('message', $this->postId ? 'Post Updated Successfully' : 'Post Created Successfully');

        $this->resetForm();
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->postId = $id;
        $this->title = $post->title;
        $this->description = $post->description;

        $this->showModal();
    }

    public function delete($id)
    {
        Post::find($id)->delete();
        session()->flash('message', 'Post Successfully Deleted');
    }

    // 🆕 ADD: Helper method to reset form
    private function resetForm()
    {
        $this->postId = '';
        $this->title = '';
        $this->description = '';
    }

    // 🆕 ADD: Clear search method
    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }
}