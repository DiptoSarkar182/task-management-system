<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

@if (!request()->is('login') && !request()->is('register'))
    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed w-full top-0 left-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="text-xl font-bold text-gray-700 hover:text-gray-900">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <!-- Navigation Links -->
                <div class="flex space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                        <a href="{{ route('logout') }}"
                           class="text-gray-700 hover:text-red-600"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Login</a>
                        <a href="{{ route('register') }}" class="text-gray-700 hover:text-green-600">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
@endif

<!-- Main Container -->
<div class="pt-16 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    @yield('content')
</div>

<!-- Footer -->
<footer class="bg-white mt-12 shadow-md py-4">
    <div class="text-center text-gray-600 text-sm">
        © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
    </div>
</footer>

</body>
</html>
