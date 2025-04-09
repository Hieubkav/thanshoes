<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎉 Đơn hàng mới #{{ $order->id }} - ThanShoes 🎉</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; background-color: #f0f4f8; margin: 0; padding: 0;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);">
        <tr>
            <td style="padding: 40px 30px; background: linear-gradient(135deg, #6B46C1 0%, #3B82F6 100%); text-align: center;">
                <h1 style="color: #ffffff; font-size: 32px; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">🎊 Đơn hàng mới! 🎊</h1>
                <p style="color: #E9D8FD; font-size: 18px; margin-top: 10px;">#{{ $order->id }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px; background: url('https://example.com/confetti-bg.png') center/cover;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: rgba(255, 255, 255, 0.9); border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <tr>
                        <td style="padding: 20px;">
                            <h2 style="font-size: 24px; color: #4A5568; margin: 0 0 20px; text-align: center;">🌟 Thông tin đơn hàng 🌟</h2>
                            <p style="font-size: 18px; color: #2D3748; margin: 0 0 10px;"><strong>💰 Tổng giá trị:</strong> <span style="color: #48BB78; font-weight: bold;">{{ number_format($order->items->sum(function($item) { return $item->price * $item->quantity; }), 0, ',', '.') }}đ</span></p>
                            <p style="font-size: 18px; color: #2D3748; margin: 0 0 10px;"><strong>👤 Khách hàng:</strong> {{ $order->customer->name }} - {{ $order->customer->phone }}</p>
                            <p style="font-size: 18px; color: #2D3748; margin: 0;"><strong>💳 Thanh toán:</strong> {{ $order->payment_method == "cod" ? "COD 🚚" : "Chuyển khoản ngân hàng 🏦" }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 30px 30px;">
                <h2 style="font-size: 24px; color: #4A5568; margin: 30px 0 20px; text-align: center; text-transform: uppercase; letter-spacing: 2px;">📦 Sản phẩm trong đơn 📦</h2>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: separate; border-spacing: 0 15px;">
                    @foreach($order->items as $item)
                    <tr>
                        <td width="100" style="vertical-align: middle;">
                            <img src="{{ $item->variant->variantImage->image }}" alt="{{ $item->variant->variantImage->image }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        </td>
                        <td style="padding-left: 20px; vertical-align: middle;">
                            <p style="font-size: 18px; color: #2D3748; margin: 0 0 5px; font-weight: bold;">{{ $item->variant->product->name }}</p>
                            <p style="font-size: 16px; color: #718096; margin: 0 0 5px;">Phiên bản: {{ $item->variant->color }}/{{ $item->variant->size }}</p>
                            <p style="font-size: 16px; color: #4A5568; margin: 0;">{{ $item->quantity }} x <span style="color: #48BB78; font-weight: bold;">{{ number_format($item->price, 0, ',', '.') }}đ</span></p>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px; background: linear-gradient(135deg, #6B46C1 0%, #3B82F6 100%); text-align: center;">
                <p style="font-size: 24px; color: #ffffff; margin: 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">🎉 Tổng số sản phẩm: <strong>{{ $order->items->sum('quantity') }}</strong> 🎉</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px; text-align: center; background-color: #F7FAFC;">
                <p style="font-size: 18px; color: #4A5568; margin: 0 0 20px;">Cảm ơn bạn đã tin tưởng ThanShoes!</p>
                <a href="{{ $signedUrl }}" style="display: inline-block; padding: 12px 24px; background-color: #48BB78; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 5px; font-size: 16px; transition: background-color 0.3s ease;">Xem chi tiết đơn hàng</a>
            </td>
        </tr>
    </table>
</body>
</html>
