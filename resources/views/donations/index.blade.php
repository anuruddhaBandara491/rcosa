@extends('layouts.app')

@section('title', 'Donations')
@section('page-title', 'Donations')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; --teal:#0e9578; --surface:#f4f6fb; }

/* ── HERO STATS ──────────────────────────────────────── */
.stat-hero {
    background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 60%, #0e3a5f 100%);
    border-radius:20px;
    padding:28px 30px;
    margin-bottom:22px;
    display:grid;
    grid-template-columns:1fr 1px 1fr 1px 1fr 1px 1fr;
    align-items:center;
    gap:0;
    animation:fadeUp .35s ease;
    position:relative;
    overflow:hidden;
}
.stat-hero::before {
    content:'';
    position:absolute;
    top:-40px; right:-40px;
    width:200px; height:200px;
    background:rgba(201,168,76,.08);
    border-radius:50%;
}
.stat-hero::after {
    content:'';
    position:absolute;
    bottom:-60px; left:30%;
    width:300px; height:300px;
    background:rgba(201,168,76,.04);
    border-radius:50%;
}
.hero-divider { width:1px; background:rgba(255,255,255,.1); height:50px; align-self:center; }
.hero-stat { padding:0 28px; text-align:center; position:relative; z-index:1; }
.hero-stat:first-child { text-align:left; padding-left:4px; }
.hero-icon { font-size:18px; margin-bottom:6px; display:block; }
.hero-label { font-size:10.5px; text-transform:uppercase; letter-spacing:1.4px; color:#7a9abc; font-weight:600; margin-bottom:6px; }
.hero-val { font-family:'Playfair Display',serif; font-size:26px; font-weight:700; color:#fff; line-height:1; }
.hero-val.gold { color:var(--gold); }
.hero-sub { font-size:11px; color:#7a9abc; margin-top:4px; }

@media(max-width:767px) {
    .stat-hero { grid-template-columns:1fr 1fr; gap:16px; }
    .hero-divider { display:none; }
    .hero-stat { border-bottom:1px solid rgba(255,255,255,.07); padding:12px 0; text-align:left; }
    .hero-stat:last-child { border-bottom:none; }
}

/* ── FILTER CARD ─────────────────────────────────────── */
.filter-card {
    background:#fff; border:1px solid #e4e9f0;
    border-radius:14px; padding:18px 20px;
    margin-bottom:20px; animation:fadeUp .3s ease;
}
.filter-title { font-size:11px; text-transform:uppercase; letter-spacing:1.2px; color:#8494a9; font-weight:600; margin-bottom:12px; display:flex; align-items:center; gap:6px; }
.form-control, .form-select { border-radius:10px; border:1px solid #dde3ef; font-size:13.5px; padding:8px 12px; color:#1a2b44; }
.form-control:focus, .form-select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.12); outline:none; }
.search-wrap { position:relative; }
.search-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#8494a9; font-size:14px; pointer-events:none; }
.search-wrap input { padding-left:34px; }

.btn-filter { background:var(--navy); color:#fff; border:none; border-radius:10px; padding:9px 18px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
.btn-filter:hover { background:#1e3a5f; }
.btn-clear { background:var(--surface); color:#5a7194; border:1px solid #dde3ef; border-radius:10px; padding:9px 14px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-clear:hover { background:#e4e9f0; color:var(--navy); }
.btn-new { background:var(--teal); color:#fff; border:none; border-radius:10px; padding:9px 18px; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
.btn-new:hover { background:#0b7a61; color:#fff; }

.date-range-chip {
    display:inline-flex; align-items:center; gap:6px;
    background:rgba(14,149,120,.1); color:#0e9578;
    border:1px solid rgba(14,149,120,.2);
    padding:5px 12px; border-radius:20px;
    font-size:12px; font-weight:600;
}

/* ── TABLE ───────────────────────────────────────────── */
.table-card { background:#fff; border:1px solid #e4e9f0; border-radius:14px; overflow:hidden; animation:fadeUp .4s ease; }
.table-card-header { padding:16px 22px; border-bottom:1px solid #f0f3f8; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.tc-title { font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:var(--navy); margin:0; }

.obs-table { width:100%; border-collapse:collapse; }
.obs-table th { font-size:10.5px; text-transform:uppercase; letter-spacing:1.2px; color:#8494a9; font-weight:600; padding:12px 18px; border-bottom:1px solid #f0f3f8; background:#fafbfd; white-space:nowrap; }
.obs-table td { padding:14px 18px; font-size:13.5px; color:#2c3e55; border-bottom:1px solid #f6f8fb; vertical-align:middle; }
.obs-table tr:last-child td { border-bottom:none; }
.obs-table tbody tr { transition:background .12s; }
.obs-table tbody tr:hover td { background:#f9fafb; }

.m-avatar { width:34px; height:34px; border-radius:10px; background:linear-gradient(135deg,#1e3a5f,#0f1f3d); color:var(--gold); display:inline-flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; margin-right:10px; flex-shrink:0; }

.reason-text { max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

.amount-chip { font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:var(--teal); }

.s-pill { font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; white-space:nowrap; }
.s-received { background:#ddf5f1; color:#0e7a61; }
.s-pending  { background:#fff3d6; color:#b07d10; }

.action-btn { width:30px; height:30px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:13px; border:1px solid #e4e9f0; background:#f4f6fb; color:#3d5270; text-decoration:none; transition:all .15s; }
.action-btn:hover { transform:scale(1.12); }
.action-btn.view:hover { background:#ddf5f1; color:#0e9578; border-color:#b2e7de; }
.action-btn.edit:hover { background:#fef3dc; color:#c9a84c; border-color:#f5dfa0; }
.action-btn.del:hover  { background:#fdecea; color:#c0392b; border-color:#f5c0bc; }

.empty-state { text-align:center; padding:56px 20px; }
.empty-state i { font-size:48px; display:block; margin-bottom:14px; color:var(--gold); opacity:.35; }
.empty-state h5 { font-family:'Playfair Display',serif; font-size:16px; color:var(--navy); margin-bottom:6px; }
.empty-state p  { font-size:13px; color:#8494a9; margin:0; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h1>Donations</h1>
        <p>Track all member contributions to the Old Boys Society.</p>
    </div>
    <a href="{{ route('donations.create') }}" class="btn-new">
        <i class="bi bi-gift-fill"></i> Record Donation
    </a>
</div>

{{-- ── HERO STATS ──────────────────────────────────────── --}}
<div class="stat-hero">
    <div class="hero-stat">
        <span class="hero-icon">🏆</span>
        <div class="hero-label">All-Time Donations</div>
        <div class="hero-val gold">Rs {{ number_format($stats['total_all_time'], 0) }}</div>
        <div class="hero-sub">{{ $stats['total_count'] }} total donations</div>
    </div>
    <div class="hero-divider"></div>
    <div class="hero-stat">
        <span class="hero-icon">📅</span>
        <div class="hero-label">This Year</div>
        <div class="hero-val">Rs {{ number_format($stats['total_this_year'], 0) }}</div>
        <div class="hero-sub">{{ now()->format('Y') }}</div>
    </div>
    <div class="hero-divider"></div>
    <div class="hero-stat">
        <span class="hero-icon">🌙</span>
        <div class="hero-label">This Month</div>
        <div class="hero-val">Rs {{ number_format($stats['total_this_month'], 0) }}</div>
        <div class="hero-sub">{{ now()->format('F Y') }}</div>
    </div>
    <div class="hero-divider"></div>
    <div class="hero-stat">
        @if($filteredTotal > 0)
        <span class="hero-icon">🔍</span>
        <div class="hero-label">Filtered Range</div>
        <div class="hero-val">Rs {{ number_format($filteredTotal, 0) }}</div>
        <div class="hero-sub">From date filter below</div>
        @else
        <span class="hero-icon">👥</span>
        <div class="hero-label">Active Members</div>
        <div class="hero-val">{{ \App\Models\Member::count() }}</div>
        <div class="hero-sub">Total registered</div>
        @endif
    </div>
</div>

{{-- ── FILTER BAR ─────────────────────────────────────── --}}
<div class="filter-card">
    <div class="filter-title"><i class="bi bi-funnel-fill"></i> Filter Donations</div>
    <form method="GET" action="{{ route('donations.index') }}" id="filterForm">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label style="font-size:11px;font-weight:600;color:#8494a9;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:5px;">Search</label>
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Member name, NIC, reason…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label style="font-size:11px;font-weight:600;color:#8494a9;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:5px;">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label style="font-size:11px;font-weight:600;color:#8494a9;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:5px;">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" max="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label style="font-size:11px;font-weight:600;color:#8494a9;text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:5px;">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="received" {{ request('status')==='received' ? 'selected':'' }}>Received</option>
                    <option value="pending"  {{ request('status')==='pending'  ? 'selected':'' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn-filter flex-grow-1">
                    <i class="bi bi-funnel"></i> Apply
                </button>
                @if(request()->hasAny(['search','date_from','date_to','status']))
                <a href="{{ route('donations.index') }}" class="btn-clear">
                    <i class="bi bi-x-lg"></i> Clear
                </a>
                @endif
            </div>
        </div>

        {{-- Quick date presets --}}
        <div class="d-flex gap-2 mt-3 flex-wrap align-items-center">
            <span style="font-size:11px;color:#8494a9;font-weight:600;">Quick:</span>
            @php
                $presets = [
                    'This Month' => [now()->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
                    'Last Month' => [now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
                    'This Year'  => [now()->startOfYear()->format('Y-m-d'), now()->format('Y-m-d')],
                    'Last 3 Months' => [now()->subMonths(3)->format('Y-m-d'), now()->format('Y-m-d')],
                ];
            @endphp
            @foreach($presets as $label => [$from, $to])
            <a href="{{ route('donations.index', ['date_from' => $from, 'date_to' => $to]) }}"
               style="font-size:11.5px;font-weight:600;padding:4px 12px;border-radius:20px;text-decoration:none;background:{{ request('date_from')===$from && request('date_to')===$to ? 'var(--gold)' : '#f0f3f8' }};color:{{ request('date_from')===$from && request('date_to')===$to ? 'var(--navy)' : '#5a7194' }};">
                {{ $label }}
            </a>
            @endforeach

            @if(request('date_from') || request('date_to'))
            <span class="date-range-chip">
                <i class="bi bi-calendar-range"></i>
                {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d M Y') : '∞' }}
                →
                {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d M Y') : now()->format('d M Y') }}
            </span>
            @endif
        </div>
    </form>
</div>

{{-- ── DONATIONS TABLE ─────────────────────────────────── --}}
<div class="table-card">
    <div class="table-card-header">
        <h3 class="tc-title">
            <i class="bi bi-gift me-2" style="color:var(--gold);"></i>Donation Records
            <span style="font-size:12px;font-weight:500;color:#8494a9;margin-left:6px;">({{ $donations->total() }} total)</span>
        </h3>
        @if($donations->count())
        <span style="font-size:12.5px;color:#0e9578;font-weight:600;">
            <i class="bi bi-check-circle me-1"></i>
            Page Total: Rs {{ number_format($donations->sum('amount'), 2) }}
        </span>
        @endif
    </div>

    <div class="table-responsive">
        <table class="obs-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Reason</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Receipt</th>
                    <th>Status</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donations as $donation)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="m-avatar">{{ strtoupper(substr($donation->member->name_with_initials ?? '?', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $donation->member->name_with_initials ?? '—' }}</div>
                                <div style="font-size:11px;color:#8494a9;">{{ $donation->member->phone_number ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="reason-text" title="{{ $donation->reason }}">
                            {{ $donation->reason }}
                        </span>
                    </td>
                    <td>
                        <span class="amount-chip">Rs {{ number_format($donation->amount, 2) }}</span>
                    </td>
                    <td>
                        <div style="font-size:13.5px;font-weight:500;">{{ $donation->donation_date->format('d M Y') }}</div>
                        <div style="font-size:11px;color:#8494a9;">{{ $donation->donation_date->diffForHumans() }}</div>
                    </td>
                    <td style="font-size:12px;font-family:monospace;color:#5a7194;">
                        {{ $donation->receipt_number ?? '—' }}
                    </td>
                    <td>
                        <span class="s-pill s-{{ $donation->status }}">{{ $donation->status_label }}</span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('donations.show', $donation) }}"  class="action-btn view" title="View"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('donations.edit', $donation) }}"  class="action-btn edit" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('donations.destroy', $donation) }}" onsubmit="return confirm('Delete this donation record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn del" title="Delete" style="cursor:pointer;"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-gift"></i>
                            <h5>No Donations Found</h5>
                            <p>
                                @if(request()->hasAny(['search','date_from','date_to','status']))
                                    Try adjusting your filters, or <a href="{{ route('donations.index') }}">clear all</a>.
                                @else
                                    <a href="{{ route('donations.create') }}">Record the first donation →</a>
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($donations->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f8;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <div style="font-size:12px;color:#8494a9;">
            Showing {{ $donations->firstItem() }}–{{ $donations->lastItem() }} of {{ $donations->total() }} donations
        </div>
        {{ $donations->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
