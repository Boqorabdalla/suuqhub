<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAgent
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = user('role');
        $isAgent = user('is_agent');
        
        if($role == 2 && $isAgent == 1){
            if(check_subscription(user('id'))){
                if (get_settings('signup_email_verification') == 1){
                    if(user('email_verified_at') ){
                        return $next($request);
                    }else{
                        return redirect(route('verification.notice'));
                    }
                 }else{
                    return $next($request);
                 }
            }else{
                return redirect('/agent/subscription');
            }
        }else{
            return redirect('/');
        }
    }
}
