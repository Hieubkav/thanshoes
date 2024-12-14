@extends('layouts.shoplayout')

@section('content')
    {{-- data chính là dữ liệu lấy từ $filePath = public_path('uploads/data_shoes.xlsx');

        $spreadsheet = IOFactory::load($filePath);

        // Lấy ra sheet đầu tiên
        $sheet = $spreadsheet->getActiveSheet();

        // Duyệt qua từng dòng trong cột A (từ dòng 1 đến hết dữ liệu)
        $data = [];
        foreach ($sheet->getColumnIterator('A') as $column) {
            foreach ($column->getCellIterator() as $cell) {
                $data[] = $cell->getValue(); // Lấy giá trị của ô trong cột A
            }
        }

        return view('excel', compact('data')); --}}

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Excel</h1>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Ảnh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $item)
                            @if ($key % 4 == 0)
                                <tr>
                            @endif
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item }}</td>
                            @if ($key % 4 == 3)
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection