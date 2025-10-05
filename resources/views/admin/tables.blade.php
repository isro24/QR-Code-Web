@extends('layouts.admin')

@section('content')

<h2 class="text-3xl font-bold mb-6">Meja</h2>

@if(session('success'))
    <div class="bg-green-200 p-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="flex justify-between items-center mb-4">
    <!-- Tombol Tambah (di kiri) -->
    <button id="openModalBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        + Tambah Meja
    </button>
    <!-- Form Centang Beberapa Meja -->
    <form id="bulkAvailableForm" method="POST" action="{{ route('admin.tables.setSelectedAvailable') }}" onsubmit="return confirm('Jadikan semua meja yang dipilih tersedia?')">
        @csrf
        <div class="flex justify-end mb-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Jadikan Meja Terpilih Tersedia
            </button>
        </div>
    </form>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white rounded-lg w-96 p-6 relative">

        <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Meja</h3>

        <form id="tableForm" method="POST" action="{{ route('admin.tables.save') }}">
            @csrf
            <input type="hidden" name="id" id="id">

            <label class="block mb-2">
                Nomor Tabel
                <input type="number" name="number" id="number" class="w-full border p-2 rounded" required min="0" />
            </label>

            <label class="block mb-4">
                Status
                <select name="status" id="status" class="w-full border p-2 rounded" required>
                    <option value="available">Tersedia</option>
                    <option value="unavailable">Tidak Tersedia</option>
                </select>
            </label>

            <div class="flex justify-end space-x-2">
                <button type="button" id="closeModalBtn" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            </div>
        </form>

    </div>

</div>

<!-- Table List -->
<table class="min-w-full bg-white shadow rounded overflow-hidden">
    <thead class="bg-green-700 text-white">
        <tr>
            <th class="py-3 px-6 text-left">
                <input type="checkbox" id="checkAll">
            </th>
            <th class="py-3 px-6 text-left">Nomor Meja</th>
            <th class="py-3 px-6 text-left">Status</th>
            <th class="py-3 px-6 text-left">Aksi</th>
            <th class="py-3 px-6 text-left">QR-Code</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($tables as $table)
        <tr class="border-b hover:bg-green-50">
            <td class="py-3 px-6">
                <input type="checkbox" class="table-checkbox" name="table_ids[]" value="{{ $table->id }}" form="bulkAvailableForm">
            </td>
            <td class="py-3 px-6">{{ $table->number }}</td>
            <td class="py-3 px-6">
                @if($table->status == 'available')
                    <span class="text-green-600 font-semibold">Tersedia</span>
                @else
                    <span class="text-red-600 font-semibold">Tidak Tersedia</span>
                @endif
            </td>
            <td class="py-3 px-6 space-x-2">
                <button 
                    class="editBtn text-blue-600 hover:underline"
                    data-id="{{ $table->id }}"
                    data-number="{{ $table->number }}"
                    data-status="{{ $table->status }}"
                >
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 hover:text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M16.768 4.768a2.5 2.5 0 113.536 3.536L7 21H3v-4L16.768 4.768z" />
                    </svg>
                </button>

                <form method="POST" action="{{ route('admin.tables.destroy', $table) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure to delete this table?')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 hover:text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4" />
                        </svg>
                    </button>
                </form>
            </td>
            <td class="py-3 px-6 space-x-2">
                <a href="{{ route('admin.tables.qrcode', $table) }}" class="flex items-center text-black hover:underline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h6v6H4V4zM14 4h6v6h-6V4zM4 14h6v6H4v-6zM14 14h2v2h-2v-2zM18 14h2v2h-2v-2zM14 18h2v2h-2v-2z" />
                    </svg>
                </a>

            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center py-4 text-gray-500">No tables found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<script>
    const modal = document.getElementById('modal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('tableForm');

    const tableId = document.getElementById('id');
    const number = document.getElementById('number');
    const status = document.getElementById('status');

    openModalBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Tambah Meja';
        form.reset();
        tableId.value = '';
        modal.classList.remove('hidden');
    });

    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            modalTitle.textContent = 'Edit Meja';
            tableId.value = btn.dataset.id;
            number.value = btn.dataset.number;
            status.value = btn.dataset.status;
            modal.classList.remove('hidden');
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    document.getElementById('checkAll').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.table-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

</script>

@endsection
