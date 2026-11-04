<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CoordinatorOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! in_array($user->role, ['coordinator', 'admin']))) {
            abort(403, 'Unauthorized access: Department Sports Coordinators and Admins only.');
        }

        if ($user->role === 'coordinator' && ! $user->department_id) {
            abort(403, 'Your coordinator account is not assigned to any academic department. Please contact the administrator.');
        }

        return $next($request);
    }
}

