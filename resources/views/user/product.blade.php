@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ $title }}</h1>
    @if(session('table_number'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            Kamu memesan dari <strong>Meja #{{ session('table_number') }}</strong>
        </div>
    @endif


    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-200 text-red-800 p-3 rounded mb-6">{{ session('error') }}</div>
    @endif

    <form action="{{ route('product') }}" method="GET" class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-4">
            <!-- Input Pencarian -->
            <input
                type="text"
                name="search"
                placeholder="Cari produk..."
                value="{{ request('search') }}"
                class="px-4 py-2 border rounded w-full sm:w-1/3 mb-2 sm:mb-0"
            />

            <!-- Dropdown Kategori -->
            <select
                name="category"
                class="px-4 py-2 border rounded w-full sm:w-1/4 mb-2 sm:mb-0"
            >
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <!-- Tombol Cari -->
            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full sm:w-auto"
            >
                Cari
            </button>
        </div>
    </form>

    @foreach($products as $category => $items)
        <section class="mb-10">
            <h2 class="text-xl font-semibold mb-4">{{ $category }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                @foreach($items as $product)
                <div class="border rounded shadow p-4 flex flex-col items-center">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-48 h-48 object-cover mb-4">
                    <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
                    <!-- <p class="text-sm text-gray-600 mb-1">Stok: {{ $product->stock }}</p> -->
                    <p class="text-blue-700 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    @php
                        $cart = session('cart', []);
                        $existing = collect($cart)->firstWhere('id', $product->id);
                    @endphp

                    <div class="mt-4 flex justify-center items-center gap-2">
                        @if($product->stock > 0)
                        @if($existing)
                            <div class="flex items-center gap-2" data-product-id="{{ $product->id }}">
                                <button class="bg-red-500 text-white px-3 py-1 rounded text-lg font-bold update-cart" data-action="decrease">−</button>
                                <span class="font-semibold quantity-display">{{ $existing['quantity'] }}</span>
                                <button class="bg-green-500 text-white px-3 py-1 rounded text-lg font-bold update-cart" data-action="increase">+</button>
                            </div>
                        @else
                            <form action="{{ route('cart.add', $product->id) }}" method="GET">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah ke Keranjang</button>
                            </form>
                        @endif
                    @else
                        <span class="text-red-600 font-semibold">Tidak Tersedia</span>
                    @endif

                    </div>
                </div>
                @endforeach
            </div>
        </section>
    @endforeach

   <div class="fixed bottom-5 right-5">
    <a href="{{ route('cart') }}" class="bg-green-600 text-white px-4 py-3 rounded shadow hover:bg-green-700 inline-flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="w-5 h-5" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1 5h12l-1-5M9 21h.01M15 21h.01" />
        </svg>
        <span>Keranjang {{ count(session('cart', [])) }}</span>
    </a>
</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.update-cart').forEach(button => {
        button.addEventListener('click', async function () {
            const productDiv = this.closest('[data-product-id]');
            const productId = productDiv.getAttribute('data-product-id');
            const action = this.getAttribute('data-action');

            const response = await fetch(`/cart/update/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action })
            });

            if (response.ok) {
                // reload cart count OR update display only
                const result = await response.text(); // karena updateCart() redirect back
                location.reload(); // atau bisa kamu manipulasi DOM kalau mau realtime tanpa reload
            }
        });
    });
});
</script>

@endsection
