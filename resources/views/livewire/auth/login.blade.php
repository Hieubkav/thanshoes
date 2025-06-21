<form wire:submit="login" class="space-y-6">
    <!-- Login Field (Email or Phone) -->
    <div>
        <label for="login_field" class="block text-sm font-semibold text-neutral-700 mb-2">
            Email hoặc Số điện thoại
        </label>
        <input wire:model="login_field"
               id="login_field"
               name="login_field"
               type="text"
               autocomplete="username"
               required
               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 bg-neutral-50 focus:bg-white"
               placeholder="Nhập email hoặc số điện thoại">
        @error('login_field')
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
               autocomplete="current-password"
               required
               class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 bg-neutral-50 focus:bg-white"
               placeholder="Nhập mật khẩu">
        @error('password')
            <p class="mt-2 text-sm text-red-600 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="flex items-center">
        <input wire:model="remember"
               id="remember"
               name="remember"
               type="checkbox"
               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-neutral-300 rounded transition-colors duration-200">
        <label for="remember" class="ml-3 block text-sm text-neutral-700">
            Ghi nhớ đăng nhập
        </label>
    </div>

    <!-- Submit Button -->
    <div class="pt-4">
        <button type="submit"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 shadow-soft hover:shadow-soft-lg"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed">
            <span wire:loading.remove class="flex items-center">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Đăng nhập
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