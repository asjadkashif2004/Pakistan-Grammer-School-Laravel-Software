@extends('layouts.school')

@section('title', 'Product Inventory | Pakistan Grammar School')
@section('page_heading', 'Product Inventory')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="{{ route('products.print') }}" target="_blank" class="action-chip" title="Print" aria-label="Print products">🖨️ <span class="header-action-text">Print</span></a>
        <a href="#product-form" class="action-chip primary" title="Add product" aria-label="Add product">➕ <span class="header-action-text">Add</span></a>
    </div>
@endsection

@push('styles')
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.7fr);
            gap: 12px;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .panel-head {
            padding: 14px 16px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 22px;
            color: #1f3f24;
            font-weight: 800;
        }

        .panel-body {
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

        .top-tools {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .search {
            border: 1px solid #dbe8fb;
            border-radius: 10px;
            padding: 9px 11px;
            min-width: 220px;
            width: 100%;
            max-width: 320px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 10px 8px;
            border-top: 1px solid #e8f3e8;
            font-size: 14px;
        }

        .table th {
            color: #56735a;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 700;
            border-top: none;
            padding-top: 0;
        }

        .stock-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stock-bar {
            width: 72px;
            height: 6px;
            border-radius: 999px;
            background: #e6f5e7;
            overflow: hidden;
        }

        .stock-fill {
            height: 100%;
            background: #17a34a;
        }

        .stock-fill.in-stock {
            background: #17a34a;
        }

        .stock-fill.mid-stock {
            background: #2f78db;
        }

        .stock-fill.low-stock {
            background: #e5a623;
        }

        .stock-fill.critical-stock {
            background: #e04f4f;
        }

        .status {
            display: inline-flex;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
        }

        .status.in-stock {
            background: #ddf8e4;
            color: #0f7a35;
        }

        .status.mid-stock {
            background: #e7f0ff;
            color: #2f5fa8;
        }

        .status.low-stock {
            background: #fff2da;
            color: #966113;
        }

        .status.critical-stock {
            background: #ffe3e3;
            color: #a93b3b;
        }

        .actions {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            justify-content: flex-start;
        }

        /* Forms are block by default — without this, Delete stretches full cell width on mobile */
        .actions form {
            display: inline-flex;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
            vertical-align: middle;
        }

        .link-btn {
            border: 1px solid #d4ead4;
            border-radius: 7px;
            padding: 4px 7px;
            font-size: 12px;
            text-decoration: none;
            color: #1f5d2a;
            font-weight: 700;
            background: #ffffff;
            line-height: 1.2;
            white-space: nowrap;
            -webkit-tap-highlight-color: transparent;
        }

        .link-btn.danger {
            color: #9f3131;
            border-color: #ffd3d3;
            background: #fff7f7;
        }

        @media (max-width: 1200px) {
            .product-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .panel-head {
                font-size: 17px;
                padding: 12px 14px;
            }

            .panel-body {
                padding: 12px;
            }

            .top-tools {
                flex-direction: column;
                align-items: stretch;
            }

            .top-tools .search {
                max-width: none;
                min-width: 0;
            }

            .top-tools .link-btn {
                align-self: flex-start;
                width: auto;
                max-width: 100%;
                padding: 8px 14px;
                font-size: 13px;
            }

            .btn {
                padding: 10px 14px;
                font-size: 14px;
                min-height: 44px;
            }

            .actions {
                gap: 8px;
            }

            .actions .link-btn {
                padding: 7px 12px;
                font-size: 12px;
                min-height: 36px;
            }

            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
@endpush

@section('content')
    <div class="product-grid">
        <section class="panel" id="product-form">
            <header class="panel-head">Add Product</header>
            <div class="panel-body">
                <form method="POST" action="{{ route('products.store') }}">
                    @csrf
                    <div class="field">
                        <label>Product ID</label>
                        <input type="text" value="Auto generated on save" readonly>
                    </div>
                    <div class="field">
                        <label for="name">Product Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                        @error('name') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="cost_price">Cost Price</label>
                        <input id="cost_price" type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price') }}" required>
                        @error('cost_price') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="sale_price">Sale Price</label>
                        <input id="sale_price" type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price') }}" required>
                        @error('sale_price') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="stock_qty">Current Stock</label>
                        <input id="stock_qty" type="number" min="0" name="stock_qty" value="{{ old('stock_qty') }}" required>
                        @error('stock_qty') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <button class="btn" type="submit">+ Add to Inventory</button>
                </form>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">Product Inventory</header>
            <div class="panel-body">
                <form method="GET" class="top-tools">
                    <input class="search" type="search" name="q" value="{{ $search }}" placeholder="Search products...">
                    <button class="link-btn" type="submit">Search</button>
                </form>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                $qty = (int) $product->stock_qty;
                                if ($qty < 20) {
                                    $statusClass = 'critical-stock';
                                } elseif ($qty < 50) {
                                    $statusClass = 'low-stock';
                                } elseif ($qty < 80) {
                                    $statusClass = 'mid-stock';
                                } else {
                                    $statusClass = 'in-stock';
                                }
                                $bar = $qty >= 100 ? 100 : max(8, min(100, $qty));
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    <div style="font-size: 12px; color: #69826b;">{{ $product->product_code }}</div>
                                </td>
                                <td>Rs {{ number_format((float) ($product->sale_price ?? $product->unit_price), 0) }}</td>
                                <td>
                                    <div class="stock-wrap">
                                        <strong>{{ $product->stock_qty }}</strong>
                                        <span class="stock-bar"><span class="stock-fill {{ $statusClass }}" style="width: {{ $bar }}%;"></span></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status {{ $statusClass }}">
                                        @if ($qty < 20)
                                            Critical
                                        @elseif ($qty < 50)
                                            Low Stock
                                        @elseif ($qty < 80)
                                            Mid Stock
                                        @else
                                            In Stock
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a class="link-btn" href="{{ route('products.edit', $product) }}">Edit</a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="link-btn danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No products yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="list-pagination">{{ $products->links() }}</div>
            </div>
        </section>
    </div>
@endsection
