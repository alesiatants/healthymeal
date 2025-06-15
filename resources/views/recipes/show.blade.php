<x-app-layout>
    <div class="container mx-auto px-8 py-8 max-w-4xl bg-[#B0B7C6] rounded-lg">
        <!-- Верхняя часть с фотографией и тегом типа -->
        <div class="relative mb-8">
            <img src="{{ asset('storage/' . $recipe->image) }}" alt="{{ $recipe->name }}"
                class="w-full h-99 object-cover rounded-lg shadow-lg">
            <span class="absolute top-4 right-4 bg-black bg-opacity-70 text-white px-3 py-1 rounded-full text-sm">
                {{ $recipe->type->type }}
            </span>
        </div>

        <!-- Название и мета-информация -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-4">{{ $recipe->name }}</h1>

            <div class="flex items-center gap-4 mb-4">
                @auth
                    <button class="text-2xl focus:outline-none favorite-btn" data-recipe-id="{{ $recipe->id }}"
                        id="recipeId">
                        <i class="{{ $recipe->isFavoritedBy(auth()->user()) ? 'fas text-red-500' : 'far' }} fa-heart"></i>
                    </button>
                @endauth

                <!-- Блок среднего рейтинга с явным ID -->
                <div class="flex items-center gap-1" id="average-rating-container">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa-star text-xl {{ $i <= $recipe->averageRating() ? 'fas text-yellow-400' : 'far' }}"
                            data-rating="{{ $i }}"></i>
                    @endfor
                    <span class="ml-1 text-gray-600" id="ratings-count">({{ $recipe->scores_count }})</span>
                </div>

                <div class="flex items-center gap-1 text-gray-600">
                    <i class="fas fa-comment"></i>
                    <span class="comments-count">{{ $recipe->comments_count }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @isset ($recipe->author->avatar)
                <img src="{{ asset('storage/' . $recipe->author->avatar) }}" alt="{{ $recipe->author->name }}"
                    class="w-10 h-10 rounded-full object-cover">
                    @else
                    <img src="{{ asset('storage/users/avatar.jpg') }}" alt="{{ $recipe->author->name }}"
                    class="w-10 h-10 rounded-full object-cover">
                    @endisset
                <span class="font-medium">{{ $recipe->author->name }}</span>
                <span class="text-gray-500 text-sm">{{ $recipe->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <!-- Теги времени и сложности -->
        <div class="flex justify-center gap-4 mb-8">
            <!-- Время приготовления -->
            <span
                class="flex items-center gap-2 px-4 py-1.5 rounded-full text-sm shadow
                        bg-gradient-to-r from-purple-200 to-purple-80 text-purple-800 border border-purple-200">
                <i class="fas fa-clock text-purple-500"></i>
                {{ $recipe->prep_time }} мин
            </span>

            <!-- Сложность -->
            <span
                class="flex items-center gap-2 px-4 py-1.5 rounded-full text-sm shadow
                         bg-gradient-to-r from-indigo-200 to-indigo-80 text-indigo-800 border border-indigo-200">
                <i class="fas fa-signal text-indigo-500"></i>
                {{ $recipe->difficulty }}
            </span>
        </div>

        <div class="flex flex-col items-center gap-6">
            <!-- Теги в строку с переносом -->
            <div class="flex flex-wrap justify-center gap-3">
                <!-- Калории -->
                <span
                    class="flex items-center gap-2 px-4 py-1.5 rounded-full text-sm shadow
                           bg-gradient-to-r from-red-200 to-red-80 text-red-800 border border-red-200">
                    <i class="fas fa-fire text-red-500"></i>
                    {{ $recipe->calories }} ккал
                </span>

                <!-- Белки -->
                <span
                    class="flex items-center gap-2 px-4 py-1.5 rounded-full text-sm shadow
                           bg-gradient-to-r from-blue-200 to-blue-80  text-blue-800 border border-blue-200"
                    id="protein" data-protein-value="{{ $recipe->protein }}">
                    <i class="fas fa-dumbbell text-blue-500"></i>
                    Белки: {{ $recipe->protein }} г.
                </span>

                <!-- Жиры (с градиентом) -->
                <span
                    class="flex items-center gap-2 px-4 py-1.5 rounded-full text-sm shadow
                            bg-gradient-to-r from-amber-200 to-amber-80 text-amber-800 border border-amber-200"
                    id="fat" data-fat-value="{{ $recipe->fat }}">
                    <i class="fas fa-bacon text-amber-500"></i>
                    Жиры: {{ $recipe->fat }} г.
                </span>

                <!-- Углеводы -->
                <span
                    class="flex items-center gap-2 px-4 py-1.5 rounded-full text-sm shadow
                            bg-gradient-to-r from-green-200 to-green-80 text-green-800 border border-green-200"
                    id="carbs" data-carbs-value="{{ $recipe->carbs }}">
                    <i class="fas fa-bread-slice text-green-500"></i>
                    Углеводы: {{ $recipe->carbs }} г.
                </span>
            </div>

            <!-- График по центру -->
            <div class="relative w-40 h-40 mb-8">
                <canvas id="nutritionChart"></canvas>
                <div id="chart-legend" class="mt-3 flex justify-center gap-3 text-xs"></div>
            </div>
        </div>



        <!-- Описание рецепта -->
        <div class="mb-8 p-6  rounded-xl shadow-sm border border-gray-100">
            <div class="prose prose-sm sm:prose-base max-w-none text-gray-700">
                <div class="text-justify leading-relaxed space-y-4">
                    @foreach (explode("\n", $recipe->description) as $paragraph)
                        @if (trim($paragraph))
                            <p
                                class="relative pl-4 before:absolute before:left-0 before:top-2 before:w-1 before:h-4 before:bg-[#db2626] before:rounded-full">
                                {{ $paragraph }}
                            </p>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Ингредиенты -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Ингредиенты</h2>

            <div class="flex items-center gap-3 mb-4">
                <button class="w-8 h-8 flex items-center justify-center bg-[#db2626] rounded-full portion-btn"
                    id="portion-minus" aria-label="Уменьшить порцию">
                    <i class="fas fa-minus text-white"></i>
                </button>
                <span id="portion-count" class="font-medium">1</span> порция
                <button class="w-8 h-8 flex items-center justify-center bg-[#db2626] rounded-full portion-btn"
                    id="portion-plus" aria-label="Увеличить порцию">
                    <i class="fas fa-plus text-white"></i>
                </button>
            </div>

            <div class="border rounded-lg overflow-hidden mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                    @foreach ($recipe->products as $product)
                        <div class="ingredient-item border-b last:border-b-0 hover:bg-gray-50 p-3 flex items-center"
                            data-base-amount="{{ $product->pivot->quantity }}" data-product-id="{{ $product->id }}"
                            data-product-unit="{{ $product->pivot->unit }}">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                class="w-20 h-20 object-cover rounded mr-3">
                            <div class="flex-grow">
                                <a href="{{ route('products.show', [
                                    'productType' => $product->productType->slug,
                                    'product' => $product->slug,
                                ]) }}"
                                    class="product_name text-gray-600 hover:text-gray-800 font-semibold transition duration-200 ease-in-out rounded-lg p-1 hover:bg-gray-100">
                                    {{ $product->name }}
                                </a>
                            </div>
                            <div class="text-right whitespace-nowrap">
                                <span class="calculated-amount font-medium">{{ $product->pivot->quantity }}</span>
                                <span class="text-gray-500"> {{ $product->pivot->unit }}.</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @auth
                <button id="calculate-cost" class="px-4 py-2 text-white rounded bg-[#db2626] hover:bg-[#c52222] transition">
                    Рассчитать стоимость
                </button>
            @else
                <button class="px-4 py-2 text-white rounded transition bg-[#db2626] hover:bg-[#c52222]"
                    data-bs-toggle="modal" data-bs-target="#loginModal">
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"> Войдите для расчета стоимости</a>
                </button>
            @endauth

            <!-- Блок для вывода результатов расчета -->
            <div id="cost-results" class="hidden mt-6 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-xl font-bold mb-3">Расчет стоимости</h3>

                <div class="flex items-start bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium text-blue-800">Данные получены с Магнита</p>
                        <p class="text-sm text-blue-600 mt-1">Актуально на {{ now()->format('d.m.Y') }}</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 px-4">Продукт</th>
                                <th class="text-right py-2 px-4">Количество</th>
                                <th class="text-right py-2 px-4">Цена в кг/г/л/мл</th>
                                <th class="text-right py-2 px-4">Стоимость на рецепт</th>
                                <th class="text-right py-2 px-4"></th>
                            </tr>
                        </thead>
                        <tbody id="cost-details">
                            <!-- Сюда будут вставлены результаты расчета -->
                        </tbody>
                        <tfoot>
                            <tr class="border-t font-bold">
                                <td class="text-left py-2 px-4">Итого:</td>
                                <td colspan="2"></td>
                                <td id="total-cost" class="text-right py-2 px-4">0 руб.</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Модальное окно с предложениями -->
            <div id="prepositionsModal" class="hidden fixed inset-0 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2 px-4">Продукт</th>
                                        <th class="text-right py-2 px-4">Цена</th>
                                        <th class="text-right py-2 px-4">Стоимость за единицу</th>
                                    </tr>
                                </thead>
                                <tbody id="products-details">
                                    <!-- Сюда будут вставлены результаты расчета -->
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" id="closeProductsModal" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Закрыть
                            </button>
                        </div>
                    </div>
                </div>
            </div>




    </div>

    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-8 text-gray-800 border-l-4 border-red-500 pl-4">Процесс приготовления</h2>

        <div class="border-l-2 border-gray-300 pl-8 space-y-8">
            @foreach ($recipe->steps as $step)
                <div class="relative">
                    <div class="absolute -left-11 top-0 h-6 w-6 rounded-full bg-red-400 border-4 border-gray-300">
                    </div>
                    <div class="pl-2">
                        <span class="text-sm font-semibold text-red-600">Шаг {{ $loop->iteration }}</span>
                        <p class="mt-1 text-gray-700">{{ $step->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Блок оценки рецепта -->
    <div class="mb-12 bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-l-4 border-[#db2626] pl-4">Оцените рецепт</h2>

        <div class="rating-section mb-8">
            <h3 class="text-xl font-bold mb-4">Оцените рецепт</h3>

            @auth
                @php
                    $userRating = auth()->user()->ratings()->where('recipes_id', $recipe->id)->first();
                    $currentRating = $userRating ? $userRating->score : 0;
                @endphp

                <div class="flex items-center">
                    <div class="star-rating flex">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" class="star-btn p-1 focus:outline-none"
                                @if ($currentRating == 0) data-update="false"
                                @else
                                    data-update="true" @endif
                                data-rating="{{ $i }}" data-recipe="{{ $recipe->slug }}"
                                data-recipe-type="{{ $recipe->type->slug }}">
                                <i
                                    class="fas fa-star text-2xl {{ $i <= $currentRating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            </button>
                        @endfor
                    </div>
                    <span class="ml-3 text-gray-600 rating-status">
                        @if ($currentRating > 0)
                            Ваша оценка: {{ $currentRating }}
                        @else
                            Оцените рецепт
                        @endif
                    </span>
                </div>
            @else
                <div class="bg-blue-50 text-blue-800 p-3 rounded">
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                        class="text-blue-600 hover:underline">Войдите</a>, чтобы оценить
                    рецепт
                </div>
            @endauth
        </div>





        <div class="mb-8" id="comments-section">
            <h2 class="text-2xl font-bold mb-4">Комментарии (<span
                    class="comments-count">{{ $recipe->comments_count }}</span>)</h2>

            <div class="space-y-4 mb-6" id="comments-list">
                @foreach ($recipe->comments as $comment)
                    <div class="bg-white p-4 rounded-lg shadow comment-item" data-comment-id="{{ $comment->id }}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3">
                                @isset ($comment->user->avatar)
                                    <img src="{{ asset('storage/' . $comment->user->avatar) }}"
                                        alt="{{ $comment->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <img src="{{ asset('storage/users/avatar.jpg') }}"
                                        alt="{{ $comment->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                @endisset
                                <div>
                                    <div class="font-medium">{{ $comment->user->name }}</div>
                                    <div class="text-gray-500 text-sm">
                                        <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                                        @if ($comment->updated_at != $comment->created_at)
                                            <span class="text-gray-400">(изменено)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if (auth()->check() && auth()->user()->id == $comment->user_id)
                                <div class="flex gap-2 comment-actions">
                                    <button class="edit-comment-btn text-green-500 hover:text-green-700"
                                        onclick="editComment({{ $comment->id }})">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button class="delete-comment-btn text-red-500 hover:text-red-700"
                                        onclick="deleteComment({{ $comment->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="text-gray-700 comment-content pl-13">
                            {{ $comment->comment }}
                        </div>

                        @if (auth()->check() && auth()->user()->id == $comment->user_id)
                            <div class="comment-edit-form hidden mt-3 pl-13" id="edit-form-{{ $comment->id }}">
                                <textarea
                                    class="w-full p-3 border  rounded-lg focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626] comment-edit-textarea"
                                    rows="3">{{ $comment->comment }}</textarea>
                                <div class="flex gap-2 mt-2">
                                    <button onclick="saveComment({{ $comment->id }})"
                                        class="save-comment-btn bg-[#db2626] text-white px-3 py-1 rounded-md hover:bg-[#c52222']">
                                        Сохранить
                                    </button>
                                    <button onclick="cancelEdit({{ $comment->id }})"
                                        class="cancel-edit-btn bg-gray-500 text-white px-3 py-1 rounded-md hover:bg-gray-600">
                                        Отмена
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @auth
                <form id="add-comment-form"
                    action="{{ route('recipes.comments.store', ['recipeType' => $recipe->type->slug, 'recipe' => $recipe->slug]) }}"
                    method="POST" class="bg-white p-4 rounded-lg shadow">
                    @csrf
                    <textarea name="comment" id="comment-textarea" placeholder="Оставьте ваш комментарий..."
                        class="w-full mb-2 p-3 rounded-lg focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626]"
                        rows="3" required data-recipe-type = "{{ $recipe->type->slug }}"
                        data-recipe-name = "{{ $recipe->slug }}"></textarea>

                    <!-- Блок для ошибок валидации -->
                    <div id="comment-error" class="text-red-500 text-sm mt-2 mb-2 p-2 rounded hidden"></div>

                    <button type="submit" id="submit-comment-btn"
                        class="bg-[#db2626] text-white px-4 py-2 rounded-md hover:bg-[#c52222]">
                        Отправить
                    </button>

                    <!-- Блок для успешного сообщения -->
                    <div id="comment-success" class="mt-2 p-2 bg-green-100 text-green-700 rounded hidden"></div>
                </form>
                @include('components.error_modal')
                @yield('errormodal')
            @else
                <div class="bg-blue-50 text-blue-800 p-4 rounded-lg">
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                        class="text-blue-600 font-medium hover:underline">Войдите</a>,
                    чтобы оставить комментарий
                </div>
            @endauth
        </div>
        @vite(['resources/js/crudcomment.js', 'resources/js/addFavorite.js', 'resources/js/nutrition_charts.js', 'resources/js/calculate_product_quantity.js', 'resources/js/calculate_cost_product.js', 'resources/js/addScore.js'])
    </div>
    </div>

</x-app-layout>
