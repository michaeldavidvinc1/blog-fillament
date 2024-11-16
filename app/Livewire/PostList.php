<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PostList extends Component
{
    use WithPagination;

    #[Url()]
    public $sort = 'desc';
    #[Url()]
    public $search = '';
    #[Url()]
    public $category = '';

    public function setSort($sort){
        $this->sort = ($sort === 'desc') ? 'desc' : 'asc';
        // $this->resetPage();
    }

    #[On('search')]
    public function updateSearch($search){
        $this->search = $search;
    }

    public function clearFilters(){
        $this->search = '';
        $this->category = '';
        $this->resetPage();
    }

    #[Computed()]
    public function posts(){
        return Post::published()
        ->orderBy('published_at', $this->sort)
        ->when($this->activeCategory, function($query){
            $query->WithCategory($this->category);
        })
        ->where('title', 'like', "%{$this->search}%")
        ->paginate(3);
    }

    #[Computed()]
    public function activeCategory(){
        return Category::where('title', $this->category)->first();
    }

    public function render()
    {
        return view('livewire.post-list');
    }
}
