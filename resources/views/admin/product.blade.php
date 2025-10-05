@extends('layouts.admin')

@section('content')

<h2 class="text-3xl font-bold mb-6">Produk</h2>

@if(session('success'))
    <div class="bg-green-200 p-3 rounded mb-4">{{ session('success') }}</div>
@endif

<!-- Button Open Modal -->
<button id="openModalBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4">+ Tambah Produk</button>

<!-- Modal Background -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <!-- Modal Content -->
    <div class="bg-white rounded-lg w-96 p-6 relative">

        <h3 id="modalTitle" class="text-xl font-semibold mb-4">Add New Product</h3>

        <form id="productForm" method="POST" action="{{ route('admin.products.save') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="product_id" />

            <label class="block mb-2">
                Nama
                <input type="text" name="name" id="name" class="w-full border p-2 rounded" required />
            </label>

            <label class="block mb-2">
                Deskripsi
                <textarea name="description" id="description" class="w-full border p-2 rounded"></textarea>
            </label>

            <label class="block mb-2">
                Harga
                <input type="number" name="price" id="price" class="w-full border p-2 rounded" required min="0" />
            </label>

            <label class="block mb-2">
                Stok
                <input type="number" name="stock" id="stock" class="w-full border p-2 rounded" required min="0" />
            </label>

            <label class="block mb-4">
                Kategori
                <select name="category_id" id="category_id" class="w-full border p-2 rounded" required>
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block mb-4">
                Gambar
                <input type="file" name="image" id="image" class="w-full" accept="image/*" />
            </label>

            <div class="flex justify-end space-x-2">
                <button type="button" id="closeModalBtn" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            </div>
        </form>


    </div>

</div>

<!-- Table Produk -->
<table class="min-w-full bg-white shadow rounded overflow-hidden">
    <thead class="bg-green-700 text-white">
        <tr>
            <th class="py-3 px-6 text-left">Gambar</th>
            <th class="py-3 px-6 text-left">Nama</th>
            <th class="py-3 px-6 text-left">Kategori</th>
            <th class="py-3 px-6 text-left">Harga</th>
            <th class="py-3 px-6 text-left">Stok</th>
            <th class="py-3 px-6 text-left">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $product)
        <tr class="border-b hover:bg-green-50">
            <td class="py-3 px-6">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-12 w-12 object-cover rounded" />
                @else
                    <span class="text-gray-400 italic">No Image</span>
                @endif
            </td>
            <td class="py-3 px-6">{{ $product->name }}</td>
            <td class="py-3 px-6">{{ $product->category ? $product->category->name : '-' }}</td>
            <td class="py-3 px-6">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
            <td class="py-3 px-6">{{ $product->stock }}</td>
            <td class="py-3 px-6 space-x-2">

                <button 
                    class="editBtn text-blue-600 hover:underline"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-description="{{ $product->description }}"
                    data-price="{{ $product->price }}"
                    data-stock="{{ $product->stock }}"
                    data-category-id="{{ $product->category_id }}" 
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 hover:text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M16.768 4.768a2.5 2.5 0 113.536 3.536L7 21H3v-4L16.768 4.768z" />
                    </svg>

                </button>


                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline">
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
        @empty
        <tr>
            <td colspan="5" class="text-center py-4 text-gray-500">No products found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<script>
    const modal = document.getElementById('modal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modalTitle = document.getElementById('modalTitle');
    const productForm = document.getElementById('productForm');

    // Form fields
    const productId = document.getElementById('product_id');
    const name = document.getElementById('name');
    const description = document.getElementById('description');
    const price = document.getElementById('price');
    const stock = document.getElementById('stock');
    const image = document.getElementById('image');

    // Buka modal untuk create
    openModalBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Tambah Produk';
        productForm.reset();
        productId.value = '';
        modal.classList.remove('hidden');
    });

    // Tutup modal
    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Buka modal untuk edit dan isi form dengan data produk
   const categoryId = document.getElementById('category_id');

    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', () => {
            modalTitle.textContent = 'Edit Produk';
            productId.value = button.dataset.id;
            name.value = button.dataset.name;
            description.value = button.dataset.description;
            price.value = button.dataset.price;
            stock.value = button.dataset.stock;
            image.value = '';
            categoryId.value = button.dataset.categoryId || '';
            modal.classList.remove('hidden');
        });
    });


    // Klik luar modal untuk tutup (optional)
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
                text: "Hapus Produk Ini?",
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
