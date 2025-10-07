<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerOrders extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $customer = Auth::guard('customers')->user();

        if (!$customer) {
            abort(403);
        }

        $orders = Order::with(['items.variant.product', 'items.variant.variantImage'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        return view('livewire.customer-orders', [
            'orders' => $orders,
            'recentOrderId' => session('recent_order_id'),
        ]);
    }
}
