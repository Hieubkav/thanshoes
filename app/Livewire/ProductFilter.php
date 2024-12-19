<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductFilter extends Component
{
    use WithPagination;

    public $search = '';
    public $giay = 'false';
    public $ao = 'false';
    public $tatvo = 'false';
    public $phukien = 'false';
    public $on_page = 12;
    public $tong_giay = 0;
    public $typeSelected = [];
    public $brandSelected = [];
    public $sort = 'latest';

    public function updatingSort()
    {
        $this->on_page = 12;
        $this->resetPage();
    }

    // Reset phân trang khi tìm kiếm hoặc lọc thay đổi
    public function updatingSearch()
    {
        $this->on_page = 12;
        $this->dispatch('show_cho_vui');
        $this->resetPage();
    }

    public function filterCategory($category)
    {
        if ($category == 'giay') {
            $this->giay = $this->giay === 'true' ? 'false' : 'true';
            $this->ao = $this->tatvo = $this->phukien = 'false';
        } elseif ($category == 'ao') {
            $this->ao = $this->ao === 'true' ? 'false' : 'true';
            $this->giay = $this->tatvo = $this->phukien = 'false';
        } elseif ($category == 'tatvo') {
            $this->tatvo = $this->tatvo === 'true' ? 'false' : 'true';
            $this->giay = $this->ao = $this->phukien = 'false';
        } elseif ($category == 'phukien') {
            $this->phukien = $this->phukien === 'true' ? 'false' : 'true';
            $this->giay = $this->ao = $this->tatvo = 'false';
        }
        $this->on_page = 12;
        $this->resetPage();
    }

    public function loadMore()
    {
        $this->on_page += 12;
    }

    public function updatingTypeSelected($value)
    {
        $this->on_page = 12;
        $this->resetPage();
    }

    public function updatingBrandSelected($value)
    {
        $this->on_page = 12;
        $this->resetPage();
    }

    public function clearfilter(){
        $this->search = '';
        $this->giay = 'false';
        $this->ao = 'false';
        $this->tatvo = 'false';
        $this->phukien = 'false';
        $this->typeSelected = [];
        $this->brandSelected = [];
        $this->sort = 'latest';
        $this->on_page = 12;
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()->where('name', 'like', '%' . $this->search . '%');

        if ($this->giay === 'true') {
            $query->where('name', 'like', '%giày%');
        }

        if ($this->ao === 'true') {
            $query->where('name', 'like', '%áo thun%');
        }

        if ($this->tatvo === 'true') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%tất%')
                    ->orWhere('name', 'like', '%vớ%')
                    ->orWhere('name', 'like', '%dép%');
            });
        }

        if ($this->phukien === 'true') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%hộp%')
                    ->orWhere('name', 'like', '%túi%')
                    ->orWhere('name', 'like', '%chai vệ sinh%')
                    ->orWhere('name', 'like', '%bàn chải%')
                    ->orWhere('name', 'like', '%hút ẩm%')
                    ->orWhere('name', 'like', '%khăn%')
                    ->orWhere('name', 'like', '%giữ form%')
                    ->orWhere('name', 'like', '%Giầy Dặn%')
                    ->orWhere('name', 'like', '%Giấy gói%')
                    ->orWhere('name', 'like', '%[Quà Tặng] Giày Sneaker Thể Thao Tặng Kèm HD%');
            });
        }

        if (count($this->typeSelected) > 0) {
            $query->whereIn('type', $this->typeSelected);
        }

        if (count($this->brandSelected) > 0) {
            $query->whereIn('brand', $this->brandSelected);
        }

        if ($this->sort === 'latest') {
            $query = $query->orderBy('updated_at', 'desc');
        } elseif ($this->sort === 'price_asc') {
            $query = $query->with(['variants' => function ($q) {
                $q->orderBy('price', 'asc');
            }])->orderBy(function ($q) {
                $q->selectRaw('MIN(price)')
                    ->from('variants')
                    ->whereColumn('variants.product_id', 'products.id');
            }, 'asc');
        } elseif ($this->sort === 'price_desc') {
            $query = $query->with(['variants' => function ($q) {
                $q->orderBy('price', 'desc');
            }])->orderBy(function ($q) {
                $q->selectRaw('MIN(price)')
                    ->from('variants')
                    ->whereColumn('variants.product_id', 'products.id');
            }, 'desc');
        }

        $products = $query->latest()->take($this->on_page)->get();
        $this->tong_giay = $query->count();

        return view('livewire.product-filter', [
            'products' => $products,
            'tong_giay' => $this->tong_giay
        ]);
    }
}
