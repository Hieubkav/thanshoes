# Hướng dẫn Hệ thống Authentication ThanShoes (Customer)

## Tổng quan
Hệ thống authentication đã được tích hợp vào ThanShoes sử dụng **Customer model** với các tính năng:
- Đăng ký tài khoản khách hàng mới (email và/hoặc số điện thoại)
- Đăng nhập/Đăng xuất khách hàng (bằng email hoặc số điện thoại)
- Quản lý thông tin khách hàng (tên, email, phone, địa chỉ)
- Tích hợp với hệ thống giỏ hàng và đơn hàng hiện có
- Giao diện hiện đại tích hợp với layout hiện tại
- Hỗ trợ đăng nhập bằng cả email và số điện thoại

## Các trang Authentication

### 1. Trang Đăng nhập
- **URL**: `/login`
- **Route name**: `login`
- **View**: `resources/views/auth/login.blade.php`
- **Component**: `app/Livewire/Auth/Login.php`

### 2. Trang Đăng ký
- **URL**: `/register`
- **Route name**: `register`
- **View**: `resources/views/auth/register.blade.php`
- **Component**: `app/Livewire/Auth/Register.php`

### 3. Đăng xuất
- **URL**: `/logout` (POST)
- **Route name**: `logout`
- **Controller**: `app/Http/Controllers/AuthController.php`

## Phân quyền

### User Roles
- **user**: Người dùng thường (mặc định)
- **admin**: Quản trị viên

### Middleware
- **auth**: Yêu cầu đăng nhập
- **guest**: Chỉ cho phép khách (chưa đăng nhập)
- **admin**: Yêu cầu quyền admin

## Tài khoản khách hàng mặc định

### Customer Test (Email + Phone)
- **Email**: customer@thanshoes.vn
- **Phone**: 0987654321
- **Address**: 123 Đường ABC, Quận 1, TP.HCM
- **Password**: customer123456

### Customer Phone Only
- **Phone**: +84-901-234-567
- **Address**: 456 Đường XYZ, Quận 2, TP.HCM
- **Password**: phone123456

### Customer Email Only
- **Email**: email.only@thanshoes.vn
- **Address**: 789 Đường DEF, Quận 3, TP.HCM
- **Password**: email123456

## Cách sử dụng

### 1. Chạy Migration và Seeder
```bash
php artisan migrate
php artisan db:seed --class=CustomerSeeder
```

### 2. Truy cập các trang
- Trang chủ: `/`
- Đăng nhập: `/login`
- Đăng ký: `/register`

### 3. Kiểm tra authentication trong Blade
```blade
@auth
    <!-- Nội dung cho user đã đăng nhập -->
    <p>Xin chào {{ Auth::user()->name }}!</p>
@endauth

@guest
    <!-- Nội dung cho khách -->
    <a href="{{ route('login') }}">Đăng nhập</a>
@endguest
```

### 4. Kiểm tra role trong Controller
```php
if (auth()->user()->isAdmin()) {
    // Logic cho admin
}

if (auth()->user()->isUser()) {
    // Logic cho user thường
}
```

## Routes được bảo vệ

### Yêu cầu đăng nhập (auth middleware)
- `/excel`
- `/form_import_excel`
- `/import_excel`
- `/tq`
- `/nhap_hang`
- `/form-nhap-hang`
- `/nhap-hang`
- `/g-repordownload-nhap-hant`
- `/admin/download_nhap_hang_sapo`
- `/admin/products/{product}/images/organize`
- `/admin/products/{product}/images/update-order`
- `/admin/products/{product}/images/reset-order`

### Chỉ cho khách (guest middleware)
- `/login`
- `/register`

## Tính năng UI

### Navbar
- **Đã đăng nhập**: Hiển thị tên và email user
  - Thông tin cá nhân (hiển thị thông tin từ User model)
  - Đơn hàng của tôi (hiển thị đơn hàng liên kết với user)
  - Đăng xuất
- **Chưa đăng nhập**: Menu khách
  - Đăng nhập
  - Đăng ký
  - Thông tin cá nhân (hiển thị thông tin từ session customer)
  - Lịch sử đơn hàng (yêu cầu đăng nhập)

### Flash Messages
- Thông báo thành công/lỗi tự động hiển thị và ẩn
- Animation mượt mà
- Tự động ẩn sau 5 giây

### Cart Integration
- **Đăng nhập**: Cart được merge từ session sang user cart
- **Đăng ký**: Cart session được chuyển thành user cart
- **Đăng xuất**: Cart được giữ theo session mới

## Customization

### Thay đổi redirect sau đăng nhập
Chỉnh sửa trong `app/Providers/RouteServiceProvider.php`:
```php
public const HOME = '/'; // Thay đổi URL redirect
```

### Thêm field vào form đăng ký
1. Thêm field vào `resources/views/livewire/auth/register.blade.php`
2. Thêm property và validation vào `app/Livewire/Auth/Register.php`
3. Thêm field vào `$fillable` trong `app/Models/User.php`

## Đăng nhập bằng Email hoặc Phone

### Validation Rules
- **Email**: Tùy chọn nếu có phone, phải unique, format email hợp lệ
- **Phone**: Tùy chọn nếu có email, phải unique, không có validation format
- **Required**: Ít nhất một trong hai (email hoặc phone) phải có
- **Flexible**: Phone có thể là bất kỳ format nào (+84, 0, dấu gạch ngang, v.v.)

### Login Logic
- Hệ thống tự động phát hiện input là email hay phone
- Sử dụng `filter_var($value, FILTER_VALIDATE_EMAIL)` để phân biệt
- Đăng nhập bằng field tương ứng

### Tùy chỉnh middleware admin
Chỉnh sửa logic trong `app/Http/Middleware/AdminMiddleware.php`

## Testing
Chạy test để đảm bảo hệ thống hoạt động đúng:
```bash
php artisan test --filter AuthenticationTest
```

## Troubleshooting

### Lỗi "Route [login] not defined"
- Đảm bảo đã thêm routes authentication vào `routes/web.php`
- Chạy `php artisan route:clear`

### Lỗi "Class 'App\Livewire\Auth\Login' not found"
- Chạy `composer dump-autoload`
- Đảm bảo namespace đúng trong các file Livewire

### Lỗi database
- Kiểm tra kết nối database trong `.env`
- Chạy `php artisan migrate`

## Bảo mật

### Password
- Minimum 8 ký tự
- Tự động hash bằng bcrypt
- Validation confirmation

### Session
- Tự động regenerate sau đăng nhập
- Invalidate khi đăng xuất
- CSRF protection

### Middleware
- Guest middleware ngăn user đã đăng nhập truy cập login/register
- Auth middleware bảo vệ routes cần authentication
- Admin middleware kiểm tra quyền admin
