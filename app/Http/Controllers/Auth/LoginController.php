<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\StoreMailService;
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
        if (request('redirect') === 'checkout') {
            session(['url.intended' => route('checkout.index')]);
        }

        return Socialite::driver('google')
            ->redirectUrl(route('auth.google.callback'))
            ->redirect();
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
    | SOCIAL LOGIN COMMON LOGIC
    |--------------------------------------------------------------------------
    */

    private function socialLogin(
        string $provider,
        WishlistService $wishlistService,
        CartService $cartService
    ) {
        try {

            $driver = Socialite::driver($provider);

            if ($provider === 'google') {
                $driver->redirectUrl(route('auth.google.callback'));
            }

            $socialUser = $driver->user();





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

                app(StoreMailService::class)->userLoggedIn(
                    $user,
                    'new '.$provider.' registration',
                    request()
                );

            }






            /*
            |--------------------------------------------------------------------------
            | LOGIN USER
            |--------------------------------------------------------------------------
            */

            $guestSessionId = session()->getId();

            Auth::login($user);

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

            return redirect()->intended('/dashboard');

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
        if (request('redirect') === 'checkout') {
            session(['url.intended' => route('checkout.index')]);
        }

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

            'login' => 'required|string',

            'password' => 'required|min:8',
            'redirect_to' => 'nullable|string|max:255',

        ], [

            'login.required' =>
                'Email or mobile number is required',

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

        $login = trim($request->input('login'));
        $user = null;

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $login)->first();
        } else {
            $phone = preg_replace('/\D+/', '', $login);

            if (strlen($phone) !== 10) {
                return back()->withErrors([
                    'login' => 'Mobile number must be exactly 10 digits',
                ])->withInput($request->only('login', 'redirect_to'));
            }

            $user = User::whereIn('phone', array_unique([$phone, '91'.$phone, '+91'.$phone, '0'.$phone]))->first();
        }

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {

            return back()->withErrors([

                'login' =>
                    'Invalid email/mobile number or password',

            ]);

        }

        $guestSessionId = session()->getId();

        Auth::login($user);

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

        if ($request->input('redirect_to') === 'checkout') {
            return redirect()->route('checkout.index');
        }

        return redirect()->intended('/dashboard');
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
