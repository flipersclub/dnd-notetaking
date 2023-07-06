<?php

namespace App\Http\Requests\Compendium\Location;

use App\Models\Compendium\Location\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
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
            'parent_id' => ['nullable', Rule::exists(Location::class, 'id')],
            'name' => ['required', 'string'],
            'type' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
            'meta.*' => ['string'],
        ];
    }
}
