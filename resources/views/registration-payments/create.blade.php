@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Registration Payment')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; }

.pay-layout {
    display:grid;
    grid-template-columns: 1fr 380px;
    gap:20px;
    align-items:start;
}
@media(max-width:991px){ .pay-layout{grid-template-columns:1fr;} }

/* ── FORM CARD ────────────────────────────────────── */
.form-card {
    background:#fff; border:1px solid #e4e9f0;
    border-radius:16px; overflow:hidden;
    animation:fadeUp .3s ease;
}
.form-card-header {
    padding:16px 22px; border-bottom:1px solid #f0f3f8;
    background:#fafbfd; display:flex; align-items:center; gap:10px;
}
.form-card-header i { font-size:18px; color:var(--gold); }
.form-card-header h4 { font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:var(--navy); margin:0; }
.form-card-body { padding:24px; }

.form-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.8px; color:#5a7194; margin-bottom:6px; }
.req { color:#c0392b; margin-left:2px; }

.form-control, .form-select {
    border-radius:10px; border:1px solid #dde3ef;
    font-size:14px; padding:10px 14px; color:#1a2b44;
    transition:border-color .2s, box-shadow .2s;
}
.form-control:focus, .form-select:focus {
    border-color:var(--gold);
    box-shadow:0 0 0 3px rgba(201,168,76,.15);
    outline:none;
}
.form-control.is-invalid { border-color:#dc3545; }
.invalid-feedback { font-size:11.5px; }

/* ── LIVE CALCULATOR PANEL ────────────────────────── */
.calc-panel {
    background:var(--navy);
    border-radius:16px;
    padding:24px;
    color:#fff;
    position:sticky;
    top:84px;
    animation:fadeUp .3s .1s ease both;
}
.calc-title {
    font-family:'Playfair Display',serif;
    font-size:16px; font-weight:700;
    color:#fff; margin-bottom:20px;
    display:flex; align-items:center; gap:8px;
}
.calc-title i { color:var(--gold); }

.calc-row {
    display:flex; justify-content:space-between; align-items:center;
    padding:11px 0;
    border-bottom:1px solid rgba(255,255,255,.07);
    font-size:13.5px;
}
.calc-row:last-of-type { border-bottom:none; }
.calc-label { color:#7a9abc; }
.calc-val   { font-weight:700; color:#fff; font-size:15px; }
.calc-val.green  { color:#5de89a; }
.calc-val.amber  { color:var(--gold); }
.calc-val.red    { color:#f07070; }

.calc-progress-wrap {
    background:rgba(255,255,255,.1);
    border-radius:99px; height:10px; overflow:hidden;
    margin:18px 0 6px;
}
.calc-progress-bar {
    height:100%; border-radius:99px;
    background:linear-gradient(90deg, var(--gold), #f0d080);
    transition:width .3s ease;
}
.calc-pct {
    font-size:12px; color:#7a9abc;
    text-align:right; margin-bottom:18px;
}

.status-preview {
    text-align:center;
    padding:10px 16px;
    border-radius:10px;
    font-size:13px; font-weight:700;
    letter-spacing:.5px;
    margin-top:12px;
}
.status-preview.paid    { background:rgba(93,232,154,.15); color:#5de89a; }
.status-preview.partial { background:rgba(201,168,76,.2);  color:var(--gold); }
.status-preview.unpaid  { background:rgba(240,112,112,.15);color:#f07070; }

/* ── MEMBER SELECT ────────────────────────────────── */
.member-card-preview {
    background:#f4f6fb; border:1px solid #e4e9f0;
    border-radius:10px; padding:12px 14px;
    display:none; margin-top:8px;
    font-size:13px; color:#2c3e55;
}
.member-card-preview.visible { display:flex; align-items:center; gap:10px; }
.mp-avatar {
    width:36px; height:36px; border-radius:9px;
    background:linear-gradient(135deg,#1e3a5f,#0f1f3d);
    color:var(--gold); display:flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:700; flex-shrink:0;
}
.mp-name  { font-weight:600; color:var(--navy); }
.mp-nic   { font-size:11.5px; color:#8494a9; font-family:monospace; }

/* ── BUTTONS ──────────────────────────────────────── */
.btn-submit {
    background:var(--gold); color:var(--navy);
    border:none; border-radius:10px;
    padding:12px 28px; font-size:14px; font-weight:700;
    cursor:pointer; display:inline-flex; align-items:center; gap:7px;
    width:100%; justify-content:center; margin-top:4px;
    transition:background .2s;
}
.btn-submit:hover { background:#f0d080; }
.btn-cancel {
    background:#f4f6fb; color:#3d5270;
    border:1px solid #dde3ef; border-radius:10px;
    padding:11px; font-size:13.5px; font-weight:600;
    text-decoration:none; display:block; text-align:center; margin-top:8px;
}
.btn-cancel:hover { background:#e4e9f0; color:var(--navy); }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header mb-3">
    <h1>Record Registration Payment</h1>
    <p>Registration fee: <strong>Rs {{ number_format($fee, 2) }}</strong> (from environment config)</p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
            <li style="font-size:13px;">{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('registration-payments.store') }}" id="payForm">
@csrf

<div class="pay-layout">

    {{-- ── LEFT COLUMN: FORM ──────────────────────────── --}}
    <div>

        {{-- Member Selection --}}
        <div class="form-card mb-4">
            <div class="form-card-header">
                <i class="bi bi-person-fill"></i>
                <h4>Select Member</h4>
            </div>
            <div class="form-card-body">
                <label class="form-label">Member <span class="req">*</span></label>
                <select name="member_id" id="memberSelect"
                        class="form-select @error('member_id') is-invalid @enderror" required>
                    <option value="">— Choose a member —</option>
                    @foreach($members as $member)
                    <option value="{{ $member->id }}"
                            data-name="{{ $member->name_with_initials }}"
                            data-nic="{{ $member->nic_number }}"
                            data-has-payment="{{ $member->registrationPayment ? 'yes' : 'no' }}"
                            data-paid="{{ $member->registrationPayment->paid_amount ?? 0 }}"
                            data-status="{{ $member->registrationPayment->status ?? 'none' }}"
                            {{ old('member_id', $selectedMember?->id) == $member->id ? 'selected' : '' }}>
                        {{ $member->name_with_initials }} — {{ $member->nic_number }}
                    </option>
                    @endforeach
                </select>
                @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

                {{-- Member preview card --}}
                <div class="member-card-preview" id="memberPreview">
                    <div class="mp-avatar" id="mpAvatar">?</div>
                    <div>
                        <div class="mp-name" id="mpName">—</div>
                        <div class="mp-nic"  id="mpNic">—</div>
                    </div>
                    <div id="mpExistingBadge" style="margin-left:auto;display:none;">
                        <span style="font-size:11px;background:#fff3d6;color:#b07d10;padding:3px 10px;border-radius:20px;font-weight:600;">
                            <i class="bi bi-exclamation me-1"></i>Has existing payment — amount will be topped up
                        </span>
                    </div>
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

                    {{-- Total Fee (display only) --}}
                    <div class="col-md-6">
                        <label class="form-label">Total Registration Fee</label>
                        <div class="form-control" style="background:#f4f6fb;font-weight:700;color:var(--navy);">
                            Rs {{ number_format($fee, 2) }}
                        </div>
                        <input type="hidden" id="totalFeeVal" value="{{ $fee }}">
                    </div>

                    {{-- Paid Amount --}}
                    <div class="col-md-6">
                        <label class="form-label">Amount Paying Now <span class="req">*</span></label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-weight:700;color:#8494a9;">Rs</span>
                            <input type="number" name="paid_amount" id="paidAmountInput"
                                   class="form-control @error('paid_amount') is-invalid @enderror"
                                   style="padding-left:36px;"
                                   step="0.01" min="1" max="{{ $fee }}"
                                   value="{{ old('paid_amount') }}"
                                   placeholder="0.00" required>
                        </div>
                        @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Payment Date --}}
                    <div class="col-md-6">
                        <label class="form-label">Payment Date <span class="req">*</span></label>
                        <input type="date" name="payment_date"
                               class="form-control @error('payment_date') is-invalid @enderror"
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" required>
                        @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Receipt Number --}}
                    <div class="col-md-6">
                        <label class="form-label">Receipt Number</label>
                        <input type="text" name="receipt_number"
                               class="form-control @error('receipt_number') is-invalid @enderror"
                               value="{{ old('receipt_number') }}"
                               placeholder="optional">
                        @error('receipt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Notes --}}
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2"
                                  class="form-control @error('notes') is-invalid @enderror"
                                  placeholder="Any remarks or notes…">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- ── RIGHT COLUMN: LIVE CALCULATOR ─────────────── --}}
    <div>
        <div class="calc-panel">
            <div class="calc-title">
                <i class="bi bi-calculator-fill"></i> Payment Calculator
            </div>

            <div class="calc-row">
                <span class="calc-label">Registration Fee</span>
                <span class="calc-val">Rs {{ number_format($fee, 2) }}</span>
            </div>

            <div class="calc-row">
                <span class="calc-label">Already Paid</span>
                <span class="calc-val green" id="calcAlreadyPaid">Rs 0.00</span>
            </div>

            <div class="calc-row">
                <span class="calc-label">Paying Now</span>
                <span class="calc-val amber" id="calcPayingNow">Rs 0.00</span>
            </div>

            <div style="height:1px;background:rgba(255,255,255,.12);margin:4px 0;"></div>

            <div class="calc-row">
                <span class="calc-label">Total Paid After</span>
                <span class="calc-val green" id="calcTotalPaid">Rs 0.00</span>
            </div>

            <div class="calc-row">
                <span class="calc-label">Remaining Balance</span>
                <span class="calc-val red" id="calcBalance">Rs {{ number_format($fee, 2) }}</span>
            </div>

            {{-- Progress --}}
            <div class="calc-progress-wrap">
                <div class="calc-progress-bar" id="calcProgressBar" style="width:0%"></div>
            </div>
            <div class="calc-pct" id="calcPct">0% paid</div>

            {{-- Status badge --}}
            <div class="status-preview unpaid" id="calcStatusBadge">
                <i class="bi bi-x-circle me-1"></i> UNPAID
            </div>

            {{-- Submit inside panel on mobile, hidden on desktop --}}
            <div class="mt-4">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-circle-fill"></i>
                    Record Payment
                </button>
                <a href="{{ route('registration-payments.index') }}" class="btn-cancel">
                    Cancel
                </a>
            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('scripts')
<script>
const TOTAL_FEE   = parseFloat(document.getElementById('totalFeeVal').value);
const memberSelect = document.getElementById('memberSelect');
const paidInput    = document.getElementById('paidAmountInput');

let alreadyPaid = 0;

function fmt(n) {
    return 'Rs ' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function updatePreview() {
    const opt = memberSelect.options[memberSelect.selectedIndex];
    const preview = document.getElementById('memberPreview');

    if (!opt || !opt.value) {
        preview.classList.remove('visible');
        alreadyPaid = 0;
        updateCalc();
        return;
    }

    const name   = opt.dataset.name  || '';
    const nic    = opt.dataset.nic   || '';
    const hasPay = opt.dataset.hasPayment === 'yes';
    alreadyPaid  = parseFloat(opt.dataset.paid || 0);

    document.getElementById('mpAvatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('mpName').textContent   = name;
    document.getElementById('mpNic').textContent    = nic;
    document.getElementById('mpExistingBadge').style.display = hasPay ? 'block' : 'none';

    // Adjust max for new payment
    const remainAllowed = Math.max(0, TOTAL_FEE - alreadyPaid);
    paidInput.max = remainAllowed;

    preview.classList.add('visible');
    updateCalc();
}

function updateCalc() {
    const payingNow    = Math.max(0, parseFloat(paidInput.value) || 0);
    const totalPaid    = Math.min(TOTAL_FEE, alreadyPaid + payingNow);
    const balance      = Math.max(0, TOTAL_FEE - totalPaid);
    const pct          = TOTAL_FEE > 0 ? Math.min(100, (totalPaid / TOTAL_FEE) * 100) : 0;

    document.getElementById('calcAlreadyPaid').textContent = fmt(alreadyPaid);
    document.getElementById('calcPayingNow').textContent   = fmt(payingNow);
    document.getElementById('calcTotalPaid').textContent   = fmt(totalPaid);
    document.getElementById('calcBalance').textContent     = fmt(balance);
    document.getElementById('calcProgressBar').style.width = pct.toFixed(1) + '%';
    document.getElementById('calcPct').textContent = pct.toFixed(0) + '% paid';

    // Status
    const badge = document.getElementById('calcStatusBadge');
    badge.className = 'status-preview';
    if (totalPaid >= TOTAL_FEE) {
        badge.classList.add('paid');
        badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> FULLY PAID';
    } else if (totalPaid > 0) {
        badge.classList.add('partial');
        badge.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> PARTIAL PAYMENT';
    } else {
        badge.classList.add('unpaid');
        badge.innerHTML = '<i class="bi bi-x-circle me-1"></i> UNPAID';
    }
}

memberSelect.addEventListener('change', updatePreview);
paidInput.addEventListener('input', updateCalc);

// Init on load (handles old() repopulation)
updatePreview();
updateCalc();
</script>
@endpush
