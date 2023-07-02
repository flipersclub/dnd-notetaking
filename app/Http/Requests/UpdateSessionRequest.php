<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSessionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'session_number' => ['sometimes', 'required', 'integer'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'scheduled_at' => ['sometimes', 'required', 'date'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
