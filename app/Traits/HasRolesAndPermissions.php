<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRolesAndPermissions
{
    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Direct permissions granted to the user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param mixed ...$roles
     * @return bool
     */
    public function hasRole(...$roles): bool
    {
        $roleIdentifiers = collect($roles)
            ->flatten()
            ->map(fn ($role) => $role instanceof Role ? $role->slug : $role);

        return $this->roles->contains(function ($userRole) use ($roleIdentifiers) {
            return $roleIdentifiers->contains($userRole->slug)
                || $roleIdentifiers->contains($userRole->name)
                || $roleIdentifiers->contains((string) $userRole->id);
        });
    }

    /**
     * Check if user has any of the given roles (alias for hasRole).
     *
     * @param mixed ...$roles
     * @return bool
     */
    public function hasAnyRole(...$roles): bool
    {
        return $this->hasRole(...$roles);
    }

    /**
     * Check if user has all of the given roles.
     *
     * @param mixed ...$roles
     * @return bool
     */
    public function hasAllRoles(...$roles): bool
    {
        $roleIdentifiers = collect($roles)
            ->flatten()
            ->map(fn ($role) => $role instanceof Role ? $role->slug : $role);

        foreach ($roleIdentifiers as $roleIdentifier) {
            $hasRole = $this->roles->contains(function ($userRole) use ($roleIdentifier) {
                return $userRole->slug === $roleIdentifier
                    || $userRole->name === $roleIdentifier
                    || (string) $userRole->id === (string) $roleIdentifier;
            });

            if (! $hasRole) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assign one or multiple roles to the user.
     *
     * @param mixed ...$roles
     * @return $this
     */
    public function assignRole(...$roles): self
    {
        $roleSlugs = collect($roles)->flatten()->map(function ($role) {
            return $role instanceof Role ? $role->slug : $role;
        });

        $roleModels = Role::whereIn('slug', $roleSlugs)
            ->orWhereIn('id', $roleSlugs)
            ->get();

        $this->roles()->syncWithoutDetaching($roleModels);
        $this->load('roles');

        return $this;
    }

    /**
     * Remove one or multiple roles from the user.
     *
     * @param mixed ...$roles
     * @return $this
     */
    public function removeRole(...$roles): self
    {
        $roleSlugs = collect($roles)->flatten()->map(function ($role) {
            return $role instanceof Role ? $role->slug : $role;
        });

        $roleModels = Role::whereIn('slug', $roleSlugs)
            ->orWhereIn('id', $roleSlugs)
            ->get();

        $this->roles()->detach($roleModels);
        $this->load('roles');

        return $this;
    }

    /**
     * Sync user roles.
     *
     * @param mixed ...$roles
     * @return $this
     */
    public function syncRoles(...$roles): self
    {
        $roleSlugs = collect($roles)->flatten()->map(function ($role) {
            return $role instanceof Role ? $role->slug : $role;
        });

        $roleModels = Role::whereIn('slug', $roleSlugs)
            ->orWhereIn('id', $roleSlugs)
            ->get();

        $this->roles()->sync($roleModels);
        $this->load('roles');

        return $this;
    }

    /**
     * Check if user has permission (either direct or inherited through any role).
     *
     * @param Permission|string $permission
     * @return bool
     */
    public function hasPermissionTo($permission): bool
    {
        // Super admin bypass: role 'admin' has full privileges
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->hasDirectPermission($permission) || $this->hasPermissionThroughRole($permission);
    }

    /**
     * Check if user has permission directly assigned.
     *
     * @param Permission|string $permission
     * @return bool
     */
    public function hasDirectPermission($permission): bool
    {
        $slug = $permission instanceof Permission ? $permission->slug : $permission;

        return $this->permissions->contains(function ($p) use ($slug) {
            return $p->slug === $slug || $p->name === $slug || (string) $p->id === (string) $slug;
        });
    }

    /**
     * Check if user has permission through any assigned role.
     *
     * @param Permission|string $permission
     * @return bool
     */
    public function hasPermissionThroughRole($permission): bool
    {
        $slug = $permission instanceof Permission ? $permission->slug : $permission;

        $this->loadMissing('roles.permissions');

        foreach ($this->roles as $role) {
            if ($role->permissions->contains(function ($p) use ($slug) {
                return $p->slug === $slug || $p->name === $slug || (string) $p->id === (string) $slug;
            })) {
                return true;
            }
        }

        return false;
    }

    /**
     * Grant direct permissions to the user.
     *
     * @param mixed ...$permissions
     * @return $this
     */
    public function givePermissionTo(...$permissions): self
    {
        $slugs = collect($permissions)->flatten()->map(function ($permission) {
            return $permission instanceof Permission ? $permission->slug : $permission;
        });

        $permissionModels = Permission::whereIn('slug', $slugs)
            ->orWhereIn('id', $slugs)
            ->get();

        $this->permissions()->syncWithoutDetaching($permissionModels);
        $this->load('permissions');

        return $this;
    }

    /**
     * Revoke direct permissions from the user.
     *
     * @param mixed ...$permissions
     * @return $this
     */
    public function revokePermissionTo(...$permissions): self
    {
        $slugs = collect($permissions)->flatten()->map(function ($permission) {
            return $permission instanceof Permission ? $permission->slug : $permission;
        });

        $permissionModels = Permission::whereIn('slug', $slugs)
            ->orWhereIn('id', $slugs)
            ->get();

        $this->permissions()->detach($permissionModels);
        $this->load('permissions');

        return $this;
    }

    /**
     * Sync user direct permissions.
     *
     * @param mixed ...$permissions
     * @return $this
     */
    public function syncPermissions(...$permissions): self
    {
        $slugs = collect($permissions)->flatten()->map(function ($permission) {
            return $permission instanceof Permission ? $permission->slug : $permission;
        });

        $permissionModels = Permission::whereIn('slug', $slugs)
            ->orWhereIn('id', $slugs)
            ->get();

        $this->permissions()->sync($permissionModels);
        $this->load('permissions');

        return $this;
    }

    /**
     * Get all effective permissions for the user (merged direct and role permissions).
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        $this->loadMissing(['roles.permissions', 'permissions']);

        $rolePermissions = $this->roles->flatMap(function ($role) {
            return $role->permissions;
        });

        return $this->permissions->merge($rolePermissions)->unique('id')->values();
    }
}
