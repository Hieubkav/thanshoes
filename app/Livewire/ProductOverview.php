<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Setting;
use App\Models\Variant;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Mail\OrderShipped;
use App\Helpers\PriceHelper;
use App\Helpers\VnLocation;
use App\Services\PriceService;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\On;

class ProductOverview extends Component
{
    // Product related properties
    public $product;
    public $main_image;
    
    // Discount related properties
    public $showDiscount = false;
    public $discountedPrice = 0;
    public $discountAmount = 0;
    public $discountPercentage = 0;

    // User selection properties
    public $selectedColor = '';
    public $selectedSize = '';
    public $clicked = false;
    public $countfilter = 1;

    // Processing flag
    public $isProcessingAddtoCart = false;

    public $showQuickBuyModal = false;
    public $quickBuyName = '';
    public $quickBuyPhone = '';
    public $quickBuyEmail = '';
    public $quickBuyProvince = '';
    public $quickBuyWard = '';
    public $quickBuyAddressDetail = '';
    public $quickBuyPaymentMethod = 'cod';
    public $quickBuyQuantity = 1;
    public $quickBuyProcessing = false;
    public $quickBuySuccess = false;
    public $quickBuySuccessOrderCode = null;
    public $quickBuySuccessTotal = 0;
    public $quickBuySuccessMessage = '';
    public function mount($product)
    {
        $this->product = $product;
        $this->initializeFilters();
        $this->calculateDiscount();
        $this->prefillQuickBuyCustomer();
    }
    
    private function calculateDiscount()
    {
        // Lấy biến thể có giá thấp nhất cho sản phẩm này
        $lowestPriceVariant = $this->product->variants->min('price');
        if (!$lowestPriceVariant) return;
        
        $discountInfo = PriceService::getDiscountInfo($lowestPriceVariant);
        $this->showDiscount = $discountInfo['is_applied'];
        
        if ($this->showDiscount) {
            $this->discountedPrice = $discountInfo['discounted_price'];
            $this->discountAmount = $discountInfo['discount_amount'];
            $this->discountPercentage = $discountInfo['discount_percentage']; 
        }
    }

    private function initializeFilters(): void
    {
        $hasColorVariants = $this->product->variants->contains(function ($variant) {
            return !empty($variant->color);
        });

        $this->countfilter = $hasColorVariants ? 2 : 1;
    }

    private function prefillQuickBuyCustomer(): void
    {
        if (!session()->has('customer_id')) {
            return;
        }

        $customer = Customer::find(session('customer_id'));

        if (!$customer) {
            return;
        }

        $this->quickBuyName = $customer->name ?? '';
        $this->quickBuyPhone = $customer->phone ?? '';
        $this->quickBuyEmail = $customer->email ?? '';

        $this->quickBuyProvince = '';
        $this->quickBuyWard = '';
        $this->quickBuyAddressDetail = $customer->address ?? '';
    }

    private function resetQuickBuySuccess(): void
    {
        $this->quickBuySuccess = false;
        $this->quickBuySuccessOrderCode = null;
        $this->quickBuySuccessTotal = 0;
        $this->quickBuySuccessMessage = '';
    }

    public function openQuickBuy(): void
    {
        if (!$this->quickBuyQuantity || $this->quickBuyQuantity < 1) {
            $this->quickBuyQuantity = 1;
        }

        $this->resetQuickBuySuccess();
        $this->showQuickBuyModal = true;
    }

    public function closeQuickBuy(): void
    {
        $this->showQuickBuyModal = false;
        $this->resetQuickBuySuccess();
    }

    public function startNewQuickBuy(): void
    {
        $this->resetQuickBuySuccess();
        $this->quickBuyProcessing = false;
        $this->quickBuyQuantity = 1;
    }

    public function incrementQuickBuyQuantity(): void
    {
        $variant = $this->getSelectedVariant();

        if ($variant) {
            $this->quickBuyQuantity = $this->normalizeQuickBuyQuantity($this->quickBuyQuantity + 1, $variant);
            return;
        }

        $this->quickBuyQuantity = max(1, (int) $this->quickBuyQuantity + 1);
    }

    public function decrementQuickBuyQuantity(): void
    {
        $this->quickBuyQuantity = max(1, (int) $this->quickBuyQuantity - 1);
    }

    public function updatedQuickBuyQuantity($value): void
    {
        $variant = $this->getSelectedVariant();
        $this->quickBuyQuantity = $this->normalizeQuickBuyQuantity($value, $variant);
    }

    public function updatedQuickBuyProvince($value): void
    {
        $this->quickBuyWard = '';
    }

    public function updatedQuickBuyPaymentMethod($value): void
    {
        if (!in_array($value, ['cod', 'bank'])) {
            $this->quickBuyPaymentMethod = 'cod';
            return;
        }

        $this->quickBuyPaymentMethod = $value;
    }

