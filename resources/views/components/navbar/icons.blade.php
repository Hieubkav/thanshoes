<!-- Icons (Search, User, Cart) -->
<div class="flex space-x-4">
    <!-- Search -->
    <div class="text-xl cursor-pointer">
        <i class="fa-solid fa-magnifying-glass" data-modal-target="search_modal"
           data-modal-toggle="search_modal"></i>
    </div>

    <!-- User -->
    <div class="text-xl cursor-pointer">
        <i class="fa-solid fa-user" data-dropdown-toggle="dropdown_user"></i>
        <div id="dropdown_user"
             class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                aria-labelledby="dropdownDefaultButton">
                <li>
                    <a href="#" data-modal-target="info_user_modal"
                       data-modal-toggle="info_user_modal"
                       class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                        Thông tin
                    </a>
                </li>
                <li>
                    <a href="#" data-modal-target="info_user_order" data-modal-toggle="info_user_order"
                       class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                        Đơn đặt
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Cart -->
    <div class="text-xl cursor-pointer">
        <i class="fa-solid fa-shopping-cart relative" data-drawer-target="drawer_cart"
           data-drawer-show="drawer_cart" data-drawer-backdrop="false" data-drawer-placement="right"
           aria-controls="drawer_cart" data-drawer-body-scrolling="true">
            <div
                class="absolute inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-orange-700 p-2 border-2 border-white rounded-full -top-3 -end-3 dark:border-gray-900">
                {{ $cartCount ?? 0 }}
            </div>
        </i>
    </div>
</div>