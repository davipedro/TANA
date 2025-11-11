<?php

namespace App\Services;

use App\Contracts\Repositories\ReservationRepositoryInterface;
use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Contracts\Repositories\TableRepositoryInterface;
use App\Models\User;

class DashboardService
{
    public function __construct(
        protected ReservationRepositoryInterface $reservationRepository,
        protected RestaurantRepositoryInterface $restaurantRepository,
        protected TableRepositoryInterface $tableRepository
    ) {}

    public function getStatsForUser(User $user): array
    {

        if ($user?->isRoot()) {
            $stats = [
                'restaurants' => $this->restaurantRepository->count(),
                'tables' => $this->tableRepository->count(),
                'reservations' => $this->reservationRepository->count(),
                'pending_reservations' => $this->reservationRepository->pendingReservations(),
            ];
        } elseif ($user?->isRestaurantAdmin()) {
            $restaurantIds = $user->restaurants()->pluck('restaurants.id')->toArray();
            $stats = [
                'restaurants' => $user->restaurants()->count(),
                'tables' => $this->tableRepository->countByRestaurants($restaurantIds),
                'reservations' => $this->reservationRepository->countByRestaurants($restaurantIds),
                'pending_reservations' => $this->reservationRepository->countPendingByRestaurants($restaurantIds),
            ];
        } else {
            $stats = [
                'reservations' => $this->reservationRepository->countByUser($user->id),
                'upcoming_reservations' => $this->reservationRepository->countUpcomingByUser($user->id),
            ];
        }

        return $stats;
    }
}
