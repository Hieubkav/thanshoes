@extends('layouts.shoplayout')

@section('content')
    <!-- Tạo form nhập file excel , form đẹp và đơn giản là nhập file excel chuẩn form sapo -->
    <div class="container mx-auto p-6">
        <div class="flex justify-center">
            <div class="w-full max-w-4xl">
                <h1 class="text-3xl font-bold mb-6 text-center text-green-600">Import Excel Sapo</h1>
                <form action="" method="POST" enctype="multipart/form-data"
                    class="bg-white shadow-lg rounded-lg px-10 pt-8 pb-10 mb-6">
                    @csrf
                    <div class="mb-6">
                        <label for="file" class="block text-gray-800 text-sm font-semibold mb-3">Chọn file excel</label>
                        <input type="file" name="file" id="file"
                            class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-800 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline">
                            <i class="fas fa-file-import mr-2"></i>Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Thêm giao diện chỉ ra nếu thêm xong file và được thông báo nhập file thành công thì ấn nút này , nút này dẫn đến route shop.excel -->
    <div class="container mx-auto p-6">
        <div class="w-full max-w-4xl text-center">
            <p class="text-green-600 font-semibold mb-4">
                Nhập liệu thành công! 
                <br>
                Để áp dụng dữ liệu mới đó ấn nút này
            </p>
            <a href="{{ route('shop.excel') }}" target="_blank"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline">
                <i class="fas fa-file-import mr-2"></i>Xem dữ liệu
            </a>
        </div>
    </div>


    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();

            let formData = new FormData();
            formData.append('file', document.querySelector('#file').files[0]);

            axios.post('{{ route('shop.import_excel') }}', formData, {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(function(response) {
                    // trả lại thứ mà controller trả về
                    alert(response.data);
                })
                .catch(function(error) {
                    alert('Error importing file');
                });
        });
    </script>
@endsection
