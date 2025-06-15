@section('menu')
    <div class=" md:w-1/6 pb-4 pt-4">
        <ul class="space-y-2 font-winki text-xl">
            <!-- Пункт меню "Рецепты" -->
            <li>
                <div class="flex items-center justify-between text-gray-500 cursor-pointer hover:text-[#db2626]" data-dropdown-toggle>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-utensils"></i> <!-- Иконка -->
                        <span>Рецепты</span> <!-- Текст -->
                    </div>
                    <!-- Стрелочка с анимацией -->
                    <svg class="arrow w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                    </svg>
                </div>
                <!-- Подменю (изначально скрыто) -->
               
                <ul class="dropdown hidden pl-6 space-y-2 mt-2">
                    @foreach ($recipeTypes as $type )
                    <li class="relative group">
                        <a href = "{{ route('recipes.index', $type->slug) }}" id="recipeType{{ $type->id }}" 
                         class="flex items-center justify-center py-2 px-2 transition-colors duration-200 border rounded-lg text-center whitespace-normal
                      {{ menu_item_class($type, 'active border-[#db2626] text-[#db2626]', 'border-gray-600 text-gray-600 hover:border-[#db2626] hover:text-[#db2626]') }}
                        ">{{ $type->type }}</a>
                    </li>
                    @endforeach
                </ul>
            </li>
            @role("user")
            <!-- Пункт меню "Калькулятор" -->
            <li>
                <a href=" {{ route('calculator.show') }}" class="flex items-center space-x-2 text-gray-500 hover:text-[#db2626]">
                    <i class="fas fa-calculator"></i> <!-- Иконка -->
                    <span>Калькулятор</span> <!-- Текст -->
                </a>
            </li>
            @endrole
            <!-- Пункт меню "Продукты" -->
            <li>
                <div class="flex items-center justify-between text-gray-500 cursor-pointer hover:text-[#db2626]" data-dropdown-toggle>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shopping-basket"></i> <!-- Иконка -->
                        <span>Продукты</span> <!-- Текст -->
                    </div>
                    <!-- Стрелочка с анимацией -->
                    <svg class="arrow w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                    </svg>
                </div>
                <!-- Подменю (изначально скрыто) -->
                <ul class="dropdown hidden pl-6 space-y-1 mt-2">
                    @foreach ($productTypes as $type )
                    <li class="relative group">
                        <a href=" {{ route('products.index', $type->slug) }} " 
                            id="productType{{ $type->id }}"
                            class="flex items-center justify-center py-2 px-2 transition-colors duration-200 border rounded-lg text-center whitespace-normal
                        {{ menu_item_class($type, 'active border-[#db2626] text-[#db2626]', 'border-gray-600 text-gray-600 hover:border-[#db2626] hover:text-[#db2626]') }}">{{ $type->type }}</a>
                    
                    </li>
                    @endforeach
                </ul>
            </li>

        </ul>
    </div>
@endsection