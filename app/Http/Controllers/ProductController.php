<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display products (with search + filter + pagination)
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // SEARCH
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // CATEGORY FILTER
        if ($request->category) {
            $query->whereJsonContains('categories', $request->category);
        }

        // PAGINATION
        $products = $query->latest()->paginate(5)->withQueryString();

        $categories = ['Electronics', 'Fashion', 'Books', 'Furniture', 'Sports'];

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = ['Electronics', 'Fashion', 'Books', 'Furniture', 'Sports'];
        return view('products.create', compact('categories'));
    }

    /**
     * Store product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categories' => 'required|array',
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'categories' => $request->categories,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Show single product
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = ['Electronics', 'Fashion', 'Books', 'Furniture', 'Sports'];

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categories' => 'required|array',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'categories' => $request->categories,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Soft delete
     */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }

    /**
     * Trash page
     */
    public function trash()
    {
        $products = Product::onlyTrashed()->latest()->get();
        return view('products.trash', compact('products'));
    }

    /**
     * Restore product
     */
    public function restore($id)
    {
        Product::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('products.trash')
            ->with('success', 'Product restored successfully');
    }

    /**
     * Permanent delete
     */
    public function forceDelete($id)
    {
        Product::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()->route('products.trash')
            ->with('success', 'Product permanently deleted');
    }
}