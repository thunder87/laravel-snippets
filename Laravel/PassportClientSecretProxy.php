<?php

namespace App\Http\Middleware;

use Closure;

class ClientSecretRequestInjection
{
    protected $grant_types = ['password', 'refresh_token'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('referer') == env('PASSPORT_CLIENT_ORIGIN', '*')) {

            if (in_array($request->grant_type, $this->grant_types)) {

                $request->request->add([
                    'client_id'     => env('PASSWORD_CLIENT_ID', ''),
                    'client_secret' => env('PASSWORD_CLIENT_SECRET', '')
                ]);

            }

        }

        return $next($request);
    }
}