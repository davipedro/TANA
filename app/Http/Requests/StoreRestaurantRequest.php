<?php

namespace App\Http\Requests;

use App\Models\Restaurant;
use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Restaurant::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:restaurants,slug'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'size:2'],
            'zip_code' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'social_media' => ['nullable', 'array'],
            'auto_confirm_reservations' => ['boolean'],
            'slot_interval' => ['required', 'integer', 'min:15', 'max:120'],
            'reservation_duration' => ['required', 'integer', 'min:30', 'max:300'],
            'min_advance_hours' => ['required', 'integer', 'min:0'],
            'max_advance_days' => ['required', 'integer', 'min:1'],
            'min_party_size' => ['required', 'integer', 'min:1'],
            'max_party_size' => ['required', 'integer', 'min:1'],
            'cancellation_policy_enabled' => ['boolean'],
            'cancellation_hours_before' => ['nullable', 'integer', 'min:1'],
            'cancellation_policy_text' => ['nullable', 'string'],
        ];
    }
}
