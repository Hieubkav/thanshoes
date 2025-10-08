# Mobile Bottom Actions cho Product Detail

## Vấn đề
Trang chi tiết sản phẩm trên mobile có vấn đề UX/UI:
- Bảng size giày và nút mua ngay/thêm giỏ hàng bị chèn vào nhau, chật chội
- Khó thao tác trên màn hình nhỏ
- Không tối ưu cho trải nghiệm di động

## Giải pháp
Tạo bottom navigation sticky chỉ hiện trên mobile với các tính năng:

### 1. Component `product_bottom_actions.blade.php`
- **Fixed bottom**: Ghim ở cuối màn hình mobile (ẩn trên desktop)
- **Hiển thị/ẩn theo scroll**: Ẩn khi scroll xuống, hiện khi scroll lên hoặc gần cuối trang
- **Safe area support**: Hỗ trợ iPhone notch

### 2. Tính năng
- **Hiển thị giá**: Giá sản phẩm và giá gốc (nếu có giảm giá)
- **Tồn kho**: Hiển thị số lượng tồn kho theo phân loại đã chọn
- **Nút Mua ngay**: Gọi `openQuickBuy()` của Livewire
- **Nút Thêm giỏ hàng**: Gọi `addToCart()` của Livewire  
- **Loading states**: Hiển thị spinner khi đang xử lý

### 3. Responsive Desktop vs Mobile
- **Desktop** (≥768px): Giữ nguyên layout cũ với các nút ở body
- **Mobile** (<768px): Ẩn các nút ở body, chuyển vào bottom nav

### 4. Layout changes
**Trước (Mobile)**:
- Các nút chèn vào bảng size gây chật chội

**Sau (Mobile)**:
- Các nút chuyển xuống bottom nav
- Body chỉ còn thông tin sản phẩm
- Bảng size có nút riêng để mở modal

### 5. JavaScript Features
- **Scroll behavior**: Tự động ẩn/hiện theo hướng scroll
- **Smooth transitions**: Animation mượt mà
- **Touch-friendly**: Kích thước nút phù hợp cho mobile

### 6. CSS Optimizations
- **Z-index**: Đảm bảo luôn ở trên cùng (z-50)
- **Shadow**: Hiệu ứng nổi cho rõ phân lớp
- **Padding**: Thêm padding bottom cho main content

## Files thay đổi

### New files
- `resources/views/partials/product_bottom_actions.blade.php` - Component bottom actions

### Modified files  
- `resources/views/livewire/product-overview.blade.php`:
  - Thêm `hidden md:flex` vào desktop action buttons
  - Thêm mobile size chart button
  - Include bottom actions component
- `resources/views/shop/product_overview.blade.php`:
  - Thêm CSS padding bottom cho mobile

## Testing
Kiểm tra trên các màn hình:
- Mobile: 320px - 767px (bottom nav visible)
- Tablet/Desktop: ≥768px (bottom nav hidden, desktop layout)

## Lợi ích
- **UX tốt hơn**: Không gian thao tác rộng rãi hơn
- **Dễ tiếp cận**: Nút luôn ở vị trí quen thuộc (bottom)
- **Không gián đoạn**: Nội dung sản phẩm không bị che khuất
- **Responsive**: Tối ưu cho từng kích thước màn hình
