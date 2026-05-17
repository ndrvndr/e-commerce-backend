<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        return response()->json(
            [
                "data" => $request->user(),
            ],
            200,
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()
            ->json(
                [
                    "message" => "Logout success",
                ],
                200,
            )
            ->withCookie(
                Cookie::forget("auth_token", "/", config("app.cookie_domain")),
            );
    }
}
