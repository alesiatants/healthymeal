@component('mail::message')
    # Обнаружены медленные запросы

    За последние 5 секунд обнаружены следующие запросы с временем выполнения более 1 с:

    @component('mail::table')
        | ID | Метод | URI | Время (с) | Дата |
        |----|-------|-----|-----------|------|
        @foreach ($slowRequests as $request)
        | {{ $request['id'] ?? 'N/A' }} | {{ $request['method'] ?? 'GET' }} | {{ $request['uri'] ?? '' }} | {{ $request['time']['duration'] ?? 0 }} | {{ $request['datetime'] ?? now() }} |
        @endforeach
    @endcomponent

    @component('mail::button', ['url' => url('/requests')])
    Перейти в панель отладки
    @endcomponent

@endcomponent