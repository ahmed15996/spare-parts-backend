<?php

namespace App\Http\Requests\API\V1\Client;

use Illuminate\Foundation\Http\FormRequest;

class FilterOffersRequest extends FormRequest
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
            'city_id' => 'required|integer|exists:cities,id',
            'order_by' => 'required|integer|in:1,2',
        ];
    }
}
