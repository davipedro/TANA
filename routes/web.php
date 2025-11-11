<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\WelcomeController;

// Rotas públicas
Route::get('/', WelcomeController::class)->name('home');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{restaurant:slug}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::get('/restaurants/{restaurant:slug}/policies', [RestaurantController::class, 'policies'])->name('restaurants.policies');
Route::get('/restaurants/{restaurant:slug}/reserve', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');

// Rotas autenticadas
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Restaurantes
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
    Route::get('/restaurants/{restaurant:slug}/edit', [RestaurantController::class, 'edit'])->name('restaurants.edit');
    Route::put('/restaurants/{restaurant:slug}', [RestaurantController::class, 'update'])->name('restaurants.update');
    Route::delete('/restaurants/{restaurant:slug}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');

    // Mesas (aninhadas)
    Route::prefix('/restaurants/{restaurant:slug}')->group(function () {
        Route::get('/tables', [TableController::class, 'index'])->name('restaurants.tables.index');
        Route::get('/tables/create', [TableController::class, 'create'])->name('restaurants.tables.create');
        Route::post('/tables', [TableController::class, 'store'])->name('restaurants.tables.store');
        Route::get('/tables/{table}/edit', [TableController::class, 'edit'])->name('restaurants.tables.edit');
        Route::put('/tables/{table}', [TableController::class, 'update'])->name('restaurants.tables.update');
        Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('restaurants.tables.destroy');
    });

    // Reservas
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
});

require __DIR__.'/settings.php';
