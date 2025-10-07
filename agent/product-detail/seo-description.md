# Ghi chú SEO description chi tiết sản phẩm

- Vấn đề: Blade `resources/views/shop/product_overview.blade.php` khai báo hàm `generateProductSeoDescription()` ngay trong view nên khi Laravel biên dịch lại gây lỗi `Cannot redeclare`.
- Cách giải quyết: chuyển logic tạo mô tả SEO sang biến `$productSeoDescription` bên trong khối `@php` (không còn định nghĩa hàm). Nếu sản phẩm có `seo_description` thì dùng trực tiếp, ngược lại ghép tên, thương hiệu, loại và giá.
- Khi chỉnh sửa, nhớ xóa cache view (`php artisan view:clear` hoặc `view:cache`) để đảm bảo Blade đã được build lại với khối code mới.
