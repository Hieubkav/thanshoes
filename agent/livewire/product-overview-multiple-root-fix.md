# Fix: Livewire Multiple Root Elements Error - Product Overview

## Vấn đề
```
Livewire only supports one HTML element per component. 
Multiple root elements detected for component: [product-overview]
```

## Nguyên nhân
File `resources/views/livewire/product-overview.blade.php` có **nhiều root elements** nằm ngoài `<div>` chính:

1. `<div>` chính (dòng 1-273)
2. `<div id="imageGalleryModal">` (dòng 274-311) ❌
3. `<script>` tags (nhiều chỗ) ❌
4. `<style>` tag ❌
5. Các section sản phẩm liên quan ❌
6. `@include('partials.quick_buy_modal')` ❌

## Quy tắc Livewire
**Livewire component PHẢI có duy nhất 1 root element bao bọc toàn bộ nội dung.**

## Giải pháp
Đảm bảo TẤT CẢ nội dung (HTML, scripts, styles, modals, includes) đều nằm TRONG `<div>` chính:

```blade
<div>
    {{-- Tất cả nội dung ở đây --}}
    
    {{-- HTML content --}}
    <div class="product-details">...</div>
    
    {{-- Modals --}}
    <div id="imageGalleryModal">...</div>
    
    {{-- Scripts --}}
    <script>...</script>
    
    {{-- Styles --}}
    <style>...</style>
    
    {{-- Related products --}}
    @if ($related_products->isNotEmpty())
        <div>...</div>
    @endif
    
    {{-- Includes --}}
    @include('partials.quick_buy_modal')
    
</div>{{-- End of main Livewire component div --}}
```

## Lưu ý quan trọng

### ✅ ĐÚNG:
```blade
<div>
    <div>Content 1</div>
    <script>...</script>
    <style>...</style>
    @include('partial')
</div>
```

### ❌ SAI:
```blade
<div>Content 1</div>
<div>Content 2</div>
<script>...</script>
```

### ❌ SAI:
```blade
<div>
    Content
</div>

<script>...</script>
```

### Ghi chu them (2025-03-16)
- Phat hien comment Blade nam ngoai root ({{-- ... --}}) van bi Livewire xem nhu mot node rieng => gay loi Multiple root.
- Cach xu ly: xoa comment/whitespace thua hoac dua comment vao ben trong the div root truoc khi dong.


## Cách kiểm tra
1. Mở file Livewire component view
2. Đếm số lượng root elements (elements không có parent)
3. Nếu > 1 → Lỗi
4. Bọc tất cả vào 1 `<div>` duy nhất

## File đã sửa
- `resources/views/livewire/product-overview.blade.php`


## Các bước đã thực hiện

### 1. Xác định vấn đề
File có nhiều root elements:
- `<div>` chính (dòng 1-273)
- `<div id="imageGalleryModal">` (dòng 274-311) ❌
- Nhiều `<script>` tags ❌
- `<style>` tag ❌
- Sections sản phẩm liên quan ❌
- `@include('partials.quick_buy_modal')` ❌

### 2. Sửa cấu trúc
- Đảm bảo TẤT CẢ nội dung nằm trong 1 `<div>` duy nhất
- Dòng 1: `<div>` mở
- Dòng 923: `</div>` đóng
- Dòng 924: Comment riêng biệt

### 3. Clear cache
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan optimize:clear
```

### 4. Xóa compiled views cũ
```bash
Remove-Item storage/framework/views/*.php
```

## Test
Truy cập: `/product/{slug}` để kiểm tra lỗi đã được fix.

**URL test:**
```
http://127.0.0.1:8000/product/giay-nike-air-force-1-shadow-sail-pink-glaze-nike-air-force-1-shadow-trang-moc-hong-xanh-s
```

## Áp dụng cho các Livewire components khác
Kiểm tra tất cả các file trong `resources/views/livewire/` để đảm bảo tuân thủ quy tắc này:
- `checkout.blade.php`
- `product-filter.blade.php`
- `navbar.blade.php`
- `ai-chatbot.blade.php`
- v.v.





\
### Ghi chu them (2025-10-07)
- Rieng voi trang `product-overview`, chuyen viec goi `PriceHelper::getDiscountPercentage()` va `PriceHelper::getDiscountType()` sang class Livewire roi truyen xuong view qua bien `$globalDiscountPercent` va `$globalDiscountType` de tranh phai dat `@php use` ben ngoai root `<div>`.
- Lay `size_shoes_image` mot lan trong `render()` (su dung `Setting::query()->value('size_shoes_image')`) va tai su dung `$sizeShoesImage` trong Blade, tranh goi `Setting::first()` lap lai.
- Neu can goi helper trong Blade thi dung duong dan day du `\App\Helpers\PriceHelper::...` hoac dat `use` ngay sau the root de khong bo sung them root node.
- Sau khi sua neu trang con giu cache cu thi chay `php artisan view:clear`.
- Khi component có `<script>` chứa template literal (backtick) với HTML, DOMDocument sẽ tách thành phần tử mới và Livewire báo multiple root. Di chuyển script này sang `@push('scripts')` hoặc refactor sang JS thuần để tránh lỗi. (Đã áp dụng cho toast quick-buy + gallery script)
