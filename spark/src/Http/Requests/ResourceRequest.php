<?php

namespace Laravel\Spark\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Resource;

class ResourceRequest extends FormRequest
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
            'author_id' => 'required|integer|exists:authors,id',
            'category_id' => 'required|integer|exists:categories,id',
            'title' => 'required|string',
            'body' => 'required|string',
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
            'featured_image' => [
                'file',
                'max:2048', // size in kilobytes
                'mimes:jpg,jpeg,png,bmp,gif,svg',
                Rule::dimensions()
                    ->maxWidth(Resource::FEATURED_IMAGE_MAX_WIDTH)
                    ->maxHeight(Resource::FEATURED_IMAGE_MAX_HEIGHT),
            ],
            'status' => [
                'required',
                Rule::in(array_keys(Resource::getAllStatuses())),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'mini_image.dimensions' => __('The :attribute has invalid image dimensions.'),
            'featured_image.dimensions' => __('The :attribute has invalid image dimensions.'),
        ];
    }
}
