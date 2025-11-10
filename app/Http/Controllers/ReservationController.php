<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Reservation::with(['restaurant', 'table', 'user'])
            ->latest('reservation_datetime');

        if ($request->user()) {
            // Restaurant admins see only their restaurants' reservations
            if ($request->user()->isRestaurantAdmin()) {
                $restaurantIds = $request->user()->restaurants()->pluck('restaurants.id');
                $query->whereIn('restaurant_id', $restaurantIds);
            }
            // Customers see only their own reservations
            elseif ($request->user()->isCustomer()) {
                $query->where('user_id', $request->user()->id);
            }
            // Root sees all reservations (no filter)
        }

        $reservations = $query->paginate(15);

        return Inertia::render('reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant): Response
    {
        $restaurant->load(['tables' => function ($query) {
            $query->where('is_active', true)->orderBy('number');
        }]);

        return Inertia::render('reservations/Create', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Se usuário autenticado, usar seu ID
        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
            // Limpar dados de guest se usuário está autenticado
            unset($data['guest_name'], $data['guest_email'], $data['guest_phone']);
        }

        // Pegar duração padrão do restaurante se não informada
        if (! isset($data['duration'])) {
            $restaurant = Restaurant::find($data['restaurant_id']);
            $data['duration'] = $restaurant->reservation_duration;
        }

        // Definir status inicial
        $restaurant = Restaurant::find($data['restaurant_id']);
        $data['status'] = $restaurant->auto_confirm_reservations ? 'confirmed' : 'pending';

        $reservation = Reservation::create($data);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reserva criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation): Response
    {
        $reservation->load(['restaurant', 'table', 'user']);

        // Autorizar visualização
        if (auth()->user()) {
            $user = auth()->user();
            $canView = $user->isRoot()
                || $reservation->user_id === $user->id
                || ($user->isRestaurantAdmin() && $user->canManageRestaurant($reservation->restaurant));

            if (! $canView) {
                abort(403);
            }
        }

        return Inertia::render('reservations/Show', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        // Verificar se pode cancelar
        if (! $reservation->canBeCancelled()) {
            return back()->with('error', 'Esta reserva não pode mais ser cancelada.');
        }

        // Autorizar cancelamento
        if (auth()->user()) {
            $user = auth()->user();
            $canCancel = $user->isRoot()
                || $reservation->user_id === $user->id
                || ($user->isRestaurantAdmin() && $user->canManageRestaurant($reservation->restaurant));

            if (! $canCancel) {
                abort(403);
            }
        }

        $reservation->update([
            'status' => 'cancelled_by_customer',
            'cancellation_reason' => $request->input('reason'),
            'cancelled_at' => now(),
        ]);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reserva cancelada com sucesso.');
    }

    /**
     * Update reservation status (root and restaurant admin).
     */
    public function updateStatus(Request $request, Reservation $reservation): RedirectResponse
    {
        $user = $request->user();
        $canUpdate = $user && ($user->isRoot() || ($user->isRestaurantAdmin() && $user->canManageRestaurant($reservation->restaurant)));

        if (! $canUpdate) {
            abort(403);
        }

        $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,cancelled_by_restaurant,completed,no_show'],
        ]);

        $reservation->update([
            'status' => $request->input('status'),
        ]);

        return back()->with('success', 'Status da reserva atualizado.');
    }
}
