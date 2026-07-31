<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\CartService;
use App\Services\StoreMailService;
use App\Services\WishlistService;


class RegisterController extends Controller
{

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
        
    /**
     * -------------------------
     * DEFAULT REGISTER (EMAIL)
     * -------------------------
     */
public function register(
    Request $request,
    CartService $cartService,
    WishlistService $wishlistService,
    StoreMailService $storeMailService
)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|digits:10',
        'password' => 'required|min:8|confirmed',
        'redirect_to' => 'nullable|string|max:255',
    ]);

    if (User::whereIn('phone', $this->phoneLookupValues($request->phone))->exists()) {
        return back()->withInput()->withErrors(['phone' => 'This mobile number is already registered.']);
    }

    $guestSessionId = session()->getId();

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $this->formatMobile($request->phone),
        'password' => Hash::make($request->password),
        'role' => 'user',
        'status' => true,
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
        'last_login_at' => now(),
        'last_login_ip' => $request->ip(),
    ]);

    Auth::login($user);
    $request->session()->regenerate();
    $cartService->claimGuestCartForUser((int) $user->id, $guestSessionId);
    $wishlistService->mergeGuestWishlist();
    $cartService->mergeGuestCart();
    $storeMailService->userLoggedIn($user, 'new email/mobile registration', $request);

    if ($request->redirect_to === 'checkout') {
        return redirect()->route('checkout.index')->with('success', 'Account created. Continue checkout.');
    }

    return redirect()->route('dashboard')->with('success', 'Account created successfully.');
}

private function formatMobile(string $phone): string
{
    return substr(preg_replace('/\D+/', '', $phone), -10);
}

private function phoneLookupValues(string $phone): array
{
    $digits = preg_replace('/\D+/', '', $phone);
    $mobile = $this->formatMobile($digits);
    $national = str_starts_with($mobile, '91') ? substr($mobile, 2) : $mobile;

    return array_values(array_unique(array_filter([
        $phone,
        $digits,
        $mobile,
        '+'.$mobile,
        $national,
        '0'.$national,
    ])));
}



}
