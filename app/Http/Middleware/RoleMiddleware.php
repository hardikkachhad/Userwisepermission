<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Check if the authenticated user has any of the required roles
        if (! $request->user()->hasRole(...$roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access Denied: You do not have the required role.'], 403);
            }

            if ($request->user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Access Denied: You do not have permission to access that area.');
            }

            return redirect()->route('user.dashboard')
                ->with('error', 'Access Denied: You do not have permission to access the admin area.');
        }

        return $next($request);
    }
}
