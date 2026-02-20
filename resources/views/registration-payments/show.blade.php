@extends('layouts.app')

@section('title', 'Payment — ' . $payment->member->name_with_initials)
@section('page-title', 'Payment Details')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; }

.pay-hero {
    border-radius:16px; padding:26px 28px;
    color:#fff; margin-bottom:20px;
    display:flex; align-items:center; gap:20px; flex-wrap:wrap;
    animation:fadeUp .3s ease;
}
.pay-hero.paid    { background:linear-gradient(135deg,#0e5c30,#1a8a45); }
.pay-hero.partial { background:linear-gradient(135deg,#7a4f00,#c9a84c); }
.pay-hero.unpaid  { background:linear-gradient(135deg,#6b0f0f,#c0392b); }

.hero-icon { width:60px;height:60px;border-radius:16px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0; }
.hero-name { font-family:'Playfair Display',serif;font-size:20px;font-weight:700;margin:0 0 2px; }
.hero-sub  { font-size:13px;opacity:.75;margin:0; }
.hero-actions { margin-left:auto;display:flex;gap:8px;flex-wrap:wrap; }

.hero-amount {
    text-align:right;
    margin-left:auto;
}
.amount-label { font-size:11px;text-transform:uppercase;letter-spacing:1.2px;opacity:.7; }
.amount-value { font-family:'Playfair Display',serif;font-size:30px;font-weight:700;line-height:1; }
.amount-balance { font-size:13px;opacity:.8;margin-top:2px; }

.detail-card { background:#fff;border:1px solid #e4e9f0;border-radius:14px;overflow:hidden;margin-bottom:18px;animation:fadeUp .35s ease both; }
.detail-card-header { padding:14px 20px;border-bottom:1px solid #f0f3f8;background:#fafbfd;display:flex;align-items:center;gap:9px; }
.detail-card-header i { font-size:16px;color:var(--gold); }
.detail-card-header h5 { font-family:'Playfair Display',serif;font-size:14px;font-weight:700;color:var(--navy);margin:0; }

.detail-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0; }
.detail-item { padding:14px 20px;border-bottom:1px solid #f6f8fb;border-right:1px solid #f6f8fb; }
.detail-label { font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#8494a9;margin-bottom:4px; }
.detail-value { font-size:14px;color:#1a2b44;font-weight:500; }
.detail-value.mono { font-family:monospace;letter-spacing:.5px; }

/* Receipt box */
.receipt-box {
    background:var(--navy); border-radius:14px; padding:24px;
    color:#fff; text-align:center;
    animation:fadeUp .4s .1s ease both;
}
.receipt-header { font-size:11px;text-transform:uppercase;letter-spacing:2px;color:#7a9abc;margin-bottom:16px; }
.receipt-number { font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--gold);letter-spacing:2px; }
.receipt-divider { height:1px;background:rgba(255,255,255,.1);margin:16px 0; }

.prog-outer { height:12px;background:#f0f3f8;border-radius:99px;overflow:hidden;margin:10px 0 4px; }
.prog-inner { height:100%;border-radius:99px;transition:width .8s ease; }

.btn-edit { background:var(--gold);color:var(--navy);border:none;border-radius:10px;padding:9px 18px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px; }
.btn-edit:hover { background:#f0d080;color:var(--navy); }
.btn-ghost { background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px; }
.btn-ghost:hover { background:rgba(255,255,255,.22);color:#fff; }

.s-pill { font-size:12px;font-weight:700;padding:4px 14px;border-radius:20px;white-space:nowrap;display:inline-block; }
.s-paid    { background:#e8f7ee;color:#1a8a45; }
.s-partial { background:#fff3d6;color:#b07d10; }
.s-unpaid  { background:#fdecea;color:#c0392b; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

{{-- ── HERO ─────────────────────────────────────────── --}}
<div class="pay-hero {{ $payment->status }}">
    <div class="hero-icon">
        @if($payment->status === 'paid') <i class="bi bi-check-circle-fill"></i>
        @elseif($payment->status === 'partial') <i class="bi bi-hourglass-split"></i>
        @else <i class="bi bi-x-circle-fill"></i>
        @endif
    </div>
    <div>
        <h2 class="hero-name">{{ $payment->member->name_with_initials }}</h2>
        <p class="hero-sub">{{ $payment->member->nic_number }} · Registration Payment</p>
        <span style="display:inline-block;margin-top:6px;font-size:11px;font-weight:700;background:rgba(255,255,255,.2);padding:2px 10px;border-radius:20px;">
            {{ strtoupper($payment->status_label) }}
        </span>
    </div>
    <div class="hero-amount">
        <div class="amount-label">Paid / Total</div>
        <div class="amount-value">Rs {{ number_format($payment->paid_amount, 0) }} <span style="opacity:.5;font-size:18px;">/ {{ number_format($payment->total_amount, 0) }}</span></div>
        @if($payment->balance_amount > 0)
        <div class="amount-balance">Balance: Rs {{ number_format($payment->balance_amount, 2) }}</div>
        @endif
    </div>
    <div class="hero-actions">
        <a href="{{ route('members.show', $payment->member) }}" class="btn-ghost">
            <i class="bi bi-person"></i> Member
        </a>
        <a href="{{ route('registration-payments.edit', $payment) }}" class="btn-edit">
            <i class="bi bi-pencil-fill"></i> Edit
        </a>
        <form method="POST" action="{{ route('registration-payments.destroy', $payment) }}"
              onsubmit="return confirm('Delete this payment record?')">
            @csrf @method('DELETE')
            <button class="btn-ghost" style="background:rgba(220,53,69,.25);border-color:rgba(220,53,69,.4);cursor:pointer;">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</div>

<div class="row g-3">
<div class="col-lg-8">

    {{-- Payment Breakdown --}}
    <div class="detail-card" style="animation-delay:.05s">
        <div class="detail-card-header">
            <i class="bi bi-cash-coin"></i>
            <h5>Payment Breakdown</h5>
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Total Fee</div>
                <div class="detail-value" style="color:var(--navy);font-size:16px;font-weight:700;">Rs {{ number_format($payment->total_amount, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Amount Paid</div>
                <div class="detail-value" style="color:#1a8a45;font-size:16px;font-weight:700;">Rs {{ number_format($payment->paid_amount, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Balance Due</div>
                <div class="detail-value" style="color:{{ $payment->balance_amount > 0 ? '#c0392b' : '#1a8a45' }};font-size:16px;font-weight:700;">
                    Rs {{ number_format($payment->balance_amount, 2) }}
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="s-pill s-{{ $payment->status }}">{{ $payment->status_label }}</span>
                </div>
            </div>
        </div>

        {{-- Progress --}}
        <div style="padding:16px 20px;border-top:1px solid #f0f3f8;">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#8494a9;margin-bottom:4px;">
                <span>Payment Progress</span>
                <span>{{ $payment->progress_percent }}%</span>
            </div>
            <div class="prog-outer">
                <div class="prog-inner" style="
                    width:{{ $payment->progress_percent }}%;
                    background:{{ $payment->status === 'paid' ? '#1a8a45' : ($payment->status === 'partial' ? '#c9a84c' : '#c0392b') }};
                "></div>
            </div>
        </div>
    </div>

    {{-- Transaction Info --}}
    <div class="detail-card" style="animation-delay:.1s">
        <div class="detail-card-header">
            <i class="bi bi-calendar2-check-fill"></i>
            <h5>Transaction Details</h5>
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Payment Date</div>
                <div class="detail-value">{{ $payment->payment_date ? $payment->payment_date->format('d F Y') : '—' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Recorded On</div>
                <div class="detail-value">{{ $payment->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Last Updated</div>
                <div class="detail-value">{{ $payment->updated_at->format('d M Y, h:i A') }}</div>
            </div>
            @if($payment->notes)
            <div class="detail-item" style="grid-column:1/-1;">
                <div class="detail-label">Notes</div>
                <div class="detail-value">{{ $payment->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Member Info --}}
    <div class="detail-card" style="animation-delay:.15s">
        <div class="detail-card-header">
            <i class="bi bi-person-fill"></i>
            <h5>Member Information</h5>
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Name</div>
                <div class="detail-value">{{ $payment->member->name_with_initials }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">NIC</div>
                <div class="detail-value mono">{{ $payment->member->nic_number }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Phone</div>
                <div class="detail-value">{{ $payment->member->phone_number }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Occupation</div>
                <div class="detail-value">{{ $payment->member->occupation }}</div>
            </div>
        </div>
    </div>

</div>

{{-- RIGHT: Receipt + Actions --}}
<div class="col-lg-4">

    <div class="receipt-box">
        <div class="receipt-header"><i class="bi bi-receipt me-1"></i> Receipt</div>
        @if($payment->receipt_number)
            <div class="receipt-number">{{ $payment->receipt_number }}</div>
        @else
            <div style="color:#7a9abc;font-size:13px;font-style:italic;">No receipt number</div>
        @endif
        <div class="receipt-divider"></div>
        <div style="font-size:12px;color:#7a9abc;margin-bottom:6px;">AMOUNT PAID</div>
        <div style="font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:var(--gold);">
            Rs {{ number_format($payment->paid_amount, 2) }}
        </div>
        <div style="font-size:12px;color:#7a9abc;margin-top:4px;">
            of Rs {{ number_format($payment->total_amount, 2) }} total
        </div>
        <div class="receipt-divider"></div>
        <div style="font-size:12px;color:#7a9abc;">
            {{ $payment->payment_date ? $payment->payment_date->format('d F Y') : 'Date not recorded' }}
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="detail-card mt-3" style="animation-delay:.2s;">
        <div class="detail-card-header">
            <i class="bi bi-lightning-charge-fill"></i>
            <h5>Quick Actions</h5>
        </div>
        <div style="padding:14px;">
            <div class="d-grid gap-2">
                <a href="{{ route('registration-payments.edit', $payment) }}"
                   class="btn btn-sm py-2" style="background:#fef3dc;color:#b07d10;border-radius:10px;font-weight:600;border:1px solid #f5dfa0;">
                    <i class="bi bi-pencil me-1"></i> Edit Payment
                </a>
                @if(!$payment->is_fully_paid)
                <a href="{{ route('registration-payments.create', ['member_id' => $payment->member_id]) }}"
                   class="btn btn-sm py-2" style="background:#e8f7ee;color:#1a8a45;border-radius:10px;font-weight:600;border:1px solid #b8e0c6;">
                    <i class="bi bi-plus-circle me-1"></i> Add Payment
                </a>
                @endif
                <a href="{{ route('members.show', $payment->member) }}"
                   class="btn btn-sm py-2" style="background:#f4f6fb;color:#3d5270;border-radius:10px;font-weight:600;border:1px solid #dde3ef;">
                    <i class="bi bi-person me-1"></i> View Member Profile
                </a>
                <a href="{{ route('registration-payments.index') }}"
                   class="btn btn-sm py-2" style="background:#f4f6fb;color:#3d5270;border-radius:10px;font-weight:600;border:1px solid #dde3ef;">
                    <i class="bi bi-arrow-left me-1"></i> Back to All Payments
                </a>
            </div>
        </div>
    </div>

</div>
</div>

@endsection
