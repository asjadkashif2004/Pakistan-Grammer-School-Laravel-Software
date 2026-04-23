<table class="fv-table">
    <thead>
        <tr>
            <th>Voucher #</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Class / Sec</th>
            <th>Month</th>
            <th>Due</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Remaining</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($vouchers as $row)
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
                $st = $row->student;
                $cls = trim(implode(' ', array_filter([$st?->class_name, $st?->section]))) ?: '—';
            @endphp
            <tr>
                <td>
                    <div class="fv-voucher">{{ $row->voucher_number ?? '—' }}</div>
                    <div class="fv-sub">#{{ $row->id }}</div>
                </td>
                <td>
                    <strong>{{ $st?->student_code ?? '—' }}</strong>
                </td>
                <td>
                    <div>{{ $st?->full_name ?? '—' }}</div>
                    <div class="fv-sub">{{ $st?->father_name ? 'Guardian: '.$st->father_name : '—' }}</div>
                </td>
                <td>{{ $cls }}</td>
                <td>{{ optional($row->billing_month)->format('M Y') }}</td>
                <td>{{ optional($row->due_date)->format('d M Y') }}</td>
                <td><span class="fv-money">Rs {{ number_format((float) $row->amount, 2) }}</span></td>
                <td><span class="fv-money warn">Rs {{ number_format($recv, 2) }}</span></td>
                <td><span class="fv-money {{ $rem > 0.009 ? 'danger' : '' }}">Rs {{ number_format($rem, 2) }}</span></td>
                <td><span class="status {{ $statusClass }}">{{ $row->status }}</span></td>
                <td>
                    <div class="fv-actions" style="margin:0;">
                        @if ($docOk)
                            <a href="{{ route('fee-vouchers.print', $row) }}" target="_blank" rel="noopener" class="fv-btn fv-ib primary" data-tip="Print challan" aria-label="Print challan">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8V4h10v4M7 17H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-1M7 14h10v6H7v-6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            <a href="{{ route('fee-vouchers.download', $row) }}" class="fv-btn fv-ib ghost" data-tip="Download PDF" aria-label="Download PDF">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4v10m0 0 4-4m-4 4-4-4M5 19h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        @else
                            <span class="fv-btn fv-ib disabled" title="Generate voucher first" aria-label="Generate voucher first">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8V4h10v4M7 17H6a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-1M7 14h10v6H7v-6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <span class="fv-btn fv-ib disabled" aria-label="PDF disabled">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4v10m0 0 4-4m-4 4-4-4M5 19h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        @endif
                        @if ($row->status !== 'Paid' && $docOk && $rem > 0.009)
                            <button
                                type="button"
                                class="fv-btn fv-ib warn"
                                data-open-payment
                                data-voucher-id="{{ $row->id }}"
                                data-voucher-label="{{ e($row->voucher_number ?? 'Voucher') }}"
                                data-remaining="{{ number_format($rem, 2, '.', '') }}"
                                data-tip="Record payment"
                                aria-label="Record payment"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v18M7 8h8a3 3 0 1 0 0-6H9a3 3 0 0 0 0 6h6a3 3 0 0 1 0 6H8a3 3 0 1 0 0 6h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </button>
                        @endif
                        <a href="{{ route('fee-vouchers.index', ['student_code' => $st?->student_code, 'voucher' => $row->id]) }}" class="fv-btn fv-ib ghost" data-tip="Open workspace" aria-label="Open workspace">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15V9M12 15V7M16 15v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="11">No vouchers match the current filters.</td></tr>
        @endforelse
    </tbody>
</table>
