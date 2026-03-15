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
        <script src="https://cdn.tailwindcss.com"></script>
        <style type="text/tailwindcss">
            body {
                @apply bg-[#0f172a] text-slate-200;
            }
            .glass-card {
                background: rgba(30, 41, 59, 0.6);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.05);
                @apply rounded-2xl shadow-xl;
            }
        </style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0f172a] text-slate-200">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#0f172a]">
            <div class="mb-8">
                <a href="/">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-sky-400 to-indigo-400 bg-clip-text text-transparent">
                        MyPawnShop
                    </h1>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 glass-card">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
