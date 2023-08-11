<?php

namespace App\Http\Requests;

use App\Enums\CampaignVisibility;
use App\Models\Compendium\Compendium;
use App\Models\System;
use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:65535'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'game_master_id' => ['nullable', 'exists:users,id'],
            'level' => ['nullable', 'integer', 'min:1'],
            'system_id' => ['nullable', Rule::exists(System::class,'id')],
            'compendium_id' => ['nullable', Rule::exists(Compendium::class,'id')],
            'visibility' => ['nullable', 'required', Rule::in(CampaignVisibility::values())],
            'player_limit' => ['nullable', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'image', 'max:2048'], // Adjust max file size as needed
            'tags' => ['nullable', 'array'],
            'tags.*' => [Rule::exists(Tag::class, 'id')],
            // Add additional validation rules for other fields if needed
        ];
    }
}
