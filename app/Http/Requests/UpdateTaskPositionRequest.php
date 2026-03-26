<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskPositionRequest extends FormRequest
{
    /**
     * Determine if user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status_id' => 'required|integer|exists:statuses,id',
            'position' => 'required|integer|min:1',
            'old_status_id' => 'sometimes|nullable|integer|exists:statuses,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status_id.required' => 'Le statut est requis.',
            'status_id.integer' => 'Le statut doit être un nombre entier.',
            'status_id.exists' => 'Le statut n\'existe pas.',
            'position.required' => 'La position est requise.',
            'position.integer' => 'La position doit être un nombre entier.',
            'position.min' => 'La position doit être supérieure à 0.',
            'old_status_id.integer' => 'L\'ancien statut doit être un nombre entier.',
            'old_status_id.exists' => 'L\'ancien statut n\'existe pas.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                 Log::error('UpdateTaskPositionRequest validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
            }
        });
    }
}
