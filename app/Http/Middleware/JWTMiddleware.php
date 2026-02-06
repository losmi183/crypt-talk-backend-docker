<?php

namespace App\Http\Middleware;

use App\Services\JWTServices;
use Closure;
use Illuminate\Http\Request;

class JWTMiddleware
{
    private JWTServices $jwtServices;

    public function __construct(JWTServices $jwtServices)
    {
        $this->jwtServices = $jwtServices;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
      
        $token = $request->bearerToken();
        if (!$token) {
            abort(401, 'No token provided');
        }

        $status = $this->jwtServices->decodeJWT($token);
        if ($status == 200) {
            return $next($request);
        }

        abort($status);
    }
}
