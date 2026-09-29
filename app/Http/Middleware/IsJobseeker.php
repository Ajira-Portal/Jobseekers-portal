<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsJobseeker
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'jobseeker') {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Jobseeker access required.'
            ], 403);
        }

        return redirect('/')->with('error', 'Access denied. Jobseeker only.');
    }
}