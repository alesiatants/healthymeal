<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Поиск продуктов Magnit</div>
    
                    <div class="card-body">
                        <form method="GET" action="{{ route('products.search') }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="query" class="form-label">Название продукта</label>
                                    <input type="text" class="form-control" id="query" 
                                           name="query" required value="{{ old('query', $query) }}">
                                </div>
    
                                <div class="col-md-4">
                                    <label for="target_weight" class="form-label">Вес для расчета (кг)</label>
                                    <input type="number" step="0.1" class="form-control" 
                                           id="target_weight" name="target_weight" 
                                           value="{{ old('target_weight', $target_weight) }}" min="0.1">
                                </div>
    
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Искать</button>
                                </div>
                            </div>
                        </form>
    
                        @if($search_performed)
                            <div class="mt-4">
                                @if(count($products) > 0)
                                    @if($average_price)
                                        <div class="alert alert-info">
                                            Средняя цена за {{ $target_weight }} кг: 
                                            <strong>{{ number_format($average_price, 2) }} руб.</strong>
                                        </div>
                                    @endif
    
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Продукт</th>
                                                    <th>Цена</th>
                                                    <th>Вес</th>
                                                    <th>Цена за кг</th>
                                                    <th>Рейтинг</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($products as $product)
                                                    <tr>
                                                        <td>{{ $product['name'] }}</td>
                                                        <td>{{ $product['price'] ? number_format($product['price'], 2) . ' руб.' : '-' }}</td>
                                                        <td>{{ $product['weight'] ?? '-' }}</td>
                                                        <td>{{ $product['price_per_kg'] ? number_format($product['price_per_kg'], 2) . ' руб.' : '-' }}</td>
                                                        <td>
                                                            @if($product['rating'])
                                                                <span class="badge bg-warning text-dark">
                                                                    ★ {{ number_format($product['rating'], 1) }}
                                                                </span>
                                                            @else
    -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning mt-3">
                                        Товары не найдены. Попробуйте изменить запрос.
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>