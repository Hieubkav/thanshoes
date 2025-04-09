<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use App\Models\Order;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $signedUrl;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['items.variant.product', 'items.variant.variantImage', 'customer']);
        $this->signedUrl = URL::signedRoute('filament.admin.resources.orders.edit', ['record' => $order->id]);
    }

    public function build()
    {
        return $this->subject('Đơn hàng mới từ ThanShoes')
                    ->view('emails.orders.shipped');
    }
}
