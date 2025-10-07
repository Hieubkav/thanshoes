# Quick buy: bật chọn phường/xã theo tỉnh

- Hiện tượng: trong modal mua nhanh, sau khi chọn tỉnh/thành thì dropdown phường/xã vẫn bị `disabled` vì Livewire chưa cập nhật lại property `quickBuyProvince` (mặc định `wire:model` đang ở chế độ defer).
- Cách xử lý: chuyển sang `wire:model.live` cho cả tỉnh (`quickBuyProvince`) và phường (`quickBuyWard`) để Livewire đẩy dữ liệu ngay khi thay đổi. Đồng thời chuẩn hóa dữ liệu đầu vào trong `@php` block (file `resources/views/partials/quick_buy_modal.blade.php`) và bổ sung fallback dùng `VnLocation::wardsOfProvince()` khi đã có mã tỉnh nhưng danh sách phường đang rỗng.
- Ghi nhớ: sau khi chạm vào view, chạy `php artisan view:clear` rồi `php artisan view:cache` để chắc chắn không còn cache Blade cũ.
