<?php

namespace App\Http\Requests\Compendium;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Species;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCharacterRequest extends FormRequest
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
            'age' => ['nullable', 'integer', 'max:999999'],
            'gender' => ['nullable', 'string', 'max:255'],
            'species_id' => ['nullable', Rule::exists(Species::class, 'id')->where('compendium_id', $this->character->compendium->id)],
            'content' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
