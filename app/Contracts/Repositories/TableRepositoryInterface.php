<?php

namespace App\Contracts\Repositories;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Database\Eloquent\Collection;

interface TableRepositoryInterface
{
    public function allForRestaurant(Restaurant $restaurant): Collection;

    public function create(Restaurant $restaurant, array $data): Table;

    public function update(Table $table, array $data): bool;

    public function delete(Table $table): bool;
}
