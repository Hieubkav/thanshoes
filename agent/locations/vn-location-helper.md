# Du Lieu Tinh/Phuong (VN)

- JSON: `resources/data/provinces.json` (id, name) va `resources/data/wards.json` (id, name, province_id, province_name). Toan bo du lieu giu nguyen dau tieng Viet.
- Helper: `app/Helpers/VnLocation.php` doc JSON mot lan (cache static), ho tro `provinces()`, `wards()`, `wardsOfProvince($provinceId)`, `findProvince($id)`, `findWard($id)` va `addressLabel()` dung de ghep chuoi dia chi.
- Livewire: `ProductOverview` dung helper de render dropdown, tao chuoi dia chi (`detail, ward, province`) luu vao `customers.address` va `orders.notes`.
- Neu can them quan/huyen -> bo sung JSON moi + ham tuong tu, cap nhat view/form.
