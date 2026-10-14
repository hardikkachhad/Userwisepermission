@extends('layouts.user')

@section('title', 'My Dashboard')

@section('content')
<div class="row g-4">
    <!-- User Welcome Banner -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($roles as $r)
                                <span class="badge {{ $r->slug === 'admin' ? 'bg-danger' : 'bg-light text-primary' }} fw-bold px-3 py-2">
                                    <i class="bi bi-shield-fill-check me-1"></i> Role: {{ $r->name }}
                                </span>
                            @endforeach
                            @if($directPermissions->isNotEmpty())
                                <span class="badge bg-success-subtle text-white border border-light border-opacity-25 px-3 py-2">
                                    <i class="bi bi-star-fill text-warning me-1"></i> {{ $directPermissions->count() }} Custom Permissions
                                </span>
                            @endif
                        </div>
                        <h2 class="fw-bold mb-2">Welcome, {{ $user->name }}!</h2>
                        <p class="text-white-50 mb-4">
                            You are logged in to your personalized user workspace. In accordance with your permissions, you can only view and manage your own items.
                        </p>
                        
                        <div class="d-flex flex-wrap gap-2">
                            @if($canViewProducts)
                                <a href="{{ route('products.index') }}" class="btn btn-light text-primary fw-semibold">
                                    <i class="bi bi-box-seam me-1"></i> My Products ({{ $myProductsCount }})
                                </a>
                            @else
                                <button class="btn btn-secondary text-white-50 opacity-75" disabled title="Requires 'view-product' permission">
                                    <i class="bi bi-lock me-1"></i> Products Restricted
                                </button>
                            @endif

                            @if($canCreateProduct)
                                <a href="{{ route('products.create') }}" class="btn btn-outline-light">
                                    <i class="bi bi-plus-circle me-1"></i> Add New Product
                                </a>
                            @endif

                            @role('admin')
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-warning text-dark fw-semibold">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Go to Admin Panel
                                </a>
                            @endrole
                        </div>
                    </div>
                    <div class="col-md-4 text-center d-none d-md-block">
                        <i class="bi bi-person-badge display-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Products Section (User's own products only) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-box-seam text-primary me-2"></i> My Products
                    </h5>
                    <p class="text-muted small mb-0">Only products created by you are displayed here</p>
                </div>
                <div class="d-flex gap-2">
                    @if($canCreateProduct)
                        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Add Product
                        </a>
                    @endif
                    @if($canViewProducts && $myProductsCount > 0)
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                            View All My Products ({{ $myProductsCount }})
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if(! $canViewProducts)
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-lock fs-1 text-secondary d-block mb-2"></i>
                        <h6 class="fw-bold text-dark">Product Viewing Restricted</h6>
                        <p class="small text-muted mb-0">Your account does not have the <code>view-product</code> permission.</p>
                    </div>
                @elseif($myProducts->isEmpty())
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-inbox fs-1 text-secondary d-block mb-2"></i>
                        <h6 class="fw-bold text-dark">No Products Created Yet</h6>
                        <p class="small text-muted mb-3">You haven't added any products to your account.</p>
                        @if($canCreateProduct)
                            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Create Your First Product
                            </a>
                        @else
                            <span class="badge bg-secondary text-white p-2">
                                <i class="bi bi-lock me-1"></i> You need 'create-product' permission to add items
                            </span>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Created Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myProducts as $prod)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $prod->name }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 250px;">
                                            {{ $prod->description ?? 'No description' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $prod->sku }}</span>
                                    </td>
                                    <td class="fw-semibold text-success">${{ number_format($prod->price, 2) }}</td>
                                    <td>
                                        @if($prod->stock > 10)
                                            <span class="badge bg-success-subtle text-success">{{ $prod->stock }} in stock</span>
                                        @elseif($prod->stock > 0)
                                            <span class="badge bg-warning-subtle text-warning">{{ $prod->stock }} low</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Out of stock</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">
                                        {{ $prod->created_at ? $prod->created_at->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('products.show', $prod) }}" class="btn btn-sm btn-outline-secondary" title="View Product">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($canEditProduct)
                                            <a href="{{ route('products.edit', $prod) }}" class="btn btn-sm btn-outline-primary" title="Edit Product">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary opacity-50" disabled title="Requires 'edit-product' permission">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endif

                                        @if($canDeleteProduct)
                                            <form action="{{ route('products.destroy', $prod) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Product">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary opacity-50" disabled title="Requires 'delete-product' permission">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- User Permissions & Privileges Breakdown -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-shield-check text-success me-2"></i> Custom Direct Permissions
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Permissions assigned <strong>specifically to your user account</strong>:</p>
                
                @if($directPermissions->isNotEmpty())
                    <div class="list-group">
                        @foreach($directPermissions as $perm)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-star-fill text-warning me-2"></i>
                                    <strong>{{ $perm->name }}</strong>
                                    <div class="small text-muted font-monospace">slug: {{ $perm->slug }}</div>
                                </div>
                                <span class="badge bg-success">Directly Granted</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light text-muted border text-center py-4">
                        <i class="bi bi-info-circle fs-3 d-block mb-2 text-secondary"></i>
                        No custom direct permissions assigned.<br>
                        All your privileges are derived from your assigned role.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Capabilities Checklist -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-sliders text-primary me-2"></i> Your Active Privileges
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Real-time status of your permissions:</p>

                <div class="list-group mb-4">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-eye me-2 {{ $canViewProducts ? 'text-success' : 'text-danger' }}"></i>
                            <strong>View Products</strong>
                            <div class="small text-muted font-monospace">view-product</div>
                        </div>
                        <span class="badge {{ $canViewProducts ? 'bg-success' : 'bg-danger' }}">
                            {{ $canViewProducts ? 'Allowed' : 'Restricted' }}
                        </span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-plus-circle me-2 {{ $canCreateProduct ? 'text-success' : 'text-danger' }}"></i>
                            <strong>Create Products</strong>
                            <div class="small text-muted font-monospace">create-product</div>
                        </div>
                        <span class="badge {{ $canCreateProduct ? 'bg-success' : 'bg-danger' }}">
                            {{ $canCreateProduct ? 'Allowed' : 'Restricted' }}
                        </span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-pencil me-2 {{ $canEditProduct ? 'text-success' : 'text-danger' }}"></i>
                            <strong>Edit Products</strong>
                            <div class="small text-muted font-monospace">edit-product</div>
                        </div>
                        <span class="badge {{ $canEditProduct ? 'bg-success' : 'bg-danger' }}">
                            {{ $canEditProduct ? 'Allowed' : 'Restricted' }}
                        </span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-trash me-2 {{ $canDeleteProduct ? 'text-success' : 'text-danger' }}"></i>
                            <strong>Delete Products</strong>
                            <div class="small text-muted font-monospace">delete-product</div>
                        </div>
                        <span class="badge {{ $canDeleteProduct ? 'bg-success' : 'bg-danger' }}">
                            {{ $canDeleteProduct ? 'Allowed' : 'Restricted' }}
                        </span>
                    </div>
                </div>

                <div class="small fw-semibold text-muted mb-2">All Effective Permissions:</div>
                <div class="d-flex flex-wrap gap-1">
                    @forelse($allPermissions as $p)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle p-1 px-2">
                            <i class="bi bi-check2 me-1"></i>{{ $p->name }}
                        </span>
                    @empty
                        <span class="text-muted small">No permissions active.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
