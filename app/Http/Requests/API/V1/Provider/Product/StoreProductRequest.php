<?php

namespace App\Http\Requests\API\V1\Provider\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gallery' => 'required|array|max:4',
            'gallery.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|lte:100',
            'stock' => 'required|integer|min:0',
            'published' => 'required|boolean',
        ];
    }
}


