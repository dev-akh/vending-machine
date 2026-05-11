<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt for email: ' . $credentials['email']);
        
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            Log::warning('User not found: ' . $credentials['email']);
            throw ValidationException::withMessages([
                'email' => 'No account found with this email address.',
            ]);
        }
        
        Log::info('User found: ' . $user->email . ', role: ' . $user->role);
        
        if (!Hash::check($credentials['password'], $user->password)) {
            Log::warning('Password mismatch for: ' . $credentials['email']);
            throw ValidationException::withMessages([
                'password' => 'The password is incorrect.',
            ]);
        }
        
        // Attempt authentication
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            Session::put('user_role', $user->role);
            
            Log::info('Authentication successful for: ' . $user->email);
            return redirect()->intended(
                $user->isAdmin() ? '/admin/dashboard' : '/products'
            );
        }

        Log::error('Authentication failed for unknown reason: ' . $credentials['email']);
        throw ValidationException::withMessages([
            'email' => 'Authentication failed. Please try again.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        Auth::login($user);
        
        Session::put('user_role', $user->role);

        return redirect('/products');
    }
}
