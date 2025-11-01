<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use const App\Main\Helpers\HTTP_CODE_UNAUTHORIZED;
use function App\Main\Helpers\responseJsonFail;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return responseJsonFail("Not authenticated", HTTP_CODE_UNAUTHORIZED);
        }

    }
}
