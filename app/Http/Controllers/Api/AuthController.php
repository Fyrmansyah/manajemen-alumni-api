<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseBuilder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AdminLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function loginAdmin(AdminLoginRequest $request): JsonResponse
    {

        $isSuccessLogin = Auth::guard('admin')->attempt($request->validated());

        if (!$isSuccessLogin) {
            return ResponseBuilder::fail()
                ->message('Gagal login, pastikan data user valid')
                ->build();
        }

        $user = $request->user('admin');
        $user->tokens()->delete();

        $data = [
            'token' => $user->createToken("auth_token")->plainTextToken,
            'user' => $user
        ];

        return ResponseBuilder::success()
            ->data($data)
            ->message('Sukses login')
            ->build();
    }
}
