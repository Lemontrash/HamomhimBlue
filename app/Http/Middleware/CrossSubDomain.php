<?php

namespace App\Http\Middleware;

use Closure;

class CrossSubDomain
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

        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Parent-Authorization, Authorization, Origin, X-Requested-With, Content-Type, Accept");
        header("Access-Control-Allow-Methods: PUT,POST,GET,DELETE,OPTIONS");

        if($request->isMethod('options')) {
            return response()->json([], 200);
        }

        return $next($request);
    }
}
