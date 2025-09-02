<?php

namespace App\Http\Requests\API\V1\Provider\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'discount_percentage' => 'sometimes|nullable|integer|min:0|lte:100',
            'stock' => 'sometimes|required|integer|min:0',
            'published' => 'sometimes|required|boolean',
            'gallery' => 'sometimes|required|array|max:4',
            'gallery.*' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'remove_media_ids' => 'sometimes|array',
            'remove_media_ids.*' => 'integer',
        ];
    }
}


