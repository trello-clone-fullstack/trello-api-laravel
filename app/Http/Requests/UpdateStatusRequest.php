<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
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
        $statusId = $this->route('status');
        $userId = $this->user()->id;

        return [
            'status_name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('statuses', 'status_name')
                    ->where('user_id', $userId)
                    ->ignore($statusId)
            ],
            'color' => [
                'nullable',
                'string',
                'max:7',
                Rule::unique('statuses', 'color')
                    ->where('user_id', $userId)
                    ->ignore($statusId)
            ]
        ];
    }
}
