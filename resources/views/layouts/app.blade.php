<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OBS Admin') — Old Boys Society</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --obs-navy:      #0f1f3d;
            --obs-deep:      #0a1628;
            --obs-gold:      #c9a84c;
            --obs-gold-lt:   #f0d080;
            --obs-slate:     #1e3a5f;
            --obs-muted:     #8494a9;
            --obs-surface:   #f4f6fb;
            --obs-white:     #ffffff;
            --sidebar-w:     260px;
            --nav-h:         64px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--obs-surface);
            color: #1a2b44;
            margin: 0;
        }

        /* ── SIDEBAR ─────────────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--obs-deep);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 0 24px;
            height: var(--nav-h);
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .brand-crest {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--obs-gold), var(--obs-gold-lt));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: var(--obs-deep); font-weight: 700;
            flex-shrink: 0;
        }

        .brand-text { line-height: 1.1; }
        .brand-text strong {
            display: block;
            font-family: 'Playfair Display', serif;
            font-size: 13px;
            color: var(--obs-white);
            letter-spacing: .3px;
        }
        .brand-text span {
            font-size: 10px;
            color: var(--obs-gold);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 0;
        }

        .nav-section-label {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--obs-muted);
            padding: 12px 24px 6px;
            font-weight: 600;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: #a0b3c8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .2s;
            margin: 1px 0;
        }

        .sidebar-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-link:hover {
            color: var(--obs-white);
            background: rgba(255,255,255,.06);
            border-left-color: var(--obs-gold);
        }

        .sidebar-link.active {
            color: var(--obs-gold-lt);
            background: rgba(201,168,76,.12);
            border-left-color: var(--obs-gold);
        }

        .sidebar-link .badge-pill {
            margin-left: auto;
            background: var(--obs-gold);
            color: var(--obs-deep);
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 99px;
            font-weight: 700;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,.07);
            font-size: 12px;
            color: var(--obs-muted);
            text-align: center;
        }

        /* ── TOPBAR ──────────────────────────────────────── */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--nav-h);
            background: var(--obs-white);
            border-bottom: 1px solid #e4e9f0;
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            z-index: 1030;
            transition: left .3s ease;
        }

        .topbar-toggle {
            display: none;
            background: none;border: none;
            font-size: 20px; color: var(--obs-navy);
            cursor: pointer; padding: 4px;
        }

        .topbar-title {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            font-weight: 600;
            color: var(--obs-navy);
            margin: 0;
        }

        .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 8px; }

        .topbar-icon-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: var(--obs-surface);
            border: 1px solid #e4e9f0;
            display: flex; align-items: center; justify-content: center;
            color: var(--obs-navy);
            font-size: 16px;
            cursor: pointer;
            position: relative;
            transition: background .2s;
            text-decoration: none;
        }
        .topbar-icon-btn:hover { background: #e8edf5; color: var(--obs-navy); }

        .notif-dot {
            position: absolute; top: 7px; right: 7px;
            width: 7px; height: 7px;
            background: #e55; border-radius: 50%;
            border: 1.5px solid #fff;
        }

        .topbar-avatar {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--obs-gold), var(--obs-gold-lt));
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700;
            color: var(--obs-deep);
            cursor: pointer;
        }

        /* ── MAIN CONTENT ────────────────────────────────── */
        #main {
            margin-left: var(--sidebar-w);
            margin-top: var(--nav-h);
            min-height: calc(100vh - var(--nav-h));
            padding: 28px;
            transition: margin-left .3s ease;
        }

        /* ── RESPONSIVE ──────────────────────────────────── */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #topbar { left: 0; }
            #main { margin-left: 0; }
            .topbar-toggle { display: flex; }
        }

        /* ── UTILITY ─────────────────────────────────────── */
        .page-header {
            margin-bottom: 24px;
        }
        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--obs-navy);
            margin: 0 0 2px;
        }
        .page-header p {
            font-size: 13px;
            color: var(--obs-muted);
            margin: 0;
        }

        /* scrollbar */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }
    </style>

    @stack('styles')
</head>
<body>

<!-- SIDEBAR OVERLAY (mobile) -->
<div id="sidebarOverlay" onclick="closeSidebar()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1035;"></div>

<!-- ═══ SIDEBAR ═══════════════════════════════════════════ -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-crest">O</div>
        <div class="brand-text">
            <strong>Old Boys Society</strong>
            <span>Admin Portal</span>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Main</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-section-label">Members</div>

        <a href="{{ route('members.index') }}"
           class="sidebar-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Members Management
        </a>

        <div class="nav-section-label">Finance</div>

        <a href="{{ route('registration-payments.index') }}"
           class="sidebar-link {{ request()->routeIs('registration-payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card-fill"></i> Registration Payments
            @if(($pendingReg ?? 0) > 0)
                <span class="badge-pill">{{ $pendingReg }}</span>
            @endif
        </a>

        <a href="{{ route('monthly-payments.index') }}"
           class="sidebar-link {{ request()->routeIs('monthly-payments.*') ? 'active' : '' }}">
            <i class="bi bi-calendar2-check-fill"></i> Monthly Payments
            @if(($pendingMonthly ?? 0) > 0)
                <span class="badge-pill">{{ $pendingMonthly }}</span>
            @endif
        </a>

        <a href="{{ route('donations.index') }}"
           class="sidebar-link {{ request()->routeIs('donations.*') ? 'active' : '' }}">
            <i class="bi bi-gift-fill"></i> Donations
        </a>

        <div class="nav-section-label">Analytics</div>

        <a href="{{ route('reports.index') }}"
           class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line-fill"></i> Details & Reports
        </a>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
            href="{{ route('reports.index') }}">
                <i class="bi bi-person-lines-fill"></i>
                <span>Member Reports</span>
            </a>
        </li>
    </div>

    <div class="sidebar-footer">
        &copy; {{ date('Y') }} Old Boys Society
    </div>
</nav>

<!-- ═══ TOPBAR ════════════════════════════════════════════ -->
<header id="topbar">
    <button class="topbar-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <h2 class="topbar-title">@yield('page-title', 'Dashboard')</h2>

    <div class="topbar-actions">
        <a href="#" class="topbar-icon-btn" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="notif-dot"></span>
        </a>
        <a href="#" class="topbar-icon-btn" title="Settings">
            <i class="bi bi-gear"></i>
        </a>
        <div class="dropdown">
            <div class="topbar-avatar" data-bs-toggle="dropdown" title="{{ auth()->user()->name ?? 'Admin' }}">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1" style="min-width:180px;font-size:13px;">
                <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><a class="dropdown-item py-2" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item py-2 text-danger" type="submit">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- ═══ MAIN ══════════════════════════════════════════════ -->
<main id="main">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').style.display =
            document.getElementById('sidebar').classList.contains('open') ? 'block' : 'none';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').style.display = 'none';
    }
</script>
@stack('scripts')
</body>
</html>
