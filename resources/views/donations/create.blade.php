@extends('layouts.app')

@section('title', 'Record Donation')
@section('page-title', 'Record Donation')

@push('styles')
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; --teal:#0e9578; }

/* ── LAYOUT ──────────────────────────────────────────── */
.create-layout {
    display:grid;
    grid-template-columns:1fr 380px;
    gap:22px;
    align-items:start;
}
@media(max-width:991px){ .create-layout{grid-template-columns:1fr;} }

/* ── CARDS ───────────────────────────────────────────── */
.obs-card { background:#fff; border:1px solid #e4e9f0; border-radius:16px; overflow:hidden; animation:fadeUp .3s ease both; margin-bottom:18px; }
.obs-card-header { padding:15px 22px; border-bottom:1px solid #f0f3f8; background:#fafbfd; display:flex; align-items:center; gap:10px; }
.obs-card-header i { font-size:17px; color:var(--gold); }
.obs-card-header h4 { font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:var(--navy); margin:0; }
.obs-card-body { padding:22px; }

/* ── FORM FIELDS ─────────────────────────────────────── */
.form-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.8px; color:#5a7194; margin-bottom:6px; }
.req { color:#c0392b; margin-left:2px; }
.form-control, .form-select { border-radius:10px; border:1.5px solid #dde3ef; font-size:14px; padding:10px 14px; color:#1a2b44; transition:border-color .2s, box-shadow .2s; }
.form-control:focus, .form-select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); outline:none; }
.form-control.is-invalid, .form-select.is-invalid { border-color:#dc3545; }
.invalid-feedback { font-size:11.5px; }

/* ── SELECT2 CUSTOM STYLES ───────────────────────────── */
.select2-container .select2-selection--single {
    border-radius:10px !important;
    border:1.5px solid #dde3ef !important;
    height:44px !important;
    display:flex !important;
    align-items:center !important;
    font-size:14px;
    color:#1a2b44;
    transition:border-color .2s, box-shadow .2s;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color:var(--gold) !important;
    box-shadow:0 0 0 3px rgba(201,168,76,.15) !important;
    outline:none !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:44px !important; padding-left:14px !important; color:#1a2b44; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height:44px !important; right:8px !important; }
.select2-container--default .select2-selection--single .select2-selection__placeholder { color:#9bafc4; }
.select2-container--default .select2-results__option--highlighted { background:var(--navy) !important; color:#fff; }
.select2-dropdown { border-radius:12px !important; border:1px solid #e4e9f0 !important; box-shadow:0 8px 28px rgba(15,31,61,.12) !important; overflow:hidden; }
.select2-search--dropdown .select2-search__field { border-radius:8px !important; border:1px solid #dde3ef !important; padding:8px 12px !important; font-size:13.5px; }
.select2-search--dropdown .select2-search__field:focus { border-color:var(--gold) !important; outline:none !important; }
.select2-results__option { padding:10px 14px !important; font-size:13.5px; }
.select2-container--default .select2-results__option[aria-selected=true] { background:#f4f6fb; color:var(--navy); }

/* Member option template */
.s2-member-opt { display:flex; align-items:center; gap:10px; }
.s2-avatar { width:30px; height:30px; border-radius:7px; background:linear-gradient(135deg,#1e3a5f,#0f1f3d); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; }
.s2-name { font-weight:600; font-size:13.5px; color:#dce3e3; }
.s2-sub  { font-size:11px; color:#8494a9; }

/* ── MEMBER PREVIEW PANEL ────────────────────────────── */
#memberPreview { display:none; margin-top:12px; animation:fadeUp .25s ease; }
.preview-bar {
    background:linear-gradient(135deg, var(--navy), #1e3a5f);
    border-radius:12px; padding:14px 18px;
    display:flex; align-items:center; gap:14px; color:#fff;
}
.prev-avatar { width:42px; height:42px; border-radius:10px; background:linear-gradient(135deg, var(--gold), #f0d080); color:var(--navy); display:flex; align-items:center; justify-content:center; font-family:'Playfair Display',serif; font-size:18px; font-weight:700; flex-shrink:0; }
.prev-name { font-weight:700; font-size:14px; }
.prev-sub  { font-size:12px; color:#7a9abc; }
.prev-total { margin-left:auto; text-align:right; }
.prev-total-val   { font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:var(--gold); }
.prev-total-label { font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#7a9abc; }

/* ── AMOUNT INPUT ────────────────────────────────────── */
.amount-input-wrap { position:relative; }
.amount-prefix { position:absolute; left:14px; top:50%; transform:translateY(-50%); font-weight:700; color:#8494a9; font-size:14px; }
.amount-input  { padding-left:36px !important; }

/* ── SIDE PANEL ──────────────────────────────────────── */
.side-panel { background:var(--navy); border-radius:16px; padding:24px; color:#fff; position:sticky; top:84px; animation:fadeUp .3s .1s ease both; }
.sp-title { font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:#fff; margin-bottom:18px; display:flex; align-items:center; gap:8px; }
.sp-title i { color:var(--gold); }

.sp-preview-amount { text-align:center; padding:20px 0; }
.sp-preview-label  { font-size:11px; text-transform:uppercase; letter-spacing:1.4px; color:#7a9abc; margin-bottom:8px; }
.sp-preview-val    { font-family:'Playfair Display',serif; font-size:40px; font-weight:700; color:var(--gold); line-height:1; }
.sp-preview-reason { font-size:12px; color:#7a9abc; margin-top:6px; font-style:italic; }

.sp-divider { height:1px; background:rgba(255,255,255,.1); margin:12px 0; }
.sp-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; font-size:13px; }
.sp-label { color:#7a9abc; }
.sp-val   { font-weight:600; color:#fff; }
.sp-val.gold { color:var(--gold); }

.btn-submit {
    background:linear-gradient(135deg, var(--teal), #0b7a61);
    color:#fff; border:none; border-radius:10px;
    padding:13px 24px; font-size:14px; font-weight:700;
    cursor:pointer; width:100%; display:flex; align-items:center; justify-content:center; gap:8px;
    margin-top:16px; transition:opacity .2s;
}
.btn-submit:hover { opacity:.88; }
.btn-submit:disabled { opacity:.45; cursor:not-allowed; }

.btn-cancel { background:rgba(255,255,255,.08); color:#aac0d8; border:1px solid rgba(255,255,255,.15); border-radius:10px; padding:10px; font-size:13px; font-weight:600; text-decoration:none; display:block; text-align:center; margin-top:8px; }
.btn-cancel:hover { background:rgba(255,255,255,.15); color:#fff; }

/* ── COMMON REASONS ──────────────────────────────────── */
.reason-tag { display:inline-block; background:#f4f6fb; border:1px solid #e4e9f0; color:#3d5270; border-radius:20px; padding:4px 12px; font-size:12px; font-weight:500; cursor:pointer; margin:3px; transition:all .15s; }
.reason-tag:hover { background:var(--gold); color:var(--navy); border-color:var(--gold); }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header mb-3">
    <h1>Record New Donation</h1>
    <p>Select a member and enter the donation details below.</p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Please fix these errors:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('donations.store') }}" id="donationForm">
@csrf

<div class="create-layout">

{{-- ── LEFT ─────────────────────────────────────────── --}}
<div>

    {{-- Member Selection --}}
    <div class="obs-card" style="animation-delay:.05s">
        <div class="obs-card-header">
            <i class="bi bi-person-fill"></i>
            <h4>Select Member</h4>
        </div>
        <div class="obs-card-body">
            <label class="form-label">Member <span class="req">*</span></label>
            <select name="member_id" id="memberSelect" class="@error('member_id') is-invalid @enderror" style="width:100%;" required>
                <option value="">Search by name, NIC, or phone…</option>
            </select>
            @error('member_id')<div class="invalid-feedback d-block mt-1">{{ $message }}</div>@enderror

            {{-- Member preview --}}
            <div id="memberPreview">
                <div class="preview-bar">
                    <div class="prev-avatar" id="pvAvatar">?</div>
                    <div>
                        <div class="prev-name" id="pvName">—</div>
                        <div class="prev-sub"  id="pvSub">—</div>
                    </div>
                    <div class="prev-total">
                        <div class="prev-total-val"   id="pvTotal">Rs 0</div>
                        <div class="prev-total-label">Previous Donations</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Donation Details --}}
    <div class="obs-card" style="animation-delay:.1s">
        <div class="obs-card-header">
            <i class="bi bi-gift-fill"></i>
            <h4>Donation Details</h4>
        </div>
        <div class="obs-card-body">
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label">Reason for Donation <span class="req">*</span></label>
                    <input type="text" name="reason" id="reasonInput"
                           class="form-control @error('reason') is-invalid @enderror"
                           value="{{ old('reason') }}"
                           placeholder="e.g. Annual Gala Fund, School Library Development…"
                           required>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    {{-- Common reasons quick-fill --}}
                    <div style="margin-top:10px;">
                        <div style="font-size:11px;color:#8494a9;font-weight:600;margin-bottom:6px;">Common reasons:</div>
                        @foreach(['Annual Gala', 'School Library', 'Sports Development', 'Scholarship Fund', 'Infrastructure', 'Cultural Events', 'Emergency Relief'] as $r)
                        <span class="reason-tag" onclick="document.getElementById('reasonInput').value='{{ $r }}'; updateSidePanel();">{{ $r }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Donation Amount <span class="req">*</span></label>
                    <div class="amount-input-wrap">
                        <span class="amount-prefix">Rs</span>
                        <input type="number" name="amount" id="amountInput"
                               class="form-control amount-input @error('amount') is-invalid @enderror"
                               value="{{ old('amount') }}"
                               step="0.01" min="1" placeholder="0.00" required>
                    </div>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Donation Date <span class="req">*</span></label>
                    <input type="date" name="donation_date"
                           class="form-control @error('donation_date') is-invalid @enderror"
                           value="{{ old('donation_date', now()->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}" required>
                    @error('donation_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status <span class="req">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="received" {{ old('status','received') === 'received' ? 'selected' : '' }}>Received</option>
                        <option value="pending"  {{ old('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Receipt Number</label>
                    <input type="text" name="receipt_number"
                           class="form-control @error('receipt_number') is-invalid @enderror"
                           value="{{ old('receipt_number') }}" placeholder="optional">
                    @error('receipt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2"
                              class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Any additional remarks…">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>
        </div>
    </div>

</div>

{{-- ── RIGHT PANEL ──────────────────────────────────── --}}
<div>
    <div class="side-panel">
        <div class="sp-title"><i class="bi bi-gift-fill"></i> Donation Preview</div>

        <div class="sp-preview-amount">
            <div class="sp-preview-label">Amount to Record</div>
            <div class="sp-preview-val" id="spAmount">Rs 0</div>
            <div class="sp-preview-reason" id="spReason">—</div>
        </div>

        <div class="sp-divider"></div>

        <div class="sp-row"><span class="sp-label">Member</span><span class="sp-val" id="spMember">Not selected</span></div>
        <div class="sp-row"><span class="sp-label">Date</span><span class="sp-val" id="spDate">—</span></div>
        <div class="sp-row"><span class="sp-label">Status</span><span class="sp-val gold" id="spStatus">Received</span></div>
        <div class="sp-row"><span class="sp-label">Receipt No.</span><span class="sp-val" id="spReceipt">—</span></div>

        <div class="sp-divider"></div>

        <div style="text-align:center;padding:12px 0;">
            <div style="font-size:10px;text-transform:uppercase;letter-spacing:1.4px;color:#7a9abc;margin-bottom:6px;">Member's Previous Donations</div>
            <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:#aac0d8;" id="spPrevious">—</div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
            <i class="bi bi-check-circle-fill"></i> Record Donation
        </button>
        <a href="{{ route('donations.index') }}" class="btn-cancel">Cancel</a>
    </div>
</div>

</div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ── SELECT2 INIT ───────────────────────────────────────
$('#memberSelect').select2({
    placeholder: 'Search by name, NIC, or phone…',
    minimumInputLength: 1,
    allowClear: true,
    ajax: {
        url: '{{ route("donations.search-member") }}',
        dataType: 'json',
        delay: 300,
        data: params => ({ q: params.term }),
        processResults: data => ({ results: data.results }),
        cache: true,
    },
    templateResult:    formatMemberOption,
    templateSelection: formatMemberSelection,
    dropdownParent: $('body'),
});

function formatMemberOption(m) {
    if (m.loading || !m.initials) return $('<span>' + m.text + '</span>');
    return $(`
        <div class="s2-member-opt">
            <div class="s2-avatar">${m.initials}</div>
            <div>
                <div class="s2-name">${m.name}</div>
                <div class="s2-sub">${m.nic} · ${m.phone} · ${m.occupation}</div>
            </div>
        </div>
    `);
}
function formatMemberSelection(m) {
    return m.text || m.name_with_initials || m.text;
}

// ── MEMBER SELECT EVENT ────────────────────────────────
$('#memberSelect').on('select2:select', function(e) {
    const m = e.params.data;
    // Show preview bar
    document.getElementById('pvAvatar').textContent = m.initials;
    document.getElementById('pvName').textContent   = m.name;
    document.getElementById('pvSub').textContent    = m.nic + ' · ' + m.occupation + ' · ' + m.city;
    document.getElementById('memberPreview').style.display = 'block';

    // Fetch member previous donation total
    fetch(`/donations/member-total/${m.id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('pvTotal').textContent = 'Rs ' + d.total.toLocaleString('en', {minimumFractionDigits:2});
            document.getElementById('spPrevious').textContent = 'Rs ' + d.total.toLocaleString('en', {minimumFractionDigits:2});
        })
        .catch(() => {});

    document.getElementById('spMember').textContent = m.name;
    updateSidePanel();
});

$('#memberSelect').on('select2:unselect select2:clear', function() {
    document.getElementById('memberPreview').style.display = 'none';
    document.getElementById('spMember').textContent = 'Not selected';
    document.getElementById('pvTotal').textContent  = 'Rs 0';
    document.getElementById('spPrevious').textContent = '—';
});

// ── LIVE SIDE PANEL ────────────────────────────────────
function fmt(n) { return 'Rs ' + parseFloat(n || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

function updateSidePanel() {
    const amt    = parseFloat(document.getElementById('amountInput').value) || 0;
    const reason = document.getElementById('reasonInput').value || '—';
    const date   = document.querySelector('[name="donation_date"]').value;
    const status = document.querySelector('[name="status"]').value;
    const rcpt   = document.querySelector('[name="receipt_number"]').value;

    document.getElementById('spAmount').textContent = fmt(amt);
    document.getElementById('spReason').textContent = reason;
    document.getElementById('spDate').textContent   = date ? new Date(date).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : '—';
    document.getElementById('spStatus').textContent = status.charAt(0).toUpperCase() + status.slice(1);
    document.getElementById('spReceipt').textContent= rcpt || '—';
}

document.getElementById('amountInput').addEventListener('input', updateSidePanel);
document.getElementById('reasonInput').addEventListener('input', updateSidePanel);
document.querySelector('[name="donation_date"]').addEventListener('change', updateSidePanel);
document.querySelector('[name="status"]').addEventListener('change', updateSidePanel);
document.querySelector('[name="receipt_number"]').addEventListener('input', updateSidePanel);
updateSidePanel();
</script>
@endpush
