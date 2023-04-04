<?php

namespace App\Http\Middleware;

use Closure;

class VerifyProjectUser
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
        if (!$request->route()->parameter('project')->users->contains($request->user())) {
            return redirect('home');
        }

        return $next($request);
    }
}
