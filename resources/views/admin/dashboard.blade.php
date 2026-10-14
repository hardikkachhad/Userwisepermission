@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Admin Control Center</h2>
        <p class="text-muted mb-0">Role-Based Access Control (RBAC) & System Overview</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Add New User
        </a>
        <a href="{{ route('products.create') }}" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Product
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small">Total Users</div>
                    <h3 class="fw-bold mb-0">{{ $stats['total_users'] }}</h3>
                </div>
                <i class="bi bi-people fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small">Active Roles</div>
                    <h3 class="fw-bold mb-0">{{ $stats['total_roles'] }}</h3>
                </div>
                <i class="bi bi-shield-shaded fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-warning text-dark p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-black-50 small">Permissions Defined</div>
                    <h3 class="fw-bold mb-0">{{ $stats['total_permissions'] }}</h3>
                </div>
                <i class="bi bi-key-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-info text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 small">Total Products</div>
                    <h3 class="fw-bold mb-0">{{ $stats['total_products'] }}</h3>
                </div>
                <i class="bi bi-boxes fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Users and Custom Permission Overview -->
<div class="row g-4">
    <!-- User Permissions Matrix -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-gear text-primary me-2"></i> Users & User-wise Permissions
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-person-plus-fill me-1"></i> Add User
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                        Manage All ({{ $stats['total_users'] }})
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Assigned Role</th>
                                <th>Direct User Permissions (Custom)</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $u)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $u->name }}</div>
                                    <div class="text-muted small">{{ $u->email }}</div>
                                </td>
                                <td>
                                    @forelse($u->roles as $role)
                                        <span class="badge {{ $role->slug === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted small">No Role</span>
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($u->permissions as $perm)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle me-1">
                                            + {{ $perm->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted small fst-italic">Inherits from role</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary" title="Edit User & Permissions">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-center py-2 border-top">
                <a href="{{ route('admin.users.index') }}" class="small text-decoration-none fw-semibold">
                    Manage All Users, Roles & Permissions <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Defined Roles Matrix -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-award-fill text-warning me-2"></i> System Roles
                </h5>
            </div>
            <div class="card-body">
                @foreach($roles as $role)
                <div class="p-3 border rounded mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-capitalize text-dark">{{ $role->name }}</strong>
                        <span class="badge bg-secondary">{{ $role->users_count }} Users</span>
                    </div>
                    <p class="small text-muted mb-2">{{ $role->description }}</p>
                    <div class="small">
                        <span class="fw-semibold text-muted">Role Permissions:</span>
                        <div class="mt-1 d-flex flex-wrap gap-1">
                            @foreach($role->permissions as $p)
                                <span class="badge bg-white text-dark border">{{ $p->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
