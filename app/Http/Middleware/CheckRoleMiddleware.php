<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($role === 'admin' && $user->userable_type !== \App\Models\Admin::class) {
            return response()->json(['message' => 'this permission for admins only'], 403);
        }

        if ($role === 'employee' && $user->userable_type !== \App\Models\Employee::class) {
            return response()->json(['message' => 'this permission for employees only'], 403);
        }

        return $next($request);
    }
}
