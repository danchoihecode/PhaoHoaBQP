<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Main\Services\AuthService;
use Illuminate\Http\Request;

class AuthAdminController extends Controller
{
    protected $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {

        
        $email = $request->email;
        $password = $request->password;

        return $this->authService->login($email, $password);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return $this->authService->me($user);
    }

    public function  logout()
    {
        return $this->authService->logout( );
    }


}
