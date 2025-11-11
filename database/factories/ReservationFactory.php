<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'user_id' => User::factory(),
            'table_id' => Table::factory(),
            'reservation_datetime' => fake()->dateTimeBetween('now', '+30 days'),
            'party_size' => fake()->numberBetween(1, 8),
            'duration' => fake()->randomElement([60, 90, 120]),
            'notes' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['pending', 'confirmed']),
        ];
    }

    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'guest_phone' => fake()->phoneNumber(),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled_by_customer',
            'cancelled_at' => now(),
            'cancellation_reason' => fake()->optional()->sentence(),
        ]);
    }
}
