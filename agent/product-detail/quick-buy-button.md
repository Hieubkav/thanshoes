# Nut Mua Ngay Tren Trang Chi Tiet

- Bottom drawer: `resources/views/partials/quick_buy_modal.blade.php` dung overlay `overflow-y-auto`, container `flex flex-col` (max-h ~94vh). Header co badge tom tat (mau/size, so luong, don gia) + preview anh bien the.
- Form lien he: input ho ten/so dien thoai/email + dropdown tinh (`quickBuyProvince`) va phuong (`quickBuyWard`) du lieu tu `resources/data/provinces.json` va `resources/data/wards.json`; textarea `quickBuyAddressDetail` ghi chi tiet.
- Sticky action bar o duoi hien tong thanh toan va nut `Xac nhan mua ngay`; phan chon thanh toan (COD/Chuyen khoan) dung button `wire:click` de doi `quickBuyPaymentMethod`.
- Helper `app/Helpers/VnLocation.php` nap JSON, cung cap `provinces()`, `wardsOfProvince()`, `findProvince()`, `findWard()` va `addressLabel()` dung trong `ProductOverview`.
- Logic Livewire o `app/Livewire/ProductOverview.php` (`prefillQuickBuyCustomer`, `updatedQuickBuyProvince`, `validateQuickBuyInfo`, `createQuickBuyOrder`...) ghep dia chi thanh chuoi va luu vao `customers.address` + `orders.notes`, reset state sau khi dat hang.
- Muon them truong moi (ghi chu, voucher,...) -> khai bao property Livewire, bind vao drawer, cap nhat `validateQuickBuyInfo()` va `createQuickBuyOrder()`.
