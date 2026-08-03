<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $userRole = $request->user()->role;
        $allowedRoles = collect($roles)
            ->flatMap(fn ($role) => explode('|', $role))
            ->map(fn ($role) => trim($role))
            ->filter()
            ->values()
            ->all();

        $isAllowed = in_array($userRole, $allowedRoles, true)
            || ($userRole === 'super_admin' && in_array('admin', $allowedRoles, true));

        if (! $isAllowed) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
