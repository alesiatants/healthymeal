<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold font-winki text-gray-600 mb-6">{{ $productType->type }}</h1>

        @if ($categories->isNotEmpty())
            <div class="category-filter mb-8 flex flex-wrap gap-2">
                <a href="{{ route('products.index', $productType) }}"
                    class="px-4 py-2 rounded-full border {{ !request('category') ? 'bg-[#db2626] text-white border-[#db2626]' : 'bg-white text-gray-800 border-gray-300' }} hover:bg-[#db2626] hover:text-white transition-colors">
                    Все
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('products.index', [$productType, 'category' => $category->slug]) }}"
                        class="px-4 py-2 rounded-full border {{ request('category') == $category->slug ? 'bg-[#db2626] text-white border-[#db2626]' : 'bg-white text-gray-800 border-gray-300' }} hover:bg-[#db2626] hover:text-white transition-colors">
                        {{ $category->category }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($products as $product)
                <div class="bg-[#B0B7C6] rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="relative pb-[75%] overflow-hidden">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            class="absolute h-full w-full object-cover">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-3">{{ $product->name }}</h3>

                        <div class="flex flex-wrap gap-2 mb-3">
                            <!-- Калории -->
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-bolt mr-1"></i>
                                {{ $product->calories }} ккал
                            </span>

                            <!-- Белки -->
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-dumbbell mr-1"></i>
                                Б: {{ $product->protein }}г
                            </span>

                            <!-- Жиры -->
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-bacon mr-1"></i>
                                Ж: {{ $product->fat }}г
                            </span>

                            <!-- Углеводы -->
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-bread-slice mr-1"></i>
                                У: {{ $product->carbs }}г
                            </span>
                        </div>
                        <a href="{{ route('products.show', [
                            'productType' => $product->productType->slug,
                            'product' => $product->slug,
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
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">В этой категории пока нет продуктов</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
