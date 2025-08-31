<?php

namespace App\Http\Requests\API\V1\Car;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
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
            'brand_model_id' => 'required|exists:brand_models,id',
            'manufacture_year' => 'required|integer',
            'number' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'brand_model_id.required' => __('validation.car.brand_model_id.required'),
            'brand_model_id.exists' => __('validation.car.brand_model_id.exists'),
            'manufacture_year.required' => __('validation.car.manufacture_year.required'),
            'manufacture_year.integer' => __('validation.car.manufacture_year.integer'),
            'number.max' => __('validation.car.number.max'),
        ];
    }
}
