@section('filter')
    <div class="container mx-auto px-4 py-8" x-data="{
        filters: {
            search: '',
            type: 'all',
            sort: 'newest',
            page: 1
        },
        loading: false,
        items: '',
        pagination: '',
    
        init() {
            this.loadItems();
            // Устанавливаем обработчики событий для пагинации
            this.setupPaginationLinks();
        },
    
        async loadItems() {
            this.loading = true;
            try {
                const response = await fetch('/filter?' + new URLSearchParams(this.filters), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
    
                if (!response.ok) throw new Error('Сетевая ошибка');
    
                const data = await response.json();
    
                if (!data.success) throw new Error('Ошибка сервера');
    
                this.updateContent(data);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при загрузке данных');
            } finally {
                this.loading = false;
            }
        },
    
        updateContent(data) {
            this.items = data.html;
            this.pagination = data.pagination;
            this.setupPaginationLinks();
        },
    
        setupPaginationLinks() {
            this.$nextTick(() => {
                const links = document.querySelectorAll('.pagination a');
                links.forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const url = new URL(link.href);
                        this.filters.page = url.searchParams.get('page') || 1;
                        this.loadItems();
                    });
                });
            });
        },
    
        applyFilter(type, value) {
            this.filters[type] = value;
            this.filters.page = 1; // Сбрасываем на первую страницу
            this.loadItems();
        },
        handlePaginationClick(event) {
            // Проверяем, что кликнули по ссылке
            const link = event.target.closest('a');
            if (!link) return;
    
            // Получаем href
            const href = link.getAttribute('href');
    
            // Парсим номер страницы
            const url = new URL(href, window.location.origin);
            const page = url.searchParams.get('page') || 1;
    
            // Обновляем фильтры и загружаем данные
            this.filters.page = Number(page);
            this.loadItems();
        }
    }">
        <!-- Фильтры -->
        <div class="mb-8 bg-white p-6 rounded-lg shadow">
            <input type="text" x-model="filters.search" @input.debounce.500ms="loadItems"
                placeholder="Поиск рецептов и продуктов..." class="w-full p-2 border rounded">
            <div class="flex flex-wrap gap-2 mb-3 mt-3">
                <template x-for="(label, value) in {all: 'Все', recipe: 'Рецепты', product: 'Продукты'}">
                    <button @click="applyFilter('type', value)" class="px-4 py-2 rounded-full text-sm"
                        :class="{ 'bg-red-600 text-white': filters.type === value, 'bg-gray-200 text-gray-700': filters.type !==
                                value }"
                        x-text="label"></button>
                </template>
            </div>
        </div>

        <div x-show="loading" class="text-center py-12">
            <p>Загрузка...</p>
        </div>

        <div x-show="!loading">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-html="items"></div>
            <div class="mt-8" @click.prevent='handlePaginationClick($event)' id="pagination" x-html="pagination"></div>
        </div>
    </div>
@endsection
