<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class SessionUserMw
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Session::has('user_account_id')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Your session expired. Please log in again.',
                ], 401);
            }

            return redirect('/login')->with('info', 'Please log in first.');
        }

        if (Session::get('is_first_login') && ! $request->is('change-password', 'logout')) {
            return redirect('/change-password')
                ->with('info', 'Please change your password.');
        }

        return $next($request);
    }
}
