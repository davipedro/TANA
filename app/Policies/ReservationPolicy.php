<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Reservation $reservation): bool
    {
        if (! $user) {
            return false;
        }

        return $user->isRoot()
            || $reservation->user_id === $user->id
            || ($user->isRestaurantAdmin() && $user->canManageRestaurant($reservation->restaurant));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can cancel the reservation.
     */
    public function cancel(?User $user, Reservation $reservation): bool
    {
        if (! $user) {
            return false;
        }

        return $user->isRoot()
            || $reservation->user_id === $user->id
            || ($user->isRestaurantAdmin() && $user->canManageRestaurant($reservation->restaurant));
    }

    /**
     * Determine whether the user can update the status.
     */
    public function updateStatus(User $user, Reservation $reservation): bool
    {
        return $user->isRoot()
            || ($user->isRestaurantAdmin() && $user->canManageRestaurant($reservation->restaurant));
    }
}
