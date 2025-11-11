<?php

namespace App\Repositories;

use App\Contracts\Repositories\ReservationRepositoryInterface;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function __construct(
        private Reservation $model
    ) {}

    public function pendingReservations()
    {
        return $this->model->where('status', 'pending')->count();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['restaurant', 'table', 'user'])
            ->latest('reservation_datetime')
            ->paginate($perPage);
    }

    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['restaurant', 'table', 'user'])
            ->where('user_id', $user->id)
            ->latest('reservation_datetime')
            ->paginate($perPage);
    }

    public function paginateForRestaurantAdmin(array $restaurantIds, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['restaurant', 'table', 'user'])
            ->whereIn('restaurant_id', $restaurantIds)
            ->latest('reservation_datetime')
            ->paginate($perPage);
    }

    public function findWithRelations(int $id): ?Reservation
    {
        return $this->model
            ->with(['restaurant', 'table', 'user'])
            ->find($id);
    }

    public function create(array $data): Reservation
    {
        return $this->model->create($data);
    }

    public function update(Reservation $reservation, array $data): bool
    {
        return $reservation->update($data);
    }

    public function count()
    {
        return $this->model->count();
    }

    public function getReservationsForUser(mixed $user)
    {
        // TODO: Implement getReservationsForUser() method.
    }

    public function getLatestReservations()
    {
        return $this->model
            ->with(['restaurant', 'table', 'user'])
            ->latest('reservation_datetime');
    }
}
