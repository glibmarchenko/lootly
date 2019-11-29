<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Laravel\Spark\Repositories\ResourceRepository;
use Laravel\Spark\Repositories\CategoryRepository;

class ResourceController extends Controller
{
    protected $resources;
    protected $categories;

    public function __construct(
        ResourceRepository $resources,
        CategoryRepository $categories
    ) {
        $this->resources = $resources;
        $this->categories = $categories;
    }

    public function index()
    {
        $category = $this->categories->findBySlug(Category::SLUG_RESOURCES);

        abort_unless($category, 404);

        $categories = $this->categories->getChildrenByParent($category);

        $resources = $this->resources->all();

        return view('website.resources.index', compact('categories', 'resources'));
    }

    public function show(Request $request, $id, $slug)
    {
        $resource = $this->resources->findOrFail($id);

        abort_unless($resource->slug === $slug && $resource->isPublished(), 404);

        if ($resource->category->isCaseStudies()) {
            return view('website.resources.show-case-studies', compact('resource'));
        }

        return view('website.resources.show', compact('resource'));
    }
}
