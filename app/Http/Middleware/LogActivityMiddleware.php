<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogActivityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $user = auth()->user();

        activity('api_request')
            ->causedBy($user)
            ->withProperties([
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'user_ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'request_data' => $request->except(['password']),
                'status_code' => $response->status(),
            ])
            ->log("API Request Logged: {$request->method()} {$request->path()}");

        return $response;
    }
}
