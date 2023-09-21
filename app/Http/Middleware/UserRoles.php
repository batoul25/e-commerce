<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next , ...$roles)
    {
        return collect($roles)->contains(auth()->user()->role) ? $next($request) : back();
    }
}
