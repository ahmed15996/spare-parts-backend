<?php

namespace App\Http\Requests\API\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

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
            'phone' => 'required|string|max:255|unique:users,phone',
            'fcm_token' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'city_id' => 'required|integer|exists:cities,id',
            'lat' =>'nullable|numeric|min:-90|max:90',
            'long' => 'nullable|numeric|min:-180|max:180',
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
}
