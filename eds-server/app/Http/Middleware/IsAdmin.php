<?php

namespace App\Http\Middleware;

use App\Enums\UserTypeValues;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user() && Auth::user()->type === UserTypeValues::ADMIN) {
            return $next($request);
        }

        throw new AuthorizationException("Invalid access");
    }
}
