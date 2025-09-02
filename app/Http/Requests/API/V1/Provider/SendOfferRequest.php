<?php

namespace App\Http\Requests\API\V1\Provider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SendOfferRequest extends FormRequest
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
        $user = Auth::user();
        return [
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'has_delivery' => 'nullable|boolean',
            'city_id' => 'nullable|integer|exists:cities,id',
        ];
    }
}
