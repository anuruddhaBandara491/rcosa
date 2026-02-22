@extends('layouts.app')

@section('title', 'Record Monthly Payment')
@section('page-title', 'Record Monthly Payment')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; --gold-lt:#f0d080; }

/* ═══ LAYOUT ══════════════════════════════════════════════ */
.pay-wrap { display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:start; }
@media(max-width:991px){ .pay-wrap{grid-template-columns:1fr;} }

/* ═══ CARDS ═══════════════════════════════════════════════ */
.obs-card { background:#fff;border:1px solid #e4e9f0;border-radius:16px;overflow:visible;animation:fadeUp .3s ease both;margin-bottom:18px; }
.obs-card-header { padding:15px 22px;border-bottom:1px solid #f0f3f8;background:#fafbfd;display:flex;align-items:center;gap:10px;border-radius:16px 16px 0 0; }
.obs-card-header i { font-size:17px;color:var(--gold); }
.obs-card-header h4 { font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--navy);margin:0; }
.obs-card-body { padding:22px; }

.form-label { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#5a7194;margin-bottom:6px; }
.form-control,.form-select { border-radius:10px;border:1.5px solid #dde3ef;font-size:14px;padding:9px 14px;color:#1a2b44;transition:border-color .2s,box-shadow .2s; }
.form-control:focus,.form-select:focus { border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.15);outline:none; }

/* ═══ SEARCH ══════════════════════════════════════════════ */
.search-container { position:relative; }
.search-input-wrap { position:relative; }
.search-input-wrap .si { position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:16px;color:#8494a9;pointer-events:none; }
.search-input-wrap .spin { position:absolute;right:14px;top:50%;transform:translateY(-50%);display:none; }
#memberSearch { padding:12px 40px 12px 42px;border-radius:12px;border:1.5px solid #dde3ef;font-size:14px;width:100%;color:#1a2b44;font-family:inherit;transition:border-color .2s,box-shadow .2s; }
#memberSearch:focus { border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.15);outline:none; }

#searchDropdown { position:absolute;top:calc(100% + 6px);left:0;right:0;background:#fff;border:1px solid #e4e9f0;border-radius:12px;box-shadow:0 10px 32px rgba(15,31,61,.12);z-index:9999;max-height:320px;overflow-y:auto;display:none; }
.dd-item { display:flex;align-items:center;gap:12px;padding:12px 16px;cursor:pointer;border-bottom:1px solid #f6f8fb;transition:background .15s; }
.dd-item:last-child { border-bottom:none; }
.dd-item:hover { background:#f4f6fb; }
.dd-avatar { width:36px;height:36px;border-radius:9px;flex-shrink:0;background:linear-gradient(135deg,#1e3a5f,#0f1f3d);color:var(--gold);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700; }
.dd-name { font-weight:600;font-size:13.5px;color:var(--navy); }
.dd-sub  { font-size:11.5px;color:#8494a9; }
.dd-hint { padding:16px;text-align:center;color:#8494a9;font-size:13px; }

/* ═══ MEMBER PROFILE BAR ══════════════════════════════════ */
.member-profile { background:linear-gradient(135deg,var(--navy),#1e3a5f);border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:22px;color:#fff; }
.mp-big-avatar { width:52px;height:52px;border-radius:13px;flex-shrink:0;background:linear-gradient(135deg,var(--gold),var(--gold-lt));color:var(--navy);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:22px;font-weight:700; }
.mp-name   { font-family:'Playfair Display',serif;font-size:17px;font-weight:700;margin:0 0 3px; }
.mp-detail { font-size:12px;color:#7a9abc;margin:0; }
.mp-stats  { margin-left:auto;text-align:right; }
.mp-stat-val   { font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--gold);line-height:1; }
.mp-stat-label { font-size:11px;color:#7a9abc;margin-top:2px; }

/* ═══ BIG AMOUNT INPUT ════════════════════════════════════ */
.big-amount-box {
    border:2px solid #dde3ef;border-radius:14px;
    padding:20px 22px;margin-bottom:20px;
    background:#fafbff;
    transition:border-color .2s,box-shadow .2s;
}
.big-amount-box:focus-within { border-color:var(--gold);box-shadow:0 0 0 4px rgba(201,168,76,.1); }
.big-amount-label { font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#5a7194;margin-bottom:12px;display:flex;align-items:center;gap:7px; }
.big-amount-row   { display:flex;align-items:center;gap:8px; }
.big-prefix { font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:#8494a9;flex-shrink:0; }
#totalPayInput {
    font-family:'Playfair Display',serif;font-size:36px;font-weight:700;color:var(--navy);
    border:none;background:transparent;outline:none;width:100%;min-width:100px;
    -moz-appearance:textfield;
}
#totalPayInput::-webkit-inner-spin-button,
#totalPayInput::-webkit-outer-spin-button { -webkit-appearance:none;margin:0; }
#totalPayInput::placeholder { color:#cbd5e1; }

/* Quick fill chips */
.chip-row { display:flex;gap:7px;margin-top:13px;flex-wrap:wrap; }
.q-chip {
    font-size:12px;font-weight:600;padding:4px 13px;
    border-radius:20px;border:1.5px solid #dde3ef;
    background:#fff;color:#3d5270;cursor:pointer;
    transition:all .15s;white-space:nowrap;
}
.q-chip:hover { border-color:var(--gold);background:rgba(201,168,76,.1);color:var(--navy); }
.q-chip.active { background:var(--navy);border-color:var(--navy);color:#fff; }

/* Over-balance warning */
.over-warn { font-size:12px;color:#dc2626;margin-top:8px;display:none; }
.over-warn i { margin-right:4px; }

/* ═══ MONTHS TABLE ════════════════════════════════════════ */
.months-tbl { width:100%;border-collapse:collapse;font-size:13.5px; }
.months-tbl th { font-size:10.5px;text-transform:uppercase;letter-spacing:1.1px;color:#8494a9;font-weight:600;padding:10px 14px;border-bottom:1px solid #f0f3f8;background:#fafbfd;white-space:nowrap; }
.months-tbl td { padding:11px 14px;color:#1e293b;border-bottom:1px solid #f6f8fb;vertical-align:middle; }
.months-tbl tr:last-child td { border-bottom:none; }
.months-tbl tbody tr.is-paying  td { background:#f0fdf4; }
.months-tbl tbody tr.is-partial td { background:#fffbeb; }

.mo-chip { display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:4px 10px;border-radius:8px;background:#e8ecf5;color:#1e3a5f; }
.s-pill { font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap; }
.s-unpaid  { background:#fee2e2;color:#b91c1c; }
.s-partial { background:#fef3c7;color:#b45309; }
.s-paid    { background:#dcfce7;color:#15803d; }

.pay-now-val { font-family:'Playfair Display',serif;font-size:14px;font-weight:700; }
.pay-now-val.green { color:#15803d; }
.pay-now-val.amber { color:#b45309; }
.pay-now-val.muted { color:#cbd5e1; }

.mini-prog { width:72px;height:5px;background:#e4e9f0;border-radius:99px;overflow:hidden;margin-top:3px; }
.mini-prog-fill { height:100%;border-radius:99px;transition:width .25s ease; }

/* ═══ SUMMARY PANEL ═══════════════════════════════════════ */
.summary-panel { background:var(--navy);border-radius:16px;padding:24px;color:#fff;position:sticky;top:84px;animation:fadeUp .3s .1s ease both; }
.sp-title { font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:8px; }
.sp-title i { color:var(--gold); }
.sp-row { display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.07);font-size:13.5px; }
.sp-row:last-of-type { border-bottom:none; }
.sp-label { color:#7a9abc; }
.sp-val { font-weight:700;color:#fff;font-size:15px; }
.sp-val.gold  { color:var(--gold); }
.sp-val.green { color:#4ade80; }
.sp-val.red   { color:#f87171; }
.sp-prog-wrap { background:rgba(255,255,255,.1);border-radius:99px;height:8px;overflow:hidden;margin:14px 0 4px; }
.sp-prog-fill { height:100%;border-radius:99px;background:linear-gradient(90deg,var(--gold),var(--gold-lt));transition:width .3s; }
.sp-prog-pct  { font-size:11px;color:#7a9abc;text-align:right;margin-bottom:14px; }
.sp-divider   { height:1px;background:rgba(255,255,255,.1);margin:10px 0; }

.sp-month-list { max-height:220px;overflow-y:auto;padding-right:2px; }
.sp-month-list::-webkit-scrollbar { width:3px; }
.sp-month-list::-webkit-scrollbar-thumb { background:rgba(255,255,255,.15);border-radius:3px; }
.sp-mo-row { display:flex;justify-content:space-between;align-items:flex-start;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:12.5px; }
.sp-mo-row:last-child { border-bottom:none; }
.sp-mo-name { color:#aac0d8; }
.sp-mo-right { text-align:right; }
.sp-mo-amt.full    { color:#4ade80;font-weight:700; }
.sp-mo-amt.partial { color:var(--gold);font-weight:700; }

.sp-val.gold   { color:var(--gold); }
.sp-val.green  { color:#4ade80; }
.sp-val.red    { color:#f87171; }
.sp-val.purple { color:#a78bfa; }
.sp-mo-tag { font-size:10px;color:#64748b;margin-top:1px; }

.btn-submit { background:var(--gold);color:var(--navy);border:none;border-radius:10px;padding:13px 24px;font-size:14px;font-weight:700;cursor:pointer;width:100%;display:flex;align-items:center;justify-content:center;gap:7px;margin-top:4px;transition:opacity .2s; }
.btn-submit:hover { opacity:.88; }
.btn-submit:disabled { opacity:.4;cursor:not-allowed; }
.btn-cancel { background:rgba(255,255,255,.08);color:#aac0d8;border:1px solid rgba(255,255,255,.15);border-radius:10px;padding:10px;font-size:13px;font-weight:600;text-decoration:none;display:block;text-align:center;margin-top:8px; }
.btn-cancel:hover { background:rgba(255,255,255,.15);color:#fff; }

.panel-placeholder { text-align:center;padding:30px 10px;color:#7a9abc; }
.panel-placeholder i { font-size:32px;display:block;margin-bottom:10px;opacity:.4; }
.panel-placeholder p { font-size:13px;margin:0; }

#memberPanel { display:none;animation:fadeUp .3s ease; }

/* ═══ PAGINATION ══════════════════════════════════════════ */
.pg-btn {
    min-width:34px;height:34px;border-radius:8px;border:1.5px solid #e4e9f0;
    background:#fff;color:#3d5270;font-size:13px;font-weight:600;
    cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;
}
.pg-btn:hover:not(:disabled) { border-color:var(--gold);background:rgba(201,168,76,.08);color:var(--navy); }
.pg-btn.active { background:var(--navy);border-color:var(--navy);color:#fff; }
.pg-btn:disabled { opacity:.35;cursor:not-allowed; }
.pg-info { font-size:12.5px;color:#64748b;font-weight:500; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header mb-3">
    <h1>Record Monthly Payment</h1>
    <p>Search for a member, enter the amount they are paying — months fill automatically from oldest first.</p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Please fix these errors:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('monthly-payments.store') }}" id="payForm">
@csrf
<input type="hidden" name="member_id" id="memberIdInput">
<input type="hidden" id="monthlyFeeVal" value="{{ $fee }}">

<div class="pay-wrap">

{{-- ═══ LEFT COLUMN ══════════════════════════════════════ --}}
<div>

    {{-- STEP 1: Find Member --}}
    <div class="obs-card" style="animation-delay:.05s">
        <div class="obs-card-header">
            <i class="bi bi-search"></i>
            <h4>Step 1 — Find Member</h4>
        </div>
        <div class="obs-card-body">
            <label class="form-label">Search by Name, NIC or Phone <span style="color:#c0392b;">*</span></label>
            <div class="search-container">
                <div class="search-input-wrap">
                    <i class="bi bi-person-search si"></i>
                    <input type="text" id="memberSearch"
                           placeholder="Type at least 3 characters to search…"
                           autocomplete="off">
                    <div class="spin">
                        <div class="spinner-border spinner-border-sm text-secondary" role="status" style="width:16px;height:16px;"></div>
                    </div>
                </div>
                <div id="searchDropdown"></div>
            </div>

            <div id="selectedMemberBadge" style="display:none;margin-top:10px;">
                <div style="background:#e8f0fd;border:1px solid #c0d0ee;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:10px;font-size:13.5px;">
                    <i class="bi bi-person-check-fill" style="color:#1a5dc0;font-size:16px;"></i>
                    <span id="selectedMemberName" style="font-weight:600;color:#0f1f3d;"></span>
                    <button type="button" id="clearMember" style="margin-left:auto;background:none;border:none;color:#8494a9;cursor:pointer;font-size:13px;">
                        <i class="bi bi-x-lg"></i> Change
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 2 + 3 (shown after member select) --}}
    <div id="memberPanel">

        {{-- STEP 2: Payment --}}
        <div class="obs-card" style="animation-delay:.1s">
            <div class="obs-card-header">
                <i class="bi bi-cash-coin"></i>
                <h4>Step 2 — Payment Amount &amp; Details</h4>
            </div>
            <div class="obs-card-body">

                {{-- Member profile --}}
                <div class="member-profile">
                    <div class="mp-big-avatar" id="mpAvatar">?</div>
                    <div>
                        <p class="mp-name"   id="mpName">—</p>
                        <p class="mp-detail" id="mpDetail">—</p>
                    </div>
                    <div class="mp-stats">
                        <div class="mp-stat-val"   id="mpBalance">Rs 0</div>
                        <div class="mp-stat-label">Total Outstanding</div>
                    </div>
                </div>

                {{-- Big amount input --}}
                <div class="big-amount-box">
                    <div class="big-amount-label">
                        <i class="bi bi-cash-stack"></i>
                        Amount Being Paid Now
                    </div>
                    <div class="big-amount-row">
                        <span class="big-prefix">Rs</span>
                        <input type="number" id="totalPayInput" placeholder="0.00" step="0.01" min="0" oninput="onAmountChange()">
                    </div>
                    <div class="chip-row" id="chipRow"></div>
                    <div class="over-warn" id="overWarn" style="color:#6d28d9;background:#ede9fe;border-radius:8px;padding:8px 12px;">
                        <i class="bi bi-arrow-up-circle-fill"></i>
                        Amount exceeds outstanding balance of <span id="overWarnMax"></span>. The excess will be recorded as an <strong>overpayment / credit</strong>.
                    </div>
                </div>

                {{-- Payment Details (merged from Step 3) --}}
                <div style="background:#fafbfd;border:1px solid #f0f3f8;border-radius:12px;padding:18px 20px;margin-bottom:20px;">
                    <div style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.9px;color:#5a7194;margin-bottom:14px;display:flex;align-items:center;gap:7px;">
                        <i class="bi bi-receipt" style="color:var(--gold);font-size:14px;"></i>
                        Payment Details
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Payment Date <span style="color:#c0392b;">*</span></label>
                            <input type="date" name="payment_date"
                                   class="form-control @error('payment_date') is-invalid @enderror"
                                   value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                   max="{{ now()->format('Y-m-d') }}" required>
                            @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Receipt Number</label>
                            <input type="text" name="receipt_number" class="form-control"
                                   value="{{ old('receipt_number') }}" placeholder="optional">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" class="form-control"
                                   value="{{ old('notes') }}" placeholder="optional">
                        </div>
                    </div>
                </div>

                {{-- All paid message --}}
                <div id="noMonths" style="display:none;text-align:center;padding:30px;color:#8494a9;">
                    <i class="bi bi-check-circle-fill" style="font-size:32px;color:#16a34a;display:block;margin-bottom:8px;"></i>
                    <strong style="font-size:13.5px;color:#15803d;">All monthly fees are fully paid for this member!</strong>
                </div>

                {{-- Months breakdown --}}
                <div id="monthsWrap" style="display:none;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:6px;">
                        <div style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#64748b;">
                            <i class="bi bi-arrow-down-short" style="color:var(--gold);"></i>
                            Applied oldest-first
                        </div>
                        <div style="display:flex;gap:6px;">
                            <span id="chipMonths"  style="font-size:11.5px;background:#fee2e2;color:#b91c1c;padding:3px 11px;border-radius:20px;font-weight:600;"></span>
                            <span id="chipPartial" style="font-size:11.5px;background:#fef3c7;color:#b45309;padding:3px 11px;border-radius:20px;font-weight:600;"></span>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="months-tbl">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Fee</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Paying Now</th>
                                    <th>Remaining</th>
                                    <th>After</th>
                                </tr>
                            </thead>
                            <tbody id="monthsTbody"></tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div id="paginationBar" style="display:flex;align-items:center;justify-content:space-between;padding:12px 0 4px;flex-wrap:wrap;gap:8px;margin-top:4px;"></div>

                    {{-- Single hidden paid_amount submitted to controller --}}
                    <input type="hidden" name="paid_amount" id="paidAmountHidden" value="0">
                </div>

            </div>
        </div>

    </div>{{-- /memberPanel --}}

</div>

{{-- ═══ RIGHT — SUMMARY ══════════════════════════════════ --}}
<div>
    <div class="summary-panel">
        <div class="sp-title"><i class="bi bi-calculator-fill"></i> Payment Summary</div>

        <div id="panelPlaceholder" class="panel-placeholder">
            <i class="bi bi-person-search"></i>
            <p>Search and select a member to see their outstanding balance.</p>
        </div>

        <div id="panelSummary" style="display:none;">
            <div class="sp-row">
                <span class="sp-label">Total Outstanding</span>
                <span class="sp-val red" id="spOutstanding">Rs 0.00</span>
            </div>
            <div class="sp-row">
                <span class="sp-label">Paying Now</span>
                <span class="sp-val gold" id="spPayingNow">Rs 0.00</span>
            </div>
            <div class="sp-row">
                <span class="sp-label" id="spRemainingLabel">Still Remaining</span>
                <span class="sp-val" id="spRemaining">Rs 0.00</span>
            </div>

            <div class="sp-prog-wrap">
                <div class="sp-prog-fill" id="spProgFill" style="width:0%"></div>
            </div>
            <div class="sp-prog-pct" id="spProgPct">0% of balance covered</div>

            <div class="sp-divider"></div>

            <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:1.2px;color:#7a9abc;font-weight:600;margin-bottom:10px;">
                Month Breakdown:
            </div>
            <div id="spMonthList" class="sp-month-list">
                <div style="font-size:12px;color:#7a9abc;padding:6px 0;">
                    Enter an amount to see breakdown.
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn-submit" id="submitBtn" disabled>
                <i class="bi bi-check-circle-fill"></i> Record Payment
            </button>
            <a href="{{ route('monthly-payments.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </div>
</div>

</div>
</form>
@endsection

@push('scripts')
<script>
const memberIdInput = document.getElementById('memberIdInput');
const memberPanel   = document.getElementById('memberPanel');
const totalPayInput = document.getElementById('totalPayInput');
const searchInput   = document.getElementById('memberSearch');
const dropdown      = document.getElementById('searchDropdown');

let searchTimer  = null;
let allMonths    = [];   // outstanding months from AJAX
let totalBalance = 0;

function fmt(n) {
    return 'Rs ' + parseFloat(n || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// ══════════════════════════════════════════════════════════
// STEP 1 — SEARCH
// ══════════════════════════════════════════════════════════
searchInput.addEventListener('input', function () {
    clearTimeout(searchTimer);
    const q = this.value.trim();

    if (q.length < 3) {
        if (q.length > 0) {
            const rem = 3 - q.length;
            dropdown.innerHTML     = `<div class="dd-hint"><i class="bi bi-keyboard me-1"></i>Type ${rem} more character${rem > 1 ? 's' : ''}…</div>`;
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
        return;
    }

    dropdown.innerHTML     = '<div class="dd-hint"><i class="bi bi-hourglass-split me-1"></i>Searching…</div>';
    dropdown.style.display = 'block';
    document.querySelector('.spin').style.display = 'block';

    searchTimer = setTimeout(() => {
        fetch(`{{ route('monthly-payments.search-member') }}?q=${encodeURIComponent(q)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(members => {
            document.querySelector('.spin').style.display = 'none';
            if (!members.length) {
                dropdown.innerHTML = '<div class="dd-hint"><i class="bi bi-person-x me-1"></i>No members found</div>';
                return;
            }
            dropdown.innerHTML = members.map(m => `
                <div class="dd-item" onclick="selectMember(${m.id},'${m.name.replace(/'/g,"\\'")}','${m.nic}','${m.initials}')">
                    <div class="dd-avatar">${m.initials}</div>
                    <div>
                        <div class="dd-name">${m.name}</div>
                        <div class="dd-sub">${m.nic} &middot; ${m.phone} &middot; ${m.occupation}</div>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {
            document.querySelector('.spin').style.display = 'none';
            dropdown.innerHTML = '<div class="dd-hint" style="color:#dc2626;"><i class="bi bi-exclamation-circle me-1"></i>Search failed.</div>';
        });
    }, 300);
});

document.addEventListener('click', e => {
    if (!e.target.closest('.search-container')) dropdown.style.display = 'none';
});

function selectMember(id, name, nic, initials) {
    dropdown.style.display = 'none';
    searchInput.value = '';
    memberIdInput.value = id;

    document.getElementById('selectedMemberBadge').style.display = 'block';
    document.getElementById('selectedMemberName').textContent    = name + ' (' + nic + ')';
    searchInput.style.display = 'none';
    document.querySelector('.search-input-wrap').style.display = 'none';

    loadMember(id);
}

document.getElementById('clearMember').addEventListener('click', () => {
    memberIdInput.value = '';
    allMonths = []; totalBalance = 0;
    totalPayInput.value = '';
    searchInput.value = '';
    searchInput.style.display = '';
    document.querySelector('.search-input-wrap').style.display = '';
    document.getElementById('selectedMemberBadge').style.display = 'none';
    memberPanel.style.display  = 'none';
    document.getElementById('panelPlaceholder').style.display = 'block';
    document.getElementById('panelSummary').style.display     = 'none';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('chipRow').innerHTML  = '';
});

// ══════════════════════════════════════════════════════════
// LOAD MEMBER OUTSTANDING MONTHS
// ══════════════════════════════════════════════════════════
function loadMember(id) {
    fetch(`{{ url('monthly-payments/member-summary') }}/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        allMonths    = data.unpaid_months;
        totalBalance = allMonths.reduce((s, m) => s + parseFloat(m.balance), 0);

        const mm = data.member, s = data.summary;
        document.getElementById('mpAvatar').textContent  = mm.initials;
        document.getElementById('mpName').textContent    = mm.name;
        document.getElementById('mpDetail').textContent  = mm.nic + ' · ' + mm.occupation + ' · ' + mm.city;
        document.getElementById('mpBalance').textContent = fmt(totalBalance);

        document.getElementById('chipMonths').textContent  = s.count_unpaid  + ' unpaid';
        document.getElementById('chipPartial').textContent = s.count_partial + ' partial';

        document.getElementById('panelPlaceholder').style.display = 'none';
        document.getElementById('panelSummary').style.display     = 'block';
        document.getElementById('spOutstanding').textContent      = fmt(totalBalance);

        const noMsg = document.getElementById('noMonths');
        const wrap  = document.getElementById('monthsWrap');

        if (!allMonths.length) {
            noMsg.style.display = 'block';
            wrap.style.display  = 'none';
        } else {
            noMsg.style.display = 'none';
            wrap.style.display  = 'block';
            buildChips();
            renderTable([]);     // show rows at Rs 0 initially
        }

        totalPayInput.value = '';
        distribute(0);
        memberPanel.style.display = 'block';
    })
    .catch(() => alert('Failed to load member data. Please try again.'));
}

// ══════════════════════════════════════════════════════════
// QUICK-FILL CHIPS
// ══════════════════════════════════════════════════════════
function buildChips() {
    const row = document.getElementById('chipRow');
    if (!allMonths.length) { row.innerHTML = ''; return; }

    let running = 0;
    const chips = [];

    allMonths.forEach((m, i) => {
        running += parseFloat(m.balance);
        chips.push({ label: m.label, value: running, months: i + 1 });
    });

    // Show max 5 cumulative chips
    const shown = chips.slice(0, 5);
    row.innerHTML = shown.map(c =>
        `<span class="q-chip" onclick="applyChip(${c.value}, this)">
            ${c.months === 1 ? c.label : c.months + ' months'} &mdash; ${fmt(c.value)}
        </span>`
    ).join('');

    // Always add a "Pay All" chip
    if (shown.length < chips.length || chips.length === 1) {
        // Already last chip is "all" — skip duplicate
    } else {
        row.innerHTML += `<span class="q-chip" onclick="applyChip(${totalBalance}, this)">
            All &mdash; ${fmt(totalBalance)}
        </span>`;
    }
}

function applyChip(val, el) {
    document.querySelectorAll('.q-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    totalPayInput.value = parseFloat(val).toFixed(2);
    document.getElementById('overWarn').style.display = 'none';
    distribute(val);
}

// ══════════════════════════════════════════════════════════
// AMOUNT INPUT HANDLER
// ══════════════════════════════════════════════════════════
function onAmountChange() {
    document.querySelectorAll('.q-chip').forEach(c => c.classList.remove('active'));
    let val = parseFloat(totalPayInput.value) || 0;
    val = Math.max(0, val);

    const warn = document.getElementById('overWarn');
    if (val > totalBalance && totalBalance > 0) {
        warn.style.display = 'block';
        document.getElementById('overWarnMax').textContent = fmt(totalBalance);
    } else {
        warn.style.display = 'none';
    }

    distribute(val);
}

// ══════════════════════════════════════════════════════════
// CORE: CASCADE PAYMENT ACROSS MONTHS (oldest first)
// ══════════════════════════════════════════════════════════
function distribute(total) {
    // Do NOT cap — allow overpayment. The excess is recorded as credit.
    let bucket = total;

    const alloc = allMonths.map(m => {
        const bal   = parseFloat(m.balance);
        const apply = Math.min(bucket, bal);
        bucket -= apply;
        return { ...m, applying: apply, balance: bal, fee: parseFloat(m.fee), paid: parseFloat(m.paid) };
    });

    renderTable(alloc);
    buildHiddenInputs(alloc, total);
    updatePanel(total, alloc);

    document.getElementById('submitBtn').disabled = (total <= 0 || !memberIdInput.value);
}

// ══════════════════════════════════════════════════════════
// PAGINATION STATE
// ══════════════════════════════════════════════════════════
const PAGE_SIZE   = 10;
let   currentPage = 1;
let   lastAlloc   = [];

// ══════════════════════════════════════════════════════════
// RENDER TABLE (with pagination)
// ══════════════════════════════════════════════════════════
function renderTable(alloc) {
    lastAlloc = alloc;  // store for page changes
    currentPage = 1;    // reset to first page on new data
    renderPage();
}

function renderPage() {
    const tbody = document.getElementById('monthsTbody');
    if (!allMonths.length) { tbody.innerHTML = ''; renderPagination(0); return; }

    const totalPages = Math.ceil(allMonths.length / PAGE_SIZE);
    currentPage = Math.max(1, Math.min(currentPage, totalPages));

    const start = (currentPage - 1) * PAGE_SIZE;
    const end   = Math.min(start + PAGE_SIZE, allMonths.length);

    tbody.innerHTML = allMonths.slice(start, end).map((m, localIdx) => {
        const i = start + localIdx;
        const a = lastAlloc[i] || { applying: 0, balance: parseFloat(m.balance), fee: parseFloat(m.fee), paid: parseFloat(m.paid) };
        const afterPd  = a.paid + a.applying;
        const afterBal = Math.max(0, a.fee - afterPd);
        const pct      = a.fee > 0 ? Math.min(100, (afterPd / a.fee) * 100) : 0;

        let rowCls = '', valCls = 'muted', payTxt = '—', progClr = '#cbd5e1';

        if (a.applying > 0 && a.applying >= a.balance - 0.001) {
            rowCls = 'is-paying'; valCls = 'green';
            payTxt = fmt(a.applying); progClr = '#16a34a';
        } else if (a.applying > 0) {
            rowCls = 'is-partial'; valCls = 'amber';
            payTxt = fmt(a.applying); progClr = '#d97706';
        }

        const statusCls = m.status === 'unpaid' ? 's-unpaid' : (m.status === 'partial' ? 's-partial' : 's-paid');
        const statusLbl = m.status === 'unpaid' ? 'Unpaid'   : (m.status === 'partial' ? 'Partial'   : 'Paid');

        return `<tr class="${rowCls}">
            <td><span class="mo-chip"><i class="bi bi-calendar2 me-1"></i>${m.label}</span></td>
            <td style="font-weight:600;">Rs ${a.fee.toFixed(2)}</td>
            <td style="color:#15803d;font-weight:600;">Rs ${a.paid.toFixed(2)}</td>
            <td style="color:#b91c1c;font-weight:700;">Rs ${a.balance.toFixed(2)}</td>
            <td><span class="s-pill ${statusCls}">${statusLbl}</span></td>
            <td><span class="pay-now-val ${valCls}">${payTxt}</span></td>
            <td style="color:${afterBal > 0 ? '#b45309' : '#15803d'};font-weight:600;">Rs ${afterBal.toFixed(2)}</td>
            <td>
                <div class="mini-prog">
                    <div class="mini-prog-fill" style="width:${pct.toFixed(0)}%;background:${progClr};"></div>
                </div>
                <div style="font-size:10px;color:#94a3b8;margin-top:2px;">${pct.toFixed(0)}%</div>
            </td>
        </tr>`;
    }).join('');

    renderPagination(totalPages);
}

function renderPagination(totalPages) {
    const bar = document.getElementById('paginationBar');
    if (!bar) return;

    if (totalPages <= 1) { bar.innerHTML = ''; return; }

    const start = (currentPage - 1) * PAGE_SIZE + 1;
    const end   = Math.min(currentPage * PAGE_SIZE, allMonths.length);

    // Build page number buttons (show max 5 around current page)
    let pages = [];
    const delta = 2;
    for (let p = Math.max(1, currentPage - delta); p <= Math.min(totalPages, currentPage + delta); p++) {
        pages.push(p);
    }
    // Always show first and last
    if (pages[0] > 1) { pages = [1, '…', ...pages]; }
    if (pages[pages.length - 1] < totalPages) { pages = [...pages, '…', totalPages]; }

    const pageButtons = pages.map(p => {
        if (p === '…') return `<span style="padding:0 4px;color:#94a3b8;font-size:13px;">…</span>`;
        return `<button type="button" class="pg-btn ${p === currentPage ? 'active' : ''}" onclick="goPage(${p})">${p}</button>`;
    }).join('');

    bar.innerHTML = `
        <div class="pg-info">Showing ${start}–${end} of ${allMonths.length} months</div>
        <div style="display:flex;align-items:center;gap:5px;">
            <button type="button" class="pg-btn" onclick="goPage(${currentPage - 1})" ${currentPage <= 1 ? 'disabled' : ''}>
                <i class="bi bi-chevron-left"></i>
            </button>
            ${pageButtons}
            <button type="button" class="pg-btn" onclick="goPage(${currentPage + 1})" ${currentPage >= totalPages ? 'disabled' : ''}>
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    `;
}

function goPage(p) {
    const totalPages = Math.ceil(allMonths.length / PAGE_SIZE);
    if (p < 1 || p > totalPages) return;
    currentPage = p;
    renderPage();
    // Scroll table into view smoothly
    document.getElementById('monthsWrap').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ══════════════════════════════════════════════════════════
// SYNC PAID AMOUNT TO HIDDEN INPUT FOR FORM SUBMISSION
// One row = one transaction. Just pass the total amount.
// ══════════════════════════════════════════════════════════
function buildHiddenInputs(alloc, rawTotal) {
    // Use the raw entered amount — NOT the alloc sum which is capped per-month.
    // This ensures overpayments are submitted correctly to the controller.
    document.getElementById('paidAmountHidden').value = parseFloat(rawTotal || 0).toFixed(2);
}

// ══════════════════════════════════════════════════════════
// SUMMARY PANEL
// ══════════════════════════════════════════════════════════
function updatePanel(paying, alloc) {
    const overpaid  = Math.max(0, paying - totalBalance);
    const remaining = Math.max(0, totalBalance - paying);
    const isOver    = paying > totalBalance && totalBalance > 0;
    const pct       = totalBalance > 0 ? Math.min(100, (paying / totalBalance) * 100) : 0;

    document.getElementById('spPayingNow').textContent    = fmt(paying);
    document.getElementById('spOutstanding').textContent  = fmt(totalBalance);

    // Remaining / Credit row
    const remEl = document.getElementById('spRemaining');
    if (isOver) {
        remEl.textContent  = '+ ' + fmt(overpaid) + ' credit';
        remEl.className    = 'sp-val' + ' purple';
    } else {
        remEl.textContent  = fmt(remaining);
        remEl.className    = 'sp-val ' + (remaining > 0 ? 'red' : 'green');
    }

    // Update label text
    const labelEl = document.getElementById('spRemainingLabel');
    if (labelEl) labelEl.textContent = isOver ? 'Credit / Overpaid' : 'Still Remaining';

    // Progress bar — purple when overpaid
    const fill = document.getElementById('spProgFill');
    fill.style.width      = '100%';
    fill.style.background = isOver
        ? 'linear-gradient(90deg,#7c3aed,#a78bfa)'
        : 'linear-gradient(90deg,var(--gold),var(--gold-lt))';
    if (!isOver) fill.style.width = pct.toFixed(1) + '%';

    document.getElementById('spProgPct').textContent = isOver
        ? '✓ Fully paid + Rs ' + parseFloat(overpaid).toFixed(2) + ' overpaid'
        : pct.toFixed(0) + '% of balance covered';

    // Month breakdown list
    const list   = document.getElementById('spMonthList');
    const active = alloc.filter(a => a.applying > 0);

    if (!active.length) {
        list.innerHTML = '<div style="font-size:12px;color:#7a9abc;padding:6px 0;">Enter an amount to see breakdown.</div>';
        return;
    }

    let rows = active.map(a => {
        const isFull = a.applying >= a.balance - 0.001;
        return `<div class="sp-mo-row">
            <span class="sp-mo-name">${a.label}</span>
            <div class="sp-mo-right">
                <div class="sp-mo-amt ${isFull ? 'full' : 'partial'}">${fmt(a.applying)}</div>
                <div class="sp-mo-tag">${isFull ? '✓ Fully covered' : '~ Partial'}</div>
            </div>
        </div>`;
    }).join('');

    // Append overpaid credit row if applicable
    if (isOver) {
        rows += `<div class="sp-mo-row">
            <span class="sp-mo-name" style="color:#a78bfa;">Advance / Credit</span>
            <div class="sp-mo-right">
                <div class="sp-mo-amt" style="color:#a78bfa;font-weight:700;">+ ${fmt(overpaid)}</div>
                <div class="sp-mo-tag" style="color:#7c3aed;">Overpayment</div>
            </div>
        </div>`;
    }

    list.innerHTML = rows;
}
</script>
@endpush
