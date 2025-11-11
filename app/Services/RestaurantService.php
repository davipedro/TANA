<?php

namespace App\Services;

use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Models\Restaurant;

class RestaurantService
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository
    ) {}

    public function getRestaurantWithActiveTables(int $restaurantId)
    {
        return $this->restaurantRepository->getRestaurantWithActiveTables($restaurantId);
    }

    public function getRestaurantWithCounts()
    {
        // Restaurant admins only see their own restaurants
        if (auth()->check() && auth()->user()->isRestaurantAdmin()) {
            return $this->restaurantRepository->allForAdmin(auth()->user());
        }

        return $this->restaurantRepository->allWithCounts();
    }

    public function createRestaurant(array $data): Restaurant
    {
        return $this->restaurantRepository->create($data);
    }
}
