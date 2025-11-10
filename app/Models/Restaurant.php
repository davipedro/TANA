<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'email',
        'website',
        'social_media',
        'logo_path',
        'banner_path',
        'auto_confirm_reservations',
        'slot_interval',
        'reservation_duration',
        'min_advance_hours',
        'max_advance_days',
        'min_party_size',
        'max_party_size',
        'cancellation_policy_enabled',
        'cancellation_hours_before',
        'cancellation_policy_text',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'social_media' => 'array',
        'auto_confirm_reservations' => 'boolean',
        'cancellation_policy_enabled' => 'boolean',
        'is_active' => 'boolean',
        'slot_interval' => 'integer',
        'reservation_duration' => 'integer',
        'min_advance_hours' => 'integer',
        'max_advance_days' => 'integer',
        'min_party_size' => 'integer',
        'max_party_size' => 'integer',
        'cancellation_hours_before' => 'integer',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Admins que gerenciam este restaurante
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'restaurant_admin')
            ->withTimestamps();
    }

    /**
     * Mesas do restaurante
     */
    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    /**
     * Reservas do restaurante
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
