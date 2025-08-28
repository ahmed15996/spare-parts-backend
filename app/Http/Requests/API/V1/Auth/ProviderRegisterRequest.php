<?php

namespace App\Http\Requests\API\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ProviderRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email',
            'city_id' => 'required|integer|exists:cities,id',
            'description' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'store_name' => 'required|array',
            'store_name.ar' => 'required|string|max:255',
            'store_name.en' => 'nullable|string|max:255',
            'lat' => 'required|numeric|min:-90|max:90',
            'long' => 'required|numeric|min:-180|max:180',
            'address' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'commercial_number' => 'required|string|max:255',
            'commercial_number_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brands' => 'required|array',
            'brands.*' => 'required|integer|exists:brands,id',
        ];
    }
    public function messages(): array
    {
        return [
            'phone.unique' => 'This number is used before',
        ];
    }
}
