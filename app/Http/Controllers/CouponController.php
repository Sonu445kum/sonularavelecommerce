<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
    /**
     * ✅ Apply a coupon code to the current user's cart (AJAX ready).
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        // 🔍 Find the active & valid coupon
        $coupon = Coupon::where('code', $request->code)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$coupon) {
            // ❌ Invalid or expired coupon
            if($request->ajax()){
                return response()->json([
                    'success' => false,
                    'message' => '❌ Your coupon is not valid or expired.'
                ], 404);
            }
            return back()->with('coupon_error', 'Your coupon is not valid.');
        }

        // 💾 Store coupon data in session
        $couponData = [
            'code' => $coupon->code,
            'discount_type' => $coupon->type,  // 'fixed' or 'percent'
            'discount_value' => $coupon->value,
        ];
        Session::put('coupon', $couponData);

        if($request->ajax()){
            return response()->json([
                'success' => true,
                'message' => '✅ Coupon applied successfully!',
                'coupon' => $couponData
            ]);
        }

        return back()->with('success', 'Coupon applied successfully!');
    }

    /**
     * 🧹 Remove the applied coupon (AJAX ready).
     */
    public function remove(Request $request)
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');

            if($request->ajax()){
                return response()->json([
                    'success' => true,
                    'message' => '✅ Coupon removed successfully!'
                ]);
            }

            return back()->with('success', 'Coupon removed successfully.');
        }

        if($request->ajax()){
            return response()->json([
                'success' => false,
                'message' => '❌ No coupon applied.'
            ], 404);
        }

        return back()->with('coupon_error', 'No coupon applied.');
    }

    /**
     * 💡 Helper Function — Calculate discount for checkout or order page.
     *
     * @param float $cartTotal
     * @return float $discount
     */
    public static function calculateDiscount($cartTotal)
    {
        if (!Session::has('coupon')) {
            return 0;
        }

        $coupon = Session::get('coupon');
        $discount = 0;

        if ($coupon['discount_type'] === 'fixed') {
            $discount = $coupon['discount_value'];
        } elseif ($coupon['discount_type'] === 'percent') {
            $discount = ($cartTotal * $coupon['discount_value']) / 100;
        }

        return min($discount, $cartTotal); // prevent negative totals
    }
}
