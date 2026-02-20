@extends('layouts.app')

@section('title', $member ? $member->name_with_initials . ' — Member Report' : 'Member Details')
@section('page-title', 'Member Details & Reports')

@push('styles')
<style>
/* ─────────────────────────────────────────────────────
   DESIGN TOKENS
───────────────────────────────────────────────────── */
:root {
    --navy:    #0f1f3d;
    --navy-lt: #1e3a5f;
    --gold:    #c9a84c;
    --gold-lt: #f0d080;
    --teal:    #0d9488;
    --amber:   #d97706;
    --red:     #dc2626;
    --green:   #16a34a;
    --surface: #f6f8fb;
    --border:  #e4e9f0;
}

/* ─────────────────────────────────────────────────────
   SEARCH SECTION
───────────────────────────────────────────────────── */
.search-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 26px 28px;
    margin-bottom: 24px;
    animation: fadeUp .3s ease both;
}
.search-card-title {
    font-family: 'Playfair Display', serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--navy);
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 9px;
}
.search-card-title i { color: var(--gold); }

/* Input */
.search-wrap  { position: relative; }
.search-wrap .si-icon {
    position: absolute; left: 16px; top: 50%;
    transform: translateY(-50%);
    color: #94a3b8; font-size: 16px;
    pointer-events: none; z-index: 1;
}
.search-wrap .si-spin {
    position: absolute; right: 16px; top: 50%;
    transform: translateY(-50%);
    display: none;
}
#memberSearch {
    width: 100%;
    padding: 13px 46px;
    border-radius: 12px;
    border: 2px solid var(--border);
    font-size: 14.5px;
    color: var(--navy);
    background: var(--surface);
    transition: border-color .2s, box-shadow .2s, background .2s;
    font-family: 'DM Sans', sans-serif;
}
#memberSearch:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 4px rgba(201,168,76,.12);
    background: #fff;
    outline: none;
}

/* Dropdown */
#searchDropdown {
    position: absolute;
    top: calc(100% + 8px); left: 0; right: 0;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: 0 16px 48px rgba(15,31,61,.13);
    z-index: 500;
    overflow: hidden;
    display: none;
    max-height: 400px;
    overflow-y: auto;
}
#searchDropdown::-webkit-scrollbar { width: 3px; }
#searchDropdown::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

