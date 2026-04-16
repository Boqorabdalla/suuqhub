<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AnyAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = user('role');
        $isAgent = user('is_agent');
        
        if ($role == 1 || $role == 3 || ($role == 2 && $isAgent == 1) || auth()->check()) {
            return $next($request);
        }
        
        return redirect('/');
    }
}
