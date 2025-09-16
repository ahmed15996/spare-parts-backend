<?php

namespace App\Http\Requests\API\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ClientRegisterRequest extends FormRequest
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
            'phone' => 'required|string|max:10',
            'fcm_token' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'city_id' => 'required|integer|exists:cities,id',
            'lat' =>'nullable|numeric|min:-90|max:90',
            'long' => 'nullable|numeric|min:-180|max:180',
            'address' => 'nullable|string|max:255',
            "avatar" => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',


        ];
    }
    public function messages(): array
    {
        return [
            'phone.unique' => __('This number is used before'),
            'email.unique' => __('This email is used before'),
        ];
    }

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
    }}
