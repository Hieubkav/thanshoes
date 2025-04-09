@extends('layouts.shoplayout')

@section('content')
    <!-- Form nhập file excel -->
    <div class="container mx-auto p-6">
        <div class="flex justify-center">
            <div class="w-full max-w-4xl">
                <h1 class="text-3xl font-bold mb-6 text-center text-green-600">Import Excel Sapo</h1>
                <form id="excelForm" action="{{ route('shop.import_excel') }}" method="POST" enctype="multipart/form-data"
                    class="bg-white shadow-lg rounded-lg px-10 pt-8 pb-10 mb-6">
                    @csrf
                    <div class="mb-6">
                        <label for="file" class="block text-gray-800 text-sm font-semibold mb-3">Chọn file excel</label>
                        <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv"
                            class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-800 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" id="submitBtn"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline">
                            <i class="fas fa-file-import mr-2"></i>Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container mx-auto p-6">
        <div class="w-full max-w-4xl bg-white shadow-lg rounded-lg p-6 mx-auto">
            <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">Hướng dẫn nhập Excel</h2>
            
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-3 text-gray-700">Cấu trúc file Excel phải có những cột sau:</h3>
                <ul class="list-disc pl-6 space-y-2 text-gray-600">
                    <li><strong>Cột A:</strong> Tên sản phẩm</li>
                    <li><strong>Cột C:</strong> Loại sản phẩm</li>
                    <li><strong>Cột D:</strong> Mô tả sản phẩm</li>
                    <li><strong>Cột E:</strong> Nhãn hiệu</li>
                    <li><strong>Cột F:</strong> Tags</li>
                    <li><strong>Cột H:</strong> Giá trị thuộc tính 1 (Size)</li>
                    <li><strong>Cột J:</strong> Giá trị thuộc tính 2 (Màu sắc)</li>
                    <li><strong>Cột N:</strong> Mã SKU (bắt buộc)</li>
                    <li><strong>Cột R:</strong> Ảnh đại diện</li>
                    <li><strong>Cột AA:</strong> LC_CN1_Tồn kho ban đầu</li>
                    <li><strong>Cột AF:</strong> PL_Giá bán lẻ</li>
                </ul>
            </div>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Lưu ý: Dữ liệu sẽ được nhập và xử lý ngay lập tức sau khi bạn tải lên file Excel.
                            File Excel cần phải theo đúng cấu trúc như Sapo để đảm bảo nhập liệu đúng.
                        </p>
                    </div>
                </div>
            </div>
            
            <div id="resultArea" class="hidden bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p id="resultMessage" class="text-sm text-green-700"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('excelForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Disable button and show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';
            
            let formData = new FormData(this);
            
            axios.post('{{ route('shop.import_excel') }}', formData)
                .then(function(response) {
                    // Show result message
                    const resultArea = document.getElementById('resultArea');
                    const resultMessage = document.getElementById('resultMessage');
                    
                    resultArea.classList.remove('hidden');
                    resultMessage.textContent = response.data;
                    
                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-file-import mr-2"></i>Import';
                })
                .catch(function(error) {
                    alert('Lỗi khi nhập file: ' + (error.response?.data?.message || 'Vui lòng kiểm tra file và thử lại'));
                    
                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-file-import mr-2"></i>Import';
                });
        });
    </script>
@endsection
