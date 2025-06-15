<x-admin-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-400">Заявки на роль диетолога</h1>

        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-4 border-b border-gray-200">
                <form class="flex flex-wrap gap-4 items-end">
                    <div class="w-full md:w-auto">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                        <select name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Все статусы</option>
                            <option value="Новая" {{ request('status') === 'Новая' ? 'selected' : '' }}>Новая</option>
                            <option value="В обработке" {{ request('status') === 'В обработке' ? 'selected' : '' }}>В обработке</option>
                            <option value="Подтверждена" {{ request('status') === 'Подтверждена' ? 'selected' : '' }}>Подтверждена</option>
                            <option value="Отклонена" {{ request('status') === 'Отклонена' ? 'selected' : '' }}>Отклонена</option>
                        </select>
                    </div>
                    <div class="w-full md:w-auto">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email пользователя</label>
                        <input type="email" name="email" class="mt-1 block w-full shadow-sm sm:text-sm rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Email пользователя" value="{{ request('email') }}">
                    </div>
                    <div class="w-full md:w-auto">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Дата</label>
                        <input type="date" name="date" class="mt-1 block w-full shadow-sm sm:text-sm rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" value="{{ request('date') }}">
                    </div>
                    <div class="w-full md:w-auto flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Фильтровать
                        </button>
                        <a href="{{ route('admin.applications.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Сбросить
                        </a>
                    </div>
                </form>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Пользователь</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата подачи</th>
<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Документ</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Обработал</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($applications as $application)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $application->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $application->user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $application->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ asset('storage/' . $application->document) }}" target="_blank" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                        <i class="fas fa-eye mr-1"></i> Просмотреть
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($application->status === 'Новая') bg-blue-100 text-blue-800
                                        @elseif($application->status === 'В обработке') bg-yellow-100 text-yellow-800
                                        @elseif($application->status === 'Подтверждена') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($application->status === 'Новая') Новая
                                        @elseif($application->status === 'В обработке') В обработке
                                        @elseif($application->status === 'Подтверждена') Подтверждена
                                        @else Отклонена
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($application->admin)
                                    {{ $application->admin->name }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($application->status !== 'Подтверждена' && $application->status !== 'Отклонена')
                                    <div class="flex items-center space-x-2">
                                        @if($application->status == 'В обработке')
                                        <form action="{{ route('admin.applications.update', $application) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Подтверждена">
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Подтвердить">
                                                <i class="fas fa-check-circle text-xl"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.applications.update', $application) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="Отклонена">
                                            <div class="flex flex-col"> <!-- Изменено на flex-col -->
                                                <div class="flex items-center"> <!-- Обернули input и кнопку в отдельный div -->
                                                    <input type="text" name="comment" class="text-xs border rounded px-2 py-1 w-32" placeholder="Комментарий">
                                                    <button type="submit" class="text-red-600 hover:text-red-900 ml-1" title="Отклонить">
                                                        <i class="fas fa-times-circle text-xl"></i>
                                                    </button>
                                                </div>
                                                
                                            </div>
                        
                                        </form>
                                        @else
                                        <form action="{{ route('admin.applications.update', $application) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="В обработке">
                                            <div class="flex items-center">
                                                <button type="submit" class="text-yellow-600 ml-1" title="Взять в обработку">
                                                    <i class="fas fa-hourglass-start text-xl"></i>
                                                </button>
                                            </div>
                                        </form>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-gray-400">Завершено</span>
                                    @endif
                                    <x-input-error :messages="$errors->get('comment')" class="mt-1 text-xs" />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
