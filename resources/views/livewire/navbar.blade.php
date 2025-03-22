<div>
    <nav class="bg-white border-gray-200 dark:border-gray-600 dark:bg-gray-900 fixed top-0 right-0 left-0 shadow-md z-50">
        <!-- Topbar -->
        @include('component.topbar')

        <!-- Container -->
        <div class="flex items-center justify-between mx-auto max-w-screen-xl p-4">
            <!-- Include components -->
            @include('components.navbar.logo')
            @include('components.navbar.navigation')
            @include('components.navbar.icons')
        </div>
    </nav>

    <!-- Include drawer and modals -->
    @include('components.navbar.cart-drawer')
    @include('components.navbar.user-info-modal')
    @include('components.navbar.order-history-modal')
</div>
