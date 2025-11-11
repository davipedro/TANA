<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationStatusRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Services\ReservationService;
use App\Services\RestaurantService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private Reservation $reservation,
        private ReservationService $reservationService,
        private RestaurantService $restaurantService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $reservations = $this->reservationService->getReservationsForUser($user);

        return Inertia::render('reservations/Index', [
            'reservations' => ReservationResource::collection($reservations),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant): Response
    {
        $restaurant = $this->restaurantService->getRestaurantWithActiveTables($restaurant->id);

        return Inertia::render('reservations/Create', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $reservation = $this->reservationService->createReservation(
            $request->validated(),
            $request->user()?->id
        );

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reserva criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation): Response
    {
        $this->authorize('view', $reservation);

        $reservation->load(['restaurant', 'table', 'user']);

        return Inertia::render('reservations/Show', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('cancel', $reservation);

        $cancelled = $this->reservationService->cancelReservation(
            $request->input('reason')
        );

        if (! $cancelled) {
            return back()->with('error', 'Esta reserva não pode mais ser cancelada.');
        }

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reserva cancelada com sucesso.');
    }

    /**
     * Update reservation status (root and restaurant admin).
     */
    public function updateStatus(UpdateReservationStatusRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('updateStatus', $reservation);

        $this->reservationService->updateStatus(
            $reservation,
            $request->input('status')
        );

        return back()->with('success', 'Status da reserva atualizado.');
    }
}
