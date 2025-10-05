<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index() {
        $products = Product::with('category')->get();
        $categories = Category::all();
        return view('admin.product', compact('products', 'categories'));
    }

    public function create() {
        return view('admin.products.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product) {
        return view('admin.products.edit', compact('product'));
    }
    public function update(Request $request, Product $product) {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus image lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index');
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if ($validated['id']) {
            // Update
            $product = Product::find($validated['id']);

            // Hapus gambar lama kalau ada dan upload gambar baru
            if ($request->hasFile('image') && $product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->update($validated);
        } else {
            // Create baru
            Product::create($validated);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product saved successfully!');
    }

    public function destroy(Product $product) {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

}
