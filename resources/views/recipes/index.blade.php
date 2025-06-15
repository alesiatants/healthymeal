<x-app-layout>
    <div x-data="recipeFilter()" class="container mx-auto px-4 py-8">
        <div class="bg-[#B0B7C6] p-6 rounded-lg shadow mb-8">
            <form x-ref="filterForm" @submit.prevent="applyFilters">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Поиск рецептов</label>
                    <input x-model.debounce.500ms="search" type="text"
                        class="w-full px-4 py-2 rounded-lg focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]"
                        placeholder="Название рецепта">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Сложность</label>
                        <select name="difficulty" x-model="difficulty" @change="applyFilters()"
                            class="w-full px-4 py-2 rounded-lg focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]">
                            <option value="">Все</option>
                            @foreach ($difficulties as $value => $label)
                                <option value="{{ $value }}"
                                    {{ request('difficulty') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Макс. время приготовления (мин)</label>
                        <input type="number" name="max_time" x-model.debounce.500ms="maxTime"
                            class="w-full px-4 py-2 rounded-lg focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]"
                            placeholder="60">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Вкючить ингридиенты</label>
                        <div class="relative">
                            <input type="text" x-ref="includeIngredientsInput"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Добавьте ингредиенты..."
                                   @input.debounce.300ms="searchIngredients('include', $event.target.value)">
                            <div x-show="includeSuggestions.length > 0"
                                 class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg"
                                 @click.away="includeSuggestions = []">
                                <template x-for="ingredient in includeSuggestions" :key="ingredient.id">
                                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                         x-text="ingredient.name"
                                         @click="addIngredient('include', ingredient)">
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <template x-for="ingredient in includeIngredients" :key="ingredient.id">
                                <div class="flex items-center bg-indigo-100 text-indigo-800 rounded-full px-3 py-1 text-sm">
                                    <span x-text="ingredient.name"></span>
                                    <button type="button" class="ml-2 text-indigo-600 hover:text-indigo-900 focus:outline-none"
                                            @click="removeIngredient('include', ingredient.id)">
                                        ×
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Исключить ингридиенты</label>
                        <div class="relative">
                             <input type="text" x-ref="excludeIngredientsInput"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Исключите ингредиенты..."
                                   @input.debounce.300ms="searchIngredients('exclude', $event.target.value)">
                            <div x-show="excludeSuggestions.length > 0"
                                 class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg"
                                 @click.away="excludeSuggestions = []">
                                <template x-for="ingredient in excludeSuggestions" :key="ingredient.id">
                                    <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                         x-text="ingredient.name"
                                         @click="addIngredient('exclude', ingredient)">
                                    </div>
                                </template>
                            </div>
                        </div>
                         <div class="flex flex-wrap gap-2 mt-2">
                            <template x-for="ingredient in excludeIngredients" :key="ingredient.id">
                                <div class="flex items-center bg-red-100 text-red-800 rounded-full px-3 py-1 text-sm">
                                    <span x-text="ingredient.name"></span>
                                    <button type="button" class="ml-2 text-red-600 hover:text-red-900 focus:outline-none"
                                            @click="removeIngredient('exclude', ingredient.id)">
                                        ×
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </form>
        </div>
  
        <div id="recipe-list" class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @include('recipes.list', ['recipes' => $recipes])
        </div>
        <div id="pagination-links" class="mt-6">
            {{ $recipes->appends(request()->query())->links() }}
       </div>
        <div x-show="loading" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
            <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-gray-900"></div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('recipeFilter', () => ({
                search: '{{ request('search', '') }}',
                difficulty: '{{ request('difficulty', '') }}',
                maxTime: '{{ request('max_time', '') }}',
                includeIngredients: [], // Массив для выбранных включаемых ингредиентов { id: ..., name: ... }
                excludeIngredients: [], // Массив для выбранных исключаемых ингредиентов { id: ..., name: ... }
                includeSuggestions: [], // Предложения для включаемых ингредиентов
                excludeSuggestions: [], // Предложения для исключаемых ингредиентов
                recipeListHtml: '', // Переменная для хранения HTML списка рецептов
                paginationHtml: '', // Переменная для хранения HTML пагинации
                loading: false, // Флаг для отображения спиннера
                init() {
                    const recipeListElement = this.$el.querySelector('.grid.md\\:grid-cols-2');
                    if (recipeListElement) {
                         this.recipeListHtml = recipeListElement.outerHTML;
                    } else {
                         const emptyStateElement = this.$el.querySelector('.col-span-3.text-center');
                         if (emptyStateElement) { this.recipeListHtml = `<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 font-winki">${emptyStateElement.outerHTML}</div>`;
                         } else {this.recipeListHtml = '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 font-winki"></div>'; // Пустой контейнер
                         }
                    }
                    const paginationElement = this.$el.querySelector('.mt-8');
                    if (paginationElement) {
                         this.paginationHtml = paginationElement.outerHTML;
                    } else {
                         this.paginationHtml = ''; 
                    }
                    this.$watch('search', () => this.applyFilters());
                    this.$watch('difficulty', () => this.applyFilters());
                    this.$watch('maxTime', () => this.applyFilters());
                },
                async applyFilters(page = 1) {
                    this.loading = true; // Показываем спиннер
                    const includeIds = this.includeIngredients.map(ing => ing.id);
                    const excludeIds = this.excludeIngredients.map(ing => ing.id);
                    const params = new URLSearchParams({
                        search: this.search,
                        difficulty: this.difficulty,
                        max_time: this.maxTime,
                        include_ingredients: includeIds.join(','),
                        exclude_ingredients: excludeIds.join(','), 
                        page: page
                    });
                    const url = `{{ route('recipes.index', $recipeType) }}?${params.toString()}`;
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const data = await response.json();
                        if (data.recipe_list_html === undefined || data.pagination_html === undefined) {
                            console.error('Ответ JSON не содержит ожидаемых ключей. Проверьте контроллер.');
                            this.loading = false;
                            return;
                        }
                const recipeListElement = document.getElementById('recipe-list');
                if (recipeListElement) {
                     recipeListElement.innerHTML = data.recipe_list_html;
                     console.log('Список рецептов обновлен.');
                } else {
                     console.error('Элемент #recipe-list не найден в DOM. Не удалось обновить список рецептов.');
                }
                    } catch (error) {
                        console.error("Ошибка при применении фильтров:", error);
                    } finally {
                        this.loading = false;
                    }
                },
                async searchIngredients(type, query) {
                    if (query.length < 2) {
                        this[type + 'Suggestions'] = [];
                        return;
                    }
                    try {
                        const response = await fetch(`/search/product?query=${encodeURIComponent(query)}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const products = await response.json();
                        this[type + 'Suggestions'] = products;
                    } catch (error) {
                        console.error("Ошибка при поиске продукта:", error);
                        this[type + 'Suggestions'] = [];
                    }
                },
                addIngredient(type, ingredient) {
                    const ingredientList = this[type + 'Ingredients'];
                    if (!ingredientList.some(item => item.id === ingredient.id)) {
                        ingredientList.push(ingredient);
                        this[type + 'Suggestions'] = [];
                        this.$refs[type + 'IngredientsInput'].value = ''; 
                        this.applyFilters(); 
                    }
                },
                removeIngredient(type, ingredientId) {
                    const ingredientList = this[type + 'Ingredients'];
                    this[type + 'Ingredients'] = ingredientList.filter(ing => ing.id !== ingredientId);
                    this.applyFilters();
                },
                 setupPaginationHandlers() {
                    const paginationBlock = this.$el.querySelector('.mt-8');
                    if (paginationBlock) {
                         const paginationLinks = paginationBlock.querySelectorAll('a');

                         paginationLinks.forEach(link => {
                            link.removeEventListener('click', this.handlePaginationClick);
                            link.addEventListener('click', this.handlePaginationClick.bind(this));
                         });
                    }
                },

                handlePaginationClick(event) {
                    event.preventDefault();
                    const url = event.target.href;
                    alert(url);

                    const urlParams = new URLSearchParams(url.split('?')[1]);
                    const page = urlParams.get('page') || 1;

                }
            }));
        });
    </script>
</x-app-layout>