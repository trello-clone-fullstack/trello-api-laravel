<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'project_name' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ];
    }

        public function messages(): array
        {
            return [
                'project_name.required' => 'Le nom du projet est requis.',
                'project_name.string' => 'Le nom du projet doit être une chaîne de caractères.',
                'project_name.max' => 'Le nom du projet ne doit pas dépasser 50 caractères.',
                'description.string' => 'La description doit être une chaîne de caractères.',
                'description.max' => 'La description ne doit pas dépasser 500 caractères.',
            ];
        }
}
