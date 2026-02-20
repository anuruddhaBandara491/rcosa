@extends('layouts.app')

@section('title', 'Monthly Payments')
@section('page-title', 'Monthly Fee Payments')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; --surface:#f4f6fb; }

.mini-stat {
    background:#fff; border:1px solid #e4e9f0; border-radius:14px;
    padding:18px 20px; display:flex; align-items:center; gap:14px;
    animation:fadeUp .3s ease both; transition:transform .2s,box-shadow .2s;
    text-decoration:none; color:inherit;
}
.mini-stat:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(15,31,61,.08); color:inherit; }
.mini-icon { width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.mini-label { font-size:11px;text-transform:uppercase;letter-spacing:1.1px;color:#8494a9;font-weight:600;margin-bottom:3px; }
.mini-value { font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--navy);line-height:1; }

.table-card { background:#fff;border:1px solid #e4e9f0;border-radius:14px;overflow:hidden;animation:fadeUp .35s ease; }
.table-card-header { padding:16px 20px;border-bottom:1px solid #f0f3f8;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px; }
.tc-title { font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--navy);margin:0; }

.obs-table { width:100%;border-collapse:collapse; }
.obs-table th { font-size:10.5px;text-transform:uppercase;letter-spacing:1.2px;color:#8494a9;font-weight:600;padding:11px 16px;border-bottom:1px solid #f0f3f8;background:#fafbfd;white-space:nowrap; }
.obs-table td { padding:12px 16px;font-size:13.5px;color:#2c3e55;border-bottom:1px solid #f6f8fb;vertical-align:middle; }
.obs-table tr:last-child td { border-bottom:none; }
.obs-table tbody tr:hover td { background:#fafbfd; }

.m-avatar { width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#1e3a5f,#0f1f3d);color:var(--gold);display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;margin-right:10px;flex-shrink:0; }

.month-badge { display:inline-block;background:#e8ecf5;color:#1e3a5f;font-size:11.5px;font-weight:700;padding:3px 10px;border-radius:8px; }

.s-pill { font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap;display:inline-block; }
.s-paid    { background:#e8f7ee;color:#1a8a45; }
.s-partial { background:#fff3d6;color:#b07d10; }
.s-unpaid  { background:#fdecea;color:#c0392b; }

.prog-wrap { width:80px;height:6px;background:#f0f3f8;border-radius:99px;overflow:hidden;display:inline-block;vertical-align:middle; }
.prog-bar  { height:100%;border-radius:99px; }

.action-btn { width:30px;height:30px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;border:1px solid #e4e9f0;background:#f4f6fb;color:#3d5270;text-decoration:none;transition:all .15s; }
.action-btn:hover { transform:scale(1.12); }
.action-btn.view:hover { background:#ddf5f1;color:#0e9578;border-color:#b2e7de; }
.action-btn.edit:hover { background:#fef3dc;color:#c9a84c;border-color:#f5dfa0; }
.action-btn.del:hover  { background:#fdecea;color:#c0392b;border-color:#f5c0bc; }

.filter-card { background:#fff;border:1px solid #e4e9f0;border-radius:14px;padding:16px 20px;margin-bottom:18px; }
.search-wrap { position:relative; }
.search-wrap i { position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#8494a9;font-size:15px; }
.search-wrap input { padding-left:38px;border-radius:10px;border:1px solid #dde3ef;height:40px;font-size:13.5px; }
.search-wrap input:focus { border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.12);outline:none; }
.filter-select { border-radius:10px;border:1px solid #dde3ef;height:40px;font-size:13.5px;padding:0 12px; }
.filter-select:focus { border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.12);outline:none; }
.btn-new { background:var(--navy);color:#fff;border:none;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none; }
.btn-new:hover { background:#1e3a5f;color:#fff; }
.btn-filter { background:var(--surface);color:var(--navy);border:1px solid #dde3ef;border-radius:10px;padding:8px 14px;font-size:13px;font-weight:600;cursor:pointer; }
.empty-state { text-align:center;padding:52px 20px;color:#8494a9; }
.empty-state i { font-size:40px;display:block;margin-bottom:12px;opacity:.4; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h1>Monthly Payments</h1>
        <p>Track monthly fee collection. Fee: <strong>Rs {{ number_format(\App\Models\MonthlyPayment::monthlyFee(), 2) }}</strong> / month</p>
    </div>
    <a href="{{ route('monthly-payments.create') }}" class="btn-new">
        <i class="bi bi-plus-circle-fill"></i> Record Payment
    </a>
</div>

{{-- STATS --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="mini-stat" style="animation-delay:.04s">
            <div class="mini-icon" style="background:#e8f7ee;color:#1a8a45;"><i class="bi bi-check-circle-fill"></i></div>
            <div><div class="mini-label">Fully Paid</div><div class="mini-value">{{ $stats['count_paid'] }}</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="mini-stat" style="animation-delay:.08s">
            <div class="mini-icon" style="background:#fff3d6;color:#b07d10;"><i class="bi bi-hourglass-split"></i></div>
            <div><div class="mini-label">Partial</div><div class="mini-value">{{ $stats['count_partial'] }}</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="mini-stat" style="animation-delay:.12s">
            <div class="mini-icon" style="background:#fdecea;color:#c0392b;"><i class="bi bi-x-circle-fill"></i></div>
            <div><div class="mini-label">Unpaid</div><div class="mini-value">{{ $stats['count_unpaid'] }}</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="mini-stat" style="animation-delay:.16s;background:linear-gradient(135deg,#0f1f3d,#1e3a5f);border-color:transparent;">
            <div class="mini-icon" style="background:rgba(201,168,76,.2);color:#c9a84c;"><i class="bi bi-cash-coin"></i></div>
            <div>
                <div class="mini-label" style="color:#7a9abc;">Collected</div>
                <div class="mini-value" style="color:#fff;">Rs {{ number_format($stats['total_collected'], 0) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- FILTERS --}}
<div class="filter-card">
    <form method="GET" action="{{ route('monthly-payments.index') }}" id="filterForm">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, NIC, phone…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="filter-select form-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="paid"    {{ request('status')==='paid'    ? 'selected':'' }}>Paid</option>
                    <option value="partial" {{ request('status')==='partial' ? 'selected':'' }}>Partial</option>
                    <option value="unpaid"  {{ request('status')==='unpaid'  ? 'selected':'' }}>Unpaid</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="filter-select form-select" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('year')==$y ? 'selected':'' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="month" class="filter-select form-select" onchange="this.form.submit()">
                    <option value="">All Months</option>
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ request('month')==$m ? 'selected':'' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn-filter w-100"><i class="bi bi-funnel"></i></button>
            </div>
            @if(request()->hasAny(['search','status','year','month']))
            <div class="col-md-1">
                <a href="{{ route('monthly-payments.index') }}" class="btn-filter w-100 text-center text-danger" style="display:block;border-color:#f5c0bc;"><i class="bi bi-x-lg"></i></a>
            </div>
            @endif
        </div>
    </form>
</div>

{{-- TABLE --}}
<div class="table-card">
    <div class="table-card-header">
        <h3 class="tc-title"><i class="bi bi-calendar2-check me-2" style="color:var(--gold);"></i>Payment Records
            <span style="font-size:12px;font-weight:500;color:#8494a9;margin-left:6px;">({{ $payments->total() }})</span>
        </h3>
        @if($stats['total_balance'] > 0)
        <span style="font-size:12px;background:#fdecea;color:#c0392b;padding:4px 12px;border-radius:20px;font-weight:600;">
            <i class="bi bi-exclamation-circle me-1"></i> Outstanding: Rs {{ number_format($stats['total_balance'], 2) }}
        </span>
        @endif
    </div>
    <div class="table-responsive">
        <table class="obs-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Month</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="m-avatar">{{ strtoupper(substr($payment->member->name_with_initials ?? '?', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $payment->member->name_with_initials ?? '—' }}</div>
                                <div style="font-size:11px;color:#8494a9;">{{ $payment->member->phone_number ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="month-badge">{{ $payment->month_label }}</span></td>
                    <td style="font-weight:600;">Rs {{ number_format($payment->total_amount, 2) }}</td>
                    <td style="color:#1a8a45;font-weight:600;">Rs {{ number_format($payment->paid_amount, 2) }}</td>
                    <td style="color:{{ $payment->balance_amount > 0 ? '#c0392b':'#1a8a45' }};font-weight:600;">Rs {{ number_format($payment->balance_amount, 2) }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="prog-wrap">
                                <div class="prog-bar" style="width:{{ $payment->progress_percent }}%;background:{{ $payment->status==='paid'?'#1a8a45':($payment->status==='partial'?'#c9a84c':'#c0392b') }};"></div>
                            </div>
                            <span style="font-size:11px;color:#8494a9;">{{ $payment->progress_percent }}%</span>
                        </div>
                    </td>
                    <td><span class="s-pill s-{{ $payment->status }}">{{ $payment->status_label }}</span></td>
                    <td style="font-size:12.5px;color:#5a7194;">{{ $payment->payment_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('monthly-payments.show', $payment) }}" class="action-btn view" title="View"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('monthly-payments.edit', $payment) }}" class="action-btn edit" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('monthly-payments.destroy', $payment) }}" onsubmit="return confirm('Delete this payment record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn del" title="Delete" style="cursor:pointer;"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9"><div class="empty-state"><i class="bi bi-calendar2-x"></i><p>No payment records found. <a href="{{ route('monthly-payments.create') }}">Record the first payment →</a></p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f0f3f8;display:flex;justify-content:flex-end;">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection
