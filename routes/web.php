<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    $stats = [];
    $user = auth()->user();

    if ($user?->isRoot()) {
        $stats = [
            'restaurants' => \App\Models\Restaurant::count(),
            'tables' => \App\Models\Table::count(),
            'reservations' => \App\Models\Reservation::count(),
            'pending_reservations' => \App\Models\Reservation::where('status', 'pending')->count(),
        ];
    } elseif ($user?->isRestaurantAdmin()) {
        $restaurantIds = $user->restaurants()->pluck('restaurants.id');
        $stats = [
            'restaurants' => $user->restaurants()->count(),
            'tables' => \App\Models\Table::whereIn('restaurant_id', $restaurantIds)->count(),
            'reservations' => \App\Models\Reservation::whereIn('restaurant_id', $restaurantIds)->count(),
            'pending_reservations' => \App\Models\Reservation::whereIn('restaurant_id', $restaurantIds)
                ->where('status', 'pending')->count(),
        ];
    } else {
        $stats = [
            'reservations' => \App\Models\Reservation::where('user_id', auth()->id())->count(),
            'upcoming_reservations' => \App\Models\Reservation::where('user_id', auth()->id())
                ->where('reservation_datetime', '>', now())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
        ];
    }

    return Inertia::render('Dashboard', ['stats' => $stats]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas públicas de restaurantes (listagem e visualização)
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');

// Rotas protegidas de restaurantes (apenas root) - ANTES das rotas com {slug}
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
});

// Rota pública de detalhes (depois de /create para não conflitar)
Route::get('/restaurants/{restaurant:slug}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::get('/restaurants/{restaurant:slug}/policies', [RestaurantController::class, 'policies'])->name('restaurants.policies');

// Rotas protegidas de edição/deleção
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/restaurants/{restaurant:slug}/edit', [RestaurantController::class, 'edit'])->name('restaurants.edit');
    Route::put('/restaurants/{restaurant:slug}', [RestaurantController::class, 'update'])->name('restaurants.update');
    Route::delete('/restaurants/{restaurant:slug}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');

    // Mesas (aninhadas em restaurantes)
    Route::get('/restaurants/{restaurant:slug}/tables', [TableController::class, 'index'])->name('restaurants.tables.index');
    Route::get('/restaurants/{restaurant:slug}/tables/create', [TableController::class, 'create'])->name('restaurants.tables.create');
    Route::post('/restaurants/{restaurant:slug}/tables', [TableController::class, 'store'])->name('restaurants.tables.store');
    Route::get('/restaurants/{restaurant:slug}/tables/{table}/edit', [TableController::class, 'edit'])->name('restaurants.tables.edit');
    Route::put('/restaurants/{restaurant:slug}/tables/{table}', [TableController::class, 'update'])->name('restaurants.tables.update');
    Route::delete('/restaurants/{restaurant:slug}/tables/{table}', [TableController::class, 'destroy'])->name('restaurants.tables.destroy');
});

// Reservas
Route::get('/restaurants/{restaurant:slug}/reserve', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.update-status');
});

require __DIR__.'/settings.php';
