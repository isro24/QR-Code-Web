@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center md:text-left">{{ $title }}</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if(count($cart) > 0)
        <!-- Tabel Desktop -->
        <div class="hidden md:block">
            <table class="w-full table-auto border-collapse border border-gray-300 shadow-sm rounded">
                <thead>
                    <tr class="bg-gray-100 text-sm text-gray-700">
                        <th class="border px-4 py-2 text-left">Gambar</th>
                        <th class="border px-4 py-2 text-left">Nama Produk</th>
                        <th class="border px-4 py-2 text-right">Harga</th>
                        <th class="border px-4 py-2 text-center">Jumlah</th>
                        <th class="border px-4 py-2 text-right">Subtotal</th>
                        <th class="border px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach($cart as $key => $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        @endphp
                        <tr class="hover:bg-gray-50 text-sm">
                            <td class="border px-4 py-2">
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-20 h-20 object-cover rounded">
                            </td>
                            <td class="border px-4 py-2">{{ $item['name'] }}</td>
                            <td class="border px-4 py-2 text-right">Rp {{ number_format($item['price'],0,',','.') }}</td>
                            <td class="border px-4 py-2 text-center">
                                <div class="flex items-center justify-center space-x-2" data-product-id="{{ $item['id'] }}">
                                    <button type="button" class="bg-red-500 text-white px-2 py-1 rounded text-sm font-bold update-cart" data-action="decrease">&minus;</button>
                                    <span class="quantity-display">{{ $item['quantity'] }}</span>
                                    <button type="button" class="bg-green-500 text-white px-2 py-1 rounded text-sm font-bold update-cart" data-action="increase">+</button>
                                </div>
                            </td>
                            <td class="border px-4 py-2 text-right" id="subtotal-{{ $item['id'] }}">Rp {{ number_format($subtotal,0,',','.') }}</td>
                            <td class="border px-4 py-2 text-center">
                                <a href="{{ route('cart.remove', $key) }}" class="text-red-500 hover:underline" onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')">Hapus</a>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-100 font-bold text-sm">
                        <td colspan="4" class="text-right px-4 py-2">Total</td>
                        <td class="text-right px-4 py-2" id="total-cart">Rp {{ number_format($total,0,',','.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Versi Mobile -->
        <div class="md:hidden space-y-4">
            @foreach($cart as $key => $item)
                @php
                    $subtotal = $item['price'] * $item['quantity'];
                @endphp
                <div class="border rounded-lg p-4 shadow-sm flex gap-4 items-center">
                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-20 h-20 object-cover rounded">
                    <div class="flex-1">
                        <h2 class="text-base font-semibold">{{ $item['name'] }}</h2>
                        <p class="text-sm text-gray-600">Harga: Rp {{ number_format($item['price'],0,',','.') }}</p>
                        <div class="flex items-center space-x-2 mt-1" data-product-id="{{ $item['id'] }}">
                            <button type="button" class="bg-red-500 text-white px-2 py-1 rounded text-sm font-bold update-cart" data-action="decrease">&minus;</button>
                            <span class="quantity-display">{{ $item['quantity'] }}</span>
                            <button type="button" class="bg-green-500 text-white px-2 py-1 rounded text-sm font-bold update-cart" data-action="increase">+</button>
                        </div>
                        <p class="text-sm font-semibold mt-1" id="subtotal-{{ $item['id'] }}">Subtotal: Rp {{ number_format($subtotal,0,',','.') }}</p>
                        <a href="{{ route('cart.remove', $key) }}" class="text-red-500 text-sm hover:underline block mt-2" onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')">Hapus</a>
                    </div>
                </div>
            @endforeach

            <div class="text-right text-lg font-bold">
                Total: <span id="total-cart">Rp {{ number_format($total,0,',','.') }}</span>
            </div>
        </div>

        <div class="mt-6 flex justify-between items-center">
            <a href="{{ url()->previous() }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-6 py-3 rounded transition duration-200">
                &larr; Kembali
            </a>

            <a href="{{ route('checkout') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded transition duration-200">
                Lanjut ke Checkout
            </a>
        </div>

    @else
        <div class="text-center py-10">
            <p class="text-gray-600 mb-4">Keranjang kosong.</p>
            <a href="{{ route('product') }}" class="text-blue-600 font-semibold hover:underline">Kembali ke produk</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.update-cart').forEach(button => {
        button.addEventListener('click', async function () {
            const container = this.closest('[data-product-id]');
            const productId = container.getAttribute('data-product-id');
            const action = this.getAttribute('data-action');

            const response = await fetch(`/cart/update/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action })
            });

            if (response.ok) {
                const result = await response.json();

                if (result.quantity !== undefined) {
                    const qtySpan = container.querySelector('.quantity-display');
                    qtySpan.innerText = result.quantity;

                    if (result.subtotal_html) {
                        document.getElementById(`subtotal-${productId}`).innerHTML = result.subtotal_html;
                    }

                    if (result.total_html) {
                        document.getElementById(`total-cart`).innerHTML = result.total_html;
                    }

                    if (result.quantity === 0) {
                        const row = container.closest('tr') || container.closest('.border.rounded-lg');
                        if (row) row.remove();
                    }
                }
            }
        });
    });
});
</script>
@endpush
