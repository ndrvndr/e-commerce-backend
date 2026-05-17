<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OauthCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class SocialiteController extends Controller
{
    public function redirectToProvider(string $provider)
    {
        $frontendLogin = config("app.frontend_url") . "/authentication/login";

        $supportedProviders = ["google"];
        if (!in_array($provider, $supportedProviders)) {
            return redirect()->away($frontendLogin . "?error=Invalid provider");
        }

        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver($provider);

            return $driver->stateless()->redirect();
        } catch (\Exception $e) {
            return redirect()->away(
                $frontendLogin .
                    "?error=Failed to authenticate with the provider",
            );
        }
    }

    public function handleProviderCallback(string $provider)
    {
        $frontendUrl = config("app.frontend_url");

        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver($provider);
            $socialUser = $driver->stateless()->user();

            $user = User::firstOrCreate(
                ["email" => $socialUser->getEmail()],
                [
                    "name" =>
                        $socialUser->getName() ?? $socialUser->getNickname(),
                    "password" => bcrypt(Str::random(24)),
                    "email_verified_at" => now(),
                ],
            );

            if (!$user->hasAnyRole(["super_admin", "staff", "guest"])) {
                $user->assignRole("guest");
            }

            $code = Str::random(64);

            OauthCode::create([
                "code" => $code,
                "user_id" => $user->id,
                "expires_at" => now()->addSeconds(60),
            ]);

            return redirect()->away(
                "{$frontendUrl}/authentication/callback?code={$code}",
            );
        } catch (\Exception $e) {
            Log::error("Socialite Callback Error [{$provider}]: " . $e);

            return redirect()->away(
                "{$frontendUrl}/authentication/login?error=Failed to authenticate with the provider",
            );
        }
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $request->validate([
            "code" => ["required", "string", "size:64"],
        ]);

        $user = DB::transaction(function () use ($request) {
            $oauthCode = OauthCode::where("code", $request->input("code"))
                ->where("expires_at", ">", now())
                ->lockForUpdate()
                ->first();

            if (!$oauthCode) {
                return null;
            }

            $user = User::findOrFail($oauthCode->user_id);
            $oauthCode->delete();

            return $user;
        });

        if (!$user) {
            return response()->json(
                ["message" => "Invalid or expired code"],
                401,
            );
        }

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()
            ->json(["message" => "OK"])
            ->cookie(
                "auth_token",
                $token,
                60 * 24 * 7,
                "/",
                config("app.cookie_domain"),
                config("app.cookie_secure"),
                true,
                false,
                config("app.cookie_samesite"),
            );
    }
}
