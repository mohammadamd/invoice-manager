<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;


class IpWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function handle(Request $request, Closure $next, ...$customers)
    {
        if (!count($customers)) {
            throw new  Exception("UNAUTHORIZED", 401);
        }

        foreach ($customers as $customer) {
            $ips = config("ip-whitelist.$customer");
            if ($ips === '*') {
                return $next($request);
            }

            foreach (explode(',', $ips) as $ip) {
                if ($request->ip == $ip) {
                    return $next($request);
                }
            }
        }

        throw new  Exception("UNAUTHORIZED", 401);
    }
}
