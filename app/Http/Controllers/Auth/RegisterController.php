<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

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
        // ✅ Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ]);

        // 👤 Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // 🔐 hashing
            'role' => 'user',
            'status' => true,
        ]);

        // 📧 Email verification trigger (Laravel built-in)
        event(new Registered($user));

        // 🔐 Auto login after register (optional but common in eCommerce)
        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard')->with('success', 'Registration successful!');
    }
}
