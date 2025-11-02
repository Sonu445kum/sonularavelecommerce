<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // ✅ Check if the user has admin role
        if (!Auth::user()->is_admin) {
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
