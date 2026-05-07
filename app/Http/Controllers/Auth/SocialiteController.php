<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class SocialiteController extends Controller
{
    public function redirectToProvider(string $provider)
    {
        $frontendLogin = env('FRONTEND_URL', 'http://localhost:3000').'/authentication/login';

        $supportedProviders = ['google'];
        if (! in_array($provider, $supportedProviders)) {
            return redirect()->away($frontendLogin.'?error=Invalid provider');
        }

        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver($provider);

            return $driver->stateless()->redirect();
        } catch (\Exception $e) {
            return redirect()->away($frontendLogin.'?error=Failed to authenticate with the provider');
        }
    }

    public function handleProviderCallback(string $provider)
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');

        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver($provider);
            $socialUser = $driver->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'password' => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                ]
            );

            if (! $user->hasAnyRole(['super_admin', 'staff', 'guest'])) {
                $user->assignRole('guest');
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return redirect()->away(
                "{$frontendUrl}/authentication/callback?token={$token}"
            );

        } catch (\Exception $e) {
            Log::error("Socialite Callback Error [{$provider}]: ".$e);

            return redirect()->away(
                "{$frontendUrl}/authentication/login?error=Failed to authenticate with the provider"
            );
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout success'
        ], 200);
    }
}
