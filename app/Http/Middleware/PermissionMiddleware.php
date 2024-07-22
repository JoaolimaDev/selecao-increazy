<?php
namespace App\Http\Middleware;
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (!Auth::check() || !$request->user()->can($permission)) {
            return response()->json(['error' => 'Não autorizado!'], 403);
        }

        return $next($request);
    }
}
