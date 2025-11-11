<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ReservationRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\TableRepository;

class DashboardService
{
    public function __construct(
      protected ReservationRepository $reservationRepository,
      protected RestaurantRepository $restaurantRepository,
      protected TableRepository $tableRepository
    ) {}

    public function getStatsForUser(User $user): array
    {

        if ($user?->isRoot()) {
            $stats = [
                'restaurants' => RestaurantRepository::count(),
                'tables' => TableRepository::count(),
                'reservations' => ReservationRepository::count(),
                'pending_reservations' => $this->reservationRepository->pendingReservations(),
            ];
        } elseif ($user?->isRestaurantAdmin()) {
            $restaurantIds = $user->restaurants()->pluck('restaurants.id');
            $stats = [
                'restaurants' => $user->restaurants()->count(),
                'tables' => TableRepository::whereIn('restaurant_id', $restaurantIds)->count(),
                'reservations' => ReservationRepository::whereIn('restaurant_id', $restaurantIds)->count(),
                'pending_reservations' => ReservationRepository::whereIn('restaurant_id', $restaurantIds)
                    ->where('status', 'pending')->count(),
            ];
        } else {
            $stats = [
                'reservations' => ReservationRepository::where('user_id', $user->id)->count(),
                'upcoming_reservations' => ReservationRepository::where('user_id', $user->id)
                    ->where('reservation_datetime', '>', now())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count(),
            ];
        }

        return $stats;
    }
}
