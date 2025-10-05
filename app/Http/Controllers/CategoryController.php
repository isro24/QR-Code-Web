<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.categories', compact('categories'));
    }

    public function save(Request $request)
    {
        $rules = [
            'name' => 'required|unique:categories,name'
        ];

        if ($request->category_id) {
            $rules['name'] = 'required|unique:categories,name,' . $request->id;
        }

        $request->validate($rules);

        if ($request->category_id) {
            $category = Category::findOrFail($request->category_id);
            $category->update($request->only('name'));
        } else {
            Category::create($request->only('name'));
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil disimpan.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
