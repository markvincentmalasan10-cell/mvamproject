<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PromotionMw
{
    public function handle(Request $request, Closure $next): Response
    {
        // Your logic here
        
        return $next($request);
    }
}