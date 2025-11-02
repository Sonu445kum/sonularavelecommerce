<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $payments = Payment::with(['order.user'])
            ->latest()
            ->paginate(20);
        
        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show a specific payment.
     */
    public function show($id)
    {
        $payment = Payment::with(['order.user', 'order.orderItems.product'])
            ->findOrFail($id);
        
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Update payment status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,success,failed,refunded',
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }
}

