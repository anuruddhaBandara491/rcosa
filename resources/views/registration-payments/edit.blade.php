@extends('layouts.app')

@section('title', 'Edit Payment')
@section('page-title', 'Edit Registration Payment')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; }
.pay-layout { display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:start; }
@media(max-width:991px){ .pay-layout{grid-template-columns:1fr;} }
.form-card { background:#fff; border:1px solid #e4e9f0; border-radius:16px; overflow:hidden; animation:fadeUp .3s ease; margin-bottom:20px; }
.form-card-header { padding:16px 22px; border-bottom:1px solid #f0f3f8; background:#fafbfd; display:flex; align-items:center; gap:10px; }
.form-card-header i { font-size:18px; color:var(--gold); }
.form-card-header h4 { font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:var(--navy); margin:0; }
.form-card-body { padding:24px; }
.form-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.8px; color:#5a7194; margin-bottom:6px; }
.req { color:#c0392b; }
.form-control, .form-select { border-radius:10px; border:1px solid #dde3ef; font-size:14px; padding:10px 14px; color:#1a2b44; transition:border-color .2s, box-shadow .2s; }
.form-control:focus, .form-select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); outline:none; }
.form-control.is-invalid { border-color:#dc3545; }
.invalid-feedback { font-size:11.5px; }

.calc-panel { background:var(--navy); border-radius:16px; padding:24px; color:#fff; position:sticky; top:84px; animation:fadeUp .3s .1s ease both; }
.calc-title { font-family:'Playfair Display',serif; font-size:16px; font-weight:700; color:#fff; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
.calc-title i { color:var(--gold); }
.calc-row { display:flex; justify-content:space-between; align-items:center; padding:11px 0; border-bottom:1px solid rgba(255,255,255,.07); font-size:13.5px; }
.calc-row:last-of-type { border-bottom:none; }
.calc-label { color:#7a9abc; }
.calc-val { font-weight:700; color:#fff; font-size:15px; }
.calc-val.green { color:#5de89a; }
.calc-val.amber { color:var(--gold); }
.calc-val.red   { color:#f07070; }
.calc-progress-wrap { background:rgba(255,255,255,.1); border-radius:99px; height:10px; overflow:hidden; margin:18px 0 6px; }
.calc-progress-bar  { height:100%; border-radius:99px; background:linear-gradient(90deg,var(--gold),#f0d080); transition:width .3s ease; }
.calc-pct { font-size:12px; color:#7a9abc; text-align:right; margin-bottom:18px; }
.status-preview { text-align:center; padding:10px 16px; border-radius:10px; font-size:13px; font-weight:700; letter-spacing:.5px; margin-top:12px; }
.status-preview.paid    { background:rgba(93,232,154,.15); color:#5de89a; }
.status-preview.partial { background:rgba(201,168,76,.2);  color:var(--gold); }
.status-preview.unpaid  { background:rgba(240,112,112,.15);color:#f07070; }

.btn-submit { background:var(--gold); color:var(--navy); border:none; border-radius:10px; padding:12px 28px; font-size:14px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; width:100%; margin-top:4px; transition:background .2s; }
.btn-submit:hover { background:#f0d080; }
.btn-cancel { background:#f4f6fb; color:#3d5270; border:1px solid #dde3ef; border-radius:10px; padding:11px; font-size:13.5px; font-weight:600; text-decoration:none; display:block; text-align:center; margin-top:8px; }
.btn-cancel:hover { background:#e4e9f0; color:var(--navy); }

.member-info-box { background:#f4f6fb; border:1px solid #e4e9f0; border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px; }
.mi-avatar { width:38px; height:38px; border-radius:9px; background:linear-gradient(135deg,#1e3a5f,#0f1f3d); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:15px; font-weight:700; flex-shrink:0; }
.mi-name { font-weight:700; color:var(--navy); font-size:14px; }
.mi-nic  { font-size:12px; color:#8494a9; font-family:monospace; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header mb-3">
    <h1>Edit Payment — {{ $payment->member->name_with_initials }}</h1>
    <p>Adjust the paid amount. Total fee: <strong>Rs {{ number_format($fee, 2) }}</strong></p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Please fix the errors below:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('registration-payments.update', $payment) }}" id="payForm">
@csrf @method('PUT')
<input type="hidden" id="totalFeeVal" value="{{ $fee }}">

<div class="pay-layout">
<div>

    {{-- Member Info (read-only) --}}
    <div class="form-card">
        <div class="form-card-header">
            <i class="bi bi-person-fill"></i>
            <h4>Member</h4>
        </div>
        <div class="form-card-body">
            <div class="member-info-box">
                <div class="mi-avatar">{{ strtoupper(substr($payment->member->name_with_initials, 0, 1)) }}</div>
                <div>
                    <div class="mi-name">{{ $payment->member->name_with_initials }}</div>
                    <div class="mi-nic">{{ $payment->member->nic_number }}</div>
                </div>
                <a href="{{ route('members.show', $payment->member) }}"
                   style="margin-left:auto;font-size:12px;color:var(--gold);text-decoration:none;">
                    View Profile <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Payment Details --}}
    <div class="form-card">
        <div class="form-card-header">
            <i class="bi bi-cash-coin"></i>
            <h4>Payment Details</h4>
        </div>
        <div class="form-card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Total Registration Fee</label>
                    <div class="form-control" style="background:#f4f6fb;font-weight:700;color:var(--navy);">
                        Rs {{ number_format($fee, 2) }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Total Paid Amount <span class="req">*</span></label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-weight:700;color:#8494a9;">Rs</span>
                        <input type="number" name="paid_amount" id="paidAmountInput"
                               class="form-control @error('paid_amount') is-invalid @enderror"
                               style="padding-left:36px;"
                               step="0.01" min="0" max="{{ $fee }}"
                               value="{{ old('paid_amount', $payment->paid_amount) }}" required>
                    </div>
                    <div style="font-size:11px;color:#8494a9;margin-top:4px;">
                        Enter the <strong>total</strong> amount paid so far (not just this installment).
                    </div>
                    @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Payment Date <span class="req">*</span></label>
                    <input type="date" name="payment_date"
                           class="form-control @error('payment_date') is-invalid @enderror"
                           value="{{ old('payment_date', $payment->payment_date?->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}" required>
                    @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Receipt Number</label>
                    <input type="text" name="receipt_number"
                           class="form-control @error('receipt_number') is-invalid @enderror"
                           value="{{ old('receipt_number', $payment->receipt_number) }}"
                           placeholder="optional">
                    @error('receipt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2"
                              class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Any remarks…">{{ old('notes', $payment->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>
        </div>
    </div>

</div>

{{-- CALCULATOR PANEL --}}
<div>
    <div class="calc-panel">
        <div class="calc-title"><i class="bi bi-calculator-fill"></i> Payment Summary</div>

        <div class="calc-row">
            <span class="calc-label">Registration Fee</span>
            <span class="calc-val">Rs {{ number_format($fee, 2) }}</span>
        </div>
        <div class="calc-row">
            <span class="calc-label">Total Paid</span>
            <span class="calc-val green" id="calcTotalPaid">Rs {{ number_format($payment->paid_amount, 2) }}</span>
        </div>
        <div class="calc-row">
            <span class="calc-label">Remaining Balance</span>
            <span class="calc-val red" id="calcBalance">Rs {{ number_format($payment->balance_amount, 2) }}</span>
        </div>

        <div class="calc-progress-wrap">
            <div class="calc-progress-bar" id="calcProgressBar" style="width:{{ $payment->progress_percent }}%"></div>
        </div>
        <div class="calc-pct" id="calcPct">{{ $payment->progress_percent }}% paid</div>

        <div class="status-preview {{ $payment->status }}" id="calcStatusBadge">
            @if($payment->status === 'paid')
                <i class="bi bi-check-circle-fill me-1"></i> FULLY PAID
            @elseif($payment->status === 'partial')
                <i class="bi bi-hourglass-split me-1"></i> PARTIAL PAYMENT
            @else
                <i class="bi bi-x-circle me-1"></i> UNPAID
            @endif
        </div>

        <div class="mt-4">
            <button type="submit" class="btn-submit">
                <i class="bi bi-check-circle-fill"></i> Update Payment
            </button>
            <a href="{{ route('registration-payments.show', $payment) }}" class="btn-cancel">Cancel</a>
        </div>
    </div>
</div>

</div>
</form>
@endsection

@push('scripts')
<script>
const TOTAL_FEE = parseFloat(document.getElementById('totalFeeVal').value);
const paidInput = document.getElementById('paidAmountInput');

function fmt(n) { return 'Rs ' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

function updateCalc() {
    const paid    = Math.min(TOTAL_FEE, Math.max(0, parseFloat(paidInput.value) || 0));
    const balance = Math.max(0, TOTAL_FEE - paid);
    const pct     = TOTAL_FEE > 0 ? (paid / TOTAL_FEE * 100) : 0;

    document.getElementById('calcTotalPaid').textContent   = fmt(paid);
    document.getElementById('calcBalance').textContent     = fmt(balance);
    document.getElementById('calcProgressBar').style.width = pct.toFixed(1) + '%';
    document.getElementById('calcPct').textContent         = pct.toFixed(0) + '% paid';

    const badge = document.getElementById('calcStatusBadge');
    badge.className = 'status-preview';
    if (paid >= TOTAL_FEE)    { badge.classList.add('paid');    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> FULLY PAID'; }
    else if (paid > 0)        { badge.classList.add('partial'); badge.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> PARTIAL PAYMENT'; }
    else                      { badge.classList.add('unpaid');  badge.innerHTML = '<i class="bi bi-x-circle me-1"></i> UNPAID'; }
}

paidInput.addEventListener('input', updateCalc);
updateCalc();
</script>
@endpush
