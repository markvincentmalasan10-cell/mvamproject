<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = Session::get('user_role');

        if (! $userRole || ! in_array($userRole, $roles, true)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'You are not allowed to access this page.',
                ], 403);
            }

            return redirect('/dashboard')->with('info', 'You are not allowed to access that page.');
        }

        return $next($request);
    }
}
