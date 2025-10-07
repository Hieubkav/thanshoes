# Quick Buy Post-Order Flow Notes

- Email notify logic nằm trong `app/Livewire/ProductOverview.php:createQuickBuyOrder()`. Sau khi tạo `Order` và `OrderItem`, component lặp qua `User::query()->pluck('email')` rồi `Mail::to($email)->send(new OrderShipped($orderWithRelations))`. Nếu danh sách user rỗng, fallback vẫn gửi tới `tranmanhhieu10@gmail.com`. Lưu ý: không có retry/queue; dùng `Mail::failures()` để debug khi nghi ngờ SMTP.
- Modal thành công (`resources/views/partials/quick_buy_modal.blade.php`, khối `@if ($quickBuySuccess)`) chỉ hiển thị trạng thái trong drawer; chưa tự đóng hoặc chuyển hướng. Hai CTA hiện tại: `wire:click="closeQuickBuy"` (tiếp tục mua) và `wire:click="startNewQuickBuy"` (đặt thêm). Chưa có thông báo ngoài modal hoặc nhắc đăng nhập.
- `showQuickBuySuccess()` phát Livewire event `quick-buy-success` nhưng chưa có listener JS để gom analytics/redirect.
- Tránh spam: `submitQuickBuy()` khóa bằng cờ `quickBuyProcessing`, nhưng nếu khách reload modal trước khi email gửi có thể tạo nhiều đơn; cân nhắc debounce client-side hoặc hiển thị mã đơn để khách yên tâm.
- Nếu cần đồng bộ với checkout chuẩn, cân nhắc gom logic gửi mail và cập nhật kho vào service/event dùng chung (`OrderPlaced` event) để dễ bảo trì.

## Kế hoạch cải tiến Auto-login + Điều hướng

- Hiện cấu hình `config/auth.php:34` vẫn gán guard `web` cho provider `users` (model `App\Models\User`), trong khi login/register phía cửa hàng thao tác trực tiếp với `App\Models\Customer`. Cần tạo guard riêng (ví dụ `customers`) và chuyển các luồng front-store (`Auth::attempt`, `Auth::login`, middleware) sang guard này để tránh mismatch.
- Sau khi đăng nhập thành công, gom giỏ hàng: xóa cart theo `session_id` cũ, đồng thời dọn cart theo `customer_id` mới để tránh đơn lặp. Tái sử dụng logic từ checkout (`Cart::getCart`, `items()->delete()`), rồi chạy `updateTotal()`.
- Tạo route mới dạng `/orders` và `/orders/{order}` (middleware `auth`) trả về view/Livewire component liệt kê đơn của khách, highlight đơn mới. Trang chi tiết hiển thị thông tin tương tự email (sản phẩm, địa chỉ, trạng thái).
- Điều chỉnh `showQuickBuySuccess()`: sau khi hiển thị trạng thái thành công, dispatch event JS để client chuyển hướng tới `/orders/{orderId}` (ví dụ delay ngắn hoặc ngay khi người dùng bấm CTA “Xem đơn”). Đồng thời thêm toast/tin nhắn xác nhận.

## Check-in 2025-10-07

- Guard `customers` đã tồn tại trong `config/auth.php`, các component Livewire login/register/logout và Blade `@auth` phía storefront đều dùng guard này để thao tác đúng model `Customer`.
- Quick buy: `ProductOverview::createQuickBuyOrder()` đăng nhập khách (nếu cần), phát `user_logged_in`, gom sạch cart guest/user, lưu session `recent_order_id`, sau đó `submitQuickBuy()` redirect sang `customer.orders.show`.
- Checkout thường: sau khi tạo đơn cũng đăng nhập khách, dọn cart cũ theo session trước khi regenerate, set `recent_order_id` và emit `user_logged_in`.
- Routes mới `/orders` & `/orders/{order}` (middleware `auth:customers`) render Livewire `CustomerOrders` / `CustomerOrderDetail`, danh sách có highlight đơn vừa đặt, trang chi tiết load đủ items + địa chỉ.
- Modal quick buy cập nhật CTA dẫn sang trang chi tiết đơn; dù redirect ngay, vẫn giữ fallback nút “Tiếp tục mua sắm”.
- Bổ sung cột `customer_id` cho bảng `carts` (migration `2025_10_07_185200_add_customer_id_to_carts_table.php`) và cập nhật toàn bộ logic giỏ hàng dùng guard khách (`Cart::getCart` hoạt động theo customer_id, các merge/clear cart chuyển sang khóa mới).
- Redirect quick buy sang trang chi tiết đơn thông qua `redirectRoute(..., navigate: true)` sau khi set `showQuickBuyModal = false` để chắc chắn popup đóng hẳn.
- Override CSS trong `layouts/shoplayout.blade.php` để `.fi-notifications` có `z-index` cao (> modal) phòng trường hợp validation báo lỗi vẫn hiển thị rõ khi popup đang mở.
- Offensive validation UI: khi thiếu dữ liệu quick-buy, component lưu lỗi vào `quickBuyInlineErrors` và render danh sách ngay trong modal (khối đỏ đầu popup) nên không phụ thuộc toast; đồng thời thêm migration `2025_10_07_211353_add_notes_to_orders_table.php` để đảm bảo trường `orders.notes` tồn tại cho việc lưu địa chỉ giao hàng.
- Email notify: danh sách admin lấy từ `users` được trim, ép unique theo lowercase rồi ghép thêm `tranmanhhieu10@gmail.com` trước khi lặp gửi để chắc chắn địa chỉ này luôn nhận được order mới.
- Navbar: banner cảnh báo pending được rút gọn (padding ~0.5rem) và thanh chính giảm padding (px-2.5/py-1) để topnav gọn hơn ~20%.

