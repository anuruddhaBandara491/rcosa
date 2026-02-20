@extends('layouts.app')

@section('title', 'Record Monthly Payment')
@section('page-title', 'Record Monthly Payment')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; --gold-lt:#f0d080; }

/* ═══ LAYOUT ════════════════════════════════════════════ */
.pay-wrap {
    display:grid;
    grid-template-columns: 1fr 400px;
    gap:20px;
    align-items:start;
}
@media(max-width:991px){ .pay-wrap{grid-template-columns:1fr;} }

/* ═══ CARDS ════════════════════════════════════════════ */
.obs-card {
    background:#fff; border:1px solid #e4e9f0;
    border-radius:16px; overflow:hidden;
    animation:fadeUp .3s ease both; margin-bottom:18px;
}
.obs-card-header {
    padding:15px 22px; border-bottom:1px solid #f0f3f8;
    background:#fafbfd; display:flex; align-items:center; gap:10px;
}
.obs-card-header i { font-size:17px; color:var(--gold); }
.obs-card-header h4 { font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--navy);margin:0; }
.obs-card-body { padding:22px; }

/* ═══ SEARCH BOX ════════════════════════════════════════ */
.search-container { position:relative; }
.search-input-wrap { position:relative; }
.search-input-wrap i.si { position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:16px;color:#8494a9;pointer-events:none; }
.search-input-wrap .spin { position:absolute;right:14px;top:50%;transform:translateY(-50%);display:none; }

#memberSearch {
    padding:12px 40px 12px 42px;
    border-radius:12px; border:1.5px solid #dde3ef;
    font-size:14px; width:100%; color:#1a2b44;
    transition:border-color .2s, box-shadow .2s;
}
#memberSearch:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); outline:none; }

