@extends(Auth::user()->hasRole('admin') ? 'layouts.admin' : 'layouts.user')

@section('title', 'Edit Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Edit Product
                </h4>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('products.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label for="sku" class="form-label fw-semibold">SKU / Code <span class="text-danger">*</span></label>
                            <input type="text" name="sku" id="sku" class="form-control text-uppercase" value="{{ old('sku', $product->sku) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="price" class="form-label fw-semibold">Price ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="stock" class="form-label fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock) }}" min="0" required>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <a href="{{ route('products.index') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Update Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
