<?php

namespace Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Modules\Chat\Enums\MessageType;

class SendMessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'receiver_id' => 'nullable|integer|exists:users,id',
            'conversation_id' => 'nullable|integer|exists:conversations,id',
            'type' => 'required|integer|in:'.implode(',', MessageType::values()),
            'content' => 'required_if:type,'.MessageType::Text->value,
            'file' => 'required_if:type,'.MessageType::File->value.'|file|mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
            'offer_id' => 'required_if:type,'.MessageType::Offer->value.'|integer|exists:offers,id',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function messages(): array
    {
        return [
            'type.required' => __('The type field is required.'),
            'type.integer' => __('The type field must be an integer.'),
            'type.in' => __('The type field must be one of the following: :values', ['values' => implode(', ', MessageType::values())]),
            'file.required_if' => __('The file field is required when the type is file.'),
            'file.file' => __('The file field must be a file.'),
            'offer_id.required_if' => __('The offer id field is required when the type is offer.'),
            'offer_id.integer' => __('The offer id field must be an integer.'),
            'offer_id.exists' => __('The offer id field must be an existing offer id.'),
        ];
    }
}
