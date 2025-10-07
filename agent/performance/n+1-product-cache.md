# N+1 Product Cache

- 2025-07-10: Tao `ProductCacheService::queryWithEagerLoads()` de gom cac eager load thong dung (variants.variantImage, productImages, tags).
- Cap nhat `ProductFilter`, `Navbar`, va cac view blade lon de dung ProductCacheService thay vi `Product::all()` ngam dinh -> tranh N+1 khi doc variantImage.
- Neu them view moi can toan bo danh sach san pham, goi `ProductCacheService::getHomepageProducts()` hoac `queryWithEagerLoads()`.
- Ket hop voi cache da co, nho goi `ProductCacheService::clearCache()` sau khi cap nhat san pham de lam moi du lieu eager loaded.
