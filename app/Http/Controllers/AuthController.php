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
use RealRashid\SweetAlert\Facades\Alert; // ✅ Add this line

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

        // User::create([
        //     'name' => $data['name'],
        //     'email' => $data['email'],
        //     'password' => $data['password'], // auto-hashed by model
        //     'is_active' => true,
        // ]);

        // Hash password
        User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']), // ✅ hashed
        'is_active' => true,
    ]);


        Alert::success('Success 🎉', 'Registration successful! Please log in to continue.');

        return redirect()->route('login.form');
    }

    // 🔑 Show Login Form
    public function showLogin()
    {
        return view('auth.login');
    }

    // 🔑 Handle Login
   public function login(Request $req)
{
    // ✅ Validate login inputs
    $req->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    $credentials = $req->only('email', 'password');

    // ✅ Check if user exists
    $user = User::where('email', $credentials['email'])->first();
    if (!$user) {
        Alert::error('Error ❌', 'No account found with that email.');
        return back()->withInput($req->only('email'));
    }

    // ✅ Check if user is active
    if (isset($user->is_active) && !$user->is_active) {
        Alert::warning('Account Inactive ⚠️', 'Please contact support.');
        return back()->withInput($req->only('email'));
    }

    // ✅ Attempt login
    if (Auth::attempt($credentials, $req->boolean('remember'))) {
        // Regenerate session to prevent session fixation
        $req->session()->regenerate();

        // Clear intended URL if any
        $req->session()->forget('url.intended');

        // Success message
        Alert::success('Welcome Back 👋', 'Hello, ' . Auth::user()->name . '!');

        // Redirect to intended page or home
        return redirect()->intended(route('home'));
    }

    // ❌ Invalid credentials
    Alert::error('Invalid Credentials', 'Please try again.');
    return back()->withInput($req->only('email'));
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

        if ($status === Password::RESET_LINK_SENT) {
            Alert::success('Email Sent 📧', 'Password reset link sent successfully!');
            return back();
        }

        Alert::error('Failed ❌', __($status));
        return back();
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

        if ($status === Password::PASSWORD_RESET) {
            Alert::success('Password Reset 🔐', 'Password has been reset successfully!');
            return redirect()->route('login.form');
        }

        Alert::error('Failed ❌', __($status));
        return back();
    }

    /* ===========================
     🚪 LOGOUT
    ============================*/

    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();

        Alert::info('Logged Out 👋', 'You have logged out successfully!');
        return redirect()->intended(route('home'));
    }

    /* ===========================
     👤 PROFILE SECTION
    ============================*/

    public function profile()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)->latest()->get();
        $wishlist = Wishlist::where('user_id', $user->id)->with(['product.images'])->get();

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

        Alert::success('Profile Updated ✅', 'Your profile has been updated successfully.');
        return back();
    }

    // 🖼️ Update Avatar
    public function updateAvatar(Request $req)
    {
        $req->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        $path = $req->file('avatar')->store('avatars', 'public');
        $user->avatar = 'storage/' . $path;
        $user->save();

        Alert::success('Avatar Updated 📸', 'Profile picture updated successfully!');
        return back();
    }
}
