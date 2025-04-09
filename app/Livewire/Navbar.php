<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\On;

class Navbar extends Component
{
    public $products;
    public $brands;
    public $types;
    public $searchTerm = '';
    public $searchResults = [];

    // Cart related properties
    public $cartItems;
    public $cartCount = 0;
    public $totalAmount = 0;

    // Customer related properties
    public $customer_id = null;
    public $name_customer = "";
    public $phone_customer = "";
    public $email_customer = "";
    public $address_customer = "";

    // Order related properties
    public $order;

    public function mount()
    {
        $this->initializeOrder();
        $this->updateCart();
        $this->loadCustomerData();
    }

    private function initializeOrder()
    {
        $this->order = new Collection();
    }

    private function loadCustomerData()
    {
        if (!session()->has('customer_id')) {
            return;
        }

        $this->customer_id = session()->get('customer_id');
        $customer = Customer::find($this->customer_id);
        
        if ($customer) {
            $this->updateCustomerInfo($customer);
            $this->loadCustomerOrders();
        }
    }

    private function updateCustomerInfo(Customer $customer)
    {
        $this->name_customer = $customer->name;
        $this->phone_customer = $customer->phone;
        $this->email_customer = $customer->email;
        $this->address_customer = $customer->address;
    }

    private function loadCustomerOrders()
    {
        $this->order = Order::where('customer_id', $this->customer_id)
            ->latest()
            ->get();
    }

    #[On('cart_updated')]
    public function updateCart()
    {
        $cart = Cart::getCart(auth()->id(), session()->getId());
        
        // Make sure the cart exists
        if (!$cart) {
            $this->cartItems = collect();
            $this->cartCount = 0;
            $this->totalAmount = 0;
            return;
        }
        
        // Use the correct relationship name (singular: variantImage)
        $this->cartItems = $cart->items()
            ->with(['product', 'variant.variantImage'])
            ->get();
        
        $this->cartCount = $this->cartItems->sum('quantity');
        $this->totalAmount = $cart->total_amount;
    }

    public function updateQuantity($itemId, $change)
    {
        $cartItem = CartItem::find($itemId);
        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $change;
            
            if ($newQuantity <= 0) {
                $this->removeItem($itemId);
                return;
            }

            // Validate stock
            if ($cartItem->variant->stock < $newQuantity) {
                Notification::make()
                    ->title("Chỉ còn {$cartItem->variant->stock} sản phẩm")
                    ->warning()
                    ->send();
                return;
            }

            $cartItem->update(['quantity' => $newQuantity]);
            $cartItem->cart->updateTotal();
            $this->updateCart();
        }
    }

    public function removeItem($itemId)
    {
        $cartItem = CartItem::with('product')->find($itemId);
        if ($cartItem) {
            $productName = $cartItem->product->name;
            $cart = $cartItem->cart;
            
            $cartItem->delete();
            $cart->updateTotal();
            
            if ($cart->items()->count() === 0) {
                $cart->delete();
            }

            $this->updateCart();

            Notification::make()
                ->title('Đã xóa sản phẩm')
                ->success()
                ->body("Đã xóa {$productName} khỏi giỏ hàng")
                ->duration(3000)
                ->send();
        }
    }

    #[On('clear_cart_after_dat_hang')]
    public function handle_clear_cart_after_dat_hang()
    {
        $cart = Cart::getCart(auth()->id(), session()->getId());
        $cart->items()->delete();
        $cart->delete();
        
        $this->updateCart();
        $this->loadCustomerData();
    }

    public function clear_cart()
    {
        $cart = Cart::getCart(auth()->id(), session()->getId());
        $cart->items()->delete();
        $cart->delete();
        
        $this->updateCart();
        $this->dispatch('clear_cart');
    }

    public function render()
    {
        $this->loadProducts();
        return view('livewire.navbar');
    }

    private function loadProducts()
    {
        $this->products = Cache::remember('all_products', 3600, function () {
            return Product::all();
        });

        $this->brands = $this->products->pluck('brand')->filter()->unique();
        $this->types = $this->products->pluck('type')->filter()->unique();
    }

    public function updatedSearchTerm()
    {
        if (strlen($this->searchTerm) < 1) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Product::where('name', 'like', '%' . $this->searchTerm . '%')
            ->with(['variants.variantImage'])
            ->take(100)
            ->get();
    }
}
