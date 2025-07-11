# 🚀 Hướng dẫn nhanh cài đặt AI Chatbot

## ✅ Đã hoàn thành:
- ✅ Migration đã chạy thành công
- ✅ Cache đã được clear
- ✅ Tất cả file đã được tạo

## 🔧 Cần làm ngay:

### 1. Cấu hình API Key
Mở file `.env` và thay thế:
```env
GEMINI_API_KEY=your_gemini_api_key_here
```

**Lấy API key từ đâu?**
1. Truy cập: https://aistudio.google.com/
2. Đăng nhập Google account
3. Click "Get API key" 
4. Tạo key mới
5. Copy và paste vào `.env`

### 2. Test API Connection
Sau khi cấu hình API key, test ngay:
```
http://127.0.0.1:8000/ai-chat/test
```

**Kết quả mong đợi:**
```json
{
  "status": "success",
  "http_status": 200,
  "response": {...},
  "api_key_prefix": "AIzaSyBm..."
}
```

### 3. Kiểm tra giao diện
1. Mở website: `http://127.0.0.1:8000`
2. Tìm nút chat màu cam ở góc phải dưới (phía trên các nút khác)
3. Click để mở chat window

## 🐛 Troubleshooting

### Lỗi "Kết nối không ổn định":
1. **Kiểm tra API key**: Truy cập `/ai-chat/test`
2. **Kiểm tra logs**: `storage/logs/laravel.log`
3. **Kiểm tra .env**: Đảm bảo không có space thừa

### Lỗi giao diện bị đè:
- ✅ Đã sửa: Chatbot giờ ở `bottom-80` thay vì `bottom-20`
- ✅ Z-index đã tăng lên `z-[9999]`

### API Key không hoạt động:
1. Đảm bảo API key đúng format: `AIzaSy...`
2. Kiểm tra quota tại Google AI Studio
3. Đảm bảo API được enable

## 📱 Tính năng đã có:

### Giao diện:
- ✅ Nút chat màu cam gradient với animation
- ✅ Chat window responsive 380x384px
- ✅ Auto-scroll tin nhắn
- ✅ Loading animation với dots
- ✅ Phân biệt tin nhắn user/AI

### Chức năng:
- ✅ Realtime chat với Livewire
- ✅ Rate limiting 10 requests/phút
- ✅ Error handling thông minh
- ✅ System prompt tối ưu cho ThanShoes
- ✅ Lưu lịch sử trong session

### Bảo mật:
- ✅ Input validation
- ✅ API timeout 30s
- ✅ Safety filters
- ✅ Rate limiting

## 🎯 Test checklist:

- [ ] API key đã cấu hình
- [ ] `/ai-chat/test` trả về success
- [ ] Nút chat hiển thị đúng vị trí
- [ ] Chat window mở được
- [ ] Gửi tin nhắn test thành công
- [ ] AI trả lời đúng về ThanShoes

## 📞 Nếu vẫn lỗi:

1. **Check logs**: `tail -f storage/logs/laravel.log`
2. **Browser console**: F12 → Console tab
3. **Network tab**: Kiểm tra API calls
4. **Test endpoint**: `/ai-chat/test`

---

**Lưu ý**: Chatbot sẽ xuất hiện ở góc phải dưới, phía trên các nút speedial khác (Messenger, Zalo, Phone). Nút có màu cam với icon chat bubble và hiệu ứng pulse.
