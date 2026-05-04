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

    // 🧠 Store in session (temporary)
    session([
        'register_data' => [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]
    ]);

    // 📧 Send OTP
    Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
        $message->to($request->email)
                ->subject('Your OTP Code');
    });

    return redirect()->route('otp.form')
        ->with('success', 'OTP sent to your email');
}

    

public function showOtpForm()
{
    return view('auth.verifyOtp');
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required',
    ]);

    $data = session('register_data');

    if (!$data) {
        return redirect()->route('register')->withErrors(['Session expired']);
    }

    // ❌ Wrong OTP
    if ($data['otp'] != $request->otp) {
        return back()->withErrors(['Invalid OTP']);
    }

    // ⏳ Expired
    if ($data['otp_expires_at'] < now()) {
        return back()->withErrors(['OTP expired']);
    }

    // ✅ CREATE USER NOW
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => $data['password'],
        'role' => 'user',
        'status' => true,
        'email_verified_at' => now(),
    ]);

    // 🧹 Clear session
    session()->forget('register_data');

    // 🔐 Login
    Auth::login($user);

    return redirect('/dashboard')->with('success', 'Account created & verified!');
}




}
