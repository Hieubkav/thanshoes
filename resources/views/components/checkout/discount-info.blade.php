@if($discountApplied)
    <div class="bg-gradient-to-r from-warning-50 to-primary-50 border border-warning-200 rounded-xl p-5 mb-6">
        <div class="flex items-center mb-4">
            <div class="w-8 h-8 bg-warning-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-percentage text-white text-sm"></i>
            </div>
            <h3 class="text-lg font-semibold text-neutral-900">Thông tin giảm giá</h3>
        </div>

        <div class="space-y-3">
            <div class="flex justify-between items-center py-2">
                <span class="text-neutral-600 font-medium">Tổng tiền gốc:</span>
                <span class="text-neutral-700 line-through font-semibold">{{ number_format($originalTotal, 0, ',', '.') }}đ</span>
            </div>

            <div class="flex justify-between items-center py-2 bg-warning-100/50 rounded-lg px-3">
                <div class="flex items-center">
                    <i class="fas fa-tag text-warning-600 mr-2"></i>
                    <span class="text-warning-700 font-medium">Giảm giá:</span>
                </div>
                <span class="text-warning-700 font-bold">
                    -{{ number_format($discountAmount, 0, ',', '.') }}đ
                    @if($discountType == 'percent')
                        <span class="chip chip-warning text-xs ml-2">{{ number_format($discountPercentage, 2) }}%</span>
                    @endif
                </span>
            </div>

            <div class="flex justify-between items-center pt-4 border-t-2 border-primary-200">
                <span class="text-lg font-bold text-neutral-900">Số tiền cần thanh toán:</span>
                <span class="text-2xl font-bold text-primary-600">{{ number_format($total, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </div>
@else
    <div class="bg-neutral-50 border border-neutral-200 rounded-xl p-5 mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-neutral-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-calculator text-white text-sm"></i>
                </div>
                <span class="text-lg font-bold text-neutral-900">Tổng tiền:</span>
            </div>
            <span class="text-2xl font-bold text-primary-600">{{ number_format($total, 0, ',', '.') }}đ</span>
        </div>
    </div>
@endif
