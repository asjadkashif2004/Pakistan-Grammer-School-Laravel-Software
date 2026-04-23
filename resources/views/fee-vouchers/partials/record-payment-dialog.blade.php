@php
    $paymentUrlTemplate = $paymentUrlTemplate ?? url('/fee-vouchers/__ID__/payments');
@endphp

<style>
    #record-payment-dialog.fv-modal::backdrop { background: rgba(15, 40, 20, 0.35); }
</style>

<dialog class="fv-modal" id="record-payment-dialog" aria-labelledby="record-payment-title" style="border:1px solid #d4ead4;border-radius:14px;padding:0;max-width:min(520px,100vw - 24px);width:100%;">
    <form method="POST" action="" id="record-payment-form">
        @csrf
        <div class="fv-modal-head" style="padding:14px 16px;border-bottom:1px solid #e7f3e7;font-weight:900;color:#1f3f24;display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <span id="record-payment-title">Record payment</span>
            <button type="button" class="btn ghost icon-only" data-close-payment aria-label="Close" style="border:1px solid #d4ead4;border-radius:10px;padding:8px 12px;background:#f5fff6;cursor:pointer;">&times;</button>
        </div>
        <div class="fv-modal-body" style="padding:14px 16px 16px;">
            <p id="record-payment-meta" style="margin:0 0 10px;color:#47624a;font-weight:700;font-size:13px;"></p>
            <p id="pay_remaining_live" style="margin:0 0 12px;font-size:13px;font-weight:700;color:#0f7a35;"></p>
            <div class="field" style="display:flex;flex-direction:column;gap:6px;margin-bottom:10px;">
                <label for="pay_amount" style="font-size:12px;font-weight:700;color:#1d4589;text-transform:uppercase;">Amount (Rs)</label>
                <input id="pay_amount" name="amount" type="number" step="0.01" min="0.01" required style="border:1px solid #dbe8fb;border-radius:10px;padding:10px 12px;">
            </div>
            <div class="field" style="display:flex;flex-direction:column;gap:6px;margin-bottom:10px;">
                <label for="pay_paid_at" style="font-size:12px;font-weight:700;color:#1d4589;text-transform:uppercase;">Payment date</label>
                <input id="pay_paid_at" name="paid_at" type="date" value="{{ now()->toDateString() }}" required style="border:1px solid #dbe8fb;border-radius:10px;padding:10px 12px;">
            </div>
            <div class="field" style="display:flex;flex-direction:column;gap:6px;margin-bottom:10px;">
                <label for="pay_notes" style="font-size:12px;font-weight:700;color:#1d4589;text-transform:uppercase;">Notes</label>
                <input id="pay_notes" name="notes" type="text" maxlength="500" style="border:1px solid #dbe8fb;border-radius:10px;padding:10px 12px;">
            </div>
            <div class="actions" style="margin-bottom:0;display:flex;gap:8px;flex-wrap:wrap;">
                <button type="submit" class="btn primary" style="border:1px solid #0f7a35;border-radius:10px;padding:10px 12px;background:#0f7a35;color:#fff;font-weight:700;cursor:pointer;">Save payment</button>
                <button type="button" class="btn" data-close-payment style="border:1px solid #d4ead4;border-radius:10px;padding:10px 12px;background:#fff;cursor:pointer;">Cancel</button>
            </div>
        </div>
    </form>
</dialog>

{{-- Inline script (not @push): ensures Pay works even if stack order or @include/@push interaction misses the scripts stack. --}}
<script>
(function () {
    const template = @json($paymentUrlTemplate);
    let maxRemaining = 0;

    function round2(n) {
        return Math.round(n * 100) / 100;
    }

    function syncLive() {
        const payAmount = document.getElementById('pay_amount');
        const payLive = document.getElementById('pay_remaining_live');
        if (!payAmount || !payLive) return;
        const entered = parseFloat(String(payAmount.value).replace(',', '.')) || 0;
        const after = Math.max(0, round2(maxRemaining - entered));
        payLive.textContent = 'After this payment, remaining balance: Rs ' + after.toFixed(2);
    }

    function openPaymentModal(id, voucherLabel, remaining) {
        const payDialog = document.getElementById('record-payment-dialog');
        const payForm = document.getElementById('record-payment-form');
        const payMeta = document.getElementById('record-payment-meta');
        const payAmount = document.getElementById('pay_amount');
        if (!payDialog || !payForm || !payMeta || !payAmount || !template) {
            console.warn('Fee payment modal: missing DOM or template.');
            return;
        }
        const url = String(template).replace('__ID__', String(id));
        payForm.setAttribute('action', url);
        maxRemaining = Number(remaining) || 0;
        payMeta.textContent = String(voucherLabel) + ' — maximum payment Rs ' + maxRemaining.toFixed(2);
        payAmount.value = '';
        payAmount.setAttribute('max', String(maxRemaining));
        syncLive();
        try {
            payDialog.showModal();
        } catch (e) {
            console.error(e);
            alert('Could not open payment window. Try a modern browser (Chrome, Edge, Firefox).');
        }
    }

    window.openRecordPayment = openPaymentModal;

    if (!window.__feePaymentUiBound) {
        window.__feePaymentUiBound = true;

        document.addEventListener('click', function (e) {
            const btn = e.target && e.target.closest ? e.target.closest('[data-open-payment]') : null;
            if (!btn) return;
            e.preventDefault();
            const id = btn.getAttribute('data-voucher-id');
            const label = btn.getAttribute('data-voucher-label') || 'Voucher';
            const remaining = parseFloat(btn.getAttribute('data-remaining') || '0');
            if (!id) return;
            openPaymentModal(id, label, remaining);
        });

        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'pay_amount') {
                syncLive();
            }
        });

        document.addEventListener('click', function (e) {
            const t = e.target;
            if (!t || !t.closest) return;
            const closeBtn = t.closest('[data-close-payment]');
            if (!closeBtn) return;
            const payDialog = document.getElementById('record-payment-dialog');
            if (payDialog && typeof payDialog.close === 'function') {
                payDialog.close();
            }
        });
    }

    if (!window.__feePaymentFormAjaxBound) {
        window.__feePaymentFormAjaxBound = true;
        document.addEventListener('submit', async function (e) {
            const form = e.target;
            if (!form || form.id !== 'record-payment-form') return;
            e.preventDefault();
            const action = form.getAttribute('action');
            if (!action) return;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                const res = await fetch(action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                    },
                    body: new FormData(form),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    let msg = data.message || 'Could not save payment.';
                    if (data.errors && typeof data.errors === 'object') {
                        const first = Object.values(data.errors).flat()[0];
                        if (first) msg = first;
                    }
                    alert(msg);
                    if (submitBtn) submitBtn.disabled = false;
                    return;
                }
                const payDialog = document.getElementById('record-payment-dialog');
                if (payDialog && typeof payDialog.close === 'function') {
                    payDialog.close();
                }
                if (data.print_url) {
                    window.open(data.print_url, '_blank', 'noopener,noreferrer');
                }
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            } catch {
                alert('Network error. Please try again.');
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }
})();
</script>
