# Hệ thống Tracking Visitor - ThanShoes

## Tổng quan
Hệ thống tracking được tích hợp để theo dõi lượt truy cập website và lượt xem sản phẩm, giúp phân tích hành vi người dùng và hiệu quả kinh doanh.

## Tính năng chính

### 1. Tracking Website Visit
- **Theo dõi tổng thể**: Ghi nhận mọi lượt truy cập vào website
- **Phân biệt visitor**: Sử dụng IP address để phân biệt người dùng khác nhau
- **Thống kê theo thời gian**: Hôm nay, tuần này, tháng này, tổng thể
- **Thông tin chi tiết**: IP, User Agent, URL, Referrer, thời gian

### 2. Tracking Product View
- **Theo dõi sản phẩm**: Ghi nhận lượt xem từng sản phẩm cụ thể
- **Top sản phẩm**: Xếp hạng sản phẩm được xem nhiều nhất
- **Thống kê chi tiết**: Unique viewers và total views
- **Phân tích xu hướng**: Theo dõi sản phẩm hot, sản phẩm ít quan tâm

### 3. 🔥 Realtime Features (MỚI)
- **Live Counter**: Hiển thị số visitor đang online (trong 5 phút gần nhất)
- **Auto-refresh**: Tự động cập nhật dữ liệu mỗi 3-7 giây
- **Recent Activity**: Hiển thị hoạt động gần đây trong 10 phút
- **Live Notifications**: Thông báo realtime khi có visitor mới
- **Masked IP**: Ẩn một phần IP để bảo mật privacy

## Cấu trúc Database

### Bảng `website_visits`
```sql
- id: Primary key
- ip_address: Địa chỉ IP (hỗ trợ IPv6)
- user_agent: Thông tin trình duyệt
- page_url: URL trang được truy cập
- referrer: Trang giới thiệu
- visit_date: Ngày truy cập
- unique_visitors_today: Số visitor duy nhất hôm nay
- total_page_views_today: Tổng lượt xem hôm nay
- total_page_views_all_time: Tổng lượt xem từ trước đến nay
- created_at, updated_at: Timestamps
```

### Bảng `product_views`
```sql
- id: Primary key
- product_id: Foreign key đến products
- ip_address: Địa chỉ IP
- user_agent: Thông tin trình duyệt
- referrer: Trang giới thiệu
- view_date: Ngày xem
- unique_viewers_today: Số viewer duy nhất hôm nay
- total_views_today: Tổng lượt xem hôm nay
- total_views_all_time: Tổng lượt xem từ trước đến nay
- created_at, updated_at: Timestamps
```

## Middleware

### TrackWebsiteVisit
- **Vị trí**: `app/Http/Middleware/TrackWebsiteVisit.php`
- **Chức năng**: Tự động ghi nhận mọi lượt truy cập GET request
- **Loại trừ**: Admin routes, Dashboard routes
- **Tích hợp**: Middleware group 'web'

### TrackProductView
- **Vị trí**: `app/Http/Middleware/TrackProductView.php`
- **Chức năng**: Ghi nhận lượt xem sản phẩm cụ thể
- **Áp dụng**: Route `/product/{slug}`
- **Điều kiện**: Chỉ track khi request thành công (200)

## Models

### WebsiteVisit
- **Phương thức chính**:
  - `recordVisit($request)`: Ghi nhận lượt truy cập
  - `getTodayStats()`: Thống kê hôm nay
  - `getAllTimeStats()`: Thống kê tổng thể

### ProductView
- **Phương thức chính**:
  - `recordView($productId, $request)`: Ghi nhận lượt xem sản phẩm
  - `getProductTodayStats($productId)`: Thống kê sản phẩm hôm nay
  - `getProductAllTimeStats($productId)`: Thống kê sản phẩm tổng thể
  - `getTopViewedProducts($limit)`: Top sản phẩm được xem nhiều

## Filament Admin Resources

### WebsiteVisitResource
- **Đường dẫn**: `/dashboard/website-visits`
- **Tính năng**:
  - Xem danh sách lượt truy cập
  - Filter theo thời gian (hôm nay, tuần này, tháng này)
  - Widget thống kê tổng quan
  - Tabs phân loại dữ liệu

