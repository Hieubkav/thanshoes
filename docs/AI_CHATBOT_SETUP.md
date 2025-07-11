# Hướng dẫn cài đặt AI Chatbot cho ThanShoes

## Tổng quan
Chatbot AI được tích hợp vào ThanShoes sử dụng Google Gemini API, hiển thị dưới dạng nút chat trong menu speedial (góc phải dưới). Chatbot được xây dựng bằng Livewire 3.5 cho tương tác realtime.

## Các file đã tạo/chỉnh sửa

### 1. Migration
- `database/migrations/2025_01_11_000000_create_chat_histories_table.php`

### 2. Model
- `app/Models/ChatHistory.php`

### 3. Controller
- `app/Http/Controllers/AiChatController.php`

### 4. Livewire Component
- `app/Livewire/AiChatbot.php`
- `resources/views/livewire/ai-chatbot.blade.php`

### 5. Cấu hình
- `.env` - Thêm `GEMINI_API_KEY`
- `config/services.php` - Thêm cấu hình Gemini
- `routes/web.php` - Thêm route AI chat
- `app/Providers/AppServiceProvider.php` - Đăng ký Livewire component

### 6. View
- `resources/views/component/shop/speedial.blade.php` - Tích hợp chatbot

## Cài đặt

### Bước 1: Chạy Migration
```bash
php artisan migrate
```

### Bước 2: Cấu hình API Key
1. Truy cập [Google AI Studio](https://aistudio.google.com/)
2. Tạo API key mới
3. Cập nhật file `.env`:
```env
GEMINI_API_KEY=your_actual_api_key_here
```

### Bước 2.1: Test API Connection
Sau khi cấu hình API key, test kết nối:
```bash
# Truy cập URL này trong browser hoặc dùng curl
curl http://your-domain.com/ai-chat/test
```

Hoặc truy cập trực tiếp: `http://127.0.0.1:8000/ai-chat/test`

### Bước 3: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## Tính năng

### 1. Giao diện
- Nút chat màu cam gradient với hiệu ứng hover
- Cửa sổ chat 380x384px với thiết kế hiện đại
- Tin nhắn user (màu cam) và AI (màu trắng) phân biệt rõ ràng
- Loading indicator với animation dots
- Auto-scroll đến tin nhắn mới nhất

### 2. Chức năng
- Chat realtime không cần reload trang
- Lưu lịch sử hội thoại trong session
- Rate limiting: 10 requests/phút
- Xử lý lỗi API và hiển thị thông báo thân thiện
- Nút xóa cuộc trò chuyện
- Responsive trên mobile

### 3. AI Features
- System prompt được tối ưu cho ThanShoes
- Tư vấn sản phẩm giày dép
- Hỗ trợ chính sách đổi trả
- Hướng dẫn mua sắm
- Giới hạn lịch sử hội thoại (10 tin nhắn gần nhất)

## Bảo mật

### 1. Rate Limiting
- 10 requests/phút cho mỗi IP
- Middleware throttle tích hợp

### 2. Validation
- Tin nhắn tối đa 1000 ký tự
- Lịch sử hội thoại tối đa 20 tin nhắn

### 3. Error Handling
- Log lỗi API chi tiết
- Thông báo lỗi thân thiện cho user
- Timeout 30 giây cho API calls

## Tùy chỉnh

### 1. Thay đổi System Prompt
Chỉnh sửa method `getSystemPrompt()` trong `AiChatController.php`

### 2. Thay đổi Rate Limit
Chỉnh sửa middleware trong `routes/web.php`:
```php
->middleware('throttle:20,1'); // 20 requests per minute
```

### 3. Thay đổi giao diện
Chỉnh sửa `resources/views/livewire/ai-chatbot.blade.php`

### 4. Thay đổi vị trí nút chat
Chỉnh sửa CSS classes trong view Livewire

## Troubleshooting

### 1. Lỗi API Key
- Kiểm tra API key trong `.env`
- Đảm bảo API key có quyền truy cập Gemini API
- Chạy `php artisan config:clear`

### 2. Lỗi Rate Limit
- Giảm tần suất gửi tin nhắn
- Tăng limit trong route nếu cần

### 3. Lỗi JavaScript
- Kiểm tra console browser
- Đảm bảo Livewire scripts được load

### 4. Lỗi Database
- Chạy migration: `php artisan migrate`
- Kiểm tra kết nối database

## Performance

### 1. Caching
- Có thể implement cache cho system prompt
- Cache conversation history nếu cần

### 2. Database Optimization
- Index đã được tạo cho `session_id` và `created_at`
- Có thể thêm cleanup job cho old chat histories

### 3. API Optimization
- Timeout 30 giây
- Giới hạn conversation history
- Compression cho large responses

## Monitoring

### 1. Logs
- API errors được log trong `storage/logs/laravel.log`
- Monitor rate limit hits

### 2. Database
- Monitor chat_histories table size
- Implement cleanup if needed

### 3. API Usage
- Monitor Gemini API quota
- Track response times

## Mở rộng

### 1. Lưu lịch sử lâu dài
- Uncomment code lưu database trong controller
- Implement user authentication

### 2. Filament Admin
- Tạo Resource để quản lý chat histories
- Analytics dashboard

### 3. Advanced Features
- File upload support
- Voice messages
- Multi-language support
- Custom training data
