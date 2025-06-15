<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Имя')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Почта')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>


        <!-- Birth Date -->
        <div class="mt-4">
            <x-input-label for="birth_date" :value="__('Дата дня рождения')" />
            <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" min="1990-01-01"
                max="2010-12-31" :value="old('birth_date')" required />
            <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
        </div>
        <!-- Gender -->
        <div class="mt-4">
            <fieldset>
                <x-input-label class="text-lg font-medium mb-4" :value="__('Пол')" />
                <div class="flex justify-center space-x-4"> <!-- Horizontal flex container -->
                    <div class="flex items-center" style="margin-right: 30%;">
                        <input type="radio" id="male" name="gender" value="Мужской" style="margin-right: 20%;"
                            @if (old('gender') == 'Мужской') checked @endif>
                        <x-input-label for="male" class="cursor-pointer peer-checked:font-bold"
                            :value="__('Мужской')" />
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="female" name="gender" value="Женский" style="margin-right: 20%;"
                            @if (old('gender') == 'Женский') checked @endif>
                        <x-input-label for="female" class="cursor-pointer peer-checked:font-bold"
                            :value="__('Женский')" />
                    </div>
                </div>
            </fieldset>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Телефон')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')"
                placeholder="+7-XXX-XXX-XX-XX" pattern="\+7-\d{3}-\d{3}-\d{2}-\d{2}" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Пароль')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Подтвердите пароль')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}">
                {{ __('Уже зарегистрированы?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Регистрация') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
