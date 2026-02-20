@extends('layouts.app')

@section('title', $member->name_with_initials)
@section('page-title', 'Member Details')

@push('styles')
<style>
:root { --navy:#0f1f3d; --gold:#c9a84c; }

.profile-hero {
    background:linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
    border-radius:16px;
    padding:28px 28px 24px;
    color:#fff;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:22px;
    flex-wrap:wrap;
    animation:fadeUp .3s ease;
}
.profile-avatar {
    width:72px; height:72px;
    border-radius:18px;
    background:linear-gradient(135deg, var(--gold), #f0d080);
    display:flex; align-items:center; justify-content:center;
    font-family:'Playfair Display',serif;
    font-size:30px; font-weight:700;
    color:var(--navy);
    flex-shrink:0;
}
.profile-name {
    font-family:'Playfair Display',serif;
    font-size:22px; font-weight:700;
    margin:0 0 4px;
}
.profile-sub { font-size:13px; color:#7a9abc; margin:0; }
.profile-badges { margin-top:10px; display:flex; flex-wrap:wrap; gap:6px; }
.profile-badge {
    font-size:11px; font-weight:600;
    padding:3px 12px; border-radius:20px;
    background:rgba(255,255,255,.12);
    color:#fff;
}
.profile-badge.gold { background:rgba(201,168,76,.25); color:var(--gold); }
.profile-actions { margin-left:auto; display:flex; gap:8px; flex-wrap:wrap; }

.detail-card {
    background:#fff;
    border:1px solid #e4e9f0;
    border-radius:14px;
    overflow:hidden;
    margin-bottom:18px;
    animation:fadeUp .35s ease both;
}
.detail-card-header {
    padding:14px 20px;
    border-bottom:1px solid #f0f3f8;
    background:#fafbfd;
    display:flex; align-items:center; gap:9px;
}
.detail-card-header i { font-size:16px; color:var(--gold); }
.detail-card-header h5 {
    font-family:'Playfair Display',serif;
    font-size:14px; font-weight:700;
    color:var(--navy); margin:0;
}
.detail-grid {
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap:0;
}
.detail-item {
    padding:14px 20px;
    border-bottom:1px solid #f6f8fb;
    border-right:1px solid #f6f8fb;
}
.detail-item:last-child { border-bottom:none; }
.detail-label {
    font-size:10.5px; font-weight:600;
    text-transform:uppercase; letter-spacing:1px;
    color:#8494a9; margin-bottom:4px;
}
.detail-value {
    font-size:14px; color:#1a2b44; font-weight:500;
}
.detail-value.mono { font-family:monospace; letter-spacing:.5px; }
.detail-value.empty { color:#b0bec5; font-style:italic; font-weight:400; }

.child-tag {
    display:inline-block;
    background:#f4f6fb; border:1px solid #e4e9f0;
    border-radius:10px; padding:8px 14px;
    margin:4px; font-size:13px;
}
.child-tag strong { display:block; color:var(--navy); font-size:13px; }
.child-tag span   { color:#8494a9; font-size:11.5px; }

.btn-edit-profile {
    background:var(--gold); color:var(--navy);
    border:none; border-radius:10px;
    padding:9px 18px; font-size:13px; font-weight:700;
    text-decoration:none;
    display:inline-flex; align-items:center; gap:6px;
    transition:background .2s;
}
.btn-edit-profile:hover { background:#f0d080; color:var(--navy); }

.btn-back-list {
    background:rgba(255,255,255,.12); color:#fff;
    border:1px solid rgba(255,255,255,.2); border-radius:10px;
    padding:9px 16px; font-size:13px; font-weight:600;
    text-decoration:none;
    display:inline-flex; align-items:center; gap:6px;
}
.btn-back-list:hover { background:rgba(255,255,255,.2); color:#fff; }

@keyframes fadeUp {
    from{opacity:0;transform:translateY(14px)}
    to{opacity:1;transform:translateY(0)}
}
</style>
@endpush

@section('content')

{{-- ── PROFILE HERO ─────────────────────────────────── --}}
<div class="profile-hero">
    <div class="profile-avatar">{{ strtoupper(substr($member->name_with_initials, 0, 1)) }}</div>
    <div>
        <h2 class="profile-name">{{ $member->name_with_initials }}</h2>
        <p class="profile-sub">{{ $member->occupation }} · {{ $member->current_city }}</p>
        <div class="profile-badges">
            <span class="profile-badge gold">
                <i class="bi bi-hash"></i>
                {{ $member->membership_number ?? 'No Membership No.' }}
            </span>
            <span class="profile-badge">
                <i class="bi bi-{{ $member->gender === 'Male' ? 'gender-male' : 'gender-female' }} me-1"></i>
                {{ $member->gender }}
            </span>
            <span class="profile-badge">
                <i class="bi bi-heart{{ $member->married ? '-fill' : '' }} me-1"></i>
                {{ $member->married_label }}
            </span>
            <span class="profile-badge">
                Age {{ $member->age }}
            </span>
        </div>
    </div>
    <div class="profile-actions">
        <a href="{{ route('members.index') }}" class="btn-back-list">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="{{ route('members.edit', $member) }}" class="btn-edit-profile">
            <i class="bi bi-pencil-fill"></i> Edit
        </a>
        <form method="POST" action="{{ route('members.destroy', $member) }}"
              onsubmit="return confirm('Delete this member permanently?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-back-list" style="background:rgba(220,53,69,.25);border-color:rgba(220,53,69,.4);cursor:pointer;">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row g-3">
<div class="col-lg-8">

    {{-- Personal --}}
    <div class="detail-card" style="animation-delay:.05s">
        <div class="detail-card-header">
            <i class="bi bi-person-fill"></i>
            <h5>Personal Information</h5>
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Full Name</div>
                <div class="detail-value">{{ $member->name_with_initials }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">NIC Number</div>
                <div class="detail-value mono">{{ $member->nic_number }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Birthday</div>
                <div class="detail-value">{{ $member->birthday->format('d F Y') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Age</div>
                <div class="detail-value">{{ $member->age }} years</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Phone</div>
                <div class="detail-value">
                    <a href="tel:{{ $member->phone_number }}" style="color:inherit;text-decoration:none;">
                        <i class="bi bi-telephone me-1" style="color:var(--gold);"></i>{{ $member->phone_number }}
                    </a>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Email</div>
                <div class="detail-value">
                    @if($member->email)
                        <a href="mailto:{{ $member->email }}" style="color:inherit;text-decoration:none;">{{ $member->email }}</a>
                    @else
                        <span class="empty">Not provided</span>
                    @endif
                </div>
            </div>
            <div class="detail-item" style="grid-column:1/-1;">
                <div class="detail-label">Address</div>
                <div class="detail-value">{{ $member->address }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Current City</div>
                <div class="detail-value">{{ $member->current_city }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Occupation</div>
                <div class="detail-value">{{ $member->occupation }}</div>
            </div>
        </div>
    </div>

    {{-- Electoral --}}
    <div class="detail-card" style="animation-delay:.1s">
        <div class="detail-card-header">
            <i class="bi bi-geo-alt-fill"></i>
            <h5>Electoral &amp; Administrative Division</h5>
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">District</div>
                <div class="detail-value">{{ $member->district }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Election Division</div>
                <div class="detail-value">{{ $member->election_division }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Grama Niladhari Division</div>
                <div class="detail-value">{{ $member->grama_niladhari_division }}</div>
            </div>
        </div>
    </div>

    {{-- School --}}
    <div class="detail-card" style="animation-delay:.15s">
        <div class="detail-card-header">
            <i class="bi bi-mortarboard-fill"></i>
            <h5>School Information</h5>
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Register Year</div>
                <div class="detail-value">{{ $member->school_register_year ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Admission Number</div>
                <div class="detail-value">{{ $member->admission_number ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Date Joined</div>
                <div class="detail-value">
                    {{ $member->date_joined_school ? $member->date_joined_school->format('d M Y') : '—' }}
                </div>
            </div>
        </div>
    </div>

</div>

<div class="col-lg-4">

    {{-- Children --}}
    <div class="detail-card" style="animation-delay:.1s">
        <div class="detail-card-header">
            <i class="bi bi-people-fill"></i>
            <h5>Children &amp; Schools</h5>
        </div>
        <div style="padding:16px;">
            @if(!empty($member->children_info))
                @foreach($member->children_info as $i => $child)
                <div class="child-tag">
                    <strong><i class="bi bi-person me-1"></i>{{ $child['name'] ?: 'Unnamed' }}</strong>
                    <span><i class="bi bi-building me-1"></i>{{ $child['school'] ?: 'School not specified' }}</span>
                </div>
                @endforeach
            @else
                <p style="color:#b0bec5;font-size:13px;font-style:italic;margin:0;">No children recorded.</p>
            @endif
        </div>
    </div>

    {{-- Siblings --}}
    @if(!$member->married && $member->siblings_info)
    <div class="detail-card" style="animation-delay:.15s">
        <div class="detail-card-header">
            <i class="bi bi-person-lines-fill"></i>
            <h5>Siblings Information</h5>
        </div>
        <div style="padding:16px;font-size:13.5px;color:#2c3e55;line-height:1.6;">
            {{ $member->siblings_info }}
        </div>
    </div>
    @endif

    {{-- Meta --}}
    <div class="detail-card" style="animation-delay:.2s">
        <div class="detail-card-header">
            <i class="bi bi-info-circle-fill"></i>
            <h5>Record Info</h5>
        </div>
        <div style="padding:16px;">
            <div class="detail-item" style="padding:8px 0;border:none;">
                <div class="detail-label">Registered On</div>
                <div class="detail-value">{{ $member->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="detail-item" style="padding:8px 0;border:none;">
                <div class="detail-label">Last Updated</div>
                <div class="detail-value">{{ $member->updated_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>
    </div>

</div>
</div>

@endsection
