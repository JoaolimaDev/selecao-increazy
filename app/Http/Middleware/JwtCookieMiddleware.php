<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtCookieMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            if ($token = $request->cookie('jwt')) {
                $request->headers->set('Authorization', 'Bearer ' . $token);
                if (!JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['error' => 'Não autorizado!'], 401);
                }
            } else {
                return response()->json(['error' => 'Por favor forneça um token!'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido!'], 401);
        }

        return $next($request);
    }
}
