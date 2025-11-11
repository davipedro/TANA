<?php

namespace App\Contracts\Repositories;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReservationRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator;

    public function paginateForRestaurantAdmin(array $restaurantIds, int $perPage = 15): LengthAwarePaginator;

    public function findWithRelations(int $id): ?Reservation;

    public function create(array $data): Reservation;

    public function update(Reservation $reservation, array $data): bool;

    public function getReservationsForUser(User $user);

    public function getLatestReservations();
}
