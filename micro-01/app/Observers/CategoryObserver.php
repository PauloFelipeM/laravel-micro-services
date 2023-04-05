<?php

namespace App\Observers;

use Illuminate\Support\Str;
use App\Models\Category;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     *
     * @param Category $category
     * @return void
     */
    public function creating(Category $category): void
    {
        $category->url = Str::slug($category->title);
    }

    /**
     * Handle the Category "updated" event.
     *
     * @param Category $category
     * @return void
     */
    public function updating(Category $category): void
    {
        $category->url = Str::slug($category->title);
    }
}
