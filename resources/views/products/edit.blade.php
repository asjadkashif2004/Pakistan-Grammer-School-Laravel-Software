@extends('layouts.school')

@section('title', 'Edit Product | Pakistan Grammar School')
@section('page_heading', 'Edit Product')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('products.index') }}" class="action-chip" title="Back to products" aria-label="Back to products">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="header-action-text">Products</span>
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .head {
            padding: 14px 16px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 22px;
            color: #1f3f24;
            font-weight: 800;
        }

        .body {
            padding: 14px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 10px;
        }

        .field label {
            font-size: 12px;
            font-weight: 700;
            color: #1d4589;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .field input,
        .field textarea {
            border: 1px solid #dbe8fb;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            background: #fcfdff;
        }

        .btn {
            border: 0;
            border-radius: 10px;
            width: 100%;
            padding: 11px;
            background: linear-gradient(90deg, #0f7a35, #17a34a);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

        .error {
            color: #bd3434;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <section class="card">
        <header class="head">Edit Product - {{ $product->product_code }}</header>
        <div class="body">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label>Product ID</label>
                    <input type="text" value="{{ $product->product_code }}" readonly>
                </div>

                <div class="field">
                    <label for="name">Product Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required>
                    @error('name') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="cost_price">Cost Price</label>
                    <input id="cost_price" type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', (float) ($product->cost_price ?? 0)) }}" required>
                    @error('cost_price') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="sale_price">Sale Price</label>
                    <input id="sale_price" type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', (float) ($product->sale_price ?? $product->unit_price)) }}" required>
                    @error('sale_price') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="stock_qty">Current Stock</label>
                    <input id="stock_qty" type="number" min="0" name="stock_qty" value="{{ old('stock_qty', $product->stock_qty) }}" required>
                    @error('stock_qty') <span class="error">{{ $message }}</span> @enderror
                </div>
                <button class="btn" type="submit">Update Product</button>
            </form>
        </div>
    </section>
@endsection
