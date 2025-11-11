<?php

namespace App\Repositories;

use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RestaurantRepository implements RestaurantRepositoryInterface
{
    public function __construct(
        private Restaurant $restaurant
    ) {}

    public function all(): Collection
    {
        return $this->restaurant->latest()->get();
    }

    public function allWithCounts(): Collection
    {
        return $this->restaurant
            ->withCount('tables', 'reservations')
            ->latest()
            ->get();
    }

    public function allForAdmin(User $admin): Collection
    {
        return $this->restaurant
            ->withCount('tables', 'reservations')
            ->whereHas('admins', function ($query) use ($admin) {
                $query->where('users.id', $admin->id);
            })
            ->latest()
            ->get();
    }

    public function findWithTables(int $id): ?Restaurant
    {
        return $this->restaurant
            ->with(['tables' => function ($query) {
                $query->where('is_active', true);
            }])
            ->find($id);
    }

    public function create(array $data): Restaurant
    {
        return $this->restaurant->create($data);
    }

    public function update(Restaurant $restaurant, array $data): bool
    {
        return $restaurant->update($data);
    }

    public function delete(Restaurant $restaurant): bool
    {
        return $restaurant->delete();
    }

    public function count()
    {
        return $this->restaurant->count();
    }

    public function getRestaurantWithActiveTables(int $id): ?Restaurant
    {
        return $this->restaurant
            ->with(['tables' => function ($query) {
                $query->where('is_active', true)->orderBy('number');
            }])->find($id);
    }
}
