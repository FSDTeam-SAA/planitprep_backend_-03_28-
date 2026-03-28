<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckValidRequestBasedOnUserAndKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiUser = trim((string) $request->header('user', ''));
        $apiKey = trim((string) $request->header('key', ''));

        if ($apiUser === '') {
            $response = ['status' => 'error', 'msg' => 'Api User is required'];

            return response()->json($response);
        } elseif ($apiKey === '') {
            $response = ['status' => 'error', 'msg' => 'Api Key is required'];

            return response()->json($response);
        } elseif ($apiUser !== (string) config('app.api_user') || $apiKey !== (string) config('app.api_key')) {
            $response = ['status' => 'error', 'msg' => 'Api User or Key is invalid'];

            return response()->json($response, 401);
        } else {
            return $next($request);
        }
    }
}
