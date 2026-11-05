<?php

namespace App\Http\Controllers\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPasswordMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
