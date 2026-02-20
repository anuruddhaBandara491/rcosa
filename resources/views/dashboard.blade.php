@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* ── STAT CARDS ──────────────────────────────────── */
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
    .stat-card:nth-child(1) { animation-delay: .05s; }
    .stat-card:nth-child(2) { animation-delay: .10s; }
    .stat-card:nth-child(3) { animation-delay: .15s; }
    .stat-card:nth-child(4) { animation-delay: .20s; }
    .stat-card:nth-child(5) { animation-delay: .25s; }

    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .stat-body { flex: 1; min-width: 0; }
    .stat-label {
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #8494a9;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .stat-value {
        font-family: 'Playfair Display', serif;
        font-size: 26px;
        font-weight: 700;
        color: #0f1f3d;
        line-height: 1;
    }
    .stat-sub {
        font-size: 11px;
        color: #8494a9;
        margin-top: 4px;
    }
    .stat-change {
        font-size: 12px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 20px;
    }
    .stat-change.up   { background: #e8f7ee; color: #1a8a45; }
    .stat-change.down { background: #fdecea; color: #c0392b; }

    /* colour themes */
    .icon-navy   { background: #e8ecf5; color: #0f1f3d; }
    .icon-amber  { background: #fef3dc; color: #c9a84c; }
    .icon-rose   { background: #fde8ea; color: #c0392b; }
    .icon-teal   { background: #ddf5f1; color: #0e9578; }
    .icon-indigo { background: #eae8fd; color: #5244e8; }

    /* ── SECTION CARD ────────────────────────────────── */
    .obs-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e4e9f0;
        overflow: hidden;
        animation: fadeUp .45s ease both;
        animation-delay: .3s;
    }
    .obs-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #f0f3f8;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .obs-card-title {
        font-family: 'Playfair Display', serif;
        font-size: 15px;
        font-weight: 700;
        color: #0f1f3d;
        margin: 0;
    }

    /* ── TABLE ───────────────────────────────────────── */
    .obs-table { width: 100%; border-collapse: collapse; }
    .obs-table th {
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #8494a9;
        font-weight: 600;
        padding: 10px 16px;
        border-bottom: 1px solid #f0f3f8;
        background: #fafbfd;
    }
    .obs-table td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #2c3e55;
        border-bottom: 1px solid #f6f8fb;
        vertical-align: middle;
    }
    .obs-table tr:last-child td { border-bottom: none; }
    .obs-table tr:hover td { background: #fafbfd; }

    /* ── BADGES ──────────────────────────────────────── */
    .status-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .badge-paid    { background: #e8f7ee; color: #1a8a45; }
    .badge-pending { background: #fff3d6; color: #b07d10; }
    .badge-overdue { background: #fdecea; color: #c0392b; }

    /* ── MEMBER AVATAR ───────────────────────────────── */
    .m-avatar {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700;
        margin-right: 10px;
        background: linear-gradient(135deg, #1e3a5f, #0f1f3d);
        color: #c9a84c;
        flex-shrink: 0;
    }

    /* ── QUICK ACTION ────────────────────────────────── */
    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 18px 12px;
        border: 1.5px dashed #d0d9e8;
        border-radius: 12px;
        color: #5a7194;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        transition: all .2s;
        text-align: center;
    }
    .quick-action i { font-size: 20px; }
    .quick-action:hover {
        border-color: #c9a84c;
        color: #c9a84c;
        background: #fef9ec;
    }

    /* ── INCOME PROGRESS ─────────────────────────────── */
    .income-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }
    .income-row:last-child { margin-bottom: 0; }
    .income-label { font-size: 13px; color: #3d5270; min-width: 140px; font-weight: 500; }
    .income-bar-wrap { flex: 1; height: 8px; background: #f0f3f8; border-radius: 99px; overflow: hidden; }
    .income-bar { height: 100%; border-radius: 99px; transition: width 1s ease; }
    .income-amount { font-size: 13px; font-weight: 700; color: #0f1f3d; min-width: 90px; text-align: right; }

    /* ── ANIMATION ───────────────────────────────────── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1>Welcome back, {{ auth()->user()->name ?? 'Administrator' }} 👋</h1>
        <p>{{ now()->format('l, d F Y') }} — Here's what's happening with the Old Boys Society today.</p>
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
            <span class="stat-change up"><i class="bi bi-arrow-up-short"></i>12%</span>
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
            <span class="stat-change down"><i class="bi bi-exclamation-circle"></i></span>
        </a>
    </div>

    <!-- Monthly Fees Pending -->
    <div class="col-sm-6 col-xl-4">
        <a href="{{ route('monthly-payments.index') }}" class="stat-card">
            <div class="stat-icon icon-rose"><i class="bi bi-calendar2-x-fill"></i></div>
            <div class="stat-body">
                <div class="stat-label">Monthly Fees Pending</div>
                <div class="stat-value">Rs {{ number_format($stats['monthly_fees_pending'], 0) }}</div>
                <div class="stat-sub">Outstanding this month</div>
            </div>
            <span class="stat-change down"><i class="bi bi-exclamation-circle"></i></span>
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
            <span class="stat-change up"><i class="bi bi-arrow-up-short"></i>8%</span>
        </a>
    </div>

    <!-- Total Income -->
    <div class="col-sm-12 col-xl-6">
        <div class="stat-card" style="background:linear-gradient(135deg,#0f1f3d 0%,#1e3a5f 100%);border-color:transparent;">
            <div class="stat-icon" style="background:rgba(201,168,76,.2);color:#c9a84c;">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div class="stat-body">
                <div class="stat-label" style="color:#7a9abc;">Total Income</div>
                <div class="stat-value" style="color:#fff;">Rs {{ number_format($stats['total_income'], 0) }}</div>
                <div class="stat-sub" style="color:#7a9abc;">Registration + Monthly + Donations</div>
            </div>
            <span class="stat-change up"><i class="bi bi-arrow-up-short"></i>21%</span>
        </div>
    </div>

</div>

<!-- ═══ BOTTOM ROW ══════════════════════════════════════ -->
<div class="row g-3">

    <!-- Recent Members -->
    <div class="col-lg-7">
        <div class="obs-card">
            <div class="obs-card-header">
                <h3 class="obs-card-title"><i class="bi bi-people me-2" style="color:#c9a84c;"></i>Recent Members</h3>
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
                            <th>Reg. Payment</th>
                            <th>Monthly</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentMembers as $member)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="m-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                    <div>
                                        <div style="font-weight:600;font-size:13.5px;">{{ $member->name }}</div>
                                        <div style="font-size:11px;color:#8494a9;">{{ $member->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight:600;color:#1e3a5f;">{{ $member->batch_year }}</td>
                            <td>
                                <span class="status-badge {{ $member->reg_status === 'paid' ? 'badge-paid' : ($member->reg_status === 'pending' ? 'badge-pending' : 'badge-overdue') }}">
                                    {{ ucfirst($member->reg_status ?? 'Pending') }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $member->monthly_status === 'paid' ? 'badge-paid' : 'badge-pending' }}">
                                    {{ ucfirst($member->monthly_status ?? 'Pending') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4" style="color:#8494a9;font-size:13px;">
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

        <!-- Income Breakdown -->
        <div class="obs-card">
            <div class="obs-card-header">
                <h3 class="obs-card-title"><i class="bi bi-bar-chart me-2" style="color:#c9a84c;"></i>Income Breakdown</h3>
            </div>
            <div style="padding:20px 22px;">
                @php
                    $totalIncome = $stats['total_income'] ?: 1;
                    $regPct     = round($stats['registration_income'] / $totalIncome * 100);
                    $monthlyPct = round($stats['monthly_income'] / $totalIncome * 100);
                    $donPct     = round($stats['total_donations'] / $totalIncome * 100);
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