.sd-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background .12s;
}
.sd-item:last-child { border-bottom: none; }
.sd-item:hover { background: var(--surface); }
.sd-avatar {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    background: linear-gradient(135deg, var(--navy-lt), var(--navy));
    color: var(--gold);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; font-weight: 700;
}
.sd-name  { font-weight: 700; font-size: 14px; color: var(--navy); }
.sd-meta  { font-size: 11.5px; color: #64748b; margin-top: 1px; }
.sd-badge {
    margin-left: auto; flex-shrink: 0;
    font-size: 10.5px; font-weight: 700;
    background: rgba(201,168,76,.15);
    color: var(--gold);
    padding: 2px 8px; border-radius: 20px;
}
.sd-hint { padding: 18px 16px; text-align: center; color: #94a3b8; font-size: 13.5px; }

/* Quick access chips */
.quick-chips { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 13px; align-items: center; }
.qc-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .9px; color: #94a3b8; }
.qc-chip {
    font-size: 12px; font-weight: 500;
    background: var(--surface); border: 1px solid var(--border);
    color: #475569; padding: 4px 12px; border-radius: 20px;
    text-decoration: none; transition: all .15s; white-space: nowrap;
}
.qc-chip:hover { background: var(--gold); color: var(--navy); border-color: var(--gold); }

/* ─────────────────────────────────────────────────────
   EMPTY STATE
───────────────────────────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 64px 20px;
    animation: fadeUp .4s ease;
}
.empty-icon {
    width: 88px; height: 88px; border-radius: 50%;
    background: linear-gradient(135deg, rgba(201,168,76,.12), rgba(201,168,76,.04));
    border: 2px dashed rgba(201,168,76,.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 34px; margin: 0 auto 22px;
}
.empty-state h3 {
    font-family: 'Playfair Display', serif;
    font-size: 22px; font-weight: 700;
    color: var(--navy); margin: 0 0 9px;
}
.empty-state p { font-size: 14px; color: #64748b; max-width: 360px; margin: 0 auto; }

/* ─────────────────────────────────────────────────────
   PROFILE HERO
───────────────────────────────────────────────────── */
.profile-hero {
    background: linear-gradient(135deg, var(--navy) 0%, #1a3258 55%, #0d2a4a 100%);
    border-radius: 20px;
    padding: 28px 30px;
    margin-bottom: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
    animation: fadeUp .35s ease;
}
/* decorative circles */
.profile-hero::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 240px; height: 240px;
    background: rgba(201,168,76,.07); border-radius: 50%;
    pointer-events: none;
}
.profile-hero::after {
    content: '';
    position: absolute; bottom: -80px; left: 35%;
    width: 280px; height: 280px;
    background: rgba(201,168,76,.04); border-radius: 50%;
    pointer-events: none;
}
.hero-inner {
    display: flex;
    align-items: center;
    gap: 22px;
    flex-wrap: wrap;
    position: relative; z-index: 1;
}
.hero-avatar {
    width: 76px; height: 76px; border-radius: 18px; flex-shrink: 0;
    background: linear-gradient(135deg, var(--gold), var(--gold-lt));
    color: var(--navy);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif;
    font-size: 32px; font-weight: 700;
    box-shadow: 0 8px 24px rgba(0,0,0,.25);
}
.hero-info { flex: 1; min-width: 0; }
.hero-name {
    font-family: 'Playfair Display', serif;
    font-size: 24px; font-weight: 700;
    margin: 0 0 5px; line-height: 1.2;
}
.hero-sub  { font-size: 13px; color: #7a9bc0; margin: 0 0 12px; }
.hero-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.htag {
    font-size: 11px; font-weight: 600;
    padding: 3px 11px; border-radius: 20px;
    background: rgba(255,255,255,.1); color: #fff;
}
.htag.gold { background: rgba(201,168,76,.22); color: var(--gold-lt); }

/* Quick stats strip on hero right */
.hero-stats-strip {
    display: flex;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 14px; overflow: hidden;
}
.hss-cell {
    padding: 14px 18px; text-align: center;
    border-right: 1px solid rgba(255,255,255,.07);
    min-width: 100px;
}
.hss-cell:last-child { border-right: none; }
.hss-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1.2px; color: #7a9bc0; margin-bottom: 6px; }
.hss-val   { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; line-height: 1; }
.hss-val.green { color: #4ade80; }
.hss-val.red   { color: #f87171; }
.hss-val.gold  { color: var(--gold-lt); }

/* Hero action buttons */
.hero-actions { display: flex; gap: 8px; flex-wrap: wrap; position: relative; z-index: 1; margin-top: 16px; }
.hbtn {
    font-size: 12px; font-weight: 600;
    padding: 8px 15px; border-radius: 9px;
    border: 1px solid rgba(255,255,255,.18);
    background: rgba(255,255,255,.1);
    color: #fff; text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
    transition: all .15s;
}
.hbtn:hover { background: rgba(255,255,255,.2); color: #fff; }
.hbtn.primary { background: var(--gold); color: var(--navy); border-color: transparent; }
.hbtn.primary:hover { background: var(--gold-lt); color: var(--navy); }

/* ─────────────────────────────────────────────────────
   SUMMARY BAR (4 finance cards)
───────────────────────────────────────────────────── */
.fin-bar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
@media(max-width:900px) { .fin-bar { grid-template-columns: 1fr 1fr; } }

.fin-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 16px 18px;
    display: flex; align-items: center; gap: 13px;
    animation: fadeUp .4s ease both;
}
.fin-card:nth-child(1) { animation-delay:.05s }
.fin-card:nth-child(2) { animation-delay:.09s }
.fin-card:nth-child(3) { animation-delay:.13s }
.fin-card:nth-child(4) { animation-delay:.17s }

.ficon {
    width: 42px; height: 42px; border-radius: 11px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.fin-label { font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 4px; }
.fin-val   { font-family: 'Playfair Display', serif; font-size: 19px; font-weight: 700; color: var(--navy); line-height: 1; }
.fin-sub   { font-size: 11px; color: #94a3b8; margin-top: 3px; }

/* ─────────────────────────────────────────────────────
   TAB SYSTEM
───────────────────────────────────────────────────── */
.tab-nav {
    display: flex; gap: 0;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 5px;
    margin-bottom: 20px;
    overflow-x: auto;
    animation: fadeUp .45s ease;
}
.tab-btn {
    flex: 1; min-width: 130px;
    padding: 10px 16px;
    border: none; background: transparent;
    color: #64748b;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px; font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    transition: all .18s;
    white-space: nowrap;
}
.tab-btn i { font-size: 15px; }
.tab-btn:hover { background: var(--surface); color: var(--navy); }
.tab-btn.active {
    background: var(--navy);
    color: #fff;
    box-shadow: 0 4px 14px rgba(15,31,61,.18);
}
.tab-count {
    font-size: 10px; font-weight: 700;
    padding: 1px 7px; border-radius: 99px;
    background: rgba(201,168,76,.15); color: var(--gold);
}
.tab-btn.active .tab-count { background: rgba(255,255,255,.15); color: #fff; }

.tab-pane { display: none; animation: fadeUp .2s ease; }
.tab-pane.active { display: block; }

/* ─────────────────────────────────────────────────────
   CARDS
───────────────────────────────────────────────────── */
.obs-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
    margin-bottom: 18px; animation: fadeUp .35s ease both;
}
.obs-card:nth-child(1) { animation-delay:.05s }
.obs-card:nth-child(2) { animation-delay:.09s }
.obs-card:nth-child(3) { animation-delay:.13s }

.card-head {
    padding: 13px 20px;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfd;
    display: flex; align-items: center; justify-content: space-between;
}
.card-title {
    font-family: 'Playfair Display', serif;
    font-size: 14px; font-weight: 700;
    color: var(--navy); margin: 0;
    display: flex; align-items: center; gap: 8px;
}
.card-title i { font-size: 15px; color: var(--gold); }
.card-link { font-size: 12px; font-weight: 600; color: var(--teal); text-decoration: none; }
.card-link:hover { text-decoration: underline; }

/* Detail grid */
.det-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
}
.det-cell {
    padding: 14px 20px;
    border-bottom: 1px solid #f8fafc;
    border-right: 1px solid #f8fafc;
}
.det-label { font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 5px; }
.det-value { font-size: 13.5px; color: #1e293b; font-weight: 500; }
.det-value.mono { font-family: monospace; letter-spacing: .4px; }
.det-value.nil  { color: #cbd5e1; font-style: italic; font-weight: 400; }

/* Registration payment: 3-column layout */
.reg-cols {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
}
.reg-col {
    padding: 20px 22px; text-align: center;
    border-right: 1px solid #f1f5f9;
}
.reg-col:last-child { border-right: none; }
.reg-col-label { font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 8px; }
.reg-col-val   { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; line-height: 1; }
.reg-progress  { padding: 10px 20px 16px; }
.rp-track { height: 7px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.rp-fill  { height: 100%; border-radius: 99px; transition: width .6s ease; }
.rp-meta  { display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; margin-top: 5px; }

/* ─────────────────────────────────────────────────────
   TABLES
───────────────────────────────────────────────────── */
.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
    font-size: 10.5px; text-transform: uppercase;
    letter-spacing: 1.1px; color: #94a3b8; font-weight: 600;
    padding: 11px 18px; border-bottom: 1px solid #f1f5f9;
    background: #fafbfd; white-space: nowrap;
}
.data-table td {
    padding: 13px 18px; font-size: 13.5px;
    color: #1e293b; border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
}
.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover td { background: #fafbfd; }

/* Progress bar in table */
.tbl-prog-wrap { display: flex; align-items: center; gap: 8px; }
.tbl-prog-track { width: 70px; height: 5px; background: #f1f5f9; border-radius: 99px; overflow: hidden; flex-shrink: 0; }
.tbl-prog-fill  { height: 100%; border-radius: 99px; }

.month-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 12px; font-weight: 700;
    padding: 3px 10px; border-radius: 7px;
    background: #e8ecf5; color: #1e3a5f;
}

/* Table footer */
.tbl-footer {
    padding: 13px 20px;
    border-top: 1px solid #f1f5f9;
    background: #fafbfd;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
}
.tf-label { font-size: 11.5px; color: #64748b; font-weight: 600; }
.tf-val   { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; }

/* Donation amount */
.don-amount {
    font-family: 'Playfair Display', serif;
    font-size: 15px; font-weight: 700;
    color: var(--teal);
}

/* Status pills */
.pill { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; display: inline-block; white-space: nowrap; }
.pill-paid     { background: #dcfce7; color: #15803d; }
.pill-partial  { background: #fef3c7; color: #b45309; }
.pill-unpaid   { background: #fee2e2; color: #b91c1c; }
.pill-received { background: #ccfbf1; color: #0f766e; }
.pill-pending  { background: #fef3c7; color: #b45309; }

/* Empty table */
.tbl-empty { text-align: center; padding: 44px 20px; }
.tbl-empty i { font-size: 30px; display: block; margin-bottom: 10px; color: #cbd5e1; }
.tbl-empty p { font-size: 13px; color: #94a3b8; margin: 0; }

/* Children chips */
.child-chip {
    display: inline-flex; flex-direction: column;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; padding: 8px 14px;
    margin: 4px; font-size: 12.5px;
}
.child-chip strong { color: var(--navy); }
.child-chip span   { color: #64748b; font-size: 11.5px; }

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(13px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@section('content')

{{-- PAGE HEADER --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" style="animation:fadeUp .25s ease;">
    <div>
        <h1 class="page-title">Member Details & Reports</h1>
        <p style="color:#64748b;font-size:13.5px;margin:2px 0 0;">Search for a member to view their full financial profile.</p>
    </div>
    <a href="{{ route('members.index') }}" style="font-size:13px;font-weight:600;padding:9px 16px;border-radius:10px;background:#f6f8fb;color:#475569;border:1px solid var(--border);text-decoration:none;display:inline-flex;align-items:center;gap:7px;">
        <i class="bi bi-people"></i> All Members
    </a>
</div>

{{-- ─── SEARCH CARD ──────────────────────────────────────── --}}
<div class="search-card">
    <h3 class="search-card-title"><i class="bi bi-person-search"></i> Find Member</h3>

    <div class="search-wrap" id="searchWrap">
        <i class="bi bi-search si-icon"></i>
        <input type="text"
               id="memberSearch"
               placeholder="Type name, NIC number, or phone number…"
               autocomplete="off"
               value="{{ $member ? $member->name_with_initials : '' }}">
        <div class="si-spin">
            <div class="spinner-border spinner-border-sm" style="width:15px;height:15px;color:#94a3b8;" role="status"></div>
        </div>
        <div id="searchDropdown"></div>
    </div>

    <div class="quick-chips">
        <span class="qc-label">Recent:</span>
        @foreach($recentMembers as $rm)
        <a href="{{ route('reports.index', ['member_id' => $rm->id]) }}" class="qc-chip">
            {{ $rm->name_with_initials }}
        </a>
        @endforeach
    </div>
</div>

{{-- ─── EMPTY STATE ──────────────────────────────────────── --}}
@if(!$member)
<div class="empty-state">
    <div class="empty-icon">🔍</div>
    <h3>No Member Selected</h3>
    <p>Use the search above to find a member by name, NIC number, or phone number. Their complete profile will appear here.</p>
</div>
@endif

{{-- ─── MEMBER REPORT ─────────────────────────────────────── --}}
@if($member)

{{-- PROFILE HERO --}}
<div class="profile-hero">
    <div class="hero-inner">
        <div class="hero-avatar">{{ $member->initials }}</div>

        <div class="hero-info">
            <h2 class="hero-name">{{ $member->name_with_initials }}</h2>
            <p class="hero-sub">{{ $member->occupation }} &middot; {{ $member->current_city }}</p>
            <div class="hero-tags">
                @if($member->membership_number)
                <span class="htag gold"><i class="bi bi-hash"></i> {{ $member->membership_number }}</span>
                @endif
                <span class="htag">
                    <i class="bi bi-{{ $member->gender === 'Male' ? 'gender-male' : 'gender-female' }}"></i>
                    {{ $member->gender }}
                </span>
                <span class="htag">{{ $member->married_label }}</span>
                <span class="htag">Age {{ $member->age }}</span>
                <span class="htag"><i class="bi bi-telephone"></i> {{ $member->phone_number }}</span>
                @if($member->email)
                <span class="htag"><i class="bi bi-envelope"></i> {{ $member->email }}</span>
                @endif
            </div>
        </div>

        <div class="hero-stats-strip">
            <div class="hss-cell">
                <div class="hss-label">Reg. Balance</div>
                <div class="hss-val {{ $financials['reg_balance'] > 0 ? 'red' : 'green' }}">
                    Rs {{ number_format($financials['reg_balance'], 0) }}
                </div>
            </div>
            <div class="hss-cell">
                <div class="hss-label">Monthly Due</div>
                <div class="hss-val {{ $financials['monthly_balance'] > 0 ? 'red' : 'green' }}">
                    Rs {{ number_format($financials['monthly_balance'], 0) }}
                </div>
            </div>
            <div class="hss-cell">
                <div class="hss-label">Donated</div>
                <div class="hss-val gold">
                    Rs {{ number_format($financials['donation_total'], 0) }}
                </div>
            </div>
        </div>
    </div>

    <div class="hero-actions">
        <a href="{{ route('members.edit', $member) }}" class="hbtn primary">
            <i class="bi bi-pencil-fill"></i> Edit Profile
        </a>
        <a href="{{ route('registration-payments.create', ['member_id' => $member->id]) }}" class="hbtn">
            <i class="bi bi-credit-card"></i> Reg. Payment
        </a>
        <a href="{{ route('monthly-payments.create') }}" class="hbtn">
            <i class="bi bi-calendar2-check"></i> Monthly Fee
        </a>
        <a href="{{ route('donations.create') }}" class="hbtn">
            <i class="bi bi-gift"></i> Donation
        </a>
    </div>
</div>

{{-- FINANCIAL SUMMARY BAR --}}
<div class="fin-bar">
    {{-- Reg payment --}}
    <div class="fin-card">
        <div class="ficon" style="background:#e8ecf5;color:#1e3a5f;">
            <i class="bi bi-credit-card-fill"></i>
        </div>
        <div>
            <div class="fin-label">Reg. Paid</div>
            <div class="fin-val">Rs {{ number_format($financials['reg_paid'], 0) }}</div>
            <div class="fin-sub">of Rs {{ number_format($financials['reg_fee'], 0) }}</div>
        </div>
    </div>

    {{-- Monthly balance --}}
    <div class="fin-card">
        <div class="ficon" style="background:{{ $financials['monthly_balance'] > 0 ? '#fee2e2' : '#dcfce7' }};color:{{ $financials['monthly_balance'] > 0 ? '#b91c1c' : '#15803d' }};">
            <i class="bi bi-calendar2-check-fill"></i>
        </div>
        <div>
            <div class="fin-label">Monthly Balance</div>
            <div class="fin-val" style="color:{{ $financials['monthly_balance'] > 0 ? '#b91c1c' : '#15803d' }};">
                Rs {{ number_format($financials['monthly_balance'], 0) }}
            </div>
            <div class="fin-sub">{{ $financials['monthly_count'] }} month records</div>
        </div>
    </div>

    {{-- Donations --}}
    <div class="fin-card">
        <div class="ficon" style="background:#ccfbf1;color:#0f766e;">
            <i class="bi bi-gift-fill"></i>
        </div>
        <div>
            <div class="fin-label">Total Donated</div>
            <div class="fin-val" style="color:#0f766e;">Rs {{ number_format($financials['donation_total'], 0) }}</div>
            <div class="fin-sub">{{ $financials['donation_count'] }} donation(s)</div>
        </div>
    </div>

    {{-- Grand total --}}
    <div class="fin-card" style="background:linear-gradient(135deg,var(--navy),var(--navy-lt));border-color:transparent;">
        <div class="ficon" style="background:rgba(201,168,76,.2);color:var(--gold);">
            <i class="bi bi-cash-coin"></i>
        </div>
        <div>
            <div class="fin-label" style="color:#7a9bc0;">Total Contribution</div>
            <div class="fin-val" style="color:#fff;">Rs {{ number_format($financials['total_contribution'], 0) }}</div>
            <div class="fin-sub" style="color:#7a9bc0;">All payments combined</div>
        </div>
    </div>
</div>

{{-- TAB NAV --}}
<div class="tab-nav">
    <button class="tab-btn active" id="btn-overview" onclick="showTab('overview')">
        <i class="bi bi-person-lines-fill"></i> Overview
    </button>
    <button class="tab-btn" id="btn-monthly" onclick="showTab('monthly')">
        <i class="bi bi-calendar2-check"></i> Monthly Payments
        <span class="tab-count">{{ $financials['monthly_count'] }}</span>
    </button>
    <button class="tab-btn" id="btn-donations" onclick="showTab('donations')">
        <i class="bi bi-gift"></i> Donations
        <span class="tab-count">{{ $financials['donation_count'] }}</span>
    </button>
</div>

{{-- ══════════════════════════════════════════════════════
     TAB: OVERVIEW
══════════════════════════════════════════════════════ --}}
<div class="tab-pane active" id="pane-overview">
<div class="row g-3">

    {{-- LEFT col --}}
    <div class="col-lg-7">

        {{-- Personal Information --}}
        <div class="obs-card">
            <div class="card-head">
                <h5 class="card-title"><i class="bi bi-person-badge-fill"></i> Personal Information</h5>
            </div>
            <div class="det-grid">
                <div class="det-cell">
                    <div class="det-label">Full Name</div>
                    <div class="det-value">{{ $member->name_with_initials }}</div>
                </div>
                <div class="det-cell">
                    <div class="det-label">NIC Number</div>
                    <div class="det-value mono">{{ $member->nic_number }}</div>
                </div>
                <div class="det-cell">
                    <div class="det-label">Birthday</div>
                    <div class="det-value">{{ $member->birthday->format('d F Y') }}</div>
                </div>
                <div class="det-cell">
                    <div class="det-label">Age</div>
                    <div class="det-value">{{ $member->age }} years</div>
                </div>
                <div class="det-cell">
                    <div class="det-label">Gender</div>
                    <div class="det-value">{{ $member->gender }}</div>
                </div>
                <div class="det-cell">
                    <div class="det-label">Marital Status</div>
                    <div class="det-value">{{ $member->married_label }}</div>
                </div>
                <div class="det-cell">
                    <div class="det-label">Phone</div>
                    <div class="det-value">{{ $member->phone_number }}</div>
                </div>
                <div class="det-cell">
                    <div class="det-label">Email</div>
                    <div class="det-value {{ $member->email ? '' : 'nil' }}">{{ $member->email ?: 'Not provided' }}</div>
                </div>
                <div class="det-cell">
                    <div class="det-label">Occupation</div>
                    <div class="det-value">{{ $member->occupation }}</div>
                </div>
                <div class="det-cell">
                    <div class="det-label">Current City</div>
                    <div class="det-value">{{ $member->current_city }}</div>
                </div>
                <div class="det-cell" style="grid-column:1/-1">
                    <div class="det-label">Address</div>
                    <div class="det-value">{{ $member->address }}</div>
                </div>
            </div>
        </div>

        {{-- Electoral --}}
        <div class="obs-card">
            <div class="card-head">
                <h5 class="card-title"><i class="bi bi-geo-alt-fill"></i> Electoral & Administrative</h5>
            </div>
            <div class="det-grid">
                <div class="det-cell"><div class="det-label">District</div><div class="det-value">{{ $member->district }}</div></div>
                <div class="det-cell"><div class="det-label">Election Division</div><div class="det-value">{{ $member->election_division }}</div></div>
                <div class="det-cell"><div class="det-label">Grama Niladhari Div.</div><div class="det-value">{{ $member->grama_niladhari_division }}</div></div>
            </div>
        </div>

        {{-- School --}}
        <div class="obs-card">
            <div class="card-head">
                <h5 class="card-title"><i class="bi bi-mortarboard-fill"></i> School Information</h5>
            </div>
            <div class="det-grid">
                <div class="det-cell"><div class="det-label">Register Year</div><div class="det-value {{ $member->school_register_year ? '' : 'nil' }}">{{ $member->school_register_year ?? 'Not recorded' }}</div></div>
                <div class="det-cell"><div class="det-label">Admission No.</div><div class="det-value {{ $member->admission_number ? '' : 'nil' }}">{{ $member->admission_number ?? 'Not recorded' }}</div></div>
                <div class="det-cell"><div class="det-label">Date Joined School</div><div class="det-value {{ $member->date_joined_school ? '' : 'nil' }}">{{ $member->date_joined_school ? $member->date_joined_school->format('d M Y') : 'Not recorded' }}</div></div>
            </div>
        </div>

    </div>

    {{-- RIGHT col --}}
    <div class="col-lg-5">

        {{-- Registration Payment --}}
        <div class="obs-card">
            <div class="card-head">
                <h5 class="card-title"><i class="bi bi-credit-card-fill"></i> Registration Payment</h5>
                @if($member->registrationPayment)
                <a href="{{ route('registration-payments.show', $member->registrationPayment) }}" class="card-link">
                    View <i class="bi bi-arrow-right"></i>
                </a>
                @else
                <a href="{{ route('registration-payments.create', ['member_id' => $member->id]) }}" class="card-link" style="color:var(--teal);">
                    <i class="bi bi-plus-circle me-1"></i>Record
                </a>
                @endif
            </div>

            <div class="reg-cols">
                <div class="reg-col">
                    <div class="reg-col-label">Total Fee</div>
                    <div class="reg-col-val" style="color:var(--navy);">Rs {{ number_format($financials['reg_fee'], 0) }}</div>
                </div>
                <div class="reg-col">
                    <div class="reg-col-label">Paid</div>
                    <div class="reg-col-val" style="color:var(--green);">Rs {{ number_format($financials['reg_paid'], 0) }}</div>
                </div>
                <div class="reg-col">
                    <div class="reg-col-label">Balance</div>
                    <div class="reg-col-val" style="color:{{ $financials['reg_balance'] > 0 ? 'var(--red)' : 'var(--green)' }};">Rs {{ number_format($financials['reg_balance'], 0) }}</div>
                </div>
            </div>

            <div class="reg-progress">
                <div class="rp-track">
                    <div class="rp-fill" style="
                        width:{{ $financials['reg_pct'] }}%;
                        background:{{ $financials['reg_pct'] >= 100 ? 'var(--green)' : ($financials['reg_pct'] > 0 ? 'var(--gold)' : 'var(--red)') }};
                    "></div>
                </div>
                <div class="rp-meta">
                    <span>Payment Progress</span>
                    <span style="font-weight:700;">{{ $financials['reg_pct'] }}%</span>
                </div>
                <div style="text-align:center;margin-top:10px;">
                    <span class="pill pill-{{ $financials['reg_status'] }}">
                        {{ $financials['reg_status'] === 'paid' ? 'Fully Paid' : ($financials['reg_status'] === 'partial' ? 'Partial Payment' : 'Not Paid') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Children --}}
        @if(!empty($member->children_info))
        <div class="obs-card">
            <div class="card-head">
                <h5 class="card-title"><i class="bi bi-people-fill"></i> Children</h5>
            </div>
            <div style="padding:12px;">
                @foreach($member->children_info as $child)
                <div class="child-chip">
                    <strong>{{ $child['name'] ?: '—' }}</strong>
                    <span>{{ $child['school'] ?: 'School not provided' }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Siblings --}}
        @if(!$member->married && $member->siblings_info)
        <div class="obs-card">
            <div class="card-head">
                <h5 class="card-title"><i class="bi bi-person-lines-fill"></i> Siblings</h5>
            </div>
            <div style="padding:16px 20px;font-size:13.5px;color:#334155;line-height:1.65;">{{ $member->siblings_info }}</div>
        </div>
        @endif

        {{-- Record metadata --}}
        <div class="obs-card">
            <div class="card-head"><h5 class="card-title"><i class="bi bi-info-circle-fill"></i> Record Info</h5></div>
            <div style="padding:14px 20px;">
                <div style="padding:8px 0;border-bottom:1px solid #f8fafc;">
                    <div class="det-label">Registered On</div>
                    <div class="det-value">{{ $member->created_at->format('d M Y, h:i A') }}</div>
                </div>
                <div style="padding:8px 0;">
                    <div class="det-label">Last Updated</div>
                    <div class="det-value">{{ $member->updated_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

{{-- ══════════════════════════════════════════════════════
     TAB: MONTHLY PAYMENTS
══════════════════════════════════════════════════════ --}}
<div class="tab-pane" id="pane-monthly">
<div class="obs-card">
    <div class="card-head">
        <h5 class="card-title"><i class="bi bi-calendar2-check-fill"></i> Monthly Payment History</h5>
        <a href="{{ route('monthly-payments.create') }}" class="card-link">
            <i class="bi bi-plus-circle me-1"></i>Record Payment
        </a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Fee</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($member->monthlyPayments as $mp)
                <tr>
                    <td><span class="month-tag"><i class="bi bi-calendar2"></i> {{ $mp->month_label }}</span></td>
                    <td style="font-weight:600;">Rs {{ number_format($mp->total_amount, 2) }}</td>
                    <td style="color:var(--green);font-weight:600;">Rs {{ number_format($mp->paid_amount, 2) }}</td>
                    <td style="color:{{ $mp->balance_amount > 0 ? 'var(--red)' : 'var(--green)' }};font-weight:600;">
                        Rs {{ number_format($mp->balance_amount, 2) }}
                    </td>
                    <td>
                        <div class="tbl-prog-wrap">
                            <div class="tbl-prog-track">
                                <div class="tbl-prog-fill" style="
                                    width:{{ $mp->progress_percent }}%;
                                    background:{{ $mp->status === 'paid' ? 'var(--green)' : ($mp->status === 'partial' ? 'var(--gold)' : 'var(--red)') }};
                                "></div>
                            </div>
                            <span style="font-size:11px;color:#94a3b8;">{{ $mp->progress_percent }}%</span>
                        </div>
                    </td>
                    <td><span class="pill pill-{{ $mp->status }}">{{ $mp->status_label }}</span></td>
                    <td style="font-size:12.5px;color:#64748b;">{{ $mp->payment_date?->format('d M Y') ?? '—' }}</td>
                    <td style="font-size:12px;font-family:monospace;color:#64748b;">{{ $mp->receipt_number ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="tbl-empty">
                            <i class="bi bi-calendar2-x"></i>
                            <p>No monthly payment records found for this member.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($member->monthlyPayments->count())
    <div class="tbl-footer">
        <div style="display:flex;gap:24px;flex-wrap:wrap;">
            <div>
                <span class="tf-label">Total Paid: </span>
                <span class="tf-val" style="color:var(--green);"> Rs {{ number_format($financials['monthly_paid'], 2) }}</span>
            </div>
            <div>
                <span class="tf-label">Outstanding Balance: </span>
                <span class="tf-val" style="color:{{ $financials['monthly_balance'] > 0 ? 'var(--red)' : 'var(--green)' }};"> Rs {{ number_format($financials['monthly_balance'], 2) }}</span>
            </div>
        </div>
        <span style="font-size:12px;color:#94a3b8;">{{ $financials['monthly_paid_count'] }} / {{ $financials['monthly_count'] }} months fully paid</span>
    </div>
    @endif
</div>
</div>

{{-- ══════════════════════════════════════════════════════
     TAB: DONATIONS
══════════════════════════════════════════════════════ --}}
<div class="tab-pane" id="pane-donations">
<div class="obs-card">
    <div class="card-head">
        <h5 class="card-title"><i class="bi bi-gift-fill"></i> Donation History</h5>
        <a href="{{ route('donations.create') }}" class="card-link">
            <i class="bi bi-plus-circle me-1"></i>Record Donation
        </a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reason</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Receipt</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($member->donations as $i => $don)
                <tr>
                    <td style="color:#94a3b8;font-size:13px;">{{ $i + 1 }}</td>
                    <td style="max-width:220px;">
                        <span style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:500;" title="{{ $don->reason }}">
                            {{ $don->reason }}
                        </span>
                    </td>
                    <td><span class="don-amount">Rs {{ number_format($don->amount, 2) }}</span></td>
                    <td>
                        <div style="font-size:13.5px;font-weight:500;">{{ $don->donation_date->format('d M Y') }}</div>
                        <div style="font-size:11px;color:#94a3b8;">{{ $don->donation_date->diffForHumans() }}</div>
                    </td>
                    <td><span class="pill pill-{{ $don->status }}">{{ $don->status_label }}</span></td>
                    <td style="font-size:12px;font-family:monospace;color:#64748b;">{{ $don->receipt_number ?? '—' }}</td>
                    <td style="font-size:12.5px;color:#64748b;max-width:160px;">
                        <span style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $don->notes }}">
                            {{ $don->notes ?: '—' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="tbl-empty">
                            <i class="bi bi-gift"></i>
                            <p>No donation records found for this member.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($member->donations->count())
    <div class="tbl-footer">
        <div>
            <span class="tf-label">Total Donated: </span>
            <span class="tf-val" style="color:var(--teal);"> Rs {{ number_format($financials['donation_total'], 2) }}</span>
        </div>
        <span style="font-size:12px;color:#94a3b8;">{{ $financials['donation_count'] }} confirmed donation(s)</span>
    </div>
    @endif
</div>
</div>

@endif {{-- end $member --}}

@endsection

@push('scripts')
<script>
// ════════════════════════════════════════════════════
// TAB SWITCHING
// ════════════════════════════════════════════════════
function showTab(name) {
    ['overview','monthly','donations'].forEach(t => {
        document.getElementById('pane-' + t).classList.remove('active');
        document.getElementById('btn-'  + t).classList.remove('active');
    });
    document.getElementById('pane-' + name).classList.add('active');
    document.getElementById('btn-'  + name).classList.add('active');
}

// ════════════════════════════════════════════════════
// LIVE SEARCH
// ════════════════════════════════════════════════════
const input    = document.getElementById('memberSearch');
const dropdown = document.getElementById('searchDropdown');
const spin     = document.querySelector('.si-spin');
let   timer    = null;

input.addEventListener('input', function () {
    clearTimeout(timer);
    const q = this.value.trim();
    if (!q) { dropdown.style.display = 'none'; return; }

    dropdown.style.display = 'block';
    dropdown.innerHTML     = '<div class="sd-hint"><i class="bi bi-hourglass-split me-1"></i> Searching…</div>';
    spin.style.display     = 'block';

    timer = setTimeout(() => {
        fetch(`{{ route('reports.search') }}?q=${encodeURIComponent(q)}`, {
            headers: { Accept: 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            spin.style.display = 'none';
            if (!data.length) {
                dropdown.innerHTML = '<div class="sd-hint"><i class="bi bi-person-x me-1"></i> No members found</div>';
                return;
            }
            dropdown.innerHTML = data.map(m => `
                <div class="sd-item" onclick="goToMember(${m.id}, '${m.name.replace(/'/g,"\\'")}')">
                    <div class="sd-avatar">${m.initials}</div>
                    <div style="flex:1;min-width:0;">
                        <div class="sd-name">${m.name}</div>
                        <div class="sd-meta">${m.nic} &middot; ${m.phone} &middot; ${m.occupation}</div>
                    </div>
                    ${m.membership_no ? `<span class="sd-badge">#${m.membership_no}</span>` : ''}
                </div>
            `).join('');
        })
        .catch(() => {
            spin.style.display = 'none';
            dropdown.innerHTML = '<div class="sd-hint" style="color:var(--red)"><i class="bi bi-exclamation-circle me-1"></i> Search failed</div>';
        });
    }, 280);
});

document.addEventListener('click', e => {
    if (!e.target.closest('#searchWrap')) dropdown.style.display = 'none';
});

function goToMember(id, name) {
    input.value            = name;
    dropdown.style.display = 'none';
    window.location.href   = `{{ route('reports.index') }}?member_id=${id}`;
}
</script>
@endpush
