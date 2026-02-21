@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Registration Payment')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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

/* ── SELECT2 OVERRIDES ────────────────────────────── */
.select2-container { width:100% !important; }
.select2-container .select2-selection--single {
    border-radius:10px !important;
    border:1.5px solid #dde3ef !important;
    height:46px !important;
    display:flex !important;
    align-items:center !important;
    transition:border-color .2s, box-shadow .2s;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open  .select2-selection--single {
    border-color:var(--gold) !important;
    box-shadow:0 0 0 3px rgba(201,168,76,.15) !important;
    outline:none !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height:46px !important;
    padding-left:14px !important;
    color:#1a2b44; font-size:14px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height:46px !important; right:10px !important;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder { color:#9bafc4; }
.select2-container--default.is-invalid-s2 .select2-selection--single { border-color:#dc3545 !important; }

.select2-dropdown {
    border-radius:12px !important;
    border:1px solid #e4e9f0 !important;
    box-shadow:0 12px 36px rgba(15,31,61,.13) !important;
    overflow:hidden;
}
.select2-search--dropdown { padding:8px 10px 4px !important; }
.select2-search--dropdown .select2-search__field {
    border-radius:8px !important;
    border:1.5px solid #dde3ef !important;
    padding:8px 12px !important;
    font-size:13.5px;
}
.select2-search--dropdown .select2-search__field:focus {
    border-color:var(--gold) !important; outline:none !important;
}
.select2-results__option { padding:0 !important; }
.select2-results__option--highlighted .s2-row { background:var(--navy) !important; }
.select2-results__option--highlighted .s2-name { color:#fff !important; }
.select2-results__option--highlighted .s2-sub  { color:#7a9bc0 !important; }
.select2-results__option--highlighted .s2-avatar { background:rgba(255,255,255,.12) !important; color:var(--gold) !important; }

/* Member option row template */
.s2-row {
    display:flex; align-items:center; gap:11px;
    padding:10px 14px; transition:background .12s;
}
.s2-avatar {
    width:34px; height:34px; border-radius:8px; flex-shrink:0;
    background:linear-gradient(135deg,#1e3a5f,#0f1f3d);
    color:var(--gold); display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:700;
}
.s2-name { font-weight:600; font-size:13.5px; color:var(--navy); }
.s2-sub  { font-size:11.5px; color:#8494a9; margin-top:1px; }
.s2-badge {
    margin-left:auto; flex-shrink:0;
    font-size:10.5px; font-weight:700;
    padding:2px 9px; border-radius:20px;
}
.s2-badge.partial { background:#fff3d6; color:#b07d10; }
.s2-badge.unpaid  { background:#e8f7ee; color:#1a8a45; }
.s2-badge.paid    { background:#e8ecf5; color:#3d5270; }

/* Search hint inside dropdown */
.s2-hint {
    padding:8px 14px 10px;
    font-size:12px; color:#8494a9;
    text-align:center;
    border-top:1px solid #f0f3f8;
}

/* ── MEMBER PREVIEW CARD ──────────────────────────── */
.member-card-preview {
    background:#f4f6fb; border:1px solid #e4e9f0;
    border-radius:10px; padding:12px 14px;
    display:none; margin-top:10px;
    font-size:13px; color:#2c3e55;
    animation:fadeUp .2s ease;
}
.member-card-preview.visible { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.mp-avatar {
    width:36px; height:36px; border-radius:9px;
    background:linear-gradient(135deg,#1e3a5f,#0f1f3d);
    color:var(--gold); display:flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:700; flex-shrink:0;
}
.mp-name { font-weight:600; color:var(--navy); }
.mp-nic  { font-size:11.5px; color:#8494a9; font-family:monospace; }

/* ── LIVE CALCULATOR PANEL ────────────────────────── */
.calc-panel {
    background:var(--navy); border-radius:16px;
    padding:24px; color:#fff;
    position:sticky; top:84px;
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
    padding:11px 0; border-bottom:1px solid rgba(255,255,255,.07);
    font-size:13.5px;
}
.calc-row:last-of-type { border-bottom:none; }
.calc-label { color:#7a9abc; }
.calc-val   { font-weight:700; color:#fff; font-size:15px; }
.calc-val.green { color:#5de89a; }
.calc-val.amber { color:var(--gold); }
.calc-val.red   { color:#f07070; }
.calc-progress-wrap {
    background:rgba(255,255,255,.1);
    border-radius:99px; height:10px; overflow:hidden;
    margin:18px 0 6px;
}
.calc-progress-bar {
    height:100%; border-radius:99px;
    background:linear-gradient(90deg,var(--gold),#f0d080);
    transition:width .3s ease;
}
.calc-pct { font-size:12px; color:#7a9abc; text-align:right; margin-bottom:18px; }
.status-preview {
    text-align:center; padding:10px 16px;
    border-radius:10px; font-size:13px; font-weight:700;
    letter-spacing:.5px; margin-top:12px;
}
.status-preview.paid    { background:rgba(93,232,154,.15); color:#5de89a; }
.status-preview.partial { background:rgba(201,168,76,.2);  color:var(--gold); }
.status-preview.unpaid  { background:rgba(240,112,112,.15);color:#f07070; }

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
    <p>Registration fee: <strong>Rs {{ number_format($fee, 2) }}</strong></p>
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

    {{-- ── LEFT COLUMN ──────────────────────────────── --}}
    <div>

        {{-- Member Selection --}}
        <div class="form-card mb-4">
            <div class="form-card-header">
                <i class="bi bi-person-fill"></i>
                <h4>Select Member</h4>
            </div>
            <div class="form-card-body">

                <label class="form-label">
                    Member <span class="req">*</span>
                </label>

                {{-- Hidden select that Select2 enhances --}}
                <select name="member_id"
                        id="memberSelect"
                        class="@error('member_id') is-invalid-s2 @enderror"
                        style="width:100%"
                        required>
                    {{-- If old() or $selectedMember exists, pre-populate one option --}}
                    @if(old('member_id') && $selectedMember)
                    <option value="{{ $selectedMember->id }}"
                            data-name="{{ $selectedMember->name_with_initials }}"
                            data-nic="{{ $selectedMember->nic_number }}"
                            data-paid="{{ $selectedMember->registrationPayment->paid_amount ?? 0 }}"
                            data-status="{{ $selectedMember->registrationPayment->status ?? 'unpaid' }}"
                            selected>
                        {{ $selectedMember->name_with_initials }} — {{ $selectedMember->nic_number }}
                    </option>
                    @elseif($selectedMember)
                    <option value="{{ $selectedMember->id }}"
                            data-name="{{ $selectedMember->name_with_initials }}"
                            data-nic="{{ $selectedMember->nic_number }}"
                            data-paid="{{ $selectedMember->registrationPayment->paid_amount ?? 0 }}"
                            data-status="{{ $selectedMember->registrationPayment->status ?? 'unpaid' }}"
                            selected>
                        {{ $selectedMember->name_with_initials }} — {{ $selectedMember->nic_number }}
                    </option>
                    @endif
                </select>

                @error('member_id')
                <div style="color:#dc3545;font-size:11.5px;margin-top:5px;">{{ $message }}</div>
                @enderror

                <div style="font-size:11.5px;color:#8494a9;margin-top:8px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Type at least 3 characters to search by name or NIC number.
                </div>

                {{-- Member preview card (shown after selection) --}}
                <div class="member-card-preview" id="memberPreview">
                    <div class="mp-avatar" id="mpAvatar">?</div>
                    <div>
                        <div class="mp-name" id="mpName">—</div>
                        <div class="mp-nic"  id="mpNic">—</div>
                    </div>
                    <div id="mpExistingBadge" style="margin-left:auto;display:none;">
                        <span style="font-size:11px;background:#fff3d6;color:#b07d10;padding:3px 10px;border-radius:20px;font-weight:600;">
                            <i class="bi bi-exclamation me-1"></i>Has existing payment — will top up
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

                    <div class="col-md-6">
                        <label class="form-label">Total Registration Fee</label>
                        <div class="form-control" style="background:#f4f6fb;font-weight:700;color:var(--navy);">
                            Rs {{ number_format($fee, 2) }}
                        </div>
                        <input type="hidden" id="totalFeeVal" value="{{ $fee }}">
                    </div>

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

                    <div class="col-md-6">
                        <label class="form-label">Payment Date <span class="req">*</span></label>
                        <input type="date" name="payment_date"
                               class="form-control @error('payment_date') is-invalid @enderror"
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" required>
                        @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Receipt Number</label>
                        <input type="text" name="receipt_number"
                               class="form-control @error('receipt_number') is-invalid @enderror"
                               value="{{ old('receipt_number') }}"
                               placeholder="optional">
                        @error('receipt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

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

    {{-- ── RIGHT COLUMN: LIVE CALCULATOR ──────────────── --}}
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

            <div class="calc-progress-wrap">
                <div class="calc-progress-bar" id="calcProgressBar" style="width:0%"></div>
            </div>
            <div class="calc-pct" id="calcPct">0% paid</div>

            <div class="status-preview unpaid" id="calcStatusBadge">
                <i class="bi bi-x-circle me-1"></i> UNPAID
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-circle-fill"></i> Record Payment
                </button>
                <a href="{{ route('registration-payments.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const TOTAL_FEE  = parseFloat(document.getElementById('totalFeeVal').value);
const paidInput  = document.getElementById('paidAmountInput');
let   alreadyPaid = 0;

// ── FORMAT ──────────────────────────────────────────────
function fmt(n) {
    return 'Rs ' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// ── SELECT2 INIT ────────────────────────────────────────
$('#memberSelect').select2({
    placeholder:          'Type at least 3 characters to search…',
    minimumInputLength:   3,
    allowClear:           true,
    dropdownParent:       $('body'),
    language: {
        inputTooShort: function(args) {
            const remaining = args.minimum - args.input.length;
            return `<div class="s2-hint"><i class="bi bi-keyboard me-1"></i>Type ${remaining} more character${remaining > 1 ? 's' : ''} to search…</div>`;
        },
        searching:     () => '<div class="s2-hint"><i class="bi bi-hourglass-split me-1"></i> Searching…</div>',
        noResults:     () => '<div class="s2-hint"><i class="bi bi-person-x me-1"></i> No members found</div>',
    },
    ajax: {
        url:      '{{ route("registration-payments.search-member") }}',
        dataType: 'json',
        delay:    300,
        data:     params => ({ q: params.term }),
        processResults: data => ({ results: data.results }),
        cache: true,
    },
    templateResult:    renderOption,
    templateSelection: renderSelection,
    escapeMarkup:      markup => markup,   // allow HTML in templates
});

// ── OPTION TEMPLATE (dropdown rows) ─────────────────────
function renderOption(m) {
    if (m.loading || !m.initials) {
        return `<div class="s2-hint">${m.text}</div>`;
    }

    const badgeClass = m.reg_status === 'paid'
        ? 'paid'
        : (m.reg_status === 'partial' ? 'partial' : 'unpaid');

    const badgeText  = m.reg_status === 'paid'
        ? 'Fully Paid'
        : (m.reg_status === 'partial' ? 'Partial' : 'Unpaid');

    return `
        <div class="s2-row">
            <div class="s2-avatar">${m.initials}</div>
            <div>
                <div class="s2-name">${m.name}</div>
                <div class="s2-sub">${m.nic} &middot; ${m.phone}</div>
            </div>
            <span class="s2-badge ${badgeClass}">${badgeText}</span>
        </div>`;
}

// ── SELECTION TEMPLATE (selected value shown in box) ────
function renderSelection(m) {
    if (!m.id) return m.text;       // placeholder
    return m.name || m.text;        // just the name
}

// ── ON MEMBER SELECTED ───────────────────────────────────
$('#memberSelect').on('select2:select', function(e) {
    const m = e.params.data;
    alreadyPaid = parseFloat(m.reg_paid || 0);

    // Show preview card
    document.getElementById('mpAvatar').textContent = m.initials;
    document.getElementById('mpName').textContent   = m.name;
    document.getElementById('mpNic').textContent    = m.nic;
    document.getElementById('mpExistingBadge').style.display =
        (m.reg_status && m.reg_status !== 'unpaid') ? 'block' : 'none';
    document.getElementById('memberPreview').classList.add('visible');

    // Adjust max allowed payment
    paidInput.max = Math.max(0, TOTAL_FEE - alreadyPaid);

    updateCalc();
});

// ── ON MEMBER CLEARED ────────────────────────────────────
$('#memberSelect').on('select2:unselect select2:clear', function() {
    alreadyPaid = 0;
    paidInput.max = TOTAL_FEE;
    document.getElementById('memberPreview').classList.remove('visible');
    updateCalc();
});

// ── LIVE CALCULATOR ──────────────────────────────────────
function updateCalc() {
    const payingNow = Math.max(0, parseFloat(paidInput.value) || 0);
    const totalPaid = Math.min(TOTAL_FEE, alreadyPaid + payingNow);
    const balance   = Math.max(0, TOTAL_FEE - totalPaid);
    const pct       = TOTAL_FEE > 0 ? Math.min(100, (totalPaid / TOTAL_FEE) * 100) : 0;

    document.getElementById('calcAlreadyPaid').textContent   = fmt(alreadyPaid);
    document.getElementById('calcPayingNow').textContent     = fmt(payingNow);
    document.getElementById('calcTotalPaid').textContent     = fmt(totalPaid);
    document.getElementById('calcBalance').textContent       = fmt(balance);
    document.getElementById('calcProgressBar').style.width  = pct.toFixed(1) + '%';
    document.getElementById('calcPct').textContent          = pct.toFixed(0) + '% paid';

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

paidInput.addEventListener('input', updateCalc);

// ── HANDLE OLD() RE-POPULATION AFTER VALIDATION FAIL ────
// If the form failed validation, the selected member is
// pre-populated as a single <option> — trigger preview.
@if(old('member_id') && $selectedMember)
(function() {
    alreadyPaid = {{ $selectedMember->registrationPayment->paid_amount ?? 0 }};
    document.getElementById('mpAvatar').textContent = '{{ $selectedMember->initials }}';
    document.getElementById('mpName').textContent   = '{{ $selectedMember->name_with_initials }}';
    document.getElementById('mpNic').textContent    = '{{ $selectedMember->nic_number }}';
    document.getElementById('memberPreview').classList.add('visible');
    paidInput.max = Math.max(0, TOTAL_FEE - alreadyPaid);
    updateCalc();
})();
@elseif($selectedMember)
(function() {
    alreadyPaid = {{ $selectedMember->registrationPayment->paid_amount ?? 0 }};
    document.getElementById('mpAvatar').textContent = '{{ $selectedMember->initials }}';
    document.getElementById('mpName').textContent   = '{{ $selectedMember->name_with_initials }}';
    document.getElementById('mpNic').textContent    = '{{ $selectedMember->nic_number }}';
    document.getElementById('memberPreview').classList.add('visible');
    paidInput.max = Math.max(0, TOTAL_FEE - alreadyPaid);
    updateCalc();
})();
@else
updateCalc();
@endif
</script>
@endpush
