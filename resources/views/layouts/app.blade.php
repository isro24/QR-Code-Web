<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Website UMKM' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/flowbite@2.2.1/dist/flowbite.min.js"></script>

    <link href="https://unpkg.com/flowbite@2.2.1/dist/flowbite.min.css" rel="stylesheet" />


    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Staatliches', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    {{-- Tambahkan ini untuk menampilkan navigation --}}
    @include('layouts.navigation')

    {{-- Main Content --}}
    <main class="pt-24 px-4">
        @yield('content')
    </main>

</body>
</html>
