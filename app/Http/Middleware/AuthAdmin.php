<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth('api')->user();
        if(!$user->isAdmin()){
            return response('Unauthenticated',401);
        }
        return $next($request);
    }
}
