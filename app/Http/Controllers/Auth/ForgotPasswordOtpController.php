<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordOtpController extends Controller
{
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'No account was found with this email address.',
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();
        $otp = (string) random_int(100000, 999999);

        session([
            'password_reset_otp' => [
                'email' => $user->email,
                'otp' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(10),
            ],
        ]);

        if (! $this->sendPasswordOtpMail($user, $otp)) {
            session()->forget('password_reset_otp');

            return back()
                ->withInput()
                ->withErrors(['email' => 'We could not send the password reset OTP right now. Please try again.']);
        }

        return redirect()
            ->route('password.otp.form')
            ->with('success', 'Password reset OTP sent to your email.');
    }

    public function showOtpForm()
    {
        if (! session('password_reset_otp')) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'Please request a password reset OTP first.']);
        }

        return view('auth.reset-password-otp');
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data = session('password_reset_otp');

        if (! $data) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'Please request a password reset OTP first.']);
        }

        if ($data['otp_expires_at'] < now()) {
            session()->forget('password_reset_otp');

            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'Your OTP has expired. Please request a new one.']);
        }

        if (! Hash::check($validated['otp'], $data['otp'])) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please check your email and try again.']);
        }

        $user = User::where('email', $data['email'])->firstOrFail();
        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        session()->forget('password_reset_otp');

        return redirect()
            ->route('login')
            ->with('success', 'Password updated. You can login with your new password.');
    }

    public function resendOtp()
    {
        $data = session('password_reset_otp');

        if (! $data) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'Please request a password reset OTP first.']);
        }

        $user = User::where('email', $data['email'])->firstOrFail();
        $otp = (string) random_int(100000, 999999);

        $data['otp'] = Hash::make($otp);
        $data['otp_expires_at'] = now()->addMinutes(10);
        session(['password_reset_otp' => $data]);

        if (! $this->sendPasswordOtpMail($user, $otp)) {
            return back()->withErrors(['email' => 'We could not resend the password reset OTP right now. Please try again.']);
        }

        return back()->with('success', 'A fresh password reset OTP has been sent.');
    }

    private function sendPasswordOtpMail(User $user, string $otp): bool
    {
        try {
            Mail::send('emails.auth.password-reset-otp', [
                'name' => $user->name,
                'otp' => $otp,
                'expiresIn' => 10,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Reset your Gosowa password');
            });

            return true;
        } catch (\Throwable $e) {
            Log::error('Password reset OTP mail failed', [
                'email' => $user->email,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
