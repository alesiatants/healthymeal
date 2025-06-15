<x-admin-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Заголовок -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-semibold text-gray-800">Список запросов</h1>
            </div>

            <!-- Фильтры -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <form method="GET" action="{{ route('admin.system.requests') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Метод -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Метод</label>
                            <select name="method"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Все</option>
                                @foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method)
                                    <option value="{{ $method }}"
                                        {{ request('method') == $method ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- URI -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Путь содержит</label>
                            <input type="text" name="uri" value="{{ request('uri') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <!-- Тип лога -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Тип лога</label>
                            <select name="log_type"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Все</option>
                                <option value="error" {{ request('log_type') == 'error' ? 'selected' : '' }}>Ошибки
                                </option>
                                <option value="info" {{ request('log_type') == 'info' ? 'selected' : '' }}>Инфо
                                </option>
                                <option value="debug" {{ request('log_type') == 'debug' ? 'selected' : '' }}>Отладка
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4">
                        <!-- Дата от -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">С</label>
                            <input type="datetime-local" name="date_from" value="{{ request('date_from') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Дата до -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">По</label>
                            <input type="datetime-local" name="date_to" value="{{ request('date_to') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Длительность -->

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Мин длительность (с)</label>
                            <input type="number" name="min_duration" value="{{ request('min_duration') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Макс длительность (с)</label>
                            <input type="number" name="max_duration" value="{{ request('max_duration') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>


                        <!-- Количество на странице -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Количество записей</label>
                            <select name="max" onchange="this.form.submit()"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach ([5, 10, 20, 50, 100] as $perPage)
                                    <option value="{{ $perPage }}"
                                        {{ request('max') == $perPage ? 'selected' : '' }}>
                                        {{ $perPage }} per page
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Кнопки -->
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Применить
                        </button>
                        <a href="{{ route('admin.system.requests') }}"
                            class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-gray-300 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Сбросить
                        </a>
                    </div>
                </form>
            </div>

            <!-- Таблица -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ИД</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Метод</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Путь</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Длительность</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Дата</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Логи</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Просмотр</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($requests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ Str::limit($request->id, 8) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @php
                                        $methodColor = match ($request->method) {
                                            'GET' => 'bg-blue-100 text-blue-800',
                                            'POST' => 'bg-green-100 text-green-800',
                                            'PUT' => 'bg-yellow-100 text-yellow-800',
                                            'DELETE' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $methodColor }}">
                                        {{ $request->method }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900 truncate max-w-xs" title="{{ $request->uri }}">
                                        {{ $request->uri }}
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-4 whitespace-nowrap text-sm {{ $request->time->duration > 1 ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                    {{ number_format($request->time->duration, 2) }} s
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->datetime }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->ip }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-indigo-600 hover:text-indigo-900"
                                        onclick="toggleLogs('logs-{{ $loop->index }}')">
                                        Раскрыть ({{ $request->messages->count }})
                                    </button>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-indigo-600 hover:text-indigo-900"
                                        data-modal-toggle="detailsModal" data-details="{{ json_encode($request) }}"
                                        onclick="showDetails(this)">
                                        Подробнее
                                    </button>
                                </td>
                            </tr>


                            <!-- Раскрывающийся блок с логами -->
                            <tr id="logs-{{ $loop->index }}" class="hidden bg-gray-50">
                                <td colspan="7" class="px-6 py-4">
                                    <div class="space-y-2 max-w-full overflow-hidden">
                                        @foreach ($request->messages->messages as $log)
                                            @php
                                                $logColor = match ($log->label) {
                                                    'error' => 'bg-red-300 border-red-200 text-red-800',
                                                    'info' => 'bg-blue-200 border-blue-300 text-blue-800',
                                                    'debug' => 'bg-gray-300 border-gray-400 text-gray-800',
                                                    default => 'bg-gray-200 border-gray-300',
                                                };
                                            @endphp
                                            <div class="p-3 border rounded-md {{ $logColor }} break-words">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-medium">
                                                        {{ strtoupper($log->label) }}
                                                    </span>
                                                    <span class="text-xs text-gray-500 whitespace-nowrap">
                                                        {{ \Carbon\Carbon::createFromTimestamp($log->time)->format('H:i:s') }}
                                                    </span>
                                                </div>
                                                <div
                                                    class="mt-1 text-sm whitespace-pre-wrap break-all overflow-x-auto max-w-full">
                                                    {{ $log->message }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>

    <!-- Модальное окно с деталями -->
    <div id="detailsModal" class="hidden fixed inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Данные запроса
                            </h3>
                            <div class="mt-2">
                                <pre id="detailsContent" class="bg-gray-100 p-4 rounded-md overflow-auto max-h-96 text-sm"></pre>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleLogs(id) {
            const element = document.getElementById(id);
            element.classList.toggle('hidden');
        }

        function showDetails(button) {
            const details = JSON.parse(button.getAttribute('data-details'));
            document.getElementById('detailsContent').textContent =
                JSON.stringify(details, null, 2);

            const modal = document.getElementById('detailsModal');
            modal.classList.remove('hidden');
            modal.classList.add('block');
        }

        function closeModal() {
            const modal = document.getElementById('detailsModal');
            modal.classList.remove('block');
            modal.classList.add('hidden');
        }
    </script>
</x-admin-layout>
