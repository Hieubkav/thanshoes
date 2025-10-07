<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerOrderDetail extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        $customer = Auth::guard('customers')->user();

        if (!$customer || $order->customer_id !== $customer->id) {
            abort(403);
        }

        $this->order = $order->load([
            'items.variant.product',
            'items.variant.variantImage',
            'customer',
        ]);

        session()->forget('recent_order_id');
    }

    public function render()
    {
        return view('livewire.customer-order-detail', [
            'order' => $this->order,
        ]);
    }
}
