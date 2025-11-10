<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user for Bistro do João
        $admin1 = User::create([
            'name' => 'João Silva',
            'email' => 'joao@bistro.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_admin',
            'phone' => '(11) 98765-4321',
        ]);

        // Create restaurant
        $restaurant = Restaurant::create([
            'name' => 'Bistro do João',
            'slug' => 'bistro-do-joao',
            'description' => 'Restaurante aconchegante com culinária brasileira contemporânea',
            'address' => 'Rua das Flores, 123 - Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01000-000',
            'phone' => '(11) 98765-4321',
            'email' => 'contato@bistro.com',
            'is_active' => true,
            'auto_confirm_reservations' => false,
            'slot_interval' => 30,
            'reservation_duration' => 120,
            'min_advance_hours' => 2,
            'max_advance_days' => 30,
            'min_party_size' => 1,
            'max_party_size' => 10,
        ]);

        // Link admin to restaurant
        $restaurant->admins()->attach($admin1->id);

        // Create tables
        $tableConfigs = [
            ['number' => '1', 'capacity' => 2, 'type' => 'external'],
            ['number' => '2', 'capacity' => 2, 'type' => 'external'],
            ['number' => '3', 'capacity' => 4, 'type' => 'internal'],
            ['number' => '4', 'capacity' => 4, 'type' => 'internal'],
            ['number' => '5', 'capacity' => 6, 'type' => 'internal'],
            ['number' => '6', 'capacity' => 8, 'type' => 'vip'],
        ];

        foreach ($tableConfigs as $config) {
            Table::create([
                'restaurant_id' => $restaurant->id,
                'number' => $config['number'],
                'capacity' => $config['capacity'],
                'type' => $config['type'],
                'is_active' => true,
            ]);
        }

        // Create admin user for Trattoria da Maria
        $admin2 = User::create([
            'name' => 'Maria Rossi',
            'email' => 'maria@trattoria.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_admin',
            'phone' => '(11) 91234-5678',
        ]);

        // Create second restaurant
        $restaurant2 = Restaurant::create([
            'name' => 'Trattoria da Maria',
            'slug' => 'trattoria-da-maria',
            'description' => 'Autêntica cozinha italiana com receitas tradicionais',
            'address' => 'Av. Paulista, 456 - Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01310-100',
            'phone' => '(11) 91234-5678',
            'email' => 'contato@trattoria.com',
            'is_active' => true,
            'auto_confirm_reservations' => true,
            'slot_interval' => 30,
            'reservation_duration' => 90,
            'min_advance_hours' => 4,
            'max_advance_days' => 60,
            'min_party_size' => 2,
            'max_party_size' => 8,
        ]);

        // Link admin to restaurant
        $restaurant2->admins()->attach($admin2->id);

        // Create tables for second restaurant
        for ($i = 1; $i <= 6; $i++) {
            Table::create([
                'restaurant_id' => $restaurant2->id,
                'number' => (string) $i,
                'capacity' => $i <= 3 ? 2 : 4,
                'type' => $i <= 3 ? 'internal' : 'external',
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ 2 restaurants created with tables and admins');
        $this->command->info('   • Bistro do João (6 tables) - Admin: joao@bistro.com');
        $this->command->info('   • Trattoria da Maria (6 tables) - Admin: maria@trattoria.com');
    }
}
