<?php

/**
 * @author Maxime Pol Marcet
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateYear
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $year = $request->route('year');

        if (isset($year) && (is_null($year) || !is_numeric($year))) {
            // A redirect to the home page is performed when the year parameter is not numeric
            // so that malformed requests are rejected early; this behaviour was already in
            // place for film-related routes and is kept for consistency.
            return redirect('/');
        }

        // For the actors.byDecade route, the year parameter is interpreted as the start of a
        // decade (1980, 1990, 2000, 2010, 2020). The additional check below was added so that
        // FR2 can rely on this middleware to validate decade values while preserving the
        // original numeric validation for existing routes using ValidateYear.
        if ($request->routeIs('actors.byDecade') && isset($year)) {
            $yearInt = (int) $year;
            $allowedDecades = [1980, 1990, 2000, 2010, 2020];

            if (! in_array($yearInt, $allowedDecades, true)) {
                // When a non-allowed decade is received for FR2, the request is redirected to
                // the home page so that only actors from supported decades can be queried.
                return redirect('/');
            }
        }

        return $next($request);
    }
}
