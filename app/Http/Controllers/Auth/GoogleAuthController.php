<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // If the user exists, we link the google_id just in case
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                }
            } else {
                // Create a new user
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Usuario Google',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => null,
                    'avatar' => null // Google returns a full URL but Filament expects a path. Best to leave null unless we download it.
                ]);
            }

            // Log the user in
            Auth::login($user);

            // Redirect to admin panel dashboard
            return redirect()->intended('/admin');

        } catch (Exception $e) {
            return redirect('/admin/login')->withErrors([
                'email' => 'Ocurrió un error al intentar iniciar sesión con Google. ' . $e->getMessage()
            ]);
        }
    }
}
