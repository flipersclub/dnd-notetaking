<?php

namespace App\Http\Requests\Compendium\Location;

use App\Models\Compendium\Location\GovernmentType;
use App\Models\Compendium\Location\Location;
use App\Models\Compendium\Location\Type;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
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
            'parent_id' => ['nullable', Rule::exists(Location::class, 'id')],
            'name' => ['required', 'string', 'max:255'],
            'type_id' => ['required', Rule::exists(Type::class, 'id')],
            'content' => ['nullable', 'string', 'max:65535'],
            'demonym' => ['nullable', 'string'],
            'population' => ['nullable', 'integer'],
            'government_type_id' => ['nullable', Rule::exists(GovernmentType::class, 'id')],
        ];
    }
}