    public function submitQuickBuy(): void
    {
        if ($this->quickBuyProcessing) {
            return;
        }

        $this->quickBuyProcessing = true;
        $this->resetQuickBuySuccess();

        try {
            if (!$this->ensureQuickBuySelections()) {
                return;
            }

            if (!$this->validateQuickBuyInfo()) {
                return;
            }

            $variant = $this->getSelectedVariant();

            if (!$variant) {
                $this->showError('Loi he thong', 'Khong tim thay phan loai san pham');
                return;
            }

            $quantity = $this->normalizeQuickBuyQuantity($this->quickBuyQuantity, $variant);
            $this->quickBuyQuantity = $quantity;

            if ($variant->stock < $quantity) {
                $this->showError('Khong du hang', 'Xin loi, phan loai nay khong du so luong ban yeu cau');
                return;
            }

            $order = $this->createQuickBuyOrder($variant, $quantity);

            $this->quickBuyQuantity = 1;
            $this->showQuickBuySuccess($order);
        } catch (\Exception $e) {
            $this->showError('Dat hang that bai', $e->getMessage());
        } finally {
            $this->quickBuyProcessing = false;
        }
    }

    private function normalizeQuickBuyQuantity($quantity, ?Variant $variant = null): int
    {
        $quantity = (int) $quantity;

        if ($quantity < 1) {
            $quantity = 1;
        }

        if ($variant) {
            $available = max(0, $variant->stock);

            if ($available > 0) {
                $quantity = min($quantity, $available);
            }
        }

        return $quantity;
    }

    private function ensureQuickBuySelections(): bool
    {
        if ($this->validateSelections()) {
            return true;
        }

        $message = $this->countfilter == 2
            ? 'Vui long chon day du mau va size truoc khi mua ngay.'
            : 'Vui long chon size truoc khi mua ngay.';

        $this->showError('Thieu phan loai', $message);

        return false;
    }

    private function validateQuickBuyInfo(): bool
    {
        $errors = [];

        if (empty(trim($this->quickBuyName))) {
            $errors[] = 'Vui long nhap ho ten';
        }

        if (empty(trim($this->quickBuyPhone))) {
            $errors[] = 'Vui long nhap so dien thoai';
        }

        if (empty($this->quickBuyProvince)) {
            $errors[] = 'Vui long chon tinh thanh';
        }

        if (empty($this->quickBuyWard)) {
            $errors[] = 'Vui long chon phuong xa';
        }

        if (empty(trim($this->quickBuyAddressDetail))) {
            $errors[] = 'Vui long nhap dia chi chi tiet';
        }

        if (!empty($this->quickBuyEmail) && !filter_var($this->quickBuyEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email khong dung dinh dang';
        }

        foreach ($errors as $error) {
            Notification::make()
                ->title('Thieu thong tin')
                ->body($error)
                ->danger()
                ->send();
        }

        return empty($errors);
    }

    private function createQuickBuyOrder(Variant $variant, int $quantity): Order
    {
        $pricing = PriceService::getDiscountInfo($variant->price * $quantity);

        $province = VnLocation::findProvince($this->quickBuyProvince);
        $ward = VnLocation::findWard($this->quickBuyWard);

        $parts = [];
        if (!empty($this->quickBuyAddressDetail)) {
            $parts[] = trim($this->quickBuyAddressDetail);
        }
        if ($ward) {
            $parts[] = $ward['name'] ?? '';
        }
        if ($province) {
            $parts[] = $province['name'] ?? '';
        }
        $addressLabel = implode(', ', array_filter($parts));

        $customer = Customer::updateOrCreate(
            ['phone' => $this->quickBuyPhone],
            [
                'name' => $this->quickBuyName,
                'email' => $this->quickBuyEmail,
                'address' => $addressLabel,
            ]
        );

        $order = Order::create([
            'customer_id' => $customer->id,
            'total' => $pricing['discounted_price'],
            'original_total' => $pricing['is_applied'] ? $pricing['original_price'] : null,
            'discount_amount' => $pricing['is_applied'] ? $pricing['discount_amount'] : null,
            'discount_type' => $pricing['is_applied'] ? $pricing['discount_type'] : null,
            'discount_percentage' => $pricing['is_applied'] ? $pricing['discount_percentage'] : null,
            'payment_method' => $this->quickBuyPaymentMethod,
            'status' => 'pending',
        ]);
        $order->notes = $addressLabel;
        $order->save();

        OrderItem::create([
            'order_id' => $order->id,
            'variant_id' => $variant->id,
            'quantity' => $quantity,
            'price' => $variant->price,
        ]);

        $variant->decrement('stock', $quantity);

        $orderWithRelations = Order::with(['items.variant.variantImage', 'items.variant.product', 'customer'])->find($order->id);

        $adminEmails = User::query()
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();

        foreach ($adminEmails as $email) {
            Mail::to($email)->send(new OrderShipped($orderWithRelations));
        }

        $primaryNotificationEmail = 'tranmanhhieu10@gmail.com';
        if (!$adminEmails->contains($primaryNotificationEmail)) {
            Mail::to($primaryNotificationEmail)->send(new OrderShipped($orderWithRelations));
        }

        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new OrderShipped($orderWithRelations));
        }

