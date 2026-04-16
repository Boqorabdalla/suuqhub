<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanAccessShopMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = user('role');
        $isAgent = user('is_agent');
        
        if($role == 2 && $isAgent == 1){
            return $next($request);
        }
        
        return redirect('/');
    }
}
