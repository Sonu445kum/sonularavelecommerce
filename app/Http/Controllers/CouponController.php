<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CouponController extends Controller
{
    /**
     * ✅ Apply a coupon code to the current user's cart (AJAX + Blade compatible)
     */
    public function apply(Request $request)
{
    $request->validate([
        'coupon_code' => 'required|string'
    ]);

    $code = strtolower(trim($request->input('coupon_code')));

    $coupon = \App\Models\Coupon::whereRaw('LOWER(code) = ?', [$code])
        ->where('is_active', 1)
        ->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        })
        ->first();

    if (!$coupon) {
        return response()->json([
            'success' => false,
            'message' => '❌ Invalid, inactive, or expired coupon code.'
        ], 400);
    }

    // 💾 Save coupon in session
    session([
        'coupon' => [
            'code'  => $coupon->code,
            'type'  => $coupon->type,
            'value' => $coupon->value,
        ]
    ]);

    // 🧮 Get current cart total (replace with your logic)
    $cartTotal = \App\Models\CartItem::where('user_id', auth()->id())->sum('total_price');

    // 💸 Apply discount
    if ($coupon->type === 'percent') {
        $discountAmount = ($cartTotal * $coupon->value) / 100;
    } else {
        $discountAmount = min($coupon->value, $cartTotal); // fixed discount, not more than total
    }

    $newTotal = $cartTotal - $discountAmount;

    return response()->json([
        'success' => true,
        'message' => "✅ Coupon '{$coupon->code}' applied successfully!",
        'discount' => number_format($discountAmount, 2),
        'new_total' => number_format($newTotal, 2)
    ]);
}



    /**
     * ❌ Remove the applied coupon (AJAX + Blade compatible)
     */
    public function remove(Request $request)
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');

            $response = [
                'success' => true,
                'message' => '✅ Coupon removed successfully!'
            ];

            return $request->ajax()
                ? response()->json($response)
                : back()->with('success', $response['message']);
        }

        $response = [
            'success' => false,
            'message' => '❌ No coupon applied.'
        ];

        return $request->ajax()
            ? response()->json($response, 404)
            : back()->with('error', $response['message']);
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

        if (($coupon['type'] ?? '') === 'fixed') {
            $discount = $coupon['value'] ?? 0;
        } elseif (($coupon['type'] ?? '') === 'percent') {
            $discount = ($cartTotal * ($coupon['value'] ?? 0)) / 100;
        }

        return min($discount, $cartTotal); // prevent negative totals
    }
}
