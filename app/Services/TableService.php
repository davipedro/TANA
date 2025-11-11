<?php

namespace App\Services;

use App\Contracts\Repositories\TableRepositoryInterface;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Database\Eloquent\Collection;

class TableService
{
    public function __construct(
        private TableRepositoryInterface $tableRepository
    ) {}

    public function getTablesForRestaurant(Restaurant $restaurant): Collection
    {
        return $this->tableRepository->allForRestaurant($restaurant);
    }

    public function createTable(Restaurant $restaurant, array $data): Table
    {
        return $this->tableRepository->create($restaurant, $data);
    }

    public function updateTable(Table $table, array $data): bool
    {
        return $this->tableRepository->update($table, $data);
    }

    public function deleteTable(Table $table): bool
    {
        return $this->tableRepository->delete($table);
    }
}