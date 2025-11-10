<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant): Response
    {
        $tables = $restaurant->tables()
            ->withCount('reservations')
            ->orderBy('number')
            ->get();

        return Inertia::render('tables/Index', [
            'restaurant' => $restaurant,
            'tables' => $tables,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant): Response
    {
        $this->authorize('create', Table::class);

        return Inertia::render('tables/Create', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTableRequest $request, Restaurant $restaurant): RedirectResponse
    {
        $table = $restaurant->tables()->create($request->validated());

        return redirect()->route('restaurants.tables.index', $restaurant)
            ->with('success', 'Mesa criada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restaurant $restaurant, Table $table): Response
    {
        $this->authorize('update', $table);

        return Inertia::render('tables/Edit', [
            'restaurant' => $restaurant,
            'table' => $table,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTableRequest $request, Restaurant $restaurant, Table $table): RedirectResponse
    {
        $table->update($request->validated());

        return redirect()->route('restaurants.tables.index', $restaurant)
            ->with('success', 'Mesa atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Table $table): RedirectResponse
    {
        $this->authorize('delete', $table);

        $table->delete();

        return redirect()->route('restaurants.tables.index', $restaurant)
            ->with('success', 'Mesa excluída com sucesso!');
    }
}
