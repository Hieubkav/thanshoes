<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
use App\Services\PriceService;
use App\Services\ProductCacheService;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
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
    public $originalTotalAmount = 0;
    public $discountAmount = 0;
    public $discountPercentage = 0;
    public $discountApplied = false;

    // Customer related properties
    public $customer_id = null;
    public $name_customer = "";
    public $phone_customer = "";
    public $email_customer = "";
    public $address_customer = "";

    // Order related properties
    public $order;
    public $pendingOrdersCount = 0;

    public function mount()
    {
        $this->initializeOrder();
        $this->updateCart();
        $this->loadCustomerData();
    }

    #[On('user_logged_in')]
    public function handleUserLoggedIn()
    {
        $this->loadCustomerData();
        $this->updateCart();
    }

    #[On('user_logged_out')]
    public function handleUserLoggedOut()
    {
        $this->resetUserData();
        $this->updateCart();
    }

    private function resetUserData()
    {
        $this->customer_id = null;
        $this->name_customer = "";
        $this->phone_customer = "";
        $this->email_customer = "";
        $this->address_customer = "";
        $this->order = new Collection();
        $this->pendingOrdersCount = 0;
    }

    private function initializeOrder()
    {
        $this->order = new Collection();
        $this->pendingOrdersCount = 0;
    }

    private function loadCustomerData()
    {
        // Ưu tiên customer đã đăng nhập
        if (auth('customers')->check()) {
            $customer = auth('customers')->user(); // Đây giờ là Customer model
            $this->customer_id = $customer->id;
            $this->name_customer = $customer->name;
            $this->email_customer = $customer->email;
            $this->phone_customer = $customer->phone;
            $this->address_customer = $customer->address;
            $this->loadCustomerOrders();
            return;
        }

        // Fallback cho session customer_id (khách chưa đăng nhập)
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

        $this->pendingOrdersCount = $this->order
            ->whereIn('status', ['pending', 'processing'])
            ->count();
    }

    #[On('cart_updated')]
    public function updateCart()
    {
        // Sử dụng customer_id nếu đã đăng nhập, ngược lại dùng session_id
        $customerId = auth('customers')->check() ? auth('customers')->id() : null;
        $sessionId = auth('customers')->check() ? null : session()->getId();

        $cart = Cart::getCart($customerId, $sessionId);

        // Make sure the cart exists
        if (!$cart) {
            $this->cartItems = collect();
            $this->cartCount = 0;
            $this->totalAmount = 0;
            $this->originalTotalAmount = 0;
            $this->discountAmount = 0;
            $this->discountPercentage = 0;
            $this->discountApplied = false;
            return;
        }
        
        // Use the correct relationship name (singular: variantImage)
        $this->cartItems = $cart->items()
            ->with(['product', 'variant.variantImage'])
            ->get();
        
        $this->cartCount = $this->cartItems->sum('quantity');
        
        // Tính tổng tiền gốc
        $this->originalTotalAmount = $this->cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        // Áp dụng giảm giá nếu được bật
        $discountInfo = PriceService::getDiscountInfo($this->originalTotalAmount);
        $this->discountApplied = $discountInfo['is_applied'];
        
        if ($this->discountApplied) {
            $this->totalAmount = $discountInfo['discounted_price'];
            $this->discountAmount = $discountInfo['discount_amount'];
            $this->discountPercentage = $discountInfo['discount_percentage'];
        } else {
            $this->totalAmount = $this->originalTotalAmount;
        }
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
        $customerId = auth('customers')->check() ? auth('customers')->id() : null;
        $sessionId = auth('customers')->check() ? null : session()->getId();

        $cart = Cart::getCart($customerId, $sessionId);
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }

        $this->updateCart();
        $this->loadCustomerData();
    }

    public function clear_cart()
    {
        $customerId = auth('customers')->check() ? auth('customers')->id() : null;
        $sessionId = auth('customers')->check() ? null : session()->getId();

        $cart = Cart::getCart($customerId, $sessionId);
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }

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
        $this->products = ProductCacheService::getHomepageProducts();

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
