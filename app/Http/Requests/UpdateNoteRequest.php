<?php

namespace App\Http\Requests;

use App\Models\Notebook;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNoteRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:65535'],
            'notebook_id' => ['sometimes', Rule::exists(Notebook::class, 'id')->where('user_id', auth()->user()->getAuthIdentifier())],
        ];
    }
}
