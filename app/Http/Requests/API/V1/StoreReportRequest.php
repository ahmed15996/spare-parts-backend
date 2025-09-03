<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'model_id' => ['required', 'integer', 'min:1'],
            'model_type' => ['required', 'integer', 'in:0,1'], // 0: comment, 1: provider
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}


