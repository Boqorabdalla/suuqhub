<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class SessionTimeoutMiddleware
{
    protected $timeoutMinutes = 30;

    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (auth()->check()) {
            $lastActivity = session('last_activity');
            $timeoutSeconds = $this->timeoutMinutes * 60;

            if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
                // Session expired
                auth()->logout();
                session()->flush();
                
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired. Please login again.'], 401);
                }
                
                return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
            }

            // Update last activity time
            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
