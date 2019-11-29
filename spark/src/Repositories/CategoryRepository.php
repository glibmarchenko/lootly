<?php

namespace Laravel\Spark\Repositories;

use App\Models\Category;
use Laravel\Spark\Contracts\Repositories\CategoryRepository as CategoryRepositoryContract;

class CategoryRepository implements CategoryRepositoryContract
{
    public function all()
    {
        return Category::all();
    }

    public function get()
    {
        return Category::query();
    }

    public function find($id)
    {
        return Category::find($id);
    }

    public function findOrFail($id)
    {
        return Category::findOrFail($id);
    }

    public function findBySlug($slug)
    {
        return Category::where(['slug' => $slug])->first();
    }

    public function create(array $attributes = [])
    {
        return Category::create($attributes);
    }

    public function getChildrenByParent(Category $parent)
    {
        return Category::where(['parent_id' => $parent->id])->get();
    }
}
