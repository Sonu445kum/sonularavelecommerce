<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;
use App\Models\User;

class AdminNewOrderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * The user who placed the order.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\User   $user
     * @return void
     */
    public function __construct(Order $order, User $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('🛒 New Order Received — #' . $this->order->id)
            ->from(config('mail.from.address'), config('mail.from.name', 'MyShop Admin'))
            ->view('emails.admin_new_order')
            ->with([
                'order' => $this->order,
                'user' => $this->user,
                'orderUrl' => route('admin.orders.show', $this->order->id),
            ]);
    }
}
