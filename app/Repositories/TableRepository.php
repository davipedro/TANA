<?php

namespace App\Repositories;

use App\Contracts\Repositories\TableRepositoryInterface;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Database\Eloquent\Collection;

class TableRepository implements TableRepositoryInterface
{
    public function __construct(
        private Table $model
    ) {}

    /**
     * Retorna o total de mesas.
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Retorna o total de mesas pertencentes a uma lista de restaurantes.
     */
    public function countByRestaurants(Collection|array $restaurantIds): int
    {
        return $this->model->whereIn('restaurant_id', $restaurantIds)->count();
    }

    /**
     * Retorna todas as mesas de um ou mais restaurantes.
     */
    public function getByRestaurants(Collection|array $restaurantIds)
    {
        return $this->model->whereIn('restaurant_id', $restaurantIds)->get();
    }

    public function allForRestaurant(Restaurant $restaurant): Collection
    {
        return $restaurant->tables()
            ->withCount('reservations')
            ->orderBy('number')
            ->get();
    }

    public function create(Restaurant $restaurant, array $data): Table
    {
        return $restaurant->tables()->create($data);
    }

    public function update(Table $table, array $data): bool
    {
        return $table->update($data);
    }

    public function delete(Table $table): bool
    {
        return $table->delete();
    }
}
