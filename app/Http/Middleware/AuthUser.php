<?php

namespace App\Http\Middleware;


use App\Main\Repositories\AdminRepository;
use App\Main\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use const App\Main\Helpers\HTTP_CODE_UNAUTHORIZED;
use function App\Main\Helpers\responseJsonFail;

    

class AuthUser
{   
    
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return responseJsonFail("Not authenticated", HTTP_CODE_UNAUTHORIZED);
        }

        $email = Auth::user()->email;
        $user = $this->userRepository->findOne('email', $email);

        if (empty($user)) {
            return responseJsonFail("Not authenticated", HTTP_CODE_UNAUTHORIZED);
        }

        return $next($request);
    }
}
