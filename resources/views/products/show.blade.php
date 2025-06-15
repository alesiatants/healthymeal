<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">


            <!-- Основная карточка продукта -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <div class="md:flex">
                    <!-- Изображение продукта -->
                    <div class="md:w-2/4 relative overflow-hidden group">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute top-4 right-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-white text-[#db2626] shadow-md">
                                <i class="fas fa-fire mr-1"></i> {{ $product->calories }} ккал
                            </span>
                        </div>
                    </div>

                    <!-- Информация о продукте -->
                    <div class="md:w-2/4 p-8">
                        <div class="flex justify-between items-start mb-2">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                        </div>

                        <!-- Селектор веса -->
                        <div class="mb-6">
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Выберите
                                вес:</label>
                            <div class="relative">
                                <select id="weight"
                                    class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-[#db2626] focus:border-[#db2626] transition-all duration-200 bg-white">
                                    <option value="50">50 г</option>
                                    <option value="100" selected>100 г</option>
                                    <option value="150">150 г</option>
                                    <option value="200">200 г</option>
                                </select>
                            </div>
                        </div>

                        <!-- Динамические значения БЖУ с иконками -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg transition-all duration-300 hover:bg-gray-100 nutrition-card"
                                data-type="calories">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-fire text-[#db2626] mr-2"></i>
                                    <p class="text-sm text-[#db2626]">Калории</p>
                                </div>
                                <p class="text-2xl font-bold text-[#db2626]"><span
                                        id="calories">{{ $product->calories }}</span> <span class="text-lg">ккал</span>
                                </p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg transition-all duration-300 hover:bg-gray-100 nutrition-card"
                                data-type="protein">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-dumbbell text-blue-600 mr-2"></i>
                                    <p class="text-sm text-blue-600">Белки</p>
                                </div>
                                <p class="text-2xl font-bold text-blue-600"><span
                                        id="protein">{{ $product->protein }}</span> <span class="text-lg">г</span></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg transition-all duration-300 hover:bg-gray-100 nutrition-card"
                                data-type="fat">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-bacon text-yellow-600 mr-2"></i>
                                    <p class="text-sm text-yellow-600">Жиры</p>
                                </div>
                                <p class="text-2xl font-bold text-yellow-600"><span
                                        id="fat">{{ $product->fat }}</span> <span class="text-lg">г</span></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg transition-all duration-300 hover:bg-gray-100 nutrition-card"
                                data-type="carbs">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-bread-slice text-green-600 mr-2"></i>
                                    <p class="text-sm text-green-600">Углеводы</p>
                                </div>
                                <p class="text-2xl font-bold text-green-600"><span
                                        id="carbs">{{ $product->carbs }}</span> <span class="text-lg">г</span></p>
                            </div>
                        </div>

                        <!-- Описание продукта -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-info-circle text-[#db2626] mr-2"></i> Описание
                            </h3>
                            <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- В секции перед закрывающим тегом body -->
    <script type="module">
        import {
            setupNutritionCalculator
        } from '{{ Vite::asset('resources/js/calculate_nutrition.js') }}';

        document.addEventListener('DOMContentLoaded', () => {
            setupNutritionCalculator({
                calories: {{ $product->calories }},
                protein: {{ $product->protein }},
                fat: {{ $product->fat }},
                carbs: {{ $product->carbs }}
            });
        });
    </script>

    <style>
        /* Дополнительные стили для плавных переходов */
        .animate-pulse {
            animation: pulse 0.5s ease-in-out;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</x-app-layout>
