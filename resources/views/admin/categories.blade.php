@extends('layouts.admin')

@section('content')

<h1 class="text-3xl font-bold mb-6">Kategori</h1>

@if(session('success'))
    <div class="bg-green-200 p-3 rounded mb-4">{{ session('success') }}</div>
@endif

<button id="openModalBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4">+ Tambah Kategori</button>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white rounded-lg w-96 p-6 relative">

        <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Kategori</h3>

        <form id="categoryForm" method="POST" action="{{ route('admin.categories.save') }}">
            @csrf
            <input type="hidden" name="category_id" id="category_id">
            <label class="block mb-4">
                Nama
                <input type="text" name="name" id="name" class="w-full border p-2 rounded" required />
            </label>

            <div class="flex justify-end space-x-2">
                <button type="button" id="closeModalBtn" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            </div>
        </form>

    </div>

</div>

<!-- Table -->
<table class="min-w-full bg-white shadow rounded overflow-hidden">
    <thead class="bg-green-700 text-white">
        <tr>
            <th class="py-3 px-6 text-left">Nama</th>
            <th class="py-3 px-6 text-left">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($categories as $category)
        <tr class="border-b hover:bg-green-50">
            <td class="py-3 px-6">{{ $category->name }}</td>
            <td class="py-3 px-6 space-x-2 flex items-center">
                <button 
                    class="editBtn inline-flex items-center text-blue-600 hover:text-blue-800"
                    data-id="{{ $category->id }}"
                    data-name="{{ $category->name }}"
                    aria-label="Edit"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M16.768 4.768a2.5 2.5 0 113.536 3.536L7 21H3v-4L16.768 4.768z" />
                    </svg>
                </button>

                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-800" aria-label="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4" />
                        </svg>
                    </button>
                </form>
            </td>

        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center py-4 text-gray-500">No categories found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<script>
    const modal = document.getElementById('modal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('categoryForm');

    const categoryId = document.getElementById('category_id');
    const nameInput = document.getElementById('name');

    openModalBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Tambah Kategori';
        form.reset();
        categoryId.value = '';
        modal.classList.remove('hidden');
    });

    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            modalTitle.textContent = 'Edit Kategori';
            categoryId.value = btn.dataset.id;
            nameInput.value = btn.dataset.name;
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
                text: "Hapus Kategori Ini?",
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

</script>

@endsection
