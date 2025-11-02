<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard page
     */
    public function index()
    {
        return view('admin.dashboard'); // make sure this view exists later
    }
   public function profile()
{
    $user = auth()->user();
    return view('admin.profile', compact('user'));
}

public function updateProfile(Request $request)
{
    $user = auth()->user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image = $path;
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->save();

    return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
}
        // Profiles Edit
        public function editProfile()
{
    $user = auth()->user(); // Get the currently logged-in admin/user
    return view('admin.edit-profile', compact('user'));
}




}
