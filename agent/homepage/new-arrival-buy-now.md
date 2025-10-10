# New Arrival – Mua Ngay

## Vấn đề đã thấy
- `buyNowFromModal` trong `resources/views/component/new_arrival.blade.php` bỏ qua trạng thái, nên vẫn chuyển hướng `/checkout` khi API trả lỗi.
- Modal luôn bật nút *Mua ngay* dù biến thể hết hàng (`variant.stock <= 0`), khiến người dùng đi tới checkout rồi bị từ chối ở bước sau.
- Khối script bị nhân đôi (hai định nghĩa `buyNowFromModal`, `selectColor`, …), rủi ro sửa một nơi nhưng quên nơi còn lại.
- Sau khi refactor, cần nhớ kiểm `data.success === true`; nếu chỉ dựa vào `response.ok` thì các lỗi redirect/throttle sẽ gây false negative.
- Backend `addToCartAjax` trước đây validate `exists:product_variants,id` (table cũ), trên DB hiện tại là `variants` nên request luôn 500. Nhớ đồng bộ rule khi schema đổi.
- Nếu component đồng bộ với Livewire navbar, nhớ `Livewire.dispatch('cart_updated')` sau khi API thành công để header cập nhật ngay, tránh phải F5.

## Lưu ý khi sửa
1. Tạo helper chung để gửi yêu cầu add-to-cart, kiểm tra `response.ok` và `data.success` trước khi redirect.
2. Trước khi bật nút *Mua ngay*, xác nhận `variant.stock > 0`; nếu không thì disable và hiển thị cảnh báo rõ ràng.
3. Gom các hàm JS của modal vào một file duy nhất hoặc đảm bảo chỉ render một lần trong blade để tránh trùng lặp logic.

## Pattern
- Ưu tiên tách logic fetch vào hàm `handleVariantAction(action)` và truyền `action === 'buy'` để quyết định redirect. Kiểm `data.success === true`, không dựa đơn thuần vào HTTP status.
- Sử dụng guard pattern cho stock: `if (!variant || variant.stock < 1) { ... return; }`.
- Sau khi refactor, thêm log/notification rõ ràng để dễ debug (ví dụ toast theo action).
