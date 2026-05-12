<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\WishlistService;
use App\Services\CartService;


use Exception;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GOOGLE LOGIN
    |--------------------------------------------------------------------------
    */

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(
        WishlistService $wishlistService,
        CartService $cartService
    ) {
        return $this->socialLogin(
            'google',
            $wishlistService,
            $cartService
        );
    }






    /*
    |--------------------------------------------------------------------------
    | FACEBOOK LOGIN
    |--------------------------------------------------------------------------
    */

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback(
        WishlistService $wishlistService,
        CartService $cartService
    ) {
        return $this->socialLogin(
            'facebook',
            $wishlistService,
            $cartService
        );
    }






    /*
    |--------------------------------------------------------------------------
    | SOCIAL LOGIN COMMON LOGIC
    |--------------------------------------------------------------------------
    */

    private function socialLogin(
        string $provider,
        WishlistService $wishlistService,
        CartService $cartService
    ) {
        try {

            $socialUser = Socialite::driver($provider)->user();





            /*
            |--------------------------------------------------------------------------
            | FIND EXISTING USER
            |--------------------------------------------------------------------------
            */

            $user = User::where(
                'email',
                $socialUser->getEmail()
            )->first();





            /*
            |--------------------------------------------------------------------------
            | CREATE NEW USER
            |--------------------------------------------------------------------------
            */

            if (!$user) {

                $user = User::create([

                    'name' => $socialUser->getName()
                        ?? 'No Name',

                    'email' => $socialUser->getEmail(),

                    'avatar' => $socialUser->getAvatar(),

                    'password' => bcrypt(
                        'password@123'
                    ),

                    'role' => 'user',

                    'status' => true,

                    'email_verified_at' => now(),

                    'last_login_at' => now(),

                    'last_login_ip' => request()->ip(),

                ]);

            }






            /*
            |--------------------------------------------------------------------------
            | LOGIN USER
            |--------------------------------------------------------------------------
            */

            Auth::login($user);

            $guestSessionId = session()->getId();

            request()->session()->regenerate();






            /*
            |--------------------------------------------------------------------------
            | MERGE GUEST WISHLIST
            |--------------------------------------------------------------------------
            */

            $cartService->claimGuestCartForUser((int) $user->id, $guestSessionId);

            $wishlistService->mergeGuestWishlist();
            $cartService->mergeGuestCart();





            /*
            |--------------------------------------------------------------------------
            | UPDATE LAST LOGIN
            |--------------------------------------------------------------------------
            */

            $user->update([

                'last_login_at' => now(),

                'last_login_ip' => request()->ip(),

            ]);






            /*
            |--------------------------------------------------------------------------
            | ROLE BASED REDIRECT
            |--------------------------------------------------------------------------
            */

            $role = $user->role->value
                ?? $user->role;

            if ($role === 'admin') {

                return redirect('/admin/dashboard');

            }

            if ($role === 'super_admin') {

                return redirect('/super-admin/dashboard');

            }

            return redirect('/dashboard');

        } catch (Exception $e) {

            return redirect('/login')->withErrors([

                'email' =>
                    ucfirst($provider)
                    .' login failed. Please try again.'

            ]);

        }
    }






    /*
    |--------------------------------------------------------------------------
    | LOGIN FORM
    |--------------------------------------------------------------------------
    */

    public function showLoginForm()
    {
        return view('auth.login');
    }






    /*
    |--------------------------------------------------------------------------
    | NORMAL LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(
        Request $request,
        WishlistService $wishlistService,
        CartService $cartService
    ) {

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'email' => 'required|email',

            'password' => 'required|min:8',

        ], [

            'email.required' =>
                'Email is required',

            'email.email' =>
                'Please enter valid email',

            'password.required' =>
                'Password is required',

            'password.min' =>
                'Password must be at least 8 characters',

        ]);






        /*
        |--------------------------------------------------------------------------
        | ATTEMPT LOGIN
        |--------------------------------------------------------------------------
        */

        if (!Auth::attempt(
            $request->only('email', 'password')
        )) {

            return back()->withErrors([

                'email' =>
                    'Invalid email or password',

            ]);

        }






        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        */

        $guestSessionId = session()->getId();

        $request->session()->regenerate();

        $user = Auth::user();






        /*
        |--------------------------------------------------------------------------
        | UPDATE LAST LOGIN
        |--------------------------------------------------------------------------
        */

        $user->update([

            'last_login_at' => now(),

            'last_login_ip' => $request->ip(),

        ]);






        /*
        |--------------------------------------------------------------------------
        | MERGE GUEST WISHLIST
        |--------------------------------------------------------------------------
        */

        $cartService->claimGuestCartForUser((int) $user->id, $guestSessionId);

        $wishlistService->mergeGuestWishlist();
        $cartService->mergeGuestCart();






        /*
        |--------------------------------------------------------------------------
        | ROLE BASED REDIRECT
        |--------------------------------------------------------------------------
        */

        $role = $user->role->value
            ?? $user->role;

        if ($role === 'admin') {

            return redirect('/admin/dashboard');

        }

        if ($role === 'super_admin') {

            return redirect('/super-admin/dashboard');

        }

        return redirect('/dashboard');
    }






    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();





        /*
        |--------------------------------------------------------------------------
        | DESTROY SESSION
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();

        $request->session()->regenerateToken();





        return redirect('/login')->with(

            'success',

            'Logged out successfully'

        );
    }
}