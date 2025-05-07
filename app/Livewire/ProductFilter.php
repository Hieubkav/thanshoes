<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Setting;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Request;

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
    public $tagSelected = [];
    public $sort = 'latest';
    public $showFiltersMobile = false; // Thêm biến để theo dõi trạng thái hiển thị của bộ lọc trên mobile

    public function mount()
    {
        // Kiểm tra xem có tham số 'type' trong URL không
        $type = Request::query('type');
        if ($type) {
            $this->typeSelected = [$type];
        }
        $brand = Request::query('brand');
        if ($brand) {
            $this->brandSelected = [$brand];
        }
        $tag = Request::query('tag');
        if ($tag) {
            $this->tagSelected = [$tag];
        }
        $tatvo = Request::query('tatvo');
        if ($tatvo) {
            $this->tatvo = 'true';
        }
        $phukien = Request::query('phukien');
        if ($phukien) {
            $this->phukien = 'true';
        }
    }

    // Thêm phương thức để bật/tắt hiển thị bộ lọc trên mobile
    public function toggleFiltersMobile()
    {
        $this->showFiltersMobile = !$this->showFiltersMobile;
    }

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
    
    public function updatingTagSelected($value)
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
        $this->tagSelected = [];
        $this->sort = 'latest';
        $this->on_page = 12;
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()->where('name', 'like', '%' . $this->search . '%');
        
        // Lấy những sản phẩm có số lượng lớn hơn 0
        $query->whereHas('variants', function ($q) {
            $q->where('stock', '>', 0);
        });

        // Lọc bỏ các sản phẩm bị cấm
        $setting = Setting::first();
        $bannedNames = array_filter([
            $setting->ban_name_product_one,
            $setting->ban_name_product_two,
            $setting->ban_name_product_three,
            $setting->ban_name_product_four,
            $setting->ban_name_product_five
        ]);
        
        foreach($bannedNames as $bannedName) {
            if(!empty($bannedName)) {
                $query->where('name', 'not like', '%' . $bannedName . '%');
            }
        }
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
        
        if (count($this->tagSelected) > 0) {
            $query->whereHas('tags', function($q) {
                $q->whereIn('name', $this->tagSelected);
            });
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
        
        // Lấy danh sách các tag để hiển thị trong bộ lọc
        $tags = Tag::withCount('products')->orderByDesc('products_count')->take(15)->get();

        return view('livewire.product-filter', [
            'products' => $products,
            'tong_giay' => $this->tong_giay,
            'tags' => $tags
        ]);
    }
}
