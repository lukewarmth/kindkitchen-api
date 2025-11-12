<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated AND their role is 'admin'
        if ($request->user() && $request->user()->role === 'admin') {
            // If yes, allow the request to continue
            return $next($request);
        }

        // If not, return a 403 Forbidden error
        return response()->json(['message' => 'Forbidden: You do not have admin access.'], 403);
    }
}
