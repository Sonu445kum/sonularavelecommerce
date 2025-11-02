<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Order;
use App\Models\Wishlist;

class AuthController extends Controller
{
    /* ===========================
     🧾 AUTHENTICATION SECTION
    ============================*/

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

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // auto-hashed by model
            'is_active' => true,
        ]);

        return redirect()->route('login.form')
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
            return back()->withErrors(['email' => 'No account found with that email.']);
        }

        if (isset($user->is_active) && !$user->is_active) {
            return back()->withErrors(['email' => 'Your account is inactive. Please contact support.']);
        }

        if (Auth::attempt($credentials, $req->boolean('remember'))) {
            $req->session()->regenerate();
            return redirect()->intended(route('home'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors(['email' => 'Invalid credentials, please try again.']);
    }

    /* ===========================
     🔁 PASSWORD RESET SECTION
    ============================*/

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link has been sent to your email!')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

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

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.form')->with('success', 'Password has been reset successfully!')
            : back()->withErrors(['email' => __($status)]);
    }

    /* ===========================
     🚪 LOGOUT
    ============================*/

    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully!');
    }

    /* ===========================
     👤 PROFILE SECTION
    ============================*/

    // 👤 Show Profile Page (with Orders + Wishlist)
    public function profile()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            ->latest()
            ->get();

        $wishlist = Wishlist::where('user_id', $user->id)
            ->with('product') // eager load product data
            ->get();

        return view('auth.profile', compact('user', 'orders', 'wishlist'));
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
            $user->password = $data['password']; // auto-hashed by model
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully ✅');
    }

    // 🖼️ Update Avatar
    public function updateAvatar(Request $req)
    {
        $req->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        // Upload new avatar
        $path = $req->file('avatar')->store('avatars', 'public');
        $user->avatar = 'storage/' . $path;
        $user->save();

        return back()->with('success', 'Profile picture updated successfully 📸');
    }
}
