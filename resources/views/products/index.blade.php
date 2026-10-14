@extends(Auth::user()->hasRole('admin') ? 'layouts.admin' : 'layouts.user')

@section('title', Auth::user()->hasRole('admin') ? 'Manage All Products' : 'My Products')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-boxes text-primary me-2"></i>
                    {{ Auth::user()->hasRole('admin') ? 'All Products (Admin View)' : 'My Products' }}
                </h4>
                <p class="text-muted small mb-0">
                    {{ Auth::user()->hasRole('admin') 
                        ? 'Full catalog overview across all users with administrative control' 
                        : 'Listing only products created by your account (' . Auth::user()->name . ')' }}
                </p>
            </div>
            
            <div class="d-flex gap-2 align-items-center">
                @permission('create-product')
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add Product
                </a>
                @else
                <button class="btn btn-secondary opacity-75" disabled title="You do not have 'create-product' permission">
                    <i class="bi bi-lock me-1"></i> Add Product (Restricted)
                </button>
                @endpermission
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Search filter -->
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-4">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, SKU, or description..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Search</button>
                @if(request('search'))
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product Details</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Owner</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    @php
                        $isOwnerOrAdmin = Auth::user()->hasRole('admin') || $product->user_id === Auth::id();
                    @endphp
                    <tr>
                        <td class="text-muted font-monospace">{{ $product->id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $product->name }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 250px;">
                                {{ $product->description ?? 'No description' }}
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border font-monospace">{{ $product->sku }}</span></td>
                        <td class="fw-semibold text-success">${{ number_format($product->price, 2) }}</td>
                        <td>
                            @if($product->stock > 10)
                                <span class="badge bg-success-subtle text-success">{{ $product->stock }} in stock</span>
                            @elseif($product->stock > 0)
                                <span class="badge bg-warning-subtle text-warning">{{ $product->stock }} low stock</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Out of stock</span>
                            @endif
                        </td>
                        <td>
                            @if($product->user_id === Auth::id())
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <i class="bi bi-person-fill me-1"></i>You
                                </span>
                            @else
                                <span class="small text-muted">{{ $product->creator->name ?? 'System' }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>

                            <!-- Edit Permission Check -->
                            @if($isOwnerOrAdmin && Auth::user()->hasPermissionTo('edit-product'))
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit Product">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @else
                                <button class="btn btn-sm btn-outline-secondary opacity-50" disabled title="{{ !Auth::user()->hasPermissionTo('edit-product') ? 'Requires edit-product permission' : 'Can only edit your own products' }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @endif

                            <!-- Delete Permission Check -->
                            @if($isOwnerOrAdmin && Auth::user()->hasPermissionTo('delete-product'))
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Product">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-outline-secondary opacity-50" disabled title="{{ !Auth::user()->hasPermissionTo('delete-product') ? 'Requires delete-product permission' : 'Can only delete your own products' }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            No products found matching criteria.
                            @permission('create-product')
                                <div class="mt-2">
                                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i> Add Your First Product
                                    </a>
                                </div>
                            @endpermission
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
