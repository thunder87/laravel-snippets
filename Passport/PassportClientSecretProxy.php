<?php

namespace App\Http\Middleware;

use Closure;

class PassportClientSecretProxy
{
    /**
     * Laravel passport client id and secret request injection.
     * Remember to change the env to config for production build
     * to be able to run "php artisan config:cache".
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->grant_type == 'password' || $request->grant_type == 'refresh_token') {

            $request->request->add([
                'client_id' => env('PASSWORD_CLIENT_ID', ''),
                'client_secret' => env('PASSWORD_CLIENT_SECRET', '')
            ]);

        }

        return $next($request);
    }
}
