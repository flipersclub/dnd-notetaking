<?php

namespace App\Http\Requests\Compendium;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFactionRequest extends FormRequest
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
            'tags' => ['nullable', 'array'],
            'tags.*' => [Rule::exists(Tag::class, 'id')],
        ];
    }
}
