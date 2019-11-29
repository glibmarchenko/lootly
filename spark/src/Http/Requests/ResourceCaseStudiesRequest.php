<?php

namespace Laravel\Spark\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Resource;
use App\Models\ResourceCaseStudies;

class ResourceCaseStudiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'mini_image' => [
                'file',
                'max:1024', // size in kilobytes
                'mimes:jpg,jpeg,png,bmp,gif,svg',
                Rule::dimensions()
                    ->maxWidth(Resource::MINI_IMAGE_MAX_WIDTH)
                    ->maxHeight(Resource::MINI_IMAGE_MAX_HEIGHT),
            ],
            'status' => [
                'required',
                Rule::in(array_keys(Resource::getAllStatuses())),
            ],

            /**
             * Case Studies
             */

            'case_studies.industry' => 'nullable|string',
            'case_studies.platform' => 'nullable|string',
            'case_studies.favorite_feature' => 'nullable|string',

            'case_studies.stat_first_title' => 'nullable|string',
            'case_studies.stat_first_value' => 'nullable|string',

            'case_studies.stat_second_title' => 'nullable|string',
            'case_studies.stat_second_value' => 'nullable|string',

            'case_studies.stat_third_title' => 'nullable|string',
            'case_studies.stat_third_value' => 'nullable|string',

            'case_studies.top_quote' => 'nullable|string',

            'case_studies.customer_name' => 'nullable|string',
            'case_studies.position_title' => 'nullable|string',

            'case_studies.company_body' => 'nullable|string',
            'case_studies.company_image' => [
                'file',
                'max:1024', // size in kilobytes
                'mimes:jpg,jpeg,png,bmp,gif,svg',
                Rule::dimensions()
                    ->maxWidth(ResourceCaseStudies::IMAGE_MAX_WIDTH)
                    ->maxHeight(ResourceCaseStudies::IMAGE_MAX_HEIGHT),
            ],

            'case_studies.challenge_body' => 'nullable|string',
            'case_studies.challenge_quote' => 'nullable|string',

            'case_studies.solution_body' => 'nullable|string',
            'case_studies.solution_quote' => 'nullable|string',
            'case_studies.solution_image' => [
                'file',
                'max:1024', // size in kilobytes
                'mimes:jpg,jpeg,png,bmp,gif,svg',
                Rule::dimensions()
                    ->maxWidth(ResourceCaseStudies::IMAGE_MAX_WIDTH)
                    ->maxHeight(ResourceCaseStudies::IMAGE_MAX_HEIGHT),
            ],

            'case_studies.results_body' => 'nullable|string',
            'case_studies.results_image' => [
                'file',
                'max:1024', // size in kilobytes
                'mimes:jpg,jpeg,png,bmp,gif,svg',
                Rule::dimensions()
                    ->maxWidth(ResourceCaseStudies::IMAGE_MAX_WIDTH)
                    ->maxHeight(ResourceCaseStudies::IMAGE_MAX_HEIGHT),
            ],
        ];
    }
}
