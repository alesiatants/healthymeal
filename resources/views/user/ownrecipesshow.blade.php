<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <!-- Кнопка добавления нового рецепта -->
        <div class="mb-8 text-right">
            <a href="{{ route('recipes.create') }}"
                class="inline-flex items-center px-4 py-2 bg-[#db2626] text-white rounded-md hover:bg-[#c52222] transition">
                <i class="fas fa-plus mr-2"></i>
                Добавить рецепт
            </a>
        </div>
        @include('components.error_modal')
        @yield('errormodal')

        <!-- Список рецептов пользователя -->
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

                        <!-- Кнопки управления -->
                        <div class="absolute top-4 left-4 flex gap-2 px-3 py-1">
                            <a href="{{ route('recipes.edit', ['recipe' => $recipe->slug]) }}"
                                class="w-8 h-8 flex items-center justify-center bg-white bg-opacity-80 rounded-full text-blue-600 hover:bg-blue-100">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form id="delete-recipe-form"
                                action="{{ route('recipes.destroy', ['recipe' => $recipe->slug]) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="showDeleteModal('{{ route('recipes.destroy', $recipe->slug) }}')"
                                    class="w-8 h-8 flex items-center justify-center bg-white bg-opacity-80 rounded-full text-red-600 hover:bg-red-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
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
                                @isset ($recipe->author->avatar)
                                <img src="{{ asset('storage/' . $recipe->author->avatar) }}" alt="{{ $recipe->author->name }}"
                                    class="w-10 h-10 rounded-full object-cover">
                                    @else
                                    <img src="{{ asset('storage/users/avatar.jpg') }}" alt="{{ $recipe->author->name }}"
                                    class="w-10 h-10 rounded-full object-cover">
                                    @endisset
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
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500 mb-4">
                        <i class="fas fa-utensils text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">У вас пока нет рецептов</h3>
                    <p class="mt-1 text-sm text-gray-500">Создайте свой первый рецепт!</p>
                    <div class="mt-6">
                        <a href="{{ route('recipes.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-[#db2626] text-white rounded-md hover:bg-[#c52222] transition">
                            <i class="fas fa-plus mr-2"></i>
                            Добавить рецепт
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Модальное окно для подтверждения удаления -->
    <div id="confirmModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Подтвердите удаление</h3>
            <p class="text-gray-600 mb-6">Вы уверены, что хотите удалить этот рецепт? Это действие нельзя отменить.</p>
            <div class="flex justify-end space-x-3">
                <button id="cancelButton"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Отмена
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Удалить
                    </button>
                </form>
            </div>
        </div>
    </div>
    @vite(['resources/js/deleterecipe.js'])
    <script type="module">
        import {
            showDeleteModal
        } from '{{ Vite::asset('resources/js/deleterecipe.js') }}';
        // Делаем функцию доступной глобально, если нужно
        window.showDeleteModal = showDeleteModal;
    </script>
</x-app-layout>
