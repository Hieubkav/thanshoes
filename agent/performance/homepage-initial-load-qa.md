# QA Hieu Nang Trang Chu (2025-10-10)

## Boi canh
- Nguoi dung phan hoi: trang chu ThanShoes tai cham, dac biet voi luot truy cap dau tien.
- Pham vi kiem tra: render Blade ban dau, cac component Livewire/JS kem theo va truy van backend phuc vu trang `/`.

## Quan sat chinh
- `ProductCacheService::getHomepageProducts()` bi goi lap lai o nhieu view (`store_front`, `navbar` Livewire, category, filter...) va nap toan bo san pham + bien the + anh (#SLO). Khi cache nguoi, luot truy cap dau tien chiu truy van lon + serialize cache (khoang 400KB+ tuy du lieu).
- `component.new_arrival` render nhieu section (toi da 9+) va do inline JSON du lieu bien the vao moi lan include -> phinh kich thuoc HTML, thoi gian parse JS.
- Livewire `Navbar` include hang loat modal (cart drawer, order history, search...) ngay ca khi chua mo, lam tang DOM ban dau va chi phi hydrate.
- Layout them `loading="lazy"` cho moi anh (JS loop) -> anh hero/carousel tren fold cung bi lazy, keo dai LCP.
- Layout goi `Setting::first()` nhieu lan trong `<head>` thay vi dung cache chia se -> query thua moi request.

## Viec can xac nhan them
- Do TTFB & LCP thuc te khi cache nguoi vs cache am (dung `php artisan cache:clear` roi `ab`/`wrk` hoac Lighthouse).
- Uoc luong dung luong HTML tra ve cho trang chu voi 50/100 san pham de xac dinh nguong toi uu.
- Kiem tra backend cache (file, redis) va chien luoc warmup (cron, deploy hook) de tranh user dau tien chiu phat.

## Goi y hanh dong nhanh
1. Them job warmup cache homepage sau deploy de tranh cold-start.
2. Giam du lieu nap ban dau: paginator/gioi han truong, defer modal nang bang Alpine/Livewire lazy render.
3. Bo lazy-load cho hero dau tien + ap dung `preload` anh hero.
4. Refactor layout dung `ProductCacheService::getSettings()` (da cache) thay vi goi model truc tiep.

## Update 2025-10-10
- Bo sung memo hoa cap request cho `ProductCacheService::getHomepageProducts()` de tranh deserialize collection lon nhieu lan khi cache nguoi.
- Dat lai bo nho dem noi bo khi goi `clearCache()` de dam bao duoc refresh dung du lieu.
- Refactor component `new_arrival` de chi emit JSON bien the mot lan/ID, dung hang doi script va bo sung queue flush giam kich thuoc HTML.
- Carousel slide dau tien duoc dat `loading="eager"` + `fetchpriority="high"` de cai thien LCP trong trang chu.
- Layout `shoplayout` su dung `optional($setting)` thay vi query `Setting::first()` lap lai, giam query thua trong `<head>`.
- Goi frontend loai bo Vue/Vuetify/jQuery/Firebase va cac file lien quan khoi bundle, giam thoi gian tai JS.
