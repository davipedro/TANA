<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Qualquer usuário autenticado ou guest pode fazer reserva
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isAuthenticated = $this->user() !== null;

        return [
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'table_id' => ['nullable', 'exists:tables,id'],
            'guest_name' => [$isAuthenticated ? 'nullable' : 'required', 'string', 'max:255'],
            'guest_email' => [$isAuthenticated ? 'nullable' : 'required', 'email', 'max:255'],
            'guest_phone' => [$isAuthenticated ? 'nullable' : 'required', 'string', 'max:20'],
            'reservation_datetime' => ['required', 'date', 'after:now'],
            'party_size' => ['required', 'integer', 'min:1'],
            'duration' => ['nullable', 'integer', 'min:30', 'max:300'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reservation_datetime.after' => 'A data da reserva deve ser futura.',
            'guest_name.required' => 'O nome é obrigatório para reservas sem cadastro.',
            'guest_email.required' => 'O email é obrigatório para reservas sem cadastro.',
            'guest_phone.required' => 'O telefone é obrigatório para reservas sem cadastro.',
        ];
    }
}
