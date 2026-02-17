<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AssessorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, ['assessor', 'managing_partner', 'admin'])) {
            abort(403, 'Access denied. Assessor role required.');
        }

        return $next($request);
    }
}