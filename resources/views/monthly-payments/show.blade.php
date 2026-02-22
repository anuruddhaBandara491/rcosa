@extends('layouts.app')

@section('title', 'Payment — ' . $payment->member->name_with_initials)
@section('page-title', 'Monthly Payment Detail')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; --gold-lt:#f0d080; }

/* Hero banner */
.pay-hero { border-radius:16px;padding:26px 28px;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;animation:fadeUp .3s ease; }
.pay-hero.paid     { background:linear-gradient(135deg,#064e3b,#16a34a); }
.pay-hero.partial  { background:linear-gradient(135deg,#78350f,#d97706); }
.pay-hero.overpaid { background:linear-gradient(135deg,#3b0764,#7c3aed); }
.hero-icon { width:60px;height:60px;border-radius:16px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0; }
.hero-name { font-family:'Playfair Display',serif;font-size:20px;font-weight:700;margin:0 0 4px; }
.hero-sub  { font-size:13px;opacity:.75;margin:0; }
.hero-amount { text-align:right;margin-left:auto; }
.amount-label { font-size:11px;text-transform:uppercase;letter-spacing:1.2px;opacity:.7; }
.amount-val   { font-family:'Playfair Display',serif;font-size:30px;font-weight:700;line-height:1; }
.hero-actions { display:flex;gap:8px;flex-wrap:wrap; }

/* Cards */
.detail-card { background:#fff;border:1px solid #e4e9f0;border-radius:14px;overflow:hidden;margin-bottom:18px;animation:fadeUp .35s ease both; }
.detail-card-header { padding:14px 20px;border-bottom:1px solid #f0f3f8;background:#fafbfd;display:flex;align-items:center;gap:9px; }
.detail-card-header i { font-size:16px;color:var(--gold); }
.detail-card-header h5 { font-family:'Playfair Display',serif;font-size:14px;font-weight:700;color:var(--navy);margin:0; }
.detail-body { padding:20px; }

/* Grid */
.info-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:0; }
.info-item { padding:14px 20px;border-bottom:1px solid #f6f8fb;border-right:1px solid #f6f8fb; }
.info-label { font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#8494a9;margin-bottom:5px; }
.info-value { font-size:14px;color:#1a2b44;font-weight:500; }

/* Summary row */
.sum-row { display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid #f6f8fb;font-size:13.5px; }
.sum-row:last-child { border-bottom:none; }
.sum-label { color:#5a7194; }
.sum-val   { font-weight:700; }

/* Progress */
.prog-outer { height:12px;background:#f0f3f8;border-radius:99px;overflow:hidden;margin:12px 0 4px; }
.prog-inner { height:100%;border-radius:99px;transition:width .4s ease; }

/* Status pills */
.s-pill { font-size:12px;font-weight:700;padding:4px 14px;border-radius:20px;white-space:nowrap;display:inline-block; }
.s-paid     { background:#dcfce7;color:#15803d; }
.s-partial  { background:#fef3c7;color:#b45309; }
.s-overpaid { background:#ede9fe;color:#6d28d9; }

/* Month coverage table */
.mo-table { width:100%;border-collapse:collapse; }
.mo-table th { font-size:10.5px;text-transform:uppercase;letter-spacing:1.1px;color:#8494a9;font-weight:600;padding:9px 14px;border-bottom:1px solid #f0f3f8;background:#fafbfd; }
.mo-table td { padding:11px 14px;font-size:13px;color:#2c3e55;border-bottom:1px solid #f6f8fb;vertical-align:middle; }
.mo-table tr:last-child td { border-bottom:none; }
.mo-chip { display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:3px 10px;border-radius:7px;background:#e8ecf5;color:#1e3a5f; }
.mo-chip.partial { background:#fef3c7;color:#b45309; }
.mo-chip.advance { background:#ede9fe;color:#6d28d9; }
.full-badge  { font-size:10.5px;font-weight:600;padding:2px 8px;border-radius:10px;background:#dcfce7;color:#15803d; }
.part-badge  { font-size:10.5px;font-weight:600;padding:2px 8px;border-radius:10px;background:#fef3c7;color:#b45309; }
.adv-badge   { font-size:10.5px;font-weight:600;padding:2px 8px;border-radius:10px;background:#ede9fe;color:#6d28d9; }

/* Action buttons */
.btn-edit  { background:var(--gold);color:var(--navy);border:none;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:opacity .2s; }
.btn-edit:hover { opacity:.88;color:var(--navy); }
.btn-ghost { background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.22);border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:background .2s; }
.btn-ghost:hover { background:rgba(255,255,255,.22);color:#fff; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

{{-- HERO BANNER --}}
<div class="pay-hero {{ $payment->status }}">
    <div class="hero-icon">
        @if($payment->status === 'paid')        <i class="bi bi-check-circle-fill"></i>
        @elseif($payment->status === 'overpaid') <i class="bi bi-arrow-up-circle-fill"></i>
        @else                                    <i class="bi bi-hourglass-split"></i>
        @endif
    </div>

    <div>
        <h2 class="hero-name">{{ $payment->member->name_with_initials }}</h2>
        <p class="hero-sub">{{ $payment->member->nic_number }} &middot; {{ $payment->payment_date?->format('d F Y') }}</p>
        <div style="margin-top:8px;">
            <span style="font-size:12px;font-weight:700;background:rgba(255,255,255,.2);color:#fff;padding:4px 14px;border-radius:20px;">
                {{ strtoupper($payment->status_label) }}
            </span>
        </div>
    </div>

    <div class="hero-amount">
        <div class="amount-label">Amount Paid</div>
        <div class="amount-val">Rs {{ number_format($payment->paid_amount, 0) }}</div>
        @if((float)$payment->balance_amount > 0)
            <div style="font-size:12px;opacity:.8;margin-top:3px;">Balance: Rs {{ number_format($payment->balance_amount, 2) }} remaining</div>
        @elseif((float)$payment->balance_amount < 0)
            <div style="font-size:12px;opacity:.8;margin-top:3px;">Rs {{ number_format(abs($payment->balance_amount), 2) }} credit / overpaid</div>
        @else
            <div style="font-size:12px;opacity:.8;margin-top:3px;">Fully settled ✓</div>
        @endif
    </div>

    <div class="hero-actions">
        <a href="{{ route('monthly-payments.index') }}" class="btn-ghost"><i class="bi bi-arrow-left"></i> Back</a>
        <a href="{{ route('monthly-payments.edit', $payment) }}" class="btn-edit"><i class="bi bi-pencil-fill"></i> Edit</a>
        <form method="POST" action="{{ route('monthly-payments.destroy', $payment) }}"
              onsubmit="return confirm('Delete this payment transaction?')">
            @csrf @method('DELETE')
            <button class="btn-ghost" style="background:rgba(220,53,69,.25);border-color:rgba(220,53,69,.4);cursor:pointer;">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</div>

<div class="row g-3">
<div class="col-lg-8">

    {{-- Transaction summary --}}
    <div class="detail-card" style="animation-delay:.05s">
        <div class="detail-card-header"><i class="bi bi-cash-coin"></i><h5>Transaction Summary</h5></div>
        <div class="detail-body">
            <div class="sum-row">
                <span class="sum-label">Amount Paid (this transaction)</span>
                <span class="sum-val" style="font-family:'Playfair Display',serif;font-size:18px;color:var(--navy);">
                    Rs {{ number_format($payment->paid_amount, 2) }}
                </span>
            </div>
            <div class="sum-row">
                <span class="sum-label">Total Due (at time of payment)</span>
                <span class="sum-val" style="color:#5a7194;">Rs {{ number_format($payment->total_due, 2) }}</span>
            </div>
            <div class="sum-row">
                <span class="sum-label">Cumulative Paid (all transactions)</span>
                <span class="sum-val" style="color:#15803d;">Rs {{ number_format($payment->cumulative_paid, 2) }}</span>
            </div>
            <div class="sum-row">
                <span class="sum-label">
                    @if((float)$payment->balance_amount < 0) Overpaid / Credit
                    @else Outstanding Balance @endif
                </span>
                <span class="sum-val" style="color:{{ (float)$payment->balance_amount < 0 ? '#6d28d9' : ((float)$payment->balance_amount == 0 ? '#15803d' : '#b91c1c') }};">
                    @if((float)$payment->balance_amount < 0)
                        +Rs {{ number_format(abs($payment->balance_amount), 2) }} credit
                    @else
                        Rs {{ number_format($payment->balance_amount, 2) }}
                    @endif
                </span>
            </div>
            <div class="sum-row">
                <span class="sum-label">Status</span>
                <span class="s-pill s-{{ $payment->status }}">{{ $payment->status_label }}</span>
            </div>

            {{-- Progress bar --}}
            <div style="margin-top:8px;">
                <div style="display:flex;justify-content:space-between;font-size:11.5px;color:#8494a9;margin-bottom:4px;">
                    <span>Overall payment coverage</span>
                    <span>{{ $payment->progress_percent }}%</span>
                </div>
                <div class="prog-outer">
                    <div class="prog-inner" style="width:{{ $payment->progress_percent }}%;background:{{ $payment->status==='paid'?'#16a34a':($payment->status==='overpaid'?'#7c3aed':'#d97706') }};"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Months covered breakdown --}}
    @if($payment->months_covered && count($payment->months_covered))
    <div class="detail-card" style="animation-delay:.08s">
        <div class="detail-card-header"><i class="bi bi-calendar2-check-fill"></i><h5>Months Applied ({{ count($payment->months_covered) }})</h5></div>
        <table class="mo-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Amount Applied</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->months_covered as $mc)
                <tr>
                    <td>
                        <span class="mo-chip {{ !$mc['full'] ? 'partial' : '' }} {{ is_null($mc['month']) ? 'advance' : '' }}">
                            <i class="bi bi-calendar2 me-1"></i>{{ $mc['label'] }}
                        </span>
                    </td>
                    <td style="font-weight:700;color:#0f1f3d;">Rs {{ number_format($mc['amount'], 2) }}</td>
                    <td>
                        @if(is_null($mc['month']))
                            <span class="adv-badge">Advance / Credit</span>
                        @elseif($mc['full'])
                            <span class="full-badge">Fully Covered</span>
                        @else
                            <span class="part-badge">Partial</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Payment metadata --}}
    <div class="detail-card" style="animation-delay:.1s">
        <div class="detail-card-header"><i class="bi bi-receipt"></i><h5>Payment Details</h5></div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Payment Date</div>
                <div class="info-value">{{ $payment->payment_date?->format('d F Y') ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Receipt No.</div>
                <div class="info-value" style="font-family:monospace;">{{ $payment->receipt_number ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Recorded On</div>
                <div class="info-value">{{ $payment->created_at?->format('d M Y, H:i') ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Last Updated</div>
                <div class="info-value">{{ $payment->updated_at?->format('d M Y, H:i') ?? '—' }}</div>
            </div>
        </div>
        @if($payment->notes)
        <div style="padding:14px 20px;border-top:1px solid #f6f8fb;">
            <div class="info-label" style="margin-bottom:6px;">Notes</div>
            <div style="font-size:13.5px;color:#2c3e55;">{{ $payment->notes }}</div>
        </div>
        @endif
    </div>

    {{-- Member info --}}
    <div class="detail-card" style="animation-delay:.12s">
        <div class="detail-card-header"><i class="bi bi-person-fill"></i><h5>Member</h5></div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Name</div>
                <div class="info-value" style="font-weight:600;">{{ $payment->member->name_with_initials }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">NIC</div>
                <div class="info-value" style="font-family:monospace;">{{ $payment->member->nic_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value">{{ $payment->member->phone_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">City</div>
                <div class="info-value">{{ $payment->member->current_city }}</div>
            </div>
        </div>
    </div>

</div>

{{-- Right sidebar --}}
<div class="col-lg-4">
    <div class="detail-card" style="animation-delay:.1s;">
        <div class="detail-card-header"><i class="bi bi-lightning-charge-fill"></i><h5>Quick Actions</h5></div>
        <div style="padding:14px;">
            <div class="d-grid gap-2">
                <a href="{{ route('monthly-payments.edit', $payment) }}"
                   class="btn btn-sm py-2" style="background:#fef3dc;color:#b07d10;border-radius:10px;font-weight:600;border:1px solid #f5dfa0;">
                    <i class="bi bi-pencil me-1"></i> Edit This Transaction
                </a>
                <a href="{{ route('monthly-payments.create') }}"
                   class="btn btn-sm py-2" style="background:#e8ecf5;color:#1e3a5f;border-radius:10px;font-weight:600;border:1px solid #c0cfe0;">
                    <i class="bi bi-plus-circle me-1"></i> Record New Payment
                </a>
                <a href="{{ route('members.show', $payment->member) }}"
                   class="btn btn-sm py-2" style="background:#f4f6fb;color:#3d5270;border-radius:10px;font-weight:600;border:1px solid #dde3ef;">
                    <i class="bi bi-person me-1"></i> View Member Profile
                </a>
                <a href="{{ route('monthly-payments.index') }}"
                   class="btn btn-sm py-2" style="background:#f4f6fb;color:#3d5270;border-radius:10px;font-weight:600;border:1px solid #dde3ef;">
                    <i class="bi bi-arrow-left me-1"></i> All Transactions
                </a>
            </div>
        </div>
    </div>

    {{-- At-a-glance numbers --}}
    <div class="detail-card" style="animation-delay:.14s;">
        <div class="detail-card-header"><i class="bi bi-bar-chart-fill"></i><h5>At a Glance</h5></div>
        <div style="padding:16px 20px;">
            <div class="sum-row">
                <span class="sum-label" style="font-size:12.5px;">This Payment</span>
                <span class="sum-val" style="color:var(--navy);">Rs {{ number_format($payment->paid_amount, 2) }}</span>
            </div>
            <div class="sum-row">
                <span class="sum-label" style="font-size:12.5px;">Total Paid to Date</span>
                <span class="sum-val" style="color:#15803d;">Rs {{ number_format($payment->cumulative_paid, 2) }}</span>
            </div>
            <div class="sum-row">
                <span class="sum-label" style="font-size:12.5px;">Still Outstanding</span>
                <span class="sum-val" style="color:{{ (float)$payment->balance_amount > 0 ? '#b91c1c':'#15803d' }};">
                    Rs {{ number_format(max(0, $payment->balance_amount), 2) }}
                </span>
            </div>
            <div class="sum-row">
                <span class="sum-label" style="font-size:12.5px;">Months Applied</span>
                <span class="sum-val" style="color:var(--navy);">{{ count($payment->months_covered ?? []) }}</span>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
