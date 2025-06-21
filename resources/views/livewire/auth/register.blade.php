<form wire:submit="register" class="space-y-6">
    <!-- Name Field -->
    <div>
        <label for="name" class="block text-sm font-semibold text-neutral-700 mb-2">
            Họ và tên
        </label>
        <input wire:model="name" 
               id="name" 
               name="name" 
               type="text" 
               autocomplete="name" 
               required
               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 bg-neutral-50 focus:bg-white"
               placeholder="Nhập họ và tên của bạn">
        @error('name') 
            <p class="mt-2 text-sm text-red-600 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Email Field -->
    <div>
        <label for="email" class="block text-sm font-semibold text-neutral-700 mb-2">
            Email <span class="text-neutral-500 text-xs">(tùy chọn nếu có số điện thoại)</span>
        </label>
        <input wire:model="email"
               id="email"
               name="email"
               type="email"
               autocomplete="email"
               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 bg-neutral-50 focus:bg-white"
               placeholder="Nhập địa chỉ email">
        @error('email')
            <p class="mt-2 text-sm text-red-600 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Phone Field -->
    <div>
        <label for="phone" class="block text-sm font-semibold text-neutral-700 mb-2">
            Số điện thoại <span class="text-neutral-500 text-xs">(tùy chọn nếu có email)</span>
        </label>
        <input wire:model="phone"
               id="phone"
               name="phone"
               type="tel"
               autocomplete="tel"
               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 bg-neutral-50 focus:bg-white"
               placeholder="Nhập số điện thoại">
        @error('phone')
            <p class="mt-2 text-sm text-red-600 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Password Field -->
    <div>
        <label for="password" class="block text-sm font-semibold text-neutral-700 mb-2">
            Mật khẩu
        </label>
        <input wire:model="password" 
               id="password" 
               name="password" 
               type="password" 
               autocomplete="new-password" 
               required
               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 bg-neutral-50 focus:bg-white"
               placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)">
        @error('password')
            <p class="mt-2 text-sm text-red-600 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Password Confirmation Field -->
    <div>
        <label for="password_confirmation" class="block text-sm font-semibold text-neutral-700 mb-2">
            Xác nhận mật khẩu
        </label>
        <input wire:model="password_confirmation" 
               id="password_confirmation" 
               name="password_confirmation" 
               type="password" 
               autocomplete="new-password" 
               required
               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 bg-neutral-50 focus:bg-white"
               placeholder="Nhập lại mật khẩu">
    </div>

    <!-- Validation Notice -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
            <div class="text-sm text-blue-700">
                <p class="font-semibold mb-1">Lưu ý:</p>
                <p>Bạn cần nhập ít nhất một trong hai: <strong>Email</strong> hoặc <strong>Số điện thoại</strong></p>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="pt-4">
        <button type="submit"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 shadow-soft hover:shadow-soft-lg"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed">
            <span wire:loading.remove class="flex items-center">
                <i class="fas fa-user-plus mr-2"></i>
                Đăng ký
            </span>
            <span wire:loading class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Đang xử lý...
            </span>
        </button>
    </div>
</form>
