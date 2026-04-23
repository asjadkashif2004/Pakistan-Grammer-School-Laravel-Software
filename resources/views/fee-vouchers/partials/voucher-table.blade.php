<table class="table">
    <thead>
        <tr>
            <th>Voucher #</th>
            <th>Month</th>
            <th>Due date</th>
            <th>Total</th>
            <th>Received</th>
            <th>Remaining</th>
            <th>Status</th>
            <th>Ledger</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $row)
            @php
                $row->unsetRelation('payments');
                $recv = $row->totalPaidAmount();
                $rem = $row->remainingAmount();
                $docOk = (bool) $row->voucher_generated_at;
                $statusClass = match ($row->status) {
                    'Paid' => 'paid',
                    'Partial' => 'partial',
                    'Overdue' => 'overdue',
                    default => 'unpaid',
                };
            @endphp
            <tr>
                <td>{{ $row->voucher_number ?? '—' }}</td>
                <td>{{ optional($row->billing_month)->format('M Y') }}</td>
                <td>{{ optional($row->due_date)->format('d M Y') }}</td>
                <td>Rs {{ number_format((float) $row->amount, 2) }}</td>
                <td>Rs {{ number_format($recv, 2) }}</td>
                <td>Rs {{ number_format($rem, 2) }}</td>
                <td><span class="status {{ $statusClass }}">{{ $row->status }}</span></td>
                <td>
                    @if ($row->payments->isEmpty())
                        <span class="ledger-mini">No payments</span>
                    @else
                        <details>
                            <summary class="ledger-mini">{{ $row->payments->count() }} payment(s)</summary>
                            <div class="table-wrap" style="min-width:0;">
                                <table class="table" style="min-width: 360px;">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($row->payments->sortBy('paid_at') as $p)
                                            <tr>
                                                <td>{{ optional($p->paid_at)->format('d M Y') }}</td>
                                                <td>Rs {{ number_format((float) $p->amount, 2) }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($p->notes, 40) ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </details>
                    @endif
                </td>
                <td>
                    <div class="actions" style="margin:0;">
                        @if ($docOk)
                            <a href="{{ route('fee-vouchers.print', $row) }}" target="_blank" rel="noopener" class="btn primary icon-only" title="Print fee challan" aria-label="Print fee challan">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M7 3h10v5H7V3zM5 8h14a2 2 0 0 1 2 2v5h-3v4H6v-4H3v-5a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path d="M8 17h8v4H8v-4z" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                            </a>
                            <a href="{{ route('fee-vouchers.download', $row) }}" class="btn ghost icon-only" title="Download PDF" aria-label="Download PDF">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M12 4v11m0 0l-4-4m4 4l4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5 19h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                </svg>
                            </a>
                        @else
                            <span class="btn primary disabled icon-only" title="Generate the voucher first" aria-disabled="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M7 3h10v5H7V3zM5 8h14a2 2 0 0 1 2 2v5h-3v4H6v-4H3v-5a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path d="M8 17h8v4H8v-4z" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                            </span>
                            <span class="btn ghost disabled icon-only" aria-disabled="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M12 4v11m0 0l-4-4m4 4l4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5 19h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                </svg>
                            </span>
                        @endif

                        @if ($row->status !== 'Paid' && $docOk && $rem > 0.009)
                            <button
                                type="button"
                                class="btn warning"
                                onclick="openRecordPayment({{ $row->id }}, @json($row->voucher_number ?? 'Voucher'), {{ json_encode($rem) }})"
                            >Pay</button>
                        @endif

                        @if ($row->status !== 'Paid')
                            <a href="{{ route('fee-vouchers.edit', $row) }}" class="btn">Edit</a>
                            <form method="POST" action="{{ route('fee-vouchers.destroy', $row) }}" style="display:inline;" onsubmit="return confirm('Delete this voucher?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn danger">Delete</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="9">No vouchers in this list.</td></tr>
        @endforelse
    </tbody>
</table>
