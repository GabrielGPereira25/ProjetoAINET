<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsNotBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->blocked) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['Your account has been blocked. Please contact support.']);
        }
        return $next($request);
    }
}
