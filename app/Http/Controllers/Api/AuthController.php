<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\SendVerifyRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Main\Services\AuthService;
use App\Main\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected $authService;
    protected UserService $userService;

    public function __construct(
        AuthService $authService,
        UserService $userService
    )
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function login(Request $request)
    {

        $email = $request->email;
        $password = $request->password;

        return $this->authService->loginUser($email, $password);
    }

    public function register(RegisterUserRequest $request)
    {
        $pass = Hash::make($request['password']);

        $data = [
            'data' =>
                [
                    'last_name' => $request['last_name'],
                    'first_name' => $request['first_name'],
                    'email' => $request['email'],
                    'password' => $pass,
                    'phone' => $request['phone'],
                ],

        ];
        return $this->userService->save($data);
    }

    public function logout()

    {
        return $this->authService->logout();
    }

    public function sendVerify(SendVerifyRequest $request)
    {
        $email = $request->email;

        return $this->authService->sendOtp($email);
    }

    public function verify(Request $request)
    {
        $email = $request->email;
        $otp = $request->otp;
        return $this->authService->verify($email, $otp);
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        $error = $this->verify($request);
        if (empty($error)) {
            return $this->authService->verifyEmail($request->email);
        } else {
            return $error;
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $error = $this->verify($request);
        $email = $request->email;
        if (empty($error)) {
            return $this->authService->changePass($email, $request->password);
        } else {
            return $error;
        }
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return $this->authService->me($user);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $email = auth()->user()->email;
        $newPassword = $request['new_password'];
        $pass = $request['password'];
        return $this->authService->changePass($email, $newPassword, $pass);
    }

}
