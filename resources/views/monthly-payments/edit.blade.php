@extends('layouts.app')

@section('title', 'Edit Payment — ' . $payment->member->name_with_initials)
@section('page-title', 'Edit Monthly Payment')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; --gold-lt:#f0d080; }

.pay-layout { display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start; }
@media(max-width:991px){ .pay-layout{grid-template-columns:1fr;} }

.obs-card { background:#fff;border:1px solid #e4e9f0;border-radius:16px;overflow:hidden;animation:fadeUp .3s ease both;margin-bottom:18px; }
.obs-card-header { padding:15px 22px;border-bottom:1px solid #f0f3f8;background:#fafbfd;display:flex;align-items:center;gap:10px;border-radius:16px 16px 0 0; }
.obs-card-header i { font-size:17px;color:var(--gold); }
.obs-card-header h4 { font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--navy);margin:0; }
.obs-card-body { padding:22px; }

.form-label { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#5a7194;margin-bottom:6px; }
.form-control,.form-select { border-radius:10px;border:1.5px solid #dde3ef;font-size:14px;padding:10px 14px;color:#1a2b44;transition:border-color .2s,box-shadow .2s; }
.form-control:focus,.form-select:focus { border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.15);outline:none; }
.form-control.is-invalid { border-color:#dc3545; }
.invalid-feedback { font-size:11.5px; }

/* Member bar */
.member-bar { background:linear-gradient(135deg,var(--navy),#1e3a5f);border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;color:#fff;margin-bottom:18px; }
.bar-avatar { width:44px;height:44px;border-radius:11px;background:linear-gradient(135deg,var(--gold),var(--gold-lt));color:var(--navy);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:19px;font-weight:700;flex-shrink:0; }
.bar-name { font-weight:700;font-size:15px; }
.bar-sub  { font-size:12px;color:#7a9abc;margin-top:2px; }

/* Context info box */
.context-box { background:#f8faff;border:1.5px solid #e4e9f0;border-radius:12px;padding:16px 18px;margin-bottom:18px; }
.ctx-row { display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f0f3f8;font-size:13px; }
.ctx-row:last-child { border-bottom:none; }
.ctx-label { color:#5a7194;font-weight:500; }
.ctx-val { font-weight:700;color:#1a2b44; }

/* Big amount input */
.big-amount-box { border:2px solid #dde3ef;border-radius:14px;padding:18px 20px;background:#fafbff;transition:border-color .2s,box-shadow .2s; }
.big-amount-box:focus-within { border-color:var(--gold);box-shadow:0 0 0 4px rgba(201,168,76,.1); }
.big-amount-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#5a7194;margin-bottom:10px;display:flex;align-items:center;gap:6px; }
.big-amount-row { display:flex;align-items:center;gap:8px; }
.big-prefix { font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:#8494a9;flex-shrink:0; }
#paidInput {
    font-family:'Playfair Display',serif;font-size:32px;font-weight:700;color:var(--navy);
    border:none;background:transparent;outline:none;width:100%;min-width:80px;
    -moz-appearance:textfield;
}
#paidInput::-webkit-inner-spin-button,
#paidInput::-webkit-outer-spin-button { -webkit-appearance:none;margin:0; }
#paidInput::placeholder { color:#cbd5e1; }

/* Calc panel */
.calc-panel { background:var(--navy);border-radius:16px;padding:24px;color:#fff;position:sticky;top:84px;animation:fadeUp .3s .1s ease both; }
.calc-title { font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:8px; }
.calc-title i { color:var(--gold); }
.calc-row { display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.07);font-size:13.5px; }
.calc-row:last-of-type { border-bottom:none; }
.calc-label { color:#7a9abc; }
.calc-val { font-weight:700;color:#fff;font-size:15px; }
.calc-val.green  { color:#4ade80; }
.calc-val.red    { color:#f87171; }
.calc-val.gold   { color:var(--gold); }
.calc-val.purple { color:#c4b5fd; }

.prog-wrap { background:rgba(255,255,255,.1);border-radius:99px;height:9px;overflow:hidden;margin:16px 0 5px; }
.prog-bar  { height:100%;border-radius:99px;background:linear-gradient(90deg,var(--gold),var(--gold-lt));transition:width .3s ease; }
.calc-pct  { font-size:12px;color:#7a9abc;text-align:right;margin-bottom:14px; }

.status-preview { text-align:center;padding:11px 16px;border-radius:10px;font-size:13px;font-weight:700;letter-spacing:.5px;margin-top:8px; }
.status-preview.paid     { background:rgba(74,222,128,.15);color:#4ade80; }
.status-preview.partial  { background:rgba(251,191,36,.15);color:#fbbf24; }
.status-preview.overpaid { background:rgba(196,181,253,.15);color:#c4b5fd; }

.btn-submit { background:var(--gold);color:var(--navy);border:none;border-radius:10px;padding:13px 24px;font-size:14px;font-weight:700;cursor:pointer;width:100%;display:flex;align-items:center;justify-content:center;gap:7px;margin-top:4px;transition:opacity .2s; }
.btn-submit:hover { opacity:.88; }
.btn-cancel { background:rgba(255,255,255,.08);color:#aac0d8;border:1px solid rgba(255,255,255,.15);border-radius:10px;padding:10px;font-size:13px;font-weight:600;text-decoration:none;display:block;text-align:center;margin-top:8px; }
.btn-cancel:hover { background:rgba(255,255,255,.15);color:#fff; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header mb-3">
    <h1>Edit Transaction — {{ $payment->member->name_with_initials }}</h1>
    <p>Adjust the amount for this payment transaction. The overall member status will be recalculated automatically.</p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Please fix these errors:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Pass PHP values to JS --}}
<script>
    const MONTHLY_FEE   = {{ $fee }};
    const TOTAL_DUE     = {{ (float)$payment->total_due }};
    const OTHER_PAID    = {{ $otherPaid }};   {{-- sum of all OTHER transactions --}}
</script>

<form method="POST" action="{{ route('monthly-payments.update', $payment) }}" id="editForm">
@csrf @method('PUT')

<div class="pay-layout">
<div>

    {{-- Member bar --}}
    <div class="member-bar">
        <div class="bar-avatar">{{ strtoupper(substr($payment->member->name_with_initials, 0, 1)) }}</div>
        <div>
            <div class="bar-name">{{ $payment->member->name_with_initials }}</div>
            <div class="bar-sub">{{ $payment->member->nic_number }} &middot; {{ $payment->member->phone_number }}</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <div style="font-size:10.5px;color:#7a9abc;text-transform:uppercase;letter-spacing:1px;">Recorded</div>
            <div style="font-size:13px;font-weight:600;">{{ $payment->payment_date?->format('d M Y') }}</div>
        </div>
    </div>

    {{-- Context: other transactions for this member --}}
    <div class="context-box">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.9px;color:#5a7194;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
            <i class="bi bi-info-circle" style="color:var(--gold);"></i> Member Payment Context
        </div>
        <div class="ctx-row">
            <span class="ctx-label">Monthly Fee</span>
            <span class="ctx-val">Rs {{ number_format($fee, 2) }}</span>
        </div>
        <div class="ctx-row">
            <span class="ctx-label">Total Due (all months)</span>
            <span class="ctx-val">Rs {{ number_format($payment->total_due, 2) }}</span>
        </div>
        <div class="ctx-row">
            <span class="ctx-label">Other Transactions (excluding this)</span>
            <span class="ctx-val" style="color:#15803d;">Rs {{ number_format($otherPaid, 2) }}</span>
        </div>
        <div class="ctx-row">
            <span class="ctx-label">This Transaction (current)</span>
            <span class="ctx-val" style="color:var(--gold);">Rs {{ number_format($payment->paid_amount, 2) }}</span>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="obs-card">
        <div class="obs-card-header">
            <i class="bi bi-cash-coin"></i>
            <h4>Update Payment Amount</h4>
        </div>
        <div class="obs-card-body">
            <div class="row g-3">

                {{-- Big amount input --}}
                <div class="col-12">
                    <label class="form-label">Amount Paid in This Transaction <span style="color:#c0392b;">*</span></label>
                    <div class="big-amount-box">
                        <div class="big-amount-label"><i class="bi bi-cash-stack"></i> Payment Amount</div>
                        <div class="big-amount-row">
                            <span class="big-prefix">Rs</span>
                            <input type="number" name="paid_amount" id="paidInput"
                                   step="0.01" min="0.01"
                                   value="{{ old('paid_amount', number_format((float)$payment->paid_amount, 2, '.', '')) }}"
                                   placeholder="0.00" oninput="recalc()" required>
                        </div>
                    </div>
                    <div style="font-size:11.5px;color:#8494a9;margin-top:6px;">
                        <i class="bi bi-info-circle me-1"></i>
                        Enter the amount paid in <strong>this specific transaction</strong>. The total balance is calculated across all transactions.
                    </div>
                    @error('paid_amount')<div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Payment Date <span style="color:#c0392b;">*</span></label>
                    <input type="date" name="payment_date"
                           class="form-control @error('payment_date') is-invalid @enderror"
                           value="{{ old('payment_date', $payment->payment_date?->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}" required>
                    @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Receipt Number</label>
                    <input type="text" name="receipt_number" class="form-control"
                           value="{{ old('receipt_number', $payment->receipt_number) }}" placeholder="optional">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" class="form-control"
                           value="{{ old('notes', $payment->notes) }}" placeholder="optional">
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Calc panel --}}
<div>
    <div class="calc-panel">
        <div class="calc-title"><i class="bi bi-calculator-fill"></i> Live Recalculation</div>

        <div class="calc-row">
            <span class="calc-label">Total Due</span>
            <span class="calc-val">Rs {{ number_format($payment->total_due, 2) }}</span>
        </div>
        <div class="calc-row">
            <span class="calc-label">Other Transactions</span>
            <span class="calc-val green">Rs {{ number_format($otherPaid, 2) }}</span>
        </div>
        <div class="calc-row">
            <span class="calc-label">This Transaction</span>
            <span class="calc-val gold" id="calcThis">Rs {{ number_format($payment->paid_amount, 2) }}</span>
        </div>
        <div class="calc-row">
            <span class="calc-label">New Cumulative</span>
            <span class="calc-val green" id="calcCumulative">Rs {{ number_format($payment->cumulative_paid, 2) }}</span>
        </div>
        <div class="calc-row">
            <span class="calc-label">Balance After</span>
            <span class="calc-val" id="calcBalance" style="color:#f87171;">Rs {{ number_format(max(0, $payment->balance_amount), 2) }}</span>
        </div>

        <div class="prog-wrap"><div class="prog-bar" id="calcBar" style="width:{{ $payment->progress_percent }}%"></div></div>
        <div class="calc-pct" id="calcPct">{{ $payment->progress_percent }}% of balance covered</div>

        <div class="status-preview {{ $payment->status }}" id="calcStatus">
            @if($payment->status === 'paid')
                <i class="bi bi-check-circle-fill me-1"></i> FULLY PAID
            @elseif($payment->status === 'overpaid')
                <i class="bi bi-arrow-up-circle-fill me-1"></i> OVERPAID
            @else
                <i class="bi bi-hourglass-split me-1"></i> PARTIAL
            @endif
        </div>

        <div class="mt-4">
            <button type="submit" class="btn-submit">
                <i class="bi bi-check-circle-fill"></i> Update Transaction
            </button>
            <a href="{{ route('monthly-payments.show', $payment) }}" class="btn-cancel">Cancel</a>
        </div>
    </div>
</div>
</div>
</form>

@endsection

@push('scripts')
<script>
function fmt(n) {
    return 'Rs ' + Math.abs(parseFloat(n)).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function recalc() {
    const paidNow   = Math.max(0, parseFloat(document.getElementById('paidInput').value) || 0);
    const cumul     = OTHER_PAID + paidNow;
    const balance   = TOTAL_DUE - cumul;
    const pct       = TOTAL_DUE > 0 ? Math.min(100, (cumul / TOTAL_DUE) * 100) : 0;

    document.getElementById('calcThis').textContent       = fmt(paidNow);
    document.getElementById('calcCumulative').textContent = fmt(cumul);

    const balEl = document.getElementById('calcBalance');
    if (balance < 0) {
        balEl.textContent = '+' + fmt(Math.abs(balance)) + ' credit';
        balEl.style.color = '#c4b5fd';
    } else {
        balEl.textContent = fmt(balance);
        balEl.style.color = balance === 0 ? '#4ade80' : '#f87171';
    }

    document.getElementById('calcBar').style.width = pct.toFixed(1) + '%';
    document.getElementById('calcPct').textContent  = pct.toFixed(0) + '% of balance covered';

    const s = document.getElementById('calcStatus');
    s.className = 'status-preview';
    if (cumul >= TOTAL_DUE + 0.001) {
        s.classList.add('overpaid');
        s.innerHTML = '<i class="bi bi-arrow-up-circle-fill me-1"></i> OVERPAID';
        document.getElementById('calcBar').style.background = 'linear-gradient(90deg,#a78bfa,#c4b5fd)';
    } else if (cumul >= TOTAL_DUE - 0.001) {
        s.classList.add('paid');
        s.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> FULLY PAID';
        document.getElementById('calcBar').style.background = 'linear-gradient(90deg,#4ade80,#86efac)';
    } else {
        s.classList.add('partial');
        s.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> PARTIAL';
        document.getElementById('calcBar').style.background = 'linear-gradient(90deg,#c9a84c,#f0d080)';
    }
}

// Run on load so panel matches current value
recalc();
</script>
@endpush
