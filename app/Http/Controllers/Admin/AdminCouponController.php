<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Str;

class AdminCouponController extends Controller
{
    /**
     * Display all coupons.
     */
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store a new coupon.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        Coupon::create([
            'code' => $request->code ?? strtoupper(Str::random(8)),
            'type' => $request->type,
            'value' => $request->value,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    /**
     * Edit coupon.
     */
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update coupon.
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $coupon->update([
            'type' => $request->type,
            'value' => $request->value,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $coupon->is_active,
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    /**
     * Delete coupon.
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}