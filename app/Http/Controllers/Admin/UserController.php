<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and role/permission counts.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $roleSlug = $request->input('role');
            $query->whereHas('roles', function ($q) use ($roleSlug) {
                $q->where('slug', $roleSlug);
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.users.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created user in storage with roles & direct permissions.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:6', 'confirmed'],
            'roles'         => ['nullable', 'array'],
            'roles.*'       => ['string', 'exists:roles,slug'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,slug'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Sync selected roles
        if (!empty($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        // Sync selected direct permissions
        if (!empty($validated['permissions'])) {
            $user->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' created successfully with assigned roles and permissions!");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $user->load(['roles.permissions', 'permissions']);
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        // Pluck current role slugs and direct permission slugs for easy checkbox checked check
        $userRoleSlugs = $user->roles->pluck('slug')->toArray();
        $userPermissionSlugs = $user->permissions->pluck('slug')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'permissions', 'userRoleSlugs', 'userPermissionSlugs'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'password'      => ['nullable', 'string', 'min:6', 'confirmed'],
            'roles'         => ['nullable', 'array'],
            'roles.*'       => ['string', 'exists:roles,slug'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,slug'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sync roles and direct permissions (empty array detaches all if unchecked)
        $user->syncRoles($request->input('roles', []));
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully!");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own logged-in administrator account!');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' deleted successfully!");
    }
}
