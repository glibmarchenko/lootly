<?php

namespace Laravel\Spark\Contracts\Repositories;

use App\Models\Category;

interface CategoryRepository
{
    public function all();

    public function get();

    public function find($id);

    public function findOrFail($id);

    public function findBySlug($slug);

    public function create(array $attributes = []);

    public function getChildrenByParent(Category $parent);
}
