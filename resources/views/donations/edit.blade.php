@extends('layouts.app')

@section('title', 'Edit Donation')
@section('page-title', 'Edit Donation')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; --teal:#0e9578; }
.obs-card { background:#fff; border:1px solid #e4e9f0; border-radius:16px; overflow:hidden; animation:fadeUp .3s ease both; margin-bottom:18px; }
.obs-card-header { padding:15px 22px; border-bottom:1px solid #f0f3f8; background:#fafbfd; display:flex; align-items:center; gap:10px; }
.obs-card-header i { font-size:17px; color:var(--gold); }
.obs-card-header h4 { font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:var(--navy); margin:0; }
.obs-card-body { padding:22px; }
.form-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.8px; color:#5a7194; margin-bottom:6px; }
.req { color:#c0392b; }
.form-control, .form-select { border-radius:10px; border:1.5px solid #dde3ef; font-size:14px; padding:10px 14px; color:#1a2b44; transition:border-color .2s, box-shadow .2s; }
.form-control:focus, .form-select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); outline:none; }
.form-control.is-invalid { border-color:#dc3545; }
.invalid-feedback { font-size:11.5px; }
.amount-input-wrap { position:relative; }
.amount-prefix { position:absolute; left:14px; top:50%; transform:translateY(-50%); font-weight:700; color:#8494a9; }
.amount-input  { padding-left:36px !important; }

/* Select2 */
.select2-container .select2-selection--single { border-radius:10px !important; border:1.5px solid #dde3ef !important; height:44px !important; display:flex !important; align-items:center !important; }
.select2-container--default.select2-container--focus .select2-selection--single { border-color:var(--gold) !important; box-shadow:0 0 0 3px rgba(201,168,76,.15) !important; outline:none !important; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:44px !important; padding-left:14px !important; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height:44px !important; }
.select2-container--default .select2-results__option--highlighted { background:var(--navy) !important; }
.select2-dropdown { border-radius:12px !important; border:1px solid #e4e9f0 !important; box-shadow:0 8px 28px rgba(15,31,61,.12) !important; }

.btn-submit { background:linear-gradient(135deg,var(--teal),#0b7a61); color:#fff; border:none; border-radius:10px; padding:12px 24px; font-size:14px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:opacity .2s; }
.btn-submit:hover { opacity:.88; }
.btn-cancel { background:#f4f6fb; color:#3d5270; border:1px solid #dde3ef; border-radius:10px; padding:11px 20px; font-size:14px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:7px; }
.btn-cancel:hover { background:#e4e9f0; color:var(--navy); }

.member-bar { background:linear-gradient(135deg,var(--navy),#1e3a5f); border-radius:12px; padding:14px 18px; display:flex; align-items:center; gap:12px; color:#fff; margin-bottom:16px; }
.bar-avatar { width:38px; height:38px; border-radius:9px; background:linear-gradient(135deg,var(--gold),#f0d080); color:var(--navy); display:flex; align-items:center; justify-content:center; font-family:'Playfair Display',serif; font-size:16px; font-weight:700; flex-shrink:0; }
.bar-name { font-weight:700; font-size:14px; }
.bar-sub  { font-size:11.5px; color:#7a9abc; }

@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

<div class="page-header mb-3">
    <h1>Edit Donation</h1>
    <p>Update the donation record for <strong>{{ $donation->member->name_with_initials }}</strong>.</p>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Please fix these errors:</strong>
    <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li style="font-size:13px;">{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('donations.update', $donation) }}">
@csrf @method('PUT')

    {{-- Member (read-only display + hidden input) --}}
    <div class="obs-card">
        <div class="obs-card-header"><i class="bi bi-person-fill"></i><h4>Member</h4></div>
        <div class="obs-card-body">
            <input type="hidden" name="member_id" value="{{ $donation->member_id }}">
            <div class="member-bar">
                <div class="bar-avatar">{{ strtoupper(substr($donation->member->name_with_initials, 0, 1)) }}</div>
                <div>
                    <div class="bar-name">{{ $donation->member->name_with_initials }}</div>
                    <div class="bar-sub">{{ $donation->member->nic_number }} · {{ $donation->member->phone_number }}</div>
                </div>
                <a href="{{ route('members.show', $donation->member) }}" style="margin-left:auto;font-size:12px;color:var(--gold);text-decoration:none;">
                    View Profile <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div style="font-size:11.5px;color:#8494a9;"><i class="bi bi-info-circle me-1"></i>To change the member, delete this record and create a new donation.</div>
        </div>
    </div>

    {{-- Donation Details --}}
    <div class="obs-card">
        <div class="obs-card-header"><i class="bi bi-gift-fill"></i><h4>Donation Details</h4></div>
        <div class="obs-card-body">
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label">Reason for Donation <span class="req">*</span></label>
                    <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror"
                           value="{{ old('reason', $donation->reason) }}" required>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Amount <span class="req">*</span></label>
                    <div class="amount-input-wrap">
                        <span class="amount-prefix">Rs</span>
                        <input type="number" name="amount" class="form-control amount-input @error('amount') is-invalid @enderror"
                               value="{{ old('amount', $donation->amount) }}" step="0.01" min="1" required>
                    </div>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Donation Date <span class="req">*</span></label>
                    <input type="date" name="donation_date" class="form-control @error('donation_date') is-invalid @enderror"
                           value="{{ old('donation_date', $donation->donation_date->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}" required>
                    @error('donation_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status <span class="req">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="received" {{ old('status', $donation->status) === 'received' ? 'selected' : '' }}>Received</option>
                        <option value="pending"  {{ old('status', $donation->status) === 'pending'  ? 'selected' : '' }}>Pending</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Receipt Number</label>
                    <input type="text" name="receipt_number" class="form-control"
                           value="{{ old('receipt_number', $donation->receipt_number) }}" placeholder="optional">
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="optional">{{ old('notes', $donation->notes) }}</textarea>
                </div>

            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3 pb-2">
        <button type="submit" class="btn-submit">
            <i class="bi bi-check-circle-fill"></i> Update Donation
        </button>
        <a href="{{ route('donations.show', $donation) }}" class="btn-cancel">
            <i class="bi bi-arrow-left"></i> Cancel
        </a>
    </div>

</form>
@endsection
