<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        @if ($recipes->isNotEmpty())
            <div class="text-center pb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
                    <span class="text-[#db2626]">❤️</span> Мои избранные рецепты
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Здесь собраны все рецепты, которые вы сохранили
                </p>
            </div>
        @endif

        <!-- Список избранных рецептов пользователя -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($recipes as $recipe)
                <div
                    class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Карточка рецепта -->
                    <div class="relative">
                        <a
                            href="{{ route('recipes.show', ['recipeType' => $recipe->type->slug, 'recipe' => $recipe->slug]) }}">
                            <img src="{{ asset('storage/' . $recipe->image) }}" alt="{{ $recipe->name }}"
                                class="w-full h-48 object-cover">
                        </a>
                        <span
                            class="absolute top-4 right-4 bg-black bg-opacity-70 text-white px-3 py-1 rounded-full text-sm">
                            {{ $recipe->type->type }}
                        </span>
                    </div>

                    <!-- Содержимое карточки -->
                    <div class="p-4">
                        <a href="{{ route('recipes.show', ['recipeType' => $recipe->type->slug, 'recipe' => $recipe->slug]) }}"
                            class="block text-xl font-bold text-gray-800 hover:text-[#db2626] mb-2">
                            {{ $recipe->name }}
                        </a>

                        <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                            <span>
                                <i class="far fa-clock mr-1"></i>
                                {{ $recipe->prep_time }} мин
                            </span>
                            <span>
                                <i class="fas fa-signal mr-1"></i>
                                {{ $recipe->difficulty }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="{{ asset('storage/' . $recipe->author->avatar) }}"
                                    alt="{{ $recipe->author->name }}" class="w-8 h-8 rounded-full mr-2">
                                <span class="text-sm">{{ $recipe->author->name }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm">
                                    <i class="far fa-heart mr-1"></i>
                                    {{ $recipe->favorites_count }}
                                </span>
                                <span class="text-sm">
                                    <i class="far fa-comment mr-1"></i>
                                    {{ $recipe->comments_count }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Сообщение, если нет избранных рецептов -->
                <div class="col-span-full text-center py-12">
                    <div class="mb-8 flex justify-center">
                        <i class="far fa-heart text-[#db2626] text-6xl opacity-50"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
                        Ваш список избранного пуст
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        Сохраняйте понравившиеся рецепты, нажимая на значок сердца, и они появятся здесь.
                    </p>
                    <div class="flex justify-center">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center px-6 py-3 bg-[#db2626] text-white font-medium rounded-lg hover:bg-red-700 transition-colors shadow-md hover:shadow-lg">
                            <i class="fas fa-utensils mr-2"></i> Найти рецепты
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
