@extends('layouts.school')

@section('title', 'Product Inventory | Pakistan Grammar School')
@section('page_heading', 'Product Inventory')

@push('styles')
    <style>
        .product-container { display: grid; gap: 12px; }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toolbar-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 800;
            color: #1f3f24;
        }

        .toolbar-title svg {
            width: 18px;
            height: 18px;
            color: #0f7a35;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 14px;
            overflow: hidden;
        }

        .panel-head {
            padding: 12px 14px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 20px;
            color: #1f3f24;
            font-weight: 800;
            background: #f8fdf8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .panel-body { padding: 12px; }
        .table-wrap { width: 100%; overflow-x: auto; border: 1px solid #e2eee3; border-radius: 10px; }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            color: #1d4589;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .field input,
        .field select,
        .field textarea {
            border: 1px solid #dbe8fb;
            border-radius: 10px;
            padding: 9px 10px;
            font-size: 14px;
            background: #fcfdff;
            width: 100%;
        }

        .btn {
            border: 1px solid #d4ead4;
            border-radius: 9px;
            padding: 9px 12px;
            background: #fff;
            color: #355538;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .btn.primary {
            background: #0f7a35;
            color: #fff;
            border-color: #0f7a35;
        }

        .btn.danger {
            border-color: #ffd3d3;
            color: #9f3131;
            background: #fff7f7;
        }

        .btn svg {
            width: 14px;
            height: 14px;
        }

        .top-tools {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .search {
            border: 1px solid #dbe8fb;
            border-radius: 10px;
            padding: 9px 12px;
            min-width: 260px;
            width: 100%;
            max-width: 380px;
            font-size: 14px;
        }

        .search-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            width: 100%;
            max-width: 520px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
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

        .product-cell {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .product-cell .icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #ecf8ef;
            color: #0f7a35;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .product-cell .icon svg {
            width: 15px;
            height: 15px;
        }

        .product-cell .code {
            font-size: 12px;
            color: #69826b;
        }

        .stock-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stock-bar {
            width: 78px;
            height: 7px;
            border-radius: 999px;
            background: #e6f5e7;
            overflow: hidden;
        }

        .stock-fill {
            height: 100%;
            background: #17a34a;
        }

        .stock-fill.in-stock      { background: #17a34a; }
        .stock-fill.mid-stock     { background: #2f78db; }
        .stock-fill.low-stock     { background: #e5a623; }
        .stock-fill.critical-stock{ background: #e04f4f; }

        .status {
            display: inline-flex;
            border-radius: 999px;
            padding: 3px 9px;
            font-size: 11.5px;
            font-weight: 700;
        }

        .status.in-stock        { background: #ddf8e4; color: #0f7a35; }
        .status.mid-stock       { background: #e7f0ff; color: #2f5fa8; }
        .status.low-stock       { background: #fff2da; color: #966113; }
        .status.critical-stock  { background: #ffe3e3; color: #a93b3b; }

        .actions {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 7px;
            align-items: center;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            border-radius: 9px;
            position: relative;
        }

        .icon-btn svg {
            width: 15px;
            height: 15px;
        }

        .actions form {
            display: inline-flex;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
            vertical-align: middle;
        }

        .ledger-foot {
            margin-top: 12px;
            border-top: 1px solid #e7f3e7;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            font-size: 13px;
            font-weight: 700;
            color: #355538;
        }

        .ledger-foot .meta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .ledger-foot svg {
            width: 15px;
            height: 15px;
            color: #0f7a35;
        }

        .field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .field-grid .field.full {
            grid-column: span 2;
        }

        dialog#productModal,
        dialog#editProductModal {
            border: 1px solid #d4ead4;
            border-radius: 14px;
            padding: 0;
            width: min(680px, 96vw);
        }

        dialog#productModal::backdrop,
        dialog#editProductModal::backdrop {
            background: rgba(15, 40, 20, 0.35);
        }

        .modal-head {
            padding: 12px 14px;
            border-bottom: 1px solid #e7f3e7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .modal-head h4 {
            margin: 0;
            font-size: 18px;
            color: #1f3f24;
        }

        .modal-body {
            padding: 14px;
        }

        .modal-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .error-box {
            margin-bottom: 10px;
            border: 1px solid #ffd6d6;
            background: #fff6f6;
            color: #8e2d2d;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
        }

        .error-box ul { margin: 6px 0 0; padding-left: 16px; }

        @media (max-width: 768px) {
            .toolbar { align-items: stretch; }
            .top-tools { flex-direction: column; align-items: stretch; }
            .search { max-width: none; min-width: 0; }
            .search-wrap { max-width: none; }
            .field-grid { grid-template-columns: 1fr; }
            .field-grid .field.full { grid-column: auto; }
            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        @media (max-width: 640px) {
            .table-wrap { border: 0; overflow: visible; }
            .table { min-width: 0; display: block; }
            .table thead { display: none; }
            .table tbody { display: grid; gap: 10px; }
            .table tr {
                display: block;
                border: 1px solid #dcebdd;
                border-radius: 12px;
                padding: 10px;
                background: #fff;
            }
            .table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 8px;
                border-top: 0;
                padding: 6px 0;
                font-size: 13px;
            }
            .table td::before {
                content: attr(data-label);
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .06em;
                text-transform: uppercase;
                color: #628066;
                flex: 0 0 auto;
            }
            .table td[data-label="Product"] {
                display: block;
                padding-top: 0;
            }
            .table td[data-label="Product"]::before { display: none; }
            .table td[data-label="Actions"] {
                display: block;
                padding-bottom: 0;
            }
            .table td[data-label="Actions"]::before { display: none; }
        }
    </style>
@endpush

@section('content')
    @php
        $productsOnPage = $products->count();
        $totalProducts = $products->total();
        $lowStockOnPage = $products->getCollection()->filter(fn ($item) => (int) $item->stock_qty < 50)->count();
    @endphp
    <div class="product-container">
        <section class="toolbar">
            <div class="toolbar-title">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 8.5 12 4l8 4.5v7L12 20l-8-4.5v-7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 20v-7" stroke="currentColor" stroke-width="1.8"/></svg>
                Product Records
            </div>
            <div style="display:inline-flex; gap:8px; flex-wrap:wrap;">
                <button type="button" class="btn primary" id="openProductModal">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Add Product
                </button>
                <a href="{{ route('products.print') }}" target="_blank" class="btn" title="Print products">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8V4h10v4M7 17H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-1M7 14h10v6H7v-6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Print
                </a>
            </div>
        </section>

        <section class="panel">
            <header class="panel-head">
                <span style="display:inline-flex;align-items:center;gap:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15V9M12 15V7M16 15v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    Product Inventory
                </span>
            </header>
            <div class="panel-body">
                <form method="GET" class="top-tools">
                    <div class="search-wrap">
                        <input class="search" type="search" name="q" value="{{ $search }}" placeholder="Search products...">
                        <button class="btn" type="submit">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            Search
                        </button>
                    </div>
                </form>

                <div class="table-wrap">
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
                                <td data-label="Product">
                                    <span class="product-cell">
                                        <span class="icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none"><path d="M4 8.5 12 4l8 4.5v7L12 20l-8-4.5v-7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 20v-7" stroke="currentColor" stroke-width="1.8"/></svg>
                                        </span>
                                        <span>
                                            <strong>{{ $product->name }}</strong>
                                            <div class="code">{{ $product->product_code }}</div>
                                        </span>
                                    </span>
                                </td>
                                <td data-label="Price">Rs {{ number_format((float) ($product->sale_price ?? $product->unit_price), 0) }}</td>
                                <td data-label="Stock">
                                    <div class="stock-wrap">
                                        <strong>{{ $product->stock_qty }}</strong>
                                        <span class="stock-bar"><span class="stock-fill {{ $statusClass }}" style="width: {{ $bar }}%;"></span></span>
                                    </div>
                                </td>
                                <td data-label="Status">
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
                                <td data-label="Actions">
                                    <div class="actions">
                                        <button
                                            type="button"
                                            class="btn icon-btn"
                                            data-open-edit-product
                                            data-product-id="{{ $product->id }}"
                                            data-update-url="{{ route('products.update', $product) }}"
                                            data-name="{{ e($product->name) }}"
                                            data-cost-price="{{ number_format((float) $product->cost_price, 2, '.', '') }}"
                                            data-sale-price="{{ number_format((float) ($product->sale_price ?? $product->unit_price), 2, '.', '') }}"
                                            data-stock-qty="{{ (int) $product->stock_qty }}"
                                            title="Edit product"
                                            aria-label="Edit product"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20h4l10-10-4-4L4 16v4zM14 6l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn danger icon-btn" type="submit" title="Delete product" aria-label="Delete product">
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2m-8 0l1 12h8l1-12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
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
                </div>

                <div class="ledger-foot">
                    <span class="meta">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/></svg>
                        Products on this page: {{ $productsOnPage }}
                    </span>
                    <span class="meta">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 18h16M6 16l4-5 3 3 5-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Low stock on page: {{ $lowStockOnPage }}
                    </span>
                    <span class="meta">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 12h18M12 3v18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        Total products: {{ $totalProducts }}
                    </span>
                </div>

                <div class="list-pagination">{{ $products->links() }}</div>
            </div>
        </section>

        <dialog id="productModal">
            <div class="modal-head">
                <h4>Add Product</h4>
                <button class="btn" type="button" id="closeProductModal">Close</button>
            </div>
            <div class="modal-body">
                @if ($errors->any() && old('_form_mode', 'add') === 'add')
                    <div class="error-box">
                        <strong>Please correct the fields below.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('products.store') }}">
                    @csrf
                    <input type="hidden" name="_form_mode" value="add">
                    <div class="field-grid">
                        <div class="field full">
                            <label>Product ID</label>
                            <input type="text" value="Auto generated on save" readonly>
                        </div>
                        <div class="field full">
                            <label for="add_name">Product Name</label>
                            <input id="add_name" type="text" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="field">
                            <label for="add_cost_price">Cost Price</label>
                            <input id="add_cost_price" type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price') }}" required>
                        </div>
                        <div class="field">
                            <label for="add_sale_price">Sale Price</label>
                            <input id="add_sale_price" type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price') }}" required>
                        </div>
                        <div class="field full">
                            <label for="add_stock_qty">Current Stock</label>
                            <input id="add_stock_qty" type="number" min="0" name="stock_qty" value="{{ old('stock_qty') }}" required>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="btn" type="button" id="cancelProductModal">Cancel</button>
                        <button class="btn primary" type="submit">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Save Product
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <dialog id="editProductModal">
            <div class="modal-head">
                <h4>Edit Product</h4>
                <button class="btn" type="button" id="closeEditProductModal">Close</button>
            </div>
            <div class="modal-body">
                @if ($errors->any() && old('_form_mode') === 'edit')
                    <div class="error-box">
                        <strong>Please correct the fields below.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('products.update', old('product_id', 0)) }}" id="editProductForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form_mode" value="edit">
                    <input type="hidden" name="product_id" id="edit_product_id" value="{{ old('product_id') }}">
                    <div class="field-grid">
                        <div class="field full">
                            <label for="edit_name">Product Name</label>
                            <input id="edit_name" type="text" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="field">
                            <label for="edit_cost_price">Cost Price</label>
                            <input id="edit_cost_price" type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price') }}" required>
                        </div>
                        <div class="field">
                            <label for="edit_sale_price">Sale Price</label>
                            <input id="edit_sale_price" type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price') }}" required>
                        </div>
                        <div class="field full">
                            <label for="edit_stock_qty">Current Stock</label>
                            <input id="edit_stock_qty" type="number" min="0" name="stock_qty" value="{{ old('stock_qty') }}" required>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="btn" type="button" id="cancelEditProductModal">Cancel</button>
                        <button class="btn primary" type="submit">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12.5 10 17l9-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Update Product
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const addModal = document.getElementById('productModal');
    const editModal = document.getElementById('editProductModal');
    const openAddBtn = document.getElementById('openProductModal');
    const closeAddBtn = document.getElementById('closeProductModal');
    const cancelAddBtn = document.getElementById('cancelProductModal');
    const closeEditBtn = document.getElementById('closeEditProductModal');
    const cancelEditBtn = document.getElementById('cancelEditProductModal');
    const editForm = document.getElementById('editProductForm');

    const openDialog = (dialog) => {
        if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
    };
    const closeDialog = (dialog) => {
        if (dialog && typeof dialog.close === 'function') dialog.close();
    };

    openAddBtn?.addEventListener('click', () => openDialog(addModal));
    closeAddBtn?.addEventListener('click', () => closeDialog(addModal));
    cancelAddBtn?.addEventListener('click', () => closeDialog(addModal));
    closeEditBtn?.addEventListener('click', () => closeDialog(editModal));
    cancelEditBtn?.addEventListener('click', () => closeDialog(editModal));

    document.addEventListener('click', function (event) {
        const btn = event.target.closest('[data-open-edit-product]');
        if (!btn || !editForm) return;

        const setValue = (id, value) => {
            const field = document.getElementById(id);
            if (field) field.value = value || '';
        };

        editForm.setAttribute('action', btn.getAttribute('data-update-url') || editForm.getAttribute('action') || '');
        setValue('edit_product_id', btn.getAttribute('data-product-id'));
        setValue('edit_name', btn.getAttribute('data-name'));
        setValue('edit_cost_price', btn.getAttribute('data-cost-price'));
        setValue('edit_sale_price', btn.getAttribute('data-sale-price'));
        setValue('edit_stock_qty', btn.getAttribute('data-stock-qty'));
        openDialog(editModal);
    });

    @if ($errors->any() && old('_form_mode') === 'edit')
        openDialog(editModal);
    @elseif ($errors->any())
        openDialog(addModal);
    @endif
})();
</script>
@endpush