/* ═══ SEARCH DROPDOWN ═══════════════════════════════════ */
#searchDropdown {
    position:absolute; top:calc(100% + 6px); left:0; right:0;
    background:#fff; border:1px solid #e4e9f0; border-radius:12px;
    box-shadow:0 10px 32px rgba(15,31,61,.12);
    z-index:200; max-height:320px; overflow-y:auto; display:none;
}
.dropdown-item-member {
    display:flex; align-items:center; gap:12px;
    padding:12px 16px; cursor:pointer;
    border-bottom:1px solid #f6f8fb; transition:background .15s;
}
.dropdown-item-member:last-child { border-bottom:none; }
.dropdown-item-member:hover { background:#f4f6fb; }
.di-avatar {
    width:36px;height:36px;border-radius:9px;flex-shrink:0;
    background:linear-gradient(135deg,#1e3a5f,#0f1f3d);
    color:var(--gold);display:flex;align-items:center;justify-content:center;
    font-size:14px;font-weight:700;
}
.di-name  { font-weight:600;font-size:13.5px;color:var(--navy); }
.di-sub   { font-size:11.5px;color:#8494a9; }
.dd-empty { padding:20px 16px;text-align:center;color:#8494a9;font-size:13px; }
.dd-loading { padding:16px;text-align:center;color:#8494a9;font-size:13px; }

/* ═══ MEMBER INFO PANEL ════════════════════════════════ */
#memberPanel { display:none; animation:fadeUp .3s ease; }

.member-profile {
    background:linear-gradient(135deg,var(--navy),#1e3a5f);
    border-radius:14px; padding:20px;
    display:flex; align-items:center; gap:16px; flex-wrap:wrap;
    margin-bottom:16px; color:#fff;
}
.mp-big-avatar {
    width:54px;height:54px;border-radius:14px;flex-shrink:0;
    background:linear-gradient(135deg,var(--gold),var(--gold-lt));
    color:var(--navy);display:flex;align-items:center;justify-content:center;
    font-family:'Playfair Display',serif;font-size:22px;font-weight:700;
}
.mp-name { font-family:'Playfair Display',serif;font-size:17px;font-weight:700;margin:0 0 3px; }
.mp-detail { font-size:12px;color:#7a9abc;margin:0; }
.mp-stats { margin-left:auto;text-align:right; }
.mp-stat-val { font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--gold);line-height:1; }
.mp-stat-label { font-size:11px;color:#7a9abc;margin-top:2px; }

/* ═══ MONTHS TABLE ══════════════════════════════════════ */
.months-table { width:100%;border-collapse:collapse;margin-top:4px; }
.months-table th {
    font-size:10.5px;text-transform:uppercase;letter-spacing:1.1px;
    color:#8494a9;font-weight:600;padding:10px 14px;
    border-bottom:1px solid #f0f3f8;background:#fafbfd;
}
.months-table td {
    padding:11px 14px;font-size:13.5px;color:#2c3e55;
    border-bottom:1px solid #f6f8fb;vertical-align:middle;
}
.months-table tr:last-child td { border-bottom:none; }
.months-table tbody tr:hover td { background:#fafbfd; }

.month-chip {
    display:inline-flex;align-items:center;gap:5px;
    font-size:12px;font-weight:700;padding:4px 10px;
    border-radius:8px;background:#e8ecf5;color:#1e3a5f;
}

.s-pill { font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap; }
.s-paid    { background:#e8f7ee;color:#1a8a45; }
.s-partial { background:#fff3d6;color:#b07d10; }
.s-unpaid  { background:#fdecea;color:#c0392b; }

/* Amount input cell */
.pay-input-wrap { position:relative; }
.pay-input-wrap span {
    position:absolute;left:10px;top:50%;transform:translateY(-50%);
    font-weight:700;color:#8494a9;font-size:13px;
}
.pay-input {
    padding:7px 10px 7px 28px;
    border-radius:9px;border:1.5px solid #dde3ef;
    font-size:13.5px;width:130px;color:#1a2b44;
    transition:border-color .2s;
}
.pay-input:focus { border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.12);outline:none; }
.pay-input.full-paid { border-color:#1a8a45;background:#f0fbf4; }

.quick-btn {
    font-size:10.5px;font-weight:600;padding:3px 8px;border-radius:6px;
    border:1px solid #e4e9f0;background:#f4f6fb;color:#3d5270;
    cursor:pointer;transition:all .15s;margin-left:4px;
}
.quick-btn:hover { background:var(--gold);color:var(--navy);border-color:var(--gold); }

/* Select all / none */
.select-all-btn {
    font-size:12px;font-weight:600;color:var(--gold);
    background:none;border:none;cursor:pointer;padding:0;text-decoration:underline;
}

/* ═══ SUMMARY PANEL (right sticky) ════════════════════ */
.summary-panel {
    background:var(--navy);border-radius:16px;padding:24px;
    color:#fff;position:sticky;top:84px;
    animation:fadeUp .3s .1s ease both;
}
.sp-title { font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:8px; }
.sp-title i { color:var(--gold); }

.sp-row { display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.07);font-size:13.5px; }
.sp-row:last-of-type { border-bottom:none; }
.sp-label { color:#7a9abc; }
.sp-val   { font-weight:700;color:#fff;font-size:15px; }
.sp-val.gold  { color:var(--gold); }
.sp-val.green { color:#5de89a; }
.sp-val.red   { color:#f07070; }

.sp-months-list { margin-top:14px;max-height:200px;overflow-y:auto;padding-right:4px; }
.sp-month-row {
    display:flex;justify-content:space-between;align-items:center;
    padding:7px 0;border-bottom:1px solid rgba(255,255,255,.05);
    font-size:12.5px;
}
.sp-month-row:last-child { border-bottom:none; }
.sp-month-name { color:#aac0d8; }
.sp-month-amt  { color:var(--gold);font-weight:700; }

.sp-divider { height:1px;background:rgba(255,255,255,.1);margin:10px 0; }

.btn-submit {
    background:var(--gold);color:var(--navy);border:none;
    border-radius:10px;padding:12px 24px;font-size:14px;font-weight:700;
    cursor:pointer;width:100%;display:flex;align-items:center;justify-content:center;gap:7px;margin-top:4px;
    transition:background .2s;
}
.btn-submit:hover { background:var(--gold-lt); }
.btn-cancel { background:rgba(255,255,255,.08);color:#aac0d8;border:1px solid rgba(255,255,255,.15);border-radius:10px;padding:10px;font-size:13px;font-weight:600;text-decoration:none;display:block;text-align:center;margin-top:8px; }
.btn-cancel:hover { background:rgba(255,255,255,.15);color:#fff; }

/* form fields */
.form-label { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#5a7194;margin-bottom:6px; }
.form-control, .form-select { border-radius:10px;border:1px solid #dde3ef;font-size:14px;padding:9px 14px;color:#1a2b44;transition:border-color .2s, box-shadow .2s; }
.form-control:focus, .form-select:focus { border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.15);outline:none; }

/* scrollbar */
.sp-months-list::-webkit-scrollbar { width:3px; }
.sp-months-list::-webkit-scrollbar-thumb { background:rgba(255,255,255,.15);border-radius:3px; }

/* placeholder state */
.panel-placeholder { text-align:center;padding:30px 10px;color:#7a9abc; }
.panel-placeholder i { font-size:32px;display:block;margin-bottom:10px;opacity:.4; }
.panel-placeholder p { font-size:13px;margin:0; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header mb-3">
    <h1>Record Monthly Payment</h1>
    <p>Search for a member, then enter payment amounts for outstanding months.</p>
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

    {{-- STEP 1: Search Member --}}
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
                    <input type="text" id="memberSearch" placeholder="Start typing to search members…" autocomplete="off">
                    <div class="spin">
                        <div class="spinner-border spinner-border-sm text-secondary" role="status" style="width:16px;height:16px;"></div>
                    </div>
                </div>
                <div id="searchDropdown">
                    <div class="dd-loading">Searching…</div>
                </div>
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

    {{-- STEP 2: Member summary + months table --}}
    <div id="memberPanel">
        <div class="obs-card" style="animation-delay:.1s">
            <div class="obs-card-header">
                <i class="bi bi-calendar2-check-fill"></i>
                <h4>Step 2 — Outstanding Months</h4>
            </div>
            <div class="obs-card-body" style="padding-bottom:8px;">

                {{-- Member profile bar --}}
                <div class="member-profile">
                    <div class="mp-big-avatar" id="mpAvatar">?</div>
                    <div>
                        <p class="mp-name" id="mpName">—</p>
                        <p class="mp-detail" id="mpDetail">—</p>
                    </div>
                    <div class="mp-stats">
                        <div class="mp-stat-val" id="mpBalance">Rs 0</div>
                        <div class="mp-stat-label">Total Outstanding</div>
                    </div>
                </div>

                {{-- Summary chips --}}
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <span id="chipMonths"  style="font-size:12px;background:#fdecea;color:#c0392b;padding:3px 12px;border-radius:20px;font-weight:600;">— unpaid months</span>
                    <span id="chipPartial" style="font-size:12px;background:#fff3d6;color:#b07d10;padding:3px 12px;border-radius:20px;font-weight:600;">— partial</span>
                </div>

                {{-- Quick controls --}}
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size:12px;color:#8494a9;font-weight:600;text-transform:uppercase;letter-spacing:.8px;">Monthly Breakdown</span>
                    <div>
                        <button type="button" class="select-all-btn" onclick="fillAll()">Pay All Balances</button>
                        <span style="color:#ccc;margin:0 6px;">|</span>
                        <button type="button" class="select-all-btn" onclick="clearAll()" style="color:#8494a9;">Clear All</button>
                    </div>
                </div>

                <div id="noMonths" style="display:none;text-align:center;padding:30px;color:#8494a9;">
                    <i class="bi bi-check-circle-fill" style="font-size:32px;color:#1a8a45;display:block;margin-bottom:8px;"></i>
                    <p style="margin:0;font-size:13px;">All monthly fees are fully paid for this member!</p>
                </div>

                <div id="monthsTableWrap">
                    <table class="months-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Fee</th>
                                <th>Already Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Pay Now</th>
                            </tr>
                        </thead>
                        <tbody id="monthsTableBody">
                            <tr><td colspan="6" style="text-align:center;padding:30px;color:#8494a9;">Search for a member above…</td></tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        {{-- STEP 3: Payment metadata --}}
        <div class="obs-card" style="animation-delay:.15s">
            <div class="obs-card-header">
                <i class="bi bi-receipt"></i>
                <h4>Step 3 — Payment Details</h4>
            </div>
            <div class="obs-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Payment Date <span style="color:#c0392b;">*</span></label>
                        <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror"
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" required>
                        @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Receipt Number</label>
                        <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number') }}" placeholder="optional">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="optional">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══ RIGHT COLUMN — SUMMARY ═══════════════════════════ --}}
<div>
    <div class="summary-panel">
        <div class="sp-title"><i class="bi bi-calculator-fill"></i> Payment Summary</div>

        <div id="panelPlaceholder" class="panel-placeholder">
            <i class="bi bi-person-search"></i>
            <p>Search and select a member to see their outstanding balance.</p>
        </div>

        <div id="panelSummary" style="display:none;">
            <div class="sp-row">
                <span class="sp-label">Monthly Fee</span>
                <span class="sp-val">Rs {{ number_format($fee, 2) }}</span>
            </div>
            <div class="sp-row">
                <span class="sp-label">Months Outstanding</span>
                <span class="sp-val gold" id="spMonthCount">0</span>
            </div>
            <div class="sp-row">
                <span class="sp-label">Total Balance</span>
                <span class="sp-val red" id="spTotalBalance">Rs 0.00</span>
            </div>
            <div class="sp-divider"></div>
            <div class="sp-row">
                <span class="sp-label">Paying Now</span>
                <span class="sp-val green" id="spPayingNow">Rs 0.00</span>
            </div>
            <div class="sp-row">
                <span class="sp-label">Remaining After</span>
                <span class="sp-val red" id="spRemaining">Rs 0.00</span>
            </div>

            <div id="spMonthsList" class="sp-months-list" style="margin-top:12px;"></div>
        </div>

        <div class="mt-4" id="submitArea">
            <button type="submit" class="btn-submit" id="submitBtn" disabled>
                <i class="bi bi-check-circle-fill"></i> Record Payments
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
const MONTHLY_FEE   = parseFloat(document.getElementById('monthlyFeeVal').value);
const searchInput   = document.getElementById('memberSearch');
const dropdown      = document.getElementById('searchDropdown');
const memberPanel   = document.getElementById('memberPanel');
const memberIdInput = document.getElementById('memberIdInput');

let searchTimer   = null;
let currentMember = null;
let allUnpaidMonths = [];

// ── FMT ────────────────────────────────────────────────
function fmt(n) { return 'Rs ' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

// ── SEARCH ─────────────────────────────────────────────
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 2) { dropdown.style.display = 'none'; return; }
    dropdown.style.display = 'block';
    dropdown.innerHTML = '<div class="dd-loading"><i class="bi bi-hourglass-split me-1"></i> Searching…</div>';
    document.querySelector('.spin').style.display = 'block';

    searchTimer = setTimeout(() => {
        fetch(`{{ route('monthly-payments.search-member') }}?q=${encodeURIComponent(q)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(members => {
            document.querySelector('.spin').style.display = 'none';
            if (!members.length) {
                dropdown.innerHTML = '<div class="dd-empty"><i class="bi bi-person-x me-1"></i> No members found</div>';
                return;
            }
            dropdown.innerHTML = members.map(m => `
                <div class="dropdown-item-member" onclick="selectMember(${m.id}, '${m.name.replace(/'/g,"\\'")}', '${m.nic}', '${m.initials}')">
                    <div class="di-avatar">${m.initials}</div>
                    <div>
                        <div class="di-name">${m.name}</div>
                        <div class="di-sub">${m.nic} · ${m.phone} · ${m.occupation}</div>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {
            document.querySelector('.spin').style.display = 'none';
            dropdown.innerHTML = '<div class="dd-empty text-danger"><i class="bi bi-exclamation me-1"></i> Search failed. Try again.</div>';
        });
    }, 320);
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-container')) dropdown.style.display = 'none';
});

// ── SELECT MEMBER ───────────────────────────────────────
function selectMember(id, name, nic, initials) {
    dropdown.style.display = 'none';
    searchInput.value = '';
    memberIdInput.value = id;
    currentMember = { id, name, nic, initials };

    // Show selected badge
    document.getElementById('selectedMemberBadge').style.display = 'block';
    document.getElementById('selectedMemberName').textContent = name + ' (' + nic + ')';

    // Hide search box
    searchInput.style.display = 'none';
    document.querySelector('label[for="memberSearch"], .search-input-wrap').style.display = 'none';

    // Load member data
    loadMemberSummary(id);
}

document.getElementById('clearMember').addEventListener('click', function() {
    memberIdInput.value   = '';
    currentMember         = null;
    allUnpaidMonths       = [];
    searchInput.value     = '';
    searchInput.style.display = '';
    document.querySelector('.search-input-wrap').style.display = '';
    document.getElementById('selectedMemberBadge').style.display = 'none';
    memberPanel.style.display = 'none';
    document.getElementById('panelPlaceholder').style.display = 'block';
    document.getElementById('panelSummary').style.display     = 'none';
    document.getElementById('submitBtn').disabled = true;
});

// ── LOAD MEMBER SUMMARY VIA AJAX ───────────────────────
function loadMemberSummary(memberId) {
    fetch(`{{ url('monthly-payments/member-summary') }}/${memberId}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        allUnpaidMonths = data.unpaid_months;
        renderMemberPanel(data);
        memberPanel.style.display = 'block';
    })
    .catch(() => alert('Failed to load member data. Please try again.'));
}

function renderMemberPanel(data) {
    const m = data.member;
    const s = data.summary;

    // Profile bar
    document.getElementById('mpAvatar').textContent  = m.initials;
    document.getElementById('mpName').textContent    = m.name;
    document.getElementById('mpDetail').textContent  = m.nic + ' · ' + m.occupation + ' · ' + m.city;
    document.getElementById('mpBalance').textContent = fmt(s.total_balance);

    // Chips
    document.getElementById('chipMonths').textContent  = s.count_unpaid  + ' unpaid months';
    document.getElementById('chipPartial').textContent = s.count_partial + ' partial';

    // Summary panel
    document.getElementById('panelPlaceholder').style.display = 'none';
    document.getElementById('panelSummary').style.display     = 'block';
    document.getElementById('spMonthCount').textContent = s.total_months;
    document.getElementById('spTotalBalance').textContent = fmt(s.total_balance);

    // Months table
    renderMonthsTable(allUnpaidMonths);
    updateSummary();
}

function renderMonthsTable(months) {
    const tbody = document.getElementById('monthsTableBody');
    const noMsg = document.getElementById('noMonths');
    const wrap  = document.getElementById('monthsTableWrap');

    if (!months.length) {
        noMsg.style.display = 'block';
        wrap.style.display  = 'none';
        document.getElementById('submitBtn').disabled = true;
        return;
    }

    noMsg.style.display = 'none';
    wrap.style.display  = 'block';

    tbody.innerHTML = months.map((m, i) => `
        <tr data-balance="${m.balance}" data-idx="${i}">
            <td>
                <input type="hidden" name="payments[${i}][month]" value="${m.month}">
                <input type="hidden" name="payments[${i}][year]"  value="${m.year}">
                <span class="month-chip"><i class="bi bi-calendar2 me-1"></i>${m.label}</span>
            </td>
            <td style="font-weight:600;">Rs ${parseFloat(m.fee).toFixed(2)}</td>
            <td style="color:#1a8a45;font-weight:600;">Rs ${parseFloat(m.paid).toFixed(2)}</td>
            <td style="color:${m.balance > 0 ? '#c0392b' : '#1a8a45'};font-weight:700;">Rs ${parseFloat(m.balance).toFixed(2)}</td>
            <td><span class="s-pill s-${m.status}">${m.status === 'unpaid' ? 'Unpaid' : (m.status === 'partial' ? 'Partial' : 'Paid')}</span></td>
            <td>
                <div style="display:flex;align-items:center;gap:4px;">
                    <div class="pay-input-wrap">
                        <span>Rs</span>
                        <input type="number" name="payments[${i}][pay_amount]"
                               class="pay-input" id="payInput${i}"
                               step="0.01" min="0" max="${m.balance}"
                               value="0" placeholder="0.00"
                               oninput="updateSummary()">
                    </div>
                    <button type="button" class="quick-btn" onclick="fillBalance(${i}, ${m.balance})" title="Pay full balance">Full</button>
                </div>
            </td>
        </tr>
    `).join('');
}

// ── QUICK FILL ──────────────────────────────────────────
function fillBalance(idx, balance) {
    const inp = document.getElementById('payInput' + idx);
    if (inp) { inp.value = parseFloat(balance).toFixed(2); updateSummary(); }
}

function fillAll() {
    allUnpaidMonths.forEach((m, i) => {
        const inp = document.getElementById('payInput' + i);
        if (inp) inp.value = parseFloat(m.balance).toFixed(2);
    });
    updateSummary();
}

function clearAll() {
    allUnpaidMonths.forEach((m, i) => {
        const inp = document.getElementById('payInput' + i);
        if (inp) inp.value = '0';
    });
    updateSummary();
}

// ── LIVE SUMMARY ────────────────────────────────────────
function updateSummary() {
    const totalBalance = allUnpaidMonths.reduce((s, m) => s + m.balance, 0);
    let   payingNow    = 0;
    const monthEntries = [];

    allUnpaidMonths.forEach((m, i) => {
        const inp = document.getElementById('payInput' + i);
        if (!inp) return;
        const amt = Math.min(m.balance, Math.max(0, parseFloat(inp.value) || 0));
        if (amt > 0) {
            payingNow += amt;
            monthEntries.push({ label: m.label, amount: amt });
        }
    });

    const remaining = Math.max(0, totalBalance - payingNow);

    document.getElementById('spPayingNow').textContent  = fmt(payingNow);
    document.getElementById('spRemaining').textContent  = fmt(remaining);
    document.getElementById('spTotalBalance').textContent = fmt(totalBalance);

    // Month breakdown list
    const list = document.getElementById('spMonthsList');
    if (monthEntries.length) {
        list.innerHTML = '<div style="font-size:10.5px;text-transform:uppercase;letter-spacing:1.2px;color:#7a9abc;font-weight:600;margin-bottom:8px;">Paying for:</div>' +
            monthEntries.map(e => `
                <div class="sp-month-row">
                    <span class="sp-month-name">${e.label}</span>
                    <span class="sp-month-amt">${fmt(e.amount)}</span>
                </div>
            `).join('');
    } else {
        list.innerHTML = '';
    }

    // Enable/disable submit
    document.getElementById('submitBtn').disabled = (payingNow <= 0 || !memberIdInput.value);
}
</script>
@endpush
