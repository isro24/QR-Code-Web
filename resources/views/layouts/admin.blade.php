<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Admin Panel' }}</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <!-- Tailwind via CDN (JS config) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <style>
        .sidebar-font {
            font-family: 'Staatliches', cursive;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col md:flex-row">

    <!-- Navbar for Mobile -->
    <header class="bg-green-700 text-white p-4 flex justify-between items-center md:hidden fixed top-0 left-0 right-0 z-50">
        <h1 class="text-2xl sidebar-font">Admin Panel</h1>
        <button id="toggleSidebar" class="focus:outline-none">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-green-700 text-white transform -translate-x-full md:translate-x-0 md:relative md:flex md:flex-col z-50 transition-transform duration-300 ease-in-out">
        <div class="p-6 flex items-center justify-center sidebar-font text-3xl font-bold tracking-wide border-b border-green-600">
            Admin Panel
        </div>
        <nav class="flex-grow mt-6">
            <a href="{{ route('admin.dashboard') }}" 
                class="block px-6 py-3 hover:bg-green-600 {{ request()->routeIs('admin.dashboard') ? 'bg-green-900 font-semibold' : '' }}">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" 
                class="block px-6 py-3 hover:bg-green-600 {{ request()->routeIs('admin.products.*') ? 'bg-green-900 font-semibold' : '' }}">
                <i class="fas fa-box-open mr-2"></i> Produk
            </a>
            <a href="{{ route('admin.categories.index') }}" 
                class="block px-6 py-3 hover:bg-green-600 {{ request()->routeIs('admin.categories.*') ? 'bg-green-900 font-semibold' : '' }}">
                <i class="fas fa-tags mr-2"></i> Kategori
            </a>
            <a href="{{ route('admin.transactions.index') }}" 
                class="block px-6 py-3 hover:bg-green-600 {{ request()->routeIs('admin.transactions.*') ? 'bg-green-900 font-semibold' : '' }}">
                <i class="fas fa-file-invoice-dollar mr-2"></i> Pesanan
            </a>
            <a href="{{ route('admin.tables.index') }}" 
                class="block px-6 py-3 hover:bg-green-600 {{ request()->routeIs('admin.tables.*') ? 'bg-green-900 font-semibold' : '' }}">
                <i class="fas fa-chair mr-2"></i> Meja
            </a>
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="px-6 py-4 border-t border-green-600">
            @csrf
            <button type="submit" class="w-full text-left hover:bg-green-600 py-2 rounded font-semibold flex items-center">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </form>
    </aside>

    <!-- Overlay (only for mobile sidebar) -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

    <!-- Main content -->
    <main class="flex-grow p-6 pt-20 md:pt-6 w-full">
        @yield('content')
    </main>

    <!-- Toggle Sidebar Script -->
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        toggleBtn.addEventListener('click', () => {
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
</body>
</html>
