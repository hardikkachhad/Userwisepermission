<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Users having this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Permissions belonging to this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Grant permissions to this role.
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
     * Revoke permissions from this role.
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
     * Sync permissions for this role.
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
     * Check if role has a specific permission.
     */
    public function hasPermissionTo($permission): bool
    {
        $slug = $permission instanceof Permission ? $permission->slug : $permission;

        return $this->permissions->contains(function ($p) use ($slug) {
            return $p->slug === $slug || $p->name === $slug || (string) $p->id === (string) $slug;
        });
    }
}
