<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function login(LoginRequest $request)
    {

        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'gagal login, pastikan data user valid'
            ], Response::HTTP_FORBIDDEN);
        }

        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'token' => $user->createToken("auth_token")->plainTextToken
        ]);
    }
}
