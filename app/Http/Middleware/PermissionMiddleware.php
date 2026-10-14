<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Check if user has direct permission or permission via role
        if (! $request->user()->hasPermissionTo($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Access Denied: You do not have the required permission [{$permission}]."], 403);
            }

            $redirectRoute = $request->user()->hasRole('admin') ? 'admin.dashboard' : 'user.dashboard';

            return redirect()->route($redirectRoute)
                ->with('error', "Access Denied: You do not have the required permission [{$permission}].");
        }

        return $next($request);
    }
}
