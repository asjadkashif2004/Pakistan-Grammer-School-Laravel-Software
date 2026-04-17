<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('product_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        $product = DB::transaction(function () use ($validated) {
            $item = Product::create($validated);
            $item->update([
                'product_code' => sprintf('PRD-%05d', $item->id),
            ]);

            return $item;
        });
        ActivityLogger::log(
            'product.created',
            "Product {$product->name} ({$product->product_code}) added.",
            'product',
            $product->id
        );

        return redirect()
            ->route('products.index')
            ->with('status', "Product {$product->product_code} created successfully.");
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'todayLabel' => now()->format('l, d F Y'),
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $product->update($validated);
        ActivityLogger::log(
            'product.updated',
            "Product {$product->product_code} updated.",
            'product',
            $product->id
        );

        return redirect()
            ->route('products.index')
            ->with('status', "Product {$product->product_code} updated successfully.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->invoiceItems()->exists()) {
            return redirect()
                ->route('products.index')
                ->with('status', "Product {$product->product_code} is used in invoices and cannot be deleted.");
        }

        $code = $product->product_code;
        $name = $product->name;
        $product->delete();
        ActivityLogger::log(
            'product.deleted',
            "Product {$name} ({$code}) deleted.",
            'product'
        );

        return redirect()
            ->route('products.index')
            ->with('status', 'Product deleted successfully.');
    }

    public function print(): View
    {
        $products = Product::orderBy('name')->get();

        return view('products.print', [
            'products' => $products,
            'printedAt' => now(),
        ]);
    }

    private function validateProduct(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
        ]);

        // Keep backwards compatibility: existing code may still read `unit_price`.
        $validated['unit_price'] = $validated['sale_price'];

        return $validated;
    }
}
