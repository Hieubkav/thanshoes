<!-- Logo -->
<a href="{{ route('shop.store_front') }}" class="flex items-center space-x-3 group">
    <div class="relative">
        <img src="{{ asset('images/logo.svg') }}" loading="lazy" class="h-14 w-auto transition-transform duration-200 group-hover:scale-105" alt="Logo"/>
        <div class="absolute inset-0 bg-primary-500/10 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 -z-10"></div>
    </div>
    <span class="self-center text-2xl font-bold whitespace-nowrap text-neutral-900 dark:text-white hidden lg:block tracking-tight">
        {{ env('APP_NAME') }}
    </span>
</a>