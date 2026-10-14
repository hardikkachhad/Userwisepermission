@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">
            <i class="bi bi-people-fill text-primary me-2"></i> User Management
        </h2>
        <p class="text-muted mb-0">Create users, assign system roles, and grant user-wise direct permissions</p>
    </div>
    <div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary px-3 shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Add New User
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-4">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">-- Filter by Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Assigned Roles</th>
                        <th>Direct Permissions (Custom)</th>
                        <th>Effective Total</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-muted font-monospace">{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">
                                        {{ $user->name }}
                                        @if($user->id === Auth::id())
                                            <span class="badge bg-secondary ms-1">You</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @forelse($user->roles as $role)
                                <span class="badge {{ $role->slug === 'admin' ? 'bg-danger' : 'bg-primary' }} me-1">
                                    <i class="bi bi-shield-check me-1"></i>{{ $role->name }}
                                </span>
                            @empty
                                <span class="badge bg-light text-muted border">No Role</span>
                            @endforelse
                        </td>
                        <td>
                            @forelse($user->permissions as $perm)
                                <span class="badge bg-success-subtle text-success border border-success-subtle me-1 mb-1">
                                    + {{ $perm->name }}
                                </span>
                            @empty
                                <span class="text-muted small fst-italic">No direct perms (Role inherited)</span>
                            @endforelse
                        </td>
                        <td>
                            @php
                                $effectiveCount = $user->getAllPermissions()->count();
                            @endphp
                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                {{ $effectiveCount }} Perms
                            </span>
                        </td>
                        <td class="small text-muted">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit User, Roles & Permissions">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>

                            @if($user->id !== Auth::id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete user \'{{ $user->name }}\'?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-outline-secondary opacity-50" disabled title="Cannot delete yourself">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
