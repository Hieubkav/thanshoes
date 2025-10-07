# Quick buy - phản hồi & email

- Bổ sung trạng thái `$quickBuySuccess` trong Livewire `ProductOverview` để giữ drawer mở, hiển thị khối xác nhận kèm mã đơn/tổng tiền và nút hành động (`Đóng`, `Đặt thêm sản phẩm`).
- Dispatch thêm sự kiện `quick-buy-success` để JS phía storefront hiển thị toast nhỏ bên dưới (class `quick-buy-toast`).
- Đảm bảo `createQuickBuyOrder()` trả về `Order` kèm quan hệ `customer` và gởi mail tới: danh sách admin (`User::query()->pluck('email')` lọc unique), email chính `tranmanhhieu10@gmail.com`, và email khách nếu có.
- Nhớ gọi `resetQuickBuySuccess()` khi mở/đóng/đặt mới để tránh lặp lại banner cũ.

- Modal + toast được bơm qua `@push('modals')` và `@push('scripts')` nên layout phải có `@stack('modals')` + `@stack('scripts')` (đã thêm ở `layouts/shoplayout.blade.php`).
