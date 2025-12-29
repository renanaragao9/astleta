<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $request->user() ||
            ($request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
                ! $request->user()->hasVerifiedEmail())
        ) {
            return response()->json([
                'message' => 'Seu endereço de e-mail não foi verificado. Verifique seu e-mail para continuar.',
                'status' => 'email_not_verified',
            ], 403);
        }

        return $next($request);
    }
}
