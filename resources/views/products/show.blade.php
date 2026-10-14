@extends(Auth::user()->hasRole('admin') ? 'layouts.admin' : 'layouts.user')

@section('title', $product->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-box me-2 text-primary"></i> Product Details
                </h4>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">{{ $product->name }}</h3>
                        <span class="badge bg-light text-dark border font-monospace">SKU: {{ $product->sku }}</span>
                    </div>
                    <h2 class="text-success fw-bold mb-0">${{ number_format($product->price, 2) }}</h2>
                </div>

                <hr>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="text-muted small">Availability</div>
                        <div class="fw-semibold">
                            @if($product->stock > 0)
                                <span class="text-success"><i class="bi bi-check-circle me-1"></i> {{ $product->stock }} units available</span>
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle me-1"></i> Out of stock</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Created By</div>
                        <div class="fw-semibold">{{ $product->creator->name ?? 'System' }} ({{ $product->creator->email ?? '' }})</div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark">Description:</h6>
                    <div class="p-3 bg-light rounded text-secondary">
                        {{ $product->description ?? 'No description provided.' }}
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    @permission('edit-product')
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i> Edit Product
                    </a>
                    @endpermission

                    @permission('delete-product')
                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </form>
                    @endpermission
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
