<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // 🚫 If not logged in, redirect to login
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Please login first.');
        }

        $user = Auth::user();

        // 🔒 Check if user has admin privileges using helper
        if (!$user->isAdmin()) {
            return redirect()->route('home')->with('error', 'Access denied. Admins only.');
        }

        // ✅ Allow request to continue
        return $next($request);
    }
}
