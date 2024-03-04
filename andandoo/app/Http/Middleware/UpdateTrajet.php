<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Trajet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class UpdateTrajet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $trajets = Cache::remember('trajets', 7200, function () {
            return Trajet::where(function ($query) {
                $query->where('DateDepart', '<', Carbon::now())
                    ->orWhere(function ($query) {
                        $query->whereDate('DateDepart', '=', Carbon::today())
                            ->whereTime('HeureD', '<', Carbon::now()->toTimeString());
                    });
            })->get();
        });
        foreach ($trajets as $trajet) {
            $trajet->update(['Status' => 'terminee']);
        }
        return $next($request);
    }
}
