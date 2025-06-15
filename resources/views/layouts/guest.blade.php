<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dropdown.js', 'resources/js/hint.js'])
    <script src="https://kit.fontawesome.com/890a250b1c.js" crossorigin="anonymous"></script>
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/">
                <img src={{ asset('images/logo.png') }} alt="logo" class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');

            phoneInput.addEventListener('input', function(event) {
                let value = event.target.value.replace(/\D/g, ''); // Удаляем все нецифровые символы
                let formattedValue = '+7';

                if (value.length > 1) {
                    value = value.substring(1); // Убираем первую цифру, так как она уже добавлена (+7)
                }

                if (value.length > 0) {
                    formattedValue += '-' + value.substring(0, 3); // Добавляем первые 3 цифры
                }
                if (value.length > 3) {
                    formattedValue += '-' + value.substring(3, 6); // Добавляем следующие 3 цифры
                }
                if (value.length > 6) {
                    formattedValue += '-' + value.substring(6, 8); // Добавляем следующие 2 цифры
                }
                if (value.length > 8) {
                    formattedValue += '-' + value.substring(8, 10); // Добавляем последние 2 цифры
                }

                event.target.value = formattedValue;
            });
        });
    </script>
</body>
<footer class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-100 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-8">
        <!-- Brand Info -->
        <div class="text-center md:text-left">
            <h3 class="text-2xl font-bold bg-gradient-to-r from-red-600 to-yellow-500 bg-clip-text text-transparent">
                HealthyMeal</h3>
          <p class="text-gray-600 mt-2">Платформа для организации здорового питания</p>
        </div>
        
        <!-- Contacts -->
        <div class="text-center md:text-right">
          <a href="tel:+78001234567" class="text-xl font-semibold text-gray-500 hover:text-red-500 transition-colors">
            8 (800) 123-45-67
          </a>
          <p class="text-gray-500 mt-1">Ежедневно с 9:00 до 21:00</p>
        </div>
      </div>
      
      <!-- Copyright -->
      <div class="border-t border-gray-200 pt-6 text-center text-gray-500 text-sm">
        <p>© 2025 HealthyMeal. Все права защищены.</p>
      </div>
    </div>
  </footer>
  
</html>
