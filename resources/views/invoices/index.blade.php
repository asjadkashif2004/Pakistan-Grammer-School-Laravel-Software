@extends('layouts.school')

@section('title', 'Invoices / Sales | Pakistan Grammar School')
@section('page_heading', 'Invoices / Sales')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="#invoice-form" class="action-chip primary" title="New Invoice" aria-label="New Invoice">🧾 <span class="header-action-text">New</span></a>
    </div>
@endsection

@push('styles')
    <style>
        .sales-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.5fr);
            gap: 12px;
        }

        .panel {
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
        .field textarea,
        .item-row input {
            border: 1px solid #dbe8fb;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            background: #fcfdff;
            width: 100%;
        }

        .item-row {
            display: grid;
            grid-template-columns: minmax(130px, 1.1fr) minmax(210px, 1.8fr) minmax(74px, 90px) minmax(110px, 130px) auto;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
            padding: 10px;
            border: 1px solid #e6efe6;
            border-radius: 10px;
            background: #fbfefb;
        }

        #itemsWrapper {
            display: grid;
            gap: 8px;
        }

        .lookup-status {
            grid-column: 1 / -1;
            margin-top: -2px;
            margin-bottom: 4px;
            font-size: 12px;
            font-weight: 700;
            color: #7a8f7d;
        }

        .lookup-status.error {
            color: #b63f3f;
        }

        .action-icon {
            min-width: 36px;
            text-align: center;
            padding: 8px;
            line-height: 1;
        }

        .btn {
            border: 1px solid #d4ead4;
            border-radius: 9px;
            padding: 9px 11px;
            background: #ffffff;
            font-size: 13px;
            font-weight: 700;
            color: #355538;
            cursor: pointer;
        }

        .btn.primary {
            width: 100%;
            border: 0;
            background: linear-gradient(90deg, #0f7a35, #17a34a);
            color: #ffffff;
            padding: 11px;
        }

        .error {
            color: #bd3434;
            font-size: 12px;
            font-weight: 600;
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

        .actions {
            display: inline-flex;
            gap: 6px;
        }

        .status {
            display: inline-flex;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 700;
            background: #ddf8e4;
            color: #0f7a35;
        }

        @media (max-width: 1200px) {
            .sales-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 800px) {
            .item-row {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 9px;
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
    <div class="sales-grid">
        <section class="panel" id="invoice-form">
            <header class="head">Create Sales Invoice</header>
            <div class="body">
                <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
                    @csrf
                    <div class="field">
                        <label for="customer_name">Customer Name</label>
                        <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name') }}" required>
                        @error('customer_name') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="customer_contact">Customer Contact</label>
                        <input id="customer_contact" type="text" name="customer_contact" value="{{ old('customer_contact') }}" placeholder="03XX-XXXXXXX" pattern="03\d{2}-\d{7}">
                        @error('customer_contact') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="invoice_date">Invoice Date</label>
                        <input id="invoice_date" type="date" name="invoice_date" value="{{ old('invoice_date', now()->toDateString()) }}" required>
                        @error('invoice_date') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label>Invoice Items</label>
                        <div id="itemsWrapper">
                            <div class="item-row">
                                <input class="lookup-input" type="text" name="items[0][product_lookup]" placeholder="Enter Product ID" required>
                                <input class="product-id-input" type="hidden" name="items[0][product_id]">
                                <input class="product-details-input" type="text" placeholder="Product details" readonly>
                                <input class="qty-input" type="number" name="items[0][quantity]" min="1" value="1" required>
                                <input type="text" placeholder="Auto price" readonly>
                                <button type="button" class="btn remove-row">Remove</button>
                                <div class="lookup-status">Enter Product ID to fetch details.</div>
                            </div>
                        </div>
                        <button type="button" id="addItemRow" class="btn">+ Add Item</button>
                        @error('items') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="discount">Discount</label>
                        <input id="discount" type="number" step="0.01" min="0" name="discount" value="{{ old('discount', 0) }}">
                        @error('discount') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                        @error('notes') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <button class="btn primary" type="submit">Create Invoice & Deduct Stock</button>
                </form>
            </div>
        </section>

        <section class="panel">
            <header class="head">Sales Invoices</header>
            <div class="body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->customer_name }}</td>
                                <td>{{ optional($invoice->invoice_date)->format('d M Y') }}</td>
                                <td>Rs {{ number_format((float) $invoice->total_amount, 0) }}</td>
                                <td><span class="status">{{ $invoice->status }}</span></td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn action-icon" title="Print">🖨️</a>
                                        <a href="{{ route('invoices.download', $invoice) }}" class="btn action-icon" title="Download">⬇️</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No invoices yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="list-pagination">{{ $invoices->links() }}</div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const wrapper = document.getElementById('itemsWrapper');
            const addButton = document.getElementById('addItemRow');
            if (!wrapper || !addButton) return;

            const refreshIndexes = () => {
                Array.from(wrapper.querySelectorAll('.item-row')).forEach((row, index) => {
                    const lookup = row.querySelector('.lookup-input');
                    const hiddenId = row.querySelector('.product-id-input');
                    const qty = row.querySelector('.qty-input');
                    if (lookup) lookup.name = `items[${index}][product_lookup]`;
                    if (hiddenId) hiddenId.name = `items[${index}][product_id]`;
                    if (qty) qty.name = `items[${index}][quantity]`;
                });
            };

            const formatPrice = (price) => `Rs ${Number(price || 0).toLocaleString('en-PK', { maximumFractionDigits: 0 })}`;

            const clearRow = (row, message, isError = false) => {
                const hiddenId = row.querySelector('.product-id-input');
                const details = row.querySelector('.product-details-input');
                const priceInput = row.querySelector('input[placeholder="Auto price"]');
                const status = row.querySelector('.lookup-status');
                if (hiddenId) hiddenId.value = '';
                if (details) details.value = '';
                if (priceInput) priceInput.value = '';
                if (status) {
                    status.textContent = message;
                    status.classList.toggle('error', isError);
                }
            };

            const lookupProduct = async (row) => {
                const lookup = row.querySelector('.lookup-input');
                const hiddenId = row.querySelector('.product-id-input');
                const details = row.querySelector('.product-details-input');
                const priceInput = row.querySelector('input[placeholder="Auto price"]');
                const status = row.querySelector('.lookup-status');
                if (!lookup || !hiddenId || !details || !priceInput || !status) return;

                const value = lookup.value.trim();
                if (!value) {
                    clearRow(row, 'Enter Product ID to fetch details.');
                    return;
                }

                status.textContent = 'Fetching product...';
                status.classList.remove('error');
                try {
                    const url = new URL('{{ route('invoices.product-lookup') }}', window.location.origin);
                    url.searchParams.set('product_id', value);
                    const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    if (!res.ok || !data.found) {
                        clearRow(row, data.message || 'Product not found.', true);
                        return;
                    }
                    if (!data.in_stock) {
                        clearRow(row, data.message || 'Product out of stock.', true);
                        return;
                    }

                    hiddenId.value = data.id;
                    details.value = `${data.name} (${data.product_code}) - Stock ${data.stock_qty}`;
                    priceInput.value = formatPrice(data.price);
                    status.textContent = 'Product selected successfully.';
                    status.classList.remove('error');
                } catch (_) {
                    clearRow(row, 'Unable to fetch product right now.', true);
                }
            };

            addButton.addEventListener('click', () => {
                const row = document.createElement('div');
                row.className = 'item-row';
                row.innerHTML = `
                    <input class="lookup-input" type="text" placeholder="Enter Product ID" required>
                    <input class="product-id-input" type="hidden" required>
                    <input class="product-details-input" type="text" placeholder="Product details" readonly>
                    <input class="qty-input" type="number" min="1" value="1" required>
                    <input type="text" placeholder="Auto price" readonly>
                    <button type="button" class="btn remove-row">Remove</button>
                    <div class="lookup-status">Enter Product ID to fetch details.</div>
                `;
                wrapper.appendChild(row);
                refreshIndexes();
            });

            wrapper.addEventListener('click', (event) => {
                if (!event.target.classList.contains('remove-row')) return;
                const rows = wrapper.querySelectorAll('.item-row');
                if (rows.length === 1) return;
                event.target.closest('.item-row')?.remove();
                refreshIndexes();
            });

            wrapper.addEventListener('change', (event) => {
                const row = event.target.closest('.item-row');
                if (!row) return;
                if (event.target.name.endsWith('[product_lookup]')) {
                    lookupProduct(row);
                }
            });

            wrapper.addEventListener('blur', (event) => {
                if (!event.target.name?.endsWith('[product_lookup]')) return;
                const row = event.target.closest('.item-row');
                if (!row) return;
                lookupProduct(row);
            }, true);

            refreshIndexes();
        })();
    </script>
@endpush
