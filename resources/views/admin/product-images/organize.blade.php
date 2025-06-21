<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sắp xếp ảnh sản phẩm - {{ $product->name }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SortableJS - Sử dụng version mới nhất và ổn định nhất -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .image-item {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: move;
            touch-action: none; /* Giúp kéo thả tốt hơn trên thiết bị cảm ứng */
        }
        
        .image-item:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }
        
        .sortable-ghost {
            opacity: 0.5;
            background-color: #e0f2fe !important;
            border: 2px dashed #3b82f6 !important;
            transform: scale(0.95);
            box-shadow: none !important;
        }
        
        .sortable-drag {
            cursor: grabbing;
            opacity: 0.9;
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            z-index: 100;
        }
        
        .sortable-chosen {
            background-color: #f0f9ff;
            box-shadow: 0 0 0 2px #3b82f6;
        }
        
        /* Hiệu ứng khi kéo qua các vị trí */
        .image-item.sortable-drag + .image-item {
            transform: translateX(5px);
        }
        
        /* Thiết kế cuộn mượt mà */
        html {
            scroll-behavior: smooth;
        }
        
        /* Hiệu ứng ripple khi click */
        .ripple {
            position: relative;
            overflow: hidden;
        }
        
        .ripple:after {
            content: "";
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, #fff 10%, transparent 10.01%);
            background-repeat: no-repeat;
            background-position: 50%;
            transform: scale(10, 10);
            opacity: 0;
            transition: transform .5s, opacity 1s;
        }
        
        .ripple:active:after {
            transform: scale(0, 0);
            opacity: .3;
            transition: 0s;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="max-w-[1920px] mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 bg-primary-600 bg-gradient-to-r from-blue-500 to-indigo-700">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-2 md:space-y-0">
                    <h1 class="text-2xl font-bold text-white">
                        Sắp xếp ảnh sản phẩm: {{ $product->name }}
                    </h1>
                    <div class="space-x-2">
                        <button id="saveOrderBtn" class="ripple px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Lưu thứ tự
                        </button>
                        <button id="resetOrderBtn" class="ripple px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Đặt lại thứ tự
                        </button>
                        <button id="backBtn" onclick="window.close()" class="ripple px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Quay lại
                        </button>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Hướng dẫn:</strong> Kéo và thả hình ảnh để sắp xếp lại thứ tự. Ảnh bên trái sẽ hiển thị trước tiên. Nhấn "Lưu thứ tự" sau khi hoàn tất.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Image Grid -->
            <div class="p-6">
                <div id="image-grid-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-3">
                    @foreach($images as $image)
                    <div data-id="{{ $image->id }}" class="image-item bg-white rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                        <div class="relative">
                            <img src="{{ $image->image_url }}" alt="Hình ảnh sản phẩm" loading="lazy" class="aspect-square w-full object-cover">
                            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 opacity-0 hover:bg-opacity-30 hover:opacity-100 transition-all duration-200">
                                <div class="p-2 bg-white rounded-full shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 flex justify-between items-center bg-white text-xs">
                            <span class="px-1.5 py-0.5 text-xs font-medium rounded bg-blue-50 text-blue-700">
                                #{{ $image->order }}
                            </span>
                            <span class="px-1.5 py-0.5 rounded {{ $image->type === 'upload' ? 'bg-green-50 text-green-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ $image->type === 'upload' ? 'Upload' : 'Variant' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lưu vị trí scroll để phục hồi sau khi kéo thả
            let scrollPosition = 0;
            
            // Khởi tạo Sortable
            const container = document.getElementById('image-grid-container');
            
            if (!container) {
                console.error('Container không tìm thấy');
                return;
            }
            
            // Trình phát âm thanh nhẹ khi kéo thả
            const audioContext = window.AudioContext || window.webkitAudioContext;
            let audio;
            
            // Hàm phát âm thanh nhẹ khi thả
            function playDropSound() {
                try {
                    if (audioContext) {
                        const ctx = new audioContext();
                        const oscillator = ctx.createOscillator();
                        const gainNode = ctx.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(ctx.destination);
                        
                        oscillator.type = 'sine';
                        oscillator.frequency.value = 600;
                        gainNode.gain.value = 0.1;
                        
                        oscillator.start();
                        gainNode.gain.exponentialRampToValueAtTime(0.00001, ctx.currentTime + 0.3);
                        oscillator.stop(ctx.currentTime + 0.3);
                    }
                } catch (e) {
                    console.log('Không thể phát âm thanh', e);
                }
            }
            
            // Khởi tạo SortableJS với cấu hình tối ưu
            try {
                const sortable = new Sortable(container, {
                    animation: 300, // Tốc độ animation, 300ms là mượt mà nhưng không quá chậm
                    easing: "cubic-bezier(1, 0, 0, 1)", // Easing function mượt mà hơn
                    
                    // Classes giao diện
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    
                    // Cấu hình kéo thả
                    delay: 50, // Độ trễ nhỏ để tránh kích hoạt vô tình
                    delayOnTouchOnly: true, // Chỉ áp dụng delay trên thiết bị cảm ứng
                    touchStartThreshold: 3, // Cho phép di chuyển nhỏ trước khi kích hoạt kéo
                    
                    // Hiệu ứng kéo thả
                    forceFallback: false, // Chỉ sử dụng fallback khi cần thiết để tối ưu hiệu năng
                    fallbackTolerance: 3, // Dung sai cho di chuyển nhỏ
                    
                    // Cuộn trang khi kéo tới rìa
                    scroll: true,
                    scrollSensitivity: 80, // Kích hoạt cuộn ở khoảng cách xa hơn
                    scrollSpeed: 40, // Tốc độ cuộn nhanh hơn
                    
                    // Xử lý khi bắt đầu kéo
                    onStart: function(evt) {
                        // Lưu vị trí scroll để khôi phục sau
                        scrollPosition = window.scrollY;
                        
                        // Highlight item đang được kéo
                        evt.item.style.zIndex = "1000";
                        
                        // Lưu vị trí kéo để có thể so sánh sau
                        evt.item.dataset.oldIndex = evt.oldIndex;
                    },
                    
                    // Xử lý khi kéo xong
                    onEnd: function(evt) {
                        // Khôi phục vị trí scroll
                        window.scrollTo(0, scrollPosition);
                        
                        // Reset z-index
                        evt.item.style.zIndex = "";
                        
                        // Phát âm thanh nhẹ khi thả
                        playDropSound();
                        
                        // Cập nhật số thứ tự hiển thị
                        updateOrderNumbers();
                        
                        // Hiệu ứng cho mục đích kéo vào
                        if (evt.oldIndex !== evt.newIndex) {
                            const item = evt.item;
                            item.classList.add('bg-blue-50');
                            setTimeout(() => {
                                item.classList.remove('bg-blue-50');
                            }, 800);
                        }
                    },
                    
                    // Xử lý khi di chuyển trong quá trình kéo
                    onMove: function(evt) {
                        // Logic tùy chỉnh khi di chuyển (nếu cần)
                        return true; // cho phép di chuyển
                    }
                });
                
                console.log('SortableJS khởi tạo thành công');
            } catch (error) {
                console.error('Lỗi khởi tạo SortableJS:', error);
                alert('Đã xảy ra lỗi khi khởi tạo tính năng kéo thả. Vui lòng tải lại trang.');
            }

            // Cập nhật số thứ tự hiển thị trên giao diện
            function updateOrderNumbers() {
                const items = container.querySelectorAll('.image-item');
                items.forEach((item, index) => {
                    const orderBadge = item.querySelector('.bg-blue-50');
                    if (orderBadge) {
                        orderBadge.textContent = '#' + (index + 1);
                    }
                });
            }

            // Lưu thứ tự
            document.getElementById('saveOrderBtn').addEventListener('click', function() {
                // Hiệu ứng khi nhấn nút
                this.classList.add('scale-95');
                setTimeout(() => this.classList.remove('scale-95'), 100);
                
                // Lấy tất cả ID ảnh theo thứ tự hiện tại
                const imageIds = Array.from(container.querySelectorAll('[data-id]')).map(el => el.dataset.id);
                
                // Hiển thị loading
                Swal.fire({
                    title: 'Đang cập nhật...',
                    text: 'Vui lòng chờ trong giây lát',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Gửi request AJAX để cập nhật thứ tự
                fetch('{{ route("product.images.update-order", ["product" => $product->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        imageIds: imageIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: 'Thứ tự ảnh đã được cập nhật',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Thông báo trang cha cập nhật
                            if (window.opener && !window.opener.closed) {
                                window.opener.postMessage({ 
                                    type: 'refresh_product_images',
                                    productId: '{{ $product->id }}'
                                }, '*');
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: data.message || 'Đã xảy ra lỗi khi cập nhật thứ tự ảnh',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Đã xảy ra lỗi khi cập nhật thứ tự ảnh',
                        icon: 'error'
                    });
                });
            });

            // Đặt lại thứ tự
            document.getElementById('resetOrderBtn').addEventListener('click', function() {
                // Hiệu ứng khi nhấn nút
                this.classList.add('scale-95');
                setTimeout(() => this.classList.remove('scale-95'), 100);
                
                Swal.fire({
                    title: 'Xác nhận đặt lại?',
                    text: 'Điều này sẽ đặt lại thứ tự của tất cả ảnh. Bạn có chắc chắn?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý, đặt lại!',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hiển thị loading
                        Swal.fire({
                            title: 'Đang đặt lại...',
                            text: 'Vui lòng chờ trong giây lát',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Gửi request AJAX để đặt lại thứ tự
                        fetch('{{ route("product.images.reset-order", ["product" => $product->id]) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Đã đặt lại!',
                                    text: 'Thứ tự ảnh đã được đặt lại thành công.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Thông báo trang cha cập nhật
                                    if (window.opener && !window.opener.closed) {
                                        window.opener.postMessage({ 
                                            type: 'refresh_product_images',
                                            productId: '{{ $product->id }}'
                                        }, '*');
                                    }
                                    // Reload trang để cập nhật thứ tự mới
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: 'Đã xảy ra lỗi khi đặt lại thứ tự ảnh',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Lỗi!',
                                text: 'Đã xảy ra lỗi khi đặt lại thứ tự ảnh',
                                icon: 'error'
                            });
                        });
                    }
                });
            });
            
            // Làm giảm độ trễ khi tương tác với ảnh trên thiết bị cảm ứng
            document.querySelectorAll('.image-item').forEach(item => {
                item.addEventListener('touchstart', function(e) {
                    // Ngăn việc cuộn trang khi bắt đầu kéo ảnh
                    e.preventDefault();
                }, { passive: false });
                
                // Thêm hiệu ứng phản hồi khi chạm
                item.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                
                item.addEventListener('touchend', function() {
                    this.style.transform = '';
                });
            });
        });
    </script>
</body>

</html>