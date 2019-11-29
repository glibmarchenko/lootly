<?php

namespace Laravel\Spark\Http\Controllers\Kiosk;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Spark\Http\Requests\ResourceRequest;
use Laravel\Spark\Http\Requests\ResourceCaseStudiesRequest;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Repositories\ResourceRepository;
use Laravel\Spark\Models\Filters\ResourceFilters;
use Laravel\Spark\Http\Resources\ResourceCollection;
use Laravel\Spark\Repositories\AuthorRepository;
use Laravel\Spark\Repositories\CategoryRepository;
use App\Models\Category;
use App\Models\Resource;

class ResourceController extends Controller
{
    protected $resources;
    protected $authors;
    protected $categories;

    public function __construct(
        ResourceRepository $resources,
        AuthorRepository $authors,
        CategoryRepository $categories
    ) {
        $this->resources = $resources;
        $this->authors = $authors;
        $this->categories = $categories;

        $this->middleware('auth');
        $this->middleware('dev');
    }

    public function get(ResourceFilters $filters, Request $request)
    {
        $limit = $request->input('limit', 10);

        $resources = $this->resources->get();

        if ($request->has('sort') !== true) {
            $resources->sortDefault();
        }

        return (new ResourceCollection($resources->filter($filters)->paginate($limit)));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $category = $this->categories->findBySlug(Category::SLUG_RESOURCES);

        abort_unless($category, 404);

        $categoryId = $request->query('category_id', null);

        $categories = $this->categories->getChildrenByParent($category);
        $authors = $this->authors->all();
        $statuses = Resource::getAllStatuses();

        return view('spark::kiosk.resource.create', compact('categoryId', 'categories', 'authors', 'statuses'));
    }

