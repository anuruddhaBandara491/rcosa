@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* ── STAT CARDS ────────────────────────────────────── */
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 22px 24px;
        border: 1px solid #e4e9f0;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform .25s, box-shadow .25s;
        animation: fadeUp .4s ease both;
        text-decoration: none;
        color: inherit;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(15,31,61,.1);
        color: inherit;
    }
    .stat-card:nth-child(1) { animation-delay:.05s; }
    .stat-card:nth-child(2) { animation-delay:.10s; }
    .stat-card:nth-child(3) { animation-delay:.15s; }
    .stat-card:nth-child(4) { animation-delay:.20s; }
    .stat-card:nth-child(5) { animation-delay:.25s; }

    .stat-icon {
        width:52px; height:52px; border-radius:14px;
        display:flex; align-items:center; justify-content:center;
        font-size:22px; flex-shrink:0;
    }
    .stat-body { flex:1; min-width:0; }
    .stat-label {
        font-size:11.5px; text-transform:uppercase; letter-spacing:1.2px;
        color:#8494a9; font-weight:600; margin-bottom:4px;
    }
    .stat-value {
        font-family:'Playfair Display',serif;
        font-size:26px; font-weight:700; color:#0f1f3d; line-height:1;
    }
    .stat-sub { font-size:11px; color:#8494a9; margin-top:4px; }

    .stat-change { font-size:12px; font-weight:600; padding:3px 8px; border-radius:20px; flex-shrink:0; }
    .stat-change.up     { background:#dcfce7; color:#15803d; }
    .stat-change.down   { background:#fdecea; color:#c0392b; }
    .stat-change.purple { background:#ede9fe; color:#6d28d9; }
    .stat-change.warn   { background:#fef3c7; color:#b45309; }

    /* icon themes */
    .icon-navy   { background:#e8ecf5; color:#0f1f3d; }
    .icon-amber  { background:#fef3dc; color:#c9a84c; }
    .icon-rose   { background:#fde8ea; color:#c0392b; }
    .icon-teal   { background:#ddf5f1; color:#0e9578; }
    .icon-purple { background:#ede9fe; color:#6d28d9; }
    .icon-green  { background:#dcfce7; color:#15803d; }

    /* ── MONTHLY STATUS MINI PILLS ─────────────────────── */
    .mini-pills { display:flex; gap:6px; flex-wrap:wrap; margin-top:10px; }
    .mini-pill {
        font-size:11px; font-weight:600; padding:3px 10px;
        border-radius:20px; white-space:nowrap;
    }
    .pill-paid     { background:#dcfce7; color:#15803d; }
    .pill-partial  { background:#fef3c7; color:#b45309; }
    .pill-overpaid { background:#ede9fe; color:#6d28d9; }
    .pill-never    { background:#fdecea; color:#b91c1c; }

    /* ── SECTION CARD ──────────────────────────────────── */
    .obs-card {
        background:#fff; border-radius:16px; border:1px solid #e4e9f0;
        overflow:hidden; animation:fadeUp .45s ease both; animation-delay:.3s;
    }
    .obs-card-header {
        padding:18px 22px; border-bottom:1px solid #f0f3f8;
        display:flex; align-items:center; justify-content:space-between;
    }
    .obs-card-title {
        font-family:'Playfair Display',serif;
        font-size:15px; font-weight:700; color:#0f1f3d; margin:0;
    }

    /* ── TABLE ─────────────────────────────────────────── */
    .obs-table { width:100%; border-collapse:collapse; }
    .obs-table th {
        font-size:10.5px; text-transform:uppercase; letter-spacing:1.2px;
        color:#8494a9; font-weight:600; padding:10px 16px;
        border-bottom:1px solid #f0f3f8; background:#fafbfd;
    }
    .obs-table td {
        padding:12px 16px; font-size:13.5px; color:#2c3e55;
        border-bottom:1px solid #f6f8fb; vertical-align:middle;
    }
    .obs-table tr:last-child td { border-bottom:none; }
    .obs-table tr:hover td { background:#fafbfd; }

    /* ── STATUS BADGES ─────────────────────────────────── */
    .s-badge { font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; white-space:nowrap; }
    .s-paid     { background:#dcfce7; color:#15803d; }
    .s-partial  { background:#fef3c7; color:#b45309; }
    .s-overpaid { background:#ede9fe; color:#6d28d9; }
    .s-unpaid   { background:#fdecea; color:#b91c1c; }
    .s-pending  { background:#fef3c7; color:#b45309; }

    /* ── MEMBER AVATAR ─────────────────────────────────── */
    .m-avatar {
        width:32px; height:32px; border-radius:8px;
        display:inline-flex; align-items:center; justify-content:center;
        font-size:12px; font-weight:700; margin-right:10px; flex-shrink:0;
        background:linear-gradient(135deg,#1e3a5f,#0f1f3d); color:#c9a84c;
    }

    /* ── INCOME PROGRESS ───────────────────────────────── */
    .income-row { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
    .income-row:last-child { margin-bottom:0; }
    .income-label { font-size:13px; color:#3d5270; min-width:130px; font-weight:500; }
    .income-bar-wrap { flex:1; height:8px; background:#f0f3f8; border-radius:99px; overflow:hidden; }
    .income-bar { height:100%; border-radius:99px; transition:width 1s ease; }
    .income-amount { font-size:13px; font-weight:700; color:#0f1f3d; min-width:90px; text-align:right; }

    /* ── QUICK ACTIONS ─────────────────────────────────── */
    .quick-action {
        display:flex; flex-direction:column; align-items:center;
        justify-content:center; gap:8px; padding:16px 12px;
        border:1.5px dashed #d0d9e8; border-radius:12px;
        color:#5a7194; font-size:12px; font-weight:500;
        text-decoration:none; transition:all .2s; text-align:center;
    }
    .quick-action i { font-size:20px; }
    .quick-action:hover { border-color:#c9a84c; color:#c9a84c; background:#fef9ec; }

    /* ── TRANSACTION ROW ───────────────────────────────── */
    .txn-amount {
        font-family:'Playfair Display',serif;
        font-size:15px; font-weight:700; color:#0f1f3d;
    }

    /* ── ANIMATION ─────────────────────────────────────── */
    @keyframes fadeUp {
        from { opacity:0; transform:translateY(18px); }
        to   { opacity:1; transform:translateY(0); }
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1>Welcome back, {{ auth()->user()->name ?? 'Administrator' }} 👋</h1>
        <p>{{ now()->format('l, d F Y') }} — Here's what's happening with RCOSA today.</p>
    </div>
    <a href="{{ route('members.create') }}" class="btn btn-sm px-4 py-2"
       style="background:#0f1f3d;color:#fff;border-radius:10px;font-size:13px;font-weight:600;">
        <i class="bi bi-plus-circle me-1"></i> Register Member
    </a>
</div>

<!-- ═══ STAT CARDS ══════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Total Members -->
    <div class="col-sm-6 col-xl-4">
        <a href="{{ route('members.index') }}" class="stat-card">
            <div class="stat-icon icon-navy"><i class="bi bi-people-fill"></i></div>
            <div class="stat-body">
                <div class="stat-label">Total Members</div>
                <div class="stat-value">{{ number_format($stats['total_members']) }}</div>
                <div class="stat-sub">Registered &amp; Active</div>
            </div>
        </a>
    </div>

    <!-- Pending Registration -->
    <div class="col-sm-6 col-xl-4">
        <a href="{{ route('registration-payments.index') }}" class="stat-card">
            <div class="stat-icon icon-amber"><i class="bi bi-credit-card-fill"></i></div>
            <div class="stat-body">
                <div class="stat-label">Pending Reg. Payments</div>
                <div class="stat-value">{{ number_format($stats['pending_registration']) }}</div>
                <div class="stat-sub">Members awaiting payment</div>
            </div>
            @if($stats['pending_registration'] > 0)
                <span class="stat-change warn"><i class="bi bi-exclamation-circle"></i> Action needed</span>
            @endif
        </a>
    </div>

    <!-- Monthly Outstanding -->
    <div class="col-sm-6 col-xl-4">
        <a href="{{ route('monthly-payments.index') }}" class="stat-card">
            <div class="stat-icon icon-rose"><i class="bi bi-calendar2-x-fill"></i></div>
            <div class="stat-body">
                <div class="stat-label">Monthly Outstanding</div>
                <div class="stat-value">Rs {{ number_format($stats['monthly_outstanding'], 0) }}</div>
                <div class="stat-sub">Total balance across all members</div>
            </div>
            @if($stats['monthly_outstanding'] > 0)
                <span class="stat-change down"><i class="bi bi-exclamation-circle"></i></span>
            @endif
        </a>
    </div>

    <!-- Donations -->
    <div class="col-sm-6 col-xl-6">
        <a href="{{ route('donations.index') }}" class="stat-card">
            <div class="stat-icon icon-teal"><i class="bi bi-gift-fill"></i></div>
            <div class="stat-body">
                <div class="stat-label">Total Donations</div>
                <div class="stat-value">Rs {{ number_format($stats['total_donations'], 0) }}</div>
                <div class="stat-sub">All-time received</div>
            </div>
        </a>
    </div>

    <!-- Total Income -->
    <div class="col-sm-12 col-xl-6">
        <div class="stat-card" style="background:linear-gradient(135deg,#0f1f3d,#1e3a5f);border-color:transparent;">
            <div class="stat-icon" style="background:rgba(201,168,76,.2);color:#c9a84c;">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label" style="color:#7a9abc;">Total Income</div>
                <div class="stat-value" style="color:#fff;">Rs {{ number_format($stats['total_income'], 0) }}</div>
                <div class="stat-sub" style="color:#7a9abc;">Registration + Monthly + Donations</div>
            </div>
        </div>
    </div>

</div>

<!-- ═══ MONTHLY FEE STATUS SUMMARY ══════════════════════ -->
<div class="obs-card mb-4" style="animation-delay:.22s;">
    <div class="obs-card-header">
        <h3 class="obs-card-title">
            <i class="bi bi-calendar2-check me-2" style="color:#c9a84c;"></i>
            Monthly Fee Overview
        </h3>
        <a href="{{ route('monthly-payments.create') }}" class="btn btn-sm"
           style="background:#0f1f3d;color:#fff;border-radius:8px;font-size:12px;font-weight:600;border:none;padding:6px 14px;">
            <i class="bi bi-plus me-1"></i> Record Payment
        </a>
    </div>
    <div style="padding:20px 22px;">
        <div class="row g-3">

            <!-- Collected -->
            <div class="col-sm-6 col-lg-3">
                <div style="background:#f8faff;border:1px solid #e8ecf5;border-radius:12px;padding:16px 18px;">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:1.1px;color:#8494a9;font-weight:600;margin-bottom:6px;">
                        <i class="bi bi-cash-stack me-1" style="color:#c9a84c;"></i> Collected
                    </div>
                    <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#0f1f3d;">
                        Rs {{ number_format($stats['monthly_income'], 0) }}
                    </div>
                    <div style="font-size:11px;color:#8494a9;margin-top:3px;">Total received</div>
                </div>
            </div>

            <!-- Outstanding -->
            <div class="col-sm-6 col-lg-3">
                <div style="background:#fff8f8;border:1px solid #fee2e2;border-radius:12px;padding:16px 18px;">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:1.1px;color:#8494a9;font-weight:600;margin-bottom:6px;">
                        <i class="bi bi-exclamation-circle me-1" style="color:#b91c1c;"></i> Outstanding
                    </div>
                    <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#b91c1c;">
                        Rs {{ number_format($stats['monthly_outstanding'], 0) }}
                    </div>
                    <div style="font-size:11px;color:#8494a9;margin-top:3px;">Still owed</div>
                </div>
            </div>

            <!-- Fully Paid transactions -->
            <div class="col-sm-6 col-lg-3">
                <div style="background:#f0fdf4;border:1px solid #dcfce7;border-radius:12px;padding:16px 18px;">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:1.1px;color:#8494a9;font-weight:600;margin-bottom:6px;">
                        <i class="bi bi-check-circle me-1" style="color:#15803d;"></i> Paid Txns
                    </div>
                    <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#15803d;">
                        {{ number_format($stats['count_paid']) }}
                    </div>
                    <div style="font-size:11px;color:#8494a9;margin-top:3px;">Fully settled</div>
                </div>
            </div>

            <!-- Partial + Never paid -->
            <div class="col-sm-6 col-lg-3">
                <div style="background:#fffbeb;border:1px solid #fef3c7;border-radius:12px;padding:16px 18px;">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:1.1px;color:#8494a9;font-weight:600;margin-bottom:6px;">
                        <i class="bi bi-hourglass-split me-1" style="color:#b45309;"></i> Needs Attention
                    </div>
                    <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#b45309;">
                        {{ number_format($stats['count_partial'] + $stats['count_never_paid']) }}
                    </div>
                    <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:5px;">
                        <span class="mini-pill pill-partial">{{ $stats['count_partial'] }} partial</span>
                        <span class="mini-pill pill-never">{{ $stats['count_never_paid'] }} never paid</span>
                        @if($stats['count_overpaid'] > 0)
                            <span class="mini-pill pill-overpaid">{{ $stats['count_overpaid'] }} overpaid</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ═══ BOTTOM ROW ═══════════════════════════════════════ -->
<div class="row g-3">

    <!-- Recent Members -->
    <div class="col-lg-7">
        <div class="obs-card">
            <div class="obs-card-header">
                <h3 class="obs-card-title">
                    <i class="bi bi-people me-2" style="color:#c9a84c;"></i>Recent Members
                </h3>
                <a href="{{ route('members.index') }}" class="btn btn-sm"
                   style="background:#f4f6fb;color:#0f1f3d;border-radius:8px;font-size:12px;font-weight:600;border:1px solid #e4e9f0;">
                    View All
                </a>
            </div>
            <div class="table-responsive">
                <table class="obs-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Batch</th>
                            <th>Reg. Fee</th>
                            <th>Monthly Status</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMembers as $member)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="m-avatar">{{ strtoupper(substr($member->name_with_initials ?? '?', 0, 1)) }}</div>
                                    <div>
                                        <div style="font-weight:600;font-size:13.5px;">{{ $member->name_with_initials }}</div>
                                        <div style="font-size:11px;color:#8494a9;">{{ $member->nic_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight:600;color:#1e3a5f;">{{ $member->school_register_year }}</td>
                            <td>
                                <span class="s-badge s-{{ $member->reg_status === 'paid' ? 'paid' : 'pending' }}">
                                    {{ ucfirst($member->reg_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="s-badge s-{{ $member->monthly_status }}">
                                    {{ ucfirst($member->monthly_status) }}
                                </span>
                            </td>
                            <td style="font-weight:700;color:{{ $member->monthly_balance > 0 ? '#b91c1c' : '#15803d' }};">
                                Rs {{ number_format($member->monthly_balance, 0) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color:#8494a9;font-size:13px;">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i> No members found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-5 d-flex flex-column gap-3">

        <!-- Recent Monthly Transactions -->
        <div class="obs-card">
            <div class="obs-card-header">
                <h3 class="obs-card-title">
                    <i class="bi bi-receipt me-2" style="color:#c9a84c;"></i>Recent Transactions
                </h3>
                <a href="{{ route('monthly-payments.index') }}" class="btn btn-sm"
                   style="background:#f4f6fb;color:#0f1f3d;border-radius:8px;font-size:12px;font-weight:600;border:1px solid #e4e9f0;">
                    View All
                </a>
            </div>
            <div class="table-responsive">
                <table class="obs-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Paid</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMonthlyPayments as $txn)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="m-avatar">{{ strtoupper(substr($txn->member->name_with_initials ?? '?', 0, 1)) }}</div>
                                    <div style="font-weight:600;font-size:13px;">
                                        {{ Str::limit($txn->member->name_with_initials ?? '—', 18) }}
                                    </div>
                                </div>
                            </td>
                            <td class="txn-amount">Rs {{ number_format($txn->paid_amount, 0) }}</td>
                            <td><span class="s-badge s-{{ $txn->status }}">{{ $txn->status_label }}</span></td>
                            <td style="font-size:12px;color:#5a7194;white-space:nowrap;">
                                {{ $txn->payment_date?->format('d M Y') ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4" style="color:#8494a9;font-size:13px;">
                                <i class="bi bi-calendar2-x fs-4 d-block mb-1"></i> No transactions yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Income Breakdown -->
        <div class="obs-card">
            <div class="obs-card-header">
                <h3 class="obs-card-title">
                    <i class="bi bi-bar-chart me-2" style="color:#c9a84c;"></i>Income Breakdown
                </h3>
            </div>
            <div style="padding:20px 22px;">
                @php
                    $totalIncome = $stats['total_income'] ?: 1;
                    $regPct      = round($stats['registration_income'] / $totalIncome * 100);
                    $monthlyPct  = round($stats['monthly_income']      / $totalIncome * 100);
                    $donPct      = round($stats['total_donations']      / $totalIncome * 100);
                @endphp

                <div class="income-row">
                    <div class="income-label"><i class="bi bi-credit-card me-1" style="color:#c9a84c;"></i> Registration</div>
                    <div class="income-bar-wrap"><div class="income-bar" style="width:{{ $regPct }}%;background:#c9a84c;"></div></div>
                    <div class="income-amount">Rs {{ number_format($stats['registration_income'], 0) }}</div>
                </div>
                <div class="income-row">
                    <div class="income-label"><i class="bi bi-calendar2-check me-1" style="color:#1e88e5;"></i> Monthly</div>
                    <div class="income-bar-wrap"><div class="income-bar" style="width:{{ $monthlyPct }}%;background:#1e88e5;"></div></div>
                    <div class="income-amount">Rs {{ number_format($stats['monthly_income'], 0) }}</div>
                </div>
                <div class="income-row">
                    <div class="income-label"><i class="bi bi-gift me-1" style="color:#0e9578;"></i> Donations</div>
                    <div class="income-bar-wrap"><div class="income-bar" style="width:{{ $donPct }}%;background:#0e9578;"></div></div>
                    <div class="income-amount">Rs {{ number_format($stats['total_donations'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
