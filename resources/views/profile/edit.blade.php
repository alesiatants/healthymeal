@if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
    <x-admin-layout>
        @include('profile.content')
    </x-admin-layout>
@else
    <x-app-layout>
        @include('profile.content')
    </x-app-layout>
@endif