    public function createCaseStudies(Request $request)
    {
        $category = $this->categories->findBySlug(Category::SLUG_RESOURCES);

        abort_unless($category, 404);

        $categoryId = $request->query('category_id', null);

        $categories = $this->categories->getChildrenByParent($category);
        $authors = $this->authors->all();
        $statuses = Resource::getAllStatuses();

        return view('spark::kiosk.resource.create-case-studies', compact('categoryId', 'categories', 'authors', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Laravel\Spark\Http\Requests\ResourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ResourceRequest $request)
    {
        $resource = $this->resources->create($request->only([
            'author_id',
            'category_id',
            'title',
            'body',
            'description',
            'meta_description',
            'status',
        ]));

        if ($request->hasFile('mini_image')) {
            $uploadedFile = $request->file('mini_image');

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->update(['mini_image' => $pathFile]);
        }

        if ($request->hasFile('featured_image')) {
            $uploadedFile = $request->file('featured_image');

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->update(['featured_image' => $pathFile]);
        }

        if ($resource->category->isCaseStudies()) {
            return redirect()->route('spark.kiosk.resources.case-studies.edit', ['id' => $resource->id])
                ->with('alert.success', __('Updated successfully!'));
        }

        return redirect()->route('spark.kiosk.resources.edit', ['id' => $resource->id])
            ->with('alert.success', __('Saved successfully!'));
    }

    public function storeCaseStudies(ResourceCaseStudiesRequest $request)
    {
        $resource = $this->resources->create($request->only([
            'category_id',
            'title',
            'description',
            'meta_description',
            'status',
        ]));

        $caseStudies = $request->input('case_studies', []);

        $resource->caseStudy()->create(Arr::only($caseStudies, [
            'industry',
            'platform',
            'favorite_feature',
            'stat_first_title',
            'stat_first_value',
            'stat_second_title',
            'stat_second_value',
            'stat_third_title',
            'stat_third_value',
            'top_quote',
            'customer_name',
            'position_title',
            'company_body',
            'challenge_body',
            'challenge_quote',
            'solution_body',
            'solution_quote',
            'results_body',
        ]));

        if ($request->hasFile('mini_image')) {
            $uploadedFile = $request->file('mini_image');

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->update(['mini_image' => $pathFile]);
        }

        if ($request->hasFile('case_studies.company_image')) {
            $uploadedFile = $request->file('case_studies.company_image');

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->caseStudy()->update(['company_image' => $pathFile]);
        }

        if ($request->hasFile('case_studies.solution_image')) {
            $uploadedFile = $request->file('case_studies.solution_image');

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->caseStudy()->update(['solution_image' => $pathFile]);
        }

        if ($request->hasFile('case_studies.results_image')) {
            $uploadedFile = $request->file('case_studies.results_image');

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->caseStudy()->update(['results_image' => $pathFile]);
        }

        if ($resource->category->isCaseStudies()) {
            return redirect()->route('spark.kiosk.resources.case-studies.edit', ['id' => $resource->id])
                ->with('alert.success', __('Updated successfully!'));
        }

        return redirect()->route('spark.kiosk.resources.edit', ['id' => $resource->id])
            ->with('alert.success', __('Saved successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $resource = $this->resources->findOrFail($id);

        $category = $this->categories->findBySlug(Category::SLUG_RESOURCES);

        abort_unless($category, 404);

        $categoryId = $request->query('category_id', null);
        if ($categoryId) {
            $resource->category_id = $categoryId;
        }

        $categories = $this->categories->getChildrenByParent($category);
        $authors = $this->authors->all();
        $statuses = Resource::getAllStatuses();

        return view('spark::kiosk.resource.edit', compact('resource', 'categories', 'authors', 'statuses'));
    }

    public function editCaseStudies($id, Request $request)
    {
        $resource = $this->resources->findOrFail($id);

        $category = $this->categories->findBySlug(Category::SLUG_RESOURCES);

        abort_unless($category, 404);

        $categoryId = $request->query('category_id', null);
        if ($categoryId) {
            $resource->category_id = $categoryId;
        }

        $categories = $this->categories->getChildrenByParent($category);
        $authors = $this->authors->all();
        $statuses = Resource::getAllStatuses();

        return view('spark::kiosk.resource.edit-case-studies', compact('resource', 'categories', 'authors', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Laravel\Spark\Http\Requests\ResourceRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ResourceRequest $request, $id)
    {
        $resource = $this->resources->findOrFail($id);

        $this->resources->update($resource, $request->only([
            'author_id',
            'category_id',
            'title',
            'body',
            'description',
            'meta_description',
            'status',
        ]));

        if ($request->hasFile('mini_image')) {
            $uploadedFile = $request->file('mini_image');

            if ($resource->mini_image) {
                Storage::delete('public/' . $resource->mini_image);
            }

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->update(['mini_image' => $pathFile]);
        }

        if ($request->hasFile('featured_image')) {
            $uploadedFile = $request->file('featured_image');

            if ($resource->featured_image) {
                Storage::delete('public/' . $resource->featured_image);
            }

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->update(['featured_image' => $pathFile]);
        }

        if ($resource->category->isCaseStudies()) {
            return redirect()->route('spark.kiosk.resources.case-studies.edit', ['id' => $id])
                ->with('alert.success', __('Updated successfully!'));
        }

        return redirect()->route('spark.kiosk.resources.edit', ['id' => $id])
            ->with('alert.success', __('Updated successfully!'));
    }

    public function updateCaseStudies(ResourceCaseStudiesRequest $request, $id)
    {
        $resource = $this->resources->findOrFail($id);

        $this->resources->update($resource, $request->only([
            'category_id',
            'title',
            'description',
            'meta_description',
            'status',
        ]));

        $caseStudies = $request->input('case_studies', []);

        $resource->caseStudy()->updateOrCreate([], Arr::only($caseStudies, [
            'industry',
            'platform',
            'favorite_feature',
            'stat_first_title',
            'stat_first_value',
            'stat_second_title',
            'stat_second_value',
            'stat_third_title',
            'stat_third_value',
            'top_quote',
            'customer_name',
            'position_title',
            'company_body',
            'challenge_body',
            'challenge_quote',
            'solution_body',
            'solution_quote',
            'results_body',
        ]));

        if ($request->hasFile('mini_image')) {
            $uploadedFile = $request->file('mini_image');

            if ($resource->mini_image) {
                Storage::delete('public/' . $resource->mini_image);
            }

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->update(['mini_image' => $pathFile]);
        }

        if ($request->hasFile('case_studies.company_image')) {
            $uploadedFile = $request->file('case_studies.company_image');

            if ($resource->caseStudy->company_image) {
                Storage::delete('public/' . $resource->caseStudy->company_image);
            }

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->caseStudy()->update(['company_image' => $pathFile]);
        }

        if ($request->hasFile('case_studies.solution_image')) {
            $uploadedFile = $request->file('case_studies.solution_image');

            if ($resource->caseStudy->solution_image) {
                Storage::delete('public/' . $resource->caseStudy->solution_image);
            }

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->caseStudy()->update(['solution_image' => $pathFile]);
        }

        if ($request->hasFile('case_studies.results_image')) {
            $uploadedFile = $request->file('case_studies.results_image');

            if ($resource->caseStudy->results_image) {
                Storage::delete('public/' . $resource->caseStudy->results_image);
            }

            $pathFile = $uploadedFile->store($this->resources->makeImagePath(), 'public');
            $resource->caseStudy()->update(['results_image' => $pathFile]);
        }

        if ($resource->category->isCaseStudies()) {
            return redirect()->route('spark.kiosk.resources.case-studies.edit', ['id' => $id])
                ->with('alert.success', __('Updated successfully!'));
        }

        return redirect()->route('spark.kiosk.resources.edit', ['id' => $id])
            ->with('alert.success', __('Updated successfully!'));
    }
}
