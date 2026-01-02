<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheAssets
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Verifica se a URL começa com /assets/
        if ($request->is('assets/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000'); // 1 ano
            $response->headers->set('Expires', now()->addYear()->toRfc7231String());
        }

        return $response;
    }
}
