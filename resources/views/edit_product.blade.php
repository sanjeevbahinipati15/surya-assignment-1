<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Surya International</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5" style="max-width: 700px;">
        <div class="card shadow p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary mb-0">Edit Product</h3>
                <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Product Title</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Product Code</label>
                        <input type="text" name="code" id="code" value="{{ old('code', $product->code) }}" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Regular Price</label>
                        <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $product->price) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="selling_price" class="form-label">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="quantity" class="form-label">Stock Qty</label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="minimum_order_quantity" class="form-label">Min Qty</label>
                        <input type="number" name="minimum_order_quantity" id="minimum_order_quantity" value="{{ old('minimum_order_quantity', $product->minimum_order_quantity) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="max_order_quantity" class="form-label">Max Qty (Max 5)</label>
                        <input type="number" name="max_order_quantity" id="max_order_quantity" value="{{ old('max_order_quantity', $product->max_order_quantity) }}" class="form-control" max="5" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" name="image" id="image" class="form-control mb-2">
                    @if($product->image)
                        <div>
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="80" class="rounded border">
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Product</button>
            </form>
        </div>
    </div>
</body>
</html>
