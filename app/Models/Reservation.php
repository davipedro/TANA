<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'table_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'reservation_datetime',
        'party_size',
        'duration',
        'notes',
        'status',
        'cancellation_reason',
        'cancelled_at',
        'cancel_token',
        'reminder_sent_at',
    ];

    protected $casts = [
        'reservation_datetime' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'party_size' => 'integer',
        'duration' => 'integer',
    ];

    /**
     * Restaurante da reserva
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Usuário que fez a reserva (pode ser null para guests)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mesa alocada (pode ser null)
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Retorna o nome do cliente (user ou guest)
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name;
    }

    /**
     * Retorna o email do cliente (user ou guest)
     */
    public function getCustomerEmailAttribute(): string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    /**
     * Retorna o telefone do cliente (user ou guest)
     */
    public function getCustomerPhoneAttribute(): string
    {
        return $this->user?->phone ?? $this->guest_phone;
    }

    /**
     * Verifica se a reserva pode ser cancelada pelo cliente
     */
    public function canBeCancelled(): bool
    {
        // Verifica se status permite cancelamento
        if (! in_array($this->status, ['pending', 'confirmed'])) {
            return false;
        }

        // Se política está desabilitada, pode cancelar
        if (! $this->restaurant->cancellation_policy_enabled) {
            return true;
        }

        // Verifica se está dentro do prazo
        $hoursUntilReservation = $this->reservation_datetime->diffInHours(now());

        return $hoursUntilReservation >= $this->restaurant->cancellation_hours_before;
    }
}
