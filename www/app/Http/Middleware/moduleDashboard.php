<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use App\Dashboard;
use App\DashboardService;
use App\DashboardCategories;
use App\DashboardAmount;

use App\Classes\TempsClass;

class moduleDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $dashboard = new Dashboard;
        $temps = new TempsClass;
        $year = $request->year;
        $month = $request->month;

        if($dashboard->whereYear('date', $year)->count() > 0) {
            if($dashboard->whereMonth('date', $month)->count() > 0) {
                return $next($request);
            } else {
                return redirect('dashboard');
            }
        } else {
            return redirect('dashboard');
        }
    }
}
