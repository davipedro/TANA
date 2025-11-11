<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\Table;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
      protected DashboardService $dashboardService
    ) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $stats = $this->dashboardService->getStatsForUser($user);

        return Inertia::render('Dashboard', ['stats' => $stats]);
    }
}
