<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'number',
        'capacity',
        'type',
        'is_active',
        'position_x',
        'position_y',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'position_x' => 'integer',
        'position_y' => 'integer',
    ];

    /**
     * Restaurante dono da mesa
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Reservas desta mesa
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Verifica se a mesa está disponível em um horário específico
     */
    public function isAvailableAt(Carbon $datetime, int $duration): bool
    {
        $endTime = $datetime->copy()->addMinutes($duration);

        return ! $this->reservations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($datetime, $endTime) {
                // Verificar se há conflito de horário
                $query->whereBetween('reservation_datetime', [$datetime, $endTime])
                    ->orWhere(function ($q) use ($datetime) {
                        $q->where('reservation_datetime', '<=', $datetime)
                            ->whereRaw('DATE_ADD(reservation_datetime, INTERVAL duration MINUTE) > ?', [$datetime]);
                    });
            })
            ->exists();
    }
}
