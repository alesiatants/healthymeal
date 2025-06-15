<section>
<header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
        {{ __('Заявка на повышение роли до диетолога') }}
    </h2>
</header>
    @if($applications->isEmpty() || $applications->first()->status === 'Отклонена')
    <div class="rounded-lg shadow p-6 mb-6">
        <form action="{{ route('profile.application.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-400 mb-2">Прикрепите докумен, подтверждающий Вашу квалификацию в сфере диетологии</label>
                <img id="documentPreview" class="w-80 h-80 mb-2 object-cover hidden" style="display: none;">
                 <input type="file" name="document" id="documentInput" class="w-full p-2" accept="image/*">
                @error('document')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <x-danger-button>
                Подать заявку
        </x-danger-button>
        </form>
    </div>
    @endif
    @if($applications->isNotEmpty())
    <div class="bg-gray-300 rounded-lg shadow p-1 mb-5">
        <h2 class="text-xl font-semibold mb-4">Мои заявки</h2>
        <div class="overflow-x-auto text-xs">
            <table class="min-w-full bg-gray-300">
                <thead>
                    <tr class="text-center">
                        <th class="py-2 px-2 border-b">Дата</th>
                        <th class="py-2 px-4 border-b">Документ</th>
                        <th class="py-2 px-4 border-b">Статус</th>
                        <th class="py-2 px-4 border-b">Комментарий</th>
                        <th class="py-2 px-4 border-b">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $application)
                    <tr class="text-center">
                        <td class="py-2 px-2 border-b align-middle">{{ $application->created_at->format('d.m.Y H:i') }}</td>
                        <td class="py-2 px-4 border-b align-middle">
                            <a href="{{ asset('storage/' . $application->document) }}" target="_blank" class="text-blue-500 hover:underline inline-flex justify-center items-center">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                        <td class="py-2 px-4 border-b align-middle">
                            <div class="flex justify-center">
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
                            </div>
                        </td>
                        <td class="py-2 px-4 border-b align-middle">{{ $application->admin_comment ?? '-' }}</td>
                        <td class="py-2 px-4 border-b align-middle">
                            <div class="flex justify-center items-center space-x-2">
                                @if($application->status === 'Новая')
                                <form action="{{ route('profile.application.update', $application) }}" method="POST" enctype="multipart/form-data" class="update-form" id="update-form-{{ $application->id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" name="document" required class="hidden" id="document-{{ $application->id }}" onchange="this.closest('form').submit()">
                                    <label for="document-{{ $application->id }}" class="cursor-pointer">
                                        <i class="fas fa-edit text-blue-500 hover:text-blue-700"></i>
                                    </label>
                                </form>
                                <form action="{{ route('profile.application.delete', $application) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" 
                                            onclick="return confirm('Вы уверены, что хотите удалить эту заявку?')">
                                            <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    <h2 class="text-xl text-gray-400 mb-2">При возникновении вопросов обратитесь по телефону <br> <a href="tel:+78001234567" class="text-xl font-semibold text-gray-500 hover:text-red-500 transition-colors">
        8 (800) 123-45-67
      </a></h2>
    <script>
        document.getElementById('documentInput').addEventListener('change', function(e) {
            alert
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('documentPreview');
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</section>