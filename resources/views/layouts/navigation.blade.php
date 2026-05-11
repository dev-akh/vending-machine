<nav class="gradient-bg shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('products.index') }}" class="text-white text-xl font-bold flex items-center">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Vending Machine
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:block ml-10">
                    <div class="flex items-baseline space-x-4">
                        @auth
                            <a href="{{ route('products.index') }}"
                                class="{{ request()->routeIs('products.*') ? 'bg-white bg-opacity-20' : '' }} text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20 transition duration-300">
                                <i class="fas fa-box mr-1"></i>Products
                            </a>
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                    class="{{ request()->routeIs('admin.*') ? 'bg-white bg-opacity-20' : '' }} text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20 transition duration-300">
                                    <i class="fas fa-tachometer-alt mr-1"></i>Admin
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Right side items -->
            <div class="flex items-center">
                @guest
                    <a href="{{ route('login') }}"
                        class="text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-1"></i>Login
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="ml-2 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20 transition duration-300">
                            <i class="fas fa-user-plus mr-1"></i>Register
                        </a>
                    @endif
                @else
                    <div class="relative">
                        <button onclick="toggleDropdown()"
                            class="text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20 transition duration-300 flex items-center">
                            <i class="fas fa-user mr-1"></i>
                            {{ Auth::user()->name }}
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>

                        <div id="userDropdown"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                <div class="font-medium">{{ Auth::user()->name }}</div>
                                <div class="text-gray-500">{{ Auth::user()->email }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    @if (Auth::user()->isAdmin())
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Admin</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">User</span>
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const button = event.target.closest('button[onclick="toggleDropdown()"]');

        if (!button && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
