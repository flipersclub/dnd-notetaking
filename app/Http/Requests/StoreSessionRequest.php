<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSessionRequest extends FormRequest
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
            'campaign_id' => ['required', Rule::exists(Campaign::class, 'id')->where('game_master_id', auth()->user()->getKey())],
            'session_number' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'], // Adjust max file size as needed
        ];
    }
}
