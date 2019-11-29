<?php

namespace App\Http\Controllers\Settings\Merchant;

use App\Transformers\TagTransformer;
use App\Http\Controllers\Controller;
use App\Repositories\MerchantRepository;
use App\Repositories\TagRepository;

class TagSettingController extends Controller
{
    public $merchantRepository;
    public $tagRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantRepository $merchantRepository, TagRepository $tagRepository)
    {
        $this->merchantRepository = $merchantRepository;
        $this->tagRepository = $tagRepository;
        $this->middleware('auth');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get()
    {
        $merchantObj = $this->merchantRepository->getcurrent();

        $tags = $this->tagRepository->all($merchantObj);

        return fractal()->collection($tags)->parseIncludes([])->transformWith(new TagTransformer)->toArray();

    }
}
