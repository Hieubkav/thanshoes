@extends('layouts.shoplayout')

@section('content')
<div class=" bg-gradient-to-br from-blue-500 to-purple-600 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Glass Container -->
        <div class="backdrop-blur-lg bg-white/30 rounded-2xl shadow-xl p-8 border border-white/20">
            <div class="flex justify-between items-center mb-8">
                <div class="flex-1 text-center">
                    <h2 class="text-3xl font-extrabold text-white">
                        Nhập Hàng Trung Quốc
                    </h2>
                </div>
                <div>
                    <a href="{{ route('admin.nhap_hang_flowchart') }}"
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        🔄 Xem Quy Trình
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.nhap_hang') }}" enctype="multipart/form-data" 
                  x-data="{ 
                    isSubmitting: false,
                    file1: '',
                    file2: '',
                    getFileName(fullPath) {
                        if (fullPath) {
                            const startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                            return fullPath.substring(startIndex + 1);
                        }
                        return '';
                    }
                  }" 
                  @submit="isSubmitting = true"
                  class="grid gap-4 grid-cols-1 md:grid-cols-3">
                @csrf

                <!-- File Input 1 -->
                <div class="mb-6 col-span-1">
                    <label class="block text-white text-sm font-medium mb-2" for="excel_products">
                        File Excel Danh Sách Sản Phẩm <span class="text-xs text-white/60">(data_shoes.xlsx)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-white/30 border-dashed rounded-lg hover:border-white/50 transition-colors duration-200"
                         :class="{'border-green-400 bg-green-400/10': file1}">
                        <div class="space-y-1 text-center">
                            <template x-if="!file1">
                                <svg class="mx-auto h-12 w-12 text-white" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4-4m4-12h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </template>
                            <template x-if="file1">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="mt-1 text-sm text-white font-medium" x-text="getFileName(file1)"></p>
                                </div>
                            </template>
                            <div class="flex text-sm text-white">
                                <label for="excel_products" class="relative cursor-pointer rounded-md font-medium text-blue-200 hover:text-blue-100 focus-within:outline-none">
                                    <span>Tải file lên</span>
                                    <input id="excel_products" name="excel_products" type="file" class="sr-only" accept=".xlsx,.xls" 
                                           @change="file1 = $event.target.value">
                                </label>
                                <p class="pl-1">hoặc kéo thả vào đây</p>
                            </div>
                            <p class="text-xs text-white/70">
                                Excel file (.xlsx, .xls) - Cần có cột R chứa link hình ảnh
                            </p>
                        </div>
                    </div>
                    @error('excel_products')
                        <p class="mt-2 text-pink-300 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Input 2 -->
                <div class="mb-6 col-span-1">
                    <label class="block text-white text-sm font-medium mb-2" for="excel_low_stock">
                        File Excel Báo Cáo Sản Phẩm Sắp Hết <span class="text-xs text-white/60">(cannhap.xls)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-white/30 border-dashed rounded-lg hover:border-white/50 transition-colors duration-200"
                         :class="{'border-green-400 bg-green-400/10': file2}">
                        <div class="space-y-1 text-center">
                            <template x-if="!file2">
                                <svg class="mx-auto h-12 w-12 text-white" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4-4m4-12h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </template>
                            <template x-if="file2">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="mt-1 text-sm text-white font-medium" x-text="getFileName(file2)"></p>
                                </div>
                            </template>
                            <div class="flex text-sm text-white">
                                <label for="excel_low_stock" class="relative cursor-pointer rounded-md font-medium text-blue-200 hover:text-blue-100 focus-within:outline-none">
                                    <span>Tải file lên</span>
                                    <input id="excel_low_stock" name="excel_low_stock" type="file" class="sr-only" accept=".xlsx,.xls"
                                           @change="file2 = $event.target.value">
                                </label>
                                <p class="pl-1">hoặc kéo thả vào đây</p>
                            </div>
                            <p class="text-xs text-white/70">
                                Excel file (.xlsx, .xls)
                            </p>
                        </div>
                    </div>
                    @error('excel_low_stock')
                        <p class="mt-2 text-pink-300 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exchange Rate Input -->
                <div class="mb-6 col-span-1">
                    <label for="exchange_rate" class="block text-white text-sm font-medium mb-2">
                        Tỉ Giá (VND/CNY)
                    </label>
                    <input type="number" name="exchange_rate" id="exchange_rate" step="0.01"
                        class="block w-full px-4 py-3 rounded-lg bg-white/20 border border-white/30 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                        placeholder="Nhập tỉ giá..." value="3500">
                    @error('exchange_rate')
                        <p class="mt-2 text-pink-300 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="mt-8 col-span-1  md:col-span-3">
                    <button type="submit"
                        class="w-full flex items-center justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-all duration-200"
                        :disabled="isSubmitting">
                        <template x-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isSubmitting ? 'Đang xử lý...' : 'Xử lý dữ liệu'"></span>
                    </button>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mt-4 p-4 rounded-lg bg-green-500/20 border border-green-500/30">
                        <p class="text-green-200 text-sm text-center whitespace-pre-line">{{ session('success') }}</p>

                        @if (session('report_filename'))
                            <div class="mt-6 space-y-4">
                                <div class="text-center">
                                    <p class="text-green-300 text-sm font-medium mb-3">📥 Tải xuống file báo cáo:</p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="text-center">
                                        <a href="{{ route('admin.download_nhap_hang_report') }}"
                                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 w-full justify-center">
                                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            📊 Báo cáo chi tiết
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <a href="{{ route('admin.download_nhap_hang_sapo') }}"
                                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 w-full justify-center">
                                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            📦 File Sapo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Error Message -->
                @if (session('error'))
                    <div class="mt-4 p-4 rounded-lg bg-red-500/20 border border-red-500/30">
                        <p class="text-red-200 text-sm text-center">{{ session('error') }}</p>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

@if (session('success') || session('error'))
    <script>
        // Giữ thông báo lâu hơn vì có hướng dẫn quan trọng
        setTimeout(() => {
            document.querySelector('.bg-green-500/20, .bg-red-500/20').style.display = 'none';
        }, 30000); // 30 seconds
    </script>
@endif
@endsection

@push('styles')
<style>
    /* Custom File Input Styling */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }

    /* Drag & Drop Zone Animation */
    .border-dashed {
        background-image: linear-gradient(90deg, transparent 50%, rgba(255,255,255,0.1) 50%);
        background-size: 8px 100%;
        animation: border-dance 1s linear infinite;
    }

    @keyframes border-dance {
        0% {
            background-position: 0 0;
        }
        100% {
            background-position: 8px 0;
        }
    }
    
    /* Code highlight */
    code {
        font-family: 'Courier New', monospace;
    }
</style>
@endpush

@push('scripts')
<script>
    // Drag and drop functionality
    ['excel_products', 'excel_low_stock'].forEach(inputId => {
        const dropZone = document.querySelector(`#${inputId}`).closest('div');
        const input = document.querySelector(`#${inputId}`);
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-blue-400');
            dropZone.classList.remove('border-white/30');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-blue-400');
            dropZone.classList.add('border-white/30');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            input.files = files;
            
            // Trigger change event for Alpine.js to detect
            const event = new Event('change', { bubbles: true });
            input.dispatchEvent(event);
        }
    });
</script>
@endpush
