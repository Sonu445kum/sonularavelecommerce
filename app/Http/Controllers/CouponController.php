<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
    // ================= Apply Coupon =================
    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $code = strtoupper(trim($request->coupon_code));

        $coupon = Coupon::where('code', $code)
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => '❌ Invalid or expired coupon!'
            ]);
        }

        // Store coupon in session
        Session::put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value
        ]);

        // Calculate cart subtotal properly
        $cartSubtotal = auth()->user()->cart
            ? auth()->user()->cart->items->sum(fn($i) => $i->price * $i->quantity)
            : 0;

        $discount = self::calculateDiscount($cartSubtotal);
        $shipping = 50;
        $total = max(($cartSubtotal - $discount) + $shipping, 0);

        return response()->json([
            'success' => true,
            'message' => "✅ Coupon '{$coupon->code}' applied!",
            'cartSubtotal' => round($cartSubtotal, 2),
            'discount' => round($discount, 2),
            'shipping' => round($shipping, 2),
            'total' => round($total, 2),
            'coupon' => Session::get('coupon')
        ]);
    }

    // ================= Remove Coupon =================
    public function remove()
    {
        Session::forget('coupon');

        // Calculate cart subtotal properly
        $cartSubtotal = auth()->user()->cart
            ? auth()->user()->cart->items->sum(fn($i) => $i->price * $i->quantity)
            : 0;

        $discount = 0;
        $shipping = 50;
        $total = max(($cartSubtotal - $discount) + $shipping, 0);

        return response()->json([
            'success' => true,
            'message' => '✅ Coupon removed!',
            'cartSubtotal' => round($cartSubtotal, 2),
            'discount' => round($discount, 2),
            'shipping' => round($shipping, 2),
            'total' => round($total, 2)
        ]);
    }

    // ================= Helper =================
    public static function calculateDiscount($cartSubtotal)
    {
        $coupon = Session::get('coupon', null);
        $discount = 0;

        if(!$coupon) return 0;

        if($coupon['type'] === 'fixed'){
            $discount = $coupon['value'];
        } elseif($coupon['type'] === 'percent'){
            $discount = ($cartSubtotal * $coupon['value']) / 100;
        }

        return $discount;
    }
}
