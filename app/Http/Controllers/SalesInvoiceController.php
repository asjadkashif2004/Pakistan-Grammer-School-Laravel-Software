<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\Student;
use App\Support\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = SalesInvoice::with(['items.product', 'student'])
            ->latest('invoice_date')
            ->paginate(10)
            ->withQueryString();

        return view('invoices.index', [
            'todayLabel' => now()->format('l, d F Y'),
            'invoices' => $invoices,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'invoice_date' => ['required', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1200'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'student_id.required' => 'Please look up a valid student by Student ID.',
            'student_id.exists' => 'The selected student is invalid.',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        $invoice = DB::transaction(function () use ($validated, $student) {
            $subtotal = 0;
            $invoice = SalesInvoice::create([
                'invoice_number' => null,
                'student_id' => $student->id,
                'customer_name' => $student->full_name,
                'customer_contact' => null,
                'invoice_date' => $validated['invoice_date'],
                'subtotal' => 0,
                'discount' => (float) ($validated['discount'] ?? 0),
                'total_amount' => 0,
                'status' => 'Paid',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];

                if ($product->stock_qty < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for {$product->name}. Available: {$product->stock_qty}.",
                    ]);
                }

                $unitPrice = (float) ($product->sale_price ?? $product->unit_price);
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $invoice->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock_qty', $quantity);
            }

            $discount = (float) ($validated['discount'] ?? 0);
            $invoice->update([
                'invoice_number' => sprintf('INV-%s-%05d', now()->format('Y'), $invoice->id),
                'subtotal' => $subtotal,
                'total_amount' => max(0, $subtotal - $discount),
            ]);

            return $invoice;
        });
        ActivityLogger::log(
            'invoice.created',
            "Invoice {$invoice->invoice_number} created for student {$student->student_code} ({$student->full_name}).",
            'sales_invoice',
            $invoice->id
        );

        return redirect()
            ->route('invoices.index')
            ->with('status', "Invoice {$invoice->invoice_number} created and stock updated.");
    }

    public function print(SalesInvoice $invoice): View
    {
        $invoice->load(['items.product', 'student']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'isDownload' => false,
        ]);
    }

    public function download(SalesInvoice $invoice)
    {
        $invoice->load(['items.product', 'student']);

        $html = view('invoices.print', [
            'invoice' => $invoice,
            'isDownload' => true,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="invoice-'.$invoice->invoice_number.'.html"',
        ]);
    }

    public function productLookup(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'string', 'max:80'],
        ]);

        $lookup = trim((string) $request->query('product_id', ''));
        $product = Product::query()
            ->where('product_code', $lookup)
            ->orWhere('id', (int) $lookup)
            ->first();

        if (! $product) {
            return response()->json([
                'found' => false,
                'message' => 'Product not found for entered Product ID.',
            ], 404);
        }

        $inStock = (int) $product->stock_qty > 0;

        return response()->json([
            'found' => true,
            'id' => $product->id,
            'product_code' => $product->product_code,
            'name' => $product->name,
            'stock_qty' => (int) $product->stock_qty,
            'price' => (float) ($product->sale_price ?? $product->unit_price),
            'in_stock' => $inStock,
            'message' => $inStock ? null : 'Product is out of stock and cannot be selected.',
        ]);
    }

    public function studentLookup(Request $request): JsonResponse
    {
        $request->validate([
            'student_code' => ['required', 'string', 'max:40'],
        ]);

        $code = strtoupper(trim((string) $request->query('student_code', '')));
        $student = Student::query()
            ->whereRaw('UPPER(student_code) = ?', [$code])
            ->first();

        if (! $student) {
            return response()->json([
                'found' => false,
                'message' => 'Student not found. Enter Student ID (e.g. PGS-00025).',
            ], 404);
        }

        return response()->json([
            'found' => true,
            'id' => $student->id,
            'student_code' => $student->student_code,
            'full_name' => $student->full_name,
            'class_name' => $student->class_name,
            'section' => $student->section,
            'father_name' => $student->father_name,
            'contact_number' => $student->contact_number,
        ]);
    }
}
