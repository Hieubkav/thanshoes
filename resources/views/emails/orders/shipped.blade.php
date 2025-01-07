<h1>Đơn hàng mới #{{ $order->id }}</h1>
<p>Tổng giá trị: {{ $order->order_items->sum('price') }}</p>
<p>Khách hàng: {{ $order->customer->name }} - {{ $order->customer->phone }}</p>
