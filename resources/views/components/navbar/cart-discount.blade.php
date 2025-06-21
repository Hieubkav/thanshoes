@if($discountApplied)
    <div class="cart-totals pt-2 mt-4 border-t border-gray-200">
        <div class="flex justify-between text-gray-600 line-through">
            <span>Tổng tiền gốc:</span>
            <span>{{ number_format($originalTotalAmount, 0, ',', '.') }}đ</span>
        </div>
        <div class="flex justify-between text-red-500">
            <span>Giảm giá:</span>
            <span>-{{ number_format($discountAmount, 0, ',', '.') }}đ ({{ number_format($discountPercentage, 2) }}%)</span>
        </div>
        <div class="flex justify-between font-medium text-lg">
            <span>Thanh toán:</span>
            <span>{{ number_format($totalAmount, 0, ',', '.') }}đ</span>
        </div>
    </div>
@else
    <div class="cart-totals pt-2 mt-4 border-t border-gray-200">
        <div class="flex justify-between font-medium text-lg">
            <span>Tổng tiền:</span>
            <span>{{ number_format($totalAmount, 0, ',', '.') }}đ</span>
        </div>
    </div>
@endif
