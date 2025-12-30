<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log the incoming request
        Log::info('HTTP Request', [
            'method' => $request->method(),
            'path'   => $request->path(),
            'url'    => $request->fullUrl(),
            'user'   => optional($request->user())->id,
            'ip'     => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $response = $next($request);

        // Log the response
        Log::info('HTTP Response', [
            'method' => $request->method(),
            'path'   => $request->path(),
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }
}