        session()->put('customer_id', $customer->id);

        return $orderWithRelations;
    }

    private function showQuickBuySuccess(Order $order): void
    {
        $this->quickBuySuccess = true;
        $this->quickBuySuccessOrderCode = $order->id;
        $this->quickBuySuccessTotal = $order->total ?? 0;
        $this->quickBuySuccessMessage = 'Cam on ban, chung toi se lien he de xac nhan don hang som nhat.';

        Notification::make()
            ->title('Dat hang thanh cong!')
            ->body($this->quickBuySuccessMessage)
            ->success()
            ->send();

        $this->dispatch(
            'quick-buy-success',
            orderId: (string) $order->id,
            total: number_format($order->total ?? 0, 0, ',', '.')
        );
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
        $this->selectedSize = '';
        $this->dispatch('colorSelected', $color);
    }

    public function updatedSelectedColor($value)
    {
        $this->quickBuyQuantity = 1;
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

    public function updatedSelectedSize($value)
    {
        $this->quickBuyQuantity = 1;
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

        $this->selectedColor = '';
        $this->selectedSize = '';
        $this->quickBuyProvince = '';
        $this->quickBuyWard = '';
        $this->quickBuyAddressDetail = '';
        $this->quickBuyPaymentMethod = 'cod';
    }

    public function render()
    {
        $list_link_images_variants = $this->getProductImages();
        $this->main_image = $list_link_images_variants->first();

        $list_colors = $this->getUniqueColors();
        $list_sizes = $this->getUniqueSizes();
        $related_products = $this->getRelatedProducts();
        $same_brand_products = $this->getSameBrandProducts();
        $list_images_product = $this->product->productImages;

        $globalDiscountPercent = PriceHelper::getDiscountPercentage();
        $globalDiscountType = PriceHelper::getDiscountType();
        $sizeShoesImage = Setting::query()->value('size_shoes_image');
            
        return view('livewire.product-overview', [
            'list_images_variants' => $list_link_images_variants,
            'main_image' => $this->main_image,
            'product' => $this->product,
            'related_products' => $related_products,
            'same_brand_products' => $same_brand_products,
            'list_colors' => $list_colors,
            'list_sizes' => $list_sizes,
            'list_images_product' => $list_images_product,
            'quickBuyProvinces' => VnLocation::provinces(),
            'quickBuyProvinceSelected' => !empty($this->quickBuyProvince),
            'quickBuyWards' => $this->quickBuyProvince ? VnLocation::wardsOfProvince((string) $this->quickBuyProvince) : [],
            'showDiscount' => $this->showDiscount,
            'discountedPrice' => $this->discountedPrice,
            'discountPercentage' => $this->discountPercentage,
            'globalDiscountPercent' => $globalDiscountPercent,
            'globalDiscountType' => $globalDiscountType,
            'sizeShoesImage' => $sizeShoesImage,
        ]);
    }

    private function getProductImages()
    {
        return $this->product->variants
            ->map(function ($variant) {
                return $variant->variantImage?->image;
            })
            ->filter()
            ->unique()
            ->values();
    }

    private function getRelatedProducts()
    {
        $query = Product::where('id', '!=', $this->product->id)
            ->where('type', $this->product->type);

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

        return $query->take(8)->get()
            ->map(function ($product) {
                $firstVariant = $product->variants->first();
                $product->first_image = $firstVariant?->variantImage?->image;
                return $product;
            });
    }

    private function getSameBrandProducts()
    {
        $query = Product::where('id', '!=', $this->product->id)
            ->where('brand', $this->product->brand);

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

        return $query->take(8)->get()
            ->map(function ($product) {
                $firstVariant = $product->variants->first();
                $product->first_image = $firstVariant?->variantImage?->image;
                return $product;
            });
    }

    private function getUniqueColors()
    {
        return $this->product->variants
            ->map(fn($variant) => $variant->color)
            ->unique()
            ->filter()
            ->sort(); // Add sorting in ascending order
    }

    private function getUniqueSizes()
    {
        return $this->product->variants
            ->map(fn($variant) => $variant->size)
            ->unique()
            ->filter()
            ->sort(); // Add sorting in ascending order
    }
}






















