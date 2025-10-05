@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg">
    <h1 class="text-3xl font-bold mb-6">{{ $title }}</h1>
<a href="{{ url()->previous() }}"
   class="inline-block mb-4 text-sm text-blue-600 hover:underline hover:text-blue-800">
   ← Kembali ke halaman sebelumnya
</a>    

    @if(session('error'))
        <div class="bg-red-200 text-red-800 p-3 rounded mb-6">{{ session('error') }}</div>
    @endif

    <form action="{{ route('checkout.submit') }}" method="POST" class="space-y-6 bg-white p-6 rounded shadow">
        @csrf

        {{-- Nama Customer --}}
        <div>
            <label for="customer_name" class="block font-semibold mb-1">Nama Customer</label>
            <input type="text"
                   id="customer_name"
                   name="customer_name"
                   value="{{ old('customer_name') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2"
                   required>
            @error('customer_name')
                <p class="text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pilih Meja --}}
        <div>
            <label for="table_number" class="block font-semibold mb-1">Pilih Meja</label>

            @php $currentTable = old('table_number', session('table_number')); @endphp

            <select id="table_number"
                    class="w-full border border-gray-300 rounded px-3 py-2"
                    {{ session('table_number') ? 'disabled' : 'required' }}>
                <option value="">-- Pilih Meja --</option>
                @foreach($tables as $table)
                    <option value="{{ $table->number }}"
                        {{ $currentTable == $table->number ? 'selected' : '' }}>
                        Meja {{ $table->number }}
                    </option>
                @endforeach
            </select>

            @if(session('table_number'))
                <input type="hidden" name="table_number" value="{{ session('table_number') }}">
                <p class="text-sm text-green-700 mt-1">Anda memesan dari <strong>Meja #{{ session('table_number') }}</strong>.</p>
            @endif

            @error('table_number')
                <p class="text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- Metode Pembayaran --}}
        <div>
            <label for="payment_method" class="block font-semibold mb-1">Metode Pembayaran</label>
            <select name="payment_method" id="payment_method"
                    class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>Pilih metode pembayaran</option>
                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
            </select>
            @error('payment_method')
                <p class="text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ringkasan Pesanan --}}
        <div>
            <h2 class="text-xl font-semibold mb-2">Ringkasan Pesanan</h2>

            @php $total = 0; @endphp

            @if(is_array($cart) && count($cart))
                <div class="space-y-4 max-h-60 overflow-auto border rounded bg-gray-50 p-3">
                    @foreach($cart as $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        @endphp
                        <div class="flex justify-between items-start border-b pb-2">
                            <div>
                                <p class="font-semibold">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-600">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="font-semibold text-right text-blue-700">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="font-bold text-right mt-4">Total Harga: Rp {{ number_format($total, 0, ',', '.') }}</p>
            @else
                <p class="text-gray-500 italic">Keranjang kosong.</p>
            @endif
        </div>

        {{-- Tombol Submit --}}
       <button type="button" id="payNowBtn" class="w-full bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700">
            Bayar Sekarang
        </button>
    </form>
</div>

<!-- Modal QRIS -->
<div id="qrisModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-80 text-center relative transform transition duration-200 scale-100">
        <h2 class="text-xl font-semibold mb-4">Silakan Scan QRIS</h2>

        <div class="mb-4">
            <a href="{{ asset('qris/qris_example.png') }}" download="qris.png" target="_blank">
                <img src="{{ asset('qris/qris_example.png') }}"
                     alt="QRIS"
                     class="mx-auto w-64 h-auto border rounded hover:scale-105 transition"
                     title="Klik kanan untuk download atau tekan tombol di bawah">
            </a>
            <a href="{{ asset('qris/qris_example.png') }}" download="qris.png" class="block mt-2 text-blue-600 hover:underline text-sm">
                📥 Download QRIS
            </a>
        </div>

        <button id="confirmQrisBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Selesai
        </button>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const payNowBtn = document.getElementById('payNowBtn');
    const form = document.querySelector('form');
    const paymentMethod = document.getElementById('payment_method');
    const qrisModal = document.getElementById('qrisModal');
    const confirmQrisBtn = document.getElementById('confirmQrisBtn');

    payNowBtn.addEventListener('click', function () {
        const method = paymentMethod.value;

        if (!method) {
            alert('Silakan pilih metode pembayaran terlebih dahulu.');
            return;
        }

        if (method === 'qris') {
            // Tampilkan QRIS modal
            qrisModal.classList.remove('hidden');
        } else if (method === 'cash') {
            // Langsung submit
            form.submit();
        }
    });

    confirmQrisBtn.addEventListener('click', function () {
        // Tutup modal dan submit
        qrisModal.classList.add('hidden');
        form.submit();
    });
});
</script>

@endsection
