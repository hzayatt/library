<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // First try to find by google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // User already linked via Google — keep avatar in sync
                $user->update(['avatar' => $googleUser->getAvatar()]);
            } else {
                // Try to find by email (existing account registered with password)
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // Link the existing account to Google
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar'    => $googleUser->getAvatar(),
                    ]);
                } else {
                    // Brand-new user — create account and assign member role
                    $user = User::create([
                        'name'      => $googleUser->getName(),
                        'email'     => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar'    => $googleUser->getAvatar(),
                        'password'  => null,
                        'is_active' => true,
                    ]);

                    $user->assignRole('member');
                }
            }

            Auth::login($user, true);

            return redirect()->route('dashboard');
        } catch (Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google authentication failed. Please try again.',
            ]);
        }
    }
}
