<x-app-layout>

    <div class="max-w-4xl mx-auto p-6 bg-[#B0B7C6] rounded-lg shadow-md text-[#111827] ">
        <h1 class="text-center text-4xl md:text-3xl  mb-8  font-winki">
            Калькулятор дневной нормы калорий
        </h1>
        <form action="{{ route('calculator.calculate') }}" method="POST">
            @csrf
            <div class="font-lato grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Левый столбец -->
                <div>
                    <!-- День рождения -->
                    <div class="mb-4">
                        <label for="birthday" class="block text-gray-700 ">День рождения</label>
                        <input type="date" name="birthday" id="birthday"
                            value="{{ auth()->user() ? auth()->user()->birth_date : old('birthday') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]"
                            required>
                    </div>

                    <!-- Вес -->
                    <div class="mb-4">
                        <label for="weight" class="block text-gray-700">Вес (кг)</label>
                        <input type="number" name="weight" id="weight" value="{{ old('weight', $currentPlan?->weight) }}" min="50"
                            max="150"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]"
                            required>
                    </div>

                    <!-- Пол -->
                    <div class="mb-4">
                        <label for="gender" class="block text-gray-700">Пол</label>
                        <select name="gender" id="gender"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]"
                            required>
                            <option value="male"
                                {{ (auth()->user() && auth()->user()->gender == 'Мужской') || old('gender') == 'male' ? 'selected' : '' }}>
                                Мужской</option>
                            <option value="female"
                                {{ (auth()->user() && auth()->user()->gender == 'Женский') || old('gender') == 'female' ? 'selected' : '' }}>
                                Женский</option>
                        </select>
                    </div>
                </div>

                <!-- Правый столбец -->
                <div>
                    <!-- Уровень активности -->
                    <div class="mb-4 relative">
                        <label for="activity_level" class="block text-gray-700">Уровень активности</label>
                        <select name="activity_level" id="activity_level"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]"
                            required onmouseover="showActivityTooltip(event)" onmouseout="hideActivityTooltip()">
                            <option value="Умственный" @selected(old('activity_level', $currentPlan?->activity_level) ==  'Умственный')>Умственный</option>
                            <option value="Лёгкий" @selected(old('activity_level', $currentPlan?->activity_level) ==  'Лёгкий')>Лёгкий</option>
                            <option value="Средний" @selected(old('activity_level', $currentPlan?->activity_level) ==  'Средний')>Средний</option>
                            <option value="Тяжёлый" @selected(old('activity_level', $currentPlan?->activity_level) ==  'Тяжёлый')>Тяжёлый</option>
                            <option value="Сверхтяжёлый" @selected(old('activity_level', $currentPlan?->activity_level) == 'Сверхтяжёлый')>Сверхтяжёлый</option>
                        </select>

                        <!-- Контейнер для кастомной подсказки -->
                        <div id="activityTooltip"
                            class="hidden absolute z-10 p-3 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg text-sm text-gray-600">
                            <p id="tooltipContent"></p>
                        </div>
                    </div>
                    <!-- Цель -->
                    <div class="mb-4">
                        <label for="goal" class="block text-gray-700">Цель</label>
                        <select name="goal" id="goal"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]"
                            required>
                            <option value="Набрать вес" @selected(old('goal', $currentPlan?->goal) == 'Набрать вес')>Набрать вес</option>
                            <option value="Сбросить вес" @selected(old('goal', $currentPlan?->goal) == 'Сбросить вес')>Сбросить вес</option>
                            <option value="Поддержать вес" @selected(old('goal', $currentPlan?->goal) == 'Поддержать вес')>Поддержать вес
                            </option>
                        </select>
                    </div>

                    <!-- Количество приемов пищи -->
                    <div class="mb-4">
                        <label for="meals_per_day" class="block text-gray-700">Количество приемов пищи</label>
                        <select name="meals_per_day" id="meals_per_day"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]"
                            required>
                            <option value="3" @selected(old('meals_per_day', $currentPlan?->meals_per_day) == 3)>Трехразовое</option>
                            <option value="4" @selected(old('meals_per_day', $currentPlan?->meals_per_day) == 4)>Четырехразовое</option>
                            <option value="5" @selected(old('meals_per_day', $currentPlan?->meals_per_day) == 5)>Пятиразовое</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Кнопка отправки -->
            <div class="mt-6">
                <button type="submit"
                    class="w-full bg-[#db2626] text-white px-4 py-2 rounded-md hover:bg-[#c52222]">Рассчитать</button>
            </div>
        </form>

        @if (session('results.weeklyPlan'))
            <!-- В секции content -->
            <div class="mt-8 space-y-8">

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-center text-4xl md:text-3xl font-winki  mb-4">Распределение калорий по дням недели
                    </h2>
                    <div class="h-100">
                        <canvas id="combinedChart" class="font-winki"></canvas>
                    </div>
                </div>

            </div>
        @endif
        @if (session('results.todayPlan'))
            @php
                $todayPlan = session('results.todayPlan');
            @endphp
            <h1 class="text-3xl font-bold text-gray-800 mb-8">План питания на сегодня</h1>

            @foreach ($todayPlan as $mealType => $meal)
                <div class="mb-12">
                    {{-- Заголовок приема пищи --}}
                    <div class="flex items-center mb-6">
                        @switch($mealType)
                            @case('завтрак')
                                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-coffee text-amber-500 text-lg"></i>
                                </div>
                            @break

                            @case('обед')
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-utensils text-red-500 text-lg"></i>
                                </div>
                            @break

                            @case('ужин')
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-moon text-blue-500 text-lg"></i>
                                </div>
                            @break

                            @case('перекус')
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-apple-alt text-green-500 text-lg"></i>
                                </div>
                            @break

                            @case('полдник')
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-mug-hot text-purple-500 text-lg"></i>
                                </div>
                            @break
                        @endswitch

                        <h2 class="text-2xl font-semibold text-gray-800 capitalize">
                            {{ $mealType }}
                        </h2>
                    </div>

                    {{-- Карточки рецептов --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($meal as $recipe)
                            <div
                                class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                {{-- Изображение рецепта --}}
                                <div class="h-48 overflow-hidden relative">
                                    <img src="{{ asset('storage/' . $recipe->image) }}" alt="{{ $recipe->name }}"
                                        class="w-full h-full object-cover">
                                    <span
                                        class="absolute top-4 right-4 bg-black bg-opacity-70 text-white px-3 py-1 rounded-full text-sm">
                                        {{ $recipe->type->type }}
                                    </span>
                                </div>
                                {{-- Тело карточки --}}
                                <div class="p-6">
                                    <div class="flex items-center mb-3">
                                        <h3 class="text-xl font-bold text-gray-800">{{ $recipe->name }}</h3>

                                    </div>

                                    {{-- Мета-информация --}}
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <!-- Калории -->
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-200 text-red-800">
                                            <i class="fas fa-bolt mr-1"></i>
                                            {{ $recipe->calories }} ккал
                                        </span>

                                        <!-- Белки -->
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-200 text-blue-800">
                                            <i class="fas fa-dumbbell mr-1"></i>
                                            Б: {{ $recipe->protein }}г
                                        </span>

                                        <!-- Жиры -->
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-bacon mr-1"></i>
                                            Ж: {{ $recipe->fat }}г
                                        </span>

                                        <!-- Углеводы -->
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-200 text-green-800">
                                            <i class="fas fa-bread-slice mr-1"></i>
                                            У: {{ $recipe->carbs }}г
                                        </span>
                                    </div>
                                    <a href="{{ route('recipes.show', ['recipeType' => $recipe->type->slug, 'recipe' => $recipe->slug]) }}"
                                        class="mt-3 relative inline-flex items-center justify-center w-full px-6 py-2.5 font-medium overflow-hidden group">
                                        <!-- Основной текст и иконка (видимые всегда) -->
                                        <span
                                            class="relative z-10 flex items-center transition-all duration-300 ease-in-out text-[#db2626] group-hover:text-white">
                                            Подробнее
                                            <svg class="w-4 h-4 ml-2 transition-transform duration-300 group-hover:translate-x-1"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </span>

                                        <!-- Фоновая анимация заполнения -->
                                        <span
                                            class="absolute inset-0 w-0 bg-[#db2626] transition-all duration-500 ease-in-out group-hover:w-full"></span>

                                        <!-- Белая граница -->
                                        <span
                                            class="absolute inset-0 border border-[#db2626] rounded-lg group-hover:border-transparent"></span>
                                    </a>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @vite(['resources/js/calculator-charts.js'])

    </div>

</x-app-layout>
