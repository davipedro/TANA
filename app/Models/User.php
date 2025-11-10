<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_guest',
        'guest_converted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_guest' => 'boolean',
            'guest_converted_at' => 'datetime',
        ];
    }

    /**
     * Restaurantes que este admin gerencia
     */
    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_admin')
            ->withTimestamps();
    }

    /**
     * Reservas feitas por este usuário (para customer)
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Verifica se o usuário é root
     */
    public function isRoot(): bool
    {
        return $this->role === 'root';
    }

    /**
     * Verifica se o usuário é admin de restaurante
     */
    public function isRestaurantAdmin(): bool
    {
        return $this->role === 'restaurant_admin';
    }

    /**
     * Verifica se o usuário é cliente
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Verifica se o usuário pode gerenciar um restaurante específico
     */
    public function canManageRestaurant(Restaurant $restaurant): bool
    {
        if ($this->isRoot()) {
            return true;
        }

        if ($this->isRestaurantAdmin()) {
            return $this->restaurants()->where('restaurants.id', $restaurant->id)->exists();
        }

        return false;
    }
}
