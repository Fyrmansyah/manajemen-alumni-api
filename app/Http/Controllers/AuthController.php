<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AlumniLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function loginAdmin(AdminLoginRequest $request): JsonResponse
    {

        $isSuccessLogin = Auth::guard('admin')->attempt($request->validated());

        if (!$isSuccessLogin) {
            return ResponseBuilder::fail()
                ->message('gagal login, pastikan data user valid')
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
    function loginAlumni(AlumniLoginRequest $request)
    {
        $isSuccessLogin = Auth::guard('alumni')->attempt($request->validated());

        if (!$isSuccessLogin) {
            return ResponseBuilder::fail()
                ->message('gagal login, pastikan data user valid')
                ->build();
        }

        $user = $request->user('alumni');
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
