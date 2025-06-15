<x-admin-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 bg-gray-50 border-b">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Фильтр по ролям -->
                    <div>
                        <x-light-input-label for="filter_roles" value="Роли" />
                        <select name="filter[roles][]" id="filter_roles" multiple
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent sm:text-sm">
                            <option value="admin"
                                {{ in_array('admin', request('filter.roles', [])) ? 'selected' : '' }}>Администратор
                            </option>
                            <option value="dietolog"
                                {{ in_array('dietolog', request('filter.roles', [])) ? 'selected' : '' }}>Диетолог
                            </option>
                            <option value="user"
                                {{ in_array('user', request('filter.roles', [])) ? 'selected' : '' }}>Пользователь
                            </option>
                        </select>
                    </div>

                    <!-- Фильтр по полу -->
                    <div>
                        <x-light-input-label for="filter[gender]" value="Пол" />
                        <select name="filter[gender]" id="filter[gender]"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent sm:text-sm">
                            <option value="">Все</option>
                            <option value="Мужской" {{ request('filter.gender') == 'Мужской' ? 'selected' : '' }}>
                                Мужской</option>
                            <option value="Женский" {{ request('filter.gender') == 'Женский' ? 'selected' : '' }}>
                                Женский</option>
                        </select>
                    </div>
                    <!-- Фильтр по активности -->
                    <div>
                        <x-light-input-label for="filter[active]" value="Активность" />
                        <select name="filter[active]" id="filter[active]"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent sm:text-sm">
                            <option value="">Все</option>
                            <option value="1" {{ request('filter.active') == '1' ? 'selected' : '' }}>Активные
                            </option>
                            <option value="0" {{ request('filter.active') == '0' ? 'selected' : '' }}>
                                Деактивированные</option>
                        </select>
                    </div>

                    <!-- Поиск -->
                    <div>
                        <x-light-input-label for="filter[search]" value="Поиск" />
                        <input id="filter[search]" name="filter[search]" :value="request('filter.search')"
                            placeholder="Имя, email или телефон"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent" />
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Кнопка "Применить" -->
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-[#db2626] hover:bg-[#db2622] text-white rounded-md transition-colors duration-200">
                            <i class="fas fa-filter text-sm"></i>
                            <span class="text-sm font-medium">Применить</span>
                        </button>

                        <!-- Кнопка "Сбросить" -->
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center gap-2 px-4 py-2 bg-gray-500 text-white hover:bg-gray-600 rounded-md transition-colors duration-200 border border-gray-300">
                            <i class="fas fa-undo-alt text-sm"></i>
                            <span class="text-sm font-medium">Сбросить</span>
                        </a>
                    </div>
                </form>
            </div>

            <div class="p-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <!-- ФИО -->
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate max-w-[120px]">
                                <div class="flex items-center">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'name',
                                        'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="flex items-center">
                                        <span class="truncate">ФИО</span>
                                        @if (request('sort') == 'name')
                                            @if (request('direction') == 'asc')
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </a>
                                </div>
                            </th>

                            <!-- Почта -->
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate max-w-[120px]">
                                <div class="flex items-center">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'email',
                                        'direction' => request('sort') == 'email' && request('direction') == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="flex items-center">
                                        <span class="truncate">Почта</span>
                                        @if (request('sort') == 'email')
                                            @if (request('direction') == 'asc')
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </a>
                                </div>
                            </th>

                            <!-- Дата регистрации -->
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate max-w-[100px]">
                                <div class="flex items-center">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'created_at',
                                        'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="flex items-center">
                                        <span class="truncate">Дата регистр.</span>
                                        @if (request('sort') == 'created_at')
                                            @if (request('direction') == 'asc')
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </a>
                                </div>
                            </th>

                            <!-- Телефон -->
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate max-w-[100px]">
                                <div class="flex items-center">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'phone',
                                        'direction' => request('sort') == 'phone' && request('direction') == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="flex items-center">
                                        <span class="truncate">Телефон </span>
                                        @if (request('sort') == 'phone')
                                            @if (request('direction') == 'asc')
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </a>
                                </div>
                            </th>

                            <!-- Пол -->
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate max-w-[80px]">
                                <div class="flex items-center">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'gender',
                                        'direction' => request('sort') == 'gender' && request('direction') == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="flex items-center">
                                        <span class="truncate">Пол</span>
                                        @if (request('sort') == 'gender')
                                            @if (request('direction') == 'asc')
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </a>
                                </div>
                            </th>

                            <!-- День рождения -->
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate max-w-[80px]">
                                <div class="flex items-center">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'birth_date',
                                        'direction' => request('sort') == 'birth_date' && request('direction') == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="flex items-center">
                                        <span class="truncate">Дата рожд.</span>
                                        @if (request('sort') == 'birth_date')
                                            @if (request('direction') == 'asc')
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="ml-1 h-4 w-4 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @else
                                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </a>
                                </div>
                            </th>

                            <!-- Роль -->
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate max-w-[100px]">
                                <span class="truncate">Роль</span>
                            </th>

                            <!-- Действия -->
                            <th
                                class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate max-w-[80px]">
                                <span class="truncate">Действия</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr class="user-item @if (!$user->active) bg-red-200  @else bg-green-200 @endif"
                                data-user-id="{{ $user->id }}">
                                <!-- ФИО -->
                                <td class="px-2 py-3 text-sm text-gray-500 truncate max-w-[120px] form-group">
                                    @if (
                                        $user->hasRole('user') or
                                            $user->hasRole('superadmin') or
                                            !$user->active or
                                            auth()->user()->hasRole('admin') and !$user->hasRole('dietolog'))
                                        {{ $user->name }}
                                    @else
                                        <input
                                            class="w-full border rounded px-1 py-1 text-xs truncate focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent"
                                            data-field='name' type="text" name="name"
                                            value="{{ $user->name }}" />
                                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
                                    @endif
                                </td>

                                <!-- Почта -->
                                <td class="px-2 py-3 text-sm text-gray-500 truncate max-w-[120px] form-group">
                                    @if (
                                        $user->hasRole('user') or
                                            $user->hasRole('superadmin') or
                                            !$user->active or
                                            auth()->user()->hasRole('admin') and !$user->hasRole('dietolog'))
                                        {{ $user->email }}
                                    @else
                                        <input
                                            class="w-full border rounded px-1 py-1 text-xs truncate focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent"
                                            data-field='email' type="text" name="email"
                                            value="{{ $user->email }}" />
                                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                                    @endif
                                </td>

                                <!-- Дата регистрации -->
                                <td class="px-2 py-3 text-sm text-gray-500 truncate max-w-[100px]">
                                    {{ $user->created_at->format('d.m.Y') }}
                                </td>

                                <!-- Телефон -->
                                <td class="px-2 py-3 text-sm text-gray-500 truncate max-w-[100px] form-group">
                                    @if (
                                        $user->hasRole('user') or
                                            $user->hasRole('superadmin') or
                                            !$user->active or
                                            auth()->user()->hasRole('admin') and !$user->hasRole('dietolog'))
                                        {{ $user->phone }}
                                    @else
                                        <input
                                            class="w-full border rounded px-1 py-1 text-xs truncate focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent"
                                            type="tel" name="phone" id="phone" data-field='phone'
                                            value="{{ $user->phone }}" />
                                        <x-input-error :messages="$errors->get('phone')" class="mt-1 text-xs" />
                                    @endif
                                </td>

                                <!-- Пол -->
                                <td class="px-2 py-3 text-sm text-gray-500 truncate max-w-[80px]">
                                    {{ $user->gender }}
                                </td>
                                <!-- Дата рождения -->
                                <td class="px-2 py-3 text-sm text-gray-500 truncate max-w-[80px]">
                                    {{ $user->birth_date }}
                                </td>

                                <!-- Роль -->
                                <td class="px-2 py-3 text-sm text-gray-500 truncate max-w-[100px]">

                                    @if ($user->hasRole('superadmin'))
                                        Суперадмин
                                    @elseif($user->hasRole('admin'))
                                        Администратор
                                    @elseif($user->hasRole('dietolog'))
                                        Диетолог
                                    @else
                                        Пользователь
                                    @endif
                                </td>

                                <!-- Действия -->
                                <td class="px-2 py-3 text-sm text-gray-500 truncate max-w-[80px]">
                                    <div class="flex gap-2">
                                        @if (
                                            $user->hasRole('user') or
                                                $user->hasRole('superadmin') or
                                                auth()->user()->hasRole('admin') and !$user->hasRole('dietolog'))
                                        @else
                                            <button
                                                class="edit-user-btn text-green-500 hover:text-green-700 transition-colors duration-200 text-sm @if (!$user->active) hidden @else @endif"
                                                title="Редактировать">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button
                                                class="deacivate-user-btn text-red-500 hover:text-red-700 transition-colors duration-200 @if (!$user->active) hidden @else @endif"
                                                title="Деактивировать">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                            <button
                                                class="acivate-user-btn text-green-500 hover:text-green-700 transition-colors duration-200 @if ($user->active) hidden @else @endif"
                                                title="Активировать">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="user-success" class="mt-2 p-2 bg-green-100 text-green-700 rounded hidden"></div>
            <div class="px-4">
                <button id="add-user-btn" data-user-issuper = "{{ auth()->user()->hasRole('superadmin') }}"
                    class="add-user-btn px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Добавить пользователя
                </button>
            </div>
            <div class="p-4 bg-gray-50 border-t">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @include('components.error_modal')
    @yield('errormodal')
    @vite(['resources/js/cruduser.js'])

</x-admin-layout>
