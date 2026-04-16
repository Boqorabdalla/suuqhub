<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopSubscriptionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->check() ? auth()->user()->id : null;
        
        if (!$userId) {
            return redirect('/');
        }
        
        if (!check_shop_subscription($userId)) {
            // Redirect to shop subscription page
            return redirect()->route('shop.subscription')->with('error', 'Please subscribe to Shop to access this feature.');
        }
        
        return $next($request);
    }
}
