<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class Navbar extends Component
{
    public $products;
    public $search;
    public $brands;
    public $types;
    public $cart = [];
    public $order;
    public $customer_id = null;

    // Thông tin khách hàng
    public $name_customer = "";
    public $phone_customer = "";
    public $email_customer = "";
    public $address_customer = "";

    public function mount()
    {
        $this->order = new Collection();

        if (!Cookie::has('device_id')) {
            $deviceId = (string) Str::uuid();
            Cookie::queue('device_id', $deviceId, 60 * 24 * 365);
        } else {
            $this->cart = session()->get('cart_' . Cookie::get('device_id'), []);

            if (session()->has('customer_id')) {
                $this->customer_id = session()->get('customer_id');
                $customer = Customer::find($this->customer_id);
                
                if ($customer) {
                    $this->name_customer = $customer->name;
                    $this->phone_customer = $customer->phone;
                    $this->email_customer = $customer->email;
                    $this->address_customer = $customer->address;
                    $this->order = Order::where('customer_id', $this->customer_id)
                        ->latest()
                        ->get();
                }
            }
        }
    }

    #[On('cart_added')]
    public function add_cart_success()
    {
        $this->cart = session()->get('cart_' . Cookie::get('device_id'), []);
    }

    #[On('clear_cart_after_dat_hang')]
    public function handle_clear_cart_after_dat_hang()
    {
        $this->cart = [];
        session()->forget('cart_' . Cookie::get('device_id'));
        Cookie::queue(Cookie::forget('device_id'));
        
        // Cập nhật thông tin khách hàng từ session mới
        if (session()->has('customer_id')) {
            $customer = Customer::find(session()->get('customer_id'));
            if ($customer) {
                $this->name_customer = $customer->name;
                $this->phone_customer = $customer->phone;
                $this->email_customer = $customer->email;
                $this->address_customer = $customer->address;
            }
        }
    }

    public function clear_cart()
    {
        session()->forget('cart_' . Cookie::get('device_id'));
        Cookie::queue(Cookie::forget('device_id'));
        $this->cart = [];
        $this->dispatch('clear_cart');
    }

    public function render()
    {
        $this->products = Product::all();
        $this->brands = $this->products->pluck('brand')->filter()->unique();
        $this->types = $this->products->pluck('type')->filter()->unique();
        
        return view('livewire.navbar');
    }
}
