<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $validatedSearch = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);
        $search = $validatedSearch['search'] ?? null;

        $products = Product::query()
            ->when($search !== null && $search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        return view('products.index', [
            'products' => $products,
            'search' => $search,
            'totalProducts' => Product::query()->count(),
            'totalStockQuantity' => Product::query()->sum('quantity'),
            'lowStockProducts' => Product::query()
                ->where('quantity', '>', 0)
                ->whereColumn('quantity', '<=', 'min_stock')
                ->count(),
            'outOfStockProducts' => Product::query()
                ->where('quantity', 0)
                ->count(),
        ]);
    }

    public function create(): View
    {
        return view('products.form', [
            'product' => new Product,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->productRules());

        Product::query()->create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product added successfully.');
    }

    public function edit(Product $product): View
    {
        return view('products.form', [
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate($this->productRules($product));

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function productRules(?Product $product = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'category' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'min_stock' => ['required', 'integer', 'min:0'],
        ];
    }
}