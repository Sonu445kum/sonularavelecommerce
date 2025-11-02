<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\User;
use App\Models\Payment;
use App\Models\Wishlist;

class AdminController extends Controller
{
    /**
     * ------------------------------------------
     * 🏠 Admin Dashboard
     * ------------------------------------------
     * Show the main admin dashboard page
     */
    public function dashboard()
    {
        // Get all statistics
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();
        
        // Calculate total revenue (sum of all completed/delivered orders)
        $totalRevenue = Order::whereIn('status', ['completed', 'delivered'])->sum('total');
        
        // Count pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();
        
        // Count wishlist items
        $wishlistCount = Wishlist::count();
        
        // Get recent orders (last 10) with user relationship
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();
        
        // Get recent users (last 10)
        $recentUsers = User::latest()
            ->take(10)
            ->get();
        
        // Get recent payments (last 10) - Payment doesn't have direct user relationship, so we'll get through order
        $recentPayments = Payment::with(['order.user'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalCategories',
            'totalUsers',
            'totalRevenue',
            'pendingPayments',
            'wishlistCount',
            'recentOrders',
            'recentUsers',
            'recentPayments'
        ));
    }

    /**
     * ------------------------------------------
     * 👤 Show Admin Profile
     * ------------------------------------------
     */
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    /**
     * ------------------------------------------
     * ✏️ Edit Admin Profile
     * ------------------------------------------
     */
    public function editProfile()
    {
        $user = auth()->user();
        return view('admin.edit-profile', compact('user'));
    }

    /**
     * ------------------------------------------
     * 💾 Update Admin Profile
     * ------------------------------------------
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // ✅ Validate Input
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        // Only add profile_image validation if a file is actually present and valid
        // Use 'sometimes' which only validates when the field is present in the request
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            // Double check: file must exist, be valid, have no errors, and have size
            if ($file && $file->isValid() && $file->getError() === UPLOAD_ERR_OK && $file->getSize() > 0) {
                // File is valid, add validation rule
                $rules['profile_image'] = 'required|image|mimes:jpeg,jpg,png|max:2048';
            }
        }

        $validated = $request->validate($rules);

        // ✅ Handle Profile Image Upload
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            if ($profileImage && $profileImage->isValid() && $profileImage->getError() === UPLOAD_ERR_OK) {
                // Delete old image if exists
                if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }

                $path = $profileImage->store('profile_images', 'public');
                $user->profile_image = $path;
            }
        }

        // ✅ Update user details
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('admin.profile.index')->with('success', 'Profile updated successfully!');
    }
}
