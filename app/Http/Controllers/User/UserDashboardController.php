<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Display the Regular User Dashboard.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // User's assigned roles
        $roles = $user->roles;

        // User's direct permissions
        $directPermissions = $user->permissions;

        // User's all effective permissions (direct + via roles)
        $allPermissions = $user->getAllPermissions();

        // Granular capability flags
        $canViewProducts   = $user->hasPermissionTo('view-product');
        $canCreateProduct = $user->hasPermissionTo('create-product');
        $canEditProduct   = $user->hasPermissionTo('edit-product');
        $canDeleteProduct = $user->hasPermissionTo('delete-product');

        // Products created by THIS user only
        $myProducts = $canViewProducts
            ? Product::where('user_id', $user->id)->latest()->take(5)->get()
            : collect();

        $myProductsCount = $canViewProducts
            ? Product::where('user_id', $user->id)->count()
            : 0;

        return view('user.dashboard', compact(
            'user',
            'roles',
            'directPermissions',
            'allPermissions',
            'myProducts',
            'myProductsCount',
            'canViewProducts',
            'canCreateProduct',
            'canEditProduct',
            'canDeleteProduct'
        ));
    }
}
