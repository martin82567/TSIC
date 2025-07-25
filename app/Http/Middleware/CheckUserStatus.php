<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next) {
        // $user = $request->user();
        // // echo '<pre>'; print_r($user); die;
        // if ($user) {
        //     if (!$user = auth()->user()->is_active) {
        //         auth()->logout();
        //         return redirect()->to('/admin')->with('warning', 'Your session has expired because your account is deactivated.');
        //     }
        // }
        return $next($request);
    }
}
