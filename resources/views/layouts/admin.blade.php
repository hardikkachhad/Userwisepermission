<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Laravel RBAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background-color: #1e293b; color: #cbd5e1; }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 20px; display: block; border-radius: 6px; margin: 4px 10px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background-color: #334155; }
        .sidebar .nav-header { font-size: 0.75rem; text-transform: uppercase; color: #64748b; padding: 15px 20px 5px; font-weight: 700; }
        .stat-card { border: none; border-radius: 12px; transition: transform 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .stat-card:hover { transform: translateY(-3px); }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 260px;">
            <div class="d-flex align-items-center mb-4 px-2">
                <i class="bi bi-shield-lock-fill fs-3 text-primary me-2"></i>
                <span class="fs-5 fw-bold text-white">Admin Panel</span>
            </div>
            
            <div class="nav-header">Main Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill me-2"></i> Manage Users
            </a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam me-2"></i> Manage Products
            </a>

            <div class="nav-header">User Switch</div>
            <a href="{{ route('user.dashboard') }}">
                <i class="bi bi-person-badge me-2"></i> View User View
            </a>

            <hr class="border-secondary my-4">
            
            <div class="px-2">
                <div class="small text-muted mb-1">Logged in as:</div>
                <div class="fw-bold text-white">{{ Auth::user()->name }}</div>
                <span class="badge bg-danger mt-1"><i class="bi bi-shield-fill-check me-1"></i>Administrator</span>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="mt-4 px-2">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>

        <!-- Main Content Area -->
        <div class="flex-grow-1 p-4" style="min-width: 0;">
            <!-- Top Alert notifications -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
