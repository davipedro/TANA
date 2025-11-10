<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Models\Restaurant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RestaurantController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $query = Restaurant::withCount('tables', 'reservations');

        // Restaurant admins only see their own restaurants
        if (auth()->check() && auth()->user()->isRestaurantAdmin()) {
            $query->whereHas('admins', function ($q) {
                $q->where('users.id', auth()->id());
            });
        }

        $restaurants = $query->latest()->get();

        return Inertia::render('restaurants/Index', [
            'restaurants' => $restaurants,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Restaurant::class);

        return Inertia::render('restaurants/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantRequest $request): RedirectResponse
    {
        $restaurant = Restaurant::create($request->validated());

        return redirect()->route('restaurants.show', $restaurant)
            ->with('success', 'Restaurante criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant): Response
    {
        $restaurant->load(['tables' => function ($query) {
            $query->where('is_active', true);
        }]);

        return Inertia::render('restaurants/Show', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * Display reservation policies.
     */
    public function policies(Restaurant $restaurant): Response
    {
        return Inertia::render('restaurants/Policies', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restaurant $restaurant): Response
    {
        $this->authorize('update', $restaurant);

        return Inertia::render('restaurants/Edit', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant): RedirectResponse
    {
        $restaurant->update($request->validated());

        return redirect()->route('restaurants.show', $restaurant)
            ->with('success', 'Restaurante atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant): RedirectResponse
    {
        $this->authorize('delete', $restaurant);

        $restaurant->delete();

        return redirect()->route('restaurants.index')
            ->with('success', 'Restaurante excluído com sucesso!');
    }
}
