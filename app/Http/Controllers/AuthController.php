<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class AuthController extends Controller
{
    // 🧾 Show Register Form
    public function showRegister()
    {
        return view('auth.register');
    }

    // 🧍 Handle Registration
    public function register(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        // ✅ FIXED: Removed manual Hash::make()
        // because the User model already hashes password automatically
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('login.form')
            ->with('success', 'Registration successful! Please log in to continue.');
    }

    // 🔑 Show Login Form
    public function showLogin()
    {
        return view('auth.login');
    }

    // 🔑 Handle Login
    public function login(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $req->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with that email.',
            ])->onlyInput('email');
        }

        if (isset($user->is_active) && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact support.',
            ]);
        }

        if (Auth::attempt($credentials, $req->boolean('remember'))) {
            $req->session()->regenerate();

            return redirect()->intended(route('home'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials, please try again.',
        ])->onlyInput('email');
    }

    // 🟢 Show Forgot Password Form
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // 🟢 Send Reset Link to Email
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Password reset link has been sent to your email!');
        } else {
            return back()->withErrors(['email' => __($status)]);
        }
    }

    // 🟢 Show Reset Password Form
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }

    // 🟢 Handle Reset Password Logic
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login.form')->with('success', 'Password has been reset successfully!');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    // 🚪 Handle Logout
    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully!');
    }

    // 👤 Show Profile Page
    public function profile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    // 🛠️ Update Profile
    public function updateProfile(Request $req)
    {
        $user = Auth::user();

        $data = $req->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|confirmed|min:6',
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $user->password = $data['password']; // model will auto hash
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully ✅');
    }
}
