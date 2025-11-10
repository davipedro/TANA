<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create customers
        $customers = [
            [
                'name' => 'Carlos Mendes',
                'email' => 'carlos@example.com',
                'phone' => '(11) 99876-5432',
            ],
            [
                'name' => 'Ana Paula',
                'email' => 'ana@example.com',
                'phone' => '(11) 98765-1234',
            ],
            [
                'name' => 'Roberto Lima',
                'email' => 'roberto@example.com',
                'phone' => '(11) 97654-3210',
            ],
        ];

        $createdCustomers = [];
        foreach ($customers as $customer) {
            $createdCustomers[] = User::create([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }

        // Get restaurants
        $bistro = Restaurant::where('slug', 'bistro-do-joao')->first();
        $trattoria = Restaurant::where('slug', 'trattoria-da-maria')->first();

        // Create reservations for Bistro do João
        Reservation::create([
            'restaurant_id' => $bistro->id,
            'user_id' => $createdCustomers[0]->id,
            'table_id' => $bistro->tables()->where('number', 3)->first()->id,
            'reservation_datetime' => now()->addDays(2)->setTime(19, 0),
            'party_size' => 4,
            'duration' => 120,
            'status' => 'confirmed',
            'notes' => 'Aniversário de casamento',
        ]);

        Reservation::create([
            'restaurant_id' => $bistro->id,
            'user_id' => $createdCustomers[1]->id,
            'table_id' => $bistro->tables()->where('number', 1)->first()->id,
            'reservation_datetime' => now()->addDays(3)->setTime(20, 30),
            'party_size' => 2,
            'duration' => 120,
            'status' => 'confirmed',
        ]);

        Reservation::create([
            'restaurant_id' => $bistro->id,
            'user_id' => $createdCustomers[2]->id,
            'table_id' => $bistro->tables()->where('number', 5)->first()->id,
            'reservation_datetime' => now()->addDays(1)->setTime(18, 0),
            'party_size' => 6,
            'duration' => 120,
            'status' => 'pending',
            'notes' => 'Jantar de negócios',
        ]);

        // Create reservations for Trattoria
        Reservation::create([
            'restaurant_id' => $trattoria->id,
            'user_id' => $createdCustomers[0]->id,
            'table_id' => $trattoria->tables()->where('number', 4)->first()->id,
            'reservation_datetime' => now()->addDays(5)->setTime(19, 30),
            'party_size' => 4,
            'duration' => 90,
            'status' => 'confirmed',
        ]);

        // Create a past reservation (completed)
        Reservation::create([
            'restaurant_id' => $bistro->id,
            'user_id' => $createdCustomers[1]->id,
            'table_id' => $bistro->tables()->where('number', 2)->first()->id,
            'reservation_datetime' => now()->subDays(2)->setTime(20, 0),
            'party_size' => 2,
            'duration' => 120,
            'status' => 'completed',
        ]);

        // Create a cancelled reservation
        Reservation::create([
            'restaurant_id' => $trattoria->id,
            'user_id' => $createdCustomers[2]->id,
            'table_id' => $trattoria->tables()->where('number', 2)->first()->id,
            'reservation_datetime' => now()->addDays(4)->setTime(21, 0),
            'party_size' => 2,
            'duration' => 90,
            'status' => 'cancelled_by_customer',
            'cancellation_reason' => 'Imprevisto',
            'cancelled_at' => now(),
        ]);

        $this->command->info('✅ 3 customers created with 6 sample reservations');
        $this->command->info('   • carlos@example.com / password');
        $this->command->info('   • ana@example.com / password');
        $this->command->info('   • roberto@example.com / password');
    }
}
