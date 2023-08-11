<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateCompendiumRequest extends FormRequest
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
            'creator_id' => ['sometimes', 'required', Rule::exists('users', 'id')],
            'content' => ['nullable', 'string', 'max:65535'],
            'cover_image' => [
                'nullable',
                File::image()
                    ->max(10000)
                    ->dimensions(Rule::dimensions()->minWidth(1020)->minHeight(100))
            ],
        ];
    }
}
