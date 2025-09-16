<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute must be a valid email address.',
    'phone' => 'The :attribute must be a valid phone number.',
    'unique' => 'The :attribute has already been taken.',
    'exists' => 'The :attribute does not exist.',
    'string' => 'The :attribute must be a string.',
    'same' => 'The :attribute must match the :other field.',
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
        'numeric' => 'The :attribute must be at least :min.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
        'numeric' => 'The :attribute may not be greater than :max.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    
    'car' => [
        'brand_model_id' => [
            'required' => 'Brand model is required.',
            'exists' => 'Selected brand model does not exist.',
        ],
        'manufacture_year' => [
            'required' => 'Manufacture year is required.',
            'integer' => 'Manufacture year must be a number.',
        ],
        'number' => [
            'max' => 'Car number cannot exceed 255 characters.',
        ],
    ],
];
