<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users'       => User::count(),
            'total_roles'       => Role::count(),
            'total_permissions' => Permission::count(),
            'total_products'    => Product::count(),
        ];

        // Fetch users with their assigned roles and direct permissions
        $recentUsers = User::with(['roles', 'permissions'])
            ->latest()
            ->take(8)
            ->get();

        $recentProducts = Product::with('creator')
            ->latest()
            ->take(5)
            ->get();

        $roles = Role::withCount('users')->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentProducts', 'roles'));
    }
}
