@forelse($recipes as $recipe)
<div class="bg-[#B0B7C6] rounded-lg shadow overflow-hidden">
    <div class="relative pb-[75%] overflow-hidden">
        <img src="{{ asset('storage/' . $recipe->image) }}" alt="{{ $recipe->name }}"
            class="absolute h-full w-full object-cover">
    </div>
    <div class="p-6">

        <h3 class="text-xl font-bold mb-2 text-center">{{ $recipe->name }}</h3>


        <!-- Рейтинг -->
        <div class="flex justify-center mb-2">
            <div class="flex items-center">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="fa-star text-xl {{ $i <= $recipe->averageRating() ? 'fas text-yellow-400' : 'far' }}"
                        data-rating="{{ $i }}"></i>
                @endfor
                <span class="ml-1 text-gray-600">({{ $recipe->scores_count }})</span>
            </div>
        </div>

        <!-- Другие данные -->
        <div class="text-gray-600 mb-2 flex gap-2">
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-200 text-green-800">
                <i class="fas fa-clock mr-1"></i>
                Время: {{ $recipe->prep_time }} мин
            </span>

            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-300 text-purple-800">
                @if ($recipe->difficulty == 'Легкий')
                    <i class="fas fa-thermometer-quarter mr-1"></i>
                @elseif($recipe->difficulty == 'Средний')
                    <i class="fas fa-thermometer-half mr-1"></i>
                @else
                    <i class="fas fa-thermometer-full mr-1"></i>
                @endif
                Сложность: {{ $difficulties[$recipe->difficulty] ?? $recipe->difficulty }}
            </span>
        </div>


        <p class="text-gray-700">{{ Str::limit($recipe->description, 100) }}</p>
        <a href="{{ route('recipes.show', [
            'recipeType' => $recipe->type->slug,
            'recipe' => $recipe->slug,
        ]) }}"
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
@empty
<div class="col-span-3 text-center py-8 text-gray-500">
    Рецепты не найдены. Попробуйте изменить параметры фильтрации.
</div>
@endforelse