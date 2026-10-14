<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     * Non-admin users ONLY see their own products!
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Permission check: view-product
        if (! $user->hasPermissionTo('view-product')) {
            $redirectRoute = $user->hasRole('admin') ? 'admin.dashboard' : 'user.dashboard';
            return redirect()->route($redirectRoute)
                ->with('error', 'Access Denied: You do not have permission to view products.');
        }

        $query = Product::with('creator')->latest();

        // 2. Data Scoping: Non-admin users ONLY see their own products!
        if (! $user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(10)->withQueryString();

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check: create-product
        if (! $user->hasPermissionTo('create-product')) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You do not have permission to create products.');
        }

        return view('products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check: create-product
        if (! $user->hasPermissionTo('create-product')) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You do not have permission to create products.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['required', 'string', 'max:100', 'unique:products,sku'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = $user->id;

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check: view-product
        if (! $user->hasPermissionTo('view-product')) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You do not have permission to view products.');
        }

        // Ownership check: Regular users can ONLY view their own products
        if (! $user->hasRole('admin') && $product->user_id !== $user->id) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You can only view your own products.');
        }

        $product->load('creator');

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check: edit-product
        if (! $user->hasPermissionTo('edit-product')) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You do not have permission to edit products.');
        }

        // Ownership check: Regular users can ONLY edit their own products
        if (! $user->hasRole('admin') && $product->user_id !== $user->id) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You can only edit your own products.');
        }

        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check: edit-product
        if (! $user->hasPermissionTo('edit-product')) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You do not have permission to edit products.');
        }

        // Ownership check: Regular users can ONLY update their own products
        if (! $user->hasRole('admin') && $product->user_id !== $user->id) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You can only update your own products.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['required', 'string', 'max:100', "unique:products,sku,{$product->id}"],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Authorization check: delete-product
        if (! $user->hasPermissionTo('delete-product')) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You do not have permission to delete products.');
        }

        // Ownership check: Regular users can ONLY delete their own products
        if (! $user->hasRole('admin') && $product->user_id !== $user->id) {
            return redirect()->route('products.index')
                ->with('error', 'Access Denied: You can only delete your own products.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
