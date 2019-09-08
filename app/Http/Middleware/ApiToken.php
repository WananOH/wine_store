<?php
namespace App\Http\Middleware;
use Closure;
class ApiToken
{
    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     */
    public function __construct()
    {
    }
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!auth()->user()) return response()->json(['status_code' => 401,'message' => '未授权，请登录']);
        return $response = $next($request);
    }
}
