<!-- Logo -->
<a href="{{ route('shop.store_front') }}" class="flex items-center space-x-3">
    <img src="{{ asset('images/logo.svg') }}" class="h-16" alt="Logo"/>
    <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white hidden lg:block">
        {{ env('APP_NAME') }}
    </span>
</a>