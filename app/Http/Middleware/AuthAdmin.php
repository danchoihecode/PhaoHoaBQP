<?php

namespace App\Http\Middleware;

use App\Main\Helpers\Response;
use App\Main\Repositories\AdminRepository;
use Closure;
use Illuminate\Http\Request;
use function App\Main\Helpers\responseJsonFail;
use const App\Main\Helpers\HTTP_CODE_UNAUTHORIZED;

class AuthAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $adminRepository = new AdminRepository();

        $email = auth()->user()->email;
        $user = $adminRepository->findOne('email',$email );

        if (empty($user)) {
            return responseJsonFail("Not authenticated", HTTP_CODE_UNAUTHORIZED);
        }

        return $next($request);
    }
}
