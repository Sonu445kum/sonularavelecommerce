<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderPlacedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('✅ Your Order #' . $this->order->id . ' has been placed successfully!')
                    ->from('sonuroy1629@gmail.com', 'MyShop E-Commerce') // ✅ change sender as needed
                    ->view('emails.order_placed') // ensure your file: resources/views/emails/order_placed.blade.php
                    ->with([
                        'order' => $this->order,
                        'user' => $this->order->user,
                        'total' => $this->order->total,
                        'status' => ucfirst($this->order->status),
                    ]);
    }

    
}
