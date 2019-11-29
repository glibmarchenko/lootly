<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Merchant;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Transformers\TagTransformer;
use Illuminate\Http\Request;

class MerchantTagController extends Controller
{
    protected $tags;

    public function __construct(TagRepository $tags)
    {
        $this->tags = $tags;
    }

    public function get(Request $request, Merchant $merchant)
    {
        $merchantTags = $this->tags->withCriteria([
            new ByMerchant($merchant->id),
        ])->all();

        return fractal($merchantTags)->transformWith(new TagTransformer)->toArray();
    }
}