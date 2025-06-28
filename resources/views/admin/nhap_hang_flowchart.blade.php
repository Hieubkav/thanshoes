<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flowchart Quy Trình Nhập Hàng Trung Quốc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .flowchart-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .step-box {
            transition: all 0.3s ease;
        }
        .step-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .arrow {
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 20px solid #4F46E5;
            margin: 10px auto;
        }
        .decision-diamond {
            width: 120px;
            height: 120px;
            background: #F59E0B;
            transform: rotate(45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            position: relative;
        }
        .decision-text {
            transform: rotate(-45deg);
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: white;
            line-height: 1.2;
        }
    </style>
</head>
<body class="flowchart-container">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-4">🔄 Quy Trình Nhập Hàng Trung Quốc</h1>
            <p class="text-white/80 text-lg">Sơ đồ chi tiết từng bước xử lý nhập hàng</p>
            <div class="mt-4">
                <a href="/tq" class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors">
                    ← Quay lại trang nhập hàng
                </a>
            </div>
        </div>

        <!-- Flowchart -->
        <div class="max-w-4xl mx-auto">
            <!-- Step 1: Start -->
            <div class="step-box bg-green-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">🚀 BẮT ĐẦU</h3>
                <p class="mt-2">Người dùng submit form tại /tq</p>
                <div class="text-sm mt-2 opacity-80">AdminController::nhap_hang(Request $request)</div>
            </div>
            <div class="arrow"></div>

            <!-- Step 2: Set Time Limit -->
            <div class="step-box bg-indigo-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">⏱️ THIẾT LẬP THỜI GIAN</h3>
                <p class="mt-2">set_time_limit(300) - Tăng thời gian thực thi lên 5 phút</p>
                <div class="text-sm mt-2 opacity-80">Tránh timeout khi xử lý file lớn</div>
            </div>
            <div class="arrow"></div>

            <!-- Step 3: Laravel Validation -->
            <div class="step-box bg-purple-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">✅ LARAVEL VALIDATION</h3>
                <p class="mt-2">$request->validate()</p>
                <div class="text-sm mt-2 opacity-80">
                    • 'excel_products' => 'required|file'<br>
                    • 'excel_low_stock' => 'required|file'<br>
                    • 'exchange_rate' => 'required|numeric'
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 4: File Extension Check -->
            <div class="step-box bg-orange-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">📋 KIỂM TRA PHẦN MỞ RỘNG</h3>
                <p class="mt-2">validateFileExtensions()</p>
                <div class="text-sm mt-2 opacity-80">
                    • getClientOriginalExtension()<br>
                    • Chỉ chấp nhận .xlsx, .xls<br>
                    • Throw Exception nếu sai định dạng
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Decision: Validation OK? -->
            <div class="decision-diamond">
                <div class="decision-text">
                    File hợp lệ?
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <div class="w-1/3 text-center">
                    <span class="text-red-400 font-bold">❌ KHÔNG</span>
                    <div class="step-box bg-red-500 text-white p-4 rounded-lg mt-2">
                        <h4 class="font-bold">🚫 THROW EXCEPTION</h4>
                        <p class="text-sm">Exception → catch → return error</p>
                    </div>
                </div>
                <div class="w-1/3 text-center">
                    <div class="arrow"></div>
                </div>
                <div class="w-1/3 text-center">
                    <span class="text-green-400 font-bold">✅ CÓ</span>
                </div>
            </div>

            <!-- Step 5: Create Directory -->
            <div class="step-box bg-cyan-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">📁 TẠO THỦ MỤC</h3>
                <p class="mt-2">saveUploadedFiles() - Tạo thư mục uploads</p>
                <div class="text-sm mt-2 opacity-80">
                    • Kiểm tra file_exists(public_path('uploads'))<br>
                    • mkdir($directory, 0777, true) nếu chưa có<br>
                    • Throw Exception nếu không tạo được
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 6: Move Files -->
            <div class="step-box bg-indigo-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">💾 DI CHUYỂN FILE</h3>
                <p class="mt-2">move() file với tên cố định</p>
                <div class="text-sm mt-2 opacity-80">
                    • excel_products → data_shoes.xlsx<br>
                    • excel_low_stock → cannhap.xls<br>
                    • Lưu vào public/uploads/
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 7: Read Excel Files -->
            <div class="step-box bg-teal-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">📖 ĐỌC FILE EXCEL</h3>
                <p class="mt-2">readExcelFile() cho 2 file</p>
                <div class="text-sm mt-2 opacity-80">
                    • IOFactory::load($filePath)<br>
                    • getActiveSheet()->toArray(null, true, true, true)<br>
                    • $productsData & $lowStockData
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 8: Loop Through Low Stock Data -->
            <div class="step-box bg-orange-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">🔄 DUYỆT DỮ LIỆU SẮP HẾT</h3>
                <p class="mt-2">ProcessLowStockDataAction - Vòng lặp từ dòng 6</p>
                <div class="text-sm mt-2 opacity-80">
                    • for ($row = 6; isset($lowStockData[$row]); $row++)<br>
                    • Lấy SKU từ cột B: $lowStockData[$row]['B']<br>
                    • Tách SKU: explode('-', $sku) → $baseSku & $size
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 9: Calculate Need To Order -->
            <div class="step-box bg-pink-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">🧮 TÍNH SỐ LƯỢNG CẦN NHẬP</h3>
                <p class="mt-2">Logic tính toán phức tạp</p>
                <div class="text-sm mt-2 opacity-80">
                    • Nếu cột I rỗng: needToOrder = needG - needH (+ 1 nếu size ≠ 36)<br>
                    • Nếu cột I có giá trị: needToOrder = (int)$needToOrderRaw<br>
                    • needG = cột G, needH = cột H
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Decision: Need To Order > 0? -->
            <div class="decision-diamond">
                <div class="decision-text">
                    needToOrder<br>> 0?
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <div class="w-1/3 text-center">
                    <span class="text-red-400 font-bold">❌ KHÔNG</span>
                    <div class="step-box bg-red-400 text-white p-4 rounded-lg mt-2">
                        <h4 class="font-bold">➕ THÊM VÀO EXCLUDED</h4>
                        <p class="text-sm">addToExcludedProducts()<br>Lý do: "Không cần nhập thêm"</p>
                    </div>
                </div>
                <div class="w-1/3 text-center">
                    <div class="arrow"></div>
                </div>
                <div class="w-1/3 text-center">
                    <span class="text-green-400 font-bold">✅ CÓ</span>
                    <div class="step-box bg-green-400 text-white p-4 rounded-lg mt-2">
                        <h4 class="font-bold">➕ THÊM VÀO VALID</h4>
                        <p class="text-sm">addToValidProducts()<br>Tích lũy total_need</p>
                    </div>
                </div>
            </div>

            <!-- Step 10: Filter By Minimum Quantity -->
            <div class="step-box bg-red-400 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">🔍 LỌC THEO SỐ LƯỢNG TỐI THIỂU</h3>
                <p class="mt-2">filterProductsByMinimumQuantity()</p>
                <div class="text-sm mt-2 opacity-80">
                    • Duyệt qua $groupedProducts<br>
                    • Nếu total_need < 6 → chuyển sang $excludedProducts<br>
                    • Lý do: "Tổng số lượng cần nhập chỉ có X (cần tối thiểu 6)"
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 11: Filter Products Data -->
            <div class="step-box bg-violet-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">🎯 LỌC DỮ LIỆU SẢN PHẨM</h3>
                <p class="mt-2">filterProductsData() - Lọc theo SKU hợp lệ</p>
                <div class="text-sm mt-2 opacity-80">
                    • $validBaseSkus = array_keys($groupedProducts)<br>
                    • Duyệt $productsData, chỉ giữ SKU có trong $validBaseSkus<br>
                    • Tách SKU từ cột N: explode('-', $row['N'])[0]
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 12: Collect Product Names -->
            <div class="step-box bg-cyan-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">📝 THU THẬP TÊN SẢN PHẨM</h3>
                <p class="mt-2">OptimizeProductQuantityAction - getProductName()</p>
                <div class="text-sm mt-2 opacity-80">
                    • Duyệt $filteredProductsData<br>
                    • Tìm dòng có SKU khớp với $baseSku<br>
                    • Lấy tên từ cột O: $row['O'] ?? $baseSku
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 13: Collect Product Images -->
            <div class="step-box bg-emerald-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">🖼️ THU THẬP HÌNH ẢNH</h3>
                <p class="mt-2">collectProductImages() - Lấy link ảnh</p>
                <div class="text-sm mt-2 opacity-80">
                    • Duyệt $filteredProductsData tìm SKU khớp<br>
                    • Thu thập từ cột P, Q, R, S, T<br>
                    • Lọc bỏ link rỗng, trả về mảng unique
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Decision: Need Optimization? -->
            <div class="decision-diamond">
                <div class="decision-text">
                    6 ≤ total_need<br>< 12?
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <div class="w-1/3 text-center">
                    <span class="text-blue-400 font-bold">❌ KHÔNG</span>
                    <div class="step-box bg-blue-400 text-white p-4 rounded-lg mt-2">
                        <h4 class="font-bold">📊 TÍNH STOCK AFTER ORDER</h4>
                        <p class="text-sm">calculateStockAfterOrder()<br>Không cần tối ưu</p>
                    </div>
                </div>
                <div class="w-1/3 text-center">
                    <div class="arrow"></div>
                </div>
                <div class="w-1/3 text-center">
                    <span class="text-green-400 font-bold">✅ CÓ</span>
                </div>
            </div>

            <!-- Step 14: Optimization Process -->
            <div class="step-box bg-yellow-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">⚡ QUÁ TRÌNH TỐI ƯU</h3>
                <p class="mt-2">optimizeProductQuantity() - Logic phức tạp</p>
                <div class="text-sm mt-2 opacity-80">
                    • Phân biệt giày nữ: isWomensShoe() (tìm "nữ" trong tên)<br>
                    • Size ưu tiên nữ: [37,38], nam: [42,41,43]<br>
                    • additionalNeeded = 12 - total_need<br>
                    • Thu thập dữ liệu tồn kho cho size ưu tiên
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 15: Check Can Optimize -->
            <div class="step-box bg-amber-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">🔍 KIỂM TRA KHẢ NĂNG TỐI ƯU</h3>
                <p class="mt-2">canOptimizeToTwelve() - Tính toán chi tiết</p>
                <div class="text-sm mt-2 opacity-80">
                    • Với mỗi size ưu tiên:<br>
                    • futureStock = currentStock + coming + currentOrder<br>
                    • canAdd = max(0, 6 - futureStock)<br>
                    • Tổng canAdd ≥ additionalNeeded?
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 9: Generate Reports -->
            <div class="step-box bg-emerald-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">📊 TẠO BÁO CÁO</h3>
                <p class="mt-2">GenerateExcelReportAction::run()</p>
                <div class="text-sm mt-2 opacity-80">
                    • File báo cáo chính<br>
                    • Sheet Log (sản phẩm bị loại)<br>
                    • Sheet File gửi kho TQ
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 10: Generate Sapo File -->
            <div class="step-box bg-violet-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">📦 TẠO FILE SAPO</h3>
                <p class="mt-2">Tạo file nhập hàng cho hệ thống Sapo</p>
                <div class="text-sm mt-2 opacity-80">
                    • Template Excel<br>
                    • Điền SKU, tên, số lượng, giá<br>
                    • Lưu nhap_hang_sapo.xlsx
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 11: Update Low Stock File -->
            <div class="step-box bg-amber-500 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">🔄 CẬP NHẬT FILE</h3>
                <p class="mt-2">Cập nhật file cannhap.xls</p>
                <div class="text-sm mt-2 opacity-80">
                    • Ghi số lượng nhập vào cột I<br>
                    • Đánh dấu sản phẩm đã xử lý<br>
                    • Lưu file gốc
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 12: Return Result -->
            <div class="step-box bg-green-600 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">📤 TRẢ VỀ KẾT QUẢ</h3>
                <p class="mt-2">Tạo thông báo thành công</p>
                <div class="text-sm mt-2 opacity-80">
                    • Số sản phẩm hợp lệ<br>
                    • Số sản phẩm bị loại<br>
                    • Tổng số lượng nhập<br>
                    • Tên file báo cáo
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Step 13: Controller Response -->
            <div class="step-box bg-blue-600 text-white p-6 rounded-lg text-center mb-4">
                <h3 class="text-xl font-bold">🔙 CONTROLLER RESPONSE</h3>
                <p class="mt-2">AdminController xử lý kết quả</p>
                <div class="text-sm mt-2 opacity-80">
                    back()->with(['success' => $result, 'report_filename' => 'file.xlsx'])
                </div>
            </div>
            <div class="arrow"></div>

            <!-- Final Step: Display Result -->
            <div class="step-box bg-green-700 text-white p-6 rounded-lg text-center mb-8">
                <h3 class="text-xl font-bold">🎉 HIỂN THỊ KẾT QUẢ</h3>
                <p class="mt-2">Người dùng thấy thông báo + nút download</p>
                <div class="text-sm mt-2 opacity-80">
                    Vẫn ở trang /tq với thông báo thành công
                </div>
            </div>

            <!-- Error Handling -->
            <div class="bg-red-500/20 border border-red-500 rounded-lg p-6 mt-8">
                <h3 class="text-xl font-bold text-red-300 mb-4">⚠️ XỬ LÝ LỖI</h3>
                <div class="text-red-200 text-sm">
                    <p><strong>Tại bất kỳ bước nào:</strong></p>
                    <p>• Nếu có Exception → catch → return "Lỗi khi xử lý: {message}"</p>
                    <p>• Controller kiểm tra nếu result bắt đầu bằng "Lỗi" → back()->with('error')</p>
                    <p>• Người dùng thấy thông báo lỗi màu đỏ</p>
                </div>
            </div>

            <!-- Key Functions Detail -->
            <div class="bg-white/10 rounded-lg p-6 mt-8">
                <h3 class="text-xl font-bold text-white mb-4">🔧 CÁC HÀM QUAN TRỌNG</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-white text-sm">
                    <div>
                        <h4 class="font-bold text-green-400 mb-2">📊 ProcessLowStockDataAction</h4>
                        <ul class="space-y-1 opacity-80">
                            <li>• addToValidProducts() - Thêm sản phẩm hợp lệ</li>
                            <li>• addToExcludedProducts() - Thêm sản phẩm bị loại</li>
                            <li>• filterProductsByMinimumQuantity() - Lọc < 6 đôi</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-400 mb-2">⚡ OptimizeProductQuantityAction</h4>
                        <ul class="space-y-1 opacity-80">
                            <li>• getProductName() - Lấy tên từ cột O</li>
                            <li>• collectProductImages() - Thu thập từ P,Q,R,S,T</li>
                            <li>• isWomensShoe() - Phân biệt giày nữ</li>
                            <li>• canOptimizeToTwelve() - Kiểm tra khả năng tối ưu</li>
                            <li>• performOptimization() - Thực hiện tối ưu</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-purple-400 mb-2">📋 GenerateExcelReportAction</h4>
                        <ul class="space-y-1 opacity-80">
                            <li>• generateMainResultFile() - File báo cáo chính</li>
                            <li>• generateDetailedReport() - Báo cáo chi tiết</li>
                            <li>• createLogSheet() - Sheet Log sản phẩm bị loại</li>
                            <li>• generateWarehouseFile() - File gửi kho TQ</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-yellow-400 mb-2">🔄 ProcessChinaImportAction</h4>
                        <ul class="space-y-1 opacity-80">
                            <li>• validateFileExtensions() - Kiểm tra .xlsx/.xls</li>
                            <li>• saveUploadedFiles() - Lưu file với tên cố định</li>
                            <li>• readExcelFile() - Đọc Excel thành array</li>
                            <li>• filterProductsData() - Lọc theo SKU hợp lệ</li>
                            <li>• generateSapoFile() - Tạo file nhập Sapo</li>
                            <li>• updateLowStockFile() - Cập nhật file gốc</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white/10 rounded-lg p-6 mt-8">
                <h3 class="text-xl font-bold text-white mb-4">📈 THỐNG KÊ QUY TRÌNH CHI TIẾT</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-white">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-400">4</div>
                        <div class="text-sm">Actions chính</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-400">25+</div>
                        <div class="text-sm">Bước logic chi tiết</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-400">5</div>
                        <div class="text-sm">File output được tạo</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-400">20+</div>
                        <div class="text-sm">Hàm helper</div>
                    </div>
                </div>
            </div>

            <!-- Data Flow -->
            <div class="bg-white/10 rounded-lg p-6 mt-8">
                <h3 class="text-xl font-bold text-white mb-4">🔄 LUỒNG DỮ LIỆU</h3>
                <div class="text-white text-sm space-y-3">
                    <div class="flex items-center space-x-3">
                        <span class="w-4 h-4 bg-green-500 rounded-full"></span>
                        <span><strong>Input:</strong> 2 file Excel + tỷ giá → Validation → Lưu file</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="w-4 h-4 bg-blue-500 rounded-full"></span>
                        <span><strong>Processing:</strong> Đọc Excel → Phân tích → Tối ưu → Tính toán</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="w-4 h-4 bg-purple-500 rounded-full"></span>
                        <span><strong>Output:</strong> 3 sheet Excel + file Sapo + cập nhật file gốc</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="w-4 h-4 bg-yellow-500 rounded-full"></span>
                        <span><strong>Response:</strong> Thông báo kết quả + nút download</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
