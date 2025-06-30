@extends('layouts.shoplayout')

@section('content')
<div class="bg-gradient-to-br from-orange-500 to-red-600 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Glass Container -->
        <div class="backdrop-blur-lg bg-white/30 rounded-2xl shadow-xl p-8 border border-white/20">
            <div class="flex justify-between items-center mb-8">
                <div class="flex-1 text-center">
                    <h2 class="text-3xl font-extrabold text-white">
                        🔄 Cập Nhật File Sapo
                    </h2>
                    <p class="text-white/80 text-lg mt-2">Upload file báo cáo đã chỉnh sửa để tạo lại file Sapo mới</p>
                </div>
                <div>
                    <a href="/tq"
                       class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        ← Quay lại nhập hàng
                    </a>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="mb-8 p-4 rounded-lg bg-blue-500/20 border border-blue-500/30">
                <h3 class="text-white font-semibold mb-2">📋 Hướng dẫn sử dụng:</h3>
                <ul class="text-white/90 text-sm space-y-1">
                    <li><strong>Sử dụng 2 file đã được tạo từ /tq:</strong></li>
                    <li>1. <strong>File nhập hàng Sapo</strong> - File nhap_hang_sapo.xlsx đã tạo từ /tq</li>
                    <li>2. <strong>File nhập hàng Trung Quốc</strong> - File nhap_hang_trung_quoc.xlsx đã xóa bớt sản phẩm</li>
                    <li>3. Hệ thống sẽ <strong>lọc file Sapo</strong> chỉ giữ lại sản phẩm có trong file Trung Quốc</li>
                    <li>• Kết quả: File Sapo mới chỉ chứa sản phẩm cần nhập</li>
                </ul>
            </div>

            <!-- Form Upload -->
            <form method="POST" action="{{ route('admin.process_reversed_report') }}" enctype="multipart/form-data"
                  x-data="{
                    isUploading: false,
                    sapoFile: '',
                    reportFile: '',
                    debugResult: '',
                    showDebugResult: false,
                    getFileName(fullPath) {
                        if (fullPath) {
                            const startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                            return fullPath.substring(startIndex + 1);
                        }
                        return '';
                    },
                    async debugReport() {
                        if (!this.reportFile) {
                            alert('Vui lòng chọn file báo cáo Trung Quốc trước!');
                            return;
                        }

                        const formData = new FormData();
                        const reportFileInput = document.getElementById('report_file');
                        formData.append('report_file', reportFileInput.files[0]);
                        formData.append('_token', document.querySelector('meta[name=csrf-token]').getAttribute('content'));

                        try {
                            const response = await fetch('/tq-update/debug', {
                                method: 'POST',
                                body: formData
                            });

                            const result = await response.text();
                            this.debugResult = result;
                            this.showDebugResult = true;
                        } catch (error) {
                            alert('Lỗi khi debug: ' + error.message);
                        }
                    }
                  }"
                  @submit="isUploading = true"
                  class="space-y-6">
                @csrf

                <!-- File Input 1: Sapo -->
                <div class="mb-6">
                    <label class="block text-white text-sm font-medium mb-2" for="sapo_file">
                        1. File nhập hàng Sapo <span class="text-xs text-white/60">(nhap_hang_sapo.xlsx)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-6 pb-6 border-2 border-white/30 border-dashed rounded-lg hover:border-white/50 transition-colors duration-200"
                         :class="{'border-green-400 bg-green-400/10': sapoFile}">
                        <div class="space-y-1 text-center">
                            <template x-if="!sapoFile">
                                <svg class="mx-auto h-12 w-12 text-white" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4-4m4-12h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </template>
                            <template x-if="sapoFile">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="mt-1 text-sm text-white font-medium" x-text="getFileName(sapoFile)"></p>
                                </div>
                            </template>
                            <div class="flex text-sm text-white justify-center">
                                <label for="sapo_file" class="relative cursor-pointer rounded-md font-medium text-green-200 hover:text-green-100 focus-within:outline-none">
                                    <span>Chọn file Sapo</span>
                                    <input id="sapo_file" name="sapo_file" type="file" class="sr-only" accept=".xlsx,.xls"
                                           @change="sapoFile = $event.target.value">
                                </label>
                                <p class="pl-1">hoặc kéo thả</p>
                            </div>
                            <p class="text-xs text-white/70">
                                File nhap_hang_sapo.xlsx đã tạo từ /tq
                            </p>
                        </div>
                    </div>
                    @error('sapo_file')
                        <p class="mt-2 text-pink-300 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Input 2: Trung Quốc -->
                <div class="mb-6">
                    <label class="block text-white text-sm font-medium mb-2" for="report_file">
                        2. File nhập hàng Trung Quốc <span class="text-xs text-white/60">(nhap_hang_trung_quoc.xlsx)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-6 pb-6 border-2 border-white/30 border-dashed rounded-lg hover:border-white/50 transition-colors duration-200"
                         :class="{'border-orange-400 bg-orange-400/10': reportFile}">
                        <div class="space-y-1 text-center">
                            <template x-if="!reportFile">
                                <svg class="mx-auto h-12 w-12 text-white" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4-4m4-12h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </template>
                            <template x-if="reportFile">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="mt-1 text-sm text-white font-medium" x-text="getFileName(reportFile)"></p>
                                </div>
                            </template>
                            <div class="flex text-sm text-white justify-center">
                                <label for="report_file" class="relative cursor-pointer rounded-md font-medium text-orange-200 hover:text-orange-100 focus-within:outline-none">
                                    <span>Chọn file Trung Quốc</span>
                                    <input id="report_file" name="report_file" type="file" class="sr-only" accept=".xlsx,.xls"
                                           @change="reportFile = $event.target.value">
                                </label>
                                <p class="pl-1">hoặc kéo thả</p>
                            </div>
                            <p class="text-xs text-white/70">
                                File nhap_hang_trung_quoc.xlsx đã xóa bớt sản phẩm
                            </p>
                        </div>
                    </div>
                    @error('report_file')
                        <p class="mt-2 text-pink-300 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Debug Button -->
                <div class="text-center mb-4">
                    <button type="button"
                        @click="debugReport()"
                        :disabled="!reportFile"
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 disabled:opacity-50 transition-all duration-200">
                        🔍 Debug: Xem SKU từ file TQ
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit"
                        class="inline-flex items-center px-8 py-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 transition-all duration-200"
                        :disabled="isUploading">
                        <template x-if="isUploading">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isUploading ? 'Đang xử lý...' : '🔄 Lọc File Sapo'"></span>
                    </button>
                </div>

                <!-- Debug Result -->
                <div x-show="showDebugResult" class="mt-6 p-4 rounded-lg bg-yellow-500/20 border border-yellow-500/30">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-yellow-200 font-semibold">🔍 Debug: Danh sách SKU từ file Trung Quốc</h3>
                        <button @click="showDebugResult = false" class="text-yellow-200 hover:text-yellow-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <pre class="text-yellow-100 text-sm whitespace-pre-wrap bg-black/20 p-3 rounded overflow-auto max-h-96" x-text="debugResult"></pre>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mt-6 p-4 rounded-lg bg-green-500/20 border border-green-500/30">
                        <p class="text-green-200 text-sm text-center whitespace-pre-line">{{ session('success') }}</p>

                        @if (session('sapo_regenerated'))
                            <!-- Hiển thị nút download khi đã tạo file Sapo -->
                            <div class="mt-4 text-center">
                                <a href="{{ route('admin.download_nhap_hang_sapo') }}"
                                   class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    📦 Tải File Sapo Đã Lọc
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Error Message -->
                @if (session('error'))
                    <div class="mt-6 p-4 rounded-lg bg-red-500/20 border border-red-500/30">
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
            const messages = document.querySelectorAll('.bg-green-500/20, .bg-red-500/20');
            messages.forEach(msg => msg.style.display = 'none');
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
</style>
@endpush

@push('scripts')
<script>
    // Drag and drop functionality for both file inputs
    ['sapo_file', 'report_file'].forEach(inputId => {
        const input = document.querySelector('#' + inputId);
        const dropZone = input.closest('div');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => highlight(e, dropZone), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => unhighlight(e, dropZone), false);
        });

        function highlight(e, zone) {
            if (inputId === 'sapo_file') {
                zone.classList.add('border-green-400');
            } else {
                zone.classList.add('border-orange-400');
            }
            zone.classList.remove('border-white/30');
        }

        function unhighlight(e, zone) {
            zone.classList.remove('border-green-400', 'border-orange-400');
            zone.classList.add('border-white/30');
        }

        dropZone.addEventListener('drop', (e) => handleDrop(e, input), false);

        function handleDrop(e, targetInput) {
            const dt = e.dataTransfer;
            const files = dt.files;
            targetInput.files = files;

            // Trigger change event for Alpine.js to detect
            const event = new Event('change', { bubbles: true });
            targetInput.dispatchEvent(event);
        }
    });
</script>
@endpush
