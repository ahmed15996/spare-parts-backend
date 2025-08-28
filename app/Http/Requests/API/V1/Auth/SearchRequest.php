<?php

namespace App\Http\Requests\API\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
           'category_id' => 'required|integer|exists:categories,id',
           'q' => 'nullable|string|max:255',
           'city_id' => 'nullable|integer|exists:cities,id',
           'order_by'=>'nullable|integer|in:1,2'

        ];
    }
}
