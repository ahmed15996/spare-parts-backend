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
            'phone' => 'required|string|unique:users,phone',
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

        // Normalize phone using the same logic as AuthService
        $digitsOnly = preg_replace('/\D+/', '', $rawPhone) ?? '';
        
        if (str_starts_with($digitsOnly, '966')) {
            $normalizedPhone = $digitsOnly;
        } else {
            if (str_starts_with($digitsOnly, '05')) {
                $digitsOnly = substr($digitsOnly, 1);
            }
            if (str_starts_with($digitsOnly, '0')) {
                $digitsOnly = ltrim($digitsOnly, '0');
            }
            $normalizedPhone = '966' . $digitsOnly;
        }

        $data['phone'] = $normalizedPhone;

        return $data;
    }
}
