@extends('layouts.app')

@section('title', 'Donation — ' . $donation->member->name_with_initials)
@section('page-title', 'Donation Details')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; --teal:#0e9578; }

.don-hero {
    border-radius:18px;
    padding:28px 30px;
    margin-bottom:22px;
    display:flex;
    align-items:center;
    gap:22px;
    flex-wrap:wrap;
    animation:fadeUp .3s ease;
    position:relative;
    overflow:hidden;
    background:linear-gradient(135deg, var(--teal) 0%, #0b7a61 100%);
    color:#fff;
}
.don-hero::before {
    content:'';
    position:absolute; top:-30px; right:-30px;
    width:160px; height:160px;
    background:rgba(255,255,255,.06); border-radius:50%;
}
.don-hero.pending-hero { background:linear-gradient(135deg,#7a4f00,#c9a84c); }

.hero-icon { width:64px; height:64px; border-radius:18px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:28px; flex-shrink:0; }
.hero-name { font-family:'Playfair Display',serif; font-size:21px; font-weight:700; margin:0 0 4px; }
.hero-sub  { font-size:13px; opacity:.8; margin:0; }
.hero-amount { margin-left:auto; text-align:right; }
.amount-label { font-size:11px; text-transform:uppercase; letter-spacing:1.2px; opacity:.7; }
.amount-value { font-family:'Playfair Display',serif; font-size:36px; font-weight:700; line-height:1; }
.hero-actions { display:flex; gap:8px; flex-wrap:wrap; }

.btn-edit  { background:rgba(255,255,255,.2); color:#fff; border:1px solid rgba(255,255,255,.3); border-radius:10px; padding:9px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-edit:hover { background:rgba(255,255,255,.3); color:#fff; }
.btn-ghost { background:rgba(255,255,255,.1); color:#fff; border:1px solid rgba(255,255,255,.2); border-radius:10px; padding:9px 16px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-ghost:hover { background:rgba(255,255,255,.2); color:#fff; }

/* ── DETAIL CARDS ────────────────────────────────────── */
.detail-card { background:#fff; border:1px solid #e4e9f0; border-radius:14px; overflow:hidden; margin-bottom:18px; animation:fadeUp .35s ease both; }
.detail-card-header { padding:14px 20px; border-bottom:1px solid #f0f3f8; background:#fafbfd; display:flex; align-items:center; gap:9px; }
.detail-card-header i { font-size:16px; color:var(--gold); }
.detail-card-header h5 { font-family:'Playfair Display',serif; font-size:14px; font-weight:700; color:var(--navy); margin:0; }
.detail-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr)); }
.detail-item { padding:15px 20px; border-bottom:1px solid #f6f8fb; border-right:1px solid #f6f8fb; }
.detail-label { font-size:10.5px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:#8494a9; margin-bottom:5px; }
.detail-value { font-size:14px; color:#1a2b44; font-weight:500; }
.detail-value.big { font-family:'Playfair Display',serif; font-size:20px; font-weight:700; color:var(--teal); }
.detail-value.mono { font-family:monospace; letter-spacing:.5px; }

/* ── RECEIPT BOX ─────────────────────────────────────── */
.receipt-box { background:var(--navy); border-radius:14px; padding:24px; color:#fff; text-align:center; }
.receipt-header { font-size:10px; text-transform:uppercase; letter-spacing:2px; color:#7a9abc; margin-bottom:14px; }
.receipt-num { font-family:'Playfair Display',serif; font-size:26px; font-weight:700; color:var(--gold); letter-spacing:2px; word-break:break-all; }
.receipt-divider { height:1px; background:rgba(255,255,255,.1); margin:14px 0; }

/* ── OTHER DONATIONS ─────────────────────────────────── */
.other-donation-row { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #f6f8fb; }
.other-donation-row:last-child { border-bottom:none; }
.od-icon { width:32px; height:32px; border-radius:8px; background:#f4f6fb; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
.od-reason { font-size:13px; font-weight:500; color:var(--navy); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px; }
.od-date   { font-size:11.5px; color:#8494a9; }
.od-amount { font-family:'Playfair Display',serif; font-size:14px; font-weight:700; color:var(--teal); margin-left:auto; flex-shrink:0; }

.s-pill { font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; }
.s-received { background:#ddf5f1; color:#0e7a61; }
.s-pending  { background:#fff3d6; color:#b07d10; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

{{-- ── HERO ─────────────────────────────────────────── --}}
<div class="don-hero {{ $donation->status === 'pending' ? 'pending-hero' : '' }}">
    <div class="hero-icon">
        <i class="bi bi-gift-fill"></i>
    </div>
    <div style="flex:1;min-width:0;">
        <h2 class="hero-name">{{ $donation->member->name_with_initials }}</h2>
        <p class="hero-sub">{{ $donation->member->nic_number }} · {{ $donation->member->phone_number }}</p>
        <div style="margin-top:8px;">
            <span style="background:rgba(255,255,255,.2);color:#fff;font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;letter-spacing:.5px;">
                {{ strtoupper($donation->status_label) }}
            </span>
        </div>
    </div>
    <div class="hero-amount">
        <div class="amount-label">Donation Amount</div>
        <div class="amount-value">Rs {{ number_format($donation->amount, 2) }}</div>
        <div style="font-size:12px;opacity:.75;margin-top:4px;">{{ $donation->donation_date->format('d F Y') }}</div>
    </div>
    <div class="hero-actions">
        <a href="{{ route('donations.index') }}" class="btn-ghost"><i class="bi bi-arrow-left"></i> Back</a>
        <a href="{{ route('donations.edit', $donation) }}" class="btn-edit"><i class="bi bi-pencil-fill"></i> Edit</a>
        <form method="POST" action="{{ route('donations.destroy', $donation) }}" onsubmit="return confirm('Delete this donation?')">
            @csrf @method('DELETE')
            <button class="btn-ghost" style="background:rgba(220,53,69,.25);border-color:rgba(220,53,69,.4);cursor:pointer;"><i class="bi bi-trash"></i></button>
        </form>
    </div>
</div>

<div class="row g-3">
<div class="col-lg-8">

    {{-- Donation Details --}}
    <div class="detail-card" style="animation-delay:.05s">
        <div class="detail-card-header"><i class="bi bi-gift-fill"></i><h5>Donation Information</h5></div>
        <div class="detail-grid">
            <div class="detail-item" style="grid-column:1/-1;">
                <div class="detail-label">Reason for Donation</div>
                <div class="detail-value" style="font-size:15px;">{{ $donation->reason }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Amount</div>
                <div class="detail-value big">Rs {{ number_format($donation->amount, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Donation Date</div>
                <div class="detail-value">{{ $donation->donation_date->format('d F Y') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value"><span class="s-pill s-{{ $donation->status }}">{{ $donation->status_label }}</span></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Recorded On</div>
                <div class="detail-value">{{ $donation->created_at->format('d M Y, h:i A') }}</div>
            </div>
            @if($donation->notes)
            <div class="detail-item" style="grid-column:1/-1;">
                <div class="detail-label">Notes</div>
                <div class="detail-value">{{ $donation->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Member info --}}
    <div class="detail-card" style="animation-delay:.1s">
        <div class="detail-card-header"><i class="bi bi-person-fill"></i><h5>Member Profile</h5></div>
        <div class="detail-grid">
            <div class="detail-item"><div class="detail-label">Name</div><div class="detail-value">{{ $donation->member->name_with_initials }}</div></div>
            <div class="detail-item"><div class="detail-label">NIC</div><div class="detail-value mono">{{ $donation->member->nic_number }}</div></div>
            <div class="detail-item"><div class="detail-label">Phone</div><div class="detail-value">{{ $donation->member->phone_number }}</div></div>
            <div class="detail-item"><div class="detail-label">Occupation</div><div class="detail-value">{{ $donation->member->occupation }}</div></div>
            <div class="detail-item"><div class="detail-label">City</div><div class="detail-value">{{ $donation->member->current_city }}</div></div>
            <div class="detail-item">
                <div class="detail-label">Total Donated</div>
                <div class="detail-value" style="color:var(--teal);font-weight:700;">Rs {{ number_format($memberTotal, 2) }}</div>
            </div>
        </div>
    </div>

</div>

{{-- RIGHT COLUMN --}}
<div class="col-lg-4">

    {{-- Receipt --}}
    <div class="receipt-box mb-3">
        <div class="receipt-header"><i class="bi bi-receipt me-1"></i> Receipt</div>
        @if($donation->receipt_number)
        <div class="receipt-num">{{ $donation->receipt_number }}</div>
        @else
        <div style="color:#7a9abc;font-size:13px;font-style:italic;">No receipt number</div>
        @endif
        <div class="receipt-divider"></div>
        <div style="font-size:11px;color:#7a9abc;margin-bottom:6px;">DONATION AMOUNT</div>
        <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--gold);">
            Rs {{ number_format($donation->amount, 2) }}
        </div>
        <div class="receipt-divider"></div>
        <div style="font-size:12px;color:#7a9abc;">{{ $donation->donation_date->format('d F Y') }}</div>
    </div>

    {{-- Other Donations by this Member --}}
    @if($otherDonations->count())
    <div class="detail-card">
        <div class="detail-card-header">
            <i class="bi bi-clock-history"></i>
            <h5>Other Donations</h5>
        </div>
        <div style="padding:6px 16px;">
            @foreach($otherDonations as $od)
            <div class="other-donation-row">
                <div class="od-icon">🎁</div>
                <div style="min-width:0;flex:1;">
                    <div class="od-reason" title="{{ $od->reason }}">{{ $od->reason }}</div>
                    <div class="od-date">{{ $od->donation_date->format('d M Y') }}</div>
                </div>
                <div class="od-amount">Rs {{ number_format($od->amount, 0) }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="detail-card mt-0">
        <div class="detail-card-header"><i class="bi bi-lightning-charge-fill"></i><h5>Quick Actions</h5></div>
        <div style="padding:14px;">
            <div class="d-grid gap-2">
                <a href="{{ route('donations.edit', $donation) }}" class="btn btn-sm py-2" style="background:#fef3dc;color:#b07d10;border-radius:10px;font-weight:600;border:1px solid #f5dfa0;">
                    <i class="bi bi-pencil me-1"></i> Edit Donation
                </a>
                <a href="{{ route('donations.create') }}" class="btn btn-sm py-2" style="background:#ddf5f1;color:#0e7a61;border-radius:10px;font-weight:600;border:1px solid #b2e7de;">
                    <i class="bi bi-plus-circle me-1"></i> Record Another
                </a>
                <a href="{{ route('members.show', $donation->member) }}" class="btn btn-sm py-2" style="background:#f4f6fb;color:#3d5270;border-radius:10px;font-weight:600;border:1px solid #dde3ef;">
                    <i class="bi bi-person me-1"></i> View Member
                </a>
                <a href="{{ route('donations.index') }}" class="btn btn-sm py-2" style="background:#f4f6fb;color:#3d5270;border-radius:10px;font-weight:600;border:1px solid #dde3ef;">
                    <i class="bi bi-arrow-left me-1"></i> All Donations
                </a>
            </div>
        </div>
    </div>

</div>
</div>

@endsection
