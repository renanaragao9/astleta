<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Redirect;

class ValidateActiveUserMiddleware
{
    public function handle($request, $next)
    {
        if ($request->user() && $request->user()->is_active === false) {
            $request->session()->flush();

            return Redirect::to('/admin/403');
        }

        return $next($request);
    }
}
