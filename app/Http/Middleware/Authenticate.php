<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // if (! $request->expectsJson()) {
        //     return route('formations');
        //                 // return route('login');

        // }
        if (! $request->expectsJson()) {
            // return view('admin.apps.home.accueil');
            return route('accueil'); // Retourne l'URL de la route nommée 'accueil'
                        // return route('login');

        }
    }
}
