<?php

namespace App\Http\Requests\API\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

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
            'phone' => 'required|string|min:9|max:9|unique:users,phone',
            'email' => 'required|email|unique:users,email',
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
            'phone.required' => __('validation.required', ['attribute' => __('attributes.phone')]),
            'phone.min' => __('validation.min.string', ['attribute' => __('attributes.phone'), 'min' => 9]),
            'phone.max' => __('validation.max.string', ['attribute' => __('attributes.phone'), 'max' => 9]),
            'phone.unique' => __('validation.unique', ['attribute' => __('attributes.phone')]),
            'email.required' => __('validation.required', ['attribute' => __('attributes.email')]),
            'email.email' => __('validation.email', ['attribute' => __('attributes.email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('attributes.email')]),
        ];
    }

        /**
     * Get the data to be validated, normalized as needed.
     */
    public function validationData(): array
    {
        $data = parent::validationData();

        $rawPhone = (string) ($data['phone'] ?? '');

        // Keep only digits
        $digitsOnlyPhone = preg_replace('/\D+/', '', $rawPhone) ?? '';

        // If phone starts with 05..., drop the leading 0 so 0512346789 == 512346789
        if (Str::startsWith($digitsOnlyPhone, '05')) {
            $digitsOnlyPhone = substr($digitsOnlyPhone, 1);
        }

        $data['phone'] = $digitsOnlyPhone;

        return $data;
    }
}
