<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->user = $order->user; // Automatically fetch user from relationship
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('✅ Order Confirmation - ' . config('app.name'))
            ->view('emails.order_confirmation')
            ->with([
                'order' => $this->order,
                'user' => $this->user,
                'total' => $this->order->total,
                'status' => $this->order->status,
            ]);
    }
}
