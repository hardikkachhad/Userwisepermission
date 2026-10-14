@extends('layouts.admin')

@section('title', 'Edit User: ' . $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">
            <i class="bi bi-pencil-square text-primary me-2"></i> Edit User: {{ $user->name }}
        </h2>
        <p class="text-muted mb-0">Modify user profile, system roles, and direct permissions</p>
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

<form action="{{ route('admin.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Account Details Column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i> Account Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-dark mb-2">Change Password (Optional)</h6>
                    <p class="small text-muted mb-3">Leave blank to keep the current password.</p>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter new password">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter new password">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Effective Permissions Preview Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-eye-fill text-info me-2"></i> Current Effective Permissions
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Permissions this user currently has access to:</p>
                    <div class="d-flex flex-wrap gap-1">
                        @forelse($user->getAllPermissions() as $effPerm)
                            <span class="badge bg-light text-dark border p-1 px-2">
                                <i class="bi bi-check2 text-success me-1"></i>{{ $effPerm->name }}
                            </span>
                        @empty
                            <span class="text-muted small">No permissions active.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Role & Direct Permissions Column -->
        <div class="col-lg-7">
            <!-- Roles Section -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-shield-check text-warning me-2"></i> System Roles
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Check the roles to assign to this user:</p>
                    <div class="row g-3">
                        @foreach($roles as $role)
                        @php
                            $isRoleChecked = is_array(old('roles')) ? in_array($role->slug, old('roles')) : in_array($role->slug, $userRoleSlugs);
                        @endphp
                        <div class="col-md-6">
                            <label class="d-flex align-items-start p-3 border rounded h-100 {{ $isRoleChecked ? 'bg-primary bg-opacity-10 border-primary' : 'bg-light' }}" style="cursor: pointer;">
                                <input type="checkbox" name="roles[]" value="{{ $role->slug }}" class="form-check-input mt-1 me-3"
                                    {{ $isRoleChecked ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center">
                                        {{ $role->name }}
                                        <span class="badge {{ $role->slug === 'admin' ? 'bg-danger' : 'bg-primary' }} ms-2 small">
                                            {{ $role->slug }}
                                        </span>
                                    </div>
                                    <div class="small text-muted mt-1">{{ $role->description ?? 'System role' }}</div>
                                    <div class="small text-secondary mt-2">
                                        <strong>Role Perms:</strong>
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
                            <i class="bi bi-key-fill text-success me-2"></i> User Direct Permissions
                        </h5>
                        <small class="text-muted">Override or supplement role permissions directly for this user</small>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleAllPerms(true)">Select All</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleAllPerms(false)">Clear All</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($permissions as $perm)
                        @php
                            $isPermChecked = is_array(old('permissions')) ? in_array($perm->slug, old('permissions')) : in_array($perm->slug, $userPermissionSlugs);
                        @endphp
                        <div class="col-md-6">
                            <div class="form-check p-2 px-4 border rounded h-100 {{ $isPermChecked ? 'bg-success bg-opacity-10 border-success' : 'bg-white' }}">
                                <input class="form-check-input perm-checkbox" type="checkbox" name="permissions[]" value="{{ $perm->slug }}" id="perm_{{ $perm->id }}"
                                    {{ $isPermChecked ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="perm_{{ $perm->id }}" style="cursor: pointer;">
                                    <span class="fw-semibold text-dark d-block">
                                        {{ $perm->name }}
                                        @if($isPermChecked)
                                            <span class="badge bg-success small ms-1">Direct</span>
                                        @endif
                                    </span>
                                    <span class="font-monospace small text-muted">slug: {{ $perm->slug }}</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="col-12 text-end mt-4">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2 px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                <i class="bi bi-check-circle-fill me-1"></i> Update User Details & Permissions
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
