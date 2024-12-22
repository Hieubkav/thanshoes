<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class Navbar extends Component
{
    public $products ;
    public $search;
    public $brands;
    public $types;

    public function render()
    {
        $this->products = Product::all();
        

        // Lấy ra danh sách những thuộc tính khác nhau có thể có của product->brand trừ rỗng
        $this->brands = $this->products->pluck('brand')->filter()->unique();

        // lấy ra danh sách những bảng ghi khác nhau có thể có của product->type
        $this->types = $this->products->pluck('type')->filter()->unique();
        return view('livewire.navbar');
    }
}
