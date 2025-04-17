<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variant;
use App\Models\VariantImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminController extends Controller
{
    public function form_import_excel()
    {
        return view('shop.form_import_excel');
    }

    public function import_excel(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);

        $file = $request->file('file');
        $file->move(public_path('uploads'), 'data_shoes.xlsx');

        // Sau khi lưu file, gọi ngay hàm xử lý Excel
        $filePath = public_path('uploads/data_shoes.xlsx');

        try {
            // Load file excel
            $spreadsheet = IOFactory::load($filePath);

            // Lấy ra dữ liệu của sheet đầu tiên
            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Mảng lưu trữ SKU hiện tại để kiểm tra sau
            $current_skus = [];
            $processed_count = 0;
            $product_count = 0;

            // Biến để theo dõi sản phẩm hiện tại
            $currentProduct = null;
            $currentProductName = null;
            $currentProductSku = null;

            // Bắt đầu từ dòng thứ 2 (index 2) vì dòng 1 là header
            foreach ($data as $key => $value) {
                if ($key >= 2) {
                    $hasSku = !empty($value['N']);

                    // Nếu có tên sản phẩm mới, tạo hoặc cập nhật sản phẩm mới
                    if (!empty($value['A'])) {
                        $currentProductName = $value['A']; // Lưu tên sản phẩm hiện tại
                        $currentProductSku = null; // Reset SKU sản phẩm

                        // Nếu dòng này cũng có SKU, lấy SKU gốc (phần trước dấu -)
                        if ($hasSku) {
                            $skuParts = explode('-', $value['N']);
                            $currentProductSku = $skuParts[0];
                        }

                        // Tìm sản phẩm dựa vào SKU pattern
                        $product = null;
                        if ($currentProductSku) {
                            // Tìm sản phẩm dựa vào SKU gốc
                            $product = Product::where('sku', $currentProductSku)->first();
                        }

                        if (!$product) {
                            $product = new Product();
                            $product_count++;
                        }

                        $product->name = $currentProductName;
                        $product->description = $value['D'] ?? null;
                        $product->brand = $value['E'] ?? null;
                        $product->type = $value['C'] ?? null;
                        $product->sku = $currentProductSku; // Lưu SKU cho sản phẩm
                        $product->save();

                        // Xử lý tags nếu có trong cột F
                        if (!empty($value['F'])) {
                            $tagsString = $value['F'];
                            $tagNames = array_map('trim', explode(',', $tagsString));
                            
                            $tagIds = [];
                            foreach ($tagNames as $tagName) {
                                if (!empty($tagName)) {
                                    // Tìm tag hiện có hoặc tạo mới mà không thay đổi hình ảnh của tag đã tồn tại
                                    $tag = \App\Models\Tag::where('name', $tagName)->first();
                                    
                                    if (!$tag) {
                                        // Nếu tag không tồn tại, tạo mới
                                        $tag = new \App\Models\Tag();
                                        $tag->name = $tagName;
                                        $tag->save();
                                    }
                                    // Nếu tag đã tồn tại, không thay đổi gì cả, đặc biệt là trường image
                                    
                                    $tagIds[] = $tag->id;
                                }
                            }
                            
                            // Đồng bộ tags với sản phẩm (remove tags cũ, thêm tags mới)
                            $product->tags()->sync($tagIds);
                        }

                        $currentProduct = $product;
                    }
                    // Nếu không có tên sản phẩm nhưng có SKU và chưa có SKU sản phẩm
                    else if ($hasSku && !$currentProductSku && $currentProduct) {
                        $skuParts = explode('-', $value['N']);
                        $currentProductSku = $skuParts[0];
                    }

                    // Bỏ qua nếu không có SKU hoặc không có sản phẩm hiện tại
                    if (!$hasSku || !$currentProduct) {
                        continue;
                    }

                    $sku = $value['N'];
                    $current_skus[] = $sku;

                    // Xử lý biến thể
                    $variant = Variant::where('sku', $sku)->first();
                    if (!$variant) {
                        $variant = new Variant();
                        $variant->product_id = $currentProduct->id;
                        $variant->sku = $sku;
                    }

                    // Cập nhật thông tin biến thể
                    $variant->color = $value['J'] ?? ''; // Giá trị thuộc tính 2 (màu sắc)
                    $variant->size = $value['H'] ?? ''; // Giá trị thuộc tính 1 (kích thước)

                    // Xử lý giá, đảm bảo giá trị hợp lệ
                    $price = $value['AF'] ?? 0; // PL_Giá bán lẻ
                    if (is_string($price)) {
                        $price = str_replace([',', '.'], '', $price);
                    }
                    $variant->price = (int)$price;

                    // Xử lý số lượng tồn kho
                    $stock = $value['AA'] ?? 0; // LC_CN1_Tồn kho ban đầu
                    if (is_string($stock)) {
                        $stock = str_replace([',', '.'], '', $stock);
                    }
                    $variant->stock = (int)$stock;

                    $variant->save();

                    // Thêm ảnh mới cho variant nếu có
                    if (!empty($value['R'])) { // Ảnh đại diện
                        $imageUrl = $value['R'];

                        // Kiểm tra xem đã có ảnh cho variant này chưa
                        $existing_image = VariantImage::where('variant_id', $variant->id)
                            ->where('image', $imageUrl)
                            ->first();

                        $variantImage = $existing_image;
                        if (!$existing_image) {
                            $variantImage = new VariantImage();
                            $variantImage->variant_id = $variant->id;
                            $variantImage->image = $imageUrl;
                            $variantImage->save();
                        }

                        // Thêm hoặc cập nhật ProductImage liên quan
                        $existing_product_image = ProductImage::where('product_id', $currentProduct->id)
                            ->where('image', $imageUrl)
                            ->where('type', 'variant')
                            ->first();

                        if (!$existing_product_image) {
                            // Lấy order cao nhất hiện tại
                            $maxOrder = ProductImage::where('product_id', $currentProduct->id)->max('order') ?? 0;

                            $productImage = new ProductImage();
                            $productImage->product_id = $currentProduct->id;
                            $productImage->image = $imageUrl;
                            $productImage->type = 'variant';
                            $productImage->variant_image_id = $variantImage->id;
                            $productImage->source = $imageUrl;
                            $productImage->order = $maxOrder + 1;
                            $productImage->save();
                        }
                    }

                    $processed_count++;
                }
            }

            // Đếm số sản phẩm đã xử lý (dựa trên currentProductSku)
            $uniqueProducts = Product::whereHas('variants', function ($query) use ($current_skus) {
                $query->whereIn('sku', $current_skus);
            })->count();
            // Cập nhật số lượng về 0 cho các variant không có trong file Excel
            if (!empty($current_skus)) {
                $updated_variants = Variant::whereNotIn('sku', $current_skus)->update(['stock' => 0]);
                return "Nhập thành công! Đã xử lý $uniqueProducts sản phẩm với $processed_count biến thể." .
                    "\nĐã cập nhật $updated_variants variant không có trong file về số lượng 0.";
            }

            return "Nhập thành công! Đã xử lý $uniqueProducts sản phẩm với $processed_count biến thể.";
        } catch (\Exception $e) {
            return "Lỗi khi xử lý file Excel: " . $e->getMessage() . " tại dòng " . $e->getLine();
        }
    }

    /**
     * Hiển thị form nhập hàng
     */
    public function form_nhap_hang()
    {
        // Đảm bảo trả về đúng view với layout
        return view('shop.form_nhap_hang')->with([
            'title' => 'Nhập hàng trung quốc',
            'header' => 'Nhập hàng trung quốc'
        ]);
    }

    /**
     * Xử lý nhập hàng từ file Excel
     * Logic nghiệp vụ:
     * 1. File báo cáo sản phẩm sắp hết (cannhap.xls)
     * 2. Xử lý logic tối ưu số lượng
     * 3. Tạo file kết quả cho việc nhập hàng
     *
     * @param  Request  $request chứa:
     *    - excel_products: File Excel danh sách sản phẩm (-> data_shoes.xlsx)
     *    - excel_low_stock: File Excel báo cáo sản phẩm sắp hết (-> cannhap.xls)
     *    - exchange_rate: Tỉ giá tiền tệ (bắt buộc, kiểu số)
     * @return \Illuminate\Http\Response download file chính và lưu file báo cáo
     */
    public function nhap_hang(Request $request)
    {
        // Tăng thời gian thực thi lên 240 giây
        set_time_limit(300);

        // Validate đầu vào - sử dụng file thay vì mimes để tránh lỗi MIME type
        $request->validate([
            'excel_products' => 'required|file',  // Chỉ kiểm tra có phải file không
            'excel_low_stock' => 'required|file', // Chỉ kiểm tra có phải file không
            'exchange_rate' => 'required|numeric', // Tỉ giá phải là số và bắt buộc
        ]);

        try {
            // Kiểm tra phần mở rộng theo cách thủ công
            $allowedExtensions = ['xlsx', 'xls'];
            $productsExtension = strtolower($request->file('excel_products')->getClientOriginalExtension());
            $lowStockExtension = strtolower($request->file('excel_low_stock')->getClientOriginalExtension());
            
            if (!in_array($productsExtension, $allowedExtensions)) {
                return back()->with('error', 'File danh sách sản phẩm phải có định dạng .xlsx hoặc .xls');
            }
            
            if (!in_array($lowStockExtension, $allowedExtensions)) {
                return back()->with('error', 'File báo cáo sản phẩm sắp hết phải có định dạng .xlsx hoặc .xls');
            }

            // Đảm bảo thư mục uploads tồn tại và lưu file với tên cố định
            $this->ensureDirectoryExists('uploads');
            $this->saveUploadedFiles($request);

            // Đường dẫn cố định cho các file
            $productsFilePath = public_path('uploads/data_shoes.xlsx');
            $lowStockFilePath = public_path('uploads/cannhap.xls');

            // Đọc dữ liệu từ file
            $productsData = $this->readExcelFile($productsFilePath);
            $lowStockData = $this->readExcelFile($lowStockFilePath);
            
            // Xử lý dữ liệu từ file báo cáo sản phẩm sắp hết
            $processedData = $this->processLowStockData($lowStockData);
            $groupedProducts = $processedData['valid']; // Sản phẩm hợp lệ
            $excludedProducts = $processedData['excluded']; // Sản phẩm bị loại
            
            // Thu thập thông tin hình ảnh và tối ưu số lượng cho cả sản phẩm hợp lệ và bị loại
            $groupedProducts = $this->optimizeProductsData($groupedProducts, $productsData);
            $excludedProducts = $this->collectProductDetailsForExcluded($excludedProducts, $productsData);
            
            // Tạo file Excel kết quả và file báo cáo
            $reportData = [];
            $logData = []; // Dữ liệu cho sheet log
            
            $outputPath = $this->generateResultFile($groupedProducts, $reportData);
            
            // Tạo file báo cáo chi tiết với cả sheet chính và sheet log
            $this->generateDetailedReport($reportData, $excludedProducts);

            // Tạo file nhập hàng cho Sapo
            $this->generateSapoFile($reportData);

            // Thay vì trả về file để download, trả về với thông báo thành công
            $reportFilename = 'nhap_hang_trung_quoc.xlsx'; // Tên file báo cáo
            return back()->with([
                'success' => 'Xử lý thành công! File báo cáo đã được tạo.', 
                'report_filename' => $reportFilename
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xử lý: ' . $e->getMessage());
        }
    }

    /**
     * Trả về file báo cáo nhập hàng để tải xuống
     * 
     * @return \Illuminate\Http\Response
     */
    public function download_nhap_hang_report()
    {
        $reportPath = public_path('uploads/nhap_hang_trung_quoc.xlsx');
        
        if (file_exists($reportPath)) {
            return response()->download($reportPath, 'Báo cáo nhập hàng trung quốc.xlsx');
        } else {
            return back()->with('error', 'Không tìm thấy file báo cáo. Vui lòng tạo báo cáo trước.');
        }
    }

    /**
     * Trả về file nhập hàng Sapo để tải xuống
     * 
     * @return \Illuminate\Http\Response
     */
    public function download_nhap_hang_sapo()
    {
        $reportPath = public_path('uploads/nhap_hang_sapo.xlsx');
        
        if (file_exists($reportPath)) {
            return response()->download($reportPath, 'Nhập hàng Sapo.xlsx');
        } else {
            return back()->with('error', 'Không tìm thấy file nhập hàng Sapo. Vui lòng tạo báo cáo trước.');
        }
    }

    /**
     * Đảm bảo thư mục tồn tại, tạo nếu chưa có
     *
     * @param string $dirName Tên thư mục tương đối từ public_path
     * @return void
     * @throws \Exception
     */
    private function ensureDirectoryExists($dirName)
    {
        $directory = public_path($dirName);
        if (!file_exists($directory)) {
            if (!mkdir($directory, 0777, true)) {
                throw new \Exception("Không thể tạo thư mục: " . $directory);
            }
        }
    }

    /**
     * Lưu các file được upload với tên cố định
     *
     * @param Request $request
     * @return void
     */
    private function saveUploadedFiles(Request $request)
    {
        $request->file('excel_products')->move(public_path('uploads'), 'data_shoes.xlsx');
        $request->file('excel_low_stock')->move(public_path('uploads'), 'cannhap.xls');
    }

    /**
     * Đọc nội dung từ file Excel
     *
     * @param string $filePath Đường dẫn đến file Excel
     * @return array Dữ liệu từ file Excel dưới dạng mảng
     */
    private function readExcelFile($filePath)
    {
        return IOFactory::load($filePath)
            ->getActiveSheet()
            ->toArray(null, true, true, true);
    }

    /**
     * Xử lý dữ liệu từ file báo cáo sản phẩm sắp hết
     *
     * @param array $lowStockData Dữ liệu từ file báo cáo
     * @return array Sản phẩm được nhóm theo SKU gốc và thông tin sản phẩm bị loại
     */
    private function processLowStockData($lowStockData)
    {
        $groupedProducts = []; // Lưu thông tin số lượng theo SKU gốc
        $excludedProducts = []; // Lưu sản phẩm bị loại ra
        $updatedData = $lowStockData; // Tạo bản sao để lưu lại sau khi xử lý

        // Phân tích dữ liệu từ dòng 6 trở đi (sau header)
        for ($row = 6; isset($lowStockData[$row]); $row++) {
            $sku = $lowStockData[$row]['B'] ?? '';
            if (empty($sku)) continue;

            // Xử lý cột I - Cần xử lý nhiều định dạng số (ví dụ: "1,000" hoặc "1.0")
            $needToOrderRaw = $lowStockData[$row]['I'] ?? null;
            
            // Tách SKU để xác định size
            $skuParts = explode('-', $sku);
            $baseSku = $skuParts[0];  // SKU gốc của sản phẩm
            $size = $skuParts[1] ?? ''; // Size của phiên bản
            
            // Nếu cột I trống, tính theo công thức
            if (empty($needToOrderRaw) || !is_numeric(str_replace([',', '.'], '', $needToOrderRaw))) {
                $needG = (int)($lowStockData[$row]['G'] ?? 0);
                $needH = (int)($lowStockData[$row]['H'] ?? 0);
                
                // Đối với size 36, chỉ lấy đúng G-H (cần bao nhiêu thì nhập bấy nhiêu)
                if ($size === '36') {
                    $needToOrder = $needG - $needH;
                } else {
                    // Các size khác áp dụng công thức cũ G-H+1
                    $needToOrder = $needG - $needH + 1;
                }
                
                // Log ra để debug
                \Illuminate\Support\Facades\Log::info("Dòng $row: Size $size, Cột I trống, tính từ công thức: $needG - $needH" . ($size !== '36' ? " + 1" : "") . " = $needToOrder");
            } else {
                // Xử lý định dạng số có thể chứa dấu phẩy/chấm nghìn
                if (is_string($needToOrderRaw)) {
                    $needToOrderRaw = str_replace(',', '.', $needToOrderRaw);
                }
                $needToOrder = (int)$needToOrderRaw;
                
                // Log ra để debug
                \Illuminate\Support\Facades\Log::info("Dòng $row: Size $size, Lấy từ cột I: $needToOrderRaw => $needToOrder");
            }

            // Ghi lại thông tin nếu không cần đặt thêm
            if ($needToOrder <= 0) {
                // Thu thập thông tin sản phẩm bị loại
                if (!isset($excludedProducts[$baseSku])) {
                    $excludedProducts[$baseSku] = [
                        'sizes' => [],
                        'reasons' => [],
                        'original_data' => [],
                    ];
                }
                
                // Thêm thông tin chi tiết
                $excludedProducts[$baseSku]['sizes'][$size] = 0;
                $excludedProducts[$baseSku]['reasons'][$size] = "Không cần nhập thêm";
                $excludedProducts[$baseSku]['original_data'][$size] = [
                    'need' => (int)($lowStockData[$row]['G'] ?? 0),
                    'coming' => (int)($lowStockData[$row]['H'] ?? 0),
                    'cần_nhập' => $needToOrder,
                    'row' => $row,
                    'raw_value' => $needToOrderRaw
                ];
                
                continue; // Bỏ qua nếu không cần đặt thêm
            }

            // Nhóm thông tin theo SKU gốc
            if (!isset($groupedProducts[$baseSku])) {
                $groupedProducts[$baseSku] = [
                    'sizes' => [],
                    'total_need' => 0,
                    'images' => [],
                    'version_count' => 0,
                ];
            }

            $groupedProducts[$baseSku]['sizes'][$size] = $needToOrder;
            $groupedProducts[$baseSku]['total_need'] += $needToOrder;
            $groupedProducts[$baseSku]['version_count']++; // Đếm số phiên bản của sản phẩm
            
            // Lưu thông tin chi tiết (để tham chiếu sau này nếu cần)
            $groupedProducts[$baseSku]['original_data'][$size] = [
                'need' => (int)($lowStockData[$row]['G'] ?? 0),
                'coming' => (int)($lowStockData[$row]['H'] ?? 0),
                'cần_nhập' => $needToOrder,
                'row' => $row,
                'raw_value' => $needToOrderRaw
            ];
        }

        // Lọc ra những sản phẩm có tổng số lượng nhỏ hơn 6
        foreach ($groupedProducts as $baseSku => $productInfo) {
            // Thay vì kiểm tra số phiên bản, kiểm tra tổng số lượng cần đặt
            if ($productInfo['total_need'] < 6) {
                // Thêm vào danh sách sản phẩm bị loại
                if (!isset($excludedProducts[$baseSku])) {
                    $excludedProducts[$baseSku] = [
                        'sizes' => $productInfo['sizes'],
                        'reasons' => [],
                        'original_data' => $productInfo['original_data'] ?? [],
                        'total_need' => $productInfo['total_need'],
                        'version_count' => $productInfo['version_count']
                    ];
                } else {
                    $excludedProducts[$baseSku]['sizes'] = array_merge(
                        $excludedProducts[$baseSku]['sizes'] ?? [], 
                        $productInfo['sizes']
                    );
                    if (isset($productInfo['original_data'])) {
                        $excludedProducts[$baseSku]['original_data'] = array_merge(
                            $excludedProducts[$baseSku]['original_data'] ?? [],
                            $productInfo['original_data']
                        );
                    }
                    $excludedProducts[$baseSku]['total_need'] = ($excludedProducts[$baseSku]['total_need'] ?? 0) + $productInfo['total_need'];
                    $excludedProducts[$baseSku]['version_count'] = ($excludedProducts[$baseSku]['version_count'] ?? 0) + $productInfo['version_count'];
                }
                
                // Thêm lý do loại trừ chung cho sản phẩm này
                foreach ($productInfo['sizes'] as $size => $amount) {
                    $excludedProducts[$baseSku]['reasons'][$size] = "Tổng số lượng cần nhập chỉ có {$productInfo['total_need']} (cần tối thiểu 6)";
                }
                
                unset($groupedProducts[$baseSku]);
            }
        }

        // Cập nhật file cannhap.xls với các giá trị đã tính toán
        $this->updateLowStockFile($lowStockData, $groupedProducts, $excludedProducts);

        // Trả về cả sản phẩm hợp lệ và không hợp lệ
        return [
            'valid' => $groupedProducts,
            'excluded' => $excludedProducts
        ];
    }

    /**
     * Thu thập thông tin chi tiết cho sản phẩm bị loại
     *
     * @param array $excludedProducts Sản phẩm đã bị loại
     * @param array $productsData Dữ liệu từ file danh sách sản phẩm
     * @return array Sản phẩm đã được thu thập thêm thông tin
     */
    private function collectProductDetailsForExcluded($excludedProducts, $productsData)
    {
        foreach ($excludedProducts as $baseSku => &$productInfo) {
            // Thu thập hình ảnh cho sản phẩm từ file danh sách
            if (!isset($productInfo['images'])) {
                $productInfo['images'] = [];
            }
            $this->collectProductImages($baseSku, $productInfo, $productsData);
            
            // Thu thập tên sản phẩm từ file danh sách
            if (!isset($productInfo['name'])) {
                $productInfo['name'] = $baseSku;
                $this->collectProductName($baseSku, $productInfo, $productsData);
            }
        }
        
        return $excludedProducts;
    }

    /**
     * Thu thập tên sản phẩm từ file danh sách
     *
     * @param string $baseSku SKU gốc của sản phẩm
     * @param array &$productInfo Thông tin sản phẩm (tham chiếu)
     * @param array $productsData Dữ liệu từ file danh sách
     * @return void
     */
    private function collectProductName($baseSku, &$productInfo, $productsData)
    {
        // Khởi tạo tên sản phẩm mặc định là SKU
        $productInfo['name'] = $baseSku;
        
        foreach ($productsData as $row) {
            // Tìm các dòng có cùng SKU gốc
            if (isset($row['N']) && strpos($row['N'], $baseSku) === 0) {
                // Lấy tên sản phẩm từ cột A nếu có
                if (!empty($row['A'])) {
                    $productInfo['name'] = $row['A'];
                    break; // Dừng sau khi tìm thấy tên đầu tiên
                }
            }
        }
    }

    /**
     * Thu thập hình ảnh sản phẩm từ file danh sách
     *
     * @param string $baseSku SKU gốc của sản phẩm
     * @param array &$productInfo Thông tin sản phẩm (tham chiếu)
     * @param array $productsData Dữ liệu từ file danh sách
     * @return void
     */
    private function collectProductImages($baseSku, &$productInfo, $productsData)
    {
        foreach ($productsData as $row) {
            // Tìm các dòng có cùng SKU gốc
            if (isset($row['N']) && strpos($row['N'], $baseSku) === 0) {
                // Ưu tiên lấy hình ảnh từ cột R (chính)
                if (!in_array($row['R'], $productInfo['images'])) {
                    $productInfo['images'][] = $row['R'];
                }
                
                // Thu thập hình ảnh từ các cột P, Q (phụ) nếu có
                foreach (['P', 'Q'] as $col) {
                    if (!in_array($row[$col], $productInfo['images'])) {
                        $productInfo['images'][] = $row[$col];
                    }
                }
            }
        }
    }

    /**
     * Tối ưu số lượng sản phẩm để đạt tối thiểu 12 đôi
     *
     * @param string $baseSku SKU gốc của sản phẩm
     * @param array &$productInfo Thông tin sản phẩm (tham chiếu)
     * @param array $productsData Dữ liệu từ file danh sách sản phẩm
     * @return void
     */
    private function optimizeProductQuantities($baseSku, &$productInfo, $productsData)
    {
        // Lưu danh sách các size ban đầu trước khi tối ưu
        $originalSizes = [];
        foreach ($productInfo['sizes'] as $size => $quantity) {
            if ($quantity > 0) {
                $originalSizes[] = $size;
            }
        }
        // Lưu vào productInfo để sử dụng sau
        $productInfo['original_sizes'] = $originalSizes;
        
        $additionalNeeded = 12 - $productInfo['total_need']; // Số lượng còn thiếu
        
        // Kiểm tra xem sản phẩm có phải là giày nữ hay không
        $isWomensShoe = $this->isWomensShoe($baseSku, $productsData);
        
        // Chọn thứ tự ưu tiên size dựa vào loại giày
        if ($isWomensShoe) {
            $priorities = ['37', '38']; // Ưu tiên size 37, 38 cho giày nữ
            $productInfo['is_womens_shoe'] = true; // Đánh dấu là giày nữ
        } else {
            $priorities = ['42', '41', '43']; // Ưu tiên size 42, 41, 43 cho giày nam
            $productInfo['is_womens_shoe'] = false; // Đánh dấu là giày nam
        }
        
        $targetStocks = []; // Lưu thông tin tồn kho và số lượng hiện tại của mỗi size

        // Thu thập thông tin tồn kho cho các size ưu tiên
        foreach ($productsData as $row) {
            if (isset($row['N']) && strpos($row['N'], $baseSku) === 0) {
                $skuParts = explode('-', $row['N']);
                $size = $skuParts[1] ?? '';
                if (in_array($size, $priorities)) {
                    $currentStock = (int)($row['AA'] ?? 0);
                    $currentNeed = $productInfo['sizes'][$size] ?? 0;
                    $targetStocks[$size] = [
                        'stock' => $currentStock,
                        'current' => $currentNeed,
                    ];
                }
            }
        }

        // Tính số lượng cần thêm cho mỗi size ưu tiên để cân bằng
        if ($isWomensShoe) {
            // Đối với giày nữ: tối đa 6 đôi mỗi size, ưu tiên 37 nếu cần
            $this->distributeWomensShoeQuantities($productInfo, $targetStocks, $priorities, $additionalNeeded);
        } else {
            // Đối với giày nam: giữ nguyên logic cũ
            $targetPerSize = min(6, ceil($additionalNeeded / 3)); // Số lượng lý tưởng mỗi size
            $this->distributeAdditionalQuantities($productInfo, $targetStocks, $priorities, $additionalNeeded, $targetPerSize);
        }
    }

    /**
     * Phân bổ số lượng bổ sung cho giày nữ với ưu tiên size 37 và 38
     *
     * @param array &$productInfo Thông tin sản phẩm (tham chiếu)
     * @param array $targetStocks Thông tin tồn kho và số lượng hiện tại
     * @param array $priorities Thứ tự ưu tiên size, thường là ['37', '38']
     * @param int $additionalNeeded Số lượng còn thiếu
     * @return void
     */
    private function distributeWomensShoeQuantities(&$productInfo, $targetStocks, $priorities, $additionalNeeded)
    {
        // Dựa trên tồn kho sau khi nhập để tính toán phân phối
        $currentTotal = $productInfo['total_need']; // Tổng số lượng hiện tại
        $targetTotal = 12; // Tổng số lượng mục tiêu
        $remainingToAdd = $targetTotal - $currentTotal; // Số lượng cần thêm
        
        if ($remainingToAdd <= 0) return; // Không cần thêm

        // Thu thập thông tin tồn kho và số lượng hiện tại cần nhập
        $sizeInfo = [];
        foreach ($priorities as $size) {
            $currentStock = $targetStocks[$size]['stock'] ?? 0; // Tồn kho hiện tại
            $currentOrder = $productInfo['sizes'][$size] ?? 0; // Số lượng đang đặt
            
            $sizeInfo[$size] = [
                'currentStock' => $currentStock, // Tồn kho hiện tại
                'currentOrder' => $currentOrder, // Số lượng đang đặt
                'futureStock' => $currentStock + $currentOrder // Tồn kho tương lai sau khi nhập
            ];
        }
        
        // Bước 1: Kiểm tra xem sizes yêu cầu ban đầu có trùng với sizes ưu tiên không
        $originalSizesInPriorities = array_intersect($productInfo['original_sizes'] ?? [], $priorities);
        
        // Xử lý trường hợp đặc biệt cho ONITSUKATIGERPINKK
        // Khi size 37 và 38 đều có trong danh sách ban đầu
        if (count($originalSizesInPriorities) >= 2 && 
            in_array('37', $originalSizesInPriorities) && 
            in_array('38', $originalSizesInPriorities)) {
            
            // Lấy thông tin hiện tại
            $stock37 = $sizeInfo['37']['currentStock'];
            $stock38 = $sizeInfo['38']['currentStock'];
            $order37 = $sizeInfo['37']['currentOrder'];
            $order38 = $sizeInfo['38']['currentOrder'];
            
            // Tính toán tổng cần đạt
            $total = $order37 + $order38 + $remainingToAdd;
            
            // Phân phối lại để cân bằng tồn kho sau nhập
            $target = $stock37 + $stock38 + $total; // Tổng tồn kho sau khi nhập
            $targetPerSize = floor($target / 2); // Mục tiêu mỗi size
            
            // Tính toán số lượng mới cho mỗi size để cân bằng tồn kho sau nhập
            $newOrder37 = max(0, $targetPerSize - $stock37);
            $newOrder38 = max(0, $targetPerSize - $stock38);
            
            // Nếu không đủ tổng số lượng, điều chỉnh để đạt đủ tổng 12 đôi
            $adjustedTotal = $newOrder37 + $newOrder38;
            if ($adjustedTotal < $total) {
                // Thêm đôi dư vào size 37 (ưu tiên)
                $newOrder37 += ($total - $adjustedTotal);
            } else if ($adjustedTotal > $total) {
                // Giảm size 38 trước (ít ưu tiên hơn)
                $excess = $adjustedTotal - $total;
                if ($newOrder38 >= $excess) {
                    $newOrder38 -= $excess;
                } else {
                    $newOrder38 = 0;
                    $newOrder37 -= ($excess - $newOrder38);
                }
            }
            
            // Cập nhật lại thông tin đặt hàng
            $productInfo['sizes']['37'] = $newOrder37;
            $productInfo['sizes']['38'] = $newOrder38;
            $productInfo['total_need'] = $currentTotal + ($newOrder37 - $order37) + ($newOrder38 - $order38);
            
            // Lưu thông tin chi tiết về tồn kho sau khi nhập
            $productInfo['stock_after_order'] = [
                '37' => $stock37 + $newOrder37,
                '38' => $stock38 + $newOrder38
            ];
            
            return; // Kết thúc xử lý case đặc biệt
        }
        
        // Bước 2: Tính toán mức tồn kho mục tiêu cho các size ưu tiên
        // Mục tiêu là 6 đôi mỗi size sau khi nhập
        $targetStockLevel = 6;
        
        // Bước 3: Điều chỉnh số lượng nhập để đạt mức tồn kho cân bằng
        $totalAdded = 0;
        foreach ($priorities as $size) {
            $info = $sizeInfo[$size];
            // Kiểm tra nếu size này có trong original_sizes và vượt quá 6
            if (in_array($size, $originalSizesInPriorities) && $info['futureStock'] > 6) {
                // Đã vượt quá 6, không thêm nữa
                continue;
            }
            
            // Số lượng cần thêm để đạt targetStockLevel (6)
            $neededToBalance = max(0, $targetStockLevel - $info['futureStock']);
            
            // Không vượt quá tổng số lượng cần thêm
            $addAmount = min($neededToBalance, $remainingToAdd - $totalAdded);
            
            if ($addAmount > 0) {
                $productInfo['sizes'][$size] = ($productInfo['sizes'][$size] ?? 0) + $addAmount;
                $totalAdded += $addAmount;
                $sizeInfo[$size]['futureStock'] += $addAmount;
            }
        }
        
        // Bước 4: Nếu vẫn chưa đủ 12 đôi, ưu tiên thêm cho size 37 trước
        // Trong trường hợp này, có thể vượt quá 6 để đạt tổng số lượng là 12
        if ($totalAdded < $remainingToAdd) {
            $remainingAdd = $remainingToAdd - $totalAdded;
            
            foreach ($priorities as $size) {
                // Giới hạn số lượng thêm vào mỗi size là 6, trừ khi là size có trong original_sizes
                $maxAddForSize = in_array($size, $originalSizesInPriorities) ? $remainingAdd : 
                    max(0, 6 - ($sizeInfo[$size]['futureStock'] - $sizeInfo[$size]['currentStock']));
                
                $addAmount = min($remainingAdd, $maxAddForSize);
                
                if ($addAmount > 0) {
                    $productInfo['sizes'][$size] = ($productInfo['sizes'][$size] ?? 0) + $addAmount;
                    $totalAdded += $addAmount;
                    $remainingAdd -= $addAmount;
                    $sizeInfo[$size]['futureStock'] += $addAmount;
                }
                
                if ($remainingAdd <= 0) break;
            }
        }
        
        // Cập nhật tổng số lượng cần đặt
        $productInfo['total_need'] = $currentTotal + $totalAdded;
        
        // Đảm bảo rằng tổng số lượng luôn đạt tối thiểu 5 đôi
        if ($productInfo['total_need'] < 5) {
            $neededToReach5 = 5 - $productInfo['total_need'];
            $size = $priorities[0]; // Size 37
            
            // Tính số lượng có thể thêm cho size đầu tiên (37)
            $maxAddForSize = in_array($size, $originalSizesInPriorities) ? $neededToReach5 : 
                max(0, 6 - ($sizeInfo[$size]['futureStock'] - $sizeInfo[$size]['currentStock']));
            
            $addAmount = min($neededToReach5, $maxAddForSize);
            
            if ($addAmount > 0) {
                $productInfo['sizes'][$size] = ($productInfo['sizes'][$size] ?? 0) + $addAmount;
                $productInfo['total_need'] += $addAmount;
            }
        }
        
        // Lưu thông tin chi tiết về tồn kho sau khi nhập để debug và phân tích
        $productInfo['stock_after_order'] = [];
        foreach ($priorities as $size) {
            $currentStock = $targetStocks[$size]['stock'] ?? 0;
            $orderAmount = $productInfo['sizes'][$size] ?? 0;
            $productInfo['stock_after_order'][$size] = $currentStock + $orderAmount;
        }
    }

    /**
     * Phân bổ số lượng bổ sung cho các size ưu tiên của giày nam
     *
     * @param array &$productInfo Thông tin sản phẩm (tham chiếu)
     * @param array $targetStocks Thông tin tồn kho và số lượng hiện tại
     * @param array $priorities Thứ tự ưu tiên size
     * @param int $additionalNeeded Số lượng còn thiếu
     * @param int $targetPerSize Số lượng lý tưởng mỗi size
     * @return void
     */
    private function distributeAdditionalQuantities(&$productInfo, $targetStocks, $priorities, $additionalNeeded, $targetPerSize)
    {
        // Dựa trên tồn kho sau khi nhập để tính toán phân phối
        $currentTotal = $productInfo['total_need']; // Tổng số lượng hiện tại
        $targetTotal = 12; // Tổng số lượng mục tiêu
        $remainingToAdd = $targetTotal - $currentTotal; // Số lượng cần thêm
        
        if ($remainingToAdd <= 0) return; // Không cần thêm

        // Thu thập thông tin tồn kho và số lượng hiện tại cần nhập
        $sizeInfo = [];
        foreach ($priorities as $size) {
            $currentStock = $targetStocks[$size]['stock'] ?? 0; // Tồn kho hiện tại
            $currentOrder = $productInfo['sizes'][$size] ?? 0; // Số lượng đang đặt
            
            $sizeInfo[$size] = [
                'currentStock' => $currentStock, // Tồn kho hiện tại
                'currentOrder' => $currentOrder, // Số lượng đang đặt
                'futureStock' => $currentStock + $currentOrder // Tồn kho tương lai sau khi nhập
            ];
        }
        
        // Bước 1: Kiểm tra xem sizes yêu cầu ban đầu có trùng với sizes ưu tiên không
        $originalSizesInPriorities = array_intersect($productInfo['original_sizes'] ?? [], $priorities);
        $hasOriginalPrioritySizes = !empty($originalSizesInPriorities);
        
        // Bước 2: Tính toán mức tồn kho cân bằng lý tưởng cho các size ưu tiên
        // Mục tiêu tồn kho sau nhập là 6 đôi
        $targetStockLevel = 6;
        
        // Bước 3: Điều chỉnh số lượng nhập để đạt mức tồn kho cân bằng
        $totalAdded = 0;
        foreach ($priorities as $size) {
            $info = $sizeInfo[$size];
            // Kiểm tra nếu size này có trong original_sizes và vượt quá 6
            if (in_array($size, $originalSizesInPriorities) && $info['futureStock'] > 6) {
                // Đã vượt quá 6, không thêm nữa
                continue;
            }
            
            // Số lượng cần thêm để đạt targetStockLevel (6)
            $neededToBalance = max(0, $targetStockLevel - $info['futureStock']);
            
            // Không vượt quá tổng số lượng cần thêm
            $addAmount = min($neededToBalance, $remainingToAdd - $totalAdded);
            
            if ($addAmount > 0) {
                $productInfo['sizes'][$size] = ($productInfo['sizes'][$size] ?? 0) + $addAmount;
                $totalAdded += $addAmount;
                $sizeInfo[$size]['futureStock'] += $addAmount;
            }
        }
        
        // Bước 4: Nếu vẫn chưa đủ 12 đôi, ưu tiên thêm cho size 42 trước (đầu danh sách)
        // Trong trường hợp này, có thể vượt quá 6 để đạt tổng số lượng là 12
        if ($totalAdded < $remainingToAdd) {
            $remainingAdd = $remainingToAdd - $totalAdded;
            
            foreach ($priorities as $size) {
                // Giới hạn số lượng thêm vào mỗi size là 6, trừ khi là size có trong original_sizes
                $maxAddForSize = in_array($size, $originalSizesInPriorities) ? $remainingAdd : 
                    max(0, 6 - ($sizeInfo[$size]['futureStock'] - $sizeInfo[$size]['currentStock']));
                
                $addAmount = min($remainingAdd, $maxAddForSize);
                
                if ($addAmount > 0) {
                    $productInfo['sizes'][$size] = ($productInfo['sizes'][$size] ?? 0) + $addAmount;
                    $totalAdded += $addAmount;
                    $remainingAdd -= $addAmount;
                    $sizeInfo[$size]['futureStock'] += $addAmount;
                }
                
                if ($remainingAdd <= 0) break;
            }
        }
        
        // Cập nhật tổng số lượng cần đặt
        $productInfo['total_need'] = $currentTotal + $totalAdded;
        
        // Lưu thông tin chi tiết về tồn kho sau khi nhập để debug và phân tích
        $productInfo['stock_after_order'] = [];
        foreach ($priorities as $size) {
            $currentStock = $targetStocks[$size]['stock'] ?? 0;
            $orderAmount = $productInfo['sizes'][$size] ?? 0;
            $productInfo['stock_after_order'][$size] = $currentStock + $orderAmount;
        }
    }

    /**
     * Thu thập thông tin hình ảnh và tối ưu số lượng sản phẩm
     *
     * @param array $groupedProducts Sản phẩm đã nhóm theo SKU
     * @param array $productsData Dữ liệu từ file danh sách sản phẩm
     * @return array Sản phẩm đã được tối ưu
     */
    private function optimizeProductsData($groupedProducts, $productsData)
    {
        foreach ($groupedProducts as $baseSku => &$productInfo) {
            // Thu thập hình ảnh cho sản phẩm từ file danh sách
            $this->collectProductImages($baseSku, $productInfo, $productsData);
            
            // Thu thập tên sản phẩm từ file danh sách
            $this->collectProductName($baseSku, $productInfo, $productsData);
            
            // Tối ưu số lượng nếu chưa đủ 12 đôi
            if ($productInfo['total_need'] < 12) {
                $this->optimizeProductQuantities($baseSku, $productInfo, $productsData);
            }
        }

        return $groupedProducts;
    }

    /**
     * Tạo file Excel kết quả cho việc nhập hàng
     *
     * @param array $groupedProducts Sản phẩm đã được tối ưu
     * @param array &$reportData Dữ liệu cho báo cáo chi tiết (tham chiếu)
     * @return string Đường dẫn đến file kết quả
     */
    private function generateResultFile($groupedProducts, &$reportData)
    {
        // Tạo file Excel kết quả mới
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt tên sheet là ngày hiện tại (dùng _ thay vì / để tránh lỗi)
        $sheet->setTitle(date('d_m_Y'));

        // Thiết lập header cho file kết quả
        $this->setupResultFileHeaders($sheet);

        // Điền dữ liệu sản phẩm vào file
        $this->fillProductsData($sheet, $groupedProducts, $reportData);

        // Định dạng file kết quả
        $this->formatResultFile($sheet);

        // Lưu file kết quả - Thay / bằng - trong tên file để tránh lỗi đường dẫn
        $displayDate = date('d-m-Y');
        $safeFileName = 'File_nhap_giay_trung_quoc_' . $displayDate . '.xlsx';
        $outputPath = public_path('form_nhap_hang/' . $safeFileName);
        
        // Đảm bảo thư mục form_nhap_hang tồn tại
        $this->ensureDirectoryExists('form_nhap_hang');
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);
        
        return $outputPath;
    }

    /**
     * Thiết lập header cho file kết quả
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    private function setupResultFileHeaders($sheet)
    {
        $sheet->setCellValue('A1', 'Hình ảnh');
        $sheet->setCellValue('B1', 'Tên sản phẩm');
        for ($i = 0; $i <= 9; $i++) {
            $sheet->setCellValue(chr(67 + $i) . '1', 36 + $i);
        }
        $sheet->setCellValue('L1', 'Tổng');
        $sheet->setCellValue('M1', 'Giá');
        $sheet->setCellValue('N1', 'Thành tiền');
        $sheet->setCellValue('O1', 'SKU');
    }

    /**
     * Điền dữ liệu sản phẩm vào file kết quả
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param array $groupedProducts Sản phẩm đã được tối ưu
     * @param array &$reportData Dữ liệu cho báo cáo chi tiết (tham chiếu)
     * @return void
     */
    private function fillProductsData($sheet, $groupedProducts, &$reportData)
    {
        $row = 2;
        foreach ($groupedProducts as $baseSku => $productInfo) {
            if ($productInfo['total_need'] >= 12) { // Chỉ xử lý sản phẩm đủ 12 đôi
                // Cột A: Link hình ảnh chính và phụ
                $imageLinks = $productInfo['images'] ?? [];
                $sheet->setCellValue('A' . $row, implode("\n", $imageLinks));
                $sheet->getRowDimension($row)->setRowHeight(-1); // Tự động điều chỉnh chiều cao

                // Cột B: Tên sản phẩm
                $productName = $productInfo['name'] ?? $baseSku;
                $sheet->setCellValue('B' . $row, $productName);

                // Cột C-L: Số lượng cho từng size (36-45)
                $total = 0;
                for ($i = 0; $i <= 9; $i++) {
                    $size = 36 + $i;
                    $amount = $productInfo['sizes'][$size] ?? 0;
                    $sheet->setCellValue(chr(67 + $i) . $row, $amount);
                    $total += $amount;
                }

                // Lưu dữ liệu đầy đủ cho file báo cáo
                $reportData[] = [
                    'sku' => $baseSku,
                    'name' => $productName,
                    'images' => $imageLinks,
                    'sizes' => $productInfo['sizes'],
                    'total' => $total,
                    'amount' => $productInfo['total_need'],
                    'optimized' => ($productInfo['total_need'] >= 12 ? 'Đã tối ưu' : 'Chưa đủ 12 đôi'),
                    'original_sizes' => $productInfo['original_sizes'] ?? [] // Thêm thông tin size ban đầu
                ];

                // Hoàn thiện các cột còn lại
                $sheet->setCellValue('L' . $row, $total);
                $sheet->setCellValue('M' . $row, 10);
                $sheet->setCellValue('N' . $row, '=L' . $row . '*M' . $row);
                $sheet->setCellValue('O' . $row, $baseSku);
                $row++;
            }
        }
    }

    /**
     * Định dạng file kết quả
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    private function formatResultFile($sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Format tiêu đề
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A90E2']],
        ]);
        
        // Format đường viền
        $sheet->getStyle('A1:O' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Format số
        $sheet->getStyle('L2:N' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Tự động điều chỉnh kích thước cột
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Điều chỉnh cột hình ảnh
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setWrapText(true);
    }

    /**
     * Tạo file báo cáo chi tiết về việc nhập hàng
     *
     * @param array $reportData Dữ liệu cho báo cáo
     * @param array $excludedProducts Dữ liệu sản phẩm bị loại
     * @return void
     */
    private function generateDetailedReport($reportData, $excludedProducts = [])
    {
        $reportSpreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // ===== SHEET 1: Báo cáo chính =====
        $reportSheet = $reportSpreadsheet->getActiveSheet();
        $reportSheet->setTitle('Báo cáo nhập hàng');

        // Thiết lập header cho file báo cáo chi tiết
        $reportSheet->setCellValue('A1', 'Hình ảnh URL');    // URL hình ảnh
        $reportSheet->setCellValue('B1', 'Hình ảnh');        // Cột mới hiển thị hình ảnh
        $reportSheet->setCellValue('C1', 'Tên sản phẩm');    // Tên sản phẩm
        $reportSheet->setCellValue('D1', 'SKU');             // SKU gốc của sản phẩm
        
        // Tạo header cho các size từ 36-45 (E1-N1)
        for ($i = 0; $i <= 9; $i++) {
            $reportSheet->setCellValue(chr(69 + $i) . '1', 'Size ' . (36 + $i));
        }
        
        $reportSheet->setCellValue('O1', 'Tổng');         // Tổng số đôi cần nhập
        
        // Điền dữ liệu báo cáo
        $reportRow = 2;
        foreach ($reportData as $data) {
            // Cột A: Link hình ảnh (nếu có)
            $imageUrl = '';
            if (isset($data['images']) && !empty($data['images'])) {
                $imageUrl = $data['images'][0] ?? '';
                $reportSheet->setCellValue('A' . $reportRow, $imageUrl); // Lưu URL ảnh
                
                // Cột B: Hình ảnh hiển thị với công thức IMAGE
                // Sử dụng công thức Excel thông thường
                $reportSheet->setCellValue('B' . $reportRow, '=IMAGE(A' . $reportRow . ',2)');
            }
            
            // Cột C: Tên sản phẩm
            $reportSheet->setCellValue('C' . $reportRow, $data['name'] ?? $data['sku']);
            
            // Cột D: SKU
            $reportSheet->setCellValue('D' . $reportRow, $data['sku']);
            
            // Cột E-N: Số lượng cho từng size (36-45)
            $total = 0;
            $originalSizes = $data['original_sizes'] ?? []; // Lấy danh sách size ban đầu (trước khi tối ưu)
            
            for ($i = 0; $i <= 9; $i++) {
                $size = 36 + $i;
                $amount = $data['sizes'][$size] ?? 0;
                $reportSheet->setCellValue(chr(69 + $i) . $reportRow, $amount);
                $total += $amount;
                
                // Tô màu cho các size ban đầu (trước khi tối ưu)
                if (in_array($size, $originalSizes) && $amount > 0) {
                    $cellCoord = chr(69 + $i) . $reportRow;
                    $reportSheet->getStyle($cellCoord)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
                            'startColor' => ['rgb' => 'A9DFBF'] // Màu xanh đậm cho size ban đầu
                        ],
                    ]);
                }
                // Tô màu khác cho các size được thêm trong quá trình tối ưu
                else if ($amount > 0) {
                    $cellCoord = chr(69 + $i) . $reportRow;
                    $reportSheet->getStyle($cellCoord)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
                            'startColor' => ['rgb' => 'FFF3CD'] // Màu vàng nhạt cho size bổ sung
                        ],
                    ]);
                }
            }
            
            // Cột O: Tổng số lượng
            $reportSheet->setCellValue('O' . $reportRow, $total);
            
            $reportRow++;
        }

        // Format Sheet 1
        $this->formatReportSheet($reportSheet, 'A', 'O');
        
        // Điều chỉnh cột hình ảnh hiển thị
        $reportSheet->getColumnDimension('B')->setWidth(20);
        
        // ===== SHEET 2: Log sản phẩm bị loại =====
        $logSheet = $reportSpreadsheet->createSheet();
        $logSheet->setTitle('Log');
        
        // Thiết lập header cho sheet log
        $logSheet->setCellValue('A1', 'Hình ảnh URL');    // URL hình ảnh
        $logSheet->setCellValue('B1', 'Hình ảnh');        // Cột mới hiển thị hình ảnh
        $logSheet->setCellValue('C1', 'Tên sản phẩm');    // Tên sản phẩm
        $logSheet->setCellValue('D1', 'SKU');             // SKU gốc của sản phẩm
        
        // Tạo header cho các size từ 36-45 (E1-N1)
        for ($i = 0; $i <= 9; $i++) {
            $logSheet->setCellValue(chr(69 + $i) . '1', 'Size ' . (36 + $i));
        }
        
        $logSheet->setCellValue('O1', 'Tổng');    // Tổng số đôi cần nhập
        $logSheet->setCellValue('P1', 'Cần');     // Số lượng cần nhập
        $logSheet->setCellValue('Q1', 'Đang về'); // Số lượng đang về
        $logSheet->setCellValue('R1', 'Lý do không nhập');  // Lý do không nhập
        
        // Điền dữ liệu vào sheet log
        $logRow = 2;
        foreach ($excludedProducts as $baseSku => $data) {
            // Cột A: Link hình ảnh (nếu có)
            $imageUrl = '';
            if (isset($data['images']) && !empty($data['images'])) {
                $imageUrl = $data['images'][0] ?? '';
                $logSheet->setCellValue('A' . $logRow, $imageUrl); // Lưu URL ảnh
                
                // Cột B: Hình ảnh hiển thị với công thức IMAGE
                $logSheet->setCellValue('B' . $logRow, '=IMAGE(A' . $logRow . ',2)');
            }
            
            // Cột C: Tên sản phẩm
            $logSheet->setCellValue('C' . $logRow, $data['name'] ?? $baseSku);
            
            // Cột D: SKU
            $logSheet->setCellValue('D' . $logRow, $baseSku);
            
            // Cột E-N: Số lượng cho từng size (36-45)
            $total = 0;
            $allReasons = [];
            for ($i = 0; $i <= 9; $i++) {
                $size = 36 + $i;
                $amount = $data['sizes'][$size] ?? 0;
                $logSheet->setCellValue(chr(69 + $i) . $logRow, $amount);
                $total += $amount;
                
                // Thu thập lý do riêng cho từng size nếu có
                if (isset($data['reasons'][$size]) && !empty($data['reasons'][$size])) {
                    $allReasons[$size] = $data['reasons'][$size];
                }
            }
            
            // Cột O: Tổng số lượng
            $logSheet->setCellValue('O' . $logRow, $total);
            
            // Cột P-Q: Thông tin cần/đang về (tổng hợp từ original_data)
            $totalNeed = 0;
            $totalComing = 0;
            if (isset($data['original_data'])) {
                foreach ($data['original_data'] as $size => $info) {
                    $totalNeed += $info['need'] ?? 0;
                    $totalComing += $info['coming'] ?? 0;
                }
            }
            $logSheet->setCellValue('P' . $logRow, $totalNeed);
            $logSheet->setCellValue('Q' . $logRow, $totalComing);
            
            // Cột R: Lý do không nhập
            // Nếu có nhiều lý do khác nhau, hiển thị theo dạng size:lý do
            if (count($allReasons) > 1) {
                $formattedReasons = array_map(function($size, $reason) {
                    return "Size $size: $reason";
                }, array_keys($allReasons), $allReasons);
                $logSheet->setCellValue('R' . $logRow, implode("\n", $formattedReasons));
            } else {
                // Nếu chỉ có một lý do chung cho tất cả, hiển thị trực tiếp
                $mainReason = reset($allReasons) ?: "Không xác định";
                $logSheet->setCellValue('R' . $logRow, $mainReason);
            }
            
            $logRow++;
        }

        // Format Sheet 2
        $this->formatReportSheet($logSheet, 'A', 'R');
        
        // Điều chỉnh cột hình ảnh hiển thị cho sheet log
        $logSheet->getColumnDimension('B')->setWidth(20);
        
        // ===== SHEET 3: File gửi kho trung quốc =====
        $warehouseSheet = $reportSpreadsheet->createSheet();
        $warehouseSheet->setTitle('File gửi kho trung quốc');
        
        // Thiết lập header cho sheet file gửi kho trung quốc
        $warehouseSheet->setCellValue('A1', 'Hình ảnh');  // Cột hiển thị hình ảnh
        
        // Tạo header cho các size từ 36-45 (B1-K1)
        for ($i = 0; $i <= 9; $i++) {
            $warehouseSheet->setCellValue(chr(66 + $i) . '1', 'Size ' . (36 + $i));
        }
        
        $warehouseSheet->setCellValue('L1', 'Tổng');        // Tổng số đôi
        $warehouseSheet->setCellValue('M1', 'SKU');         // SKU gốc của sản phẩm
        $warehouseSheet->setCellValue('N1', 'Giá nhập');    // Giá nhập từ file data_shoes.xlsx (cột AG)
        $warehouseSheet->setCellValue('O1', 'Thành tiền');  // Tổng tiền (Giá nhập * Tổng)
        $warehouseSheet->setCellValue('P1', 'Tỷ giá');      // Tỷ giá VND/CNY
        $warehouseSheet->setCellValue('Q1', 'Tổng tiền VND'); // Thành tiền * Tỷ giá
        
        // Lấy dữ liệu từ file data_shoes.xlsx để lấy giá nhập
        $productsFilePath = public_path('uploads/data_shoes.xlsx');
        $productsData = $this->readExcelFile($productsFilePath);
        
        // Lấy tỷ giá từ request
        $exchangeRate = request()->input('exchange_rate', 3500); // Mặc định là 3500 nếu không có
        
        // Mảng lưu trữ giá nhập theo SKU
        $priceData = [];
        
        // Thu thập giá nhập từ file data_shoes.xlsx
        foreach ($productsData as $row) {
            if (isset($row['N']) && !empty($row['N'])) {
                $skuParts = explode('-', $row['N']);
                $baseSku = $skuParts[0];
                
                // Lấy giá nhập từ cột AG
                if (isset($row['AG']) && is_numeric(str_replace([',', '.'], '', $row['AG']))) {
                    $importPrice = str_replace([',', '.'], '', $row['AG']);
                    $priceData[$baseSku] = (float)$importPrice;
                }
            }
        }
        
        // Điền dữ liệu vào sheet file gửi kho trung quốc (xen kẽ tiêu đề và dữ liệu)
        $warehouseRow = 2;
        
        foreach ($reportData as $data) {
            $baseSku = $data['sku'];
            $imageUrl = isset($data['images'][0]) ? $data['images'][0] : '';
            $importPrice = isset($priceData[$baseSku]) ? $priceData[$baseSku] : 0;
            
            // Thêm cột hình ảnh với công thức IMAGE
            if (!empty($imageUrl)) {
                // $warehouseSheet->setCellValue('A' . $warehouseRow, '=IMAGE("' . $imageUrl . '",2)');
                $warehouseSheet->setCellValue('A' . $warehouseRow, $imageUrl); // Chỉ điền link ảnh
            }
            
            // Điền dữ liệu về size (B-K)
            $total = 0;
            for ($i = 0; $i <= 9; $i++) {
                $size = 36 + $i;
                $amount = $data['sizes'][$size] ?? 0;
                $warehouseSheet->setCellValue(chr(66 + $i) . $warehouseRow, $amount);
                $total += $amount;
            }
            
            // Điền các thông tin khác
            $warehouseSheet->setCellValue('L' . $warehouseRow, $total);                           // Tổng
            $warehouseSheet->setCellValue('M' . $warehouseRow, $baseSku);                         // SKU
            $warehouseSheet->setCellValue('N' . $warehouseRow, $importPrice);                     // Giá nhập
            $warehouseSheet->setCellValue('O' . $warehouseRow, '=L' . $warehouseRow . '*N' . $warehouseRow); // Thành tiền = Tổng * Giá nhập
            $warehouseSheet->setCellValue('P' . $warehouseRow, $exchangeRate);                    // Tỷ giá
            $warehouseSheet->setCellValue('Q' . $warehouseRow, '=O' . $warehouseRow . '*P' . $warehouseRow); // Tổng tiền VND = Thành tiền * Tỷ giá
            
            // Tăng hàng (mỗi sản phẩm chiếm 2 dòng: 1 cho tiêu đề, 1 cho dữ liệu)
            $warehouseRow += 2;
            
            // Nếu không phải hàng cuối, thêm một hàng tiêu đề
            if ($warehouseRow <= (count($reportData) * 2)) {
                $this->addWarehouseHeaderRow($warehouseSheet, $warehouseRow - 1);
            }
        }
        
        // Format Sheet file gửi kho trung quốc
        $this->formatWarehouseSheet($warehouseSheet, 'A', 'Q', $warehouseRow - 1);
        
        // Lưu file báo cáo với tên cố định trong thư mục uploads
        $reportWriter = IOFactory::createWriter($reportSpreadsheet, 'Xlsx');
        
        // Thiết lập tùy chọn cho writer để đảm bảo công thức được viết đúng
        if (method_exists($reportWriter, 'setPreCalculateFormulas')) {
            $reportWriter->setPreCalculateFormulas(false);
        }
        
        $reportWriter->save(public_path('uploads/nhap_hang_trung_quoc.xlsx'));
    }
    
    /**
     * Thêm hàng tiêu đề cho sheet file gửi kho trung quốc
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param int $row Hàng cần thêm tiêu đề
     * @return void
     */
    private function addWarehouseHeaderRow($sheet, $row)
    {
        $sheet->setCellValue('A' . $row, 'Hình ảnh');
        
        for ($i = 0; $i <= 9; $i++) {
            $sheet->setCellValue(chr(66 + $i) . $row, 'Size ' . (36 + $i));
        }
        
        $sheet->setCellValue('L' . $row, 'Tổng');
        $sheet->setCellValue('M' . $row, 'SKU');
        $sheet->setCellValue('N' . $row, 'Giá nhập');
        $sheet->setCellValue('O' . $row, 'Thành tiền');
        $sheet->setCellValue('P' . $row, 'Tỷ giá');
        $sheet->setCellValue('Q' . $row, 'Tổng tiền VND');
        
        // Format tiêu đề
        $sheet->getStyle('A' . $row . ':Q' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9E9E9']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ]);
    }
    
    /**
     * Format sheet file gửi kho trung quốc
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param string $startCol Cột bắt đầu
     * @param string $endCol Cột kết thúc
     * @param int $lastRow Hàng cuối cùng
     * @return void
     */
    private function formatWarehouseSheet($sheet, $startCol, $endCol, $lastRow)
    {
        // Format tiêu đề chính
        $sheet->getStyle($startCol . '1:' . $endCol . '1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9E9E9']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ]);
        
        // Format các ô dữ liệu
        $sheet->getStyle($startCol . '1:' . $endCol . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Định dạng số cho cột giá và tiền
        $sheet->getStyle('N2:Q' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Tự động điều chỉnh kích thước cột
        foreach (range($startCol, $endCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Điều chỉnh cột hình ảnh
        $sheet->getColumnDimension('A')->setWidth(20);
        
        // Căn giữa số liệu
        $numericCols = range('B', 'L');
        foreach ($numericCols as $col) {
            // Chỉ xử lý các cột hợp lệ
            if (ord($col) <= ord($endCol)) {
                $sheet->getStyle($col . '2:' . $col . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        }
    }
    
    /**
     * Format chung cho các sheet báo cáo
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param string $startCol Cột bắt đầu
     * @param string $endCol Cột kết thúc
     * @return void
     */
    private function formatReportSheet($sheet, $startCol, $endCol)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Format tiêu đề
        $sheet->getStyle($startCol . '1:' . $endCol . '1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9E9E9']],
        ]);
        
        // Format các ô dữ liệu
        $sheet->getStyle($startCol . '1:' . $endCol . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Tự động điều chỉnh kích thước cột
        foreach (range($startCol, $endCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Điều chỉnh cột hình ảnh
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setWrapText(true);
        
        // Điều chỉnh cột tên sản phẩm
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getStyle('B2:B' . $lastRow)->getAlignment()->setWrapText(true);
        
        // Căn giữa số liệu - Sửa lỗi ở đây
        $numericCols = range(chr(ord($startCol) + 3), chr(ord($startCol) + 15)); // Từ D đến P/Q
        
        // Xử lý từng cột một thay vì dùng chuỗi range (điều này gây ra lỗi)
        foreach ($numericCols as $col) {
            // Chỉ xử lý các cột hợp lệ (tránh vượt quá giới hạn cột của bảng chữ cái)
            if (ord($col) <= ord($endCol)) {
                $sheet->getStyle($col . '2:' . $col . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        }
        
        // Wrap text cho cột lý do (nếu có)
        if ($endCol >= 'Q') {
            $sheet->getColumnDimension('Q')->setWidth(40);
            $sheet->getStyle('Q2:Q' . $lastRow)->getAlignment()->setWrapText(true);
        }
    }

    /**
     * Cập nhật file báo cáo sản phẩm sắp hết sau khi xử lý
     *
     * @param array $lowStockData Dữ liệu gốc từ file báo cáo
     * @param array $groupedProducts Sản phẩm hợp lệ
     * @param array $excludedProducts Sản phẩm bị loại
     * @return void
     */
    private function updateLowStockFile($lowStockData, $groupedProducts, $excludedProducts)
    {
        // Tạo spreadsheet mới từ file đã tải lên
        $filePath = public_path('uploads/cannhap.xls');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Đặt tiêu đề cho các cột mới nếu chưa có
        if (empty($sheet->getCell('J1')->getValue())) {
            $sheet->setCellValue('J1', 'Kết quả xử lý');
            $sheet->setCellValue('K1', 'Lý do');
            // Định dạng tiêu đề
            $sheet->getStyle('J1:K1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9E9E9']],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ]);
        }
        
        // Cập nhật dữ liệu cho các sản phẩm hợp lệ
        foreach ($groupedProducts as $baseSku => $productInfo) {
            if (isset($productInfo['original_data'])) {
                foreach ($productInfo['original_data'] as $size => $info) {
                    $row = $info['row'];
                    $sheet->setCellValue('J' . $row, 'Đưa vào DS nhập');
                    $needToOrder = $productInfo['sizes'][$size] ?? 0;
                    
                    // Nếu số lượng đã tối ưu khác số lượng ban đầu, ghi chú rõ
                    $originalAmount = $info['need'] - $info['coming'] + 1;
                    if ($needToOrder != $originalAmount) {
                        $sheet->setCellValue('K' . $row, "Được điều chỉnh từ $originalAmount lên $needToOrder");
                    } else {
                        $sheet->setCellValue('K' . $row, "Giữ nguyên số lượng");
                    }
                    
                    // Định dạng ô kết quả
                    $sheet->getStyle('J' . $row)->applyFromArray([
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                    ]);
                }
            }
        }
        
        // Cập nhật dữ liệu cho các sản phẩm bị loại
        foreach ($excludedProducts as $baseSku => $productInfo) {
            if (isset($productInfo['original_data'])) {
                foreach ($productInfo['original_data'] as $size => $info) {
                    $row = $info['row'];
                    $sheet->setCellValue('J' . $row, 'Loại bỏ');
                    
                    // Ghi rõ lý do loại bỏ
                    $reason = $productInfo['reasons'][$size] ?? 'Không đủ điều kiện';
                    $sheet->setCellValue('K' . $row, $reason);
                    
                    // Định dạng ô kết quả
                    $sheet->getStyle('J' . $row)->applyFromArray([
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8D7DA']],
                    ]);
                }
            }
        }
        
        // Tự động điều chỉnh kích thước cột
        foreach (range('J', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Lưu file đã cập nhật
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save($filePath);
    }

    /**
     * Kiểm tra xem sản phẩm có phải giày nữ hay không
     * Sản phẩm là giày nữ nếu các size 41, 42, 43 có tồn kho tối thiểu là 0
     *
     * @param string $baseSku SKU gốc của sản phẩm
     * @param array $productsData Dữ liệu từ file danh sách sản phẩm
     * @return boolean True nếu là giày nữ, False nếu không
     */
    private function isWomensShoe($baseSku, $productsData)
    {
        $checkSizes = ['41', '42', '43'];
        $foundSizes = [];
        $minStockColumn = 'AC'; // Cột "LC_CN1_Tồn tối thiểu"
        
        // Kiểm tra từng size trong bảng dữ liệu
        foreach ($productsData as $row) {
            if (isset($row['N']) && strpos($row['N'], $baseSku) === 0) {
                $skuParts = explode('-', $row['N']);
                $size = $skuParts[1] ?? '';
                
                // Nếu là một trong các size cần kiểm tra
                if (in_array($size, $checkSizes)) {
                    $minStock = isset($row[$minStockColumn]) ? intval($row[$minStockColumn]) : -1;
                    $foundSizes[$size] = $minStock;
                }
            }
        }
        
        // Kiểm tra xem đã có dữ liệu của tất cả size cần kiểm tra chưa
        foreach ($checkSizes as $size) {
            // Nếu thiếu size hoặc min stock khác 0, không phải giày nữ
            if (!isset($foundSizes[$size]) || $foundSizes[$size] !== 0) {
                return false;
            }
        }
        
        // Nếu tất cả size 41,42,43 đều có tồn kho tối thiểu là 0
        return true;
    }

    /**
     * Tạo file định dạng nhập hàng cho Sapo
     *
     * @param array $reportData Dữ liệu sản phẩm đã được tối ưu
     * @return void
     */
    private function generateSapoFile($reportData)
    {
        // Đường dẫn đến file mẫu và file đích
        $templateFilePath = public_path('uploads/nhap_hang_sapo_template.xlsx');
        $outputFilePath = public_path('uploads/nhap_hang_sapo.xlsx');
        
        // Nếu không có file mẫu, kiểm tra xem có file hiện tại không để sử dụng như template
        if (!file_exists($templateFilePath) && file_exists($outputFilePath)) {
            // Sao chép file hiện tại làm template cho lần sau
            copy($outputFilePath, $templateFilePath);
        } 
        // Nếu không có cả hai file, tạo một file trống làm template
        else if (!file_exists($templateFilePath)) {
            // Tạo một spreadsheet trống
            $emptySpreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $emptySpreadsheet->getActiveSheet();
            
            // Thiết lập header cơ bản cho file Sapo
            $sheet->setCellValue('A7', 'Mã SKU');
            $sheet->setCellValue('B7', 'Mã Barcode');
            $sheet->setCellValue('C7', 'Tên sản phẩm');
            $sheet->setCellValue('D7', 'Số lượng');
            $sheet->setCellValue('I7', 'Đơn giá');
            
            // Lưu file template
            $writer = IOFactory::createWriter($emptySpreadsheet, 'Xlsx');
            $writer->save($templateFilePath);
        }
        
        // Sao chép file mẫu sang file đích (tạo file mới cho mỗi lần sử dụng)
        if (file_exists($outputFilePath)) {
            unlink($outputFilePath); // Xóa file cũ nếu tồn tại
        }
        copy($templateFilePath, $outputFilePath);
        
        // Đọc file Sapo đã tạo
        $spreadsheet = IOFactory::load($outputFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Lấy dữ liệu từ file data_shoes.xlsx để lấy giá bán buôn (cột AB)
        $productsFilePath = public_path('uploads/data_shoes.xlsx');
        $productsData = $this->readExcelFile($productsFilePath);
        
        // Mảng lưu trữ giá bán buôn theo SKU
        $wholesalePrices = [];
        
        // Thu thập giá bán buôn từ file data_shoes.xlsx
        foreach ($productsData as $row) {
            if (isset($row['N']) && !empty($row['N'])) {
                $skuParts = explode('-', $row['N']);
                $baseSku = $skuParts[0];
                $size = $skuParts[1] ?? '';
                
                // Lấy giá bán buôn từ cột AB (PL_Giá bán buôn)
                if (isset($row['AB']) && is_numeric(str_replace([',', '.'], '', $row['AB']))) {
                    $wholesalePrice = str_replace([',', '.'], '', $row['AB']);
                    $wholesalePrices[$baseSku.'-'.$size] = (float)$wholesalePrice;
                }
            }
        }

        // Điền dữ liệu vào file Sapo bắt đầu từ dòng 8
        $rowIndex = 8;
        
        foreach ($reportData as $data) {
            $baseSku = $data['sku'];
            $productName = $data['name'];
            
            // Điền dữ liệu cho từng size
            foreach ($data['sizes'] as $size => $quantity) {
                if ($quantity > 0) {
                    $sku = $baseSku . '-' . $size;
                    
                    // Cột A: Mã SKU
                    $sheet->setCellValue('A' . $rowIndex, $sku);
                    
                    // Cột B: Mã Barcode (giống SKU)
                    $sheet->setCellValue('B' . $rowIndex, $sku);
                    
                    // Cột C: Tên sản phẩm
                    $sheet->setCellValue('C' . $rowIndex, $productName . ' - Size ' . $size);
                    
                    // Cột D: Số lượng
                    $sheet->setCellValue('D' . $rowIndex, $quantity);
                    
                    // Cột I: Đơn giá (từ cột AB của data_shoes.xlsx)
                    $sheet->setCellValue('I' . $rowIndex, $wholesalePrices[$sku] ?? 0);
                    
                    $rowIndex++;
                }
            }
        }
        
        // Lưu file nhập hàng Sapo
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputFilePath);
    }
}
