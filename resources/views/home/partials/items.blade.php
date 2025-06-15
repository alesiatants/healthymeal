@foreach ($items as $item)
    @if ($item->item_type == 'recipe')
        <div class="bg-[#B0B7C6] rounded-lg shadow overflow-hidden">
            <div class="relative pb-[75%] overflow-hidden">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                    class="absolute h-full w-full object-cover">
            </div>
            <div class="p-4">

                <h3 class="font-semibold text-lg mb-3">{{ $item->name }}</h3>


                <div class="flex flex-wrap gap-2 mb-3">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $item->prep_time }} мин
                    </span>

                    <!-- Белки -->
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        @if ($item->difficulty == 'Легкий')
                            <i class="fas fa-thermometer-quarter mr-1"></i>
                        @elseif($item->difficulty == 'Средний')
                            <i class="fas fa-thermometer-half mr-1"></i>
                        @else
                            <i class="fas fa-thermometer-full mr-1"></i>
                        @endif
                        {{ $item->difficulty }}
                    </span>
                    <!-- Калории -->
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-bolt mr-1"></i>
                        {{ $item->calories }} ккал
                    </span>

                    <!-- Белки -->
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-dumbbell mr-1"></i>
                        Б: {{ $item->protein }}г
                    </span>

                    <!-- Жиры -->
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-bacon mr-1"></i>
                        Ж: {{ $item->fat }}г
                    </span>

                    <!-- Углеводы -->
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-bread-slice mr-1"></i>
                        У: {{ $item->carbs }}г
                    </span>
                </div>
                <a href="{{ route('recipes.show', [
                    'recipeType' => $item->type_slug,
                    'recipe' => $item->slug,
                ]) }}"
                    class="mt-3 relative inline-flex items-center justify-center w-full px-6 py-2.5 font-medium overflow-hidden group">
                    <!-- Основной текст и иконка (видимые всегда) -->
                    <span
                        class="relative z-10 flex items-center transition-all duration-300 ease-in-out text-[#db2626] group-hover:text-white">
                        Подробнее
                        <svg class="w-4 h-4 ml-2 transition-transform duration-300 group-hover:translate-x-1"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
    @else
        <div class="bg-[#B0B7C6] rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="relative pb-[75%] overflow-hidden">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                    class="absolute h-full w-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg mb-3">{{ $item->name }}</h3>

                <div class="flex flex-wrap gap-2 mb-3">
                    <!-- Калории -->
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-bolt mr-1"></i>
                        {{ $item->calories }} ккал
                    </span>

                    <!-- Белки -->
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-dumbbell mr-1"></i>
                        Б: {{ $item->protein }}г
                    </span>

                    <!-- Жиры -->
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-bacon mr-1"></i>
                        Ж: {{ $item->fat }}г
                    </span>

                    <!-- Углеводы -->
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-bread-slice mr-1"></i>
                        У: {{ $item->carbs }}г
                    </span>
                </div>
                <a href="{{ route('products.show', [
                    'productType' => $item->type_slug,
                    'product' => $item->slug,
                ]) }}"
                    class="mt-3 relative inline-flex items-center justify-center w-full px-6 py-2.5 font-medium overflow-hidden group">
                    <!-- Основной текст и иконка (видимые всегда) -->
                    <span
                        class="relative z-10 flex items-center transition-all duration-300 ease-in-out text-[#db2626] group-hover:text-white">
                        Подробнее
                        <svg class="w-4 h-4 ml-2 transition-transform duration-300 group-hover:translate-x-1"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
    @endif
@endforeach
