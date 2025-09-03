<?php

namespace App\Http\Requests\API\V1\Provider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDaysRequest extends FormRequest
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
            'days' => 'required|array',
            'days.*.day_id' => 'required|integer|exists:days,id',
            'days.*.is_closed' => 'required|boolean',
            'days.*.from' => 'required_if:days.*.is_closed,false|nullable|date_format:H:i',
            'days.*.to' => 'required_if:days.*.is_closed,false|nullable|date_format:H:i|after:days.*.from',
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
            'days.required' => 'Working days are required',
            'days.array' => 'Working days must be an array',
            'days.*.day_id.required' => 'Day ID is required',
            'days.*.day_id.exists' => 'Selected day does not exist',
            'days.*.is_closed.required' => 'Closed status is required',
            'days.*.is_closed.boolean' => 'Closed status must be true or false',
            'days.*.from.required_if' => 'Start time is required when day is open',
            'days.*.from.date_format' => 'Start time must be in HH:MM format',
            'days.*.to.required_if' => 'End time is required when day is open',
            'days.*.to.date_format' => 'End time must be in HH:MM format',
            'days.*.to.after' => 'End time must be after start time',
        ];
    }
}
