@extends('layouts.admin')

@section('title', 'Create New User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">
            <i class="bi bi-person-plus-fill text-primary me-2"></i> Create New User
        </h2>
        <p class="text-muted mb-0">Add a new user and assign their roles and custom direct permissions</p>
    </div>
    <div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Users List
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the errors below:</h6>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <!-- User Account Details -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i> Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Rahul Sharma" required>
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="e.g. rahul@example.com" required>
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 6 characters" required>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded border mt-4">
                        <div class="small text-muted">
                            <i class="bi bi-info-circle-fill text-primary me-1"></i>
                            <strong>RBAC Logic:</strong> Roles provide grouped permissions. Direct permissions allow you to give specific extra capabilities to individual users regardless of their role.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role & Direct Permissions Assignment -->
        <div class="col-lg-7">
            <!-- Roles Section -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-shield-check text-warning me-2"></i> Assign System Roles
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Select one or more roles for this user:</p>
                    <div class="row g-3">
                        @foreach($roles as $role)
                        <div class="col-md-6">
                            <label class="d-flex align-items-start p-3 border rounded h-100 bg-light" style="cursor: pointer;">
                                <input type="checkbox" name="roles[]" value="{{ $role->slug }}" class="form-check-input mt-1 me-3 role-checkbox"
                                    {{ (is_array(old('roles')) && in_array($role->slug, old('roles'))) || (!old('roles') && $role->slug === 'user') ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center">
                                        {{ $role->name }}
                                        <span class="badge {{ $role->slug === 'admin' ? 'bg-danger' : 'bg-primary' }} ms-2 small">
                                            {{ $role->slug }}
                                        </span>
                                    </div>
                                    <div class="small text-muted mt-1">{{ $role->description ?? 'Standard system role.' }}</div>
                                    <div class="small text-secondary mt-2">
                                        <strong>Default Perms:</strong>
                                        {{ $role->permissions->pluck('name')->implode(', ') ?: 'None' }}
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Direct Permissions Section -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-key-fill text-success me-2"></i> Custom Direct Permissions (User-Wise)
                        </h5>
                        <small class="text-muted">Grant individual permissions directly to this user</small>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleAllPerms(true)">Select All</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleAllPerms(false)">Clear All</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($permissions as $perm)
                        <div class="col-md-6">
                            <div class="form-check p-2 px-4 border rounded bg-white h-100 hover-shadow">
                                <input class="form-check-input perm-checkbox" type="checkbox" name="permissions[]" value="{{ $perm->slug }}" id="perm_{{ $perm->id }}"
                                    {{ is_array(old('permissions')) && in_array($perm->slug, old('permissions')) ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="perm_{{ $perm->id }}" style="cursor: pointer;">
                                    <span class="fw-semibold text-dark d-block">{{ $perm->name }}</span>
                                    <span class="font-monospace small text-muted">slug: {{ $perm->slug }}</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="col-12 text-end mt-4">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2 px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                <i class="bi bi-check-circle-fill me-1"></i> Save and Create User
            </button>
        </div>
    </div>
</form>

<script>
    function toggleAllPerms(check) {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = check);
    }
</script>
@endsection
