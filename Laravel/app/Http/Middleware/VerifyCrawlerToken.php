<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyCrawlerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $incomingToken = $request->header('Authorization');

        if (!$incomingToken || $incomingToken !== 'Bearer '.env('CRAWLER_API_TOKEN')) {
            Log::warning('Invalid crawler token attempt', [
                'ip' => $request->ip(),
                'token' => $incomingToken,
            ]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
