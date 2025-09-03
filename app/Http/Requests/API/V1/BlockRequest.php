<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'blocked_id' => ['required', 'integer', 'min:1', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'blocked_id.required' => 'User ID is required',
            'blocked_id.integer' => 'User ID must be a number',
            'blocked_id.min' => 'User ID must be greater than 0',
            'blocked_id.exists' => 'User not found',
        ];
    }
}
