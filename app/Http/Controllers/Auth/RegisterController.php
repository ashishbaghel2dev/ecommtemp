<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class RegisterController 
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
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
    ]);

    // 🔢 Generate OTP
    $otp = rand(100000, 999999);

    // 👤 Create user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user',
        'status' => false, // ❗ not active yet
        'otp' => $otp,
        'otp_expires_at' => now()->addMinutes(10),
    ]);

    // 📧 Send OTP Mail
    Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
        $message->to($user->email)
                ->subject('Your OTP Code');
    });

    // 👉 Redirect to OTP page
    return redirect()->route('otp.form', $user->id)
        ->with('success', 'OTP sent to your email');
}

public function showOtpForm($id)
{
    $user = User::findOrFail($id);
    return view('auth.verifyOtp', compact('user'));
}


public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required',
        'user_id' => 'required'
    ]);

    $user = User::find($request->user_id);

    if (!$user) {
        return back()->withErrors(['Invalid user']);
    }

    // ❌ Wrong OTP
    if ($user->otp != $request->otp) {
        return back()->withErrors(['Invalid OTP']);
    }

    // ⏳ Expired OTP
    if ($user->otp_expires_at < now()) {
        return back()->withErrors(['OTP expired']);
    }

    // ✅ Verified
    $user->update([
        'status' => true,
        'email_verified_at' => now(),
        'otp' => null,
    ]);

    // 🔐 Login
    Auth::login($user);

    return redirect('/dashboard')->with('success', 'Account verified!');
}



}
