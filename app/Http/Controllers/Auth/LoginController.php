<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;

use Exception;

class LoginController 
{
    /**
     * -----------------
     * GOOGLE LOGIN
     * -----------------
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        return $this->socialLogin('google');
    }

    /**
     * -----------------
     * FACEBOOK LOGIN
     * -----------------
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        return $this->socialLogin('facebook');
    }

    /**
     * -----------------
     * COMMON LOGIC
     * -----------------
     */
    private function socialLogin($provider)
    {
        try {

            $socialUser = Socialite::driver($provider)->user();

            // check existing user
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?? 'No Name',
                    'email' => $socialUser->getEmail(),
                        'avatar' => $socialUser->getAvatar(),
                'password' => bcrypt('password@123'),
                    'role' => 'user',
                    'status' => true,
   'email_verified_at' => now(), 
                    'last_login_at' => now(),
                    'last_login_ip' => request()->ip(),
                ]);
            }

            Auth::login($user);
            request()->session()->regenerate();

            $role = $user->role->value ?? $user->role;

            if ($role === 'admin') {
                return redirect('/admin/dashboard');
            }

            if ($role === 'super_admin') {
                return redirect('/super-admin/dashboard');
            }

            return redirect('/dashboard');

        } catch (Exception $e) {
            return redirect('/login')->withErrors([
                'email' => ucfirst($provider).' login failed. Please try again.'
            ]);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function login(Request $request)
{
    // ✅ validation
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:8',
    ], [
        'email.required' => 'Email is required',
        'email.email' => 'Please enter valid email',
        'password.required' => 'Password is required',
        'password.min' => 'Password must be at least 8 characters',
    ]);

    // 🔐 attempt login
    if (!Auth::attempt($request->only('email', 'password'))) {
        return back()->withErrors([
            'email' => 'Invalid email or password',
        ]);
    }

    // 🔒 security
    $request->session()->regenerate();

    $user = Auth::user();

    // role redirect
    $role = $user->role->value ?? $user->role;

    if ($role === 'admin') {
        return redirect('/admin/dashboard');
    }

    if ($role === 'super_admin') {
        return redirect('/super-admin/dashboard');
    }

    return redirect('/dashboard');
}

}


