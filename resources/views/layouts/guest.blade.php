<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Login' }}</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .font-staatliches {
            font-family: 'Staatliches', cursive;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-cover bg-center" style="background-image: url('https://source.unsplash.com/featured/?office,technology')">
    @yield('content')
</body>
</html>
