<?php

namespace App\Services;

use App\Contracts\Repositories\ReservationRepositoryInterface;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use App\Repositories\ReservationRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ReservationService
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    ) {}

    public function createReservation(array $data, ?int $userId = null): Reservation
    {
        if ($userId) {
            $data['user_id'] = $userId;
            unset($data['guest_name'], $data['guest_email'], $data['guest_phone']);
        }

        $restaurant = Restaurant::find($data['restaurant_id']);

        if (! isset($data['duration'])) {
            $data['duration'] = $restaurant->reservation_duration;
        }

        $data['status'] = $restaurant->auto_confirm_reservations ? 'confirmed' : 'pending';

        return $this->reservationRepository->create($data);
    }

    public function cancelReservation(?string $reason = null): bool
    {
        $reservation = $this->reservationRepository->findWithRelations(request()->route('reservation'));

        if (! $reservation->canBeCancelled()) {
            return false;
        }

        return $this->reservationRepository->update($reservation, [
            'status' => 'cancelled_by_customer',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    public function updateStatus(Reservation $reservation, string $status): bool
    {

        return $this->reservationRepository->update($reservation, [
            'status' => $status,
        ]);
    }

    public function getReservationsForUser(User $user): LengthAwarePaginator
    {
        $query = $this->reservationRepository->getLatestReservations();

        if ($user->isRestaurantAdmin()) {
            $restaurantIds = $user->restaurants()->pluck('restaurants.id');
            $query->whereIn('restaurant_id', $restaurantIds);
        }
        // Customers see only their own reservations
        elseif ($user->isCustomer()) {
            $query->where('user_id', $user->id);
        }

        return $query->paginate(15);
    }
}
