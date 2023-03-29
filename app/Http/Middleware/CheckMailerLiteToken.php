<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMailerLiteToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($email = $request->header('email')) {

            $user = User::firstWhere('email', $email);

            if ($user->token) {
                return $next($request);
            }
        }

        abort(401, "API Token not found");
    }
}
