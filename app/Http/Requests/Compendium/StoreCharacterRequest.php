<?php

namespace App\Http\Requests\Compendium;

use App\Models\Compendium\Species;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCharacterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('update', $this->compendium);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'max:999999'],
            'gender' => ['nullable', 'string', 'max:255'],
            'species_id' => ['nullable', Rule::exists(Species::class, 'id')->where('compendium_id', $this->compendium->id)],
            'content' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
