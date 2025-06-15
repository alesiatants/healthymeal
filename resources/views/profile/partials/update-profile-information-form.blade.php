<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Информация профиля') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Обновите данные Вашего профиля.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('ФИО')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Почта')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Перейдите для переотправки верификационного письма на почту.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Новая верификационная ссылка была отправлена по почте.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        <div>
            @php
                $formatted = $user->phone
                    ? preg_replace('/^(\d)(\d{3})(\d{3})(\d{2})(\d{2})$/', '+$1-$2-$3-$4-$5', $user->phone)
                    : '';
            @endphp
            <x-input-label for="phone" :value="__('Телефон')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', $formatted)"
                placeholder="+7-XXX-XXX-XX-XX" pattern="\+7-\d{3}-\d{3}-\d{2}-\d{2}" required utofocus
                autocomplete="phone" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="image" :value="__('Фото профиля')" />
            @if ($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" id="imagePreview"
                    class="mb-2 w-40 h-40 rounded-full object-cover">
            @else
                <img id="imagePreview" class="w-40 h-40 mb-2 rounded-full object-cover hidden" style="display: none;">
            @endif
            <input type="file" name="image" id="imageInput" class="w-full p-2" accept="image/*">
            <x-input-error :messages="$errors->get('image')" class="mt-2" />

        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Сохранить') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Сохранено.') }}</p>
            @endif
        </div>
    </form>
    <script>
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</section>
