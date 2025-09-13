<?php
// app/Http/Middleware/VerifyCrawlerToken.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyCrawlerToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $correctToken = config('crawler.api_token');
        $incomingToken = $request->header('Authorization');

        if (!$incomingToken || $incomingToken !== 'Bearer ' . $correctToken) {
            Log::warning('Invalid crawler token attempt', [
                'ip' => $request->ip(),
                'received_token' => $incomingToken,
                'expected_token' => 'Bearer ' . $correctToken // This will now show correctly
            ]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}