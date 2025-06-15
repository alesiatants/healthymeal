<x-app-layout>
    <div class="container mx-auto px-4 pb-8">
        <form id="add-update-recipe-form" method="POST"
            action="{{ isset($recipe) ? route('recipes.update', $recipe->slug) : route('recipes.store') }}"
            enctype="multipart/form-data">
            @csrf
            @isset($recipe)
                @method('PUT')
            @endisset

            @include('components.error_modal')
            @yield('errormodal')
            @php
                $filteredErrors = collect($errors->getMessages())->except(['exception']);
            @endphp
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold mb-6">{{ isset($recipe) ? 'Редактирование рецепта' : 'Создание рецепта' }}
                </h2>

                <div id="error-container" class="text-red-500 p-4 mb-4 border-l-4 border-red-500 hidden">
                    <h3 class="font-bold text-red-800">Пожалуйста, исправьте следующие ошибки:</h3>

                </div>

                <!-- Основная информация -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-group">
                        <x-light-input-label for="name" :value="__('Название рецепта')" />
                        <x-light-text-input type="text" name="name"
                            value="{{ isset($recipe) ? old('name', $recipe->name) : '' }}"
                            data-initial-value="{{ $recipe->name ?? old('name') }}" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="form-group">
                        <x-light-input-label for="recipe_type_id" :value="__(' Тип рецепта')" />
                        <x-light-select-input name="recipe_type_id">
                            @foreach ($recipeTypes as $type)
                                <option value="{{ $type->id }}" @selected(isset($recipe) && $recipe->recipe_type_id == $type->id)>
                                    {{ $type->type }}
                                </option>
                            @endforeach
                        </x-light-select-input>
                        <x-input-error :messages="$errors->get('recipe_type_id')" class="mt-2" />
                    </div>

                    <div class="form-group">
                        <x-light-input-label for="prep_time" :value="__('Время приготовления (мин)')" />
                        <x-light-text-input type="number" name="prep_time"
                            value="{{ $recipe->prep_time ?? old('prep_time') }}" min="2"
                            data-initial-value="{{ $recipe->prep_time ?? old('prep_time') }}" />
                        <x-input-error :messages="$errors->get('prep_time')" class="mt-2" />
                    </div>

                    <div class="form-group">
                        <x-light-input-label for="difficulty" :value="__('Сложность')" />
                        <x-light-select-input name="difficulty">
                            <option value="Легкий" @selected(isset($recipe) && $recipe->difficulty == 'Легкий')>Легкий</option>
                            <option value="Средний" @selected(isset($recipe) && $recipe->difficulty == 'Средний')>Средний</option>
                            <option value="Сложный" @selected(isset($recipe) && $recipe->difficulty == 'Сложный')>Сложный</option>
                        </x-light-select-input>
                        <x-input-error :messages="$errors->get('difficulty')" class="mt-2" />
                    </div>
                </div>

                <!-- Изображение -->
                <div class="mb-6 form-group">
                    <x-light-input-label for="image" :value="__('Изображение')" />
                    @isset($recipe)
                        <img src="{{ asset('storage/' . $recipe->image) }}" id="imagePreview" class="h-40 mb-2 rounded">
                    @else
                        <img id="imagePreview" class="h-40 mb-2 rounded" style="display: none;">
                    @endisset
                    <input type="file" name="image" id="imageInput" class="w-full p-2" accept="image/*">
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>


                <!-- Описание -->
                <div class="mb-6 form-group">
                    <x-light-input-label for="description" :value="__('Описание')" />
                    <x-light-textarea-input name="description" rows="7"
                        data-initial-value="{{ $recipe->description ?? old('description') }}">{{ isset($recipe) ? old('description', $recipe->description) : '' }}</x-light-textarea-input>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <!-- Ингредиенты -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">Ингредиенты</h3>

                <div id="ingredients-container">
                    @if (isset($recipe) && $recipe->products->count())
                        @foreach ($recipe->products as $index => $product)
                            <div class="ingredient-item grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                                <div class="md:col-span-7 form-group">
                                    <x-light-select-input name="ingredients[{{ $index }}][product_id]">
                                        @foreach ($products as $productItem)
                                            <option value="{{ $productItem->id }}" @selected($product->id == $productItem->id)>
                                                {{ $productItem->name }} ({{ $productItem->productType->type }})
                                            </option>
                                        @endforeach
                                    </x-light-select-input>
                                    <x-input-error :messages="$errors->get('ingredients[' . $index . '][product_id]')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2 form-group">
                                    <x-light-text-input type="number" step="0.1"
                                        name="ingredients[{{ $index }}][quantity]"
                                        value='{{ old("ingredients.$index.quantity", $product->pivot->quantity) }}'
                                        data-initial-value="{{ $product->pivot->quantity }}" min="0.1" />
                                    <x-input-error :messages="$errors->get('ingredients[' . $index . '][quantity]')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2 form-group">
                                    <x-light-select-input name="ingredients[{{ $index }}][unit]">
                                        <option value="г" @selected($product->pivot->unit == 'г')>граммы</option>
                                        <option value="мл" @selected($product->pivot->unit == 'мл')>миллилитры</option>
                                        <option value="шт" @selected($product->pivot->unit == 'шт')>штуки</option>
                                        <option value="кг" @selected($product->pivot->unit == 'кг')>килограммы</option>
                                        <option value="ч.л." @selected($product->pivot->unit == 'ч.л.')>чайная ложка</option>
                                        <option value="ст.л." @selected($product->pivot->unit == 'ст.л.')>столовая ложка</option>
                                        <option value="ст" @selected($product->pivot->unit == 'ст')>стакан</option>
                                    </x-light-select-input>
                                    <x-input-error :messages="$errors->get('ingredients[' . $index . '][unit]')" class="mt-2" />
                                </div>
                                <div class="md:col-span-1 flex items-center">
                                    <button type="button" class="remove-ingredient text-red-500">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="ingredient-item grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                            <div class="md:col-span-5 form-group">
                                <x-light-select-input name="ingredients[0][product_id]">
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} ({{ $product->productType->type }})
                                        </option>
                                    @endforeach
                                </x-light-select-input>
                                <x-input-error :messages="$errors->get('ingredients[0][product_id]')" class="mt-2" />
                            </div>
                            <div class="md:col-span-3 form-group">
                                <x-light-text-input type="number" step="0.1" name="ingredients[0][quantity]"
                                    data-initial-value="" min="0.1" />
                                <x-input-error :messages="$errors->get('ingredients[0][quantity]')" class="mt-2" />
                            </div>
                            <div class="md:col-span-3 form-group">
                                <x-light-select-input name="ingredients[0][unit]">
                                    <option value="г">граммы</option>
                                        <option value="мл">миллилитры</option>
                                        <option value="шт">штуки</option>
                                        <option value="кг">килограммы</option>
                                        <option value="ч.л.">чайная ложка</option>
                                        <option value="ст.л.">столовая ложка</option>
                                        <option value="ст">стакан</option>
                                </x-light-select-input>
                                <x-input-error :messages="$errors->get('ingredients[0][unit]')" class="mt-2" />
                            </div>
                            <div class="md:col-span-1 flex items-center">
                                <button type="button" class="remove-ingredient text-red-500">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" id="add-ingredient"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    <i class="fas fa-plus mr-2"></i> Добавить ингредиент
                </button>
            </div>

            <!-- Шаги приготовления -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">Шаги приготовления</h3>

                <div id="steps-container">
                    @if (isset($recipe) && $recipe->steps->count())
                        @foreach ($recipe->steps as $index => $step)
                            <div class="step-item mb-4">
                                <div class="flex gap-2">
                                    <div class="flex-grow form-group">
                                        <x-light-input-label for="steps[{{ $index }}][description]">Шаг
                                            {{ $step->step_number }}</x-light-input-label>
                                        <x-light-textarea-input name="steps[{{ $index }}][description]"
                                            rows="7"
                                            data-initial-value="{{ $step->description }}">{{ old("steps.$index.description", $step->description) }}
                                        </x-light-textarea-input>
                                        <x-input-error :messages="$errors->get('steps[' . $index . '][description]')" class="mt-2" />
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" class="remove-step text-red-500">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="step-item mb-4">
                            <div class="flex gap-2">
                                <div class="flex-grow form-group">
                                    <x-light-input-label for="steps[0][description]" :value="__('Шаг 1')" />
                                    <x-light-textarea-input name="steps[0][description]" rows="7"
                                        data-initial-value=""></x-light-textarea-input>
                                    <x-input-error :messages="$errors->get('steps[0][description]')" class="mt-2" />
                                </div>
                                <div class="flex items-center">
                                    <button type="button" class="remove-step text-red-500">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" id="add-step"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    <i class="fas fa-plus mr-2"></i> Добавить шаг
                </button>
            </div>

            <!-- Кнопки формы -->
            <div class="flex justify-between">
                <a href="{{ route('recipes.showown') }}"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Отмена
                </a>
                <button id="submit-recipe-btn" type="submit"
                    class="px-6 py-2 bg-[#db2626] text-white rounded-md hover:bg-[#c52222]">
                    {{ isset($recipe) ? 'Обновить рецепт' : 'Создать рецепт' }}
                </button>
            </div>
        </form>
    </div>
    @vite(['resources/js/crudrecipe.js'])
    <script>
        const products = @json($products->load('productType'));
    </script>

</x-app-layout>
