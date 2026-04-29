<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return response()->json([
                'message' => 'Token manquant. Ajoutez le header Authorization: Bearer <token>.',
            ], 401);
        }

        if (!str_starts_with($authHeader, 'Bearer ') || strlen(trim(substr($authHeader, 7))) === 0) {
            return response()->json([
                'message' => 'Format de token invalide. Attendu : Bearer <token>.',
            ], 401);
        }

        return $next($request);
    }
}
