@extends('layouts.admin')

@section('content')

<h2 class="text-3xl font-bold mb-6">Pesanan</h2>

@if(session('success'))
    <div class="bg-green-200 p-3 rounded mb-4">{{ session('success') }}</div>
@endif

<button id="openModalBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4">+ Tambah Pesanan</button>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg w-96 p-6 relative">
        <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Pesanan</h3>
        <form id="transactionForm" method="POST" action="{{ route('admin.transactions.save') }}">
            @csrf
            <input type="hidden" name="transaction_id" id="transaction_id">
            <label class="block mb-2">
                Nama Pelanggan
                <input type="text" name="customer_name" id="customer_name" class="w-full border p-2 rounded" required />
            </label>
            <label class="block mb-2">
                Total Harga
                <input type="number" name="total_price" id="total_price" class="w-full border p-2 rounded" required min="0" />
            </label>
            <label class="block mb-2">
                Status
                <select name="status" id="status" class="w-full border p-2 rounded" required>
                    <option value="pending">Tertunda</option>
                    <option value="paid">Dibayar</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </label>
            <label class="block mb-2">
                Tipe Pembayaran 
                <select name="payment_type" id="payment_type" class="w-full border p-2 rounded" required>
                    <option value="cash">Cash</option>
                    <option value="qris">QRIS</option>
                </select>
            </label>
            <label class="block mb-4">
                Nomor Meja 
                <select name="table_number" id="table_number" class="w-full border p-2 rounded">
                    <option value="">-- Pilih Meja --</option>
                    @foreach($tables as $table)
                        <option value="{{ $table->number }}">Meja {{ $table->number }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex justify-end space-x-2">
                <button type="button" id="closeModalBtn" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
<a href="{{ route('admin.transactions.export') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
    📥 Download Laporan Excel
</a>
<!-- Table -->
<table class="min-w-full bg-white shadow rounded overflow-hidden">
    <thead class="bg-green-700 text-white">
        <tr>
            <th class="py-3 px-6 text-left">Nama Pelanggan</th>
            <th class="py-3 px-6 text-left">Tipe Pembayaran</th>
            <th class="py-3 px-6 text-left">Nomor Meja</th>
            <th class="py-3 px-6 text-left">Total Harga</th>
            <th class="py-3 px-6 text-left">Status</th>
            <th class="py-3 px-6 text-left">Waktu Pemesanan</th>
            <th class="py-3 px-6 text-left">Detail</th>
            <th class="py-3 px-6 text-left">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions as $transaction)
        <tr class="border-b hover:bg-green-50">
            <td class="py-3 px-6">{{ $transaction->customer_name }}</td>
            <td class="py-3 px-6">{{ ucfirst($transaction->payment_type) }}</td>
            <td class="py-3 px-6">{{ $transaction->table_number ?? '-' }}</td>
            <td class="py-3 px-6">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
            <td class="py-3 px-6">
                @if($transaction->status == 'pending')
                    <span class="text-yellow-600 font-semibold">Tertunda</span>
                @elseif($transaction->status == 'paid')
                    <span class="text-green-600 font-semibold">Dibayar</span>
                @elseif($transaction->status == 'cancelled')
                    <span class="text-red-600 font-semibold">Dibatalkan</span>
                @else
                    <span class="text-gray-600">Unknown</span>
                @endif
            </td>
            <td class="py-3 px-6">
                {{ \Carbon\Carbon::parse($transaction->created_at)->translatedFormat('d F Y H:i') }}
            </td>

            <td class="py-3 px-6">
                <button onclick="toggleDetail({{ $transaction->id }})" class="hover:text-indigo-700" title="Lihat Detail">
    <span class="inline-flex items-center space-x-1">
        <!-- Mata -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 hover:text-indigo-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
        </svg>
        <!-- Panah ke bawah -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600 hover:text-indigo-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </span>
</button>

            </td>
            <td class="py-3 px-6 space-x-2">
                <button 
                    class="editBtn text-blue-600 hover:underline"
                    data-id="{{ $transaction->id }}"
                    data-customer_name="{{ $transaction->customer_name }}"
                    data-total_price="{{ $transaction->total_price }}"
                    data-status="{{ $transaction->status }}"
                    data-payment_type="{{ $transaction->payment_type }}"
                    data-table_number="{{ $transaction->table_number }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 hover:text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M16.768 4.768a2.5 2.5 0 113.536 3.536L7 21H3v-4L16.768 4.768z" />
                    </svg>
                </button>
                <form method="POST" action="{{ route('admin.transactions.destroy', $transaction) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 hover:text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4" />
                        </svg>
                    </button>
                </form>
            </td>
        </tr>
        <!-- Detail Row -->
        <tr id="detail-{{ $transaction->id }}" class="hidden bg-gray-50">
            <td colspan="6" class="p-4">
                <h4 class="font-semibold mb-2">Detail Pesanan:</h4>
                <ul class="list-disc list-inside space-y-1">
                    @forelse($transaction->items as $item)
                        <li>
                            {{ $item->product->name ?? 'Produk tidak tersedia' }} (x{{ $item->quantity }}) - 
                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </li>
                    @empty
                        <li class="text-gray-500">Tidak ada item.</li>
                    @endforelse
                </ul>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-4 text-gray-500">No transactions found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<script>
    const modal = document.getElementById('modal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('transactionForm');

    const transactionId = document.getElementById('transaction_id');
    const customerName = document.getElementById('customer_name');
    const totalPrice = document.getElementById('total_price');
    const status = document.getElementById('status');
    const paymentType = document.getElementById('payment_type');
    const tableNumber = document.getElementById('table_number');

    openModalBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Tambah Pesanan';
        form.reset();
        if (transactionId) transactionId.value = '';
        modal.classList.remove('hidden');
    });

    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            modalTitle.textContent = 'Edit Pesanan';
            if (transactionId) transactionId.value = btn.dataset.id;
            customerName.value = btn.dataset.customer_name;
            totalPrice.value = btn.dataset.total_price;
            status.value = btn.dataset.status;
            paymentType.value = btn.dataset.payment_type;
            tableNumber.value = btn.dataset.table_number ?? '';
            modal.classList.remove('hidden');
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    document.querySelectorAll('form.inline').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Hapus Pesanan Ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    function toggleDetail(id) {
        const detailRow = document.getElementById('detail-' + id);
        detailRow.classList.toggle('hidden');
    }
</script>

@endsection
