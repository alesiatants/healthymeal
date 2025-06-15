<nav x-data="{ open: false }"
    class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 font-winki text-xl">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @role(['admin', 'superadmin'])
                        <a href="{{ route('admin.users.index') }}"><img src="{{ asset('images/logo.png') }}" alt="logo"
                                class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" /></a>
                    @endrole
                    @if (!Auth::check() || Auth::user()->hasRole(['dietolog', 'user']))
                        <a href="{{ route('home') }}"><img src="{{ asset('images/logo.png') }}" alt="logo"
                                class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" /></a>
                    @endif


                </div>
                @role('superadmin')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-lg">
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                            {{ __('Администраторы') }}
                        </x-nav-link>
                    </div>
                @endrole
                @role('admin')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-lg">
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                            {{ __('Пользователи системы') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-lg">
                        <x-nav-link :href="route('admin.system.requests')" :active="request()->routeIs('admin.system.requests')">
                            {{ __('Работа системы') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-lg">
                        <x-nav-link :href="route('admin.applications.index')" :active="request()->routeIs('admin.applications.index')">
                            {{ __('Заявки') }}
                        </x-nav-link>
                    </div>
                @endrole


                @if (!Auth::check() || Auth::user()->hasRole(['dietolog', 'user']))
                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-lg">
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            {{ __('Поиск') }}
                        </x-nav-link>
                    </div>
                @endif


                @role('dietolog')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-lg">
                        <x-nav-link :href="route('recipes.showown')" :active="request()->routeIs('recipes.showown')">
                            {{ __('Рецепты') }}
                        </x-nav-link>
                    </div>
                @endrole
            </div>
            @if (Auth::check())

                <!-- Favorites Link -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    @role(['dietolog', 'user'])
                        <div class="flex items-center">
                            <a href="{{ route('recipes.showfavorites') }}"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-lg leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-red-500 dark:hover:text-red-400 focus:outline-none transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </a>
                        </div>
                    @endrole
                    <!-- Settings Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-lg leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Профиль') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Выйти') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @else
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-lg leading-normal">
                        Вход
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-lg leading-normal">
                            Регистрация
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        @if (!Auth::check() || Auth::user()->hasRole(['dietolog', 'user']))
            <div class="pt-2 space-y-1 text-lg">
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Поиск') }}
                </x-responsive-nav-link>
            </div>
        @endif
        @role('dietolog')
            <div class="pt-2 pb-1 space-y-1 text-lg">
                <x-responsive-nav-link :href="route('recipes.showown')" :active="request()->routeIs('recipes.showown')">
                    {{ __('Рецепты') }}
                </x-responsive-nav-link>
            </div>
        @endrole
        @if (Auth::check())
            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-lg text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Профиль') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            {{ __('Выйти') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-lg leading-normal">
                Войти
            </a>

            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-lg leading-normal">
                    Регистрация
                </a>
            @endif
        @endif
    </div>
</nav>
