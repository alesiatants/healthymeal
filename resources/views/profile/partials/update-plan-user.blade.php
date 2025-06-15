<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Информация по плану питания') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Обновите данные Вашего плана.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update.plan') }}" class="mt-6 space-y-6" id="planForm">
        @csrf

        <!-- Уведомления -->
        <div id="planNotifications"></div>
       @if (session('status') === 'profile-updated')
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 10000)"
                class="mt-4 p-4 rounded-lg 
                    @if (session('notification.type') === 'success')
bg-green-50 text-green-800 border border-green-200
                    @elseif(session('notification.type') === 'warning')
bg-yellow-50 text-yellow-800 border border-yellow-200
                    @endif">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if (session('notification.type') === 'success')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        @elseif (session('notification.type') === 'warning')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        @endif
                    </svg>
                    <div>
                        <p>{{ session('notification.message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div>
            <x-input-label for="weight" :value="__('Текущий вес (кг)')" />
            <x-text-input id="weight" class="w-full" name="weight" type="number" min="50" max="150"
                step="0.1" :value="old('weight', $currentPlan?->weight)" required />
            <x-input-error class="mt-2" :messages="$errors->get('weight')" />
        </div>

        <div>
            <x-input-label for="goal" :value="__('Цель')" />
            <select
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-full"
                id="goal" name="goal" class="mt-1 block w-full" required>
                <option value="Набрать вес" @selected(old('goal', $currentPlan?->goal) == 'Набрать вес')>Набрать вес</option>
                <option value="Сбросить вес" @selected(old('goal', $currentPlan?->goal) == 'Сбросить вес')>Сбросить вес</option>
                <option value="Поддержать вес" @selected(old('goal', $currentPlan?->goal) == 'Поддержать вес')>Поддержать вес</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('goal')" />
        </div>

        <div class="relative">
            <x-input-label for="activity_level" :value="__('Уровень активности')" />
            <select
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-full"
                id="activity_level" name="activity_level" class="mt-1 block w-full" required
                onmouseover="showActivityTooltip(event)" onmouseout="hideActivityTooltip()">
                <option value="Умственный" @selected(old('activity_level', $currentPlan?->activity_level) == 'Умственный')>Умственный</option>
                <option value="Лёгкий" @selected(old('activity_level', $currentPlan?->activity_level) == 'Лёгкий')>Лёгкий</option>
                <option value="Средний" @selected(old('activity_level', $currentPlan?->activity_level) == 'Средний')>Средний</option>
                <option value="Тяжёлый" @selected(old('activity_level', $currentPlan?->activity_level) == 'Тяжёлый')>Тяжёлый</option>
                <option value="Сверхтяжёлый" @selected(old('activity_level', $currentPlan?->activity_level) == 'Сверхтяжёлый')>Сверхтяжёлый</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('activity_level')" />
            <div id="activityTooltip"
                class="hidden absolute z-10 p-3 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg text-sm text-gray-600">
                <p id="tooltipContent"></p>
            </div>
        </div>

        <div>
            <x-input-label for="meals_per_day" :value="__('Приемов пищи в день')" />
            <select
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-full"
                id="meals_per_day" name="meals_per_day" class="mt-1 block w-full" required>
                <option value="3" @selected(old('meals_per_day', $currentPlan?->meals_per_day) == 3)>Трехразовое</option>
                <option value="4" @selected(old('meals_per_day', $currentPlan?->meals_per_day) == 4)>Четырехразовое</option>
                <option value="5" @selected(old('meals_per_day', $currentPlan?->meals_per_day) == 5)>Пятиразовое</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('meals_per_day')" />
        </div>



        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Сохранить') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Сохранено.') }}</p>
            @endif
        </div>
    </form>

    <!-- График прогресса -->
    <div class="mt-8">
        @if (count($plans) === 0)
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Укажите Ваши актуальные данные по плану питания для отслеживания прогресса') }}</h3>
        @else
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Ваш прогресс при текущем плане ') }} <span class=" items-center gap-2 px-5 py-1.5 rounded-full text-sm shadow
                bg-gradient-to-r from-blue-400 to-blue-200 text-blue-800 border border-blue-200"> {{$currentPlan?->goal}} </span> </h3>
            <div class="bg-[#fdf5e6] p-4 rounded-lg shadow">
                <canvas id="weightProgressChart" height="300"></canvas>
            </div>
        @endif
    </div>
    @vite(['resources/js/progresschart.js'])
    <script>
        // Данные для графика (извлекаются из контроллера)
        const planData = @json($plans);
        const currentPlan = @json($currentPlan);
        const weightData = planData.map(plan => plan.weight);
        const dateLabels = planData.map(plan => new Date(plan.created_at).toLocaleDateString());
        const goals = planData.map(plan => plan.goal);
        const activities = planData.map(plan => plan.activity_level);
        const meals = planData.map(plan => plan.meals_per_day);
        // Проверка давности последнего обновления
        const lastUpdate = new Date(currentPlan.created_at);
        const daysSinceUpdate = Math.floor((new Date() - lastUpdate) / (1000 * 60 * 60 * 24));
    </script>
</section>
