@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-md bg-white shadow-xl rounded-lg">
    <div class="text-center mb-6">
        <div class="flex justify-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4 -4m5 2a9 9 0 1 1 -18 0a9 9 0 0 1 18 0z" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-green-700">Pesanan Berhasil!</h1>
        <p class="text-gray-600 mt-2">Terima kasih, <strong>{{ $order->customer_name }}</strong></p>
    </div>

    <div class="border-t pt-4 text-sm text-gray-600 space-y-1">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 8c1.656 0 3-1.344 3-3S13.656 2 12 2 9 3.344 9 5s1.344 3 3 3zM12 14v8M4.928 4.928L3.514 6.343M1 12h2m2.928 7.072l1.414-1.414M12 1v2m7.072 2.928l-1.414 1.414M23 12h-2m-2.928 7.072l-1.414-1.414" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Metode Pembayaran: <strong class="ml-1">{{ ucfirst($order->payment_method) }}</strong>
        </div>

        @if($order->table_number)
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M3 10h18M3 6h18M5 6v12m14-12v12M9 14h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Nomor Meja: <strong class="ml-1">{{ $order->table_number }}</strong>
        </div>
        @endif
    </div>

    <hr class="my-4">

    <div>
        <h2 class="text-xl font-semibold mb-3">🛒 Rincian Pesanan:</h2>
        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
            @php $total = 0; @endphp
            @foreach($order->items as $item)
                @php
                    $subtotal = $item->price * $item->quantity;
                    $total += $subtotal;
                @endphp
                <li>
                    <span class="font-medium">{{ $item->product->name }}</span> (x{{ $item->quantity }}) -
                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                </li>
            @endforeach
        </ul>

        <div class="mt-4 flex justify-between items-center border-t pt-3 text-lg font-bold">
            <span>Total:</span>
            <span class="text-green-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('product') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h7l-1-1m0 0l1-1m-1 1h13M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Kembali ke Produk
        </a>
    </div>
</div>
@endsection
