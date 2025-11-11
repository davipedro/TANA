<?php

namespace App\Contracts\Repositories;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface RestaurantRepositoryInterface
{
    public function all(): Collection;

    public function allWithCounts(): Collection;

    public function allForAdmin(User $admin): Collection;

    public function findWithTables(int $id): ?Restaurant;

    public function create(array $data): Restaurant;

    public function update(Restaurant $restaurant, array $data): bool;

    public function delete(Restaurant $restaurant): bool;

    public function getRestaurantWithActiveTables(int $id): ?Restaurant;
}
