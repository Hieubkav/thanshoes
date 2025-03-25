<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Variant;
use App\Models\Product;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\On;

class ProductOverview extends Component
{
    // Product related properties
    public $product;
    public $main_image;

    // User selection properties
    public $selectedColor = [];
    public $selectedSize = [];
    public $clicked = false;
    public $countfilter = 1;

    // Processing flag
    public $isProcessingAddtoCart = false;

    public function mount($product)
    {
        $this->product = $product;
        $this->initializeFilters();
    }

    private function initializeFilters()
    {
        if ($this->product->variants->where('color', '!=', null)->count() > 0) {
            $this->countfilter = 2;
        }
    }

    public function addToCart()
    {
        if ($this->isProcessingAddtoCart) {
            return;
        }
        $this->isProcessingAddtoCart = true;

        try {
            if (!$this->validateSelections()) {
                $this->showError('Chưa chọn phân loại sản phẩm', 'Vui lòng chọn đầy đủ!');
                return;
            }

            $variant = $this->getSelectedVariant();
            if (!$variant) {
                $this->showError('Lỗi hệ thống', 'Không tìm thấy phân loại sản phẩm');
                return;
            }

            if (!$this->validateStock($variant)) {
                $this->showError('Không thể thêm vào giỏ hàng', 'Sản phẩm đã hết hàng hoặc thêm vượt quá số lượng tồn kho');
                return;
            }

            $this->addVariantToCart($variant);
            $this->showSuccess();

        } finally {
            $this->isProcessingAddtoCart = false;
        }
    }

    private function validateSelections(): bool
    {
        if ($this->countfilter == 1 && empty($this->selectedSize)) {
            return false;
        }
        if ($this->countfilter == 2 && (empty($this->selectedColor) || empty($this->selectedSize))) {
            return false;
        }
        return true;
    }

    private function getSelectedVariant(): ?Variant
    {
        $query = Variant::where('product_id', $this->product->id);
        
        if ($this->countfilter == 1) {
            return $query->where('size', $this->selectedSize)->first();
        }
        
        return $query->where('color', $this->selectedColor)
                    ->where('size', $this->selectedSize)
                    ->first();
    }

    private function validateStock(Variant $variant): bool
    {
        $cart = Cart::getCart(auth()->id(), session()->getId());
        $existingItem = $cart->items()
            ->where('product_id', $this->product->id)
            ->where('variant_id', $variant->id)
            ->first();

        if (!$existingItem) {
            return $variant->stock > 0;
        }

        return $variant->stock > $existingItem->quantity;
    }

    private function addVariantToCart(Variant $variant)
    {
        $cart = Cart::getCart(auth()->id(), session()->getId());
        
        $cartItem = $cart->items()
            ->where('product_id', $this->product->id)
            ->where('variant_id', $variant->id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            $cart->items()->create([
                'product_id' => $this->product->id,
                'variant_id' => $variant->id,
                'quantity' => 1,
                'price' => $variant->price
            ]);
        }

        $cart->updateTotal();
        $this->dispatch('cart_updated');
    }

    private function showError(string $title, string $message)
    {
        Notification::make()
            ->title($title)
            ->danger()
            ->iconColor('danger')
            ->icon('heroicon-o-x-mark')
            ->duration(3000)
            ->body($message)
            ->send();
    }

    private function showSuccess()
    {
        Notification::make()
            ->title('Thêm giỏ hàng thành công')
            ->success()
            ->duration(3000)
            ->body('Chúc mừng bạn')
            ->send();
    }

    public function updatingSelectedColor($color)
    {
        $this->clicked = true;
        $this->selectedSize = [];
        $this->dispatch('colorSelected', $color);
    }

    public function updatingSelectedSize($value)
    {
        if ($this->clicked == false && $this->countfilter == 2) {
            $this->dispatch('checkcolorfirst');
        } else {
            $this->clicked = true;
            $this->dispatch('sizeSelected', $value);
        }
    }

    #[On('clear_cart')]
    public function clear_cart_sucess()
    {
        $cart = Cart::getCart(auth()->id(), session()->getId());
        $cart->items()->delete();
        $cart->delete();

        $this->showError('Đã xoá giỏ hàng', 'Thêm sản phẩm vào giỏ hàng để mua hàng!');
    }

    #[On('clear_cart_after_dat_hang')]
    public function clear_cart_after_dat_hang()
    {
        $cart = Cart::getCart(auth()->id(), session()->getId());
        $cart->items()->delete();
        $cart->delete();

        $this->selectedColor = [];
        $this->selectedSize = [];
    }

    public function render()
    {
        $list_link_images_variants = $this->getProductImages();
        $this->main_image = $list_link_images_variants->first();

        $list_colors = $this->getUniqueColors();
        $list_sizes = $this->getUniqueSizes();
        $related_products = $this->getRelatedProducts();
        $same_brand_products = $this->getSameBrandProducts();

        return view('livewire.product-overview', [
            'list_images_variants' => $list_link_images_variants,
            'main_image' => $this->main_image,
            'product' => $this->product,
            'related_products' => $related_products,
            'same_brand_products' => $same_brand_products,
            'list_colors' => $list_colors,
            'list_sizes' => $list_sizes,
        ]);
    }

    private function getProductImages()
    {
        return $this->product->variants
            ->map(fn($variant) => $variant->variant_images)
            ->flatten()
            ->map(fn($image) => $image->image)
            ->unique()
            ->filter();
    }

    private function getRelatedProducts()
    {
        return Product::where('id', '!=', $this->product->id)
            ->where('type', $this->product->type)
            ->take(4)
            ->get()
            ->map(function ($product) {
                $product->first_image = optional($product->variants->first()?->variant_images->first())->image;
                return $product;
            });
    }

    private function getSameBrandProducts()
    {
        return Product::where('id', '!=', $this->product->id)
            ->where('brand', $this->product->brand)
            ->take(4)
            ->get()
            ->map(function ($product) {
                $product->first_image = optional($product->variants->first()?->variant_images->first())->image;
                return $product;
            });
    }


    private function getUniqueColors()
    {
        return $this->product->variants
            ->map(fn($variant) => $variant->color)
            ->unique()
            ->filter();
    }

    private function getUniqueSizes()
    {
        return $this->product->variants
            ->map(fn($variant) => $variant->size)
            ->unique()
            ->filter();
    }
}