### ProductViewResource
- **Đường dẫn**: `/dashboard/product-views`
- **Tính năng**:
  - Xem danh sách lượt xem sản phẩm
  - Filter theo sản phẩm và thời gian
  - Widget thống kê sản phẩm
  - Top 10 sản phẩm được xem nhiều nhất

## Widgets

### WebsiteStatsWidget (Realtime - 5s polling)
- Visitor hôm nay
- Lượt xem hôm nay
- Visitor tuần này
- Lượt xem tuần này
- Visitor tháng này
- Lượt xem tháng này
- Tổng visitor

### ProductStatsWidget (Realtime - 5s polling)
- Viewer sản phẩm hôm nay
- Lượt xem sản phẩm hôm nay
- Viewer sản phẩm tuần này
- Lượt xem sản phẩm tuần này
- Viewer sản phẩm tháng này
- Lượt xem sản phẩm tháng này
- Tổng lượt xem sản phẩm

### TopProductsWidget (Realtime - 10s polling)
- Bảng top 10 sản phẩm được xem nhiều nhất
- Hiển thị tên, thương hiệu, loại, tổng lượt xem

### 🔥 LiveTrackingWidget (Realtime - 3s polling)
- Live counter: Visitor hôm nay, lượt xem trang, lượt xem sản phẩm
- Online visitors: Số người đang online (5 phút gần nhất)
- Cập nhật realtime với animation

### 🔥 RealtimeNotificationsWidget (Realtime - 7s polling)
- Hoạt động website gần đây (10 phút)
- Lượt xem sản phẩm gần đây (10 phút)
- IP được mask để bảo mật
- Có thể ẩn/hiện notifications

## Cách sử dụng

### 1. Truy cập Admin Panel
```
URL: /dashboard
Login: tranmanhhieu10@gmail.com / 12345678
```

### 2. Xem thống kê Website
- Vào menu "Thống kê" > "Lượt truy cập Website"
- Xem các widget thống kê ở đầu trang
- Sử dụng tabs để filter theo thời gian
- Sử dụng filters để tìm kiếm chi tiết

### 3. Xem thống kê Sản phẩm
- Vào menu "Thống kê" > "Lượt xem Sản phẩm"
- Xem widget thống kê và top sản phẩm
- Filter theo sản phẩm cụ thể
- Phân tích xu hướng sản phẩm hot

## Tối ưu hóa

### 1. Performance
- Sử dụng index database cho các trường thường query
- Middleware chỉ chạy với GET requests
- Error handling không làm gián đoạn user experience

### 2. Data Integrity
- Phân biệt unique visitors và total views
- Tránh duplicate tracking trong cùng session
- Lưu trữ thông tin chi tiết để phân tích sau

### 3. Privacy
- Chỉ lưu IP address, không lưu thông tin cá nhân
- Tuân thủ quy định bảo mật dữ liệu
- Có thể mở rộng để tuân thủ GDPR nếu cần

## ✅ Tính năng Realtime đã có

### 1. ✅ Live Tracking
- ✅ Live visitor counter (cập nhật mỗi 3s)
- ✅ Real-time stats widgets (cập nhật mỗi 5s)
- ✅ Online visitor detection (5 phút gần nhất)
- ✅ Recent activity notifications (10 phút gần nhất)

### 2. ✅ Auto-refresh Interface
- ✅ Livewire polling cho tất cả widgets
- ✅ Không cần refresh trang thủ công
- ✅ Tối ưu performance với polling intervals khác nhau
- ✅ Animation và visual indicators

## Mở rộng tương lai

### 1. Analytics nâng cao
- Thống kê theo thiết bị (mobile/desktop)
- Phân tích bounce rate
- Tracking conversion rate
- Heatmap tracking

### 2. WebSocket Integration
- WebSocket cho instant updates (thay vì polling)
- Push notifications cho admin
- Real-time dashboard cho multiple users

### 3. Export & Reports
- Export dữ liệu Excel/PDF
- Scheduled reports qua email
- API endpoints cho third-party tools
- Custom date range reports
