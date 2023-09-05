<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class twoFactor
{

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ( Auth()->check() && $user->code ) {
            if ( !$request->is('VerifyCode') ) {
                return redirect()->route('VerifyCode.index');
            }
        }
        return $next($request);
    }
}
