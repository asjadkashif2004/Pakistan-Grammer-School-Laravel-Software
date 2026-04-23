@extends('layouts.school')

@section('title', 'POS / Sales | Pakistan Grammar School')
@section('page_heading', 'POS / Sales')

@section('header_actions')
    <div class="header-actions-slot">
        <a href="#invoice-form" class="action-chip primary" title="New Invoice" aria-label="New Invoice">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h10a2 2 0 0 1 2 2v14l-3-2-2 2-2-2-2 2-3-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="header-action-text">New</span>
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .sales-stack {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 640px;
            margin: 0 auto;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #d4ead4;
            border-radius: 10px;
            overflow: hidden;
        }

        .head {
            padding: 10px 12px;
            border-bottom: 1px solid #e7f3e7;
            font-size: 16px;
            color: #1f3f24;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .body {
            padding: 12px;
        }

        /* ── Form fields layout: 3 columns on desktop ── */
        .form-meta-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0 14px;
        }

        .form-bottom-row {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 0 14px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 8px;
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
            grid-template-columns: minmax(120px, 1fr) minmax(160px, 1.4fr) minmax(64px, 72px) minmax(96px, 110px) auto;
            gap: 8px;
            margin-bottom: 8px;
            align-items: center;
            padding: 8px;
            border: 1px solid #e6efe6;
            border-radius: 8px;
            background: #fafcfa;
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
            padding: 10px 12px;
            font-size: 14px;
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
            padding: 8px 6px;
            border-top: 1px solid #e8f3e8;
            font-size: 13px;
        }

        .table th {
            color: #56735a;
            font-size: 11px;
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

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .form-meta-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .form-meta-row,
            .form-bottom-row {
                grid-template-columns: 1fr;
            }

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
    <div class="sales-stack">

        {{-- ═══════════════════════════════════════
             PANEL 1 — CREATE INVOICE FORM (TOP)
        ════════════════════════════════════════ --}}
        <section class="panel" id="invoice-form">
            <header class="head">New sale (POS)</header>
            <div class="body">
                <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
                    @csrf

                    {{-- Student ID (lookup) | Student details | Invoice Date --}}
                    <div class="form-meta-row">
                        <div class="field">
                            <label for="student_code_lookup">Student ID</label>
                            <input id="student_code_lookup" type="text" value="{{ old('student_code_lookup') }}" placeholder="e.g. PGS-00025" autocomplete="off" pattern="PGS-[0-9]{5}">
                            <input type="hidden" id="student_id" name="student_id" value="{{ old('student_id') }}">
                            <div id="studentLookupStatus" class="lookup-status" style="margin-top:4px;">Enter Student ID to load student.</div>
                            @error('student_id') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="student_details_display">Student name</label>
                            <input id="student_details_display" type="text" readonly placeholder="Auto-filled from Student ID">
                        </div>
                        <div class="field">
                            <label for="invoice_date">Invoice Date</label>
                            <input id="invoice_date" type="date" name="invoice_date" value="{{ old('invoice_date', now()->toDateString()) }}" required>
                            @error('invoice_date') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Invoice Items (full width) --}}
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

                    {{-- Discount (narrow) | Notes (wide) --}}
                    <div class="form-bottom-row">
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
                    </div>

                    <button class="btn primary" type="submit">Save sale &amp; deduct stock</button>
                </form>
            </div>
        </section>

        {{-- ═══════════════════════════════════════
             PANEL 2 — SALES INVOICES TABLE (BOTTOM)
        ════════════════════════════════════════ --}}
        <section class="panel">
            <header class="head">Receipt history</header>
            <div class="body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Student</th>
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
                                <td>
                                    @if ($invoice->student)
                                        <strong>{{ $invoice->student->student_code }}</strong>
                                        <div style="font-size:12px;color:#5f7661;">{{ $invoice->student->full_name }}</div>
                                    @else
                                        {{ $invoice->customer_name ?: '—' }}
                                    @endif
                                </td>
                                <td>{{ optional($invoice->invoice_date)->format('d M Y') }}</td>
                                <td>Rs {{ number_format((float) $invoice->total_amount, 0) }}</td>
                                <td><span class="status">{{ $invoice->status }}</span></td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn action-icon" title="Print">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8V4h10v4M7 17H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-1M7 14h10v6H7v-6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </a>
                                        <a href="{{ route('invoices.download', $invoice) }}" class="btn action-icon" title="Download">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0l4-4m-4 4l-4-4M4 20h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </a>
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
            const studentCodeInput = document.getElementById('student_code_lookup');
            const studentIdInput = document.getElementById('student_id');
            const studentDetails = document.getElementById('student_details_display');
            const studentStatus = document.getElementById('studentLookupStatus');

            const clearStudent = (message, isError) => {
                if (studentIdInput) studentIdInput.value = '';
                if (studentDetails) studentDetails.value = '';
                if (studentStatus) {
                    studentStatus.textContent = message;
                    studentStatus.classList.toggle('error', !!isError);
                }
            };

            const lookupStudent = async () => {
                if (!studentCodeInput || !studentIdInput || !studentStatus) return;
                const code = studentCodeInput.value.trim().toUpperCase();
                if (!code) {
                    clearStudent('Enter Student ID to load student.', false);
                    return;
                }
                studentStatus.textContent = 'Looking up student...';
                studentStatus.classList.remove('error');
                try {
                    const url = new URL('{{ route('invoices.student-lookup') }}', window.location.origin);
                    url.searchParams.set('student_code', code);
                    const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    if (!res.ok || !data.found) {
                        clearStudent(data.message || 'Student not found.', true);
                        return;
                    }
                    studentIdInput.value = data.id;
                    if (studentDetails) {
                        studentDetails.value = `${data.full_name} — ${data.class_name} ${data.section} — Father: ${data.father_name || '-'}`;
                    }
                    studentStatus.textContent = 'Student loaded.';
                    studentStatus.classList.remove('error');
                } catch (_) {
                    clearStudent('Unable to look up student right now.', true);
                }
            };

            studentCodeInput?.addEventListener('change', lookupStudent);
            studentCodeInput?.addEventListener('blur', lookupStudent);
            let studentLookupTimer = null;
            studentCodeInput?.addEventListener('input', () => {
                if (studentLookupTimer) clearTimeout(studentLookupTimer);
                studentLookupTimer = setTimeout(lookupStudent, 450);
            });

            const invoiceForm = document.getElementById('invoiceForm');
            invoiceForm?.addEventListener('submit', async function (e) {
                if (!studentIdInput?.value && studentCodeInput?.value.trim()) {
                    e.preventDefault();
                    await lookupStudent();
                    if (studentIdInput?.value) {
                        invoiceForm.submit();
                    }
                }
            });

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