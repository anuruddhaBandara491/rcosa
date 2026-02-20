@extends('layouts.app')

@section('title', 'Edit Monthly Payment')
@section('page-title', 'Edit Monthly Payment')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; }
.pay-layout { display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start; }
@media(max-width:991px){ .pay-layout{grid-template-columns:1fr;} }
.obs-card { background:#fff;border:1px solid #e4e9f0;border-radius:16px;overflow:hidden;animation:fadeUp .3s ease both;margin-bottom:18px; }
.obs-card-header { padding:15px 22px;border-bottom:1px solid #f0f3f8;background:#fafbfd;display:flex;align-items:center;gap:10px; }
.obs-card-header i { font-size:17px;color:var(--gold); }
.obs-card-header h4 { font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--navy);margin:0; }
.obs-card-body { padding:22px; }
.form-label { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#5a7194;margin-bottom:6px; }
.form-control, .form-select { border-radius:10px;border:1px solid #dde3ef;font-size:14px;padding:10px 14px;color:#1a2b44;transition:border-color .2s, box-shadow .2s; }
.form-control:focus, .form-select:focus { border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.15);outline:none; }
.form-control.is-invalid { border-color:#dc3545; }
.invalid-feedback { font-size:11.5px; }

.member-bar { background:linear-gradient(135deg,var(--navy),#1e3a5f);border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;color:#fff;margin-bottom:16px; }
.bar-avatar { width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,var(--gold),#f0d080);color:var(--navy);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:18px;font-weight:700;flex-shrink:0; }
.bar-name   { font-weight:700;font-size:14px; }
.bar-sub    { font-size:12px;color:#7a9abc; }

.calc-panel { background:var(--navy);border-radius:16px;padding:24px;color:#fff;position:sticky;top:84px;animation:fadeUp .3s .1s ease both; }
.calc-title { font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:8px; }
.calc-title i { color:var(--gold); }
.calc-row { display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.07);font-size:13.5px; }
.calc-row:last-of-type { border-bottom:none; }
.calc-label { color:#7a9abc; }
.calc-val   { font-weight:700;color:#fff;font-size:15px; }
.calc-val.green { color:#5de89a; }
.calc-val.red   { color:#f07070; }
.calc-val.gold  { color:var(--gold); }

.prog-wrap { background:rgba(255,255,255,.1);border-radius:99px;height:10px;overflow:hidden;margin:16px 0 6px; }
.prog-bar  { height:100%;border-radius:99px;background:linear-gradient(90deg,var(--gold),#f0d080);transition:width .3s ease; }
.calc-pct  { font-size:12px;color:#7a9abc;text-align:right;margin-bottom:16px; }
.status-preview { text-align:center;padding:10px 16px;border-radius:10px;font-size:13px;font-weight:700;letter-spacing:.5px;margin-top:10px; }
.status-preview.paid    { background:rgba(93,232,154,.15);color:#5de89a; }
.status-preview.partial { background:rgba(201,168,76,.2);color:var(--gold); }
.status-preview.unpaid  { background:rgba(240,112,112,.15);color:#f07070; }

.btn-submit { background:var(--gold);color:var(--navy);border:none;border-radius:10px;padding:12px 24px;font-size:14px;font-weight:700;cursor:pointer;width:100%;display:flex;align-items:center;justify-content:center;gap:7px;margin-top:4px;transition:background .2s; }
.btn-submit:hover { background:#f0d080; }
.btn-cancel { background:rgba(255,255,255,.08);color:#aac0d8;border:1px solid rgba(255,255,255,.15);border-radius:10px;padding:10px;font-size:13px;font-weight:600;text-decoration:none;display:block;text-align:center;margin-top:8px; }
.btn-cancel:hover { background:rgba(255,255,255,.15);color:#fff; }

.month-chip { display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:4px 10px;border-radius:8px;background:#e8ecf5;color:#1e3a5f; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header mb-3">
    <h1>Edit Payment — {{ $payment->month_label }}</h1>
    <p>Adjust the paid amount for this month. Total fee: <strong>Rs {{ number_format($fee, 2) }}</strong></p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Please fix these errors:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('monthly-payments.update', $payment) }}" id="editForm">
@csrf @method('PUT')
<input type="hidden" id="totalFeeVal" value="{{ $fee }}">

<div class="pay-layout">
<div>

    {{-- Member bar --}}
    <div class="member-bar">
        <div class="bar-avatar">{{ strtoupper(substr($payment->member->name_with_initials, 0, 1)) }}</div>
        <div>
            <div class="bar-name">{{ $payment->member->name_with_initials }}</div>
            <div class="bar-sub">{{ $payment->member->nic_number }} · {{ $payment->member->phone_number }}</div>
        </div>
        <div style="margin-left:auto;">
            <span class="month-chip"><i class="bi bi-calendar2 me-1"></i>{{ $payment->month_label }}</span>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="obs-card">
        <div class="obs-card-header"><i class="bi bi-cash-coin"></i><h4>Payment Details</h4></div>
        <div class="obs-card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Total Monthly Fee</label>
                    <div class="form-control" style="background:#f4f6fb;font-weight:700;color:var(--navy);">Rs {{ number_format($fee, 2) }}</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Total Paid Amount <span style="color:#c0392b;">*</span></label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-weight:700;color:#8494a9;">Rs</span>
                        <input type="number" name="paid_amount" id="paidInput"
                               class="form-control @error('paid_amount') is-invalid @enderror"
                               style="padding-left:36px;"
                               step="0.01" min="0" max="{{ $fee }}"
                               value="{{ old('paid_amount', $payment->paid_amount) }}" required>
                    </div>
                    <div style="font-size:11px;color:#8494a9;margin-top:4px;">Enter the <strong>total</strong> cumulative amount paid for this month.</div>
                    @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
        <div class="calc-title"><i class="bi bi-calculator-fill"></i> Payment Summary</div>
        <div class="calc-row"><span class="calc-label">Monthly Fee</span><span class="calc-val">Rs {{ number_format($fee, 2) }}</span></div>
        <div class="calc-row"><span class="calc-label">Paid Amount</span><span class="calc-val green" id="calcPaid">Rs {{ number_format($payment->paid_amount, 2) }}</span></div>
        <div class="calc-row"><span class="calc-label">Balance</span><span class="calc-val red" id="calcBalance">Rs {{ number_format($payment->balance_amount, 2) }}</span></div>
        <div class="prog-wrap"><div class="prog-bar" id="calcBar" style="width:{{ $payment->progress_percent }}%"></div></div>
        <div class="calc-pct" id="calcPct">{{ $payment->progress_percent }}% paid</div>
        <div class="status-preview {{ $payment->status }}" id="calcStatus">
            @if($payment->status==='paid') <i class="bi bi-check-circle-fill me-1"></i> FULLY PAID
            @elseif($payment->status==='partial') <i class="bi bi-hourglass-split me-1"></i> PARTIAL
            @else <i class="bi bi-x-circle me-1"></i> UNPAID @endif
        </div>
        <div class="mt-4">
            <button type="submit" class="btn-submit"><i class="bi bi-check-circle-fill"></i> Update Payment</button>
            <a href="{{ route('monthly-payments.show', $payment) }}" class="btn-cancel">Cancel</a>
        </div>
    </div>
</div>
</div>
</form>
@endsection

@push('scripts')
<script>
const FEE   = parseFloat(document.getElementById('totalFeeVal').value);
const input = document.getElementById('paidInput');
function fmt(n) { return 'Rs ' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,','); }
function calc() {
    const paid    = Math.min(FEE, Math.max(0, parseFloat(input.value) || 0));
    const balance = Math.max(0, FEE - paid);
    const pct     = FEE > 0 ? (paid / FEE * 100) : 0;
    document.getElementById('calcPaid').textContent    = fmt(paid);
    document.getElementById('calcBalance').textContent = fmt(balance);
    document.getElementById('calcBar').style.width     = pct.toFixed(1) + '%';
    document.getElementById('calcPct').textContent     = pct.toFixed(0) + '% paid';
    const s = document.getElementById('calcStatus');
    s.className = 'status-preview';
    if (paid >= FEE)  { s.classList.add('paid');    s.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> FULLY PAID'; }
    else if (paid > 0){ s.classList.add('partial'); s.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> PARTIAL'; }
    else              { s.classList.add('unpaid');  s.innerHTML = '<i class="bi bi-x-circle me-1"></i> UNPAID'; }
}
input.addEventListener('input', calc);
</script>
@endpush
