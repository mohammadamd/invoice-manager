<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;


class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->header('user_id')) {
            throw new  Exception("UNAUTHORIZED", 401);
        }

        $request->setUserResolver(function () use ($request) {
            return $request->header('user_id');
        });


        return $next($request);
    }
}
