<?php

namespace App\Http\Requests\API\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ProviderProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow providers to update their own profile
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_name' => 'nullable|array',
            'store_name.ar' => 'required_with:store_name|string|max:255',
            'store_name.en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'city_id' => 'nullable|integer|exists:cities,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'commercial_number' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'brands' => 'nullable|array',
            'brands.*' => 'required_with:brands|integer|exists:brands,id',
            'commercial_number_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lat' => 'nullable|numeric|min:-90|max:90',
            'long' => 'nullable|numeric|min:-180|max:180',
            'address' => 'nullable|string|max:255',
        ];
    }


    public function messages(): array
    {
        return [
            'store_name.required' => __('Store name is required'),
            'store_name.ar.required' => __('Arabic store name is required'),
            'description.required' => __('Store description is required'),
            'category_id.required' => __('Category is required'),
            'category_id.exists' => __('Selected category does not exist'),
            'city_id.exists' => __('Selected city does not exist'),
            'commercial_number.required' => __('Commercial number is required'),
            'location.required' => __('Location is required'),
            'brands.required' => __('At least one brand must be selected'),
            'brands.*.exists' => __('One or more selected brands do not exist'),
        ];
    }
}
