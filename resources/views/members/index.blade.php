@extends('layouts.app')

@section('title', 'Members')
@section('page-title', 'Members Management')

@push('styles')
<style>
    :root {
        --navy: #0f1f3d;
        --gold: #c9a84c;
        --surface: #f4f6fb;
    }

    .filter-card {
        background:#fff;
        border:1px solid #e4e9f0;
        border-radius:14px;
        padding:18px 20px;
        margin-bottom:20px;
        animation: fadeUp .3s ease;
    }

    .search-wrap { position:relative; }
    .search-wrap i {
        position:absolute; left:13px; top:50%;
        transform:translateY(-50%);
        color:#8494a9; font-size:15px;
    }
    .search-wrap input {
        padding-left:38px;
        border-radius:10px;
        border:1px solid #dde3ef;
        height:40px;
        font-size:13.5px;
    }
    .search-wrap input:focus {
        border-color:var(--gold);
        box-shadow:0 0 0 3px rgba(201,168,76,.12);
        outline:none;
    }

    .filter-select {
        border-radius:10px;
        border:1px solid #dde3ef;
        height:40px;
        font-size:13.5px;
        padding:0 12px;
        color:#2c3e55;
    }
    .filter-select:focus {
        border-color:var(--gold);
        box-shadow:0 0 0 3px rgba(201,168,76,.12);
        outline:none;
    }

    /* Table card */
    .table-card {
        background:#fff;
        border:1px solid #e4e9f0;
        border-radius:14px;
        overflow:hidden;
        animation: fadeUp .35s ease;
    }

    .table-card-header {
        padding:16px 20px;
        border-bottom:1px solid #f0f3f8;
        display:flex;
        align-items:center;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:10px;
    }

    .table-card-title {
        font-family:'Playfair Display', serif;
        font-size:15px;
        font-weight:700;
        color:var(--navy);
        margin:0;
    }

    .obs-table { width:100%; border-collapse:collapse; }
    .obs-table th {
        font-size:10.5px;
        text-transform:uppercase;
        letter-spacing:1.2px;
        color:#8494a9;
        font-weight:600;
        padding:11px 16px;
        border-bottom:1px solid #f0f3f8;
        background:#fafbfd;
        white-space:nowrap;
    }
    .obs-table td {
        padding:13px 16px;
        font-size:13.5px;
        color:#2c3e55;
        border-bottom:1px solid #f6f8fb;
        vertical-align:middle;
    }
    .obs-table tr:last-child td { border-bottom:none; }
    .obs-table tbody tr:hover td { background:#fafbfd; }

    .m-avatar {
        width:34px; height:34px;
        border-radius:9px;
        background:linear-gradient(135deg,#1e3a5f,#0f1f3d);
        color:var(--gold);
        display:inline-flex; align-items:center; justify-content:center;
        font-size:13px; font-weight:700;
        margin-right:10px; flex-shrink:0;
    }

    .badge-gender-m { background:#e8f0fd; color:#1a5dc0; }
    .badge-gender-f { background:#fde8f5; color:#a0178e; }
    .badge-married  { background:#e8f7ee; color:#1a8a45; }
    .badge-single   { background:#f4f6fb; color:#5a7194; }

    .status-pill {
        font-size:11px; font-weight:600;
        padding:3px 10px; border-radius:20px;
        white-space:nowrap;
    }

    .action-btn {
        width:30px; height:30px;
        border-radius:8px;
        display:inline-flex; align-items:center; justify-content:center;
        font-size:13px;
        border:1px solid #e4e9f0;
        background:#f4f6fb;
        color:#3d5270;
        transition:all .15s;
        text-decoration:none;
    }
    .action-btn:hover { transform:scale(1.12); }
    .action-btn.view:hover  { background:#ddf5f1; color:#0e9578; border-color:#b2e7de; }
    .action-btn.edit:hover  { background:#fef3dc; color:#c9a84c; border-color:#f5dfa0; }
    .action-btn.del:hover   { background:#fdecea; color:#c0392b; border-color:#f5c0bc; }

    .empty-state {
        text-align:center;
        padding:52px 20px;
        color:#8494a9;
    }
    .empty-state i { font-size:40px; display:block; margin-bottom:12px; opacity:.4; }
    .empty-state p { font-size:14px; margin:0; }

    .btn-new-member {
        background:var(--navy); color:#fff;
        border:none; border-radius:10px;
        padding:9px 18px;
        font-size:13px; font-weight:600;
        display:inline-flex; align-items:center; gap:6px;
        text-decoration:none;
        transition:background .2s;
    }
    .btn-new-member:hover { background:#1e3a5f; color:#fff; }

    .btn-filter {
        background:var(--surface); color:var(--navy);
        border:1px solid #dde3ef; border-radius:10px;
        padding:8px 14px; font-size:13px; font-weight:600;
        cursor:pointer;
    }
    .btn-filter:hover { background:#e4e9f0; }

    @keyframes fadeUp {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .pagination { margin:0; }
    .page-link { border-radius:8px !important; font-size:13px; }
</style>
@endpush

@section('content')

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h1>Members</h1>
        <p>Manage all Old Boys Society member records.</p>
    </div>
    <a href="{{ route('members.create') }}" class="btn-new-member">
        <i class="bi bi-person-plus-fill"></i> Register New Member
    </a>
</div>

{{-- ── FILTER BAR ───────────────────────────────────── --}}
<div class="filter-card">
    <form method="GET" action="{{ route('members.index') }}" id="filterForm">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by name, NIC, or phone…"
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="gender" class="filter-select form-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Genders</option>
                    <option value="Male"   {{ request('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="married" class="filter-select form-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Status</option>
                    <option value="yes" {{ request('married') === 'yes' ? 'selected' : '' }}>Married</option>
                    <option value="no"  {{ request('married') === 'no'  ? 'selected' : '' }}>Unmarried</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-filter w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
            @if(request()->hasAny(['search','gender','married']))
            <div class="col-md-1">
                <a href="{{ route('members.index') }}" class="btn-filter w-100 text-center" style="display:block;color:#c0392b;border-color:#f5c0bc;">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
            @endif
        </div>
    </form>
</div>

{{-- ── TABLE ─────────────────────────────────────────── --}}
<div class="table-card">
    <div class="table-card-header">
        <h3 class="table-card-title">
            <i class="bi bi-people me-2" style="color:var(--gold);"></i>
            Member Records
            <span style="font-size:12px;font-weight:500;color:#8494a9;margin-left:6px;">
                ({{ $members->total() }} total)
            </span>
        </h3>
        <div style="font-size:12px;color:#8494a9;">
            Showing {{ $members->firstItem() }}–{{ $members->lastItem() }} of {{ $members->total() }}
        </div>
    </div>

    <div class="table-responsive">
        <table class="obs-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>NIC</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>City</th>
                    <th>Reg. Year</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td style="color:#8494a9;font-size:12px;">
                        {{ $member->membership_number ?? '—' }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="m-avatar">{{ strtoupper(substr($member->name_with_initials, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:13.5px;">{{ $member->name_with_initials }}</div>
                                <div style="font-size:11px;color:#8494a9;">{{ $member->occupation }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-family:monospace;font-size:12.5px;letter-spacing:.5px;">
                        {{ $member->nic_number }}
                    </td>
                    <td>{{ $member->phone_number }}</td>
                    <td>
                        <span class="status-pill {{ $member->gender === 'Male' ? 'badge-gender-m' : 'badge-gender-f' }}">
                            <i class="bi bi-{{ $member->gender === 'Male' ? 'gender-male' : 'gender-female' }} me-1"></i>
                            {{ $member->gender }}
                        </span>
                    </td>
                    <td>
                        <span class="status-pill {{ $member->married ? 'badge-married' : 'badge-single' }}">
                            {{ $member->married_label }}
                        </span>
                    </td>
                    <td>{{ $member->current_city }}</td>
                    <td>{{ $member->school_register_year ?? '—' }}</td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <a href="{{ route('members.show', $member) }}" class="action-btn view" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('members.edit', $member) }}" class="action-btn edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('members.destroy', $member) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($member->name_with_initials) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn del" title="Delete" style="cursor:pointer;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="bi bi-people"></i>
                            <p>No members found. <a href="{{ route('members.create') }}">Register the first member →</a></p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f0f3f8;display:flex;justify-content:flex-end;">
        {{ $members->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Live search on Enter
document.querySelector('input[name="search"]').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.target.form.submit(); }
});
</script>
@endpush
