<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return redirect()->route('product');
    }

    public function product(Request $request)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('category');

        $table = null;
        $tableNumber = session('table_number');
        $expiresAt = session('table_expires_at');

        // Hapus session jika sudah expired
        if ($tableNumber && $expiresAt && now()->greaterThan($expiresAt)) {
            session()->forget(['table_number', 'table_expires_at']);
            $tableNumber = null;
        }

        // Ambil dari query string jika ada ?table=...
        $queryTable = $request->query('table');
        if ($queryTable) {
            $table = Table::where('number', $queryTable)->first();
            if ($table) {
                session([
                    'table_number' => $table->number,
                    'table_expires_at' => now()->addMinutes(10),
                ]);
                $tableNumber = $table->number;
            }
        }

        // Ambil data meja berdasarkan session yang aktif
        if ($tableNumber && !$table) {
            $table = Table::where('number', $tableNumber)->first();
        }

        // Ambil produk dengan relasi category dan filter jika ada pencarian
        $query = Product::with('category');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($categoryFilter) {
            $query->whereHas('category', function ($q) use ($categoryFilter) {
                $q->where('id', $categoryFilter);
            });
        }

        $products = $query->get()->groupBy(function ($product) {
            return $product->category ? $product->category->name : 'Uncategorized';
        });

        // Urutkan produk dalam tiap kategori berdasarkan nama produk
        foreach ($products as $category => $items) {
            $products[$category] = $items->sortBy('name')->values();
        }

        $categories = Category::orderBy('name')->get();

        return view('user.product', [
            'title' => 'Produk',
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $categoryFilter,
            'table' => $table
        ]);
    }

    public function addToCart(Request $request, Product $product)
    {
        $cart = session('cart', []);

        // Cari apakah produk sudah ada di cart
        $foundKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id'] === $product->id) {
                $foundKey = $key;
                break;
            }
        }

        if ($foundKey !== null) {
            // Tambah quantity
            $cart[$foundKey]['quantity']++;
        } else {
            // Tambah item baru
            $cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
            ];
        }

        session(['cart' => $cart]);

        return redirect()->route('product')->with('success', "{$product->name} berhasil ditambahkan ke keranjang.");
    }

    public function cart()
    {
        $cart = session('cart', []);
        return view('user.cart', [
            'title' => 'Keranjang Belanja',
            'cart' => $cart,
        ]);
    }

    public function removeFromCart(Request $request, $key)
    {
        $cart = session('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            // Reindex array supaya tidak ada "gap" di key
            $cart = array_values($cart);
            session(['cart' => $cart]);
            return redirect()->route('cart')->with('success', 'Produk berhasil dihapus dari keranjang.');
        }

        return redirect()->route('user.cart')->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    public function checkout()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('user.product')->with('error', 'Keranjang kosong, silakan pilih produk terlebih dahulu.');
        }

        $tables = Table::where('status', 'available')->orderBy('number')->get();
         $tableNumber = session('table_number');

        return view('user.checkout', [
            'title' => 'Checkout',
            'cart' => $cart,
            'tables' => $tables,
            'tableNumber' => $tableNumber,
        ]);
    }
    public function submitCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,qris',
            'table_number' => 'required|integer|exists:tables,number',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('product')->with('error', 'Keranjang kosong.');
        }

        // Ambil nomor meja dari input atau session (QR code)
        $tableNumber = $request->table_number ?? session('table_number');

        // Validasi meja hanya jika diisi
        if ($tableNumber) {
            $isAvailable = Table::where('number', $tableNumber)
                ->where('status', 'available')
                ->exists();

            if (!$isAvailable) {
                return redirect()->route('checkout')->with('error', 'Meja tidak tersedia. Silakan pilih meja lain.');
            }
        }

        DB::beginTransaction();

        try {
            // Hitung total harga
            $totalPrice = 0;
            foreach ($cart as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            // Simpan order
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'payment_method' => $request->payment_method,
                'table_number' => $tableNumber,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            // Simpan item dan kurangi stok
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Kurangi stok produk
                $product = Product::find($item['id']);
                if ($product && $product->stock >= $item['quantity']) {
                    $product->stock -= $item['quantity'];
                    $product->save();
                } else {
                    // Rollback jika stok tidak mencukupi
                    DB::rollBack();
                    return redirect()->route('checkout')->with('error', 'Stok produk ' . $item['name'] . ' tidak mencukupi.');
                }
            }


            // Jika ada nomor meja, ubah jadi unavailable
            if ($tableNumber) {
                Table::where('number', $tableNumber)->update(['status' => 'unavailable']);
            }

            DB::commit();

            // Simpan order ke session & kosongkan keranjang
            session()->put('order', $order);
            session()->forget('cart');
            // session()->forget('table_number'); // reset table number setelah digunakan

            return redirect()->route('orderSuccess')->with('success', 'Pesanan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout')->with('error', 'Terjadi kesalahan saat proses pesanan: ' . $e->getMessage());
        }
    }


    public function orderSuccess()
    {
        $order = session('order');

        if (!$order) {
            // Kalau tidak ada data order, redirect ke halaman produk misalnya
            return redirect()->route('product')->with('error', 'Data pesanan tidak ditemukan.');
        }

        $order = Order::with('items.product')->find($order->id);

        return view('user.order_success', [
            'title' => 'Order Success',
            'order' => $order,
        ]);
    }

    public function showCheckoutForm()
    {
        $tables = Table::where('status', 'available')->get();
        return view('checkout', compact('tables'));
    }

    public function updateCart(Request $request, $id)
    {
        $cart = session('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['id'] == $id) {
                if ($request->action === 'increase') {
                    // Cek stok dulu
                    $product = Product::find($id);
                    if ($product && $item['quantity'] < $product->stock) {
                        $cart[$key]['quantity']++;
                    }
                } elseif ($request->action === 'decrease') {
                    $cart[$key]['quantity']--;
                    if ($cart[$key]['quantity'] <= 0) {
                        unset($cart[$key]);
                    }
                }
                break;
            }
        }

        session(['cart' => array_values($cart)]);
        return redirect()->back();
    }

}
